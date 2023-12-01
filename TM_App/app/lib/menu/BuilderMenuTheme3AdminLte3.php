<?php

use Adianti\Widget\Base\TElement;

class BuilderMenuTheme3AdminLte3 extends BuilderMenu
{
    private function getItemMenu($menu, $level = 1)
    {
        $items = [];
        foreach($menu as $item)
        {
            $itemHtml = TElement::tag('li', '', ['class' => "nav-item"]);

            $link = new TElement('a');
            $link->{'class'} = 'nav-link';
            
            $link->add(!empty($item['icon']) ? new TImage($item['icon']) : '');

            if (! empty($item['action']))
            {
                $link->{'href'} = $item['action'];
                $link->{'generator'} = 'adianti';
            }
            else
            {
                $link->{'href'} = '#';
            }

            $itemHtml->add($link);
            
            if (! empty($item['menu']))
            {
                $itemsMenu =  $this->getItemMenu($item['menu'], ($level + 1));
                $menus = TElement::tag('ul', $itemsMenu, ['class' => "nav nav-treeview has-treeview level-{$level}"]);
                $itemHtml->add($menus);
                $link->add(TElement::tag('p', $item['label'] . TElement::tag('i', '', ['class' => 'right fas fa-angle-left'])));
            }
            else
            {
                $link->add(TElement::tag('p', $item['label']));
            }

            $items[] = $itemHtml->getContents();
        }

        return implode('', $items);
    }

    public function getMenu()
    {
        $options = [
            'class' => "nav nav-pills nav-sidebar flex-column",
            'id' => "side-menu",
            'role' => "menu",
            'data-widget' => "treeview",
            'data-accordion' => "false"
        ];

        $menu = TElement::tag('ul', '', $options);
        $items = $this->getItemMenu($this->items);
        $menu->add($items);
        
        return $menu->getContents();
    }

    public function getModuleMenu() { return ''; }

    public function getItemDropdownMenu($menu)
    {
        $items = [];
        foreach($menu as $item)
        {
            if (! empty($item['menu']))
            {
                $separator = new TElement('div');
                $separator->{'class'} = 'dropdown-item dropdown-header text-left';
                $separator->add(!empty($item['icon']) ? new TImage($item['icon']) : '');
                $separator->add($item['label']);

                if (! empty($item['action']))
                {
                    $separator->{'href'} = $item['action'];
                    $separator->{'generator'} = 'adianti';
                }

                $items[] = $separator . TElement::tag('div', '', ['class' => 'dropdown-divider']);
                $items[] = $this->getItemDropdownMenu($item['menu']);
            }
            else
            {
                $label = TElement::tag('h3', '', ['class' => 'dropdown-item-title']);
                $label->add(!empty($item['icon']) ? new TImage($item['icon']) : '');
                $label->add($item['label']);

                $a = new TElement('a');
                $a->{'class'} = 'dropdown-item';
                $a->add(TElement::tag('div', TElement::tag('div', $label , ['class' => 'media-body']), ['class' => 'media']));
    
                if (! empty($item['action']))
                {
                    $a->{'href'} = $item['action'];
                    $a->{'generator'} = 'adianti';
                }
                else
                {
                    $a->{'href'} = '#';
                }

                $items[] = $a . TElement::tag('div', '', ['class' => 'dropdown-divider']);
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
                    $a = new TElement('a');
                    $a->{'class'} = 'nav-link';
                    $a->{'data-toggle'} = "dropdown";
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

                    $divMenu = TElement::tag('div', $items,['class' => 'dropdown-menu dropdown-menu-lg dropdown-menu-right']);

                    $div = TElement::tag('div', $a);
                    $div->add($divMenu);

                    $li = TElement::tag('li', $div, ['class' => 'nav-item dropdown']);
                    $items_menus[] = $li->getContents();
                }
                else
                {
                    $a = new TElement('a');
                    $a->add(new TImage($item['icon'] ?? 'fas:circle'));
                    $a->{'title'} = $item['label'];
                    $a->{'titside'} = "left";
                    $a->{'class'} = 'nav-link';

                    if (! empty($item['action']))
                    {
                        $a->{'href'} = $item['action'];
                        $a->{'generator'} = 'adianti';
                    }
                    else
                    {
                        $a->{'href'} = '#';
                    }

                    $li = TElement::tag('li', $a, ['class' => 'nav-item hidden-xs']);

                    $items_menus[] = $li->getContents();
                }
            }

            $menus = implode('', $items_menus);
        }

        return $menus;
    }
}