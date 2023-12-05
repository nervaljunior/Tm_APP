<?php

class BuilderCNPJService
{
    public static function getUrl($cnpj)
    {
        $cnpj = str_replace(['-','.', '/'], ['', '', ''], $cnpj);
        
        $ini = AdiantiApplicationConfig::get();
        $url = $ini['builder']['services_url'];

        return "{$url}/cnpj/api/v1/{$cnpj}";
    }

    public static function get($cnpj)
    {
        $url = self::getUrl($cnpj);

        $ini = AdiantiApplicationConfig::get();
        
        $url .= '/' . $ini['general']['token'];

        return BuilderHttpClientService::get($url);
    }

    public static function getFull($cnpj)
    {
        $cnpj = str_replace(['-','.', '/'], ['', '', ''], $cnpj);

        $ini = AdiantiApplicationConfig::get();
        
        $url = "{$ini['builder']['services_url']}/cnpj/api/v1/full/{$cnpj}/{$ini['general']['token']}";

        return BuilderHttpClientService::get($url);
    }
}