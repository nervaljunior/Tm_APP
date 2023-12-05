<?php

class ExtensionsInstall extends TPage
{
    /**
     * método construtor
     * Cria a página e o formulário de cadastro
     */
    function __construct()
    {
        parent::__construct();
    
        try 
        {
            $this->adianti_target_container = 'adianti_div_content';

            $this->form = new BootstrapFormBuilder('form-download-step-1');            
            $this->form->setFormTitle(_bt('Installing your application'));

            $tstep = new TStep();
            $tstep->addItem('<b>'._bt('PHP verification').'</b>', true, false);
            $tstep->addItem(_bt('Directory and files verification'), false, false);
            $tstep->addItem(_bt('Database configuration/creation'), false, false);
            
            $this->form->addContent([$tstep]);
            
            $separator = new TFormSeparator(_bt('PHP version verification and installed extensions'));
            $separator->setFontSize('24');
            $this->form->addContent([$separator]);
            
            $modulesCheckView = new SystemModulesCheckView([]);
            $modulesCheckView->setIsWrapped(true);
        
            $this->form->addContent([$modulesCheckView]);
        
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
        $form = new PathInstall();
        $form->setIsWrapped(true);
        $form->show();
    }

    public function onShow()
    {
        
    }
}