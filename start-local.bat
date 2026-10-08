@echo off
echo ===================================================
echo   THE DRIVE CLINIC — Starting Local Servers
echo ===================================================
echo.
echo Starting Node.js 20 Express API on http://127.0.0.1:5000/api ...
start "The Drive Clinic API (Port 5000)" cmd /k "cd api && npm start"

echo Starting PHP 8.2 Web Server on http://127.0.0.1:8000 ...
start "The Drive Clinic Web (Port 8000)" cmd /k "cd web\public && set PHP_CLI_SERVER_WORKERS=4 && php -S 127.0.0.1:8000 index.php"

echo.
echo ===================================================
echo   Local Servers Running:
echo   - Web Site: http://127.0.0.1:8000
echo   - REST API: http://127.0.0.1:5000/api/health
echo ===================================================
echo.
