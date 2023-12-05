<?php

use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Database\TConnection;
use Adianti\Database\TCriteria;
use Adianti\Database\TFilter;
use Adianti\Database\TTransaction;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Template\THtmlRenderer;

/**
 * Chart Widget
 *
 * @version    7.4
 * @package    widget
 * @subpackage builder
 * @author     Lucas Tomasi
 */
abstract class BChart extends TElement
{
    private $options;
    private $formatsTooltip;

    protected $type;
    protected $name;
    protected $database;
    protected $model;
    protected $fieldGroup;
    protected $fieldValue;
    protected $fieldColor;
    protected $joins;
    protected $totalChart;
    protected $criteria;
    protected $html;
    protected $title;
    protected $legend;
    protected $percentage;
    protected $height;
    protected $width;
    protected $displayFunction;
    protected $tooltipFunction;
    protected $barDirection;
    protected $colors;
    protected $labelValue;
    protected $rotateLegend;
    protected $legendHeight;
    protected $grid;
    protected $showPanel;
    protected $area;
    protected $customClass;
    protected $transformerValue;
    protected $transformerLegend;
    protected $transformerSubLegend;
    protected $customOptions;
    protected $data;
    protected $loaded;
    protected $zoom;
    protected $areaRounded;
    protected $orderByValue;
    protected $legendsLimitShow;
    protected $barStack;
    protected $showMethods;

    /**
     * Class Constructor
     * @param  $name         widget's name
     * @param  $database     database name
     * @param  $model        model class name
     * @param  $fieldGroup   table fields to be used as group in the chart
     * @param  $fieldValue   table field to be used as calc total
     * @param  $joins        array with joins to be used on select
     * @param  $totalChart   set type total (optional) default sum [sum, max, min, count, avg]
     * @param  $criteria     criteria (TCriteria object) to filter the model (optional)
     */
    public function __construct(String $name, String $database = null, String $model = null, array $fieldGroup = [], String $fieldValue = null, array $joins = [], $totalChart = 'sum', TCriteria $criteria = NULL)
    {
        parent::__construct('div');

        $this->html = new THtmlRenderer(__DIR__.'/bchart.html');

        $this->name = $name;
        $this->showMethods = [];
        $this->setDatabase($database);
        $this->setModel($model);
        $this->setFieldGroup($fieldGroup);
        $this->setFieldValue($fieldValue);
        $this->setTotal($totalChart);
        $this->setCriteria($criteria??new TCriteria);
        $this->setJoins($joins);

        $this->showLegend();
        $this->showPercentage(FALSE);
        $this->hidePanel(FALSE);
        $this->setSize('100%', 300);

        $this->setDisplayNumeric();

        $this->grid = false;
        $this->area = false;
        $this->areaRounded = false;
        $this->loaded = false;
        $this->zoom = true;
        $this->orderByValue = false;
        $this->colors = ['#1abc9c', '#2ecc71', '#3498db', '#9b59b6', '#34495e', '#f1c40f', '#e67e22', '#e74c3c', '#95a5a6', '#16a085', '#27ae60', '#2980b9', '#8e44ad', '#2c3e50', '#f39c12', '#d35400', '#c0392b', '#7f8c8d', '#6182f7', '#9084c3', '#52812d', '#7af8ba', '#39953d', '#de6a40', '#1876a9', '#ad4316', '#5c1365', '#8b7e65', '#6ef53b', '#aea561', '#8a8fe8', '#b0ab4c', '#a3d22e', '#8b4515', '#5debbf', '#6dca27', '#9a535e', '#2daacc', '#102ac1', '#40d4e9', '#a7e34d', '#2e7888', '#d33da6', '#84f86d', '#fe0de0', '#5a1c46', '#4a98b1', '#2092c4', '#6f921d', '#7a6ea0', '#805d46', '#4669c1', '#63f587', '#325ca0', '#1a782d', '#9a5c25', '#ec7dd8', '#41acc3', '#3abe6c', '#bd65e2', '#eca34c', '#7c0239', '#b38c7e', '#726fa0', '#974bb6', '#54c3ec', '#6bfaa9', '#a9b472', '#953e18', '#fb6524', '#b4f5c1', '#6eaa5d', '#630b47', '#47fc00', '#93277c', '#81fc80', '#f1db20', '#ad6bc0', '#712269', '#36f290', '#e88da8', '#fbadcd', '#9f4923', '#c0bdb4', '#d592d0', '#9f707e', '#8f2d42', '#a5d6e0', '#92866f', '#43395e', '#69c28b', '#b96749', '#7b2bc2', '#ae70ac', '#b9bf26', '#9eb5c3', '#4149d3', '#b4deec', '#a7c4b2', '#58bab3', '#875db2', '#19c2f1', '#19b670', '#5b6aa4', '#78661f', '#830989', '#19898f', '#c3d27b', '#7f4848', '#ceb5fc', '#1f6200', '#4fb154', '#94debe', '#f3e9a3', '#e74431', '#5f635a', '#dfb39e', '#3576a4'];

        parent::add($this->html);
    }

