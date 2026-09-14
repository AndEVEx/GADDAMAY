@echo off
echo ==============================================
echo   GADDAMAY - Menjalankan Modul Agenda (Laravel)
echo ==============================================
cd /d "%~dp0..\apps\agenda"
php artisan serve --port=8000
