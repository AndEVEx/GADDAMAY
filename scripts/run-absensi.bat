@echo off
echo ==============================================
echo   GADDAMAY - Menjalankan Modul Absensi (CI4)
echo ==============================================
cd /d "%~dp0..\apps\absensi"
php spark serve --port=8080
