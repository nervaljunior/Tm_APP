<?php

use Adianti\Widget\Base\TElement;

class BuilderMenuTheme4 extends BuilderMenu
{
    private function getItemMenu($menu, $level = 1)
    {
        $items = [];
        foreach($menu as $item)
        {
            $itemHtml = TElement::tag('li', '', ['class' => "x"]);

            $link = new TElement('a');
            $link->{'class'} = 'waves-effect waves-block';
            $link->add(!empty($item['icon']) ? new TImage($item['icon']) : '');
            $link->add(TElement::tag('span', $item['label']));

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
                $menus = TElement::tag('ul', $itemsMenu, ['class' => "ml-menu level-{$level}"]);
                $itemHtml->add($menus);
                $link->{'class'} .= ' menu-toggle ';
            }

            $items[] = $itemHtml->getContents();
        }

        return implode('', $items);
    }

    public function getMenu()
    {
        $menu = new TElement('ul');
                
        $li = new TElement('li');
        $li->{'class'} = 'active';
        $menu->add($li);
        
        $li = new TElement('li');
        $li->add('MENU');
        $li->{'class'} = 'header';
        $menu->add($li);
        
        $menu->class = 'list';
        $menu->style = 'overflow: hidden; width: auto; height: 390px;';
        
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
                $separator = new TElement('li');
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
                $a->{'class'} = 'waves-effect waves-block';
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

                $li = TElement::tag('li', $a);

                $items[] = $li->getContents();
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
                    $a->{'class'} = 'dropdown-toggle';
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

                    $ulMenu = TElement::tag('ul', $items,['class' => 'menu', 'style' => 'list-style:none']);

                    $li = TElement::tag('li', $a, ['class' => 'dropdown']);
                    $li->add(TElement::tag('ul', TElement::tag('li', $ulMenu, ['class' => 'body', 'style' => 'min-width: 270px']), ['class' => 'dropdown-menu']));
                    
                    $items_menus[] = $li->getContents();
                }
                else
                {
                    $a = new TElement('a');
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

                    $li = TElement::tag('li', $a, ['class' => 'dropdown']);

                    $items_menus[] = $li->getContents();
                }
            }

            $menus = implode('', $items_menus);
        }

        return $menus;
    }
}