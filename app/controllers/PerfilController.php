<?php
/**
 * InvSys - PerfilController
 * 
 * Controlador para el perfil del usuario autenticado.
 * Todos los roles pueden acceder a su propio perfil.
 */

class PerfilController extends Controller
{
    private Usuario $usuarioModel;
    private Log $logModel;
    private SecurityService $securityService;

    public function __construct()
    {
        $this->usuarioModel = new Usuario();
        $this->logModel = new Log();
        $this->securityService = SecurityService::getInstance();
    }

    /**
     * Mostrar página de perfil.
     */
    public function index(): void
    {
        $userId = currentUserId();
        $usuario = $this->usuarioModel->findWithRole($userId);

        if (!$usuario) {
            $this->setFlash('error', 'Error al cargar el perfil.');
            $this->redirect('dashboard');
            return;
        }

        // Obtener historial de actividad del usuario (últimas 15)
        $actividad = $this->logModel->getByUserId($userId, 1, 15);

        $csrfToken = $this->generateCSRF();
        $flash = $this->getFlash();

        $this->view('perfil/index', [
            'titulo'    => 'Mi Perfil',
            'usuario'   => $usuario,
            'actividad' => $actividad,
            'csrfToken' => $csrfToken,
            'flash'     => $flash,
        ]);
    }

    /**
     * Actualizar datos personales (nombre y email).
     */
    public function updateInfo(): void
    {
        if (!$this->validateCSRF()) {
            $this->redirect('perfil');
            return;
        }

        $userId = currentUserId();
        $nombre = trim($this->input('nombre'));
        $email = trim($this->input('email'));

        // Validaciones
        $errors = [];
        if (empty($nombre) || strlen($nombre) < 2) {
            $errors[] = 'El nombre debe tener al menos 2 caracteres.';
        }
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Ingrese un email válido.';
        }
        if ($this->usuarioModel->emailExists($email, $userId)) {
            $errors[] = 'El email ya está en uso por otro usuario.';
        }

        if (!empty($errors)) {
            $this->setFlash('error', implode('<br>', $errors));
            $this->redirect('perfil');
            return;
        }

        // Actualizar
        $this->usuarioModel->update($userId, [
            'nombre' => $nombre,
            'email'  => $email,
        ]);

        // Actualizar sesión para reflejar cambios inmediatamente
        $_SESSION['user_nombre'] = $nombre;
        $_SESSION['user_email'] = $email;

        $this->securityService->logAction(
            $userId, 'editar_perfil', 'perfil',
            "Datos personales actualizados: {$nombre} ({$email})"
        );

        $this->setFlash('success', 'Datos personales actualizados exitosamente.');
        $this->redirect('perfil');
    }

    /**
     * Cambiar contraseña.
     */
    public function updatePassword(): void
    {
        if (!$this->validateCSRF()) {
            $this->redirect('perfil');
            return;
        }

        $userId = currentUserId();
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        // Validaciones
        $errors = [];

        // Verificar contraseña actual
        $usuario = $this->usuarioModel->findById($userId);
        if (!$usuario || !password_verify($currentPassword, $usuario->password)) {
            $errors[] = 'La contraseña actual es incorrecta.';
        }

        if (strlen($newPassword) < 8) {
            $errors[] = 'La nueva contraseña debe tener al menos 8 caracteres.';
        }

        if ($newPassword !== $confirmPassword) {
            $errors[] = 'Las contraseñas no coinciden.';
        }

        if ($currentPassword === $newPassword) {
            $errors[] = 'La nueva contraseña debe ser diferente a la actual.';
        }

        if (!empty($errors)) {
            $this->setFlash('error', implode('<br>', $errors));
            $this->redirect('perfil');
            return;
        }

        // Actualizar contraseña
        $this->usuarioModel->update($userId, [
            'password' => password_hash($newPassword, PASSWORD_DEFAULT),
        ]);

        $this->securityService->logAction(
            $userId, 'cambiar_password', 'perfil',
            'Contraseña cambiada exitosamente'
        );

        $this->setFlash('success', 'Contraseña actualizada exitosamente.');
        $this->redirect('perfil');
    }
}
