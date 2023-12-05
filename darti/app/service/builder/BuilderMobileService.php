<?php

use Adianti\Core\AdiantiApplicationConfig;
use Adianti\Database\TTransaction;
use Adianti\Registry\TSession;
use Adianti\Util\AdiantiStringConversion;
use Adianti\Validator\TRequiredValidator;
use Firebase\JWT\JWT;

class BuilderMobileService
{
    public static function encrypt($data)
    {
        $ini = AdiantiApplicationConfig::get();
        $key = APPLICATION_NAME . $ini['general']['token'];

        $plaintext = serialize($data);
        $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
        $iv = openssl_random_pseudo_bytes($ivlen);
        $ciphertext_raw = openssl_encrypt($plaintext, $cipher, $key, OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac('sha256', $ciphertext_raw, $key, true);
        
        return base64_encode( $iv.$hmac.$ciphertext_raw );
    }

    public static function decrypt($token)
    {
        $ini = AdiantiApplicationConfig::get();
        $key = APPLICATION_NAME . $ini['general']['token'];

        $c = base64_decode($token);
        $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
        $iv = substr($c, 0, $ivlen);
        $hmac = substr($c, $ivlen, $sha2len=32);
        $ciphertext_raw = substr($c, $ivlen+$sha2len);
        $original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, $key, OPENSSL_RAW_DATA, $iv);
        $calcmac = hash_hmac('sha256', $ciphertext_raw, $key, true);
        
        if (hash_equals($hmac, $calcmac))
        {
            return unserialize($original_plaintext);
        }

        return NULL;
    }

    public static function initSessionFromToken($token)
    {
        $ini = AdiantiApplicationConfig::get();
        $key = APPLICATION_NAME . $ini['general']['token'];
        
        if (empty($ini['general']['token']))
        {
            throw new Exception('Application token not defined');
        }
        
        $token = self::decrypt(JWT::decode($token, $key, array('HS256')));
        
        $expires = $token['expires'];
        
        if ($expires < strtotime('now'))
        {
            throw new Exception('Token expired. This operation is not allowed');
        }

        TTransaction::open('permission');
        
        $user = SystemUsers::newFromLogin($token['user']);
        
        $programs = $user->getPrograms();
        $programs['LoginForm'] = TRUE;

        TSession::setValue('logged',        TRUE);
        TSession::setValue('logged_mobile', TRUE);
        TSession::setValue('login',         $token['user']);
        TSession::setValue('userid',        $token['userid']);
        TSession::setValue('username',      $token['username']);
        TSession::setValue('usermail',      $token['usermail']);
        TSession::setValue('user_language', $token['user_language']);
        TSession::setValue('userunitid',    $token['userunitid']);
        TSession::setValue('userunitname',  $token['userunitname']);
        TSession::setValue('unit_database', $token['unit_database']);
        TSession::setValue('frontpage',     $token['frontpage']);
        TSession::setValue('usergroupids',  $user->getSystemUserGroupIds());
        TSession::setValue('userunitids',   $user->getSystemUserUnitIds());
        TSession::setValue('programs',      $programs);
        
        TTransaction::close();
    }

    public static function onLogin($param)
    {
        $user = ApplicationAuthenticationService::authenticate($param['login'], $param['password']);

        $ini = AdiantiApplicationConfig::get();
        $key = APPLICATION_NAME . $ini['general']['token'];
        
        if (empty($ini['general']['token']))
        {
            throw new Exception('Application token not defined');
        }
        
        if (!empty($ini['general']['multiunit']) and $ini['general']['multiunit'] == '1')
        {
            (new TRequiredValidator)->validate( _t('Unit'), $param['unit_id']??'');
        }

        TTransaction::open('permission');

        ApplicationAuthenticationService::setUnit( $param['unit_id'] ?? null );
        ApplicationAuthenticationService::setLang( $param['lang_id'] ?? null );

        $frontpage = $user->frontpage;

        $frontpageUser = 'EmptyPage';

        if ($frontpage instanceof SystemProgram and $frontpage->controller)
        {
            $frontpageUser = $frontpage->controller;
        }

        TSession::setValue('usergroupids',  $user->getSystemUserGroupIds());
        TSession::setValue('userunitids',   $user->getSystemUserUnitIds());
        TSession::setValue('programs',      $user->getPrograms());

        $token = [
            "user" => $param['login'],
            "userid" => $user->id,
            "username" => $user->name,
            "usermail" => $user->email,
            "frontpage" => $frontpageUser,
            "user_language" => $param['lang_id'],
            "userunitid"  => TSession::getValue("userunitid"),
            "userunitname" => TSession::getValue("userunitname"),
            "unit_database" => TSession::getValue("unit_database"),
            "expires" => strtotime("+ 3 month")
        ];

        $token = self::encrypt($token);
        $token = JWT::encode($token, $key);

        TTransaction::close();

        return [
            'token' => $token,
            'id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'frontpage' => $frontpageUser,
            'menus' => self::getMenu()
        ];
    }

