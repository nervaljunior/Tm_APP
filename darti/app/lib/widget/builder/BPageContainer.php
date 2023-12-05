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

class BPageContainer extends TElement
{
    protected $size;
    protected $height;
    protected $action;
    protected $hide;

    /**
     * Class Constructor
     * @param $tagname  tag name
     */
    public function __construct()
    {
        $this->hide = false;
        parent::__construct('div');
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
    
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Returns the size
     * @return array(width, height)
     */
    public function getSize()
    {
        return array( $this->size, $this->height );
    }

    public function setAction($action)
    {
        $parameters = $action->getParameters();
        $parameters['target_container'] = $this->id;
        $parameters['register_state'] = 'false';
        $parameters['show_loading'] = 'false';
        $action->setParameters($parameters);
        $this->action = $action;
    }

    public function setParameter($key, $value)
    {
        if($this->action)
        {
            $this->action->setParameter($key, $value);
        }
    }

    public function unhide()
    {
        $this->hide = false;
    }

    public function hide()
    {
        $this->hide = true;
    }

    public function show()
    {
        if($this->hide)
        {
            parent::show();
            return;
        }
        $action = $this->action->getAction();

        $parameters = $this->action->getParameters();
        $parameters['target_container'] = $this->id;
        
        $controller = $action[0];
        $method = $action[1];

        ob_start();
        TApplication::loadPage($controller, $method, $parameters);
        $this->add(ob_get_contents());
        ob_end_clean();

        parent::show();
    }
}
