<?php

use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Database\TConnection;
use Adianti\Database\TCriteria;
use Adianti\Database\TFilter;
use Adianti\Database\TSqlSelect;
use Adianti\Database\TTransaction;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Base\TStyle;
use Adianti\Widget\Datagrid\TDataGrid;
use Adianti\Widget\Datagrid\TDataGridColumn;
use Adianti\Wrapper\BootstrapDatagridWrapper;

/**
 * Table chart Widget
 *
 * @version    7.4
 * @package    widget
 * @subpackage builder
 * @author     Lucas Tomasi
 */
class BTableChart extends TElement
{
    protected $datagrid;

    protected $name;
    protected $database;
    protected $model;
    protected $columns;
    protected $joins;
    protected $criteria;
    protected $data;
    protected $loaded;
    protected $title;
    protected $customClass;
    protected $showPanel;
    protected $height;
    protected $width;
    protected $groupColumns;
    protected $showMethods;
    
    // Colors
    private $rowColorOdd;
    private $rowColorEven;
    private $fontRowColorOdd;
    private $fontRowColorEven;
    private $borderColor;
    private $tableHeaderColor;
    private $tableHeaderFontColor;
    private $tableFooterColor;
    private $tableFooterFontColor;

    /**
     * Class Constructor
     * @param  $name         widget's name
     * @param  $database     database name
     * @param  $model        model class name
     * @param  $joins        array with joins to be used on select
     */
    public function __construct(String $name, String $database = null, String $model = null, array $joins = [])
    {
        parent::__construct('div');

        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid($name));
        
        $this->columns = [];
        $this->groupColumns = [];
        $this->showMethods = [];

        $this->name = $name;
        $this->setDatabase($database);
        $this->setModel($model);
        $this->setCriteria($criteria??new TCriteria);
        $this->setJoins($joins);

        $this->hidePanel(FALSE);
        $this->setSize('100%', 300);

