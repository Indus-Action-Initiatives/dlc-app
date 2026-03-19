# DLC App - Frontend Setup Guide

This repository contains the Vue.js frontend for the DLC application.

## Prerequisites
Before you begin, ensure you have the following installed:
* **Git:** To download the project repository.
* **Node.js 24+:** Required to run Vue and the Vite development server.

## 1. Clone the Project
Open your terminal and clone the frontend branch.

**Using SSH:**
```bash
git clone -b frontend git@github.com:Indus-Action-Initiatives/dlc-app.git frontend
```

**Using HTTPS:**
```bash
git clone -b frontend https://github.com/Indus-Action-Initiatives/dlc-app.git frontend
```

## 2. Install Dependencies
Navigate into your newly cloned frontend folder and install the required Node modules:

```bash
cd frontend
npm install
```

## 3. Configure Environment (.env)
Create a file named `.env` in the root of your `/frontend` directory and paste the following configuration:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=labourchowk
DB_USERNAME=
DB_PASSWORD=

BROADCAST_DRIVER=log
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

VITE_FIREBASE_API_KEY=
VITE_FIREBASE_PROJECT_ID=
VITE_FIREBASE_MESSAGING_SENDER_ID=
VITE_FIREBASE_APP_ID=
VITE_FIREBASE_VAPID_KEY=

UPLOAD_PATH_WORKER="upload/worker"
UPLOAD_PATH_EMPLOYER="upload/employer"

APP_KEY=
JWT_SECRET=
```

## 4. Run the Application
Start the Vite development server:

```bash
npm run dev
```

---

## Troubleshooting & Notes
* **OTP Bypass:** For local testing, use `123456` as the universal OTP.
* **Firebase Setup:** Ensure the Firebase keys in the `.env` are accurate for notification and auth features.