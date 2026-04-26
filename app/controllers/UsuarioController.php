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
        $search = $this->query('search', '');
        $rolFilter = (int) $this->query('rol', 0);
        $estadoFilter = $this->query('estado', '');

        $usuarios = $this->usuarioModel->getAllWithRole(
            $page,
            $this->getPerPage(),
            $search,
            $rolFilter,
            $estadoFilter
        );

        $roles = $this->rolModel->getAll();
        $flash = $this->getFlash();
        $csrfToken = $this->generateCSRF();

        $this->view('usuarios/index', [
            'titulo'       => 'Gestión de Usuarios',
            'usuarios'     => $usuarios,
            'roles'        => $roles,
            'search'       => $search,
            'rolFilter'    => $rolFilter,
            'estadoFilter' => $estadoFilter,
            'csrfToken'    => $csrfToken,
            'flash'        => $flash,
        ]);
    }

    /**
     * Vista detalle de un usuario con sesiones y actividad reciente.
     */
    public function show(string $id): void
    {
        $userId = (int) $id;
        $usuario = $this->usuarioModel->findWithRole($userId);
        if (!$usuario) {
            $this->setFlash('error', 'Usuario no encontrado.');
            $this->redirect('usuarios');
            return;
        }

        $perPage = 10;

        // Sesiones activas
        $sesionModel = new Sesion();
        $sesiones = $sesionModel->getActiveByUserId($userId);

        // Actividad (logs) — paginada
        $pgAct = max(1, (int) $this->query('pg_act', 1));
        $logModel = new Log();
        $actividad = $logModel->getByUserId($userId, $pgAct, $perPage);

        // Movimientos — paginados
        $pgMov = max(1, (int) $this->query('pg_mov', 1));
        $movimientos = $this->getMovimientosByUser($userId, $pgMov, $perPage);

        $flash = $this->getFlash();

        $this->view('usuarios/show', [
            'titulo'      => $usuario->nombre,
            'usuario'     => $usuario,
            'sesiones'    => $sesiones,
            'actividad'   => $actividad,
            'movimientos' => $movimientos,
            'pgAct'       => $pgAct,
            'pgMov'       => $pgMov,
            'flash'       => $flash,
        ]);
    }

    /**
     * Obtener movimientos paginados de un usuario.
     */
    private function getMovimientosByUser(int $userId, int $page, int $perPage): array
    {
        $offset = ($page - 1) * $perPage;

        $countSql = "SELECT COUNT(*) as total FROM movimientos WHERE usuario_id = :uid";
        $total = (int) (new Movimiento())->rawQuery($countSql, ['uid' => $userId])[0]->total ?? 0;

        $sql = "SELECT m.*, p.nombre as producto_nombre, p.sku as producto_sku
                FROM movimientos m
                INNER JOIN productos p ON m.producto_id = p.id
                WHERE m.usuario_id = :uid
                ORDER BY m.created_at DESC
                LIMIT {$perPage} OFFSET {$offset}";
        $data = (new Movimiento())->rawQuery($sql, ['uid' => $userId]);

        return [
            'data'    => $data,
            'total'   => $total,
            'pages'   => (int) ceil($total / $perPage),
            'current' => $page,
            'perPage' => $perPage,
        ];
    }

    public function create(): void
    {
        $roles = $this->rolModel->getAll();
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

        $roles = $this->rolModel->getAll();
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

    /**
     * Eliminar usuario (soft-delete: activo = 0).
     * No permite eliminar al propio usuario ni al último administrador activo.
     */
    public function destroy(string $id): void
    {
        $id = (int) $id;

        if (!$this->validateCSRF()) {
            $this->redirect('usuarios');
            return;
        }

        // No permitir eliminarse a uno mismo
        if ($id === currentUserId()) {
            $this->setFlash('error', 'No puedes eliminar tu propia cuenta.');
            $this->redirect('usuarios');
            return;
        }

        $usuario = $this->usuarioModel->findWithRole($id);
        if (!$usuario) {
            $this->setFlash('error', 'Usuario no encontrado.');
            $this->redirect('usuarios');
            return;
        }

        // No permitir eliminar al último admin activo
        if ($usuario->rol_id === 1 && $this->usuarioModel->countActiveByRole(1) <= 1) {
            $this->setFlash('error', 'No se puede eliminar al último administrador activo del sistema.');
            $this->redirect('usuarios');
            return;
        }

        // Soft-delete: desactivar
        $this->usuarioModel->update($id, ['activo' => 0]);

        $this->securityService->logAction(
            currentUserId(), 'eliminar_usuario', 'usuarios',
            "Usuario eliminado (desactivado): {$usuario->nombre} ({$usuario->email}), ID: {$id}"
        );

        $this->setFlash('success', "Usuario \"{$usuario->nombre}\" eliminado exitosamente.");
        $this->redirect('usuarios');
    }
}
