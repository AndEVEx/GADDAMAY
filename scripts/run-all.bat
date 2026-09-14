@echo off
echo ========================================================
echo   GADDAMAY - Memulai Semua Layanan Sistem Terpadu
echo   - Agenda:  http://localhost:8000
echo   - Absensi: http://localhost:8080
echo ========================================================
start "GADDAMAY - Agenda" cmd /k "%~dp0run-agenda.bat"
start "GADDAMAY - Absensi" cmd /k "%~dp0run-absensi.bat"