        $this->loaded = false;
        parent::add($this->html);
    }
    
    /**
     * Set row odd color
     * @param $rowColorOdd string color
     */
    public function setRowColorOdd($rowColorOdd)
    {
        $this->rowColorOdd = $rowColorOdd;
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
     * Get row odd color
     */
    public function getRowColorOdd()
    {
        return $this->rowColorOdd;
    }

    /**
     * Set row even color
     * @param $rowColorEven string color
     */
    public function setRowColorEven($rowColorEven)
    {
        $this->rowColorEven = $rowColorEven;
    }

    /**
     * Get row even color
     */
    public function getRowColorEven()
    {
        return $this->rowColorEven;
    }

    /**
     * Set row odd font color
     * @param $fontRowColorOdd string color
     */
    public function setFontRowColorOdd($fontRowColorOdd)
    {
        $this->fontRowColorOdd = $fontRowColorOdd;
    }

    /**
     * Get row odd font color
     */
    public function getFontRowColorOdd()
    {
        return $this->fontRowColorOdd;
    }

    /**
     * Set row even font color
     * @param $fontRowColorEven string color
     */
    public function setFontRowColorEven($fontRowColorEven)
    {
        $this->fontRowColorEven = $fontRowColorEven;
    }

    /**
     * Get row even font color
     */
    public function getFontRowColorEven()
    {
        return $this->fontRowColorEven;
    }

    /**
     * Set row border color
     * @param $borderColor string color
     */
    public function setBorderColor($borderColor)
    {
        $this->borderColor = $borderColor;
    }

    /**
     * Get row border color
     */
    public function getBorderColor()
    {
        return $this->borderColor;
    }

    /**
     * Set table header color
     * @param $tableHeaderColor string color
     */
    public function setTableHeaderColor($tableHeaderColor)
    {
        $this->tableHeaderColor = $tableHeaderColor;
    }

    /**
     * Get table header color
     */
    public function getTableHeaderColor()
    {
        return $this->tableHeaderColor;
    }

    /**
     * Set table header font color
     * @param $tableHeaderFontColor string color
     */
    public function setTableHeaderFontColor($tableHeaderFontColor)
    {
        $this->tableHeaderFontColor = $tableHeaderFontColor;
    }

    /**
     * Get table header font color
     */
    public function getTableHeaderFontColor()
    {
        return $this->tableHeaderFontColor;
    }

    /**
     * Set table footer color
     * @param $tableFooterColor string color
     */
    public function setTableFooterColor($tableFooterColor)
    {
        $this->tableFooterColor = $tableFooterColor;
    }

    /**
     * Get table footer color
     */
    public function getTableFooterColor()
    {
        return $this->tableFooterColor;
    }

    /**
     * Set table footer font color
     * @param $tableFooterFontColor string color
     */
    public function setTableFooterFontColor($tableFooterFontColor)
    {
        $this->tableFooterFontColor = $tableFooterFontColor;
    }

     /**
     * Get table footer font color
     */
    public function getTableFooterFontColor()
    {
        return $this->tableFooterFontColor;
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
     * Add column
     * @param  $column BTableColumnChart column
     */
    public function addColumn(BTableColumnChart $column)
    {
        $this->columns[] = $column;
    }

    /**
     * Set group column
     * @param  $column BTableColumnChart column
     */
    public function setGroupColumn($column, callable $transformer = null, bool $showTotal = false, callable $transformerTotal = null)
    {
        $groupColumn = new stdClass;
        $groupColumn->column = $column;
        $groupColumn->transformer = $transformer;
        $groupColumn->showTotal = $showTotal;
        $groupColumn->transformerTotal = $transformerTotal;

        $this->groupColumns[] = $groupColumn;
    }

    /**
     * Set columns
     * @param  $columns [ BTableColumnChart ] array of columns
     */
    public function setColumns($columns)
    {
        if (! is_array($columns))
        {
            return;
        }
        
        foreach($columns as $column)
        {
            $this->addColumn($column);
        }
    }

    /**
     * Get column     
     * List columns
     */
    public function getColumns()
    {
        return $this->columns;
    }

     /**
     * Get column     
     * List columns
     */
    public function getColumn($name)
    {
        $filter = array_filter($this->columns, function($c) use ($name) { return $c->getName() === $name; });

        return $filter ? $filter[0] : null;
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
     * Hide panel
     */
    public function hidePanel($hide = true)
    {
        $this->showPanel = ! $hide;
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
     * Set size panel chart
     * @param $width  size width
     * @param $height size height
     */
    public function getSize()
    {
        return null;
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

        if (empty($this->columns))
        {
            throw new Exception(AdiantiCoreTranslator::translate('The parameter (^1) of ^2 is required', 'columns', __CLASS__));
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

        $sql = new TSqlSelect();
        $groups = [];
        $orders = [];

        foreach($this->columns AS $key => $tableColumn)
        {
            $nameColumn = $tableColumn->getName();
            $name = $tableColumn->getName();

            // Not find dot, insert table name before
            if (strpos($nameColumn, '.') === FALSE && strpos($nameColumn, ':') === FALSE && strpos($nameColumn, '(') === FALSE)
            {
                $nameColumn = "{$entity}.{$nameColumn}";
            }

            if ($tableColumn->getAggregate())
            {
                $nameColumn = "{$tableColumn->getAggregate()}($nameColumn)";
            }
            else
            {
                $groups[] = ($nameColumn);
            }

            $sql->addColumn("{$nameColumn} as \"{$name}\" ");

            if ($tableColumn->getOrder())
            {
                $orders[] = "{$name} {$tableColumn->getOrder()}";
            }
        }

        if ($this->groupColumns)
        {
            foreach($this->groupColumns AS $key => $tableColumn)
            {
                $nameColumn = $tableColumn->column;
                $name = $tableColumn->column;

                // Not find dot, insert table name before
                if (strpos($nameColumn, '.') === FALSE && strpos($nameColumn, ':') === FALSE && strpos($nameColumn, '(') === FALSE)
                {
                    $nameColumn = "{$entity}.{$nameColumn}";
                }

                $groups[] = count($this->columns) + $key + 1;
                $sql->addColumn("{$nameColumn} as \"{$name}\" ");            
            }
        }

        $group = implode(', ', $groups);

        $this->criteria->setProperty('group', $group);
        if($orders)
        {
            $this->criteria->setProperty('order', implode(', ', $orders));
        }

        $sql->setEntity($entities);
        $sql->setCriteria($this->criteria);

        $stmt = $conn->prepare($sql->getInstruction(TRUE), array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $result = $stmt->execute($this->criteria->getPreparedVars());

        if($result)
        {
            $items = $stmt->fetchAll(PDO::FETCH_CLASS, 'stdClass');
        }

        // close connection
        if ($open_transaction)
        {
            TTransaction::close();
        }

        $this->data = $items;
    }

    /**
     * Exec chart before show
     */
    public function create()
    {
        $this->loaded = true;
        $this->loadData();
    }

    private function makeDatagrid()
    {
        $this->datagrid->{'class'} .= " {$this->customClass} table-responsive ";
        $this->datagrid->{'style'} = "width: {$this->width};";
        $this->datagrid->setHeight($this->height);
        $this->datagrid->makeScrollable();

        if ($this->groupColumns)
        {
            foreach( $this->groupColumns as $groupColumn )
            {
                $this->datagrid->setGroupColumn($groupColumn->column, null, $groupColumn->transformer);
                $this->datagrid->useGroupTotal($groupColumn->showTotal);
            }
        }

        foreach( $this->columns as $tableColumn)
        {
            $tdcolumn = new TDataGridColumn($tableColumn->getName(), $tableColumn->getLabel(), $tableColumn->getAlign() , $tableColumn->getWidth());
            
            $totalFunction = $tableColumn->getTotal();
            $transformerTotal = $tableColumn->getTransformerTotal();

            if ($totalFunction)
            {
                $tdcolumn->setTotalFunction(
                    function($values) use ($totalFunction, $transformerTotal) {
                        $total = null;

                        if ($totalFunction === 'avg' && count($values??[]) > 0)
                        {
                            $total = array_sum($values) / count($values??[]);
                        }
                        else if ($totalFunction === 'count')
                        {
                            $total = count($values??[]);
                        }
                        else if ($totalFunction === 'sum')
                        {
                            $total = array_sum($values??[]);
                        }
                        else if ($totalFunction === 'max')
                        {
                            $total = max($values??[]);
                        }
                        else if ($totalFunction === 'min')
                        {
                            $total = min($values??[]);
                        }

                        if ($transformerTotal)
                        {
                            return call_user_func($transformerTotal, $total, null, null);
                        }

                        return $total;
                    },
                    FALSE
                );
            }

            if ($tableColumn->getTransformer())
            {
                $tdcolumn->setTransformer($tableColumn->getTransformer());
            }


            $this->datagrid->addColumn($tdcolumn);
        }

        $this->datagrid->createModel();
    }

    public function setDataDatagrid()
    {
        $this->datagrid->clear();

        if (empty($this->data))
        {
            return;
        }

        foreach($this->data as $data)
        {
            $this->datagrid->addItem($data);
        }
    }

    public function setRowColors(string $rowColorOdd, string $rowColorEven, string $fontRowColorOdd = null, string $fontRowColorEven = null, $borderColor = null)
    {
        $this->rowColorOdd = $rowColorOdd;
        $this->rowColorEven = $rowColorEven;
        $this->fontRowColorOdd = $fontRowColorOdd;
        $this->fontRowColorEven = $fontRowColorEven;
        $this->borderColor = $borderColor;
    }

    public function setTableHeaderColors(string $tableHeaderColor, string $tableHeaderFontColor = null)
    {
        $this->tableHeaderColor = $tableHeaderColor;
        $this->tableHeaderFontColor = $tableHeaderFontColor;
    }

    public function setTableFooterColors(string $tableFooterColor, string $tableFooterFontColor = null)
    {
        $this->tableFooterColor = $tableFooterColor;
        $this->tableFooterFontColor = $tableFooterFontColor;
    }

    private function showStyle()
    {
        if ($this->tableHeaderColor)
        {
            $style = new TStyle($this->name . ' thead');
            $style->background = $this->tableHeaderColor;

            if ($this->tableHeaderFontColor)
            {
                $style->color = $this->tableHeaderFontColor;
            }

            $style->show();
            
            $style = new TStyle($this->name . ' thead tr th');
            $style->{'border-color'} = $this->tableHeaderColor;
            $style->show();
        }

        if ($this->tableFooterColor)
        {
            $style = new TStyle($this->name . ' tfoot');
            $style->background = $this->tableFooterColor;

            if ($this->tableFooterFontColor)
            {
                $style->color = $this->tableFooterFontColor;
            }

            $style->show();
        }

        if ($this->rowColorOdd)
        {
            $style = new TStyle($this->name . ' table tbody tr:nth-of-type(odd)');
            $style->background = $this->rowColorOdd;

            if ($this->fontRowColorOdd)
            {
                $style->color = $this->fontRowColorOdd;
            }

            $style->show();
        }

        if ($this->rowColorEven)
        {
            $style = new TStyle($this->name . ' table tbody tr:nth-of-type(even)');
            $style->background = $this->rowColorEven;

            if ($this->fontRowColorEven)
            {
                $style->color = $this->fontRowColorEven;
            }

            $style->show();
        }

        if ($this->borderColor)
        {
            $style = new TStyle($this->name . ' table tbody tr td');
            $style->{"border-color-top"} = $this->borderColor;
            $style->show();
        }
    }

    /**
    * Show
    */
    public function show()
    {
        if (! $this->canDisplay())
        {
            return;
        }

        if (! $this->loaded)
        {
            $this->create();
        }

        $this->{'class'} = 'btablechart ' . $this->name;

        if (empty($this->data))
        {
            $panel = new TElement('div');
            $panel->{'class'} = 'panel panel-default';

            $panelHeader = new TElement('div');
            $panelHeader->{'class'} = 'panel-heading';
            $panelHeader->add("<div class='panel-title'>{$this->title}</div>");
            $panel->add($panelHeader);

            $panelBody = new TElement('div');
            $panelBody->{'class'} = 'panel-body';
            $panelBody->{'style'} = 'padding: 0px';
            $panelBody->add(_t('No records found'));
            $panel->add($panelBody);
        }
        else
        {
            $this->makeDatagrid();
            $this->setDataDatagrid();

            if ($this->showPanel)
            {
                $panel = new TElement('div');
                $panel->{'class'} = 'panel panel-default';

                $panelHeader = new TElement('div');
                $panelHeader->{'class'} = 'panel-heading';
                $panelHeader->add("<div class='panel-title'>{$this->title}</div>");
                $panel->add($panelHeader);

                $panelBody = new TElement('div');
                $panelBody->{'class'} = 'panel-body';
                $panelBody->{'style'} = 'padding: 0px';
                $panelBody->add($this->datagrid);
                $panel->add($panelBody);

                $this->add($panel);
            }
            else
            {
                $this->add($this->datagrid);
            }
        }

        $this->showStyle();

        parent::show();
    }
}