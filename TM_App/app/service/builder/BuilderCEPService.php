<?php

class BuilderCEPService
{
    const ENDPOINT = 'https://services.adiantibuilder.com.br/cep/api/v1/';
    
    public static function getUrl($cep)
    {
        $cep = str_replace(['-','.'], ['', ''], $cep);

        return self::ENDPOINT . $cep;
    }

    public static function get($cep)
    {
        $url = self::getUrl($cep);

        $ini = parse_ini_file('app/config/application.ini');
        
        $url .= '/' . $ini['token'];

        return BuilderHttpClientService::get($url);
    }
}
