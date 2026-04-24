<?php
/**
 * InvSys - Controller Base
 * 
 * Clase base que todos los controladores deben extender.
 * Proporciona métodos para renderizar vistas, redireccionar,
 * enviar respuestas JSON y gestionar datos de sesión.
 */

class Controller
{
    /**
     * Renderizar una vista con layout.
     *
     * @param string $view Ruta de la vista (ej: 'productos/index')
     * @param array $data Datos a pasar a la vista
     * @param bool $withLayout Si se debe incluir el layout completo (header, sidebar, footer)
     */
    protected function view(string $view, array $data = [], bool $withLayout = true): void
    {
        // Extraer datos para que estén disponibles como variables en la vista
        extract($data);

        // Obtener datos globales disponibles en todas las vistas
        $currentUser = currentUser();
        $alertasNoLeidas = $this->getUnreadAlertCount();
        $temaActual = $this->getCurrentTheme();
        $uiSettings = $this->getUISettings();

        $viewFile = APP_PATH . "/views/{$view}.php";

        if (!file_exists($viewFile)) {
            throw new \RuntimeException("Vista no encontrada: {$view}");
        }

        if ($withLayout) {
            // Renderizar con layout completo
            require APP_PATH . '/views/layouts/header.php';
            require APP_PATH . '/views/layouts/sidebar.php';
            require $viewFile;
            require APP_PATH . '/views/layouts/footer.php';
        } else {
            // Renderizar solo la vista (útil para login, errores, etc.)
            require $viewFile;
        }
    }

    /**
     * Redireccionar a otra URL.
     *
     * @param string $path Ruta relativa (ej: 'productos')
     */
    protected function redirect(string $path): void
    {
        header('Location: ' . url($path));
        exit;
    }

    /**
     * Enviar respuesta JSON.
     *
     * @param mixed $data Datos a serializar
     * @param int $statusCode Código HTTP de respuesta
     */
    protected function json(mixed $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Establecer mensaje flash en sesión.
     *
     * @param string $type Tipo de mensaje (success, error, warning, info)
     * @param string $message Contenido del mensaje
     */
    protected function setFlash(string $type, string $message): void
    {
        $_SESSION['flash'] = [
            'type'    => $type,
            'message' => $message,
        ];
    }

    /**
     * Obtener y limpiar mensaje flash de la sesión.
     *
     * @return array|null Mensaje flash o null si no existe
     */
    protected function getFlash(): ?array
    {
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }
        return null;
    }

    /**
     * Obtener datos del POST de forma segura.
     *
     * Los datos se devuelven sin escapar HTML para evitar doble-encoding.
     * La sanitización HTML debe hacerse al MOSTRAR datos en las vistas,
     * no al recibirlos. Las vistas ya usan htmlspecialchars().
     *
     * @param string $key Nombre del campo
     * @param mixed $default Valor por defecto si no existe
     * @return mixed Valor del campo limpio (trimmed)
     */
    protected function input(string $key, mixed $default = null): mixed
    {
        if (isset($_POST[$key])) {
            if (is_string($_POST[$key])) {
                return trim($_POST[$key]);
            }
            return $_POST[$key];
        }
        return $default;
    }

    /**
     * Obtener parámetro del GET de forma segura.
     *
     * Los datos se devuelven sin escapar HTML. La sanitización
     * debe realizarse al mostrar en vistas con htmlspecialchars().
     *
     * @param string $key Nombre del parámetro
     * @param mixed $default Valor por defecto
     * @return mixed Valor del parámetro limpio (trimmed)
     */
    protected function query(string $key, mixed $default = null): mixed
    {
        if (isset($_GET[$key])) {
            if (is_string($_GET[$key])) {
                return trim($_GET[$key]);
            }
            return $_GET[$key];
        }
        return $default;
    }

    /**
     * Obtener cantidad de registros por página desde el query string.
     * Valida contra opciones permitidas (10, 15, 25, 50).
     * Fallback: configuración del sistema o 15.
     *
     * @return int
     */
    protected function getPerPage(): int
    {
        $allowed = [10, 15, 25, 50];
        $perPage = (int) $this->query('per_page', 0);

        if (in_array($perPage, $allowed)) {
            return $perPage;
        }

        return (int) sysConfig('registros_por_pagina', '15');
    }

    /**
     * Validar token CSRF.
     *
     * @return bool
     */
    protected function validateCSRF(): bool
    {
        $token = $_POST['_csrf_token'] ?? '';
        $sessionToken = $_SESSION['_csrf_token'] ?? '';

        if (empty($token) || empty($sessionToken) || !hash_equals($sessionToken, $token)) {
            $this->setFlash('error', 'Token de seguridad inválido. Intente de nuevo.');
            return false;
        }
        return true;
    }

    /**
     * Generar token CSRF y almacenarlo en sesión.
     *
     * @return string Token generado
     */
    protected function generateCSRF(): string
    {
        $token = bin2hex(random_bytes(32));
        $_SESSION['_csrf_token'] = $token;
        return $token;
    }

    /**
     * Obtener cantidad de alertas no leídas (para el badge del navbar).
     *
     * @return int
     */
    private function getUnreadAlertCount(): int
    {
        if (!isLoggedIn()) {
            return 0;
        }
        try {
            $alerta = new Alerta();
            return $alerta->countUnread();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Obtener el tema actual del usuario.
     * Prioridad: sesión del usuario > configuración global > 'light'
     *
     * @return string 'light' o 'dark'
     */
    private function getCurrentTheme(): string
    {
        if (!isLoggedIn()) {
            return sysConfig('tema_defecto', 'light');
        }
        // Si hay tema en sesión, usarlo; si no, leer config del sistema
        return $_SESSION['user_theme'] ?? sysConfig('tema_defecto', 'light');
    }

    /**
     * Obtener configuraciones de interfaz de usuario.
     *
     * @return array
     */
    private function getUISettings(): array
    {
        return [
            'sidebar_colapsable' => sysConfig('sidebar_colapsable', '1'),
            'densidad_compacta'  => sysConfig('densidad_compacta', '0'),
            'animaciones'        => sysConfig('animaciones', '1'),
        ];
    }
}
