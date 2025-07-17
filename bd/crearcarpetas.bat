@echo off
setlocal

REM Carpeta raíz del proyecto
set "PROYECTO=minidespensa"

REM Crear carpetas principales
mkdir "%PROYECTO%\config"
mkdir "%PROYECTO%\public"
mkdir "%PROYECTO%\public\css"
mkdir "%PROYECTO%\public\js"
mkdir "%PROYECTO%\public\uploads"
mkdir "%PROYECTO%\public\uploads\productos"
mkdir "%PROYECTO%\includes"
mkdir "%PROYECTO%\auth"
mkdir "%PROYECTO%\modules"
mkdir "%PROYECTO%\modules\productos"
mkdir "%PROYECTO%\modules\usuarios"
mkdir "%PROYECTO%\modules\compras"
mkdir "%PROYECTO%\modules\ventas"
mkdir "%PROYECTO%\modules\dashboard"
mkdir "%PROYECTO%\afip"
mkdir "%PROYECTO%\afip\cert"
mkdir "%PROYECTO%\storage"
mkdir "%PROYECTO%\storage\logs"
mkdir "%PROYECTO%\templates"

REM Crear archivos vacíos comunes
echo <?php // index principal ?> > "%PROYECTO%\public\index.php"
echo <?php // conexión base de datos ?> > "%PROYECTO%\config\db.php"
echo <?php // configuración general ?> > "%PROYECTO%\config\config.php"
echo <?php // encabezado HTML ?> > "%PROYECTO%\includes\header.php"
echo <?php // pie de página ?> > "%PROYECTO%\includes\footer.php"

echo Estructura de proyecto '%PROYECTO%' creada correctamente.
pause
endlocal
