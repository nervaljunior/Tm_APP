<?php

class BuilderCEPService
{
    
    public static function getUrl($cep)
    {
        $ini = AdiantiApplicationConfig::get();

        $cep = str_replace(['-','.'], ['', ''], $cep);

        return "{$ini['builder']['services_url']}/cep/api/v1/{$cep}";
    }

    public static function get($cep)
    {
        $url = self::getUrl($cep);

        $ini = AdiantiApplicationConfig::get();
        
        $url .= '/' . $ini['general']['token'];

        return BuilderHttpClientService::get($url);
    }
}
