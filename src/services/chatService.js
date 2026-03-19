import {
  addDoc,
  collection,
  doc,
  getDoc,
  serverTimestamp,
  setDoc,
} from "firebase/firestore";

import {
  query,
  orderBy,
  onSnapshot,
} from "firebase/firestore";

import { db } from "@/services/firebase.js";

function normalizeId(id) {
  return String(id ?? "").trim();
}

/**
 * Simple thread model:
 * - threads/{threadId}
 *   - participants: ["worker:123", "employer:456"]
 *   - participantNames: { "worker:123": "X", "employer:456": "Y" }
 *   - updatedAt
 *   - lastMessageText
 * - threads/{threadId}/messages/{messageId}
 *   - text
 *   - senderId ("worker:123")
 *   - createdAt
 */
export function makeThreadId(a, b) {
  const A = normalizeId(a);
  const B = normalizeId(b);


  return [A, B].sort().join("__")
}

export async function ensureThread({
  threadId,
  participants,
  participantNames,
}) {
  const threadRef = doc(db, "threads", threadId);
  const snap = await getDoc(threadRef);
  if (snap.exists()) return;

  await setDoc(threadRef, {
    participants,
    participantNames: participantNames || {},     
    createdAt: serverTimestamp(),
    updatedAt: serverTimestamp(),
    lastMessageText: "",
  });
}

export async function sendThreadMessage({ threadId, text, senderId }) {
  if (!text || !text.trim()) return;
  const threadRef = doc(db, "threads", threadId);
  const messagesRef = collection(threadRef, "messages");

  await addDoc(messagesRef, {
    text,
    senderId,
    createdAt: serverTimestamp(),
  });

  await setDoc(
    threadRef,
    {
      updatedAt: serverTimestamp(),
      lastMessageText: text,
      lastMessageAt: serverTimestamp()
    },
    { merge: true }
  );
}

// 🔥 Real-time listener
export function listenToMessages(threadId, callback) {
  const messagesRef = collection(db, "threads", threadId, "messages");

  const q = query(messagesRef, orderBy("createdAt", "asc"));

  return onSnapshot(q, (snapshot) => {
    const messages = snapshot.docs.map((doc) => ({
      id: doc.id,
      ...doc.data(),
    }));

    callback(messages);
  });
}
