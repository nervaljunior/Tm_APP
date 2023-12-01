<?php


/**
 * BreadCrumb
 *
 * @version    3.0
 * @package    widget
 * @subpackage util
 * @author     Pablo Dall'Oglio
 * @author     Nataniel Rabaioli
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TStep extends TElement
{
    protected $container;
    protected $items;
    private   $stepNumber = 1;
    /**
     * Handle paths from a XML file
     * @param $xml_file path for the file
     */
    public function __construct()
    {
        parent::__construct('div');
        $this->{'id'} = 'div_steps';
        
        $this->container = new TElement('ul');
        $this->container->{'class'} = 'steps';
        parent::add( $this->container );
    }
    /**
     * Add an item
     * @param $path Path to be shown
     * @param $last If the item is the last one
     */
    public function addItem($title, $active = false, $complete = false)
    {
        $li = new TElement('li');

        if($complete)
        {
            $li->class = 'complete';   
        }
        elseif($active)
        {
            $li->class = 'active';
        }

        $this->container->add( $li );
        
        $spanTitle = new TElement('span');
        $spanTitle->class = 'step-title';
        $spanTitle->add( $title );

        $spanStep = new TElement('span');
        $spanStep->class = 'step-number';
        $spanStep->add( $this->stepNumber );
    
        
        $li->add( $spanStep );
        $li->add( $spanTitle );
        
        $this->stepNumber++;
    }
}
