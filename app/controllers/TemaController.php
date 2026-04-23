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
