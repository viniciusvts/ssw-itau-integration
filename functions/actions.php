<?php
//adiocionar admin menu
add_action ('admin_menu', 'sswitauiMainAdminPage');
// para pegar a url de uma página
/* menu_page_url( string $menu_slug, bool $echo = true ) */
function sswitauiMainAdminPage()
{
	add_menu_page(
		SSW_ITAUI_PLUGIN_NAME,
		SSW_ITAUI_PLUGIN_NAME,
		'manage_options',
		SSW_ITAUI_PLUGIN_SLUG,
		'sswitauiReturnMainPage',
		'dashicons-admin-settings',
		150
	);
	/*
	add_submenu_page( string $parent_slug, 
					string $page_title, 
					string $menu_title, 
					string $capability, 
					string $menu_slug, 
					callable $function = '', 
					int $position = null )
	*/
	add_submenu_page( 
		SSW_ITAUI_PLUGIN_SLUG, 
		SSW_ITAUI_PLUGIN_NAME.'Autorização', 
		'Autorização', 
		'manage_options',
		SSW_ITAUI_PLUGIN_SLUG.'-auth', 
		'sswitauiReturnAuthPage', 
		1
	);
}
function sswitauiReturnMainPage(){
	include SSW_ITAUI_PATH."/views/template/index.php";
}
function sswitauiReturnAuthPage(){
	include SSW_ITAUI_PATH."/views/template/auth.php";
}