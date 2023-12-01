<?php

use Adianti\Widget\Base\TElement;

class BuilderMenuThemeBuilder extends BuilderMenu
{
    private function getItemMenu($menu, $level = 1)
    {
        $items = [];
        foreach($menu as $key => $item)
        {
            if ($level == 1 && $this->canUseLeftModuleMenu())
            {
                $itemHtml = TElement::tag('div', '');

                if (empty($item['menu']))
                {
                    continue;
                }

                $itemHtml->{'module-menu'} = md5($item['label']);

                if ($key == 0)
                {
                    $itemHtml->{'class'} = 'open';
                }
            }
            else
            {
                $itemHtml = TElement::tag('li', '');
            }

            $link = new TElement('a');
            $link->add(new TImage($item['icon'] ?? 'fas:circle'));
            $link->add($item['label']);

            if (! empty($item['action']))
            {
                $link->{'href'} = $item['action'];
                $link->{'generator'} = 'adianti';
            }
            else
            {
                $link->{'href'} = '#';
            }

            if (! empty($item['menu']))
            {
                $link->{'class'} = 'sub ' . ($key == 0 && $this->canUseLeftModuleMenu() ? 'open' : '');
            }

            if ($level != 1 || ! $this->canUseLeftModuleMenu())
            {
                $itemHtml->add($link);
            }
            
            if (! empty($item['menu']))
            {
                $itemsMenu =  $this->getItemMenu($item['menu'], ($level + 1));
                $menus = TElement::tag('ul', $itemsMenu);

                if (($key != 0 && $level > 1) || ! $this->canUseLeftModuleMenu())
                {
                    $menus->{'style'} = 'display: none';
                }

                $itemHtml->add($menus);
            }

            $items[] = $itemHtml->getContents();
        }

        return implode('', $items);
    }

    public function getMenu()
    {
        $menu = TElement::tag('ul', '');
        $items = $this->getItemMenu($this->items);
        $menu->add($items);
        
        return $menu->getContents();
    }

    protected function getTopItemMenu($menu, $level = 1, $id = '')
    {
        $items = [];
        foreach($menu as $item)
        {
            $link = new TElement('a');
            $link->{'class'} = ' ';
            $link->add(new TImage($item['icon'] ?? 'fas:circle'));
            $link->add($item['label']);

            if (! empty($item['action']))
            {
                $link->{'href'} = $item['action'];
                $link->{'generator'} = 'adianti';
            }
            else
            {
                $link->{'href'} = '#';
            }

            if (! empty($item['menu']))
            {
                $link->{'class'} .= 'sub';
                $link = TElement::tag('li', $link);
                $link->{'top-module-menu'} = $id;

                $itemsMenu =  $this->getTopItemMenu($item['menu'], ($level + 1), 'top-' . md5($item['label']));

                if ($level == 1 && ($this->canUseTopModuleMenu())) {
                    array_push($items, $itemsMenu);
                    continue;
                } else {
                    $link->add(TElement::tag('ul', $itemsMenu));
                }
            } else {
                $link = TElement::tag('li', $link);
                $link->{'top-module-menu'} = $id;
            }

            $items[] = $link->getContents();
        }

        return implode('', $items);
    }

    public function getTopMenu()
    {
        if ( ! $this->canUseTopModuleMenu() )
        {
            return '';
        }

        $submenu = TElement::tag('div', '', ['class' => "container-submenu", 'id' => "top-submenu"]);
        $subitems = $this->getTopItemMenu($this->top_items);
        $submenu->add(TElement::tag('ul', $subitems));
        
        return $submenu->getContents();
    }

    public function getTopModuleMenu()
    {
        if ($this->canUseTopModuleMenu())
        {
            $modulesMenu = '';

            if ($this->top_items)
            {
                foreach($this->top_items as $key => $module)
                {
                    $icon = new TImage($module['icon'] ?? 'fas:circle');

                    $link = new TElement('a');
                    $link->{'class'} = 'button-circle-label ';
                    $link->add($icon);
                    $link->add($module['label']);
                    $link->{'top-menu-target'} = 'top-'.md5($module['label']);

                    if (! empty($module['action']))
                    {
                        $link->{'href'} = $module['action'];
                        $link->{'generator'} = 'adianti';
                    }
                    else
                    {
                        $link->{'href'} = '#';
                    }

                    $modulesMenu .= $link->getContents();
                }
            }

            $menu = TElement::tag('div', $modulesMenu, ['class' => 'container-menu builder-top-module-menu', 'id' => "top-menu"]);
            return $menu->getContents();
        }
        else
        {
            $submenu = TElement::tag('div', '', ['class' => "container-submenu", 'id' => "top-submenu"]);
            $subitems = $this->getTopItemMenu($this->top_items);
            $submenu->add(TElement::tag('ul', $subitems));
            
            return $submenu->getContents();
        }

    }

