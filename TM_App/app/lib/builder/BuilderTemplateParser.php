<?php

use Adianti\Registry\TSession;

class BuilderTemplateParser
{
    /**
     * Parse template and replace basic system variables
     * @param $content raw template
     */
    public static function parse($content, $theme = 'theme3')
    {
        $ini       = AdiantiApplicationConfig::get();
        
        if ((TSession::getValue('login') == 'admin') && !empty($ini['general']['token']) && file_exists("app/templates/{$theme}/builder-menu.html"))
        {
            $builder_menu = file_get_contents("app/templates/{$theme}/builder-menu.html");
            $content = str_replace('<!--{BUILDER-MENU}-->', $builder_menu, $content);
        }
        else
        {
            $content = str_replace('<!--[IFADMIN]-->',  '<!--',  $content);
            $content = str_replace('<!--[/IFADMIN]-->', '-->',   $content);
            
            $content = str_replace('<!--[IFADMIN-LEFT-MENU]-->',  '<!--',  $content);
            $content = str_replace('<!--[/IFADMIN-LEFT-MENU]-->', '-->',   $content);
        }
        
        if (!isset($ini['permission']['user_register']) OR $ini['permission']['user_register'] !== '1')
        {
            $content = str_replace(['<!--[CREATE-ACCOUNT]-->', '<!--[CREATE-ACCOUNT]-->'], ['<!--', '-->'], $content);
        }
        
        if (!isset($ini['permission']['reset_password']) OR $ini['permission']['reset_password'] !== '1')
        {
            $content = str_replace(['<!--[RESET-PASSWORD]-->', '<!--[RESET-PASSWORD]-->'], ['<!--', '-->'], $content);
        }
        
        $use_tabs = $ini['general']['use_tabs'] ?? 0;
        $store_tabs = $ini['general']['store_tabs'] ?? 0;
        $use_mdi_windows = $ini['general']['use_mdi_windows'] ?? 0;
        $store_mdi_windows = $ini['general']['store_mdi_windows'] ?? 0;

        if ($use_mdi_windows) {
            $use_tabs = 1;
        }

        if ($store_mdi_windows) {
            $store_tabs = 1;
        }

        $has_left_menu = false;
        $has_top_menu = false;
        $top_menu_var = 'false';
        $top_menu = '';

        if ( (!isset($ini['general']['left_menu']) || $ini['general']['left_menu'] == '0') && (!isset($ini['general']['top_menu']) || $ini['general']['top_menu'] == '0') )
        {
            $content = str_replace(['<!--[IF-LEFT-MENU]-->', '<!--[/IF-LEFT-MENU]-->'], ['', ''], $content);
            $has_left_menu = true;
        }
        elseif (!isset($ini['general']['left_menu']) || $ini['general']['left_menu'] == '0')
        {
            $content = str_replace(['<!--[IF-LEFT-MENU]-->', '<!--[/IF-LEFT-MENU]-->'], ['<!--', '-->'], $content);
            $content = str_replace(['<!--[IFADMIN-LEFT-MENU]-->', '<!--[/IFADMIN-LEFT-MENU]-->'], ['<!--', '-->'], $content);
        }
        elseif(isset($ini['general']['left_menu']) && $ini['general']['left_menu'] == '1')
        {
            $content = str_replace(['<!--[IF-LEFT-MENU]-->', '<!--[/IF-LEFT-MENU]-->'], ['', ''], $content);
            $has_left_menu = true;
        }

        if (isset($ini['general']['top_menu']) && $ini['general']['top_menu'] == '1')
        {
            $content = str_replace(['<!--[IF-TOP-MENU]-->', '<!--[/IF-TOP-MENU]-->'], ['', ''], $content);
            $content = str_replace(['<!--[IF-NOT-TOP-MENU]-->', '<!--[/IF-NOT-TOP-MENU]-->'], ['/*', '*/'], $content);
            $has_top_menu = true;
            $top_menu_var = 'true';
        }
        else
        {
            $content = str_replace(['<!--[IF-TOP-MENU]-->', '<!--[/IF-TOP-MENU]-->'], ['<!--', '-->'], $content);
            $content = str_replace(['<!--[IF-NOT-TOP-MENU]-->', '<!--[/IF-NOT-TOP-MENU]-->'], ['', ''], $content);
        }

        if(!$has_left_menu)
        {
            $content = str_replace('{builder_top_menu}', 'top-menu-only', $content);
            $content = str_replace('{top_menu_only}', 'true', $content);
        }
        else
        {
            $content = str_replace('{builder_top_menu}', '', $content);
            $content = str_replace('{top_menu_only}', 'false', $content);
        }
        
        $menu = 'menu.xml';
        if (isset($ini['general']['public_view']) && $ini['general']['public_view'] == '1' && empty(TSession::getValue('logged')))
        {
            $menu = 'menu-public.xml';
        }

        $themeMenuFactory = BuilderMenuFactory::getInstance($theme, $menu);
        $menu = $themeMenuFactory->getMenu();
        $modulemenu = $themeMenuFactory->getModuleMenu();
        $dropdownMenu = $themeMenuFactory->getDropdownNavbarMenu();
        $top_menu_module = $has_top_menu ? $themeMenuFactory->getTopModuleMenu() : '';
        $top_menu = $has_top_menu ? $themeMenuFactory->getTopMenu() : '';

        $content = str_replace('{TOP-MENU-BUILDER}', $top_menu, $content);
        $content = str_replace('{TOP-MODULE-MENU-BUILDER}', $top_menu_module, $content);
        $content = str_replace('{MODULE_MENU}', $modulemenu, $content);
        $content = str_replace('{DROPDOWN_MENU}', $dropdownMenu, $content);
        $content = str_replace('{MENU}', $menu, $content);

        $use_tabs = $ini['general']['use_tabs'] ?? 0;
        $store_tabs = $ini['general']['store_tabs'] ?? 0;
        $use_mdi_windows = $ini['general']['use_mdi_windows'] ?? 0;
        $store_mdi_windows = $ini['general']['store_mdi_windows'] ?? 0;

        if ($use_mdi_windows) {
            $use_tabs = 1;
        }

        if ($store_mdi_windows) {
            $store_tabs = 1;
        }

        $class     = isset($_REQUEST['class']) ? $_REQUEST['class'] : '';

        $libraries_user = file_get_contents("app/templates/{$theme}/libraries_user.html");
        $libraries_builder = file_get_contents("app/templates/{$theme}/libraries_builder.html");
        $libraries_theme = file_get_contents("app/templates/{$theme}/libraries_theme.html");
        $libraries = file_get_contents("app/templates/{$theme}/libraries.html");
        $user_theme = BuilderService::getTheme(TSession::getValue('userid'));

        $content   = str_replace('{LIBRARIES}', $libraries, $content);
        $content   = str_replace('{class}',     $class, $content);
        $content   = str_replace('{template}',  $theme, $content);
        $content   = str_replace('{lang}',      AdiantiCoreTranslator::getLanguage(), $content);
        $content   = str_replace('{debug}',     isset($ini['general']['debug']) ? $ini['general']['debug'] : '1', $content);
        $content   = str_replace('{login}',     (string) TSession::getValue('login'), $content);
        $content   = str_replace('{title}',     isset($ini['general']['title']) ? $ini['general']['title'] : '', $content);
        $content   = str_replace('{username}',  (string) TSession::getValue('username'), $content);
        $content   = str_replace('{usermail}',  (string) TSession::getValue('usermail'), $content);
        $content   = str_replace('{frontpage}', (string) TSession::getValue('frontpage'), $content);
        $content   = str_replace('{userunitid}', (string) TSession::getValue('userunitid'), $content);
        $content   = str_replace('{userunitname}', (string) TSession::getValue('userunitname'), $content);
        $content   = str_replace('{query_string}', $_SERVER["QUERY_STRING"] ?? '', $content);
        $content   = str_replace('{use_tabs}', $use_tabs, $content);
        $content   = str_replace('{store_tabs}', $store_tabs, $content);
        $content   = str_replace('{use_mdi_windows}', $use_mdi_windows, $content);
        $content   = str_replace('{application}', $ini['general']['application'], $content);
        $content   = str_replace('{user_theme}', $user_theme, $content);
        
        $css       = TPage::getLoadedCSS();
        $js        = TPage::getLoadedJS();
        $content   = str_replace('{HEAD}', $css.$js, $content);
        
        $content = str_replace('{LIBRARIES_USER}', $libraries_user, $content);
        $content = str_replace('{LIBRARIES_BUILDER}', $libraries_builder, $content);
        $content = str_replace('{LIBRARIES_THEME}', $libraries_theme, $content);
        $content = str_replace('{template}', $theme, $content);
        $content = str_replace('{top_menu_var}', $top_menu_var, $content);
        $content = str_replace('{lang}', AdiantiCoreTranslator::getLanguage(), $content);
        $content = str_replace('{debug}', isset($ini['general']['debug']) ? $ini['general']['debug'] : '1', $content);
        $content = str_replace('{verify_messages_menu}', isset($ini['general']['verify_messages_menu']) ? $ini['general']['verify_messages_menu'] : 'false', $content);
        $content = str_replace('{verify_notifications_menu}', isset($ini['general']['verify_notifications_menu']) ? $ini['general']['verify_notifications_menu'] : 'false', $content);
        $content = str_replace('{use_tabs}', $use_tabs, $content);
        $content = str_replace('{store_tabs}', $store_tabs, $content);
        $content = str_replace('{use_mdi_windows}', $use_mdi_windows, $content);
        $content = str_replace('{application}', $ini['general']['application'], $content);

        // Remove all comments
        $content = preg_replace('/<!--.*?-->/s', '', $content);
        
        return $content;
    }

