<?php

use Adianti\Database\TCriteria;

/**
 * Line chart Widget
 *
 * @version    7.4
 * @package    widget
 * @subpackage builder
 * @author     Lucas Tomasi
 */
class BLineChart extends BChart
{
    /**
     * Class Constructor
     * @param  $name         widget's name
     * @param  $database     database name
     * @param  $model        model class name
     * @param  $fieldGroup   table field to be used as group in the chart
     * @param  $fieldValue   table field to be used as calc total
     * @param  $joins        array with joins to be used on select
     * @param  $totalChart   set type total (optional) default sum [sum, max, min, count, avg]
     * @param  $criteria     criteria (TCriteria object) to filter the model (optional)
     */
    public function __construct(String $name, String $database = null, String $model = null, array $fieldGroup = [], String $fieldValue = null, array $joins = [], $totalChart = 'sum', TCriteria $criteria = NULL)
    {
        parent::__construct($name, $database, $model, [], $fieldValue, $joins, $totalChart, $criteria);
        $this->setFieldGroup($fieldGroup);
        $this->setType('line');
    }

    /**
     * Define transformer sub legends (lines)
     *
     * @param $transformer callable
     */
    public function setTransformerSubLegend(callable $transformer)
    {
        $this->transformerSubLegend = $transformer;
    }

    /**
     * Show grid
     */
    public function showGrid($showGrid = true)
    {
        $this->grid = $showGrid;
    }

    /**
     * Set label of value
     */
    public function setLabelValue($labelValue)
    {
        $this->labelValue = $labelValue;
    }

    /**
     * Set show area
     */
    public function showArea($areaRounded = true)
    {
        $this->area = TRUE;
        $this->areaRounded = $areaRounded;
    }
}