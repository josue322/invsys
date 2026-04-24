<?php
/**
 * InvSys - BarcodeLookupService
 *
 * Consulta APIs externas gratuitas para obtener información de un producto
 * a partir de su código de barras (UPC/EAN).
 *
 * APIs utilizadas:
 * - Open Food Facts (alimentos, bebidas, cosméticos) — sin API key
 * - UPCitemdb (productos generales) — sin API key, 100 req/día
 */

class BarcodeLookupService
{
    /** @var int Timeout en segundos para cada API */
    private int $timeout = 4;

    /** @var string User-Agent requerido por Open Food Facts */
    private string $userAgent = 'InvSys/1.0 (inventory-system)';

    /**
     * Buscar información del producto por código de barras.
     * Intenta Open Food Facts primero, luego UPCitemdb como fallback.
     *
     * @param string $barcode Código de barras (UPC/EAN)
     * @return array|null Datos del producto o null si no se encontró
     */
    public function lookup(string $barcode): ?array
    {
        $barcode = preg_replace('/[^0-9]/', '', $barcode);

        if (empty($barcode)) {
            return null;
        }

        // Intentar Open Food Facts primero (mayor cobertura LatAm)
        $result = $this->lookupOpenFoodFacts($barcode);

        if ($result !== null) {
            $result['fuente'] = 'Open Food Facts';
            return $result;
        }

        // Fallback: UPCitemdb
        $result = $this->lookupUPCitemdb($barcode);

        if ($result !== null) {
            $result['fuente'] = 'UPCitemdb';
            return $result;
        }

        return null;
    }

    /**
     * Consultar Open Food Facts API.
     * Endpoint: https://world.openfoodfacts.org/api/v2/product/{barcode}
     *
     * @param string $barcode
     * @return array|null
     */
    private function lookupOpenFoodFacts(string $barcode): ?array
    {
        $url = "https://world.openfoodfacts.org/api/v2/product/{$barcode}.json";

        $data = $this->httpGet($url);

        if ($data === null || ($data['status'] ?? 0) != 1) {
            return null;
        }

        $product = $data['product'] ?? [];

        if (empty($product)) {
            return null;
        }

        $nombre = $product['product_name'] ?? $product['product_name_es'] ?? '';
        $marca = $product['brands'] ?? '';

        // Si no tiene nombre, no es útil
        if (empty(trim($nombre)) && empty(trim($marca))) {
            return null;
        }

        // Construir nombre descriptivo
        $nombreFinal = trim($nombre);
        if (!empty($marca) && stripos($nombreFinal, $marca) === false) {
            $nombreFinal = trim("{$marca} - {$nombreFinal}");
        }

        return [
            'nombre'      => mb_substr($nombreFinal, 0, 200),
            'descripcion' => mb_substr($this->buildDescription($product), 0, 500),
            'marca'       => mb_substr($marca, 0, 100),
            'imagen_url'  => $product['image_front_url'] ?? $product['image_url'] ?? null,
            'categoria'   => $product['categories'] ?? null,
        ];
    }

    /**
     * Consultar UPCitemdb API (free tier, 100 req/día).
     * Endpoint: https://api.upcitemdb.com/prod/trial/lookup?upc={barcode}
     *
     * @param string $barcode
     * @return array|null
     */
    private function lookupUPCitemdb(string $barcode): ?array
    {
        $url = "https://api.upcitemdb.com/prod/trial/lookup?upc={$barcode}";

        $data = $this->httpGet($url);

        if ($data === null || ($data['code'] ?? '') !== 'OK') {
            return null;
        }

        $items = $data['items'] ?? [];

        if (empty($items)) {
            return null;
        }

        $item = $items[0];
        $nombre = $item['title'] ?? '';
        $marca = $item['brand'] ?? '';

        if (empty(trim($nombre))) {
            return null;
        }

        $descripcion = $item['description'] ?? '';
        if (empty($descripcion) && !empty($item['category'] ?? '')) {
            $descripcion = "Categoría: {$item['category']}";
        }

        // Obtener imagen
        $imagen = null;
        $images = $item['images'] ?? [];
        if (!empty($images)) {
            $imagen = $images[0];
        }

        return [
            'nombre'      => mb_substr(trim($nombre), 0, 200),
            'descripcion' => mb_substr(trim($descripcion), 0, 500),
            'marca'       => mb_substr(trim($marca), 0, 100),
            'imagen_url'  => $imagen,
            'categoria'   => $item['category'] ?? null,
        ];
    }

    /**
     * Construir descripción a partir de los datos de Open Food Facts.
     *
     * @param array $product
     * @return string
     */
    private function buildDescription(array $product): string
    {
        $parts = [];

        if (!empty($product['generic_name'])) {
            $parts[] = $product['generic_name'];
        }

        if (!empty($product['quantity'])) {
            $parts[] = "Contenido: {$product['quantity']}";
        }

        if (!empty($product['categories'])) {
            $cats = explode(',', $product['categories']);
            $cats = array_slice($cats, 0, 3);
            $parts[] = "Categoría: " . implode(', ', array_map('trim', $cats));
        }

        return implode(' | ', $parts);
    }

    /**
     * Realizar petición HTTP GET con timeout.
     *
     * @param string $url
     * @return array|null JSON decodificado o null si falla
     */
    private function httpGet(string $url): ?array
    {
        $context = stream_context_create([
            'http' => [
                'method'  => 'GET',
                'timeout' => $this->timeout,
                'header'  => "User-Agent: {$this->userAgent}\r\nAccept: application/json\r\n",
                'ignore_errors' => true,
            ],
            'ssl' => [
                'verify_peer'      => false,
                'verify_peer_name' => false,
            ],
        ]);

        try {
            $response = @file_get_contents($url, false, $context);

            if ($response === false) {
                return null;
            }

            $data = json_decode($response, true);

            return is_array($data) ? $data : null;
        } catch (\Throwable $e) {
            error_log("BarcodeLookupService error: {$e->getMessage()} (URL: {$url})");
            return null;
        }
    }
}
