<?php
/**
 * InvSys - AuthController
 */

class AuthController extends Controller
{
    private AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    /**
     * Mostrar formulario de login.
     */
    public function showLogin(): void
    {
        $csrfToken = $this->generateCSRF();
        $flash = $this->getFlash();

        $this->view('auth/login', [
            'csrfToken' => $csrfToken,
            'flash'     => $flash,
        ], false);
    }

    /**
     * Procesar login.
     */
    public function login(): void
    {
        if (!$this->validateCSRF()) {
            $this->redirect('login');
            return;
        }

        $email = $this->input('email', '');
        $password = $_POST['password'] ?? '';

        // Validaciones
        if (empty($email) || empty($password)) {
            $this->setFlash('error', 'Todos los campos son obligatorios.');
            $this->redirect('login');
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->setFlash('error', 'El email no tiene un formato válido.');
            $this->redirect('login');
            return;
        }

        // Intentar login
        $result = $this->authService->login($email, $password);

        if ($result['success']) {
            $this->redirect('dashboard');
        } else {
            $this->setFlash('error', $result['message']);
            $this->redirect('login');
        }
    }

    /**
     * Cerrar sesión.
     */
    public function logout(): void
    {
        $this->authService->logout();
        $this->setFlash('success', 'Sesión cerrada correctamente.');
        $this->redirect('login');
    }

    /**
     * Mostrar formulario de registro público.
     */
    public function showRegister(): void
    {
        // Verificar si el registro público está habilitado
        if (!$this->isRegistrationEnabled()) {
            $this->setFlash('error', 'El registro público no está habilitado.');
            $this->redirect('login');
            return;
        }

        $csrfToken = $this->generateCSRF();
        $flash = $this->getFlash();

        $this->view('auth/register', [
            'csrfToken' => $csrfToken,
            'flash'     => $flash,
        ], false);
    }

    /**
     * Procesar registro de nuevo usuario.
     */
    public function register(): void
    {
        if (!$this->isRegistrationEnabled()) {
            $this->setFlash('error', 'El registro público no está habilitado.');
            $this->redirect('login');
            return;
        }

        if (!$this->validateCSRF()) {
            $this->redirect('registro');
            return;
        }

        $nombre   = $this->input('nombre', '');
        $email    = $this->input('email', '');
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['password_confirm'] ?? '';

        // Validaciones
        $errors = [];

        if (empty($nombre) || mb_strlen($nombre) < 3) {
            $errors[] = 'El nombre debe tener al menos 3 caracteres.';
        }
        if (mb_strlen($nombre) > 100) {
            $errors[] = 'El nombre no puede exceder 100 caracteres.';
        }
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Ingrese un correo electrónico válido.';
        }
        if (strlen($password) < 8) {
            $errors[] = 'La contraseña debe tener al menos 8 caracteres.';
        }
        if (!preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
            $errors[] = 'La contraseña debe incluir al menos una mayúscula y un número.';
        }
        if ($password !== $confirm) {
            $errors[] = 'Las contraseñas no coinciden.';
        }

        if (!empty($errors)) {
            $this->setFlash('error', implode('<br>', $errors));
            $this->redirect('registro');
            return;
        }

        // Verificar email duplicado
        $usuarioModel = new Usuario();
        if ($usuarioModel->emailExists($email)) {
            $this->setFlash('error', 'Este correo electrónico ya está registrado.');
            $this->redirect('registro');
            return;
        }

        // Crear usuario con rol Operador (menos privilegios) por defecto
        $rolOperador = 3; // ID del rol Operador en la BD
        $id = $usuarioModel->create([
            'nombre'   => $nombre,
            'email'    => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'rol_id'   => $rolOperador,
            'activo'   => 1,
        ]);

        // Log de auditoría
        $securityService = SecurityService::getInstance();
        $securityService->logAction(
            $id, 'registro_publico', 'auth',
            "Nuevo usuario registrado: {$nombre} ({$email}), ID: {$id}"
        );

        // Enviar email de bienvenida
        $mailService = MailService::getInstance();
        $mailService->sendWelcomeEmail($email, $nombre, 'registro');

        $this->setFlash('success', 'Cuenta creada exitosamente. Ya puede iniciar sesión.');
        $this->redirect('login');
    }

    /**
     * Verificar si el registro público está habilitado en la configuración.
     *
     * @return bool
     */
    private function isRegistrationEnabled(): bool
    {
        return filter_var(sysConfig('permitir_registro', '0'), FILTER_VALIDATE_BOOLEAN);
    }
}