    public static function getPublicMenus()
    {
        $menus = [];

        if (file_exists('menu-public-mobile.xml'))
        {
            $menu_string = AdiantiStringConversion::assureUnicode(file_get_contents('menu-public-mobile.xml'));
            $xml = new SimpleXMLElement($menu_string);
            
            foreach ($xml as $xmlElement)
            {
                $group = self::getGroupMenus($xmlElement, FALSE);

                if ($group)
                {
                    $menus[] = $group;
                }
            }
        }

        return $menus;
    }

    public static function getMenu()
    {
        $menus = [];

        if (file_exists('menu-mobile.xml'))
        {
            $menu_string = AdiantiStringConversion::assureUnicode(file_get_contents('menu-mobile.xml'));
            $xml = new SimpleXMLElement($menu_string);
            
            foreach ($xml as $xmlElement)
            {
                $group = self::getGroupMenus($xmlElement);

                if ($group)
                {
                    $menus[] = $group;
                }
            }
        }

        return $menus;
    }

    private static function getGroupMenus($group, $checkPermission = TRUE)
    {
        $atts   = $group->attributes();
        $label  = (string) $atts['label'];
        $icon   = (string) $group->icon;
        $action   = (string) $group->action;

        if ($checkPermission AND !empty($action) AND (substr($action,0,7) !== 'http://') AND (substr($action,0,8) !== 'https://'))
        {
            // check permission
            $parts = explode('#', $action);
            $className = $parts[0];

            if (! SystemPermission::checkPermission($className) )
            {
                return null;
            }
        }

        $icone = explode(' ', str_replace('fa-fw', '', trim($icon)));
        $iconeParts = explode(':', $icone[0]);
        $iconColor = trim($icone[ count($icone) -1 ]);
        $iconColor = strpos($iconColor, "#") !== false ? $iconColor : NULL;

        $pages = [];
        $groupItem = null;
        if ($group->menu)
        {
            foreach($group->menu as $groupMenu)
            {
                $itens = $groupMenu->menuitem;

                foreach($itens as $item)
                {
                    if ($item->menu)
                    {
                        $menuGroup = self::getGroupMenus($item);

                        if ($menuGroup && ! empty($menuGroup['pages']))
                        {
                            $pages[] = $menuGroup;
                        }
                    }
                    else
                    {
                        $page = self::getItemMenu($item, $checkPermission);

                        if ($page) {
                            $pages[] = $page;
                        }
                    }
                }
            }

            if ($pages)
            {
                $groupItem = [
                    'icon' => (empty($iconeParts[0]) || empty($iconeParts[1])) ? null : [
                        (empty($iconeParts[0]) ? 'far' : $iconeParts[0]),
                        (empty($iconeParts[1]) ? 'circle' : $iconeParts[1])
                    ],
                    'iconColor' => $iconColor,
                    'label' => self::getLabel($label),
                    'pages' => $pages
                ];
            }
        }
        else
        {
            $groupItem = [
                'icon' => (empty($iconeParts[0]) || empty($iconeParts[1])) ? null : [
                    (empty($iconeParts[0]) ? 'far' : $iconeParts[0]),
                    (empty($iconeParts[1]) ? 'circle' : $iconeParts[1])
                ],
                'iconColor' => $iconColor,
                'label' => self::getLabel($label),
                'page' => str_replace('#', '&', $action)
            ];
        }

        return $groupItem;
    }

