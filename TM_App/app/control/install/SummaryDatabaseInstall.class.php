<?php

class SummaryDatabaseInstall extends TPage
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

            $fields = [];
            
            $tstep = new TStep();
            $tstep->addItem(_bt('PHP verification'), true, true);
            $tstep->addItem(_bt('Directory and files verification').'</b>', true, true);
            $tstep->addItem('<b>'._bt('Database configuration/creation').'</b>', true, true);
            
            $this->form->addContent([$tstep]);
            
            $separator = new TFormSeparator(_bt('Summary database install'));
            $separator->setFontSize('24');
            $this->form->addContent([$separator]);
            
            $table = new TTable();
            $table->class = 'table';
            $table->style = 'width:100%';
            
            foreach (DatabaseInstall::$msgs as $database => $msgs) 
            {
                $table->addRowSet($database)->style='font-weight:bold; color:white; background-color:#29688c';
                foreach ($msgs as $msg)
                {
                    $table->addRowSet($msg);
                }
            }
            $this->form->addContent([$table]);
            
            $this->form->setFields($fields);
            
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

    public function onShow()
    {
        
    }
    

}