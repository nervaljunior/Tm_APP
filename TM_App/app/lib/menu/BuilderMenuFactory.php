<?php
class BuilderMenuFactory
{
    public static function getInstance($theme, $menu)
    {
        if ($theme == 'theme3')
        {
            return new BuilderMenuTheme3($menu);
        }
        else if ($theme == 'theme4')
        {
            return new BuilderMenuTheme4($menu);
        }
        else if ($theme == 'theme3-adminlte3')
        {
            return new BuilderMenuTheme3AdminLte3($menu);
        }
        else if ($theme == 'theme-builder')
        {
            return new BuilderMenuThemeBuilder($menu);
        }

        throw new Exception('Theme not definied');
    }
}