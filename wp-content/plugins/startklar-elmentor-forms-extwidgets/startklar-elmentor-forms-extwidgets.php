<?php
namespace StartklarElmentorFormsExtWidgets;
/*
@link https://web-shop-hosting.com/
Plugin Name: Startklar Elmentor Addons
Plugin URI: https://web-shop-hosting.com/
Description: Plugin adds additional fields like country code selector, Advanced Honeypot and DropZone to Elementor Forms.
Version: 1.7.7
Author:  WEB-SHOP-HOSTING
Author URI: https://web-shop-hosting.com/
Requires at least: 5.6
Requires PHP: 5.6.20
Text Domain: startklar-elmentor-forms-extwidgets
Domain Path: /languages
*/


use StartklarElmentorFormsExtWidgets\StartklarCountruySelectorFormField;

register_activation_hook( __FILE__, array( "StartklarElmentorFormsExtWidgets\StartklarCheck_Plugin_Dependencies", 'on_activation' ));


require_once(__DIR__."/plugin_admin_page.php");
require_once(__DIR__."/startklarCountrySelectorProcess.php");

$admin_page  =  new StartklarPluginAdminPage();


//===== geolocation
require_once(__DIR__."/startklarCountrySelectorProcess.php");
add_action( 'wp_ajax_startklar_country_selector_process',array( "StartklarElmentorFormsExtWidgets\startklarCountrySelectorProcess", 'process' ) );
add_action( "wp_ajax_nopriv_startklar_country_selector_process",array( "StartklarElmentorFormsExtWidgets\startklarCountrySelectorProcess", 'process' ) );
add_action( 'elementor_pro/init', function(){
    include_once( __DIR__.'/widgets/country_selector_form_field.php' );
    new StartklarCountruySelectorFormField();
} );
//=====
require_once(__DIR__ . "/startklarDropZoneUploadProcess.php");
add_action( 'wp_ajax_startklar_drop_zone_upload_process',array( "StartklarElmentorFormsExtWidgets\startklarDropZoneUploadProcess", 'process' ) );
add_action( "wp_ajax_nopriv_startklar_drop_zone_upload_process",array( "StartklarElmentorFormsExtWidgets\startklarDropZoneUploadProcess", 'process' ) );
add_action( 'elementor_pro/init', function(){
    include_once( __DIR__.'/widgets/dropzone_form_field.php' );
    new StartklarDropzoneFormField();
} );
//=====
add_action( 'elementor_pro/init', function(){
    include_once( __DIR__.'/widgets/honeypot_form_field.php' );
    new StartklarHoneyPotFormField();
} );


class StartklarCheck_Plugin_Dependencies{
    public static function on_activation()
    {

        if ( current_user_can( 'activate_plugins' )){
            if (!class_exists('ElementorPro\Plugin') || !class_exists('Elementor\Plugin')) {
                // Deactivate the plugin.
                deactivate_plugins(plugin_basename(__FILE__));
                // Throw an error in the WordPress admin console.
                $error_message = '<p style="font-family:-apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Oxygen-Sans,Ubuntu,Cantarell,\'Helvetica Neue\',
                    sans-serif;font-size: 13px;line-height: 1.5;color:#444;">' . esc_html__('This plugin requires ', 'startklar-elmentor-forms-extwidgets') .
                    '<a href="' . esc_url('https://elementor.com/pricing/#features') . '">"ELEMENTOR" AND "ELEMENTOR PRO"</a>' .
                    esc_html__(' plugins to be active.', 'startklar-elmentor-forms-extwidgets') . '</p>';
                die($error_message); // WPCS: XSS ok.
            }
        }
    }
}


/**
 * Class Elementor_Form_Email_Attachments
 *
 * Send Elementor Form upload field as attachments to email
 */
class StartklarElementor_Form_Email_Attachments {
    public $attachments_array = [];
    public function __construct() {
        add_action( 'elementor_pro/forms/process', [ $this, 'init_form_email_attachments' ], 11, 2 );
    }
    /**
     * @param \ElementorPro\Modules\Forms\Classes\Form_Record $record
     * @param \ElementorPro\Modules\Forms\Classes\Ajax_Handler $ajax_handler
     */
    public function init_form_email_attachments( $record, $ajax_handler ) {
        $this->attachments_array = [];
        $fields = $record->get_field( "" );
        $home_url = get_option( 'home' );
        $home_path = get_home_path();
        foreach($fields as $field){
            if ($field["type"] == "drop_zone_form_field"){
                $t_array = explode(",", $field["value"]);
                foreach($t_array as $item){
                    $item = str_replace($home_url, $home_path, $field["value"]);
                    $item = str_replace("//","/",$item);
                    $item = str_replace("//","/",$item);
                    $this->attachments_array[] = $item;
                }
            }
        }
        if ( 0 < count( $this->attachments_array ) ) {
            add_filter( 'wp_mail', [ $this, 'wp_mail' ] );
            add_action( 'elementor_pro/forms/new_record', [ $this, 'remove_wp_mail_filter' ], 5 );
        }
    }
    public function remove_wp_mail_filter() {
        $this->attachments_array = [];
        remove_filter( 'wp_mail', [ $this, 'wp_mail' ] );
    }
    public function wp_mail( $args ) {
        $args['attachments'] = $this->attachments_array;
        return $args;
    }
}
new StartklarElementor_Form_Email_Attachments();