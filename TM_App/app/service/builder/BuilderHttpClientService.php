<?php
class BuilderHttpClientService
{
    public static function post($url, $params = [], $authorization = null, $customHeaders = [], $customOptions = [])
    {
        return self::request($url, 'POST', $params, $authorization, $customHeaders, $customOptions);
    }

    public static function get($url, $params = [], $authorization = null, $customHeaders = [], $customOptions = [])
    {
        return self::request($url, 'GET', $params, $authorization, $customHeaders, $customOptions);
    }

    /**
     * Execute a HTTP request
     *
     * @param $url URL
     * @param $method method type (GET,PUT,DELETE,POST)
     * @param $params request body
     */
    public static function request($url, $method = 'POST', $params = [], $authorization = null, $customHeaders = [], $customOptions = [])
    {
        $ch = curl_init();
        
        if ($method == 'POST' || $method == 'PUT')
        {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
            curl_setopt($ch, CURLOPT_POST, true);
     
        }
        else if ( ($method == 'GET' || $method == 'DELETE') && $params)
        {
            $url .= '?'.http_build_query($params);
        }
       
        $defaults = [
            CURLOPT_URL => $url,
            CURLOPT_HEADER => false,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_CONNECTTIMEOUT => 10
        ];
        
        if($customOptions)
        {
            foreach($customOptions as $key=>$value)
            {
                $defaults[$key] = $value;
            }
        }
        
        if (!empty($authorization))
        {
            $defaults[CURLOPT_HTTPHEADER] = array_merge(['Authorization: '. $authorization], $customHeaders);
        }
        
        curl_setopt_array($ch, $defaults);
        $output = curl_exec ($ch);
        
        if ($output === false)
        {
            throw new Exception( curl_error($ch) );
        }
        
        $info = curl_getinfo($ch);
        curl_close ($ch);
        
        if(!empty($info['http_code']) && $info['http_code'] >= 400)
        {
            throw new Exception($output, $info['http_code']);
        }

        $return = json_decode($output);
    
        if (json_last_error() != JSON_ERROR_NONE)
        {
            return $output;
        }
    
        return $return;
    }
}
