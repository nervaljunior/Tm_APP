<?php
class BuilderService
{
    public static function setTheme($userid, $themename)
    {
        try
        {
            TTransaction::open('permission');

            $preference = SystemPreference::find('builder_user_themes')??new SystemPreference;
            $builder_user_themes = json_decode($preference->preference, true)??[];
            $builder_user_themes[$userid] = $themename;

            $preference->id = 'builder_user_themes';
            $preference->preference = json_encode($builder_user_themes);
            $preference->store();

            TTransaction::close();
        }
        catch(Exception $e)
        {
            TTransaction::rollback();
            throw $e;
        }
    }

    public static function getTheme($userid)
    {
        try
        {
            TTransaction::open('permission');
            
            $preference = SystemPreference::find('builder_user_themes')??new SystemPreference;
            $builder_user_themes = [];
            
            if(!empty($preference->preference))
            {
                $builder_user_themes = json_decode($preference->preference, true)??[];
            }

            TTransaction::close();

            return $builder_user_themes[$userid] ?? 'default';
        }
        catch(Exception $e)
        {
            TTransaction::rollback();
            return 'default';
        }
    }

    public static function getInstructionsUpdateLib()
    {
        BuilderPermissionService::checkPermission();
        
        $ini = AdiantiApplicationConfig::get();
        $token = $ini['general']['token'];
        $url = "https://manager.adiantibuilder.com.br/ws.php?method=getInstructionsUpdateLib&token={$token}";

        $content = file_get_contents($url, false, stream_context_create(['ssl' => ['verify_peer' => false, 'verify_peer_name' => false]]));
        $response = json_decode($content, true);        

        if ($response and $response['status'] == 'success')
        {
            $infos = $response['data'];
        }
        else
        {
            throw new Exception( (is_array($response) ? $response['message']??'Error' : 'Error'));
        }
       
        // Validate sqls

        return $infos;
    }

    public static function downloadFilesUploadLib()
    {
        BuilderPermissionService::checkPermission();
        
        $ini = AdiantiApplicationConfig::get();
        $token = $ini['general']['token'];
        $url = "https://manager.adiantibuilder.com.br/ws.php?method=downloadFilesUploadLib&token={$token}&targz=1";

        $content = file_get_contents($url, false, stream_context_create(['ssl' => ['verify_peer' => false, 'verify_peer_name' => false]]));
        
        if ($content)
        {
            $jsonData = json_decode($content, true);
            
            if ($jsonData && ! empty($jsonData['status']) && $jsonData['status'] == 'error')
            {
                throw new Exception($jsonData['message']??'Error');
            }

            $content = trim($content);
            $folder_name = str_replace('code/', '', str_replace('.tar.gz', '', $content) );
            $zipContent = @file_get_contents('https://app.adiantibuilder.com.br/' . $content);
            file_put_contents('tmp/update_lib.tar.gz', $zipContent);
            chmod('tmp/update_lib.tar.gz', 0777);
            exec('tar -xzf tmp/update_lib.tar.gz -C tmp/');
            exec('rm -rf tmp/update_lib.tar.gz');
            exec("mv {$folder_name} tmp/update_lib");

            if (! file_exists("tmp/update_lib/"))
            {
                throw new Exception(_bt('Permission denied, could not copy the update folder files'));
            }

            return "tmp/update_lib/";
        }

        throw new Exception('Error');
    }
}