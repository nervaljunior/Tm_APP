<?php

use Adianti\Core\AdiantiApplicationConfig;
use Adianti\Core\AdiantiCoreApplication;

header('Access-Control-Allow-Origin: https://testeonline.madbuilder.com.br'); 
header('Access-Control-Allow-Headers: *');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {    
   return 0;    
}

// initialization script
require_once 'init.php';

class MobileRestServer
{
    public static function run($request)
    {
        $ini      = AdiantiApplicationConfig::get();
        $input    = json_decode(file_get_contents("php://input"), true);
        $request  = array_merge($request, (array) $input);
        $class    = isset($request['class']) ? $request['class']   : '';
        $method   = isset($request['method']) ? $request['method'] : '';
        $headers  = AdiantiCoreApplication::getHeaders();
        $response = NULL;
        
        $headers['Authorization'] = $headers['Authorization'] ?? ($headers['authorization'] ?? null); // for clientes that send in lowercase (Ex. futter)
        
        try
        {
            if (empty($headers['Authorization']))
            {
                throw new Exception( _t('Authorization error') );
            }
            else
            {
                if (substr($headers['Authorization'], 0, 5) == 'Basic')
                {
                    if (empty($ini['general']['rest_key']))
                    {
                        throw new Exception( _t('REST key not defined') );
                    }
					
                    if ($ini['general']['rest_key'] !== substr($headers['Authorization'], 6))
                    {
                        throw new Exception(_t('Authorization error'));
                    }
                }
                else if (substr($headers['Authorization'], 0, 6) == 'Bearer')
                {
                    BuilderMobileService::initSessionFromToken( substr($headers['Authorization'], 7) );
                }
                else
                {
                    throw new Exception( _t('Authorization error') );
                }
            }
            
            ob_start();
            $response = AdiantiCoreApplication::execute($class, $method, $request, 'rest');
            ob_end_clean();

            if (is_array($response))
            {
                array_walk_recursive($response, ['AdiantiStringConversion', 'assureUnicode']);
            }
            
            return json_encode($response);
        }
        catch (Exception $e)
        {
            http_response_code(500);

            return $e->getMessage();
        }
        catch (Error $e)
        {
            http_response_code(500);
            return $e->getMessage();
        }
    }
}

print MobileRestServer::run($_REQUEST);