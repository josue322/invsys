<?php
/**
 * InvSys - BackupController
 *
 * Gestión de copias de seguridad de la base de datos.
 * Permite generar, descargar y eliminar respaldos SQL.
 * Solo accesible por administradores.
 */

class BackupController extends Controller
{
    private SecurityService $securityService;
    private string $backupDir;

    public function __construct()
    {
        $this->securityService = SecurityService::getInstance();
        $this->backupDir = STORAGE_PATH . '/backups';

        // Crear directorio si no existe
        if (!is_dir($this->backupDir)) {
            @mkdir($this->backupDir, 0755, true);
        }
    }

    /**
     * Listar backups existentes.
     */
    public function index(): void
    {
        $backups = $this->getBackupsList();

        $this->view('backups/index', [
            'titulo'  => 'Copias de Seguridad',
            'backups' => $backups,
            'flash'   => $this->getFlash(),
            'csrfToken' => $this->generateCSRF(),
        ]);
    }

    /**
     * Generar un nuevo backup de la base de datos.
     */
    public function create(): void
    {
        if (!$this->validateCSRF()) {
            $this->redirect('backups');
            return;
        }

        // Cargar configuración de BD
        $config = require CONFIG_PATH . '/database.php';
        $host = $config['host'];
        $port = $config['port'];
        $database = $config['database'];
        $username = $config['username'];
        $password = $config['password'];

        // Nombre del archivo con timestamp
        $timestamp = date('Y-m-d_H-i-s');
        $filename = "invsys_backup_{$timestamp}.sql";
        $filepath = $this->backupDir . '/' . $filename;

        // Detectar ruta de mysqldump
        $mysqldumpPath = $this->findMysqldump();

        if (!$mysqldumpPath) {
            // Fallback: generar backup con PHP/PDO
            $success = $this->generateBackupWithPDO($filepath, $database);
        } else {
            // Usar mysqldump (más completo y confiable)
            $success = $this->generateBackupWithMysqldump(
                $mysqldumpPath, $host, $port, $username, $password, $database, $filepath
            );
        }

        if ($success && file_exists($filepath) && filesize($filepath) > 0) {
            $size = $this->formatFileSize(filesize($filepath));

            $this->securityService->logAction(
                currentUserId(), 'crear_backup', 'backups',
                "Backup creado: {$filename} ({$size})"
            );

            $this->setFlash('success', "Backup creado exitosamente: {$filename} ({$size})");
        } else {
            // Limpiar archivo vacío si existe
            if (file_exists($filepath)) {
                @unlink($filepath);
            }
            $this->setFlash('error', 'Error al generar el backup. Verifique los permisos y la configuración del servidor.');
        }

        $this->redirect('backups');
    }

