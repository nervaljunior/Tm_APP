<?php

use Adianti\Registry\TSession;

/**
 * BuilderConfigForm
 *
 * @version    1.0
 * @author     Matheus Agnes Dias
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */

class BuilderConfigForm extends TPage
{
    private $datagrid;
    
    public function __construct()
    {
        parent::__construct();
        
        BuilderPermissionService::checkPermission();
        
        parent::setTargetContainer('adianti_right_panel');
        
        // creates the form
        $this->form = new BootstrapFormBuilder('builderConfigForm');
        $this->form->setFormTitle('Arquivo de configuração');
        $this->form->setProperty('style', 'margin-bottom:0;box-shadow:none');
        
        $code = new TText('code');
        $code->setId('code');
        $code->style='display:none;';
        $code->setSize(1,1);
        
        $this->code = $code;
        
        $file_name = new TEntry('file_name');
        $file_name->setSize('100%');
        
        $this->monacoEditor = new THtmlRenderer('app/lib/include/builder/monaco.html');
        
        $this->form->addFields([new TLabel("Nome do arquivo:", null, '14px', null, '100%'), $file_name]);
        $this->form->addContent([$code]);
        $this->form->addContent([$this->monacoEditor]);
        
        $btnClose = new TButton('closeCurtain');
        $btnClose->class = 'btn btn-sm btn-default';
        $btnClose->style = 'margin-right:10px;';
        $btnClose->onClick = "Template.closeRightPanel();";
        $btnClose->setLabel(_bt('Close'));
        $btnClose->setImage('fas:times');
        $this->form->addHeaderWidget($btnClose);
        
        $btn_onsave = $this->form->addAction("Salvar", new TAction([$this, 'onSave']), 'far fas fas:save #ffffff');
        $btn_onsave->addStyleClass('btn-primary'); 
        
        parent::add($this->form);
        
        $style = new TStyle('right-panel > .container-part[page-name={$class}]');
        $style->width = '80% !important';   
        $style->show(true);
    }
    
    /**
     * Executed when the user clicks at the view button
     */
    public function onEdit($param)
    {
        if(!empty($param['file']))
        {
            $file = str_replace('../', '', $param['file']);
            $file = str_replace('/', '', $file);
            
            $language = 'php';
            if(substr($file, -4) == '.ini')
            {
                $language = 'ini';
            }
            
            $this->monacoEditor->enableSection('main', ['language'=> $language, 'value_selector'=> '#code', 'height'=> 'calc(100vh - 265px)']);
            
            $code = file_get_contents("app/config/{$file}");
            $this->code->setValue($code);
            $this->form->setData((object)[
                'file_name' => $param['file'],
                'code' => $code
            ]);    
        }
    }
    
    public static function onSave($param = null)
    {
        try
        {
            BuilderPermissionService::checkPermission();
            
            $file_name = $param['file_name'];
            $file_name = str_replace('../', '', $file_name);
            $file_name = str_replace('/', '', $file_name);
            
            if(!$file_name)
            {
                throw new Exception("O nome do arquivo é obrigatório");
            }
            
            file_put_contents("app/config/{$file_name}", $param['code']);
            
            new TMessage('info', 'Arquivo salvo', new TAction(['BuilderConfigList', 'onShow']), 'Sucesso!');
        }
        catch(Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
    
    public function onClose($param = null )
    {
            
    }
    
    public function onShow($param = null )
    {
        $this->monacoEditor->enableSection('main', ['language'=>'php', 'value_selector'=> '#code', 'height'=> 'calc(100vh - 265px)']);
    }
    /**
     * shows the page
     */
    public function show()
    {
        parent::show();
    }

    public static function setTheme($param)
    {
        try
        {
            BuilderService::setTheme(TSession::getValue('userid'), $param['theme']);
        }
        catch(Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
}