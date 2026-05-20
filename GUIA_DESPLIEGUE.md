# 🚀 Guía de Despliegue a Producción — InvSys WMS

Esta guía contiene los pasos técnicos detallados para subir, configurar y poner en marcha el sistema **InvSys WMS** en un entorno de hosting compartido (con cPanel), servidor VPS o hosting dedicado.

---

## 📋 Requisitos del Servidor
Antes de iniciar, asegúrate de que tu servidor cumpla con:
* **PHP**: Versión `8.1` o superior.
* **Extensiones PHP requeridas**: `pdo_mysql`, `curl`, `mbstring`, `openssl`, `zip`.
* **Base de Datos**: MySQL `8.0` o MariaDB `10.4` superior.
* **Acceso web**: Soporte para archivos `.htaccess` (módulo `mod_rewrite` activo en Apache).

---

## 🛠️ Paso 1: Subir los Archivos al Hosting

1. Compime los archivos del proyecto localmente en un archivo `.zip`.
   * **Importante**: No incluyas la carpeta `vendor/` ni el archivo `.env` local.
2. Sube el archivo `.zip` a tu hosting utilizando el Administrador de Archivos de cPanel o vía FTP.
3. Descomprime el archivo en el directorio correspondiente:
   * Si es el dominio principal: carpeta `/public_html/`.
   * Si es un subdominio o carpeta: carpeta `/public_html/invsys/` o equivalente.

> [!NOTE]
> **Seguridad de Archivos:**
> El sistema viene equipado con archivos `.htaccess` de seguridad que previenen el listado de directorios y bloquean el acceso web directo a carpetas sensibles como `/app`, `/config` y `/storage`. Asegúrate de que los archivos ocultos (dotfiles) como `.htaccess` se hayan subido y descomprimido correctamente.

---

## 🗄️ Paso 2: Configuración de la Base de Datos

1. Ingresa al panel de tu hosting (cPanel) y ve a **Bases de Datos MySQL**.
2. Crea una nueva base de datos (ej. `nombreusuario_invsys`).
3. Crea un nuevo usuario de base de datos con una contraseña altamente segura.
4. Asocia el usuario a la base de datos y otórgale **TODOS LOS PERMISOS**.
5. Abre **phpMyAdmin** desde tu cPanel, selecciona la base de datos creada e importa el archivo semilla inicial ubicado en **`database/invsys.sql`**.

---

## 🔒 Paso 3: Configurar Variables de Entorno (`.env`)

En el directorio raíz del hosting donde subiste los archivos, crea un nuevo archivo llamado **`.env`** (si no lo ves, asegúrate de activar "Mostrar archivos ocultos" en la configuración del administrador de archivos) y añade la configuración de producción:

```env
# =====================================================
# InvSys — Configuración de Producción
# =====================================================
APP_ENV=production

# Configuración de Base de Datos del Hosting
DB_DRIVER=mysql
DB_HOST=127.0.0.1       # Usualmente localhost o 127.0.0.1 en cPanel
DB_PORT=3306
DB_DATABASE=nombreusuario_invsys
DB_USERNAME=nombreusuario_user
DB_PASSWORD=ContraseñaFuerteYSegura_123!
DB_CHARSET=utf8mb4

# Dirección URL Base
# Si tu app corre en la raíz de tu dominio (ej: www.tudominio.com) dejar en: /
# Si corre en una subcarpeta (ej: www.tudominio.com/invsys) dejar en: /invsys/public
APP_BASE_URL=/

# Configuración de Correo por Defecto
MAIL_FROM_ADDRESS=noreply@tudominio.com
MAIL_FROM_NAME="InvSys WMS"
```

---

## 📦 Paso 4: Instalar Dependencias de Composer

Si tu hosting cuenta con acceso por SSH terminal:
1. Conéctate a tu hosting y navega al directorio del proyecto.
2. Ejecuta el comando para instalar las dependencias de producción de forma optimizada:
   ```bash
   composer install --no-dev --optimize-autoloader
   ```

