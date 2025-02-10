@echo off
echo Setting up Financial Planner...

REM Check if Docker is installed
where docker >nul 2>nul
if %errorlevel% neq 0 (
    echo Docker is not installed! Please install Docker Desktop for Windows first.
    pause
    exit /b
)

REM Create necessary files
echo Creating configuration files...
copy .env.example .env
echo server { > nginx.conf
echo     listen 80; >> nginx.conf
echo     index index.php index.html; >> nginx.conf
echo     root /var/www/public; >> nginx.conf
echo     location / { >> nginx.conf
echo         try_files $uri $uri/ /index.php?$query_string; >> nginx.conf
echo     } >> nginx.conf
echo     location ~ \.php$ { >> nginx.conf
echo         fastcgi_pass app:9000; >> nginx.conf
echo         fastcgi_index index.php; >> nginx.conf
echo         include fastcgi_params; >> nginx.conf
echo         fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name; >> nginx.conf
echo     } >> nginx.conf
echo } >> nginx.conf

REM Start Docker containers
echo Starting Docker containers...
docker-compose up -d --build

REM Install dependencies and set up database
echo Installing dependencies...
docker-compose exec app composer install
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate

echo.
echo Setup complete! Visit http://localhost:8000 to see your application.
echo.
pause 