    public static function init($layoutName)
    {
        $ini        = AdiantiApplicationConfig::get();
        $theme      = $ini['general']['theme'];
        $publicName = 'public';
        $loginName  = 'login';

        if (isset($_REQUEST['token_mobile']) || (!isset($_REQUEST['token_mobile']) && TSession::getValue('logged_mobile')))
        {
            $layoutName = 'layout-mobile';
            $publicName = 'public-mobile';
            $loginName = 'login-mobile';
            $theme  = $ini['general']['theme_mobile'];

            try
            {
                if (isset($_REQUEST['token_mobile']) && empty($_REQUEST['token_mobile']))
                {
                    TSession::freeSession();
                }
                else if (! empty($_REQUEST['token_mobile']))
                {
                    BuilderMobileService::initSessionFromToken($_REQUEST['token_mobile']);
                }
            }
            catch (Exception $e)
            {
                new TMessage('erro', $e->getMessage());
                return;
            }
        }


        if ( TSession::getValue('logged') )
        {
            if (isset($_REQUEST['template']) AND $_REQUEST['template'] == 'iframe')
            {
                $content = file_get_contents("app/templates/{$theme}/iframe.html");
            }
            else
            {
                $content = file_get_contents("app/templates/{$theme}/{$layoutName}.html");
            }
        }
        else
        {
            if (isset($ini['general']['public_view']) && $ini['general']['public_view'] == '1')
            {
                $content = file_get_contents("app/templates/{$theme}/{$publicName}.html");
            }
            else
            {
                $content = file_get_contents("app/templates/{$theme}/{$loginName}.html");
            }
        }

        $content = self::parse($content, $theme);
        return $content;
    }
}
