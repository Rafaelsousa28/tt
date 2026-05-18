@echo off
chcp 65001 > nul
echo ============================================
echo  Pure Garden - Instalacao no Windows
echo ============================================
echo.

REM Verificar PHP
php --version > nul 2>&1
if %errorlevel% neq 0 (
    echo [ERRO] PHP nao encontrado!
    echo Baixe e instale em: https://www.php.net/downloads
    echo Ou instale o XAMPP: https://www.apachefriends.org/
    pause
    exit /b 1
)
echo [OK] PHP encontrado

REM Verificar Composer
composer --version > nul 2>&1
if %errorlevel% neq 0 (
    echo [ERRO] Composer nao encontrado!
    echo Baixe em: https://getcomposer.org/download/
    pause
    exit /b 1
)
echo [OK] Composer encontrado

REM Verificar Node.js
node --version > nul 2>&1
if %errorlevel% neq 0 (
    echo [ERRO] Node.js nao encontrado!
    echo Baixe em: https://nodejs.org/
    pause
    exit /b 1
)
echo [OK] Node.js encontrado

echo.
echo Instalando dependencias PHP...
composer install --ignore-platform-reqs

echo.
echo Instalando dependencias Node.js...
npm install

echo.
echo Configurando .env...
if not exist .env (
    copy .env.example .env
)

echo.
echo Gerando chave da aplicacao...
php artisan key:generate

echo.
echo Criando banco de dados SQLite...
if not exist database\database.sqlite (
    type nul > database\database.sqlite
)

echo.
echo Rodando migrations...
php artisan migrate --force

echo.
echo Inserindo dados de exemplo...
php artisan db:seed --force

echo.
echo Compilando frontend...
npm run build

echo.
echo Criando link de storage...
php artisan storage:link

echo.
echo ============================================
echo  Instalacao concluida!
echo ============================================
echo.
echo Iniciando servidor...
echo Acesse: http://localhost:8000
echo Admin:  http://localhost:8000/admin
echo Login admin: admin@puregarden.com / password
echo.
php artisan serve