    public function getItemDropdownMenu($menu)
    {
        $items = [];
        foreach($menu as $item)
        {
            if (! empty($item['menu']))
            {
                $separator = new TElement('div');
                $separator->{'class'} = 'separador';
                $separator->add(!empty($item['icon']) ? new TImage($item['icon']) : '');
                $separator->add($item['label']);

                if (! empty($item['action']))
                {
                    $separator->{'href'} = $item['action'];
                    $separator->{'generator'} = 'adianti';
                }

                $items[] = $separator;
                $items[] = $this->getItemDropdownMenu($item['menu']);
            }
            else
            {
                $a = new TElement('a');
                $a->add(!empty($item['icon']) ? new TImage($item['icon']) : '');
                $a->{'href'} = $item['action'];
                $a->add($item['label']);
    
                if (! empty($item['action']))
                {
                    $a->{'href'} = $item['action'];
                    $a->{'generator'} = 'adianti';
                }
                else
                {
                    $a->{'href'} = '#';
                }
                $items[] = TElement::tag('div', TElement::tag('div', $a, ['class' => 'fast-drop-label']), ['class' => 'fast-drop'])->getContents();
            }

            
        }

        return implode('', $items);
    }

    public function getDropdownNavbarMenu()
    {
        $menus = '';
        if ($this->dropdown_navbar_items)
        {
            $items_menus = [];
            foreach($this->dropdown_navbar_items as $item)
            {
                if (! empty($item['menu']))
                {
                    $div = new TElement('div');
                    $div->{'class'} = 'fast-drop';

                    $a = new TElement('a');
                    $a->{'class'} = 'button-circle';
                    $a->add(new TImage($item['icon'] ?? 'fas:circle'));
                    $a->{'title'} = $item['label'];
                    $a->{'titside'} = "left";
                    
                    if (! empty($item['action']))
                    {
                        $a->{'href'} = $item['action'];
                        $a->{'generator'} = 'adianti';
                    }
                    else
                    {
                        $a->{'href'} = '#';
                    }

                    $items = $this->getItemDropdownMenu($item['menu']);

                    $div->add(TElement::tag('div', $a, ['class' => 'fast-drop-label']));
                    $div->add(TElement::tag('div', $items, ['class' => 'fast-drop-open']));
                    $items_menus[] = $div->getContents();
                }
                else
                {
                    $a = new TElement('a');
                    $a->{'class'} = 'button-circle';
                    $a->add(new TImage($item['icon'] ?? 'fas:circle'));
                    $a->{'title'} = $item['label'];
                    $a->{'titside'} = "left";

                    if (! empty($item['action']))
                    {
                        $a->{'href'} = $item['action'];
                        $a->{'generator'} = 'adianti';
                    }
                    else
                    {
                        $a->{'href'} = '#';
                    }

                    $items_menus[] = $a->getContents();
                }
            }

            $menus = implode('', $items_menus);
        }

        return $menus;
    }

    public function getModuleMenu()
    {
        if (! $this->canUseLeftModuleMenu())
        {
            return '';
        }

        $modulesMenu = '';

        if ($this->items)
        {
            foreach($this->items as $key => $module)
            {
                $link = new TElement('a');
                $link->{'class'} = 'button-circle ' . ($key == 0 ? 'checked' : '');
                $link->{'title'} = $module['label'];
                $link->{'menu-target'} = md5($module['label']);

                if (! empty($module['action']))
                {
                    $link->{'href'} = $module['action'];
                    $link->{'generator'} = 'adianti';
                }
                else
                {
                    $link->{'href'} = '#';
                }

                $link->add(new TImage($module['icon'] ?? 'fas:circle'));

                $modulesMenu .= $link->getContents();
            }
        }

        return $modulesMenu;
    }
}