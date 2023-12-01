<?php

use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Widget\Base\TElement;

/**
 * Table column chart Widget
 *
 * @version    7.4
 * @package    widget
 * @subpackage builder
 * @author     Lucas Tomasi
 */
class BTableColumnChart extends TElement
{
    protected $name;
    protected $order;
    protected $width;
    protected $align;
    protected $transformer;
    protected $total;
    protected $aggregate;
    protected $label;
    protected $transformerTotal;

    /**
     * Class Constructor
     * @param  $name column name
     */
    public function __construct(String $name, String $label, $align = 'left', $width = '', $order = '')
    {
        parent::__construct('div');

        $this->name = $name;
        $this->order = $order;
        $this->label = $label;

        $this->setAlign($align);
        $this->setWidth($width);
    }

    /**
     * Get name
     *
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set label
     *
     * @param $label header label of chart
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * Get label
     *
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set name
     *
     * @param $name name of chart
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Set width
     *
     * @param $width column size
     */
    public function setWidth($width)
    {
        $this->width = (is_numeric($width) !== FALSE) ? "{$width}px" : $width ;
    }

    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set order
     *
     * @param $order column size
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }

    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set align column
     */
    public function setAlign($align)
    {
        if (! in_array($align, ['left', 'right', 'center']))
        {
            throw new Exception(AdiantiCoreTranslator::translate('Invalid parameter (^1) in ^2', $align, __METHOD__));
        }

        $this->align = $align;
    }

    /**
     * Get align column
     */
    public function getAlign()
    {
        return $this->align;
    }

    /**
     * Set transformer column
     */
    public function setTransformer(callable $transformer)
    {
        $this->transformer = $transformer;
    }

    /**
     * Get transformer column
     */
    public function getTransformer()
    {
        return $this->transformer;
    }

    /**
     * Set total
     * @param  $total set type total (optional) default sum [sum, max, min, count, avg]
     * @param  $transformerTotal set function transformer total number
     */
    public function setTotal($total, callable $transformerTotal = null)
    {
        if (! in_array($total, ['sum', 'max', 'min', 'count', 'avg']))
        {
            throw new Exception(AdiantiCoreTranslator::translate('Invalid parameter (^1) in ^2', $total, __METHOD__));
        }

        $this->total = $total;
        $this->transformerTotal = $transformerTotal;
    }

    /**
     * Return total column
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Return total column
     */
    public function getTransformerTotal()
    {
        return $this->transformerTotal;
    }

     /**
     * Set aggregate column
     * @param  $aggregate set type aggregate (optional) default sum [sum, max, min, count, avg]
     */
    public function setAggregate($aggregate)
    {
        if (! in_array($aggregate, ['sum', 'max', 'min', 'count', 'avg']))
        {
            throw new Exception(AdiantiCoreTranslator::translate('Invalid parameter (^1) in ^2', $aggregate, __METHOD__));
        }

        $this->aggregate = $aggregate;
    }

    /**
     * Return aggregate column
     */
    public function getAggregate()
    {
        return $this->aggregate;
    }
}