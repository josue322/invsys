# 📦 InvSys WMS — Intelligent Warehouse Management System

![Version](https://img.shields.io/badge/version-1.5.0-blue)
![License](https://img.shields.io/badge/license-MIT-green)
![PHP](https://img.shields.io/badge/PHP-8.1+-777bb4)
![Architecture](https://img.shields.io/badge/Architecture-MVC_Native-000000)
![Security](https://img.shields.io/badge/Security-Hardened_CSP-orange)
![UX](https://img.shields.io/badge/UX-Dark_Mode_Native-purple)

**InvSys WMS** es una plataforma de gestión de almacenes (Warehouse Management System) de alto rendimiento, diseñada para empresas que exigen precisión quirúrgica en sus inventarios. A diferencia de sistemas genéricos, InvSys ha sido optimizado para la **trazabilidad total**, eliminando la incertidumbre en la cadena de suministro mediante una arquitectura robusta MVC sin dependencias pesadas.

---

## 📊 Auditoría Técnica y Estructura del Sistema (Versión Actual)

InvSys ha crecido hasta convertirse en un ERP logístico robusto, manteniendo un código limpio y separado bajo el patrón **Modelo-Vista-Controlador (MVC)**. Actualmente el núcleo del sistema se compone de **más de 120 archivos especializados**:

*   **🎛️ Controladores (23 archivos):** Orquestan la lógica de negocio, desde la autenticación (`AuthController`) hasta operaciones complejas de inventario (`ConteoController`, `RequisicionController`, `DevolucionController`).
*   **🧠 Modelos (27 archivos):** Interactúan con la base de datos de forma segura usando PDO. Incluyen entidades avanzadas como `AnalisisInventario`, `Movimiento`, `Lote` y `OrdenCompra`.
*   **🖥️ Vistas (62 archivos):** Interfaces modulares divididas por entidad. Incluyen componentes reutilizables (layouts, modales) y vistas dedicadas para operaciones como *Kardex*, *Punto de Venta/Escáner*, y *Reportes*.
*   **⚙️ Core & Servicios:** Motor de enrutamiento (`routes/web.php`), servicios de exportación (`ExportService`) y envíos de correo SMTP (`MailService`).
*   **🎨 Assets (13 JS / 2 CSS):** Arquitectura Frontend basada en eventos delegados, cumpliendo al 100% con directivas de **Content Security Policy (CSP)**.

---

## 🚀 Módulos Principales de Alto Impacto

### 🛡️ 1. Control de Inventario & Lógica FEFO
El sistema no solo cuenta unidades; gestiona la "salud" del stock.
* **Gestión de Lotes:** Registro obligatorio y seguimiento visual para productos perecederos directamente desde el Kardex.
* **Algoritmo FEFO (First Expire, First Out):** El sistema prioriza automáticamente la salida de productos con fecha de vencimiento más cercana.
* **Alertas Inteligentes:** Notificaciones preventivas automáticas (Email) antes de que un producto caduque o llegue al punto de reorden.

### 🔄 2. Ciclo de Abastecimiento & Compras
Gestión documental completa desde la necesidad hasta la recepción.
* **Catálogo de Proveedores:** Vinculación de productos con proveedores, registrando costos y tiempos de entrega como valores referenciales.
* **Órdenes de Compra:** Generación de PDFs profesionales (vía FPDF) y seguimiento de estados (Borrador, Pendiente, Recibida).
* **Recepción Controlada:** Validación de cantidades físicas contra órdenes de compra para evitar discrepancias.

### 🏗️ 3. Logística Interna, Requisiciones y Devoluciones
Diseñado para empresas con múltiples departamentos o áreas de consumo.
* **Requisiciones Departamentales:** Flujo de aprobación para solicitudes internas de insumos, con deducción automática de stock.
* **Gestión de Devoluciones:** Reingreso controlado de mercancía a través de inspección de calidad.
* **Kardex Dinámico:** Historial inalterable de cada movimiento (Entrada, Salida, Ajuste, Transferencia, Devolución) con firma digital del usuario responsable y detalle de lotes.

### 🔍 4. Auditoría, Conteo Cíclico y Escaneo
La verdad del almacén sin detener la producción.
* **Sesiones de Conteo:** Creación de auditorías ciegas por ubicaciones específicas.
* **Escáner de Código de Barras:** Integración nativa con JSBarcode para ingreso y salida rápida de productos.
* **Ajustes de Inventario:** Registro detallado de faltantes y sobrantes con justificaciones obligatorias para auditorías externas.

---

## ⚙️ Arquitectura de Seguridad y Cumplimiento

*   **Zero Inline-Scripts (CSP):** El frontend está completamente blindado contra vulnerabilidades de inyección básica (XSS) al delegar toda la lógica a archivos JS externos.
*   **Protección contra Inyección SQL:** Uso estricto de Sentencias Preparadas (PDO) en el 100% de los modelos.
*   **Defensa CSRF:** Tokens de seguridad obligatorios en cada petición POST y saneamiento de salidas.
*   **Rate Limiting:** Protección activa mediante bloqueo temporal contra ataques de fuerza bruta en el login.

---

## 📸 Experiencia de Usuario (UX/UI) Premium
InvSys ha sido diseñado con un enfoque **User-First**, asegurando que el personal de almacén pueda operar con la mínima curva de aprendizaje.
*   **Modo Oscuro Nativo:** Optimizado para reducir la fatiga visual en entornos de baja iluminación (almacenes/bodegas).
*   **Validación Asíncrona:** Formularios que informan errores en tiempo real y transiciones fluidas.
*   **Tipografía y Diseño Tabular:** Lectura de datos clara, usando etiquetas translúcidas (`bg-opacity-10`) e iconos dinámicos para el estado del inventario.

---

## 🌐 Analítica Avanzada
Gracias al motor de reportes y analítica, la gerencia puede visualizar:
* **Análisis ABC:** Clasificación automática de productos por valorización y rotación.
* **Valorización de Inventario:** Valor real del activo en bodega en tiempo real usando el costo promedio.
* **Eficiencia Operativa:** Registro de actividad de usuarios para medir la productividad del equipo logístico.

---

## 👥 Perfil de Usuario Ideal
* **Centros de Distribución (CEDIS).**
* **Cadenas de Restaurantes y Hoteles.**
* **Laboratorios y Farmacias.**
* **Tiendas de Retail con alta rotación de SKU.**

---

## 📜 Licencia
Este software se distribuye bajo la **Licencia MIT**.

---

> **Compromiso de Calidad:** InvSys WMS es un proyecto de grado corporativo, diseñado para adaptarse a las necesidades cambiantes del comercio global y la logística 4.0.
