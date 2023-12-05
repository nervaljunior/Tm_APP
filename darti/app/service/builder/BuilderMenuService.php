<?php

class BuilderMenuService
{
    public static function parse($file, $theme)
    {
        switch ($theme)
        {
            case 'top-theme4':
                ob_start();
                $callback = array('SystemPermission', 'checkPermission');
                $xml = new SimpleXMLElement(file_get_contents($file));
                $menu = new BuilderTopMenu($xml, $callback, 1, ' dropdown-menu ', 'dropdown', ' dropdown-toggle ');
                $menu->class = 'nav navbar-nav';
                $menu->id    = 'top-menu';
                $menu->show();
                $menu_string = ob_get_clean();
                return $menu_string;
                break;
            case 'top-theme3-adminlte3':
                    ob_start();
                    $callback = array('SystemPermission', 'checkPermission');
                    $xml = new SimpleXMLElement(file_get_contents($file));
                    $menu = new BuilderTopMenu($xml, $callback, 1, ' dropdown-menu ', 'dropdown', ' dropdown-toggle ');
                    $menu->class = 'navbar-nav';
                    $menu->id    = 'top-menu';
                    $menu->show();
                    $menu_string = ob_get_clean();
                    return $menu_string;
                    break;
            case 'top-theme3':
                ob_start();
                $callback = array('SystemPermission', 'checkPermission');
                $xml = new SimpleXMLElement(file_get_contents($file));
                $menu = new BuilderTopMenu($xml, $callback, 1, ' dropdown-menu ', 'dropdown', ' dropdown-toggle ');
                $menu->class = 'nav navbar-nav';
                $menu->id    = 'top-menu';
                $menu->show();
                $menu_string = ob_get_clean();
                return $menu_string;
                break;
        }
    }   
}
