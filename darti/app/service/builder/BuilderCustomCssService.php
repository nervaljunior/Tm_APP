<?php
use Adianti\Core\AdiantiApplicationConfig;
use Adianti\Http\AdiantiHttpClient;
use Adianti\Widget\Dialog\TMessage;

class BuilderCustomCssService
{
    const FILE_NAME = 'app/lib/include/css/builder_user_custom_css.css';
    const FOLDER_NAME = 'app/lib/include/css';
   
    public static function sendFile($csss)
    {
        BuilderPermissionService::checkPermission();
    
        $ini = AdiantiApplicationConfig::get();
        $token = $ini['general']['token'];
        $manager_url = $ini['builder']['manager_url'];
        $url = "{$manager_url}/ws.php?method=editCustomCss&token={$token}";
        
        if (BuilderPageService::checkExternalUrl($url) !== 200)
        {
            new TMessage('error', _bt('Connection failed'));
        }
        else
        {
            AdiantiHttpClient::request(
                $url,
                'POST',
                ['css_content' => base64_encode($csss)]
            );
        }
    }
    
    public static function update($param)
    {
        try
        {
            BuilderPermissionService::checkPermission(); 

            $csss = '';

            if (! file_exists(self::FOLDER_NAME)) {
                mkdir(self::FOLDER_NAME);
            }

            if (! file_exists(self::FILE_NAME))
            {
                // add  libraries user
                $fileName = self::FILE_NAME;
                $include = "<link href='{$fileName}?afver=730' rel='stylesheet' type='text/css' media='screen' />\n";
                
                $templates = scandir('app/templates');
                if ($templates)
                {
                    foreach($templates as $template)
                    {
                        if (in_array($template, ['..', '.']))
                        {
                            continue;
                        }

                        file_put_contents("app/templates/{$template}/libraries_user.html", $include, FILE_APPEND);
                    }
                }               
            }
            else
            {
                $templates = scandir('app/templates');
                if ($templates)
                {
                    foreach($templates as $template)
                    {
                        if (in_array($template, ['..', '.']))
                        {
                            continue;
                        }

                        $content = file_get_contents("app/templates/{$template}/libraries_user.html");
                        
                        $selector = str_replace(['/', '.'], ['\/', '\.'], self::FILE_NAME);

                        $reg_exp = "(?<={$selector}\?appver=)([0-9]+)";

                        if (preg_match("/$reg_exp/", $content)) {
                            $content = preg_replace("/$reg_exp/", uniqid(), $content);
                        }
                        
                        file_put_contents("app/templates/{$template}/libraries_user.html", $content);
                    }
                }
            }

            if (file_exists(self::FILE_NAME))
            {
                $csss = file_get_contents(self::FILE_NAME);
            }
    
            $selector = str_replace(['(', ')', '[', ']', '.'], ['\(', '\)', '\[', '\]', '\.'], $param['selector']);
    
            $reg_exp = "({$selector}\s* \{\n)([^\}]*?)(})";
    
            $newCss = self::format($param['selector'], $param['css']);

            if (preg_match("/$reg_exp/", $csss)) {
                $csss = preg_replace("/$reg_exp/", $newCss, $csss);
            } else {
                $csss .= "{$newCss}\n";
            }

            $param['selector'] .=  ':hover';
            $selector = str_replace(['(', ')', '[', ']', '.'], ['\(', '\)', '\[', '\]', '\.'], $param['selector']);

            $reg_exp = "({$selector}\s* \{\n)([^\}]*?)(})";

            $newCss = self::format($param['selector'], $param['hover']);

            if (preg_match("/$reg_exp/", $csss)) {
                $csss = preg_replace("/$reg_exp/", $newCss, $csss);
            } else {
                $csss .= "{$newCss}\n";
            }

            // clear empty csss
            $csss = preg_replace("/\#builder-layout.*{\n}/", '', $csss);
    
            self::sendFile($csss);
            
            file_put_contents(self::FILE_NAME, $csss);

            echo $csss;
        }
        catch (Exception $e)
        {
            new TMessage('error', _bt('Connection failed') . ': ' . $e->getMessage());
        }
    }

    public static function getCss()
    {
        try
        {
            BuilderPermissionService::checkPermission(); 

            $csss = '';

            if (file_exists(self::FILE_NAME))
            {
                $csss = file_get_contents(self::FILE_NAME);
            }

            echo $csss;
        }
        catch (Exception $e)
        {
            echo "";
        }
    }

    public static function format($selector, $css)
    {
        $newCss = $selector . " {\n";

        if (! empty($css))
        {
            foreach($css as $key => $value)
            {
                $newCss .= "    {$key}: {$value};\n";
            }
        }

        $newCss .= "}";
        
        return $newCss;
    }
}