<?php

use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Database\TConnection;
use Adianti\Database\TCriteria;
use Adianti\Database\TFilter;
use Adianti\Database\TSqlSelect;
use Adianti\Database\TTransaction;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Base\TStyle;
use Adianti\Widget\Template\THtmlRenderer;
use Adianti\Widget\Util\TImage;

/**
 * Chart Widget
 *
 * @version    7.4
 * @package    widget
 * @subpackage builder
 * @author     Lucas Tomasi
 */
class BIndicator extends TElement
{
    private $html;
    private $value;
    private $name;
    protected $showMethods;

    /* data properties */
    protected $database;
    protected $model;
    protected $fieldValue;
    protected $joins;
    protected $total;

    protected $loaded;

    protected $criteria;

    /* layout properties */
    protected $layout;
    protected $alignLayout;
    protected $width;
    protected $height;
    protected $contentColor;

    /* title properties */
    protected $title;
    protected $titleSize;
    protected $titleColor;
    protected $titleDecoration;

    /* description properties */
    protected $description;
    protected $descriptionSize;
    protected $descriptionDecoration;

    protected $target;
    protected $targetColor;
    protected $transformerDescription;

    /* value properties */
    protected $valueColor;
    protected $valueDecoration;
    protected $valueSize;

    protected $transformerValue;

    /* icon properties */
    protected $icon;
    protected $backgroundIconColor;


    /**
     * Class Constructor
     * @param  $name         widget's name
     * @param  $database     database name
     * @param  $model        model name
     * @param  $fieldValue   field name
     * @param  $total        set type total (optional) default count [sum, max, min, count, avg]
     * @param  $joins        array with joins to be used on select
     * @param  $criteria     criteria (TCriteria object) to filter the model (optional)
     */
    public function __construct(String $name, $database = null, $model = null, $fieldValue = null, $total = 'count', TCriteria $criteria = null, array $joins = [])
    {
        parent::__construct('div');

        $this->name = $name;
        $this->showMethods = [];
        $this->loaded = false;
        $this->layout = 'horizontal';

        $this->setDatabase($database);
        $this->setModel($model);
        $this->setFieldValue($fieldValue);
        $this->setTotal($total);
        $this->setSize('100%', 90);

        $this->setColors('#007bff', '#333333', 'white', "#555555");

        $this->setCriteria($criteria??new TCriteria);
        $this->setJoins($joins);
    }

