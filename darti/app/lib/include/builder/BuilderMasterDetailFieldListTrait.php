<?php

/**
 * Master Detail Trait
 *
 * @version    7.1
 * @author     Matheus Agnes Dias
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
trait BuilderMasterDetailFieldListTrait
{
    /**
     * Store an item from details session into database
     * @param $model Model class name
     * @param $foreign_key Detail foreign key name
     * @param $master_object Master object
     * @param $fieldList TFieldList component
     * @param $transformer Function to be applied over the objects
     */
    public function storeItems($model, $foreign_key, $master_object, $fieldList, Callable $transformer = null, $criteria = null)
    {
        $master_pkey    = $master_object->getPrimaryKey();
        $master_id      = $master_object->$master_pkey;
        $detail_objects = [];
        $detail_items   = $fieldList->getPostData();
        
        if(!$criteria)
        {
            $criteria = new TCriteria();
        }
        
        if ($detail_items)
        {
            foreach ($detail_items as $row => $object)
            {
                $array_object = (array) $object;
                unset($array_object['uniq']);
                unset($array_object['__row__data']);
                unset($array_object['__row__id']);

                if (count(array_filter($array_object)) == 0)
                {
                    unset($detail_items[$row]);
                }
            }
        }

        if ($detail_items) 
        {
            $detail_ids = [];
            foreach ($detail_items as $key => $item)
            {   
                if(is_object($item))
                {
                    $item = (array) $item;
                }
                
                $detail_object = new $model;
                $detail_object->fromArray($item);
                $detail_pkey   = $detail_object->getPrimaryKey();
                
                $detail_object->$foreign_key = $master_id;
                
                if(!empty($item['__row__id']))
                {
                    $detail_object->__row__id = $item['__row__id'];    
                }
                
                if(!empty($item['__row__data']))
                {
                    $detail_object->__row__data = $item['__row__data'];
                }
                
                if ($transformer)
                {
                    call_user_func($transformer, $master_object, $detail_object);
                }
                
                $detail_object->store();
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
     * @param $master_object Master object
     * @param $fieldList TFieldList component
     * @param $transformer Function to be applied over the objects
     */
    public function loadItems($model, $foreign_key, $master_object, $fieldList, Callable $transformer = null, $criteria = null)
    {
        $fieldList->addHeader();
        $prefix = $fieldList->getFieldPrefix();
        
        $master_pkey  = $master_object->getPrimaryKey();
        $master_id    = $master_object->$master_pkey;

        if(!$criteria)
        {
            $criteria = new TCriteria();
        }

        $criteria->add(new TFilter($foreign_key, '=', $master_id));

        $objects = $model::getObjects($criteria);
        
        if ($objects)
        {
            foreach ($objects as $detail_object)
            {
                $detail_pkey  = $detail_object->getPrimaryKey();
                $array_object = $detail_object->toArray();
                
                $object_item = new stdClass();
                foreach ($array_object as $attribute => $value) 
                {
                    $object_item->{"{$prefix}_{$attribute}"} = $value;
                }
                
                $object_item->__row__id = 'b'.uniqid();
                $object_item->__row__data = '';
                
                if ($transformer)
                {
                    call_user_func($transformer, $master_object, $detail_object, $object_item);
                }
                
                $fieldList->addDetail($object_item);
            }    
        }
        else
        {
            $fieldList->addDetail(new stdClass);
        }
        
        $fieldList->addCloneAction();
        return $objects;
    }
}
