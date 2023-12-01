<?php

/**
 * BuilderPermissionUpdate
 *
 * @version    1.0
 * @package    control
 * @subpackage admin
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class BuilderPermissionUpdate extends TPage
{
    /**
     * Constructor method
     */
    public function __construct()
    {
        parent::__construct();
        
        BuilderPermissionService::checkPermission();
    }
    
    /**
     * Ask for Update menu
     */
    public function onAskUpdate()
    {
        try
        {
            BuilderPermissionService::checkPermission();
            
            if (!file_exists('menu-dist.xml'))
            {
                throw new Exception(_bt('File not found') . ':<br> menu-dist.xml');
            }
            
            $action = new TAction(array($this, 'onUpdatePermissions'));
            new TQuestion(_bt('Update permissions?'), $action);
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
    
    /**
     * Update menu
     */
    public static function onUpdatePermissions($param)
    {
        try
        {
            BuilderPermissionService::checkPermission();
            
            $permissions = BuilderPageService::getPermissions();
            
            if ($permissions)
            {
                $systemGroup = new SystemGroup();
                $hasUuid = false;
                if(in_array('uuid', $systemGroup->getAttributes()))
                {
                    $hasUuid = true;
                }

                TTransaction::open('permission');
                
                foreach ($permissions as $group_uuid => $groupInfo)
                {
                    if($hasUuid)
                    {
                        $system_group = SystemGroup::where('uuid', '=', $group_uuid)->first();
                        if(!$system_group)
                        {
                            $system_group = SystemGroup::where('name', '=', $groupInfo->name)->first()??new SystemGroup;
                            $system_group->name = $groupInfo->name;
                            $system_group->uuid = $group_uuid;
                            $system_group->store();
                        }
                    }
                    else
                    {
                        $system_group = SystemGroup::where('name', '=', $groupInfo->name)->first();
                    }
                    
                    if (empty($system_group))
                    {
                        $system_group = new SystemGroup;
                    }

                    $system_group->name = $groupInfo->name;
                    
                    if($hasUuid)
                    {
                        $system_group->uuid = $group_uuid;
                    }

                    $system_group->store();

                    if (!empty($groupInfo->builder_permission_group)) 
                    {
                        SystemGroupProgram::where('system_group_id', '=', $system_group->id)->delete();
                    }
                    
                    $programs = $groupInfo->programs;

                    if ($programs)
                    {
                        $databasePrograms =  SystemProgram::getIndexedArray('controller', 'id');

                        foreach ($programs as $controller => $name)
                        {
                            if (empty($databasePrograms[$controller]))
                            {
                                $system_program = new SystemProgram;
                                $system_program->name = $name;
                                $system_program->controller = $controller;
                                $system_program->store();
                            }
                            else
                            {
                                $system_program = new SystemProgram($databasePrograms[$controller]);
                            }

                            $system_group->addSystemProgram($system_program);
                        }
                    }
                }
                TTransaction::close();
            }
            LoginForm::reloadPermissions();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
}
