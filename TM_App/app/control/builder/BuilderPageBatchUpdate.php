<?php

use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Wrapper\BootstrapFormBuilder;

/**
 * BuilderPageBatchUpdate
 *
 * @version    2.0
 * @package    control
 * @subpackage admin
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class BuilderPageBatchUpdate extends TPage
{
    private $form;
    private $datagrid;
    
    /**
     * Constructor method
     */
    public function __construct()
    {
        try
        {
            parent::__construct();
            
            BuilderPermissionService::checkPermission();
            
            $this->form = new BootstrapFormBuilder('builder_page_batch_update_form');
            $this->form->setFormTitle('Atualizar códigos');

            // creates one datagrid
            $this->datagrid = new BootstrapDatagridWrapper(new TQuickGrid);
            $this->datagrid->disableDefaultClick();
            $this->datagrid->width = '100%';

            $this->datagridConflicts = new BootstrapDatagridWrapper(new TQuickGrid);
            $this->datagridConflicts->disableDefaultClick();
            $this->datagridConflicts->width = '100%';
            
            $this->datagridConflicts->addQuickColumn(_bt('Resolve'), 'check',     'center', 70);
            $this->datagridConflicts->addQuickColumn(_bt('Type'),     'type',      'left');
            $this->datagridConflicts->addQuickColumn(_bt('Path'),     'file_path', 'left');
            $this->datagridConflicts->addQuickColumn(_bt('File'),     'file_name', 'left');
            $column = $this->datagridConflicts->addQuickColumn(_bt('Message'),  'type',   'left');
            $column->setTransformer( function() {
                $div = new TElement('span');
                $div->class="label label-info";
                $div->style="text-shadow:none; font-size:12px";
                $div->add(_bt('Conflict'));
                return $div;
            });

            // add the columns
            $this->datagrid->addQuickColumn(_bt('Download'), 'check',     'center', 70);
            $this->datagrid->addQuickColumn(_bt('Type'),     'type',      'left');
            $this->datagrid->addQuickColumn(_bt('Path'),     'file_path', 'left');
            $this->datagrid->addQuickColumn(_bt('File'),     'file_name', 'left');
            $column = $this->datagrid->addQuickColumn(_bt('Message'),  'type',   'left');
            
            $column->setTransformer( function($value, $object, $row) {
                $div = new TElement('span');
                $div->class="label label-" . (($object->change_type == 1) ? 'success' : 'warning');
                $div->style="text-shadow:none; font-size:12px";
                $div->add((($object->change_type == 1) ? _bt('New') : _bt('Changed')));
                return $div;
            });
            
            $action1 = new TDataGridAction(array($this, 'onView'));
            $action1->setLabel(_bt('View'));
            $action1->setUseButton(true);
            $action1->setImage('fa:search blue');
            $action1->setFields(['id']);
            $this->datagrid->addAction($action1);


            $action1 = new TDataGridAction(array($this, 'onViewConflict'));
            $action1->setLabel(_bt('View'));
            $action1->setUseButton(true);
            $action1->setImage('fa:search blue');
            $action1->setFields(['id', 'path']);
            $this->datagridConflicts->addAction($action1);
            
            // creates the datagrid model
            $this->datagrid->createModel();
            $this->datagridConflicts->createModel();
        
        
            // connection info
            $db = ['name' => 'app/database/.cache.db', 'type' => 'sqlite'];
            TTransaction::open(NULL, $db); // open transaction 
            $conn = TTransaction::get(); // get PDO connection 
             
            TDatabase::dropTable($conn, 'builder_codes', true);
            TDatabase::createTable($conn, 'builder_codes', ['id' => 'int', 'type' => 'text', 'file_path' => 'text', 'file_name' => 'text', 'content' => 'text']);
            
            $pages = BuilderPageService::getCodes();
            
            $projectFiles = self::listAllFiles();
            $haveConflicts = false;

            $types = ['models', 'codes', 'template_codes', 'pages'];
            foreach ($types as $type)
            {
                if (!empty($pages->$type))
                {
                    foreach ($pages->$type as $page)
                    {
                        $full_path = $page->file_path . '/' . $page->file_name;
                        $full_path = (substr($full_path, 0,1) == '/') ? substr($full_path,1) : $full_path;
                        
                        $id = mt_rand(1000000000, 1999999999);
                        TDatabase::insertData($conn, 'builder_codes', [ 'id' => $id,
                                                                        'type'      => $page->type,
                                                                        'file_path' => $page->file_path,
                                                                        'file_name' => $page->file_name,
                                                                        'content'   => $page->content ]);
                        
                        if (!file_exists($full_path) OR base64_decode($page->content) !== file_get_contents( $full_path ))
                        {
                            $page->id = $id;
                            // add an regular object to the datagrid
                            $page->check = new TCheckButton('check_'.$id);
                            $page->check->setIndexValue('on');
                            
                            $page->change_type = 2;
                            
                            if (!file_exists($full_path))
                            {
                                $page->change_type = 1;
                                $page->check->setValue('on');
                            }
                            
                            $this->form->addField($page->check); // important!
                            $this->datagrid->addItem((object) $page);
                        }

                        $file_name = $page->file_name;

                        $files = array_filter($projectFiles, function($file) use ($file_name ) { return $file_name == $file; });

                        if ( count( $files ) > 1  )
                        {
                            $haveConflicts = true;
                            $page->id = $file_name;
                            $page->path = $full_path;

                            // add an regular object to the datagrid
                            $page->check = new TCheckButton('conflicts[]');
                            $page->check->setIndexValue($full_path);
                            
                            $page->change_type = 'conflict';
                            $page->check->setValue($full_path);
                            
                            $this->form->addField($page->check); // important!
                            $this->datagridConflicts->addItem((object) $page);
                        }
                    }
                }
            }
            
            TTransaction::close(); // close transaction
        
            $panel = new TPanelGroup();
            $panelConflicts = new TPanelGroup();
            
            $div = new TElement('div');
            $div->class = 'fb-inline-field-container';
            $div->style = 'display: inherit;vertical-align:top;';
            
            $btnUnCheckAll = new TButton('unCheckAll');
            $btnUnCheckAll->class = 'btn btn-default btn-sm pull-left';
            $btnUnCheckAll->style = 'margin-right:10px;';
            $btnUnCheckAll->onClick = "Builder.checkAllCheckboxes('builder_page_batch_update_form', 'uncheck');";
            $btnUnCheckAll->type = 'button';
            $btnUnCheckAll->setLabel('Desmarcar todos');
            $btnUnCheckAll->setImage('far:square');
            
            $btnCheckAll = new TButton('checkAll');
            $btnCheckAll->class = 'btn btn-default btn-sm pull-left';
            $btnCheckAll->style = 'margin-right:10px;';
            $btnCheckAll->onClick = "Builder.checkAllCheckboxes('builder_page_batch_update_form', 'check');";
            $btnCheckAll->type = 'button';
            $btnCheckAll->setLabel('Marcar todos');
            $btnCheckAll->setImage('far:check-square');
            
            $btnInvert = new TButton('invert');
            $btnInvert->class = 'btn btn-default btn-sm pull-left';
            $btnInvert->style = 'margin-right:10px;';
            $btnInvert->onClick = "Builder.checkAllCheckboxes('builder_page_batch_update_form', 'invert');";
            $btnInvert->type = 'button';
            $btnInvert->setLabel('Inverter');
            $btnInvert->setImage('fas:retweet');

            $div->add($btnUnCheckAll);
            $div->add($btnCheckAll);
            $div->add($btnInvert);
            
            $panel->add($div);
            $panelConflicts->add($div);
            
            $panel->add($this->datagrid);
            $panelConflicts->add($this->datagridConflicts);
            
            $button = TButton::create('action1', [$this, 'onSave'], _bt('Save'), 'fa:save green');
            $this->form->addField($button);
            $panel->addFooter($button);

            $button = TButton::create('action2', [$this, 'onResolve'], _bt('Resolve conflict'), 'fa:save green');
            $this->form->addField($button);
            $panelConflicts->addFooter($button);
            
            $this->form->appendPage(_bt('Page Batch update'));
            if ($haveConflicts)
            {
                $this->form->addContent(["<div class='alert alert-warning'>". _bt('Your project contains conflicting files. check the tab') ." <b>". _bt('File conflicts') ."</b></div>"]);
            }
            $this->form->addContent([$panel]);
            $this->form->appendPage(_bt('File conflicts'));
            $this->form->addContent([$panelConflicts]);
            
            parent::add($this->form);
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }

    public static function onResolve($param)
    {
        try
        {
            BuilderPermissionService::checkPermission();
            // check_conflicts
            if (empty($param['conflicts']))
            {
                new TMessage("info", _bt('File without conflicts'));
                return;
            }

            $projectFiles = self::listAllFiles();

            
            $messages = [];
            foreach ($param['conflicts'] as $file)
            {
                $file_name = basename($file);
                $file_path = $file;

                $files = array_filter(
                    $projectFiles,
                    function($file, $path) use ($file_name, $file_path) {
                        $file_path = str_replace('//', '/', $file_path);
    
                        return ($file_path != $path && $file_name == $file);
                    },
                    ARRAY_FILTER_USE_BOTH
                );

                foreach (array_keys($files??[]) as $fileRemove)
                {
                    $xx = unlink($fileRemove);
                 
                    if ($xx)
                    {
                        $messages[] = '<font color=green>'._bt('File removed').'</font>: ' . $fileRemove;
                    }
                    else
                    {
                        $messages[] = '<font color=red>'. _bt('Permission denied') . ', '. _bt('File not removed') . '</font>: ' . $fileRemove ;
                    }
                }
            }

            new TMessage('info', _bt('Success'). '!<br/>' . implode('<br/>', $messages));

        }
        catch(Exception $e)
        {
            new TMessage('info', $e->getMessage());
        }
    }

    public static function onViewConflict($param)
    {
        try
        {
            BuilderPermissionService::checkPermission();

            $projectFiles = self::listAllFiles();
            $file_name = $param['key'];
            $file_path = $param['path'];
            
            $files = array_filter(
                $projectFiles,
                function($file, $path) use ($file_name, $file_path) {
                    $file_path = str_replace('//', '/', $file_path);

                    return ($file_path != $path && $file_name == $file);
                },
                ARRAY_FILTER_USE_BOTH
            );

            if ( count( $files ) > 0  )
            {
                $comp = count( $files ) > 1 ? 's' : '';
                $files = implode('<br>', array_map(function($file) { return "<li>{$file}</li>"; }, array_keys($files)));
                new TMessage(
                    "info",
                    _bt(
                        "File <b>^1</b> is in more than one location:<br/><br/>^2<br/>If you wanted to resolve conflicts, the above file{$comp} will be <font color=red> removed </font> and this action cannot be undone!",
                        $file_name,
                        $files
                    )
                );
            }
            else
            {
                new TMessage("info", _bt('File without conflicts'));
            }
        }
        catch(Exception $e)
        {
            new TMessage('info', $e->getMessage());
        }
    }
    
    /**
     * View diffs
     */
    public static function onView($param)
    {
        try
        {
            BuilderPermissionService::checkPermission();
            
            $db = ['name' => 'app/database/.cache.db', 'type' => 'sqlite'];
            TTransaction::open(NULL, $db); // open transaction 
            $conn = TTransaction::get(); // get PDO connection
            
            $query = "SELECT *
                        FROM builder_codes
                       WHERE id = ?";
            
            $data = TDatabase::getData($conn, $query, null, [ $param['id'] ])[0];
            
            $full_path = $data['file_path'] . '/' . $data['file_name'];
            $full_path = (substr($full_path, 0,1) == '/') ? substr($full_path,1) : $full_path;
            
            $exType = explode('.', $data['file_name']);
            $type = end($exType);
            
            if($type == 'js')
            {
                $type = 'javascript';
            }
            
            $file_type = new TEntry('file_type');
            $file_type->setId('file_type');
            $file_type->setValue($type);
            $file_type->style = 'display:none';
            
            $code1 = file_exists($full_path) ? file_get_contents($full_path) : '';
            $code2 = base64_decode( $data['content'] );
            
            $source1 = new TText('source1');
            $source1->style = 'display:none';
            $source1->setId('source1');
            $source1->setValue($code1);
            
            $source2 = new TText('source1');
            $source2->style = 'display:none';
            $source2->setId('source2');
            $source2->setValue($code2);

            $iframe = new TElement('iframe');
            $uniqid = uniqid();
            $iframe->src = 'app/lib/include/builder/diff.html?rndval='.$uniqid;
            $iframe->style = 'width:100%; height: 100%;border: 0px;';
                
            $win = TWindow::create("Diff {$full_path}", 0.9, 0.9);
            
            $win->add($iframe);
            $win->add($source1);
            $win->add($source2);
            $win->add($file_type);
            
            $win->show();
            
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('info', $e->getMessage());
        }
    }
    
    /**
     * Save files
     */
    public static function onSave($param)
    {
        BuilderPermissionService::checkPermission();
        
        if ($param)
        {
            $datagrid = new BootstrapDatagridWrapper(new TQuickGrid);
            $datagrid->width = '100%';
            
            $datagrid->addQuickColumn(_bt('Type'),     'type',      'left');
            $datagrid->addQuickColumn(_bt('Path'),     'file_path', 'left');
            $datagrid->addQuickColumn(_bt('File'),     'file_name', 'left');
            $column = $datagrid->addQuickColumn(_bt('Message'),  'message',   'left');
            
            $column->setTransformer( function($value, $object, $row) {
                $div = new TElement('span');
                $div->class="label label-" . (($object->status==1) ? 'success' : 'danger');
                $div->style="text-shadow:none; font-size:12px";
                $div->add($value);
                return $div;
            });
            
            $datagrid->createModel();
            
            try
            {
                $db = ['name' => 'app/database/.cache.db', 'type' => 'sqlite'];
                TTransaction::open(NULL, $db); // open transaction 
                $conn = TTransaction::get(); // get PDO connection
                
                foreach ($param as $variable => $value)
                {
                    if (substr($variable,0,5) == 'check')
                    {
                        $parts = explode('_', $variable);
                        $id    = $parts[1];
                        
                        $query = "SELECT *
                                    FROM builder_codes
                                   WHERE id = ?";
                        
                        $data = TDatabase::getData($conn, $query, null, [ $id ])[0];
                        
                        $full_path = $data['file_name'];
                        
                        if($data['file_path'])
                        {
                            $full_path = $data['file_path'] . '/' . $data['file_name'];
                        
                            if (!file_exists($data['file_path']))
                            {
                                mkdir($data['file_path'], 0777, true);
                            }
                        }
                        
                        if ( (file_exists($full_path) AND is_writable($full_path)) OR (!file_exists($full_path) AND is_writable($data['file_path'])) OR (!$data['file_path']))
                        {
                            file_put_contents($full_path, base64_decode($data['content']));
                            
                            $data['status']  = 1;
                            $data['message'] = _bt('Success');
                            $datagrid->addItem( (object) $data);
                        }
                        else
                        {
                            $data['status']  = 2;
                            $data['message'] = _bt('Permission denied');
                            $datagrid->addItem( (object) $data);
                        }
                    }
                }
                
                TTransaction::close();
                
                $win = TWindow::create('Result', 1000, 800);
                $win->add($datagrid);
                $win->show();
            }
            catch (Exception $e)
            {
                new TMessage('info', $e->getMessage());
            }
        }
    }

    public static function listAllFiles()
    {
        $filesControl = self::getFiles('app/control', '*');
        $filesModel = self::getFiles('app/model', '*');
        $filesService = self::getFiles('app/service', '*');

        $filesTmp = array_merge($filesControl, $filesModel, $filesService);

        $files = [];

        foreach ($filesTmp as $file)
        {
            $files[$file] = basename($file);
        }

        return $files;
    }

    private static function getFiles($base, $pattern, $flags = 0)
    {
        $flags = $flags & ~GLOB_NOCHECK;
        
        if (substr($base, -1) !== DIRECTORY_SEPARATOR) {
            $base .= DIRECTORY_SEPARATOR;
        }
    
        $files = glob($base.$pattern, $flags);
        if (!is_array($files)) {
            $files = [];
        }
    
        $dirs = glob($base.'*', GLOB_ONLYDIR|GLOB_NOSORT|GLOB_MARK);
        if (!is_array($dirs)) {
            return $files;
        }
        
        foreach ($dirs as $dir) {
            $dirFiles = self::getFiles($dir, $pattern, $flags);
            $files = array_merge($files, $dirFiles);
        }
    
        return $files;
    }
}
