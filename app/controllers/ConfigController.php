<?php
/**
 * InvSys - ConfigController
 */

class ConfigController extends Controller
{
    private ConfigService $configService;
    private SecurityService $securityService;

    public function __construct()
    {
        $this->configService = new ConfigService();
        $this->securityService = SecurityService::getInstance();
    }

    /**
     * Mostrar panel de configuración.
     */
    public function index(): void
    {
        $configs = $this->configService->getAll();
        $csrfToken = $this->generateCSRF();
        $flash = $this->getFlash();

        $this->view('configuracion/index', [
            'titulo'    => 'Configuración del Sistema',
            'configs'   => $configs,
            'csrfToken' => $csrfToken,
            'flash'     => $flash,
        ]);
    }

    /**
     * Actualizar configuraciones.
     */
    public function update(): void
    {
        if (!$this->validateCSRF()) {
            $this->redirect('configuracion');
            return;
        }

        $configs = $_POST['config'] ?? [];

        // Procesar subida de logo
        $this->handleLogoUpload();

        if (!empty($configs) && is_array($configs)) {
            $this->configService->updateMultiple($configs);

            // Sincronizar tema con la sesión actual
            if (isset($configs['tema_defecto'])) {
                $_SESSION['user_theme'] = $configs['tema_defecto'];
            }

            $this->securityService->logAction(
                currentUserId(), 'actualizar_configuracion', 'configuracion',
                'Configuraciones del sistema actualizadas: ' . implode(', ', array_keys($configs))
            );

            $this->setFlash('success', 'Configuración actualizada exitosamente.');
        }

        $this->redirect('configuracion');
    }

    /**
     * Manejar la subida del logo.
     */
    private function handleLogoUpload(): void
    {
        if (!isset($_FILES['logo']) || $_FILES['logo']['error'] !== UPLOAD_ERR_OK) {
            return;
        }

        $file = $_FILES['logo'];
        $allowedTypes = ['image/png', 'image/jpeg', 'image/svg+xml', 'image/webp'];
        $maxSize = 2 * 1024 * 1024; // 2MB

        // Validar tipo
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $allowedTypes)) {
            $this->setFlash('error', 'Formato de logo no válido. Use PNG, JPG, SVG o WebP.');
            return;
        }

        // Validar tamaño
        if ($file['size'] > $maxSize) {
            $this->setFlash('error', 'El logo es demasiado grande. Máximo: 2MB.');
            return;
        }

        // Generar nombre único
        $extension = match ($mimeType) {
            'image/png' => 'png',
            'image/jpeg' => 'jpg',
            'image/svg+xml' => 'svg',
            'image/webp' => 'webp',
            default => 'png',
        };
        $filename = 'logo_' . time() . '.' . $extension;
        $destination = PUBLIC_PATH . '/assets/img/' . $filename;

        // Eliminar logo anterior
        $oldLogo = $this->configService->get('logo');
        if ($oldLogo && file_exists(PUBLIC_PATH . '/assets/img/' . $oldLogo)) {
            @unlink(PUBLIC_PATH . '/assets/img/' . $oldLogo);
        }

        // Mover archivo
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            $this->configService->set('logo', $filename);
            $this->securityService->logAction(
                currentUserId(), 'actualizar_logo', 'configuracion',
                'Logo del sistema actualizado: ' . $filename
            );
        } else {
            $this->setFlash('error', 'Error al subir el logo. Verifique los permisos del directorio.');
        }
    }
}
