<?php

use Adianti\Core\AdiantiApplicationConfig;
use Adianti\Core\AdiantiCoreApplication;
use Adianti\Widget\Base\TElement;

abstract class BuilderMenu
{
    protected $xml;
    protected $top_xml;
    protected $dropdown_navbar_xml;
    protected $items;
    protected $top_items;
    protected $dropdown_navbar_items;
    protected $ini;
    protected $callback_permission;

    public function __construct($menu, $callback_permission = ['SystemPermission', 'checkPermission'])
    {
        $this->ini = AdiantiApplicationConfig::get();
        $menuXml = file_get_contents($menu);
        if($menuXml)
        {
            $this->xml = new SimpleXMLElement(file_get_contents($menu));
        }
        
        $topMenuXml = file_get_contents('top_menu.xml');
        if($topMenuXml)
        {
            $this->top_xml = new SimpleXMLElement($topMenuXml);
        }
        
        $dropdownMenuXml = file_get_contents('menu-navbar-dropdown.xml');
        if($dropdownMenuXml)
        {
            $this->dropdown_navbar_xml = new SimpleXMLElement($dropdownMenuXml);
        }
        
        $this->callback_permission = $callback_permission;
        $this->items = [];
        $this->top_items = [];
        $this->dropdown_navbar_items = [];
        $this->process();
    }

    protected function canUseLeftModuleMenu()
    {
        return ! isset($this->ini['general']['left_menu_modules']) || $this->ini['general']['left_menu_modules'] == 1;
    }

    protected function canUseTopModuleMenu()
    {
        return ! isset($this->ini['general']['top_menu_modules']) || $this->ini['general']['top_menu_modules'] == 1;
    }

    abstract function getModuleMenu();
    abstract function getMenu();
    abstract function getDropdownNavbarMenu();
    
    protected function getTopItemMenu($menu, $level = 1)
    {
        $items = [];
        foreach($menu as $item)
        {
            $itemHtml = new TElement('li');

            $link = new TElement('a');
            $link->add(!empty($item['icon']) ? new TImage($item['icon']) : '');
            $link->add(TElement::tag('span', $item['label']));

            if (! empty($item['action']))
            {
                $link->{'onclick'} = "__adianti_load_page('{$item['action']}');";
                $link->{'generator'} = 'adianti';
            }
            else
            {
                $link->{'class'} = 'dropdown-toggle';
                $link->{'data-toggle'} = 'dropdown';
                $link->{'href'} = '#';
            }

            $itemHtml->add($link);
            
            if (! empty($item['menu']))
            {
                $itemHtml->{'class'} = "dropdown" . ($level == 1 ? '' : '-submenu');
                $itemsMenu =  $this->getTopItemMenu($item['menu'], ($level + 1));
                $menus = TElement::tag('ul', $itemsMenu, ['class' => "dropdown-menu level-{$level}"]);
                $itemHtml->add($menus);
            }
            
            $items[] = $itemHtml->getContents();
        }

        return implode('', $items);
    }

    public function getTopModuleMenu(){ return ''; }

    public function getTopMenu()
    {
        $menu = TElement::tag('ul', '', ['class' => "nav navbar-nav", 'id' => "top-menu"]);
        $items = $this->getTopItemMenu($this->top_items);
        $menu->add($items);
        
        return $menu->getContents();
    }

    private function process()
    {
        if ($this->xml)
        {
            $this->items = $this->parse([], $this->xml);
        }

        if ($this->top_xml)
        {
            $this->top_items = $this->parse([], $this->top_xml);
        }

        if ($this->dropdown_navbar_xml)
        {
            $this->dropdown_navbar_items = $this->parse([], $this->dropdown_navbar_xml);
        }
    }

    private function parse($items, $xml, $prefix = '')
    {
        foreach ($xml as $xmlElement)
        {
            $atts     = $xmlElement-> attributes ();
            $label    = (string) $atts['label'];
            $action   = (string) $xmlElement-> action;
            $icon     = (string) $xmlElement-> icon;

            if ($action)
            {
                if ( !empty($action) AND $this->callback_permission AND (substr($action,0,7) !== 'http://') AND (substr($action,0,8) !== 'https://'))
                {
                    // check permission
                    $parts = explode('#', $action);
                    $className = $parts[0];
                    if (! call_user_func($this->callback_permission, $className))
                    {
                        if ($xmlElement->menu)
                        {
                            $action = '';   
                        }
                        else
                        {
                            continue;    
                        }
                    }
                }
                
                if ($action && (! empty($this->ini['general']['use_tabs']) || ! empty($this->ini['general']['use_mdi_windows'])))
                {
                    $action .= "#adianti_open_tab=1#adianti_tab_name={$label}";
                }
                
                $action = str_replace('#', '&', $action);
    
                // Controll if menu.xml contains a short url e.g. \home  -> back slash is the char controll
                if ($action && substr($action,0,1) == '\\')
                {
                    $action = substr($action, 1);
                }
                elseif ($action && (substr($action,0,7) != 'http://') AND (substr($action,0,8) != 'https://'))
                {
                    if ($router = AdiantiCoreApplication::getRouter())
                    {
                        $action = $router("class={$action}", true);
                    }
                    else
                    {
                        $action = "index.php?class={$action}";
                    }
                }
            }

            if ($xmlElement->menu)
            {
                $menuItems = $this->parse([], $xmlElement->menu->menuitem, $prefix . 'sub');
                
                if ($menuItems || $action)
                {
                    $items[] = [
                        'label' => $label,
                        'action' => $action,
                        'icon' =>  $icon,
                        'menu' => $menuItems
                    ];
                }
            }
            else
            {
                $items[] = [
                    'label' => $label,
                    'action' => $action,
                    'icon' => $icon,
                ];
            }
        }

        return $items;
    }
}