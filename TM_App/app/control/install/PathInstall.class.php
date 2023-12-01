<?php

class PathInstall extends TPage
{
    private $datagrid;
    /**
     * método construtor
     * Cria a página e o formulário de cadastro
     */
    function __construct()
    {
        parent::__construct();
    
        try 
        {
            $phpversion = substr(phpversion(), 0, 1);
            $this->adianti_target_container = 'adianti_div_content';

            $this->form = new BootstrapFormBuilder('form-download-step-1');            
            $this->form->setFormTitle(_bt('Installing your application'));

            $paths = ['app/config/','app/control/','app/database/','app/model/','app/output/','tmp/'];
            $fields = [];
            
            $tstep = new TStep();
            $tstep->addItem(_bt('PHP verification'), true, true);
            $tstep->addItem('<b>'._bt('Directory and files verification').'</b>', true, false);
            $tstep->addItem(_bt('Database configuration/creation'), false, false);
            
            $this->form->addContent([$tstep]);
            
            $separator = new TFormSeparator(_bt('Directory and files verification'));
            $separator->setFontSize('24');
            $this->form->addContent([$separator]);
            
            $tablePath = new TTable();
            $tablePath->class = 'table';
            $tablePath->style = 'width:100%';
            
            $row = $tablePath->addRow();
            $row->addCell(_bt('Path'))->style='text-align:left;font-weight:bold';
            $row->addCell(_bt('Read'))->style='text-align:center;font-weight:bold';
            $row->addCell(_bt('Write'))->style='text-align:center;font-weight:bold';
            
            foreach ($paths as $path) 
            {
                $img_read = is_readable($path) ? 'check green' : 'times red';
                $img_write = is_writable($path) ? 'check green' : 'times red';
                
                $row = $tablePath->addRow();
                
                $row->addCell($path)->style = 'width:350px';
                $row->addCell("<i class='fas fa-{$img_read}' aria-hidden='true'></i>")->style = 'width:70px;text-align:center';
                $row->addCell("<i class='fas fa-{$img_write}' aria-hidden='true'></i>")->style = 'width:70px;text-align:center';
            }
            $this->form->addContent([$tablePath]);
            
            $files = ['app/config/permission.php','app/config/communication.php','app/config/log.php','app/config/install.ini','app/config/installed.ini'];
            
            $tableFiles = new TTable();
            $tableFiles->class = 'table';
            $tableFiles->style = 'width:100%';
            
            $row = $tableFiles->addRow();
            $row->addCell(_bt('File'))->style='text-align:left;font-weight:bold';
            $row->addCell(_bt('Read'))->style='text-align:center;font-weight:bold';
            $row->addCell(_bt('Write'))->style='text-align:center;font-weight:bold';
            
            foreach ($files as $file) 
            {
                $row = $tableFiles->addRow();
                
                $img_read = is_readable($file) ? 'check green' : 'times red';
                $img_write = is_writable($file) ? 'check green' : 'times red';
                
                $row->addCell($file)->style = 'width:350px';
                $row->addCell("<i class='fa fa-{$img_read}' aria-hidden='true'></i>")->style = 'width:70px;text-align:center;';
                $row->addCell("<i class='fa fa-{$img_write}' aria-hidden='true'></i>")->style = 'width:70px;text-align:center;';
            }
            $this->form->addContent([$tableFiles]);
        
            $this->form->setFields($fields);
            $this->form->addAction(_bt('Back'), new TAction([$this, 'lastStep']), 'fa:arrow-left red');
            $this->form->addAction(_bt('Next'), new TAction([$this, 'nextStep']), 'fa:arrow-right green');
            
            $container = new TElement('div');
            $container->class = 'container formBuilderContainer';
            
            $container->add($this->form);
            
            parent::add($container);
        } 
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());
        }
    }
    
    public static function nextStep($params = null)
    {
        $paths = ['app/config/','app/control/','app/database/','app/model/','app/output/','tmp/'];
        foreach ($paths as $path) 
        {
            if(!is_readable($path))
            {
                new TMessage('error', _bt("In order to continue with the installation you must grant read permission to the directory").":<b>{$path}</b>");
                return false;   
            }
            if(!is_writable($path))
            {
                new TMessage('error', _bt("In order to continue with the installation you must grant write permission to the directory").":<b>{$path}</b>");
                return false;   
            }            
        }
        
        $files = ['app/config/permission.php','app/config/communication.php','app/config/log.php','app/config/install.ini','app/config/installed.ini'];
        
        foreach ($files as $file) 
        {
            if(!is_readable($file))
            {
                new TMessage('error', _bt("In order to continue with the installation you must grant read permission to the file").":<b>{$file}</b>");
                return false;   
            }
            if(!is_writable($file))
            {
                new TMessage('error', _bt("In order to continue with the installation you must grant write permission to the file:")."<b>{$file}</b>");
                return false;   
            }
        }
        
        $form = new DatabaseInstall();
        $form->setIsWrapped(true);
        $form->show();
    }
    
    public static function lastStep($params = null)
    {
        $form = new ExtensionsInstall();
        $form->setIsWrapped(true);
        $form->show();
    }

    public function onShow()
    {
        
    }
    

}