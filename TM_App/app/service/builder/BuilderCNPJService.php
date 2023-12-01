<?php

class BuilderCNPJService
{
    const ENDPOINT = 'https://services.adiantibuilder.com.br/cnpj/api/v1/';
    
    public static function getUrl($cnpj)
    {
        $cnpj = str_replace(['-','.', '/'], ['', '', ''], $cnpj);

        return self::ENDPOINT . $cnpj;
    }

    public static function get($cnpj)
    {
        $url = self::getUrl($cnpj);

        $ini = parse_ini_file('app/config/application.ini');
        
        $url .= '/' . $ini['token'];

        return BuilderHttpClientService::get($url);
    }

    public static function getFull($cnpj)
    {
        $cnpj = str_replace(['-','.', '/'], ['', '', ''], $cnpj);

        $url = self::ENDPOINT .'full/' .$cnpj;

        $ini = parse_ini_file('app/config/application.ini');
        
        $url .= '/' . $ini['token'];

        return BuilderHttpClientService::get($url);
    }
}