    public static function generate(...$charts)
    {
        $chartsDBs = [];
        
        foreach($charts as $chart)
        {
            if (! $chart->canDisplay())
            {
                continue;
            }

            if (! $chart instanceof BTableChart && ! $chart instanceof BIndicator && ! $chart instanceof BChart)
            {
                throw new Exception(AdiantiCoreTranslator::translate('Invalid parameter (^1) in ^2', 'charts', __METHOD__));
            }

            $db = $chart->getDatabase();

            if ( empty($db) )
            {
                continue;
            }

            if ( empty($chartsDBs[$db]) )
            {
                $chartsDBs[$db] = [];
            }

            $chartsDBs[$db][] = $chart;
        }


        foreach($chartsDBs as $db => $charts)
        {
            TTransaction::open($db);

            foreach($charts as $chart)
            {
                $chart->create();
            }
            
            TTransaction::close();
        }
    }

    /**
     * Parse a functions
     *
     * @param $nameFunction name of function
     */
    public static function formatFunction($nameFunction)
    {
        return "::{$nameFunction}::";
    }

    /**
     * Validate method is allowed
     */
    public function canDisplay()
    {
        if ($this->showMethods)
        {
            return in_array($_REQUEST['method']??'', $this->showMethods);
        }

        return true;
    }

    /**
     * Set display condition
     *
     * @param $methods methods show chart
     */
    public function setShowMethods($methods = [])
    {
        $this->showMethods = $methods;
    }

    /**
     * Get tooltip function
     */
    private function getTootipFunction()
    {
        if (! $this->tooltipFunction )
        {
            return ["format" => ["value" => "::defaultFormatFunction::"]];
        }

        return ["contents" => "::{$this->tooltipFunction}::"];
    }

    /**
     * Get display function
     */
    private function getDisplayFunction()
    {
        if (! $this->displayFunction )
        {
            $this->displayFunction = 'defaultFormatFunction';
        }

        return $this->displayFunction;
    }

    /**
     * Define type char
     *
     * @param $type Type of the chart [pie, line, bar, donut]
     */
    protected function setType($type)
    {
        if (! in_array($type, ['pie', 'line', 'bar', 'donut']))
        {
            throw new Exception(AdiantiCoreTranslator::translate('Invalid parameter (^1) in ^2', $type, __METHOD__));
        }

        $this->type = $type;
    }