    /**
     * Descargar un archivo de backup.
     */
    public function download(string $id): void
    {
        $filename = base64_decode($id);

        // Validar que el nombre no contenga rutas maliciosas
        if (!$this->isValidBackupFilename($filename)) {
            $this->setFlash('error', 'Archivo de backup no válido.');
            $this->redirect('backups');
            return;
        }

        $filepath = $this->backupDir . '/' . $filename;

        if (!file_exists($filepath)) {
            $this->setFlash('error', 'Archivo de backup no encontrado.');
            $this->redirect('backups');
            return;
        }

        $this->securityService->logAction(
            currentUserId(), 'descargar_backup', 'backups',
            "Backup descargado: {$filename}"
        );

        // Enviar archivo para descarga
        header('Content-Type: application/sql');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($filepath));
        header('Cache-Control: no-cache, no-store, must-revalidate');
        readfile($filepath);
        exit;
    }

    /**
     * Eliminar un archivo de backup.
     */
    public function destroy(string $id): void
    {
        if (!$this->validateCSRF()) {
            $this->redirect('backups');
            return;
        }

        $filename = base64_decode($id);

        if (!$this->isValidBackupFilename($filename)) {
            $this->setFlash('error', 'Archivo de backup no válido.');
            $this->redirect('backups');
            return;
        }

        $filepath = $this->backupDir . '/' . $filename;

        if (!file_exists($filepath)) {
            $this->setFlash('error', 'Archivo no encontrado.');
            $this->redirect('backups');
            return;
        }

        if (@unlink($filepath)) {
            $this->securityService->logAction(
                currentUserId(), 'eliminar_backup', 'backups',
                "Backup eliminado: {$filename}"
            );
            $this->setFlash('success', "Backup \"{$filename}\" eliminado.");
        } else {
            $this->setFlash('error', 'No se pudo eliminar el archivo.');
        }

        $this->redirect('backups');
    }

    // =========================================================
    // MÉTODOS PRIVADOS
    // =========================================================

    /**
     * Obtener lista de backups ordenados por fecha (más reciente primero).
     */
    private function getBackupsList(): array
    {
        $backups = [];
        $files = glob($this->backupDir . '/invsys_backup_*.sql');

        if ($files) {
            foreach ($files as $file) {
                $filename = basename($file);
                $backups[] = (object) [
                    'id'       => base64_encode($filename),
                    'filename' => $filename,
                    'size'     => $this->formatFileSize(filesize($file)),
                    'bytes'    => filesize($file),
                    'date'     => date('d/m/Y H:i:s', filemtime($file)),
                    'timestamp' => filemtime($file),
                ];
            }

            // Ordenar por más reciente
            usort($backups, fn($a, $b) => $b->timestamp - $a->timestamp);
        }

        return $backups;
    }

    /**
     * Detectar la ruta de mysqldump.
     */
    private function findMysqldump(): ?string
    {
        $possiblePaths = [];

        if (PHP_OS_FAMILY === 'Windows') {
            $possiblePaths = [
                'C:\\xampp\\mysql\\bin\\mysqldump.exe',
                'C:\\wamp64\\bin\\mysql\\mysql8.0.31\\bin\\mysqldump.exe',
                'C:\\laragon\\bin\\mysql\\mysql-8.0.30-winx64\\bin\\mysqldump.exe',
            ];
        } else {
            $possiblePaths = [
                '/usr/bin/mysqldump',
                '/usr/local/bin/mysqldump',
                '/usr/local/mysql/bin/mysqldump',
            ];
        }

        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        // Intentar con which/where
        $command = PHP_OS_FAMILY === 'Windows' ? 'where mysqldump 2>NUL' : 'which mysqldump 2>/dev/null';
        $result = @exec($command, $output, $returnCode);
        if ($returnCode === 0 && !empty($result)) {
            return trim($result);
        }

        return null;
    }

    /**
     * Generar backup usando mysqldump (método preferido).
     */
    private function generateBackupWithMysqldump(
        string $mysqldump, string $host, string $port,
        string $username, string $password, string $database,
        string $filepath
    ): bool {
        $command = sprintf(
            '"%s" --host=%s --port=%s --user=%s %s --single-transaction --routines --triggers --add-drop-table "%s" > "%s" 2>&1',
            $mysqldump,
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            $password ? '--password=' . escapeshellarg($password) : '',
            $database,
            $filepath
        );

        exec($command, $output, $returnCode);
        return $returnCode === 0;
    }

    /**
     * Generar backup con PHP/PDO (fallback si mysqldump no está disponible).
     */
    private function generateBackupWithPDO(string $filepath, string $database): bool
    {
        try {
            $db = Model::getConnection();
            $output = "-- InvSys Database Backup\n";
            $output .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
            $output .= "-- Database: {$database}\n";
            $output .= "-- =====================================================\n\n";
            $output .= "SET FOREIGN_KEY_CHECKS = 0;\n";
            $output .= "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n\n";

            // Obtener todas las tablas
            $tables = $db->query("SHOW TABLES")->fetchAll(\PDO::FETCH_COLUMN);

            foreach ($tables as $table) {
                // Estructura
                $createTable = $db->query("SHOW CREATE TABLE `{$table}`")->fetch(\PDO::FETCH_ASSOC);
                $output .= "-- Table: {$table}\n";
                $output .= "DROP TABLE IF EXISTS `{$table}`;\n";
                $output .= $createTable['Create Table'] . ";\n\n";

                // Datos
                $rows = $db->query("SELECT * FROM `{$table}`")->fetchAll(\PDO::FETCH_ASSOC);
                if (!empty($rows)) {
                    $columns = array_keys($rows[0]);
                    $columnList = '`' . implode('`, `', $columns) . '`';

                    foreach (array_chunk($rows, 100) as $chunk) {
                        $values = [];
                        foreach ($chunk as $row) {
                            $rowValues = array_map(function ($val) use ($db) {
                                if ($val === null) return 'NULL';
                                return $db->quote($val);
                            }, $row);
                            $values[] = '(' . implode(', ', $rowValues) . ')';
                        }
                        $output .= "INSERT INTO `{$table}` ({$columnList}) VALUES\n" . implode(",\n", $values) . ";\n\n";
                    }
                }
            }

            $output .= "SET FOREIGN_KEY_CHECKS = 1;\n";

            return file_put_contents($filepath, $output, LOCK_EX) !== false;
        } catch (\Exception $e) {
            error_log("Backup PDO Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Validar que el nombre del archivo sea un backup legítimo.
     */
    private function isValidBackupFilename(string $filename): bool
    {
        // Solo permite archivos que coincidan con el patrón de backup
        if (!preg_match('/^invsys_backup_\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}\.sql$/', $filename)) {
            return false;
        }
        // No permitir path traversal
        if (str_contains($filename, '..') || str_contains($filename, '/') || str_contains($filename, '\\')) {
            return false;
        }
        return true;
    }

    /**
     * Formatear tamaño de archivo a formato legible.
     */
    private function formatFileSize(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 1) . ' KB';
        }
        return $bytes . ' B';
    }
}
