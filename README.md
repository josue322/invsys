# InvSys WMS - Warehouse Management System

InvSys es un Sistema de Gestión de Almacenes (WMS) de grado corporativo, diseñado bajo la arquitectura MVC. Está construido en PHP nativo (cero dependencias externas pesadas) y JavaScript para asegurar extrema velocidad, bajo consumo de recursos de servidor y despliegues muy sencillos en entornos productivos.

## 🚀 Características Principales

* **Autenticación y Seguridad Avanzada:** 
  * Protección contra CSRF (Cross-Site Request Forgery) en todos los formularios.
  * Autenticación basada en sesiones seguras HTTPOnly.
  * Rate Limiting (limitador de peticiones) para prevenir ataques de fuerza bruta.
  * CSP (Content Security Policy) estricto.
* **Gestión Exhaustiva de Inventarios:** Control exacto del stock con historial (Kardex) detallado.
* **Lógica FEFO (First Expire, First Out):** Gestión de Lotes y control de perecederos.
* **Módulo de Escáner:** Escaneo vía cámara web nativa (html5-qrcode) e integración automática de productos vía APIs gratuitas externas (Open Food Facts / UPCitemdb) si el SKU no es encontrado.
* **Dashboard Analítico:** Gráficos estadísticos rápidos en tiempo real (Chart.js) y Alertas de bajo inventario.
* **Flujos de Trabajo:** Soporte completo de ciclos logísticos: Órdenes de Compra, Requisiciones departamentales, Transferencias de ubicación y Devoluciones controladas.
* **Auditorías:** Módulo de conteo físico para la reconciliación real de inventario (auditoría cíclica).
* **Experiencia de Usuario (UX):** Interfaz premium completamente responsiva, asíncrona (AJAX), con soporte nativo a **Modo Oscuro**.

---

## 💻 Requisitos Técnicos del Servidor

Dado que el sistema no usa frameworks grandes como Laravel, los requisitos son mínimos y correrá muy rápido en casi cualquier hosting estándar:

* Servidor Web: Apache (Recomendado para el `.htaccess`) o Nginx.
* PHP: **Versión 8.1 o superior**.
* Extensiones PHP Obligatorias: `pdo`, `pdo_mysql`, `json`, `mbstring`, `curl`, `gd` o `imagick` (para manejo de imágenes).
* Base de Datos: MySQL 5.7+ o MariaDB 10.3+.

---

## 🛠️ Instrucciones de Despliegue en cPanel (Producción)

Para instalar InvSys en tu hosting (ej. un dominio dedicado), sigue rigurosamente estos pasos:

### 1. Preparación de la Base de Datos
1. Ve a **Bases de Datos MySQL** en tu cPanel.
2. Crea una nueva base de datos (ej. `midominio_invsys`).
3. Crea un usuario (ej. `midominio_user`) con una contraseña segura.
4. Asigna todos los privilegios al usuario sobre esa base de datos.
5. Ve a **phpMyAdmin**, selecciona la base de datos que creaste e **importa** el archivo SQL de la base de datos local (puedes generar el script completo exportando tu BD actual en tu entorno local o usando el último respaldo en `storage/backups`).

### 2. Subida de Archivos
1. Comprime toda la carpeta del proyecto en un archivo `.zip`.
2. Ve a **Administrador de Archivos** (File Manager) en cPanel.
3. Navega hasta la carpeta raíz de tu dominio (usualmente `public_html`).
4. Sube el `.zip` y extráelo allí mismo de modo que el archivo `.htaccess` del proyecto quede en la raíz de `public_html`.
   *(Asegúrate de que la configuración "Mostrar archivos ocultos" esté activada en cPanel para ver los `.htaccess`).*

### 3. Configuración de Variables de Entorno (`.env`)
El proyecto incluye un archivo `.env.example`. 
1. Renombra `.env.example` a `.env` (si ya tienes un `.env`, usa ese).
2. Edítalo y ajusta la configuración de producción:

```env
APP_ENV=production

# Datos de la base de datos que creaste en el paso 1
DB_DRIVER=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=midominio_invsys
DB_USERNAME=midominio_user
DB_PASSWORD=TuPasswordSeguro123!
DB_CHARSET=utf8mb4

# Al estar desplegado en la raíz del dominio principal, debe estar VACÍO
APP_BASE_URL=

# Configuración de notificaciones
MAIL_FROM_ADDRESS=noreply@tudominio.com
MAIL_FROM_NAME=InvSys
```

### 4. Verificación de Permisos (CHMOD)
Para que el sistema pueda guardar imágenes de productos, generar reportes y registrar logs, asegúrate de que las siguientes carpetas tengan permisos **755** o **775** (escribe permisos, pero no 777 por seguridad):

* `storage/` y todos sus subdirectorios (`logs`, `backups`, `uploads`, `rate_limits`).
* `public/assets/img/productos/`

---

## 🛡️ Notas de Seguridad Pre-Lanzamiento

1. El archivo `.htaccess` ubicado en la raíz (`public_html/.htaccess`) se encarga de proteger todo el código fuente. Bloquea el acceso a `/app`, `/config` y `/storage`, dirigiendo todo el tráfico de usuarios obligatoriamente a la carpeta `/public`. Nunca elimines este archivo.
2. Como `APP_ENV` está configurado como `production`, el sistema no mostrará errores de PHP (pantallazos con rutas o código a la vista del usuario) sino que mostrará la pantalla amistosa de Error 500 y guardará el error real en `storage/logs/error.log`.
3. ¡Cualquier cambio futuro que hagas en JavaScript o CSS se reflejará instantáneamente en el usuario final gracias al sistema interno automático de "Cache-busting" nativo!

---

## ⚙️ Configuración Post-Despliegue (Alertas Automáticas)

Una vez que el sistema esté subido a cPanel, debes configurar las notificaciones para que lleguen a tu correo y WhatsApp.

### 1. Configurar Tarea Programada (Cron Job)
Para que el sistema revise automáticamente el stock bajo y los lotes por vencer todos los días:
1. En cPanel, busca la sección **Trabajos de Cron (Cron Jobs)**.
2. Configura un nuevo trabajo para que se ejecute **Una vez al día** (por ejemplo, a las `08:00 AM`).
3. En el campo **Comando**, ingresa lo siguiente (ajustando `tu_usuario_cpanel`):
   ```bash
   /usr/local/bin/php /home/tu_usuario_cpanel/public_html/scripts/cron_alertas.php
   ```

### 2. Configurar Correo (SMTP)
1. En cPanel, ve a **Cuentas de Correo Electrónico** y crea una cuenta (ej. `sistema@midominio.com`).
2. Entra a InvSys WMS como **Administrador**.
3. Ve a **Configuración del Sistema** -> pestaña **Correo (SMTP)**.
4. Llena los datos con la información que te proporcionó cPanel (Servidor, Puerto, Usuario y Contraseña).

### 3. Configurar WhatsApp (CallMeBot)
1. Entra a InvSys WMS como **Administrador**.
2. Ve a **Configuración del Sistema** -> pestaña **WhatsApp**.
3. Ingresa el número de teléfono con código de país (ej. `+51999888777`) y tu **API Key** de CallMeBot.
   *(Instrucciones de cómo obtener el API Key están dentro del mismo módulo de configuración).*

## Soporte y Mantenimiento
Si experimentas errores 500 en producción:
1. Revisa el archivo `storage/logs/error.log`.
2. Verifica que las credenciales en el archivo `.env` son correctas.
3. Asegúrate de que las extensiones de PHP requeridas (como PDO) están activadas en el "Selector de versión de PHP" de cPanel.
