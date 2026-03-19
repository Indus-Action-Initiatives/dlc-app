import { messaging } from "./firebase";
import { getToken, onMessage } from "firebase/messaging";

const VAPID_KEY = import.meta.env.VITE_FIREBASE_VAPID_KEY;
const API_BASE = import.meta.env.VITE_API_BASE_URL;

// 🔹 Generate or fetch device ID
function getDeviceId() {
  let id = localStorage.getItem("dlc_device_id");

  if (!id) {
    id = "dev_" + Math.random().toString(36).substring(2, 11);
    localStorage.setItem("dlc_device_id", id);
  }

  return id;
}

// 🔹 Register Service Worker
async function registerServiceWorker() {
  if (!("serviceWorker" in navigator)) return null;

  const registration = await navigator.serviceWorker.register("/firebase-messaging-sw.js");

  return registration;
}

// 🔹 Register Push (main entry)
export async function registerPush(userType, userId) {
  try {
    const permission = await Notification.requestPermission();
    if (permission !== "granted") return false;

    if (!messaging) return false;

    await registerServiceWorker();

    const token = await getToken(messaging, {
      vapidKey: VAPID_KEY,
    });

    if (!token) return false;

    const deviceId = getDeviceId();

    const response = await fetch(`${API_BASE}/save-token`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        deviceId,
        fcmToken: token,
        userType,
        currentUser: userId,
      }),
    });

    return response.ok;
  } catch (err) {
    console.error("❌ Push registration error:", err);
    return false;
  }
}

// 🔹 Foreground listener
export function listenForPush() {
  if (!messaging) {
    console.error("Messaging not initialized");
    return;
  }

  onMessage(messaging, async (payload) => {

    const title = payload.notification?.title || "Notification";

    const options = {
      body: payload.notification?.body || "",
      icon: "/icon.png",
      data: payload.data || {},
    };

    // 👉 If app is visible → better UX: show system notification OR toast
    if (document.visibilityState === "visible") {
      try {
        new Notification(title, options);
        return;
      } catch (e) {
        console.warn("Direct notification failed, fallback to SW");
      }
    }

    // 👉 Fallback to service worker
    const registration = await navigator.serviceWorker.ready;
    registration.showNotification(title, options);
  });
}