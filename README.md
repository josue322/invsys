# 📦 InvSys WMS — Sistema Inteligente de Gestión de Inventarios

![Version](https://img.shields.io/badge/version-1.5.0-blue)
![License](https://img.shields.io/badge/license-MIT-green)
![PHP](https://img.shields.io/badge/PHP-8.1+-777bb4)
![Architecture](https://img.shields.io/badge/Architecture-MVC_Native-000000)
![Security](https://img.shields.io/badge/Security-Hardened_CSP-orange)

<p align="center">
  <img src="public/assets/img/logo_inventario.png" alt="InvSys Logo" width="300"/>
</p>

## Descripción del Proyecto

**InvSys WMS** (Warehouse Management System) es una plataforma de gestión de almacenes de alto rendimiento, de **código abierto (Open Source)** y disponible para el público en general. Está construida sobre una arquitectura pura Modelo-Vista-Controlador (MVC) en PHP 8 y diseñada para empresas que exigen precisión quirúrgica en sus inventarios, proporcionando trazabilidad total sin la carga de dependencias de frameworks pesados.

## Objetivos del Sistema

Proporcionar un control absoluto sobre el flujo de mercancías desde que ingresan al almacén hasta que son despachadas, asegurando la integridad de la base de datos mediante transacciones bloqueantes y una interfaz de usuario extremadamente veloz.

## Problema que Resuelve

- Pérdida de trazabilidad de artículos de alto valor (resuelta mediante trazabilidad de Números de Serie).
- Mermas por caducidad (resuelto mediante gestión de lotes FEFO - First Expired, First Out).
- Inconsistencias de stock (resuelto mediante conteo cíclico y validaciones a nivel de base de datos).
- Lentitud operativa (resuelto con escáneres de códigos de barras integrados).

## Público Objetivo

PyMEs, distribuidoras, empresas de logística, ensambladoras y corporativos que requieren auditar flujos de entrada, salida y ajustes de inventario con roles de seguridad estrictos.

## Características Principales

- 🔐 **Login y Seguridad:** Autenticación robusta con protección contra fuerza bruta (Rate Limiting) y tokens CSRF dinámicos.
- 👥 **Roles y Permisos:** Control de acceso granular (Administrador, Supervisor, Operador, Auditor).
- 📦 **CRUD Completo:** Gestión de Productos, Categorías, Ubicaciones y Proveedores.
- 📊 **Dashboard Administrativo:** Analíticas en tiempo real, alertas de stock mínimo y gráficos de movimiento.
- 🔍 **Buscador Avanzado y Escáner:** Integración de escáner de códigos de barras (UPC/EAN) y QR.
- 📄 **Reportes PDF / CSV:** Generación de requisiciones, órdenes de compra e historiales mediante FPDF.
- 🔄 **Historial de Movimientos:** Trazabilidad inmutable de quién, qué y cuándo se movió el inventario.
- 📱 **Responsive Design:** Interfaz adaptativa con Modo Oscuro/Claro nativo y densidades compactas.

## Tecnologías Utilizadas

- **Backend:** PHP >= 8.1 (Nativo)
- **Base de Datos:** MySQL >= 8.0 (con soporte para For Update Locks)
- **Frontend:** HTML5, CSS3, Vanilla JavaScript (ES6+)
- **Estilos:** Bootstrap 5.3 + Iconos de Bootstrap
- **Gestión de Dependencias:** Composer
- **Librerías Adicionales:** Dompdf, FPDF (Generación de Reportes PDF)

## Arquitectura del Proyecto

- **Patrón MVC (Modelo-Vista-Controlador)** construido desde cero para máxima velocidad.
- **Monolito Modular:** Diseñado con Clean Architecture a nivel de servicios (`AlertService`, `SecurityService`, `ExportService`).
- **State Persistence:** Implementación personalizada de `Old Input` flash data para una UX sin interrupciones.

## Requisitos del Sistema

- **Servidor Web:** Apache (con `mod_rewrite` habilitado) o Nginx.
- **PHP:** Versión 8.1 o superior (Extensiones: `pdo_mysql`, `mbstring`, `gd`, `zip`).
- **Base de Datos:** MySQL 8.0+ o MariaDB 10.5+.
- **Herramientas:** Composer (para instalar FPDF/Dompdf).

## Instalación Paso a Paso

### 1. Clonar repositorio
```bash
git clone https://github.com/tu-usuario/invsys.git
```

### 2. Entrar al proyecto
```bash
cd invsys
```

### 3. Instalar dependencias
```bash
composer install
```

### 4. Configurar base de datos
Importa el esquema de la base de datos utilizando el archivo provisto:
```bash
mysql -u root -p invsys_db < database/esquema.sql
```

### 5. Configurar entorno
Renombra el archivo `config.example.php` (si existe) a `config.php` y ajusta las constantes de la base de datos y la `BASE_URL`.

### 6. Levantar servidor local (Desarrollo)
Si utilizas XAMPP, asegúrate de configurar el DocumentRoot apuntando a la carpeta `/public` del proyecto, o levanta el servidor integrado de PHP:
```bash
cd public
php -S localhost:8000
```

## Estructura de Carpetas

```text
/app
  /Controllers     # Lógica de las rutas
  /Models          # Interactores con la DB (ActiveRecord custom)
  /Views           # Plantillas HTML/PHP de presentación
  /core            # Núcleo MVC (Router, Controller base, DB connection)
  /services        # Lógica de negocio encapsulada (Mailing, Reports)
  /helpers         # Funciones globales (url_helper.php)
/public
  /assets          # CSS, JS (Vanilla), Imágenes e Íconos
  index.php        # Front Controller
/routes
  web.php          # Definición de rutas del sistema
/database          # Scripts SQL de migraciones y esquemas
```

## Módulos del Sistema

1. **Gestión de Productos:** Catálogo principal con soporte para SKU y Códigos de Barras.
2. **Trazabilidad Unitaria (Fase 3):** Seguimiento exhaustivo de Números de Serie individuales.
3. **Control FEFO:** Administración de Lotes y Fechas de Vencimiento para perecederos.
4. **Movimientos de Inventario:** Entradas, Salidas y Ajustes blindados por transacciones atómicas.
5. **Requisiciones y Despachos:** Flujo interno de solicitudes de material para departamentos.
6. **Auditoría Física (Conteos):** Conciliación de inventario en tiempo real.

## Roles de Usuario

- **Administrador:** Acceso total a configuración, usuarios y reportería sensible.
- **Supervisor:** Capacidad de realizar ajustes y auditorías cíclicas.
- **Operador:** Limitado al registro diario de entradas, salidas y escaneo de códigos de barras.

## Seguridad Implementada

- **Hash de Contraseñas:** Encriptación robusta usando `password_hash()` (Bcrypt).
- **Protección CSRF:** Token generado dinámicamente y validado en todas las peticiones `POST`.
- **Content Security Policy (CSP):** Eliminación completa de `unsafe-inline` scripts.
- **Protección Fuerza Bruta:** Rate Limiter basado en IPs en el módulo de Autenticación.
- **Bloqueo Transaccional (FOR UPDATE):** Evita condiciones de carrera durante actualizaciones simultáneas de stock.
- **Auditoría Interna:** `SecurityService::logAction()` registra quién, cómo y cuándo alteró un dato crítico.

## Scripts y Comandos Útiles

Dado que es un sistema MVC nativo (no requiere de un CLI pesado como Laravel artisan), las tareas se realizan mediante composer:

```bash
# Instalar / Actualizar librerías de reportes (FPDF)
composer install

# Optimizar el autoloader de clases
composer dump-autoload -o
```

## Estado del Proyecto

- **Estado actual:** Producción (Release v1.5.0).
- Todos los módulos CORE de gestión y trazabilidad han sido implementados y auditados.

## Buenas Prácticas Aplicadas

- **Clean Code & PSR-12:** Código altamente legible y estructurado bajo los estándares de PHP.
- **Separación de Responsabilidades:** Vistas estrictamente separadas de los Controladores.
- **UX/UI Consistente:** Retención de variables (Old Input) al fallar las validaciones.
- **Optimización SQL:** Consultas tipadas y preparadas mediante PDO, evitando inyecciones SQL.

## Compatibilidad

- **Navegadores Soportados:** Chrome, Firefox, Safari, Edge (Desktop & Mobile).
- **Dispositivos:** 100% responsivo para Tablets y Smartphones de almacén.

## Autor

Desarrollado por **Josué Lopez**.  
*Programador JR. / Arquitectura de Sistemas Web*

## Licencia

Este proyecto es de **código abierto** y está disponible para el público bajo la licencia **MIT**. Puedes utilizarlo, modificarlo y distribuirlo libremente de acuerdo con los términos de dicha licencia.

---
**Desarrollado con precisión y robustez para ecosistemas empresariales críticos.**
