<?php

use Adianti\Database\TCriteria;

/**
 * Donut chart Widget
 *
 * @version    7.4
 * @package    widget
 * @subpackage builder
 * @author     Lucas Tomasi
 */
class BDonutChart extends BChart
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
    public function __construct(String $name, String $database = null, String $model = null, String $fieldGroup = '', String $fieldValue = null, array $joins = [], $totalChart = 'sum', TCriteria $criteria = NULL)
    {
        parent::__construct($name, $database, $model, [], $fieldValue, $joins, $totalChart, $criteria);
        $this->setFieldGroup($fieldGroup);
        $this->setType('donut');
    }

    public function setFieldGroup($fieldGroup)
    {
        parent::setFieldGroup([$fieldGroup]);
    }
}