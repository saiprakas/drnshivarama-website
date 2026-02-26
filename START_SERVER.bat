@echo off
echo.
echo ====================================================
echo Starting Sri Krishna Hospital Admin Panel...
echo ====================================================
echo.

cd /d "%~dp0"

REM Check if node_modules exists
if not exist "node_modules" (
    echo Installing dependencies (this may take a minute)...
    echo.
    call npm install
    echo.
)

echo Starting server...
echo.
timeout /t 2 /nobreak
call npm start

pause
