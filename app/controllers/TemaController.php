<?php
/**
 * InvSys - TemaController
 */

class TemaController extends Controller
{
    /**
     * Toggle de tema (AJAX).
     */
    public function toggle(): void
    {
        if (!$this->validateCSRF()) {
            $this->json(['success' => false, 'error' => 'Token CSRF inválido.'], 403);
            return;
        }

        $themeService = new ThemeService();
        $newTheme = $themeService->toggleTheme(currentUserId());

        // Actualizar sesión
        $_SESSION['user_theme'] = $newTheme;

        $this->json([
            'success' => true,
            'theme'   => $newTheme,
        ]);
    }
}
