<?php
/**
 * InvSys - UsuarioController
 */

class UsuarioController extends Controller
{
    private Usuario $usuarioModel;
    private Rol $rolModel;
    private SecurityService $securityService;

    public function __construct()
    {
        $this->usuarioModel = new Usuario();
        $this->rolModel = new Rol();
        $this->securityService = SecurityService::getInstance();
    }

    public function index(): void
    {
        $page = (int) $this->query('page', 1);
        $usuarios = $this->usuarioModel->getAllWithRole($page, (int) sysConfig('registros_por_pagina', '15'));
        $flash = $this->getFlash();
        $csrfToken = $this->generateCSRF();

        $this->view('usuarios/index', [
            'titulo'    => 'Gestión de Usuarios',
            'usuarios'  => $usuarios,
            'csrfToken' => $csrfToken,
            'flash'     => $flash,
        ]);
    }

    public function create(): void
    {
        $roles = $this->rolModel->getAllActive();
        $csrfToken = $this->generateCSRF();

        $this->view('usuarios/create', [
            'titulo'    => 'Nuevo Usuario',
            'roles'     => $roles,
            'csrfToken' => $csrfToken,
        ]);
    }

    public function store(): void
    {
        if (!$this->validateCSRF()) {
            $this->redirect('usuarios/crear');
            return;
        }

        $nombre = $this->input('nombre');
        $email = $this->input('email');
        $password = $_POST['password'] ?? '';
        $rolId = (int) $this->input('rol_id');

        $errors = [];
        if (empty($nombre)) $errors[] = 'El nombre es obligatorio.';
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email inválido.';
        if (strlen($password) < 8) $errors[] = 'La contraseña debe tener al menos 8 caracteres.';
        if ($rolId <= 0) $errors[] = 'Debe seleccionar un rol.';

        if ($this->usuarioModel->emailExists($email)) {
            $errors[] = 'El email ya está registrado.';
        }

        if (!empty($errors)) {
            $this->setFlash('error', implode('<br>', $errors));
            $this->redirect('usuarios/crear');
            return;
        }

        $id = $this->usuarioModel->create([
            'nombre'   => $nombre,
            'email'    => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'rol_id'   => $rolId,
            'activo'   => 1,
        ]);

        $this->securityService->logAction(
            currentUserId(), 'crear_usuario', 'usuarios',
            "Usuario creado: {$nombre} ({$email}), ID: {$id}"
        );

        // Enviar email de bienvenida al nuevo usuario
        $mailService = MailService::getInstance();
        $mailService->sendWelcomeEmail($email, $nombre, 'admin');

        $this->setFlash('success', 'Usuario creado exitosamente.');
        $this->redirect('usuarios');
    }

    public function edit(string $id): void
    {
        $usuario = $this->usuarioModel->findWithRole((int) $id);
        if (!$usuario) {
            $this->setFlash('error', 'Usuario no encontrado.');
            $this->redirect('usuarios');
            return;
        }

        $roles = $this->rolModel->getAllActive();
        $csrfToken = $this->generateCSRF();

        $this->view('usuarios/edit', [
            'titulo'    => 'Editar Usuario',
            'usuario'   => $usuario,
            'roles'     => $roles,
            'csrfToken' => $csrfToken,
        ]);
    }

    public function update(string $id): void
    {
        $id = (int) $id;

        if (!$this->validateCSRF()) {
            $this->redirect("usuarios/editar/{$id}");
            return;
        }

        // Flujo: Restablecer contraseña (desde modal)
        if ($this->input('reset_password') === '1') {
            $newPassword = $_POST['new_password'] ?? '';
            if (strlen($newPassword) < 6) {
                $this->setFlash('error', 'La contraseña temporal no es válida.');
                $this->redirect("usuarios/editar/{$id}");
                return;
            }

            $this->usuarioModel->update($id, [
                'password' => password_hash($newPassword, PASSWORD_DEFAULT),
            ]);

            $usuario = $this->usuarioModel->findById($id);
            $this->securityService->logAction(
                currentUserId(), 'resetear_password', 'usuarios',
                "Contraseña restablecida para: {$usuario->nombre} (ID: {$id})"
            );

            $this->setFlash('success', 'Contraseña restablecida exitosamente. Comparte la contraseña temporal con el usuario.');
            $this->redirect('usuarios');
            return;
        }

        // Flujo normal: Actualizar datos del usuario
        $nombre = $this->input('nombre');
        $email = $this->input('email');
        $rolId = (int) $this->input('rol_id');
        $activo = $this->input('activo') ? 1 : 0;

        $data = [
            'nombre' => $nombre,
            'email'  => $email,
            'rol_id' => $rolId,
            'activo' => $activo,
        ];

        if ($this->usuarioModel->emailExists($email, $id)) {
            $this->setFlash('error', 'El email ya está en uso.');
            $this->redirect("usuarios/editar/{$id}");
            return;
        }

        $this->usuarioModel->update($id, $data);

        $this->securityService->logAction(
            currentUserId(), 'editar_usuario', 'usuarios',
            "Usuario editado: {$nombre} (ID: {$id})"
        );

        $this->setFlash('success', 'Usuario actualizado exitosamente.');
        $this->redirect('usuarios');
    }

    /**
     * Activar o desactivar un usuario (toggle).
     */
    public function toggleStatus(string $id): void
    {
        $id = (int) $id;

        if (!$this->validateCSRF()) {
            $this->redirect('usuarios');
            return;
        }

        // No permitir desactivarse a uno mismo
        if ($id === currentUserId()) {
            $this->setFlash('error', 'No puedes desactivar tu propia cuenta.');
            $this->redirect('usuarios');
            return;
        }

        $usuario = $this->usuarioModel->findById($id);
        if (!$usuario) {
            $this->setFlash('error', 'Usuario no encontrado.');
            $this->redirect('usuarios');
            return;
        }

        $nuevoEstado = $this->usuarioModel->toggleActive($id);
        $estadoTexto = $nuevoEstado ? 'activado' : 'desactivado';

        $accion = $nuevoEstado ? 'activar_usuario' : 'desactivar_usuario';

        $this->securityService->logAction(
            currentUserId(), $accion, 'usuarios',
            "Usuario {$estadoTexto}: {$usuario->nombre} (ID: {$id})"
        );

        $this->setFlash('success', "Usuario {$estadoTexto} exitosamente.");
        $this->redirect('usuarios');
    }
}