    /**
     * Define order data
     *
     */
    public function enableOrderByValue($order = 'desc')
    {
        $this->orderByValue = $order;
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
     * Set name
     *
     * @param $name name of chart
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Set count limit display legends
     */
    public function setLegendsLimitShow($x, $y)
    {
        $this->legendsLimitShow = [$x, $y];
    }

    /**
     * Define transformer values
     *
     * @param $transformer callable
     */
    public function setTransformerValue(callable $transformer)
    {
        $this->transformerValue = $transformer;
    }

    /**
     * Define transformer legends
     *
     * @param $transformer callable
     */
    public function setTransformerLegend(callable $transformer)
    {
        $this->transformerLegend = $transformer;
    }

    /**
     * Define custom css class
     *
     * @param $class css class
     */
    public function setCustomClass($class)
    {
        $this->customClass = $class;
    }

    /**
     * Get database name
     */
    public function getDatabase()
    {
        return $this->database;
    }

    /**
     * Set database name
     * @param  $database database name
     */
    public function setDatabase($database)
    {
        $this->database = $database;
    }

    /**
     * Set model name
     * @param  $model model extends TRecord
     */
    public function setModel($model)
    {
        $this->model = $model;
    }

    /**
     * Set field used for group
     * @param  $fieldGroup array field names
     */
    public function setFieldGroup($fieldGroup)
    {
        if (! is_array($fieldGroup) || count($fieldGroup) > 2)
        {
            throw new Exception(AdiantiCoreTranslator::translate('Invalid parameter (^1) in ^2', 'fieldGroup', __METHOD__));
        }
        $this->fieldGroup = $fieldGroup;
    }

    /**
     * Set field value
     * @param  $fieldValue field name
     */
    public function setFieldValue($fieldValue)
    {
        $this->fieldValue = $fieldValue;
    }

    /**
     * Set field value
     * @param  $fieldColor field name
     */
    public function setFieldColor($fieldColor)
    {
        $this->fieldColor = $fieldColor;
    }

    /**
     * Set joins
     * @param  $joins database joins
     */
    public function setJoins($joins)
    {
        $this->joins = $joins;
    }

    /**
     * Set total
     * @param  $totalChart set type total (optional) default sum [sum, max, min, count, avg]
     */
    public function setTotal($totalChart)
    {
        if (! in_array($totalChart, ['sum', 'max', 'min', 'count', 'avg']))
        {
            throw new Exception(AdiantiCoreTranslator::translate('Invalid parameter (^1) in ^2', $totalChart, __METHOD__));
        }

        $this->totalChart = $totalChart;
    }

    /**
     * Set criteria for filter
     * @param  $criteria criteria with filters
     */
    public function setCriteria(TCriteria $criteria)
    {
        $this->criteria = $criteria;
    }

    /**
     * Get title panel
     * @param $title String title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Show % values
     */
    public function showPercentage($percentage = true)
    {
        $this->percentage = $percentage;
    }

    /**
     * Hide panel
     */
    public function hidePanel($hide = true)
    {
        $this->showPanel = ! $hide;
    }

    /**
     * Enable legend chart
     */
    public function showLegend($legend = true)
    {
        $this->legend = $legend;
    }

    /**
     * Rotate legend chart
     */
    public function setRotateLegend($rotate, $height = 100)
    {
        $this->rotateLegend = $rotate;
        $this->legendHeight = $height;
    }

    /**
     * Return sizes
     */
    public function getSize()
    {
        return null;
    }

    /**
     * Set size panel chart
     * @param $width  size width
     * @param $height size height
     */
    public function setSize($width, $height)
    {
        $height = (strstr($height, '%') !== FALSE) ? $height : "{$height}px";
        $width  = (strstr($width, '%') !== FALSE) ? $width : "{$width}px";

        $this->width = $width;
        $this->height = $height;
    }

    /**
     * Set tootip function: need 4 params (d, defaultTitleFormat, defaultValueFormat, color)
     * @see https://c3js.org/reference.html#tooltip-contents
     *
     * @param $function name function js
     */
    public function setTootipFunction($function)
    {
        $this->tooltipFunction = $function;
    }

    /**
     * Set display function
     * @see https://c3js.org/reference.html#data-labels-format
     *
     * @param $function name function js
     */
    public function setDisplayFunction($function)
    {
        $this->displayFunction = $function;
    }

    /**
     * Set display function with numeric mask
     *
     * @param $precision precision
     * @param $decimalSeparator decimal separator
     * @param $thousandSeparator thousand separator
     * @param $prefix prefix value
     * @param $sufix sufix value
     */
    public function setDisplayNumeric($precision = 2,  $decimalSeparator = ',',  $thousandSeparator = '.',  $prefix = '',  $sufix = '')
    {
        $this->precision = $precision;
        $this->decimalSeparator = $decimalSeparator;
        $this->thousandSeparator = $thousandSeparator;
        $this->prefix = $prefix;
        $this->sufix = $sufix;
    }

    /**
     * Get data
     */
    private function loadData()
    {
        $items = [];

        if (empty($this->database))
        {
            throw new Exception(AdiantiCoreTranslator::translate('The parameter (^1) of ^2 is required', 'database', __CLASS__));
        }

        if (empty($this->model))
        {
            throw new Exception(AdiantiCoreTranslator::translate('The parameter (^1) of ^2 is required', 'model', __CLASS__));
        }

        if (empty($this->fieldGroup))
        {
            throw new Exception(AdiantiCoreTranslator::translate('The parameter (^1) of ^2 is required', 'fieldGroup', __CLASS__));
        }

        if (empty($this->fieldValue))
        {
            throw new Exception(AdiantiCoreTranslator::translate('The parameter (^1) of ^2 is required', 'fieldValue', __CLASS__));
        }

        $cur_conn = serialize(TTransaction::getDatabaseInfo());
        $new_conn = serialize(TConnection::getDatabaseInfo($this->database));

        $open_transaction = ($cur_conn !== $new_conn);

        // open transaction case not opened
        if ($open_transaction)
        {
            TTransaction::open($this->database);
        }

        $conn = TTransaction::get();

        $entity = (new $this->model)->getEntity();
        $entities = array_keys($this->joins??[]);
        if(!in_array($entity, $entities))
        {
            $entities[] = $entity;
        }

        $entities = implode(', ', $entities);

        if ($this->joins)
        {
            foreach ($this->joins as $join)
            {
                $key = $join[0];

                // Not find dot, insert table name before
                if (strpos($key, '.') === FALSE)
                {
                    $key = "{$entity}.{$key}";
                }

                if(count($join) > 2)
                {
                    $operator = $join[1];
                    $value    = $join[2];
                }
                else
                {
                    $operator = '=';
                    $value    = $join[1];
                }

                // Not find dot, insert table name before
                if (strpos($value, '.') === FALSE)
                {
                    $value = "{$entity}.{$value}";
                }

                $this->criteria->add(new TFilter($key, $operator, "NOESC: {$value}"));
            }
        }

        $sql = new BChartSqlSelect();
        $groups = [];

        if ($this->fieldColor)
        {
            $sql->addColumn("{$this->fieldColor} as color");
            $groups[] = "color";
        }

        foreach($this->fieldGroup AS $key => $fieldGroup)
        {
            // Not find dot, insert table name before
            if (strpos($fieldGroup, '.') === FALSE && strpos($fieldGroup, ':') === FALSE && strpos($fieldGroup, '(') === FALSE)
            {
                $fieldGroupColumn = "{$entity}.{$fieldGroup} as {$fieldGroup}";
                $fieldGroup = "{$entity}.{$fieldGroup}";
            }
            else 
            {
                $column = explode('.', $fieldGroup);
                $fieldGroupColumn = "{$fieldGroup} as fieldGroup{$key}";
            }

            $sql->addColumn($fieldGroupColumn);
            $groups[] = ($fieldGroup);
        }

        // Not find dot, insert table name before
        if (strpos($this->fieldValue, '.') === FALSE && strpos($this->fieldValue, ':') === FALSE && strpos($this->fieldValue, '(') === FALSE)
        {
            $this->fieldValue = "{$entity}.{$this->fieldValue}";
        }

        $orders = $groups;

        if ($this->orderByValue)
        {
            $conn = TTransaction::get();
            $driver = $conn->getAttribute(PDO::ATTR_DRIVER_NAME);
            
            $totalOrder =  'total ';

            if (in_array($driver, array('mssql', 'dblib', 'sqlsrv')))
            {
                $totalOrder = "{$this->totalChart}({$this->fieldValue}) ";
            }

            array_unshift($orders, $totalOrder . $this->orderByValue );
        }

        $group = implode(', ', $groups);
        $order = implode(', ', $orders);

        $this->criteria->setProperty('group', $group);
        $this->criteria->setProperty('order', $order);
        
        $sql->addColumn("{$this->totalChart}({$this->fieldValue}) as total");
        $sql->setEntity($entities);
        $sql->setCriteria($this->criteria);

        $stmt = $conn->prepare($sql->getInstruction(TRUE), array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $result = $stmt->execute($this->criteria->getPreparedVars());

        if($result)
        {
            $items = $stmt->fetchAll(PDO::FETCH_NUM);

            if ($this->fieldColor)
            {
                if (count($this->fieldGroup) > 1)
                {
                    $this->colors = array_column($items, 0, 2);
                }
                else if ($this instanceof BBarChart)
                {
                    $this->colors = array_column($items, 0);
                }
                else
                {
                    $this->colors = array_column($items, 0, 1);
                }

                $items = array_map(function($item) {
                    unset($item[0]);
                    return array_values($item);
                }, $items);
            }
        }

        // close connection
        if ($open_transaction)
        {
            TTransaction::close();
        }

        $this->data = $items;
    }

    private function formatTooltip()
    {
        $simpleCharts = in_array($this->type, ['pie', 'donut']);

        if (! $this->transformerValue)
        {
            if ($simpleCharts)
            {
                $this->options['tooltip'] = $this->getTootipFunction();
            }
        }

        $formats = [];
        $formatsTooltip = new stdClass;

        foreach ( $this->options['data']['columns'] as $column)
        {
            $newColumns = [];

            foreach($column as $key => $value)
            {
                if($key == 0)
                {
                    continue;
                }

                $newColumns[] = $this->transformerValue ? call_user_func($this->transformerValue, $value, $column, $this->options['data']['columns']) : $value;
            }

            $formatsTooltip->{$column[0]} = $newColumns;

            $values = json_encode($newColumns);

            $key = $simpleCharts ? '0' : 'i';

            $formats[$column[0]] = "::(v, id, i, j, k) => { const values = eval('{$values}'); return values[{$key}]; }::";
        }

        $this->formatsTooltip = json_encode($formatsTooltip);

        $this->options['data']['labels'] = ['format' => $formats];

        $return = "tooltipFormats[i][x]";

        if ($simpleCharts)
        {
            $return = "tooltipFormats[i][0]";

            if ( ! $this->percentage)
            {
                $this->options[$this->type]['label']['format'] =  "::(v, id, i, j, k) => {$return}::";;
            }
        }

        $this->options['tooltip']['format'] = ['value' => "::(v,r,i,x,z) => {$return}::"];
    }

    /**
     * Adjust columns to bar chart
     * @param $option
     */
    private function makeOptionsBar()
    {
        $data = $this->options['data']['columns'];

        $this->options[$this->type]['space'] = .1;

        $countGroups = count($this->fieldGroup);

        $labelsData = array_unique(array_column($data, 0));

        $labels = [];
        $labelsFormatted = [];

        foreach($labelsData as $label)
        {
            foreach($labelsData as $label)
            {
                $labels[] = $label;

                if ($this->transformerLegend)
                {
                    $labelsFormatted[] = call_user_func($this->transformerLegend, $label, $labelsData, $data);
                }
                else
                {
                    $labelsFormatted[] = $label;
                }
            }
        }

        $this->options['axis']['rotated'] = ($this->barDirection == 'horizontal');

        // Case more one column group adjust values
        if($countGroups > 1)
        {
            $values = [];

            $registers = (count(array_unique((array_column($data,0)))));

            $line = array_fill(1, $registers, 0);

            foreach($data as $item)
            {
                $key = $item[$countGroups-1];

                if ($this->transformerSubLegend)
                {
                    $key = call_user_func($this->transformerSubLegend, $key, $item, $data);
                }

                if (empty($values[$key]))
                {
                    $values[$key] = array_merge([$key], $line);
                }

                $posi = array_search($item[0], $labels);

                $values[$key][$posi + 1] = $item[$countGroups];
            }

            sort($values);
            
            $data = $values;

            $this->options['axis']['x'] = ['type' => 'category','categories' => $labelsFormatted, 'tick' => ['centered' => true]];

            if ($this->barStack)
            {
                $this->options['data']['groups'] = [array_column($values, 0)];
            }
        }
        else
        {
            $values = array_column($data, 1);

            $valueInit = $this->labelValue??$this->fieldGroup[0];

            $data = [array_merge([$valueInit], $values)];

            $this->options['legend']['show'] = FALSE;
            $this->options['axis']['x'] = ['type' => 'category', 'categories' => $labelsFormatted, 'tick' => ['centered' => true]];
            $this->options['data']['color'] = '::changeColor::';
        }

        $this->options['data']['columns'] = $data;

        $this->formatTooltip();
    }

    /**
     * Adjust columns to line chart
     * @param $option
     */
    private function makeOptionsline()
    {
        $data = $this->options['data']['columns'];

        $countGroups = count($this->fieldGroup);

        $labelsData = array_unique(array_column($data, 0));

        $labels = [];
        $labelsFormatted = [];

        foreach($labelsData as $label)
        {
            $labels[] = $label;

            if ($this->transformerLegend)
            {
                $labelsFormatted[] = call_user_func($this->transformerLegend, $label, $labelsData, $data);
            }
            else
            {
                $labelsFormatted[] = $label;
            }
        }

        $this->options['axis']['rotated'] = ($this->barDirection == 'horizontal');

        // Case more one column group adjust values
        if($countGroups > 1)
        {
            $values = [];

            $line = array_fill(1, count($labels), 0);

            foreach($data as $item)
            {
                $key = $item[$countGroups-1];

                if ($this->transformerSubLegend)
                {
                    $key = call_user_func($this->transformerSubLegend, $key, $item, $data);
                }

                if (empty($values[$key]))
                {
                    $values[$key] = array_merge( [$key], $line);
                }

                $posi = array_search($item[0], $labels);

                $values[$key][$posi + 1] = $item[$countGroups];
            }

            sort($values);

            $data = $values;

            $this->options['axis']['x'] = ['type' => 'category','categories' => $labelsFormatted];
        }
        else
        {
            $values = array_column($data, 1);

            $valueInit = $this->labelValue??$this->fieldGroup[0];

            $data = [array_merge([$valueInit], $values)];

            $this->options['legend']['show'] = FALSE;
            $this->options['axis']['x'] = ['type' => 'category', 'categories' => $labelsFormatted];
        }

        $this->options['data']['columns'] = $data;

        $this->formatTooltip();
    }

    public function makeOptionsPie()
    {
        $data = $this->options['data']['columns'];

        if ($this->transformerLegend)
        {
            $dataFormatted = [];

            foreach ($data as $key => $item)
            {
                $dataFormatted[$key] = $item;
                $dataFormatted[$key][0] = call_user_func($this->transformerLegend, $item[0], $item, $data);
            }

            $this->options['data']['columns'] = $dataFormatted;
        }

        $this->formatTooltip();
    }

    public function makeOptions()
    {
        $displayFunction = $this->getDisplayFunction();

        $type = ($this->area) ? ('area' . ($this->areaRounded? '-spline' : '') ) : $this->type;

        $this->options = [
            $this->type => [
                'label' =>  ['format' => $this->percentage ? null : "::{$displayFunction}::"],
            ],
            'size' => [
                'height' => str_replace('px', '', $this->height)
            ],
            'data' => [
                'columns' => $this->data,
                'type' => $type
            ],
            'bindto' => "#{$this->name}-container",
            'legend' => ['show' =>  $this->legend],
            'zoom' => ["enabled" => $this->zoom],
            'axis' => []
        ];

        if ($this->title AND ! $this->showPanel)
        {
            $this->options['title'] = ['text' => $this->title];
        }

        if ($this->colors)
        {
            if ($this->fieldColor)
            {
                $this->options['data']['colors'] = $this->colors;
                $this->options['data']['color'] = "::(color, d) => colors[d.id] ?? color::";
            }
            else
            {
                $this->options['color'] = ['pattern' => $this->colors];
            }
        }

        if ($this->type == 'bar')
        {
            $this->makeOptionsBar();
        }
        else if ($this->type == 'line')
        {
            $this->makeOptionsLine();
        }
        else
        {
            $this->makeOptionsPie();
        }

        if ($this->grid)
        {
            $this->options['grid'] = [
                'x' => [ 'show' => true ],
                'y' => [ 'show' => true ]
            ];
        }

        if($this->rotateLegend)
        {
            $this->options['axis']['x']['tick'] = [
                'rotate' => $this->rotateLegend,
            ];

            $this->options['axis']['x']['height'] = $this->legendHeight;
        }

        if ($this->legendsLimitShow)
        {
            if ( empty($this->options['axis']['x']['tick']) )
            {
                $this->options['axis']['x']['tick'] = [];
            }

            if ( empty($this->options['axis']['y']['tick']) )
            {
                $this->options['axis']['y']['tick'] = [];
            }

            $this->options['axis']['x']['tick']['culling'] = ['max' => $this->legendsLimitShow[0]];
            $this->options['axis']['y']['tick']['count'] = $this->legendsLimitShow[1];
        }
    }

    /**
     * Set colors chart
     *
     * @param $colors array with string colors
     */
    public function setColors(array $colors)
    {
        $this->colors = $colors;
    }

    /**
     * Set zoom disabled
     *
     */
    public function disableZoom()
    {
        $this->zoom = FALSE;
    }

    /**
     * Merge options with defaults
     *
     * @param $options array options
     */
    public function setCustomOptions(array $options)
    {
        $this->customOptions = $options;
    }

    /**
     * Exec chart before show
     */
    public function create()
    {
        $this->loaded = true;
        $this->loadData();
    }

    /**
    * Show
    */
    public function show()
    {
        if (empty($this->type))
        {
            throw new Exception(AdiantiCoreTranslator::translate('The parameter (^1) of ^2 is required', 'type', __CLASS__));
        }

        if (! $this->canDisplay())
        {
            return;
        }

        if (! $this->loaded)
        {
            $this->create();
        }

        $this->makeOptions();

        if ($this->customOptions)
        {
            $this->options = array_replace_recursive($this->options, $this->customOptions);
        }

        $options = json_encode($this->options);
        $options = str_replace('"::', "", $options);
        $options = str_replace('::"', "", $options);

        $this->html->enableSection('main');

        if (empty($this->data))
        {
            $this->html->enableSection(
                'no-data',
                [
                    'class' => $this->customClass,
                    'name' => $this->name,
                    'width' => $this->width,
                    'height' => $this->height,
                    'title' => $this->title,
                    'label' => _t('No records found'),
                ]
            );
        }
        else
        {
            $this->html->enableSection(
                'data',
                [
                    'class' => $this->customClass,
                    'name' => $this->name,
                    'options' => $options,
                    'width' => $this->width,
                    'precision' => $this->precision,
                    'decimalSeparator' => $this->decimalSeparator,
                    'thousandSeparator' => $this->thousandSeparator,
                    'prefix' => $this->prefix,
                    'sufix' => $this->sufix,
                    'colors' => json_encode($this->colors??[]),
                    'tooltipFormats' => $this->formatsTooltip??'0'
                ]
            );
    
            $this->html->enableSection( $this->showPanel ? 'panel' : 'nopanel', ['name' => $this->name]);

            if ($this->title && $this->showPanel)
            {
                $this->html->enableSection('header', ['title' => $this->title]);
            }
        }


        parent::show();
    }
}
