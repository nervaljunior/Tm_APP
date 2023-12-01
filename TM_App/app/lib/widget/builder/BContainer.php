<?php

/**
 *
 * @version    1.0
 * @package    widget
 * @subpackage base
 * @author     Matheus Agnes Dias
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */

class BContainer extends BootstrapFormBuilder
{
    /**
     * Class Constructor
     * @param $tagname  tag name
     */

    private $title;
    private $expanderEnabled = false;
    private $startExpanderOpened = false;
    private $id;
    private $borderColor = '#c0c0c0';
    private $titleFontSize;
    private $titelDecoration;
    private $titleBackgroundColor;
    private $titleFontColor;
    private $titleStyle;

    public function __construct($name)
    {
        parent::__construct($name);
    }

    public function setTitle($title, $titleFontColor = null, $titleFontSize = null, $titelDecoration = null, $titleBackgroundColor = null)
    {
        $this->title = $title;
        $this->titleFontSize = $titleFontSize;
        $this->titelDecoration = $titelDecoration;
        $this->titleBackgroundColor = $titleBackgroundColor;
        $this->titleFontColor = $titleFontColor;

        if (strpos(strtolower($this->titelDecoration), 'b') !== FALSE)
        {
            $this->titleStyle .= 'font-weight: bold;';
        }
        
        if (strpos(strtolower($this->titelDecoration), 'i') !== FALSE)
        {
            $this->titleStyle .= 'font-style: italic;';
        }
        
        if (strpos(strtolower($this->titelDecoration), 'u') !== FALSE)
        {
            $this->titleStyle .= 'text-decoration: underline;';
        }

        if($titleFontColor)
        {
            $this->titleStyle .= "color: {$titleFontColor};";
        }

        if($titleFontSize)
        {
            $this->titleStyle .= "font-size: {$titleFontSize};";
        }

        if($titleBackgroundColor)
        {
            $this->titleStyle .= "background-color: {$titleBackgroundColor};";
        }
    }

    public function enableExpander()
    {
        $this->expanderEnabled = true;
    }

    public function disableExpander()
    {
        $this->expanderEnabled = false;
    }

    public function enableStartExpanderOpened()
    {
        $this->startExpanderOpened = true;
    }

    public function setBorderColor($borderColor)
    {
        $this->borderColor = $borderColor;
    }

    public function setId($id)
    {
        $this->id = $id;
    }
    
    public function show()
    {
        if($this->title)
        {
            if($this->id)
            {
                $this->setProperty('id', $this->id);
            }
            
            $this->setProperty('class', 'bContainer-fieldset');
            $this->setProperty('style', "border:1px solid {$this->borderColor};");

            $this->titleStyle .= "border:1px solid {$this->borderColor};";

            $titleContainer = new TElement('div');
            $titleContainer->setProperty('class', 'bContainer-title');
            $titleContainer->setProperty('style', $this->titleStyle);
            $titleContainer->add($this->title);

            if($this->expanderEnabled)
            {
                $titleContainer->onClick = "BContainer.toggle(this);";

                if($this->startExpanderOpened)
                {
                    $this->setProperty('class', 'bContainer-fieldset bContainer-accordion');
                    $titleContainer->add("<i style='display:none' class='fas fa-plus bContainer-accordion-icon bContainer-accordion-icon-show'></i>");
                    $titleContainer->add("<i class='fas fa-minus bContainer-accordion-icon bContainer-accordion-icon-hide'></i>");
                }
                else
                {
                    $this->setProperty('class', 'bContainer-fieldset bContainer-accordion bContainer-accordion-hide');
                    $titleContainer->add("<i class='fas fa-plus bContainer-accordion-icon bContainer-accordion-icon-show'></i>");
                    $titleContainer->add("<i style='display:none' class='fas fa-minus bContainer-accordion-icon bContainer-accordion-icon-hide'></i>");
                }
            }

            $this->add($titleContainer);
        }
        else
        {
            $this->setProperty('style', 'border:none; box-shadow:none;');
        }
        
        parent::show();
    }

}
