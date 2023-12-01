<?php

use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Database\TTransaction;
use Adianti\Registry\TSession;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Container\TPanelGroup;
use Adianti\Widget\Container\TVBox;
use Adianti\Widget\Datagrid\TDataGrid;
use Adianti\Widget\Datagrid\TDataGridColumn;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Form\TButton;
use Adianti\Widget\Form\TCombo;
use Adianti\Widget\Form\TForm;
use Adianti\Wrapper\BootstrapDatagridWrapper;

/**
 * SystemFrameworkUpdate
 *
 * @version    1.0
 * @package    control
 * @subpackage admin
 * @author     Lucas Tomasi
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class SystemFrameworkUpdate extends TPage
{
    private static $formName = 'form_SystemFrameworkUpdate';
    private $datagrid_form;  // form listing
    private $loaded;

    /**
     * Class constructor
     * Creates the page, the form and the listing
     */
    public function __construct()
    {
        parent::__construct();
        
        BuilderPermissionService::checkPermission();

        // creates a Datagrid
        $this->datagrid_files = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid_files->disableHtmlConversion();
        $this->datagrid_files->style = 'width: 100%';
        $this->datagrid_files->setHeight(320);

        $this->datagrid_sqls = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid_sqls->disableHtmlConversion();
        $this->datagrid_sqls->style = 'width: 100%';
        $this->datagrid_sqls->setHeight(320);

        $this->datagrid_form = new TForm(self::$formName);
        $this->datagrid_form->onsubmit = 'return false';

        $column_file = new TDataGridColumn('file', _bt("File"), 'left');
        $column_status = new TDataGridColumn('message', _bt("Message"), 'left','65%');

        $column_sql = new TDataGridColumn('sql', "SQL", 'left', '40%');
        $column_database = new TDataGridColumn('database', _bt("Database"), 'left');
        $column_message = new TDataGridColumn('message', _bt("Message"), 'left','40%');
        
        $this->datagrid_files->addColumn($column_file);
        $this->datagrid_files->addColumn($column_status);

        $this->datagrid_sqls->addColumn($column_database);
        $this->datagrid_sqls->addColumn($column_sql);
        $this->datagrid_sqls->addColumn($column_message);
        
        $this->datagrid_files->createModel();
        $this->datagrid_sqls->createModel();

        $this->datagrid_form->setData( TSession::getValue(__CLASS__.'_filter_data') );
        $this->datagrid_form->add('<div class="card-header panel-heading" style=""><div class="card-title panel-title"><div style="width: 100%">'._bt('Structure').'</div></div></div>');
        $this->datagrid_form->add($this->datagrid_files);
        $this->datagrid_form->add('<div class="card-header panel-heading" style=""><div class="card-title panel-title"><div style="width: 100%">'._bt('Database').'</div></div></div>');
        $this->datagrid_form->add($this->datagrid_sqls);

        $button = new TButton('button_onupdate');
        $this->datagrid_form->addField($button);
        
        $button->setAction(new TAction([$this, 'onUpdate'], ['static' => 1]), 'Update');
        $button->setImage('fa:sync-alt');
        $button->addStyleClass('btn-success');

        $infos = new TElement('div');
        $infos->style = 'padding: 10px;';
        $infos->add('<h5>' . _bt('Some important information') . '!</h5>');
        $infos->add('<div>' . _bt('A backup will be performed in your project <b>tmp</b> directory, containing the folders and files replaced during the process') . '</div>');
        $infos->add('<div>' .  _bt('See the complete changelog') . ': <br> <a target="_blank" href="https://manager.adiantibuilder.com.br/changelogs">Adianti Builder</a> <br> <a target="_blank" href="https://www.adianti.com.br/framework-changelog">Adianti Framework</a></div>');
        
        $panel = new TPanelGroup('System Framework Update');
        $panel->add($infos);
        $panel->add($this->datagrid_form);
        $panel->addFooter($button);

        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add($panel);

        parent::add($container);
    }
    
    public static function onUpdate($param)
    {
        try
        {
            BuilderPermissionService::checkPermission();

            $infos = BuilderService::getInstructionsUpdateLib();

            $msg = '';

            if (! empty($infos['files']))
            {
                $folder = BuilderService::downloadFilesUploadLib();
                
                $folder_name = 'tmp/bkp_update_lib_' . date('Y_m_d_H_i_s');

                mkdir($folder_name);

                if (! is_writable($folder_name))
                {
                    throw new Exception( (string) _bt('Permission denied, could not create the backup folder'));
                }

                foreach($infos['files'] as $file => $fileInfo)
                {
                    if (! file_exists("{$folder}{$file}"))
                    {
                        throw new Exception('File not found: ' . $file);
                    }

                    if (file_exists($file) && !is_writable($file))
                    {
                        throw new Exception('Do not have permission on: ' . "{$file}");
                    }

                    if ($fileInfo['type'] == 'folder')
                    {
                        exec("cp --parents -rf {$file} {$folder_name}");
                        exec("rm -rf {$file}/");
                        exec("cp -rf {$folder}{$file}/ {$file}/");
                    }
                    else
                    {
                        exec("cp --parents {$file} {$folder_name}");
                        exec("cp {$folder}{$file} {$file}");
                    }
                }

                exec("rm -r {$folder}");
                
                $msg .= '-' . _bt('Files changed') . '<br/>';
                $msg .= '-'. _bt('Backup created') . ': ' . $folder_name . '<br/>';
            }
            
            if (! empty($infos['sqls']))
            {
                $msg .= '-' . _bt('SQL changes') . '<br/>';
                foreach($infos['sqls'] as $key => $sql)
                {
                    try
                    {
                        $msg .= $sql['message'] . ': <b>';
                        TTransaction::open($param['databases'][$key]??$sql['schema']);
                        
                        // If insert permission validate controller already exists
                        if ($sql['type'] == 'INSERT_PERMISSION' && ! empty($sql['controller']))
                        {
                            $count = SystemProgram::where('controller', '=', $sql['controller'])->count();

                            if ($count > 0)
                            {
                                $msg .= 'Program: ' . $sql['controller'] . ' already exists';
                                $msg .=  '</b><br/>';
                                TTransaction::close();
                                continue;
                            }
                        }

                        $type = TTransaction::getDatabaseInfo()['type'];
                        $commandSQL = $sql['command'][$type]??null;
                        $conn = TTransaction::get();

                        if (is_array($commandSQL))
                        {
                            foreach($commandSQL as $cSql)
                            {
                                $conn->query($cSql);
                            }
                        }
                        else
                        {
                            $conn->query($commandSQL);
                        }

                        $msg .= _bt('Success');
                        
                        TTransaction::close();
                    }
                    catch(Exception $e)
                    {
                        TTransaction::rollback();
                        $msg .= $e->getMessage();
                    }
                    finally
                    {
                        $msg .=  '</b><br/>';
                    }
                }
            }

            ob_start();
            BuilderMenuUpdate::onUpdateMenu([]);
            ob_end_clean();

            new TMessage('info', $msg , new TAction(['LoginForm', 'reloadPermissions'], ['static' => 1]), _bt('Success'));
        }
        catch(Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }

    /**
     * Load the datagrid with data
     */
    public function onReload()
    {
        try
        {
            $diffs = BuilderService::getInstructionsUpdateLib();

            $files = $diffs['files'];
            $sqls = $diffs['sqls'];

            if($filters = TSession::getValue(__CLASS__.'_filters'))
            {
                foreach ($filters as $key => $filter) 
                {
                    if($key == 'file')
                    {
                        $files = array_filter(
                            $files,
                            function($file) use ($filter) {
                                return strpos( strtolower($file['file']??''), strtolower($filter) ) !== FALSE;
                            }
                        );
                    }
                    
                    if($key == 'message')
                    {
                        $files = array_filter(
                            $files,
                            function($file) use ($filter) {
                                return $file['message'] == $filter;
                            }
                        );
                    }
                }
            }

            $this->datagrid_files->clear();

            if ($files)
            {
                foreach ($files as $key => $file)
                {
                    $object = new stdClass;
                    $object->message = $file['message'];
                    $object->file   = $file['file'];

                    $this->datagrid_files->addItem($object);
                }
            }

            $this->datagrid_sqls->clear();

            if ($sqls)
            {
                $databases = $this->getDatabases();

                foreach ($sqls as $key => $sql)
                {
                    $sqlCommand = $sql['command']['sqlite']??'';
                    $sqlCommand = is_array($sqlCommand) ? implode('<br/><br/>', $sqlCommand) : $sqlCommand;

                    $object = new stdClass;
                    $object->sql = $sqlCommand;
                    $object->message = $sql['message'];

                    $combo = new TCombo('databases[]');
                    $combo->addItems($databases);
                    $combo->setValue($sql['schema']??'');
                    $combo->setDefaultOption(false);
                    $combo->setSize('100%');

                    $object->database = $combo;

                    $this->datagrid_form->addField($combo);
                    $this->datagrid_sqls->addItem($object);
                }
            }

            $this->loaded = true;
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }

    private function getDatabases()
    {
        foreach (new DirectoryIterator('app/config') as $file)
        {
            $connection = str_replace(['.ini','.php'], ['',''], $file->getFilename());

            if (in_array($connection, ['application', 'install', 'installed', 'framework_hashes']))
            {
                continue;
            }

            if ($file->isFile() && in_array($file->getExtension(), ['ini', 'php']))
            {
                $list[ $connection ] = $connection;
            }
        }

        natcasesort($list);

        return $list;
    }

    /**
     * method show()
     * Shows the page
     */
    public function show()
    {
        if (!$this->loaded)
        {
            $this->onReload();
        }

        parent::show();
    }
}