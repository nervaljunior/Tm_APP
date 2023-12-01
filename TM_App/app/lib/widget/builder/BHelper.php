<?php

use Adianti\Control\TAction;
use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Util\TImage;

class BHelper extends TElement
{
    private $id;
    private $title;
    private $icon;
    private $action;
    private $size;
    private $hover;
    private $side;
    private $content;

    /**
     * Class Constructor
     * @param  string $title        widget's title
     * @param  TImage $icon         widget's icon
     */
    public function __construct(TImage $icon = null)
    {
        parent::__construct('div');
        
        if (empty($icon))
        {
            $icon = new TImage('far:question-circle');
        }

        $this->icon = $icon;
        $this->id = 'bhelper_'.rand();
        $this->side = 'auto';
    }

    /**
     * Define side popover
     *
     * @param string $side ['auto', 'top', 'right', 'bottom', 'left']
     */
    public function setSide($side)
    {
        if (! in_array($side, ['auto', 'top', 'right', 'bottom', 'left']))
        {
            throw new Exception(AdiantiCoreTranslator::translate('Invalid parameter (^1) in ^2', $side, __METHOD__));
        }

        $this->side = $side;
    }

    /**
     * Set content popover
     * 
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }
    
    /**
     * Get content popover
     * @return string $content
     */
    public function getContent()
    {
        return $this->content;
    }
    
    /**
     * Get size
     * @return int $size
     */
    public function getSize()
    {
        return null;
    }

    /**
     * Enable hover
     * @return bool $hover
     */
    public function enableHover($hover = true)
    {
        $this->hover = $hover;
    }

    /**
     * Set size
     * @return int $size
     */
    public function setSize(int $size)
    {
        $this->size = $size;
    }

    /**
     * Get action
     * @return TAction $action
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set click action
     * @return TAction $action
     */
    public function setAction(TAction $action)
    {
        $this->action = $action;
    }

    /**
     * Get title
     * @return string $title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Get icon
     * @return TImage $icon
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * Set title component
     * @param string $title title shown on hover component
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     * Set icon component
     * @param TImage $icon icon shown on component
     */
    public function setIcon(TImage $icon)
    {
        $this->icon = $icon;
    }

    /**
     * Show component on screen
     */
    public function show()
    {
        if ($this->getProperties())
        {
            foreach ($this->getProperties() as $property => $value)
            {
                $this->icon->{"{$property}"} = $value;
            }
        }
        
        if ($this->action && $this->hover)
        {
            $this->icon->style .= '; cursor: pointer';
            $this->icon->generator = 'adianti';
            $this->icon->href = $this->action->serialize();
        }
        else if ($this->action)
        {
            $action = new TElement('span');
            $action->onclick = "$('#{$this->id}').popover('hide')";
            $action->style = 'cursor: pointer';
            $action->generator = 'adianti';
            $action->href = $this->action->serialize();

            if ($this->title)
            {
                $actionTitle = clone $action;
                $actionTitle->add($this->title);
                $this->title = $actionTitle;
            }

            if ($this->content)
            {
                $actionContent = clone $action;
                $actionContent->add($this->content);
                $this->content = $actionContent;
            }
        }
        
        if ($this->size)
        {
            $this->icon->style .= "; font-size: {$this->size}px !important; text-align: center;";
        }
        
        $this->icon->{'id'} = $this->id;
        $this->icon->{'data-popover'} ="true";
        $this->icon->{'poptitle'} = htmlspecialchars(str_replace("\n", '', nl2br($this->title)));
        $this->icon->{'popcontent'} = htmlspecialchars(str_replace("\n", '', nl2br($this->content)));
        $this->icon->{'popside'} = $this->side;

        if (! $this->hover)
        {
            $this->icon->{'poptrigger'} = "click";
            $this->icon->style .= '; cursor: pointer';
        }

        $this->icon->show();
    }
}