import { getMessaging, getToken, deleteToken } from "firebase/messaging";
import app from "./firebase";
import { PushNotifications } from "@capacitor/push-notifications";
import { Capacitor } from "@capacitor/core";
import router from "@/router";
const VAPID_KEY = import.meta.env.VITE_FIREBASE_VAPID_KEY;

// 🔥 Main function
export async function generateFCMToken() {
  try {
    
    // ✅ STEP 1A: If MOBILE (APK)
    if (Capacitor.isNativePlatform()) {
      try {
        const permission = await PushNotifications.requestPermissions();
        let mobile_token;
        if (permission.receive !== 'granted') {
          console.log('❌ Mobile permission denied');
          return null;
        }
  
        await PushNotifications.register();
  
        // ✅ TOKEN LISTENER
        PushNotifications.addListener('registration', async (token) => {
          mobile_token = token.value;
          console.log('📱 MOBILE TOKEN:', token.value);
        });
  
        // ✅ ERROR LISTENER
        PushNotifications.addListener('registrationError', (err) => {
          console.error('❌ Registration error:', err);
        });
  
        // ✅ FOREGROUND NOTIFICATION
        PushNotifications.addListener('pushNotificationReceived', (notification) => {
          console.log('📩 Foreground notification:', notification);
        
          const data = notification.data || {};
        
          if (confirm(notification.title + "\n" + notification.body)) {
            if (data.type === 'chat') {
              router.push({
                name: 'worker-chat',
                params: { id: data.senderId },
                query: { name: data.senderName }
              });
            }
            if (data.type === 'job') {
              router.push({
                name: 'worker-dashboard-employer-detail',
                params: { id: data.job_id }
              });
            }
          }
        });
  
        // ✅ CLICK HANDLER (MOST IMPORTANT)

        PushNotifications.addListener('pushNotificationActionPerformed', (action) => {
          const data = action.notification.data || {};
        
          console.log('👉 Notification clicked:', data);
        
          setTimeout(() => {
            if (data.type === 'chat') {
              router.push({
                name: 'worker-chat',
                params: { id: data.senderId },
                query: { name: data.senderName }
              });
              return;
            }
        
            if (data.type === 'job') {
              router.push({
                name: 'worker-dashboard-employer-detail',
                params: { id: data.job_id }
              });
              return;
            }
          }, 300);
        });
        return mobile_token;
      } catch (err) {
        console.error('❌ Mobile token error:', err);
        return null;
      }
    }
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