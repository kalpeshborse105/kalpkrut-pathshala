# Pathshala Android App (APK & AAB)
**By Kalpkrut Infotech Pvt Ltd**

This Android native project wraps the Kalpkrut Pathshala web ERP into a production-ready Android App with:
- Native Hardware Acceleration & Chrome Engine
- File picker integration (Camera, Gallery, PDF documents for homework & admissions)
- Swipe-to-Refresh
- WhatsApp & Phone dialer direct linking
- Back navigation stack history
- Custom Status bar & Kalpkrut branding

---

## 🛠️ How to Generate APK & AAB

### Step 1 — Open in Android Studio
1. Open **Android Studio**.
2. Click **Open** and select:
   `C:\Users\Admin\.gemini\antigravity\scratch\kalpkrut-pathshala\android-app`

### Step 2 — Set Your Production URL
Open `app/src/main/java/com/kalpkrut/pathshala/MainActivity.kt` and change:
```kotlin
private val appUrl = "https://your-hostinger-domain.com"
```

### Step 3 — Build APK (For Direct Install / Testing)
- In Android Studio menu: **Build** → **Build Bundle(s) / APK(s)** → **Build APK(s)**
- The output APK will be generated at:
  `android-app/app/build/outputs/apk/debug/app-debug.apk`

### Step 4 — Build AAB (Android App Bundle for Google Play Store)
- In Android Studio menu: **Build** → **Generate Signed Bundle / APK**
- Select **Android App Bundle** (.aab)
- Create or select your `.jks` keystore
- Build **Release**
- The `.aab` file can now be uploaded directly to **Google Play Console**!
