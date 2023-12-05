<?php

use Adianti\Widget\Base\TElement;
use Adianti\Widget\Base\TScript;

class BImageCarousel extends TElement
{
    private $id;
    private $sources;
    private $thumbs;
    private $width;
    private $height;
    private $widthThumb;
    private $heightThumb;
    private $customOptionsThumb;
    private $customOptions;

    public function __construct()
    {
        parent::__construct('section');
        $this->id = 'bimagecarousel_' . mt_rand(1000000000, 1999999999);
        $this->thumbs = FALSE;
        $this->sources = [];
        $this->width = '100%';
        $this->height = '100%';
        $this->widthThumb = 100;
        $this->heightThumb = 60;
    }

    public function setSize($width, $height)
    {
        $width = (strstr($width, '%') !== FALSE) ? $width : "{$width}px";
        $height = (strstr($height, '%') !== FALSE) ? $height : "{$height}px";

        $this->width = $width;
        $this->height = $height;
    }

    public function enableThumbs()
    {
        $this->thumbs = TRUE;
    }

    public function setSizeThumbs($width, $height)
    {
        $width = (strstr($width, '%') !== FALSE) ? $width : "{$width}px";
        $height = (strstr($height, '%') !== FALSE) ? $height : "{$height}px";

        $this->widthThumb = $width;
        $this->heightThumb = $height;
    }

    public function disableThumbs()
    {
        $this->thumbs = FALSE;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function addSource($source)
    {
        $this->sources[] = $source;
    }

    public function setSources($sources)
    {
        $this->sources = $sources;
    }

    public function getSources()
    {
        return $this->sources;
    }

    private function mount($id, $class = '')
    {
        $el = new TElement('section');
        $el->id = $id;
        $el->class = "splide bimagecarousel " . $class;

        $track = new TElement('div');
        $track->class = "splide__track";

        $list = new TElement('ul');
        $list->class = 'splide__list';

        $track->add($list);

        if ($this->sources)
        {
            foreach($this->sources as $source)
            {
                $item = new TElement("li");
                $item->class = "splide__slide";
                $item->add(TElement::tag('img', '', ['src' => $source]));
                
                $list->add($item);
            }
        }

        $el->add($track);
        
        return $el;
    }

    private function getSplide()
    {
        return $this->mount($this->id);
    }

    private function getThumbs()
    {
        return $this->mount($this->id.'thumb', 'thumb');
    }

    public function setCustomOptions($options)
    {
        $this->customOptions = $options;
    }

    public function setCustomOptionsThumb($options)
    {
        $this->customOptionsThumb = $options;
    }

    public function getOptions()
    {
        $options = [
            'type' => 'fade',
            'rewind' => true,
            'pagination' => ! $this->thumbs,
            'arrows'  => ! $this->thumbs,
            'width' => $this->width,
            'height' => $this->height,
        ];

        return array_merge($options, ($this->customOptions??[]));
    }

    public function getOptionsThumb()
    {
        $options = [
            'fixedWidth' => $this->widthThumb,
            'fixedHeight' => $this->heightThumb,
            'gap' => 10,
            'rewind' => true,
            'pagination' => false,
            'isNavigation' => true,
            'width' => $this->width,
            'breakpoints' => [
                '600' => ['fixedWidth' => 60, 'fixedHeight' => 44]
            ],
        ];

        return array_merge($options, ($this->customOptionsThumb??[]));
    }

    public function show()
    {
        $options = json_encode($this->getOptions());
        $optionsThumb = json_encode($this->getOptionsThumb());

        if ($this->thumbs)
        {
            $div = new TElement('div');
            $div->add($this->getSplide());
            $div->add($this->getThumbs());
            $div->setProperties($this->getProperties());
            $div->show();
            
            TScript::create("bimagecarousel_start('{$this->id}', {$options}, '{$this->id}thumb', {$optionsThumb})");
        }
        else
        {
            $splide = $this->getSplide();
            $splide->setProperties($this->getProperties());
            $splide->show();
            
            TScript::create("bimagecarousel_start('{$this->id}', {$options})");
        }
    }
}