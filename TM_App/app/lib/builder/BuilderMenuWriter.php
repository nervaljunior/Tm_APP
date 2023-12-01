<?php

class BuilderMenuWriter
{
    private $menu;
    private $menu_path;
    public $modules;
    public $submodulos;

    /**
     * Read a menu XML file
     * @param $xml_file file path
     */
    public function __construct($menu_path)
    {
        BuilderPermissionService::checkPermission();
        
        $this->menu                     = new DomDocument;
        $this->menu->preserveWhiteSpace = FALSE;
        $this->menu->formatOutput       = TRUE;
        $this->menu->encoding           = 'utf-8';
        $this->menu_path                = $menu_path;
        $this->menu->load($menu_path);

        $this->getModulesFromMenu();
    }

    /**
     * Get menu modules
     */
    private function getModulesFromMenu()
    {
        $menu    = $this->menu->childNodes[0];
        $modules = $menu->childNodes;

        foreach ($modules as $module) {
            $this->modules[$module->getAttribute('label')] = $module;

            $submodulos = $module->getElementsByTagName('menuitem');

            if (empty($submodulos)) {
                continue;
            }

            foreach ($submodulos as $submodulo) {
                $this->submodulos[$submodulo->getAttribute('label')] = $submodulo;
            }
        }
    }

    /**
     * Create menu item
     */
    private function createMenuItem($label, $action = NULL, $icon = NULL, $icon_color = NULL)
    {
        $menuitem = $this->menu->createElement("menuitem");
        $menuitem->setAttribute('label', $label);

        $menu      = $this->menu->createElement("menu");
        $el_icon   = $this->menu->createElement("icon");
        $el_action = $this->menu->createElement("action");

        $el_action->nodeValue = $action;
        $el_icon->nodeValue  = str_replace('fa-', 'fa:', $icon) . ' fa-fw ' . $icon_color;

        $menuitem->appendChild($el_icon);
        $menuitem->appendChild($el_action);
        $menuitem->appendChild($menu);

        return $menuitem;
    }

    /**
     * Get menu element
     */
    private function getMenu($element)
    {
        $menu = $element->getElementsByTagName('menu');

        if (empty($menu->length)) {
            $menu = $this->menu->createElement("menu");
            $element->appendChild($menu);
        } else {
            $menu = $menu[0];
        }

        return $menu;
    }

    /**
     * Remove empty menus
     */
    private function removeEmptyMenus()
    {
        $menus  = $this->menu->getElementsByTagName("menu");
        $length = $menus->length;

        // Iterate backwards by decrementing the loop counter
        for ($i = $length - 1; $i >= 0; $i--) 
        {
            $menu = $menus->item($i);

            if ($menu->childNodes->length === 0) 
            {
                $parent = $menu->parentNode;
                $parent->removeChild($menu);
            }
        }
    }

    /**
     * Append Module
     */
    public function appendModule($label, $action = NULL, $icon = NULL, $icon_color = NULL)
    {
        if (!empty($this->modules[$label])) 
        {
            throw new Exception("{$label} module already exists.");
        }
        
        $menuitem = $this->createMenuItem($label, $action, $icon, $icon_color);
        $this->modules[$label] = $menuitem;

        $last = $this->menu->documentElement;
        $last->insertBefore($menuitem, $this->menu->documentElement->firstChild);
    }

    /**
     * Append SubModule
     */
    public function appendSubModule($module, $submodulo, $action = NULL, $icon = NULL, $icon_color = NULL)
    {
        $menuitem = $this->createMenuItem($submodulo, $action, $icon, $icon_color);

        $this->submodulos[$submodulo] = $menuitem;
        $this->modules[$module]->getElementsByTagName('menu')[0]->appendChild($menuitem);
    }

    /**
     * Append Module
     */
    public function appendItem($module, $label, $action = NULL, $icon = NULL, $icon_color = NULL, $submodulo = NULL)
    {
        // If module doesnt exist
        if (empty($this->modules[$module])) 
        {
            $this->appendModule($module);
        }

        $menuitem = $this->createMenuItem($label, $action, $icon, $icon_color);

        if (empty($submodulo)) 
        {
            $menu = $this->getMenu($this->modules[$module]);
            $menu->appendChild($menuitem);
        } 
        else 
        {
            //Add on a sub module
            if (empty($this->submodulos[$submodulo])) 
            {
                $this->appendSubModule($module, $submodulo);
            }

            $menu = $this->getMenu($this->submodulos[$submodulo]);
            $menu->appendChild($menuitem);
        }
    }

    /**
     * Save menu
     */
    public function save()
    {
        $this->removeEmptyMenus();
        $this->menu->save($this->menu_path);
    }
}