    /**
     * Define the style
     * @param  $decoration text decorations (b=bold, i=italic, u=underline)
     */
    private function setFontStyle($decoration)
    {
        $style = new TStyle('title_style');
        if (strpos(strtolower((string) $decoration), 'b') !== FALSE)
        {
            $style->{'font-weight'} = 'bold';
        }

        if (strpos(strtolower((string) $decoration), 'i') !== FALSE)
        {
            $style->{'font-style'} = 'italic';
        }

        if (strpos(strtolower((string) $decoration), 'u') !== FALSE)
        {
            $style->{'text-decoration'} = 'underline';
        }

        return $style;
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
     * Get name
     */
    public function getName()
    {
        return $this->name;
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
     * Set icon
     * @param $icon label indicator
     */
    public function setIcon(TImage $icon)
    {
        $this->icon = $icon;
    }

    /**
     * Set backgroundIcon color
     * @param $color color
     */
    public function setBackgroundIconColor(String $color)
    {
        $this->backgroundIconColor = $color;
    }

    /**
     * Set Title color
     * @param $color color
     */
    public function setTitleColor(String $color)
    {
        $this->titleColor = $color;
    }

    /**
     * Set Content color
     * @param $color color
     */
    public function setContentColor(String $color)
    {
        $this->contentColor = $color;
    }

    /**
     * Set Value color
     * @param $color color
     * @param $decoration text decorations (b=bold, i=italic, u=underline)
     */
    public function setValueColor(String $color, $decoration = null)
    {
        $this->valueColor = $color;

        if ($decoration)
        {
            $this->valueDecoration = $this->setFontStyle($decoration);
        }
    }

    /**
     * Set colors
     *
     * @param $backgroundIconColor String color
     * @param $titleColor String color
     * @param $contentColor String color
     * @param $valueColor String color
     */
    public function setColors(String $backgroundIconColor, String $titleColor, String $contentColor, String $valueColor)
    {
        $this->backgroundIconColor = $backgroundIconColor;
        $this->titleColor = $titleColor;
        $this->contentColor = $contentColor;
        $this->valueColor = $valueColor;
    }

    /**
     * Set transformer value
     * @param  $transformer callable
     */
    public function setTransformerValue(callable $transformer)
    {
        $this->transformerValue = $transformer;
    }

     /**
     * Set target and enable progressBar
     * @param  $target value target progressBar
     * @param  $targetColor color progressBar
     * @param  $transformerDescription callable
     * @param  $size string
     * @param  $decoration text decorations (b=bold, i=italic, u=underline)
     */
    public function setTarget($target, $targetColor, callable $transformerDescription = null, $size = '80%', $decoration = null)
    {
        $this->target = $target;
        $this->targetColor = $targetColor;
        $this->transformerDescription = $transformerDescription;

        $this->descriptionDecoration = $this->setFontStyle($decoration);
        $this->descriptionSize = (strstr($size, '%') !== FALSE) ? $size : "{$size}px";
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
     * Set joins
     * @param  $joins database joins
     */
    public function setJoins($joins)
    {
        $this->joins = $joins;
    }

    /**
     * Set total
     * @param  $total set type total (optional) default sum [sum, max, min, count, avg]
     */
    public function setTotal($total)
    {
        if (! in_array($total, ['sum', 'max', 'min', 'count', 'avg']))
        {
            throw new Exception(AdiantiCoreTranslator::translate('Invalid parameter (^1) in ^2', $total, __METHOD__));
        }

        $this->total = $total;
    }

    /**
     * Set layout
     * @param  $layout set layout indicator [horizontal|vertical]  default horizontal
     * @param  $align set align layout indicator [left|right|center]  default left
     */
    public function setLayout(string $layout, String $align = 'left')
    {
        if (! in_array($layout, ["horizontal", "vertical"]))
        {
            throw new Exception(AdiantiCoreTranslator::translate('Invalid parameter (^1) in ^2', $layout, __METHOD__));
        }

        if ($align && ! in_array($align, ["left", "right", "center"]))
        {
            throw new Exception(AdiantiCoreTranslator::translate('Invalid parameter (^1) in ^2', $align, __METHOD__));
        }

        if ($align)
        {
            $this->alignLayout = $align;
        }

        $this->layout = $layout;
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
     * @param $titleSize int title size
     * @param $color  decoration title
     * @param $decoration text decorations (b=bold, i=italic, u=underline)
     */
    public function setTitle($title, $color, $size = null, $decoration = null)
    {
        if ($decoration)
        {
            $this->titleDecoration = $this->setFontStyle($decoration);
        }

        $this->title = $title;
        $this->titleColor = $color;
        $this->titleSize = (strstr($size, '%') !== FALSE) ? $size : "{$size}px";
    }

    /**
     * Set description
     * @param $description String description
     * @param $decoration text decorations (b=bold, i=italic, u=underline)
     */
    public function setDescription($description, $descriptionSize = '80%', $decoration = null)
    {
        $this->description = $description;
        $this->descriptionSize = (strstr($descriptionSize, '%') !== FALSE) ? $descriptionSize : "{$descriptionSize}px";

        if ($decoration)
        {
            $this->descriptionDecoration = $this->setFontStyle($decoration);
        }
    }


    /**
     * Set size panel chart
     * @param $width  size width
     * @param $height size height
     */
    public function setSize($width, $height = null)
    {
        $this->width = (strstr($width, '%') !== FALSE) ? $width : "{$width}px";

        if ($height)
        {
            $this->height = (strstr($height, '%') !== FALSE) ? $height : "{$height}px";
        }
    }

    /**
     * Return sizes
     */
    public function getSize()
    {
        return null;
    }

    /**
     * Set value size
     *
     * @param $valuSize size of value
     */
    public function setValueSize($valuSize)
    {
        $this->valueSize = (strstr($valuSize, '%') !== FALSE) ? $valuSize : "{$valuSize}px";;
    }

    /**
     * Get data
     */
    private function loadData()
    {
        $this->value = 0;

        if (empty($this->database))
        {
            throw new Exception(AdiantiCoreTranslator::translate('The parameter (^1) of ^2 is required', 'database', __CLASS__));
        }

        if (empty($this->model))
        {
            throw new Exception(AdiantiCoreTranslator::translate('The parameter (^1) of ^2 is required', 'model', __CLASS__));
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
        $entities[] = $entity;

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

        // Not find dot, insert table name before
        if (strpos($this->fieldValue, '.') === FALSE && strpos($this->fieldValue, ':') === FALSE && strpos($this->fieldValue, '(') === FALSE)
        {
            $this->fieldValue = "{$entity}.{$this->fieldValue}";
        }
        $sql->addColumn("{$this->total}({$this->fieldValue})");
        $sql->setEntity($entities);
        $sql->setCriteria($this->criteria);

        $stmt = $conn->prepare($sql->getInstruction(TRUE), array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $result = $stmt->execute($this->criteria->getPreparedVars());

        if($result)
        {
            $this->value = $stmt->fetch(PDO::FETCH_NUM)[0];
        }

        // close connection
        if ($open_transaction)
        {
            TTransaction::close();
        }
    }

    /**
     * Get data
     */
    private function loadTemplate()
    {
        if (empty($this->layout))
        {
            throw new Exception(AdiantiCoreTranslator::translate('The parameter (^1) of ^2 is required', 'layout', __CLASS__));
        }

        $this->html = new THtmlRenderer(__DIR__.'/bindicator.html');
    }

    /**
     * Enable Description section
     */
    private function enableDescription()
    {
        $styles  = $this->descriptionDecoration ? $this->descriptionDecoration->getInline() : '';
        $styles .= "font-size: {$this->descriptionSize};color: {$this->titleColor}";

        if ($this->target)
        {
            $value = floor(($this->value / $this->target) * 100);
            $description = $this->transformerDescription ? call_user_func($this->transformerDescription, $value, $this->target, $this->value) : $this->description;

            $this->html->enableSection(
                $this->layout . '-progress',
                [
                    'targetColor' => $this->targetColor,
                    'value' => $value,
                    'styles' => $styles,
                    'description' => $description,
                ]
            );
        }
        elseif ($this->description)
        {
            $this->html->enableSection(
                $this->layout . '-description',
                [
                    'description' => $this->description,
                    'styles' => $styles
                ]
            );
        }
    }

    /**
     * Enable icon section
     */
    private function enableIcon()
    {
        if (! $this->icon)
        {
            return;
        }

        $this->html->enableSection(
            $this->layout . '-icon',
            [
                'height' => $this->height,
                'icon' => $this->icon,
                'backgroundIconColor' => $this->backgroundIconColor
            ]
        );
    }

    /**
     * Exec indicator before show
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
        if (! $this->canDisplay())
        {
            return;
        }

        $this->loadTemplate();

        if (! $this->loaded)
        {
            $this->create();
        }

        $transformerdValue = $this->value;
        if ($this->transformerValue)
        {
            $transformerdValue = call_user_func($this->transformerValue, $this->value);
        }

        $this->enableDescription();
        $this->enableIcon();

        $style  = $this->titleDecoration ? $this->titleDecoration->getInline() : '';
        $style .= "color: {$this->titleColor}; font-size: {$this->titleSize};";

        $styleValue  = $this->valueDecoration ? $this->valueDecoration->getInline() : '';
        $styleValue .= "color: {$this->valueColor}; font-size: {$this->valueSize} !important;";

        $this->html->enableSection(
            $this->layout,
            [
                'name' => $this->name,
                'marginContent' => $this->icon ? '' :  0,
                'alignLayout' => $this->alignLayout,
                'title' => $this->title,
                'style' => $style,
                'styleValue' => $styleValue,
                'data' =>  $transformerdValue,
                'height' => $this->height,
                'width' => $this->width,
                'contentColor' => $this->contentColor,
            ]
        );

        parent::add($this->html);
        parent::show();
    }
}