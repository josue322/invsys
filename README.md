# 📦 InvSys WMS — Intelligent Warehouse Management System

![Version](https://img.shields.io/badge/version-1.2.0-blue)
![License](https://img.shields.io/badge/license-MIT-green)
![PHP](https://img.shields.io/badge/PHP-8.1+-777bb4)
![Security](https://img.shields.io/badge/Security-Hardened-orange)
![UX](https://img.shields.io/badge/UX-Dark_Mode_Native-purple)

**InvSys WMS** es una plataforma de gestión de almacenes (Warehouse Management System) de alto rendimiento, diseñada para empresas que exigen precisión quirúrgica en sus inventarios. A diferencia de sistemas genéricos, InvSys ha sido optimizado para la **trazabilidad total**, eliminando la incertidumbre en la cadena de suministro.

---

## 🚩 La Problemática del Mercado Actual
En un entorno donde el **e-commerce** y la **logística Just-In-Time** dominan, el error humano en el almacén es el costo más alto de una empresa. La pérdida de productos por vencimiento, el stock fantasma y las discrepancias en auditorías representan hasta un **15% de pérdida en el margen de beneficio anual**.

**InvSys WMS** nace para solucionar esto mediante la automatización de flujos y la digitalización total del activo más importante: la mercancía.

---

## 🚀 Anatomía del Sistema: Módulos de Alto Impacto

### 🛡️ 1. Control de Inventario & Lógica FEFO
El sistema no solo cuenta unidades; gestiona la "salud" del stock.
* **Gestión de Lotes:** Registro obligatorio para productos perecederos.
* **Algoritmo FEFO (First Expire, First Out):** El sistema prioriza automáticamente la salida de productos con fecha de vencimiento más cercana.
* **Alertas Inteligentes:** Notificaciones preventivas (Email/WhatsApp) antes de que un producto caduque o llegue al punto de reorden.

### 🔄 2. Ciclo de Abastecimiento & Compras
Gestión documental completa desde la necesidad hasta la recepción.
* **Órdenes de Compra:** Generación de PDFs profesionales y seguimiento de estados (Borrador, Pendiente, Recibida).
* **Recepción Controlada:** Validación de cantidades físicas contra órdenes de compra para evitar discrepancias con proveedores.

### 🏗️ 3. Logística Interna & Requisiciones
Diseñado para empresas con múltiples departamentos o áreas de consumo.
* **Requisiciones Departamentales:** Flujo de aprobación para solicitudes internas de insumos.
* **Transferencias entre Ubicaciones:** Movimientos controlados entre diferentes zonas del almacén o sucursales.
* **Kardex Dinámico:** Historial inalterable de cada movimiento (Entrada, Salida, Ajuste, Transferencia) con firma digital del usuario responsable.

### 🔍 4. Auditoría & Conteo Cíclico
La verdad del almacén sin detener la producción.
* **Sesiones de Conteo:** Creación de auditorías por categorías o ubicaciones específicas.
* **Conciliación Automática:** Comparación instantánea entre el stock lógico y el físico.
* **Ajustes de Inventario:** Registro detallado de faltantes y sobrantes con justificaciones obligatorias para auditorías externas.

---

## 📸 Experiencia de Usuario (UX/UI) Premium
InvSys ha sido diseñado con un enfoque **User-First**, asegurando que el personal de almacén pueda operar con la mínima curva de aprendizaje.
* **Modo Oscuro Nativo:** Optimizado para reducir la fatiga visual en entornos de baja iluminación (almacenes/bodegas).
* **Interfaz Asíncrona (AJAX):** Navegación fluida y cambios de estado en tiempo real sin recargas de página molestas.
* **Diseño Responsivo:** Funciona perfectamente en Tablets, Smartphones y computadoras de escritorio.

---

## ⚙️ Arquitectura Técnica de Vanguardia

### 🔹 Core MVC (Model-View-Controller)
Arquitectura limpia que separa la lógica de negocio de la interfaz, facilitando el mantenimiento y la escalabilidad.

### 🔹 Seguridad de Grado Bancario
* **Protección contra Inyección SQL:** Uso estricto de Sentencias Preparadas (PDO).
* **Defensa CSRF & XSS:** Tokens de seguridad en cada petición y saneamiento de salidas.
* **Rate Limiting:** Protección activa contra ataques de fuerza bruta en el login.
* **CSP (Content Security Policy):** Bloqueo de ejecución de scripts no autorizados.

### 🔹 Zero-Dependency Framework
A diferencia de sistemas que dependen de cientos de paquetes externos, InvSys utiliza **código nativo optimizado**. Esto garantiza:
1. **Velocidad:** Carga en milisegundos incluso en hostings compartidos.
2. **Seguridad:** Menos vectores de ataque por vulnerabilidades en terceros.
3. **Portabilidad:** Despliegue instantáneo mediante copia de archivos y carga de base de datos.

---

## 🌐 Impacto en la Toma de Decisiones
Gracias al motor de reportes y analítica, la gerencia puede visualizar:
* **Valorización de Inventario:** Valor real del activo en bodega en tiempo real.
* **Índice de Rotación:** Identificación de productos "estrella" y productos de lento movimiento.
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

> **Compromiso de Calidad:** InvSys WMS es un proyecto en constante evolución, diseñado para adaptarse a las necesidades cambiantes del comercio global.
