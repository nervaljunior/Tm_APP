<?php

/**
 * Seek Window Trait
 *
 * @version    7.3
 * @author     Matheus Agnes Dias
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
trait BuilderSeekWindowTrait
{
    public function setSeekParameters($action, $param)
    {
        if(!empty($param['_seek_fields']))
        {
            $action->setParameter('_seek_fields', $param['_seek_fields']);
            $action->setParameter('_seek_filters', $param['_seek_filters']);
            $action->setParameter('_seek_hash', $param['_seek_hash']);
            $action->setParameter('_field_data', $param['_field_data']);
            $action->setParameter('_form_name', $param['_form_name']);
            $action->setParameter('_field_id', $param['_field_id']);
            $action->setParameter('_seek_window_id', $param['_seek_window_id'] ?? null);
        }
    }

    public static function getSeekFields($param)
    {
        $seed = AdiantiApplicationConfig::get()['general']['seed'];
        if(!empty($param['_seek_fields']) && !empty($param['_seek_hash']) && !empty($param['_seek_filters']))
        {
            if(md5($seed.$param['_seek_fields'].$param['_seek_filters']) != $param['_seek_hash'])
            {
                throw new Exception(_t('Permission denied'));
            }

            return unserialize(base64_decode($param['_seek_fields']));
        }
        else
        {
            throw new Exception(_t('Permission denied'));
        }
    }

    public static function getSeekFiltersCriteria($param)
    {
        $criteria = new TCriteria();

        $seed = AdiantiApplicationConfig::get()['general']['seed'];
        if(!empty($param['_seek_fields']) && !empty($param['_seek_hash']) && !empty($param['_seek_filters']))
        {
            if(md5($seed.$param['_seek_fields'].$param['_seek_filters']) != $param['_seek_hash'])
            {
                throw new Exception(_t('Permission denied'));
            }

            $filters = unserialize(base64_decode($param['_seek_filters']));
            
            if($filters)
            {
                foreach($filters as $filter)
                {
                    $criteria->add(new TFilter($filter[0],$filter[1],$filter[2]));
                }    
            }
        }
        
        return $criteria;
    }
}
