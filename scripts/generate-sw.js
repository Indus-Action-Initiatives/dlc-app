import fs from "fs";
import path from "path";
import dotenv from "dotenv";

dotenv.config();

const templatePath = path.resolve("public/firebase-messaging-sw.template.js");
const outputPath = path.resolve("public/firebase-messaging-sw.js");

let content = fs.readFileSync(templatePath, "utf8");

content = content
  .replace("__API_KEY__", process.env.VITE_FIREBASE_API_KEY)
  .replace("__PROJECT_ID__", process.env.VITE_FIREBASE_PROJECT_ID)
  .replace("__MESSAGING_SENDER_ID__", process.env.VITE_FIREBASE_MESSAGING_SENDER_ID)
  .replace("__APP_ID__", process.env.VITE_FIREBASE_APP_ID)
  .replace("__AUTH_DOMAIN__", `${process.env.VITE_FIREBASE_PROJECT_ID}.firebaseapp.com`);

fs.writeFileSync(outputPath, content);

console.log("✅ Service Worker generated");