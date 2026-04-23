<?php
/**
 * InvSys - ThemeService
 * 
 * Servicio de temas (modo oscuro/claro).
 * Gestiona la preferencia de tema del usuario,
 * almacenándola en la tabla user_settings.
 */

class ThemeService
{
    private UserSetting $settingModel;

    public function __construct()
    {
        $this->settingModel = new UserSetting();
    }

    /**
     * Obtener el tema actual del usuario.
     *
     * @param int $userId
     * @return string 'light' o 'dark'
     */
    public function getUserTheme(int $userId): string
    {
        $setting = $this->settingModel->getSetting($userId, 'tema');
        return $setting ?? 'light';
    }

    /**
     * Establecer el tema del usuario.
     *
     * @param int $userId
     * @param string $theme 'light' o 'dark'
     * @return bool
     */
    public function setUserTheme(int $userId, string $theme): bool
    {
        $theme = in_array($theme, ['light', 'dark']) ? $theme : 'light';
        return $this->settingModel->setSetting($userId, 'tema', $theme);
    }

    /**
     * Alternar el tema del usuario (toggle).
     *
     * @param int $userId
     * @return string El nuevo tema aplicado
     */
    public function toggleTheme(int $userId): string
    {
        $current = $this->getUserTheme($userId);
        $newTheme = $current === 'light' ? 'dark' : 'light';
        $this->setUserTheme($userId, $newTheme);
        return $newTheme;
    }
}
