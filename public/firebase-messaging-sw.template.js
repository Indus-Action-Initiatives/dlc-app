importScripts("https://www.gstatic.com/firebasejs/10.7.0/firebase-app-compat.js");
importScripts("https://www.gstatic.com/firebasejs/10.7.0/firebase-messaging-compat.js");

console.log("🔥 SW LOADED");
firebase.initializeApp({
    apiKey: "__API_KEY__",
    authDomain: "__AUTH_DOMAIN__",
    projectId: "__PROJECT_ID__",
    messagingSenderId: "__MESSAGING_SENDER_ID__",
    appId: "__APP_ID__",
});

// 🔹 Messaging instance
const messaging = firebase.messaging();


// 🔹 Handle click
self.addEventListener("notificationclick", (event) => {

  event.notification.close();

  const data = event.notification.data || {};

  let url = "/";

  if (data.type === "job") {
    url = `/worker-dashboard-employer-detail/${data.job_id}`;
  } else if (data.type === "chat") {
    url = `/chat/${data.thread_id}`;
  }

  event.waitUntil(
    clients.matchAll({ type: "window", includeUncontrolled: false }).then((clientsArr) => {
      for (const client of clientsArr) {
        // 🔥 Only interact with SAME ORIGIN + controlled clients
        if (client.url.startsWith(self.location.origin) && "focus" in client) {
          try {
            client.postMessage({
              type: "NAVIGATE",
              url,
            });
            return client.focus();
          } catch (e) {
            console.log("⚠️ postMessage failed, fallback to openWindow");
          }
        }
      }

      // fallback
      return clients.openWindow(url);
    })
  );
});

self.addEventListener("push", (event) => {

  let payload = {};

  try {
    payload = event.data ? event.data.json() : {};
  } catch (e) {
    console.log("Invalid JSON");
  }


  const data = payload.data || {};
  const notification = payload.notification || {};

  const title = notification.title || data.title || "Notification";

  const options = {
    body: notification.body || data.body || "",
    icon: "/icon.png",
    data: {
      ...data,
    },
  };

  event.waitUntil(
    self.registration.showNotification(title, options)
  );
});