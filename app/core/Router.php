<?php
/**
 * InvSys - Router
 * 
 * Sistema de enrutamiento que mapea URLs a controladores y acciones.
 * Soporta rutas GET/POST, parámetros dinámicos {id}, y middleware.
 */

class Router
{
    /**
     * @var array Rutas registradas agrupadas por método HTTP
     */
    private array $routes = [];

    /**
     * Cargar rutas desde el archivo de definición.
     *
     * @param array $routes Array de definiciones de rutas
     */
    public function loadRoutes(array $routes): void
    {
        foreach ($routes as $route) {
            $method = strtoupper($route['method']);
            $path = trim($route['path'], '/');
            $this->routes[$method][] = [
                'path'       => $path,
                'controller' => $route['controller'],
                'action'     => $route['action'],
                'middleware'  => $route['middleware'] ?? [],
                'pattern'    => $this->buildPattern($path),
            ];
        }
    }

    /**
     * Construir patrón regex a partir de la ruta con parámetros dinámicos.
     * Convierte {id} a grupos de captura (?P<id>[0-9]+)
     * Convierte {slug} a (?P<slug>[a-zA-Z0-9_-]+)
     *
     * @param string $path Ruta con posibles parámetros dinámicos
     * @return string Patrón regex
     */
    private function buildPattern(string $path): string
    {
        // Escapar barras
        $pattern = str_replace('/', '\/', $path);

        // Reemplazar {id} por grupo numérico
        $pattern = preg_replace('/\{id\}/', '(?P<id>[0-9]+)', $pattern);

        // Reemplazar otros parámetros {param} por grupo alfanumérico
        $pattern = preg_replace('/\{(\w+)\}/', '(?P<$1>[a-zA-Z0-9_-]+)', $pattern);

        return '/^' . $pattern . '$/';
    }

    /**
     * Despachar la petición a la ruta correspondiente.
     *
     * @param string $url URL solicitada
     * @param string $method Método HTTP (GET, POST)
     */
    public function dispatch(string $url, string $method): void
    {
        $method = strtoupper($method);

        // Verificar si hay rutas registradas para este método
        if (!isset($this->routes[$method])) {
            $this->sendError(404, 'Página no encontrada');
            return;
        }

        // Buscar coincidencia de ruta
        foreach ($this->routes[$method] as $route) {
            if (preg_match($route['pattern'], $url, $matches)) {
                // Extraer parámetros nombrados
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                // Ejecutar middlewares
                if (!$this->runMiddlewares($route['middleware'])) {
                    return;
                }

                // Instanciar controlador y ejecutar acción
                $this->callAction($route['controller'], $route['action'], $params);
                return;
            }
        }

        // Ninguna ruta coincidió
        $this->sendError(404, 'Página no encontrada');
    }

    /**
     * Ejecutar la cadena de middlewares.
     *
     * @param array $middlewares Lista de middlewares a ejecutar
     * @return bool true si todos los middlewares pasan, false si alguno detiene la ejecución
     */
    private function runMiddlewares(array $middlewares): bool
    {
        foreach ($middlewares as $middleware) {
            $result = Middleware::handle($middleware);
            if ($result === false) {
                return false;
            }
        }
        return true;
    }

    /**
     * Instanciar controlador y ejecutar la acción.
     *
     * @param string $controllerName Nombre del controlador
     * @param string $action Método del controlador a ejecutar
     * @param array $params Parámetros extraídos de la URL
     */
    private function callAction(string $controllerName, string $action, array $params): void
    {
        // Verificar que la clase del controlador existe
        if (!class_exists($controllerName)) {
            $this->sendError(500, "Controlador '{$controllerName}' no encontrado");
            return;
        }

        $controller = new $controllerName();

        // Verificar que el método existe
        if (!method_exists($controller, $action)) {
            $this->sendError(500, "Acción '{$action}' no encontrada en {$controllerName}");
            return;
        }

        // Llamar la acción con los parámetros
        call_user_func_array([$controller, $action], $params);
    }

    /**
     * Enviar página de error.
     *
     * @param int $code Código HTTP
     * @param string $message Mensaje de error
     */
    private function sendError(int $code, string $message): void
    {
        http_response_code($code);
        
        // Si existe una vista de error, mostrarla
        $errorView = APP_PATH . "/views/errors/{$code}.php";
        if (file_exists($errorView)) {
            require $errorView;
        } else {
            echo "<h1>Error {$code}</h1><p>{$message}</p>";
        }
    }
}
