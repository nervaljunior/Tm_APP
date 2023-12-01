<?php

/**
 * Master Detail Trait
 *
 * @version    7.3
 * @author     Matheus Agnes Dias
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
trait BuilderMasterDetailTrait
{
    /**
     * Store an item from details session into database
     * @param $model Model class name
     * @param $foreign_key Detail foreign key name
     * @param $master_object Master object
     * @param $detail_items Array component
     * @param $datagrid TDataGrid component
     * @param $transformer Function to be applied over the objects
     */
    public function storeMasterDetailItems($model, $foreign_key, $prefix, $master_object, $detail_items, $form, $datagrid, Callable $transformer = null, $criteria = null)
    {
        $master_pkey    = $master_object->getPrimaryKey();
        $master_id      = $master_object->$master_pkey;
        $detail_objects = [];

        if(!$criteria)
        {
            $criteria = new TCriteria();
        }
        
        if ($detail_items) 
        {
            $detail_ids = [];
            foreach ($detail_items as $key => $item)
            {   
                $item = unserialize(base64_decode($item));
                if(is_object($item))
                {
                    $item = (array) $item;
                }
                
                $detail_object = new $model;
                $detail_object->fromArray($item);
                $detail_pkey = $detail_object->getPrimaryKey();
                
                $detail_object->$foreign_key = $master_id;
                 
                if ($transformer)
                {
                    call_user_func($transformer, $master_object, $detail_object);
                }

                $detail_object->store();

                $item = (object) $item;
                $item->$detail_pkey = $detail_object->$detail_pkey;

                $detail_object->__row__id = $item->__row__id;
                
                if(is_array($item->__display__))
                {
                    $item->__display__ = (object) $item->__display__;
                }
                
                $detail_object->__row__data = base64_encode(serialize($item));

                $row = $datagrid->addItem($detail_object);
                $row->id = $item->__row__id;

                TDataGrid::replaceRowById($datagrid->id, $item->__row__id, $row);

                $detail_objects[] = $detail_object;
                $detail_ids[] = $detail_object->$detail_pkey;
            }
            
            $criteria->add(new TFilter($foreign_key, '=', $master_id));
            if ($detail_ids)
            {
                $criteria->add(new TFilter($detail_pkey, 'not in', $detail_ids));
            }
            $repository = new TRepository($model);
            $repository->delete($criteria); 
        }
        else
        {
            $criteria->add(new TFilter($foreign_key, '=', $master_id));
            
            $repository = new TRepository($model);
            $repository->delete($criteria); 
        }
        
        return $detail_objects;
    }
    
    /**
     * Load items for detail into session
     * @param $model Model class name
     * @param $foreign_key Detail foreign key name
     * @param $prefix Detail prefix name
     * @param $master_object Master object
     * @param $form TForm component
     * @param $datagrid TDataGrid component
     * @param $transformer Function to be applied over the objects
     */
    public function loadMasterDetailItems($model, $foreign_key, $prefix, $master_object, $form, $datagrid, $criteria = null, Callable $transformer = null)
    {
        $master_pkey  = $master_object->getPrimaryKey();
        $master_id    = $master_object->$master_pkey;

        if(!$criteria)
        {
            $criteria = new TCriteria();
        }

        $criteria->add(new TFilter($foreign_key, '=', $master_id));
        $objects = $model::getObjects($criteria);
        $fields = $form->getFields();

        if ($objects)
        {
            foreach ($objects as $detail_object)
            {
                $detail_pkey  = $detail_object->getPrimaryKey();
                $array_object = $detail_object->toArray();
                
                $object_item = (object) $array_object;
                
                if(empty($object_item->__display__))
                {
                    $object_item->__display__ = new stdClass();
                }

                if ($transformer)
                {
                    call_user_func($transformer, $master_object, $detail_object, $object_item);
                }
                
                foreach($object_item as $key => $value)
                {
                    if(!empty($fields["{$prefix}_{$key}"]))
                    {
                        $field = $fields["{$prefix}_{$key}"];
                        $field->setValue($value);
                        
                        $field_value = $field->getValue();

                        if($field instanceof TMultiFile || $field instanceof TFile || $field instanceof TImageCropper || $field instanceof TImageCapture)
                        {
                            $object_item->{$key} = $field_value;
                        }

                        $object_item->__display__->{$key} = $field_value;
                        $field->setValue(null);
                    }
                }

                $detail_object->__row__id = uniqid('b');
                $object_item->__row__id = $detail_object->__row__id;

                $detail_object->__row__data = base64_encode(serialize($object_item));

                $row = $datagrid->addItem( $detail_object );
                $row->id = $detail_object->__row__id;
            }    
        }
        
        return $objects;
    }
}
