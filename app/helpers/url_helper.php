<?php
/**
 * InvSys - URL Helper
 * 
 * Funciones auxiliares para generación de URLs y redirección.
 */

/**
 * Generar URL completa a partir de una ruta relativa.
 *
 * @param string $path Ruta relativa (ej: 'productos', 'productos/crear')
 * @return string URL completa
 */
function url(string $path = ''): string
{
    $base = rtrim(BASE_URL, '/');
    $path = ltrim($path, '/');
    return $path ? "{$base}/{$path}" : $base;
}

/**
 * Generar URL a un asset (CSS, JS, imagen).
 *
 * @param string $path Ruta del asset relativa a /assets/ (ej: 'css/style.css')
 * @return string URL completa del asset
 */
function asset(string $path): string
{
    return rtrim(ASSET_URL, '/') . '/' . ltrim($path, '/');
}

/**
 * Obtener la URL actual.
 *
 * @return string
 */
function currentUrl(): string
{
    return $_SERVER['REQUEST_URI'] ?? '';
}

/**
 * Verificar si la ruta actual coincide con una ruta dada.
 * Útil para marcar ítems activos en el sidebar.
 *
 * @param string $path Ruta a comparar
 * @return bool
 */
function isCurrentRoute(string $path): bool
{
    $current = trim(parse_url(currentUrl(), PHP_URL_PATH), '/');
    $compare = trim(url($path), '/');
    
    // Comparar directamente
    if ($current === $compare) {
        return true;
    }

    // Comparar sin el base URL
    $currentClean = str_replace(trim(BASE_URL, '/'), '', $current);
    $currentClean = trim($currentClean, '/');
    $path = trim($path, '/');

    return $currentClean === $path;
}

/**
 * Verificar si la ruta actual comienza con un prefijo dado.
 *
 * @param string $prefix Prefijo de ruta
 * @return bool
 */
function isRoutePrefix(string $prefix): bool
{
    $current = trim(parse_url(currentUrl(), PHP_URL_PATH), '/');
    $currentClean = str_replace(trim(BASE_URL, '/'), '', $current);
    $currentClean = trim($currentClean, '/');
    $prefix = trim($prefix, '/');

    return str_starts_with($currentClean, $prefix);
}

/**
 * Check if the current URL matches an exact route path.
 *
 * @param string $route Exact route path to match
 * @return bool
 */
function isRoute(string $route): bool
{
    $current = trim(parse_url(currentUrl(), PHP_URL_PATH), '/');
    $currentClean = str_replace(trim(BASE_URL, '/'), '', $current);
    $currentClean = trim($currentClean, '/');
    $route = trim($route, '/');

    return $currentClean === $route;
}

/**
 * Generar token CSRF como campo hidden de formulario.
 *
 * @return string HTML del campo hidden
 */
function csrfField(): string
{
    $token = $_SESSION['_csrf_token'] ?? '';
    return '<input type="hidden" name="_csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
}

/**
 * Formatear precio con símbolo de moneda.
 *
 * @param float $amount Cantidad
 * @return string Precio formateado (ej: $1,250.00)
 */
function formatMoney(float $amount): string
{
    static $symbol = null;
    if ($symbol === null) {
        $symbol = Config::get('moneda_simbolo', '$');
    }
    return $symbol . number_format($amount, 2, '.', ',');
}

/**
 * Formatear fecha en formato legible según la configuración del sistema.
 * Lee la clave 'formato_fecha' (DD/MM/YYYY, MM/DD/YYYY, YYYY-MM-DD)
 * y la convierte al formato PHP equivalente.
 *
 * @param string $date Fecha en formato MySQL (Y-m-d H:i:s)
 * @param bool|string $withTime true = fecha+hora, false = solo fecha, 'short' = día/mes + hora (sin año)
 * @return string Fecha formateada según la configuración
 */
function formatDate(string $date, bool|string $withTime = true): string
{
    static $phpFormat = null;
    static $shortFormat = null;
    if ($phpFormat === null) {
        $configFormat = Config::get('formato_fecha', 'DD/MM/YYYY');
        $phpFormat = match ($configFormat) {
            'MM/DD/YYYY' => 'm/d/Y',
            'YYYY-MM-DD' => 'Y-m-d',
            default      => 'd/m/Y',   // DD/MM/YYYY
        };
        $shortFormat = match ($configFormat) {
            'MM/DD/YYYY' => 'm/d',
            'YYYY-MM-DD' => 'm-d',
            default      => 'd/m',
        };
    }

    if ($withTime === 'short') {
        $fmt = $shortFormat . ' H:i';
    } else {
        $fmt = $withTime ? $phpFormat . ' H:i' : $phpFormat;
    }
    return date($fmt, strtotime($date));
}

/**
 * Truncar texto a un número de caracteres.
 *
 * @param string $text Texto a truncar
 * @param int $length Longitud máxima
 * @param string $suffix Sufijo cuando se trunca
 * @return string
 */
function truncate(string $text, int $length = 50, string $suffix = '...'): string
{
    if (mb_strlen($text) <= $length) {
        return $text;
    }
    return mb_substr($text, 0, $length) . $suffix;
}

/**
 * Obtener el nombre del sistema desde la configuración.
 *
 * @return string Nombre del sistema
 */
function systemName(): string
{
    static $name = null;
    if ($name === null) {
        $name = Config::get('nombre_sistema', 'InvSys');
    }
    return $name;
}

/**
 * Obtener un valor de configuración del sistema.
 *
 * @param string $key Clave de configuración
 * @param mixed $default Valor por defecto
 * @return mixed
 */
function sysConfig(string $key, mixed $default = null): mixed
{
    return Config::get($key, $default);
}

/**
 * Obtener la URL del logo del sistema, o null si no hay logo.
 *
 * @return string|null URL del logo o null
 */
function systemLogo(): ?string
{
    static $logo = false;
    if ($logo === false) {
        $file = sysConfig('logo', '');
        if ($file && file_exists(PUBLIC_PATH . '/assets/img/' . $file)) {
            $logo = asset('img/' . $file);
        } else {
            $logo = null;
        }
    }
    return $logo;
}

/**
 * Obtener la URL de la imagen de un producto.
 * Retorna un placeholder SVG inline si no tiene imagen.
 *
 * @param string|null $filename Nombre del archivo de imagen
 * @return string URL de la imagen o data URI del placeholder
 */
function productImage(?string $filename): string
{
    if ($filename && file_exists(PUBLIC_PATH . '/assets/img/productos/' . $filename)) {
        return asset('img/productos/' . $filename);
    }
    // Placeholder SVG — icono de caja
    return 'data:image/svg+xml,' . rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" fill="none"><rect width="64" height="64" rx="12" fill="#f0f0f5"/><path d="M32 16L44 22V42L32 48L20 42V22L32 16Z" stroke="#b0b0c0" stroke-width="2" fill="none"/><path d="M32 28L44 22" stroke="#b0b0c0" stroke-width="2"/><path d="M32 28L20 22" stroke="#b0b0c0" stroke-width="2"/><path d="M32 28V48" stroke="#b0b0c0" stroke-width="2"/></svg>');
}

