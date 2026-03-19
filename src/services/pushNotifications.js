import { getMessaging, getToken, deleteToken } from "firebase/messaging";
import app from "./firebase";

const VAPID_KEY = import.meta.env.VITE_FIREBASE_VAPID_KEY;

// 🔥 Main function
export async function generateFCMToken() {
  try {
    // 1. Ask permission
    const permission = await Notification.requestPermission();
    if (permission !== "granted") {
      console.log("❌ Permission denied");
      return null;
    }

    // 2. Register YOUR service worker
    const registration = await navigator.serviceWorker.register("/firebase-messaging-sw.js");

    // 3. Ensure it controls the page
    await navigator.serviceWorker.ready;

    // 4. Initialize messaging ONLY now
    const messaging = getMessaging(app);

    // 5. Generate new token linked to YOUR SW
    const token = await getToken(messaging, {
      vapidKey: VAPID_KEY,
      serviceWorkerRegistration: registration,
    });

    return token;
  } catch (err) {
    console.error("❌ Token error:", err);
    return null;
  }
}


export async function saveFCMToken(token, userType, userId) {
    if (token) {
    await fetch(`${import.meta.env.VITE_API_BASE_URL}/save-token`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        deviceId: getDeviceId(), // same function you already had
        fcmToken: token,
        userType: userType,
        currentUser: String(userId),
      }),
    });
    return true;
    }
    return false;
}

function getDeviceId() {
  let id = localStorage.getItem("dlc_device_id");

  if (!id) {
    id = "dev_" + Math.random().toString(36).substring(2, 11);
    localStorage.setItem("dlc_device_id", id);
  }

  return id;
}