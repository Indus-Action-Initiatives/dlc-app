importScripts("https://www.gstatic.com/firebasejs/10.7.0/firebase-app-compat.js");
importScripts("https://www.gstatic.com/firebasejs/10.7.0/firebase-messaging-compat.js");

// 🔹 Force SW activation immediately (good for dev)
self.addEventListener("install", (event) => {
  self.skipWaiting();
});

self.addEventListener("activate", (event) => {
  event.waitUntil(self.clients.claim());
});

firebase.initializeApp({
    apiKey: "__API_KEY__",
    authDomain: "__AUTH_DOMAIN__",
    projectId: "__PROJECT_ID__",
    messagingSenderId: "__MESSAGING_SENDER_ID__",
    appId: "__APP_ID__",
});

// 🔹 Messaging instance
const messaging = firebase.messaging();

// 🔹 Background notifications
messaging.onBackgroundMessage((payload) => {

  const title = payload.notification?.title || "Notification";

  const options = {
    body: payload.notification?.body || "",
    icon: "/icon.png",
    data: payload.data || {},
  };

  self.registration.showNotification(title, options);
});

// 🔹 Handle click
self.addEventListener("notificationclick", (event) => {
  event.notification.close();

  const data = event.notification.data || {};
  let url = "/";

  if (data.type === "job") {
    url = `/jobs/${data.job_id}`;
  } else if (data.type === "chat") {
    url = `/chat/${data.chat_id}`;
  }

  event.waitUntil(
    clients.matchAll({ type: "window", includeUncontrolled: true }).then((clientsArr) => {
      for (const client of clientsArr) {
        if ("focus" in client) {
          client.navigate(url);
          return client.focus();
        }
      }
      return clients.openWindow(url);
    })
  );
});