    private static function getItemMenu($item, $checkPermission = TRUE)
    {
        $atts   = $item->attributes();
        $label  = (string) $atts['label'];
        $icon   = (string) $item->icon;
        $action = (string) $item->action;

        $icone = explode(' ', str_replace('fa-fw', '', trim($icon)));
        $iconeParts = explode(':', $icone[0]);
        $iconColor = trim($icone[ count($icone) -1 ]);
        $iconColor = strpos($iconColor, "#") !== false ? $iconColor : NULL;
        
        if ($checkPermission AND !empty($action) AND (substr($action,0,7) !== 'http://') AND (substr($action,0,8) !== 'https://'))
        {
            // check permission
            $parts = explode('#', $action);
            $className = $parts[0];

            if (! SystemPermission::checkPermission($className) )
            {
                return null;
            }
        }

        return [
            'icon' => (empty($iconeParts[0]) || empty($iconeParts[1])) ? null : [
                (empty($iconeParts[0]) ? 'far' : $iconeParts[0]),
                (empty($iconeParts[1]) ? 'circle' : $iconeParts[1])
            ],
            'label' => self::getLabel($label),
            'iconColor' => $iconColor,
            'page' => str_replace('#', '&', $action)
        ];
    }

    private static function getLabel($label)
    {
        if (strpos($label, '_t{') !== false)
        {
            $label = _t(substr($label, 3, -1));
        }

        return $label;
    }

    public static function getUnits($param)
    {

        try
        {
            $options = [];

            TTransaction::open('permission');

            if (empty($param['login']))
            {
                throw new Exception("Login is required");
            }
            
            $ini = AdiantiApplicationConfig::get();
            
            if (!empty($ini['general']['multiunit']) and $ini['general']['multiunit'] == '1')
            {
                $user = SystemUsers::newFromLogin( $param['login'] );
                
                if ($user instanceof SystemUsers)
                {
                    $units = $user->getSystemUserUnits();
                    
                    if ($units)
                    {
                        foreach ($units as $unit)
                        {
                            $options[] = ['label' => $unit->name, 'value' => $unit->id];
                        }
                    }
                    
                }
                
            }

            TTransaction::close();

            return $options;

        }
        catch (Exception $e)
        {
            TTransaction::rollback();
            throw $e;
        }
    }

    public static function getInitConfig()
    {
        $ini = AdiantiApplicationConfig::get();
        
        $public_menu = self::getPublicMenus();

        $user_public_pages = [];

        foreach ($ini['user_public_pages']??[] as $item)
        {
            $icone = explode(' ', str_replace('fa-fw', '', trim($item['icon'])));
            $iconeParts = explode(':', $icone[0]);
            $iconColor = trim($icone[ count($icone) -1 ]);
            $iconColor = strpos($iconColor, "#") !== false ? $iconColor : NULL;
    
            $item['class'] = str_replace('#', '&', $item['class']);
            $item['icon'] = (empty($iconeParts[0]) || empty($iconeParts[1])) ? null : [
                (empty($iconeParts[0]) ? 'far' : $iconeParts[0]),
                (empty($iconeParts[1]) ? 'circle' : $iconeParts[1])
            ];
            $item['iconColor'] = $iconColor;

            $user_public_pages[] = $item;
        }

        $lang_options = array_map(function($lang) {
            return ['label' => $lang, 'value' => $lang];
        }, ($ini["general"]["lang_options"]??[]));

        return [
            "public_menu" => $public_menu,
            "user_public_pages" => $user_public_pages,
            "public_view" => (!! $ini["general"]["public_view"]),
            "user_register" => (!! $ini["permission"]["user_register"]),
            "reset_password" => (!! $ini["permission"]["reset_password"]),
            "multiunit" => (!! $ini["general"]["multiunit"]),
            "multilang" => (!! $ini["general"]["multi_lang"]),
            "language" => ($ini["general"]["language"]??''),
            "lang_options" => $lang_options
        ];
    }
}