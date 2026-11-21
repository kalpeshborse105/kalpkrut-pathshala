# Pathshala Android App (APK & AAB)
**By Kalpkrut Infotech Pvt Ltd**

Native Android shell for the live ERP at:
`https://pathshala.kalpkrutinfotech.com`

Includes:
- Production WebView (same login/API/data as website)
- Top progress bar + full-screen loader
- Pull-to-refresh
- Offline retry screen
- Back-stack navigation
- File picker (camera/gallery/docs)
- WhatsApp / tel / mailto handoff
- Deep link support for the production domain

## Build (CLI)

```bat
cd android-app
gradlew.bat assembleDebug
gradlew.bat assembleRelease
gradlew.bat bundleRelease
```

Outputs:
- Debug APK: `app/build/outputs/apk/debug/app-debug.apk`
- Release APK: `app/build/outputs/apk/release/app-release.apk`
- Play Store AAB: `app/build/outputs/bundle/release/app-release.aab`

## Signing

`keystore.properties` + `pathshala-upload.jks` are local (gitignored).
Keep the `.jks` backup safe — Play Console needs the same upload key forever.

## Auth note

Current app uses the website session login.
Native auth (Firebase/OTP/biometric) can be added later without changing the ERP backend.
