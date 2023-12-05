<?php

/**
 * BuilderConfigList
 *
 * @version    1.0
 * @author     Matheus Agnes Dias
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */

class BuilderConfigList extends TPage
{
    private $datagrid;
    
    public function __construct()
    {
        parent::__construct();
        
        BuilderPermissionService::checkPermission();
        
        // creates one datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->width = '100%';
        
        $file = $this->datagrid->addColumn( new TDataGridColumn('file', _bt('File'), 'left') );
        
        $action = new TDataGridAction(['BuilderConfigForm', 'onEdit'],   ['file'=>'{file}'] );
        $this->datagrid->addAction($action, _bt('Edit'), 'far:edit blue');
        
        // creates the datagrid model
        $this->datagrid->createModel();
        
        $files = scandir('app/config');
        
        foreach ($files as $file)
        {
            if ( (substr($file, -4) == '.ini') || (substr($file, -4) == '.php') )
            {
                $this->datagrid->addItem( (object) ['file'=>$file] );
            }
        }
        
        $panel = new TPanelGroup('Arquivos de configuração');
        $panel->addHeaderActionLink(_bt('New'), new TAction(['BuilderConfigForm', 'onShow'], ['register_state'=>'false']), 'fas:plus green');
        $panel->add($this->datagrid);
        
        
        // wrap the page content using vertical box
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add($panel);
        parent::add($vbox);
    }
    
    public function onShow($param = null )
    {
            
    }
    /**
     * shows the page
     */
    public function show()
    {
        parent::show();
    }
}