<?php
class BuilderPageService
{
    
    /**
     * Edit the current page
     */
    public static function editPage($param)
    {
        BuilderPermissionService::checkPermission();
        
        $ini = AdiantiApplicationConfig::get();
        $token = $ini['general']['token'];
        $controller = $param['controller'];
        $manager_url = $ini['builder']['manager_url'];
        $url = "{$manager_url}/ws.php?method=editPage&controller={$controller}&token={$token}";
        
        if (self::checkExternalUrl($url) !== 200)
        {
            new TMessage('error', _bt('Connection failed'));
        }
        else
        {
            TScript::create("__adianti_open_page('{$url}')");
        }
    }
    
    /**
     * Get page code from all pages except the informed
     */
    public static function getCodes()
    {
        BuilderPermissionService::checkPermission();
        
        $ini = AdiantiApplicationConfig::get();
        $token = $ini['general']['token'];
        $manager_url = $ini['builder']['manager_url'];
        $url = "{$manager_url}/ws.php?method=getAllCodes&token={$token}";
        
        if (self::checkExternalUrl($url) !== 200)
        {
            throw new Exception(_bt('Connection failed'));
        }
        
        $content = file_get_contents($url, false, stream_context_create(['ssl' => ['verify_peer' => false, 'verify_peer_name' => false]]));
        $response = (array) json_decode($content);
        
        if (json_last_error() == JSON_ERROR_NONE)
        {
            if ($response['status'] == 'error')
            {
                throw new Exception('Builder: '. $response['message']);
            }
        }
        else
        {
            throw new Exception(_bt('Invalid return'));
        }
        
        return $response['data'];
    }
    
    /**
     * Get page code from all pages except the informed
     */
    public static function getMenus()
    {
        BuilderPermissionService::checkPermission();
        
        $ini = AdiantiApplicationConfig::get();
        $token = $ini['general']['token'];
        $manager_url = $ini['builder']['manager_url'];
        $url = "{$manager_url}/ws.php?method=getMenus&token={$token}";
        
        if (self::checkExternalUrl($url) !== 200)
        {
            throw new Exception(_bt('Connection failed'));
        }
        
        $content = file_get_contents($url, false, stream_context_create(['ssl' => ['verify_peer' => false, 'verify_peer_name' => false]]));
        $response = (array) json_decode($content);
        
        if (json_last_error() == JSON_ERROR_NONE)
        {
            if ($response['status'] == 'error')
            {
                throw new Exception('Builder: '. $response['message']);
            }
        }
        else
        {
            throw new Exception(_bt('Invalid return'));
        }
        
        return $response['data'];
    }
    
    /**
     * Get page code from all pages except the informed
     */
    public static function getPermissions()
    {
        BuilderPermissionService::checkPermission();
        
        $ini = AdiantiApplicationConfig::get();
        $token = $ini['general']['token'];
        $manager_url = $ini['builder']['manager_url'];
        $url = "{$manager_url}/ws.php?method=getPermissionsV1&token={$token}";
        
        if (self::checkExternalUrl($url) !== 200)
        {
            throw new Exception(_bt('Connection failed'));
        }
        
        $content = file_get_contents($url, false, stream_context_create(['ssl' => ['verify_peer' => false, 'verify_peer_name' => false]]));
        $response = (array) json_decode($content);
        
        if (json_last_error() == JSON_ERROR_NONE)
        {
            if ($response['status'] == 'error')
            {
                throw new Exception('Builder: '. $response['message']);
            }
        }
        else
        {
            throw new Exception(_bt('Invalid return'));
        }
        
        return $response['data'];
    }
    
    /**
     * Check if the URL is Ok
     */
    public static function checkExternalUrl($url)
    {
        BuilderPermissionService::checkPermission();
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_exec($ch);
        $retCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
    
        return $retCode;
    }
}