*Nota: Si tu hosting es compartido y no cuenta con SSH, puedes ejecutar `composer install --no-dev` localmente en tu computadora antes de comprimir el proyecto y subir la carpeta `vendor` ya pre-instalada.*

---

## ⚡ Paso 5: Ejecutar Migraciones de Base de Datos

Para activar los módulos especiales de alertas por WhatsApp/CallMeBot, ejecuta las migraciones correspondientes.

### Opción A (Vía SSH Terminal - Recomendada):
Corre los scripts en orden desde la terminal de tu hosting:
```bash
php database/migrations/fase11_whatsapp.php
php database/migrations/migrate_callmebot.php
```

### Opción B (Manual - Vía phpMyAdmin):
Si no cuentas con terminal SSH, puedes importar directamente este bloque SQL en la pestaña **SQL** de tu phpMyAdmin para registrar los campos requeridos en la base de datos:

```sql
-- 1. Añadir columna a usuarios
ALTER TABLE usuarios ADD COLUMN telefono VARCHAR(20) NULL AFTER email;

-- 2. Registrar claves de configuración de WhatsApp CallMeBot
INSERT IGNORE INTO configuraciones (clave, valor, descripcion, tipo, updated_at) VALUES 
('whatsapp_enabled', '0', 'Habilitar notificaciones por WhatsApp (1=Sí, 0=No)', 'boolean', NOW()),
('whatsapp_phone', '', 'Número de teléfono del administrador con código de país (ej: +50588887777)', 'string', NOW()),
('whatsapp_apikey', '', 'API Key de CallMeBot (obtenida al registrarse)', 'string', NOW());
```

---

## ⏰ Paso 6: Configurar la Tarea Automatizada (Cron Job)

Para que el sistema de alertas por WhatsApp y correo funcione diariamente sin intervención humana, debes configurar un Cron Job en tu hosting:

1. Ingresa a **cPanel** y busca la opción **Tareas Cron (Cron Jobs)**.
2. En la sección **Añadir nueva tarea cron**, selecciona la frecuencia común: **Una vez al día (0 9 * * *)** o la hora de tu preferencia.
3. En el campo **Comando**, escribe la ruta al ejecutable PHP seguida de la ruta absoluta del archivo `cron_alertas.php`:
   ```bash
   /usr/local/bin/php /home/tu_usuario_cpanel/public_html/scripts/cron_alertas.php
   ```
   *(Nota: Asegúrate de reemplazar `tu_usuario_cpanel` por tu usuario real de hosting y `/public_html` por la carpeta real donde resida el proyecto. Puedes consultar la ruta absoluta de tu cuenta en la barra lateral del cPanel).*

---

## 📂 Paso 7: Permisos de Escritura

Asegúrate de cambiar los permisos (CHMOD) a **`755`** (o `775` según el hosting) a las siguientes carpetas del sistema para garantizar el correcto funcionamiento de almacenamiento:
* `storage/` (y todo su contenido recursivo).
* `storage/logs/` (para los archivos de bitácora del sistema y correos).
* `storage/backups/` (para almacenar los respaldos automáticos de base de datos).

---

## ⚙️ Paso 8: Configuración Final en el Panel Visual

Una vez el sitio cargue en tu dominio:
1. Inicia sesión como **Administrador** (utiliza tu cuenta configurada).
2. Dirígete a la sección de **Configuración**:
   * **Servidor de Correo (SMTP)**: Activa el módulo SMTP e ingresa las credenciales reales de tu servidor de correo para garantizar que los correos no caigan en SPAM.
   * **WhatsApp**: Activa el módulo e ingresa tu número con código de país y tu API Key de CallMeBot.
3. Ve a tu perfil y asegúrate de registrar tu teléfono y dirección de correo correctos para que las alertas del Cron Job te sean despachadas con éxito.
