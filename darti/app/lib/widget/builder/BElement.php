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

class BElement extends TElement
{
    protected $size;
    protected $height;
    
    /**
     * Class Constructor
     * @param $tagname  tag name
     */
    public function __construct($tagname)
    {
        parent::__construct($tagname);
    }
    
    /**
     * Define the widget's size
     * @param  $width   Widget's width
     * @param  $height  Widget's height
     */
    public function setSize($width, $height = NULL)
    {
        $this->size   = $width;
        if ($height)
        {
            $this->height = $height;
        }
        
        if ($this->size)
        {
            $this->size = str_replace('px', '', $this->size);
            $size = (strstr($this->size, '%') !== FALSE) ? $this->size : "{$this->size}px";
            $this->setProperty('style', "width:{$size};", FALSE); //aggregate style info
        }
        
        if ($this->height)
        {
            $this->height = str_replace('px', '', $this->height);
            $height = (strstr($this->height, '%') !== FALSE) ? $this->height : "{$this->height}px";
            $this->setProperty('style', "height:{$height}", FALSE); //aggregate style info
        }
        
    }
    
    /**
     * Returns the size
     * @return array(width, height)
     */
    public function getSize()
    {
        return array( $this->size, $this->height );
    }

    /**
     * Define a field property
     * @param $name  Property Name
     * @param $value Property Value
     */
    public function setProperty($name, $value, $replace = TRUE)
    {
        if ($replace)
        {
            // delegates the property assign to the composed object
            $this->$name = $value;
        }
        else
        {
            if ($this->$name)
            {
            
                // delegates the property assign to the composed object
                $this->$name = $this->$name . ';' . $value;
            }
            else
            {
                // delegates the property assign to the composed object
                $this->$name = $value;
            }
        }
        
        parent::setProperty($name, $this->name);
    }
}
