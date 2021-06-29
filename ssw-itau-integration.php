<?php
/**
 * Plugin Name: SSW Integração do Itaú
 * Plugin URI: https://www.santanasolucoesweb.com.br/
 * Description: Provê uma classe para acesso ao Itaú
 * Version: 1.0
 * Author: Vinicius de Santana
 * Author URI: https://www.santanasolucoesweb.com.br/
 */
if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
// Informações do app
define('SSW_ITAUI_PATH', dirname( __FILE__ ) );
define('SSW_ITAUI_URL', plugins_url( '', __FILE__ ) );
define('SSW_ITAUI_PLUGIN_NAME', 'SSW Itau Integra' );
define('SSW_ITAUI_PLUGIN_SLUG', 'ssw-itaui' );
define('SSW_ITAUI_URLHOME', '/wp-admin/admin.php?page='.SSW_ITAUI_PLUGIN_SLUG );
//informações do aplicativo criado no rd
define('SSW_ITAUI_CLIENT_ID', 'ssw-itaui-client-id');
define('SSW_ITAUI_CLIENTE_SECRET', 'ssw-itaui-cliente-secret');
define('SSW_ITAUI_URLCALLBACK', site_url().'/wp-json/ssw-itaui-integration/v1/callback/');
define('SSW_ITAUI_CODE', 'ssw-itaui-code');
define('SSW_ITAUI_ACCESS_TOKEN', 'ssw-itaui-access-token');

include_once SSW_ITAUI_PATH.'/class/index.php';
include_once SSW_ITAUI_PATH.'/api/index.php';
include_once SSW_ITAUI_PATH.'/functions/index.php';

register_activation_hook(__FILE__, 'ssw_itaui_install');
register_uninstall_hook(__FILE__, 'ssw_itaui_uninstall');
//==================================================================
//funções
/**
 * função de instalação do plugin
 */
function ssw_itaui_install(){
	add_option(SSW_ITAUI_CLIENT_ID, '');
	add_option(SSW_ITAUI_CLIENTE_SECRET, '');
	add_option(SSW_ITAUI_CODE, '');
	add_option(SSW_ITAUI_ACCESS_TOKEN, '');
}

/**
 * função de desinstalação do plugin
 */
function ssw_itaui_uninstall(){
	delete_option(SSW_ITAUI_CLIENT_ID);
	delete_option(SSW_ITAUI_CLIENTE_SECRET);
	delete_option(SSW_ITAUI_CODE);
	delete_option(SSW_ITAUI_ACCESS_TOKEN);
}