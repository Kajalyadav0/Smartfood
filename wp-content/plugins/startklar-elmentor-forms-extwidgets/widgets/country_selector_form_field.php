<?php
namespace StartklarElmentorFormsExtWidgets;
use Elementor\Controls_Manager;
use ElementorPro\Modules\Forms\Classes;
use ElementorPro\Modules\Forms\Fields\Field_Base;
use ElementorPro\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class StartklarCountruySelectorFormField extends Field_Base {


	public function get_type() {
		return 'phone_number_prefix_selector_form_field';
	}

	public function get_name() {
		return __( 'Phone number prefix', 'startklar-elmentor-forms-extwidgets' );
	}

    public function __construct() {
        parent::__construct();
        add_action( 'wp_footer', [ $this, 'drawStartklarJsScript' ] );
        add_action( 'wp_head', [ $this, 'getPageHeadersData']);
        wp_enqueue_script( 'select2_min', plugin_dir_url(__DIR__).'assets/country_selector/select2.min.js', array('jquery'), false, true);
        wp_enqueue_style( "startklar_select2_styles", plugin_dir_url(__DIR__)."assets/country_selector/select2.min.css");
        load_theme_textdomain( 'startklar-elmentor-forms-extwidgets', __DIR__. '/../lang' );
    }



    public function update_controls($widget)
    {
        $elementor = Plugin::elementor();
        $control_data = $elementor->controls_manager->get_control_from_stack($widget->get_unique_name(), 'form_fields');
        if (is_wp_error($control_data)) {
            return;
        }

        $content = file_get_contents(__DIR__.'/../assets/country_selector/counties_arr.json');
        $cntr_arr = json_decode($content, true);

        $default_value_options = [""=>""];
        foreach ($cntr_arr  as $country) {
            if (isset($country['phone_code']) && !empty($country['phone_code'])) {
                $t_val = "(" . $country['phone_code'] . ") ";
                $default_value_options[$t_val] = $t_val . __($country['country_name_en'],
                        "startklar-elmentor-forms-extwidgets");
            }
        }
        $temp = 0;

        $field_controls = [
            'phone_numb_format' => [
                'name' => 'phone_numb_format',
                'label' => esc_html__( 'Format +XX or 00XX', 'startklar-elmentor-forms-extwidgets' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'condition' => ['field_type' => $this->get_type()],
                'default' => '',
                'options' => [
                    '' => esc_html__( '+XX', 'startklar-elmentor-forms-extwidgets' ),
                    'old_format' => esc_html__( '00XX', 'startklar-elmentor-forms-extwidgets' ),
                ],

                'tab'          => 'content',
                'inner_tab'    => 'form_fields_content_tab',
                'tabs_wrapper' => 'form_fields_tabs',
            ],


            'display_composition' => [
                'name' => 'display_composition',
                'label' => esc_html__( 'Display Composition', 'startklar-elmentor-forms-extwidgets' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'condition' => ['field_type' => $this->get_type()],
                'default' => 'name_flag',
                'options' => [
                    'name_flag' => esc_html__( 'name and flag', 'startklar-elmentor-forms-extwidgets' ),
                    'flag_only' => esc_html__( 'flag only', 'startklar-elmentor-forms-extwidgets' ),
                    'name_only' => esc_html__( 'name only', 'startklar-elmentor-forms-extwidgets' ),
                    'code only' => esc_html__( 'code only', 'startklar-elmentor-forms-extwidgets' ),
                ],

                'tab'          => 'content',
                'inner_tab'    => 'form_fields_content_tab',
                'tabs_wrapper' => 'form_fields_tabs',
            ],



            'default_value' => [
                'name' => 'default_value',
                'label' => esc_html__( 'Default Value', 'startklar-elmentor-forms-extwidgets' ),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'label_block' => true,
                'multiple' => false,
                'condition' => ['field_type' => $this->get_type()],
                'default' => 'name_flag',
                'options' => $default_value_options,
                'tab'          => 'advanced',
                'inner_tab'    => 'form_fields_advanced_tab',
                'tabs_wrapper' => 'form_fields_tabs',
            ],


        ];
        $control_data['fields'] = $this->inject_field_controls($control_data['fields'], $field_controls);
        $widget->update_control('form_fields', $control_data);
    }

    /**
	 * @param      $item
	 * @param      $item_index
	 * @param Form $form
	 */
	public function render( $item, $item_index, $form ) {

        $widget_styles = "";
        $all_settings = $form->get_settings_for_display();
        $settings = $all_settings["form_fields"][$item_index];
        $dflt_code =  "";
        if (isset($settings["default_value"]) && !empty($settings["default_value"])){
            if (isset($settings["phone_numb_format"]) && $settings["phone_numb_format"]=="old_format"){
                $settings["default_value"] = preg_replace("/\(\+([0-9\s]+?)\)/i", "(00$1)", $settings["default_value"]);
            }
            $dflt_code =  " data-default_val = '".$settings["default_value"]."' ";
        }

        $whitelist = array('phone_numb_format', 'display_composition');
        $settings = array_intersect_key( $settings, array_flip( $whitelist ) );
        $rend_html =  '<select class="startklar_country_selector" name="form_fields['.$item['custom_id'].']" data-options=\''.json_encode($settings).'\' '.$dflt_code.'>';
        $rend_html .=  '</select>';

        $widget_styles = self::generateCorrectiveStyles($all_settings);
        if (!empty($widget_styles)){
            $rend_html .=  '<style>'.$widget_styles.'</style>';
        }

        echo $rend_html;
	}



	public function getPageHeadersData(){

        load_theme_textdomain( 'startklar-elmentor-forms-extwidgets', __DIR__. '/../languages' );

        $rend_html =  '<option selected value></option>';
        $content = file_get_contents(__DIR__.'/../assets/country_selector/counties_arr.json');
        $cntr_arr = json_decode($content, true);
        $slctd_country = false;
        if (isset($item["field_value"]) && !empty($item["field_value"])){
            if (preg_match("/(\(.*\))?(.+)/ism", $item["field_value"], $matches)){
                $slctd_country = trim($matches[2]);
            }
        }
        foreach ($cntr_arr  as $country) {
            $temp = "";
            if(isset($country['phone_code']) && !empty($country['phone_code'])){
                $temp = "(".$country['phone_code'].") ";
            }
            $t_val = $temp."<span class='country_name'>".__($country['country_name_en'], "startklar-elmentor-forms-extwidgets")."</span>";



            $selected  = '';
            if ($slctd_country == $country['country_name_en']){
                $selected  = 'selected="selected"';
            }
            $icon_code ="";
            if(isset($country['icon']) && !empty($country['icon'])){
                $icon_code = '" data-icon="'.plugin_dir_url(__DIR__).'assets/country_selector'.$country['icon'];
            }
            $rend_html .=  '<option  data-country_en="'.esc_html($country['country_name_en']).'" 
                value="'.$temp.$icon_code.'" '.$selected.'>'.esc_html($t_val).'</option>';
        }
        $rend_html = str_replace([ "`", "’"], ["\`","\’"], $rend_html);

        echo <<<EOT
        <script>
            window.phone_number_prefix_selector_options = `{$rend_html}`;
        </script>
        <style>
            .select2-container .select2-selection img,
            .select2-container .select2-results__option img { width: 50px; vertical-align: middle; padding: 0px;  -webkit-box-shadow: 2px 2px 7px 1px rgba(34, 60, 80, 0.2);
                            -moz-box-shadow: 2px 2px 7px 1px rgba(34, 60, 80, 0.2); box-shadow: 2px 2px 7px 1px rgba(34, 60, 80, 0.2); margin: 0px 12px 0 0;}
            .select2-container .selection  { padding: 0px; display: inherit;    width: 100%; } 
            .select2-container .select2-selection,
            .select2-container .select2-results__option{  padding: 0 6px; color:#777; font-family: arial;}
            select.startklar_country_selector { width: 100%; } 
            .elementor-field-type-country_selector_form_field { display: block; }
            .select2.select2-container--default .select2-selection--single .select2-selection__arrow b {
                    border-color: #c7c7c7 transparent transparent transparent; border-style: solid; border-width: 16px 10px 0 10px;
                    height: 0; left: 50%;  margin-left: -22px; margin-top: 3px; position: absolute;  top: 50%;  width: 0; }
            .select2.select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow b {
                    border-color: transparent transparent #c7c7c7 transparent;   border-width: 0 10px 16px 10px;}
            .select2-selection--single   #select2-startklar_country_selector-container { height: 34px; }
               .select2-results__options li { margin: 10px 0 0 0; }  
            .select2.select2-container--default .select2-search--dropdown .select2-search__field { margin:0; }
            .select2 .select2-selection.select2-selection--single { padding: 5px 0 0 0;  height: 48px;}  
            .select2-container > .select2-dropdown { width: 370px !important; }
            .select2-container .select2-dropdown.hide_counties_names .select2-results__option span.country_name { display: none; }
        </style>
EOT;
    }



    public function drawStartklarJsScript(){
            $site_abs_url = get_site_url();
            ?>
            <script>

                testjstartklarCountrySelectorQueryExist() ;
                function testjstartklarCountrySelectorQueryExist() {
                    if (window.jQuery) {

                        jQuery(window).on('elementor/frontend/init', function() {
                            if (typeof(elementor) !== "undefined") {
                                elementor.hooks.addFilter('elementor_pro/forms/content_template/field/phone_number_prefix_selector_form_field', function (inputField, item, i, settings) {
                                    var widget_id = item._id;
                                    var setting = "";
                                    settings.form_fields.forEach(sett_obj => {
                                        if (sett_obj._id == widget_id) {
                                            setting = sett_obj;
                                        }
                                    });

                                    if (setting && (typeof(setting.display_composition) !== "undefined")){
                                        var json_text = JSON.stringify({ display_composition: setting.display_composition, phone_numb_format: setting.phone_numb_format });
                                        var ret_str = `<select class="startklar_country_selector" name="${item.custom_id}"  data-options="`+json_text+`"></select>`;
                                    }else{
                                        var ret_str = `<select class="startklar_country_selector" name="${item.custom_id}"></select>`;
                                    }
                                    return ret_str;
                                }, 10, 4);
                            }

                            elementorFrontend.hooks.addAction('frontend/element_ready/form.default', function(){
                                if ( jQuery(".startklar_country_selector").length){
                                    var t_selector_options = "";
                                    if (typeof(window.phone_number_prefix_selector_options) !== "undefined") {
                                        var setting = jQuery(".startklar_country_selector").data("options");
                                        if (typeof(setting.phone_numb_format) !== "undefined" &&  setting.phone_numb_format == "old_format"){
                                            t_selector_options =  window.phone_number_prefix_selector_options.replace(/\(\+([0-9\s]+?)\)/gi, '(00$1)');
                                        }else{
                                            t_selector_options =  window.phone_number_prefix_selector_options;
                                        }

                                        if (typeof(setting.display_composition) !== "undefined" &&  (setting.display_composition.toLowerCase().indexOf("flag") == -1)){
                                            t_selector_options =  t_selector_options.replace(/data-icon="[^"]*"/gi, '');
                                        }

                                        if (typeof(setting.display_composition) !== "undefined" &&  (setting.display_composition.toLowerCase().indexOf("name") == -1)){
                                            //t_selector_options =  t_selector_options.replace(/\)[^>]+?<\/option>/gi, ')</option>');
                                            jQuery(".startklar_country_selector").addClass("hide_counties_names");

                                        }

                                    }else{
                                        t_selector_options = "";
                                    }




                                    jQuery(".startklar_country_selector").html(t_selector_options);
                                    var default_val = jQuery(".startklar_country_selector").data("default_val");
                                    if (default_val){
                                        jQuery(".startklar_country_selector").val(default_val);
                                    }

                                    jQuery('.startklar_country_selector').select2({
                                        //allowClear: true,
                                        templateSelection: startklarCountrySelectorformatText,
                                        templateResult: startklarCountrySelectorRsltformatText,
										selectionTitleAttribute: false
                                    });
									clearSelectTitle();
									 jQuery(document).on('change.select2', () => {
										clearSelectTitle();
									});
                                    jQuery('.select2-selection.select2-selection--single').addClass("elementor-field-textual");
                                    jQuery('.select2.select2-container  .select2-selection .select2-selection__rendered span').addClass("elementor-field");


                                    jQuery(document).on('select2:open', () => {
                                            document.querySelector('.select2-search__field').focus();
                                            if (jQuery(".startklar_country_selector.hide_counties_names").length){
                                                jQuery(".select2-container .select2-dropdown").addClass("hide_counties_names");
                                            }
                                        });

                                    var p_form = jQuery(".startklar_country_selector").closest("form");
                                    if ( typeof p_form !== "undefined" ) {
                                        jQuery(p_form).on('submit_success', function () {
                                            jQuery('.startklar_country_selector', this).val('').trigger("change");
                                        });
                                    }

                                    var default_val = jQuery(".startklar_country_selector").data("default_val");

                                    if (!default_val){
                                        jQuery.post( "<?php echo $site_abs_url; ?>/wp-admin/admin-ajax.php?action=startklar_country_selector_process", function( data ) {
                                            if(typeof data["country"] !== "undefined"){
                                                jQuery('.startklar_country_selector  > option').each(function() {
                                                    var country_en = jQuery(this).data("country_en");
                                                    if ( typeof country_en !=="undefined" && country_en.includes(data["country"])){
                                                        jQuery('.startklar_country_selector').val(this.value).trigger('change');
                                                    }
                                                });
                                            }
                                        },"json");
                                    }

                                }

                            });

                        });

                    }else{
                        setTimeout(testjstartklarCountrySelectorQueryExist, 100);
                    }
                }


                function startklarCountrySelectorformatText (icon) {
                    var str = "";
                    if(typeof icon.element !== "undefined") {
                        var phone_code = /\(.+\)/g.exec(icon.text);
                        var icon_src = jQuery(icon.element).data('icon');
                        var icon_code = '';
                        if (typeof icon_src !== "undefined" && icon_src.length){
                            icon_code = '<img src="'+icon_src+'">';
                        }
                        if (typeof phone_code !== "undefined" && phone_code != null && phone_code.length ) {
                            str = '<span>' + icon_code + phone_code[0] + '</span>';
                        }
                    }
                    return jQuery(str);
                };

                function startklarCountrySelectorRsltformatText (icon) {
                    if(typeof icon.element !== "undefined") {
                        var icon_src = jQuery(icon.element).data('icon');
                        var icon_code = '';
                        if (typeof icon_src !== "undefined" && icon_src.length){
                            icon_code = '<img src="'+icon_src+'">';
                        }
                        var str = '<span>'+icon_code+icon.text+'</span>';
                    }
                    return jQuery(str);
                };
				function clearSelectTitle(){
					var select_items = jQuery(".select2.select2-container  .select2-selection.select2-selection--single > .select2-selection__rendered")
					if(select_items.length>0){
						jQuery(select_items).each(function( index ) {
							var select_itm = jQuery(this);
							var title = select_itm.attr("title");
							if (typeof title !== 'undefined'  &&  title) {
								var rslt = title.match(/([^<]+)<span[^>]*>([^<]+)<\/span>/ism);
								if (rslt !== null){
									var new_title = rslt[1]+" "+rslt[2];
									select_itm.attr("title", new_title);
								}
							}
						});	
					}
				}
            </script>
            <?php

    }

    static function  generateCorrectiveStyles($settings){
        $widget_styles = "";
        if (isset($settings["field_text_color"]) &&  !empty($settings["field_text_color"])){
            $widget_styles .=  "\n.select2.select2-container  .select2-selection .select2-selection__rendered span { color: ".$settings["field_text_color"]." ;} ";
        }

        if (isset($settings["field_border_radius"]) &&  !empty($settings["field_border_radius"])){
            $border_code =  $settings["field_border_radius"]["top"].$settings["field_border_radius"]["unit"]." ".
                            $settings["field_border_radius"]["right"].$settings["field_border_radius"]["unit"]." ".
                            $settings["field_border_radius"]["bottom"].$settings["field_border_radius"]["unit"]." ".
                            $settings["field_border_radius"]["left"].$settings["field_border_radius"]["unit"];
            $widget_styles .=  "\n.select2.select2-container  .select2-selection.select2-selection--single { border-radius: ".$border_code." ;} ";
        }


        if (isset($settings["field_border_color"]) &&  !empty($settings["field_border_color"])){
            $widget_styles .=  "\n.select2.select2-container  .select2-selection .select2-selection__rendered span { border-color: ".$settings["field_border_color"]." ;} ";
        }

        if (isset($settings["field_typography_font_size"]) &&  !empty($settings["field_typography_font_size"])){
            $widget_styles .=  "\n.select2.select2-container  .select2-selection .select2-selection__rendered span { font-size: ".
                    $settings["field_typography_font_size"]["size"].$settings["field_typography_font_size"]["unit"]." ;} ";
        }

        if (isset($settings["field_border_width"]) &&  !empty($settings["field_border_width"])){
            $border_code =  $settings["field_border_width"]["top"].$settings["field_border_width"]["unit"]." ".
                $settings["field_border_width"]["right"].$settings["field_border_width"]["unit"]." ".
                $settings["field_border_width"]["bottom"].$settings["field_border_width"]["unit"]." ".
                $settings["field_border_width"]["left"].$settings["field_border_width"]["unit"];
            $widget_styles .=  "\n.select2.select2-container  .select2-selection.select2-selection--single { border-width: ".$border_code." ;} ";
        }
        if (isset($settings["field_typography_font_family"]) &&  !empty($settings["field_typography_font_family"])){
            $widget_styles .=  "\n.select2.select2-container  .select2-selection .select2-selection__rendered span { font-family: ".
                $settings["field_typography_font_family"]." ;} ";
        }

        if (isset($settings["field_typography_font_weight"]) &&  !empty($settings["field_typography_font_weight"])){
            $widget_styles .=  "\n.select2.select2-container  .select2-selection .select2-selection__rendered span { font-weight: ".
                $settings["field_typography_font_weight"]." ;} ";
        }

        if (isset($settings["html_typography_line_height"]) &&  !empty($settings["html_typography_line_height"])){
            $widget_styles .=  "\n.select2.select2-container  .select2-selection .select2-selection__rendered span { line-height: ".
                $settings["html_typography_line_height"]["size"].$settings["html_typography_line_height"]["unit"]." ;} ";
        }

        if (isset($settings["field_background_color"]) &&  !empty($settings["field_background_color"])){
            $widget_styles .=  "\n.select2.select2-container  .select2-selection.select2-selection--single  { background-color: ".$settings["field_background_color"]." ;} ";
            $widget_styles .=  "\n.select2.select2-container  .select2-selection .select2-selection__rendered span { background-color: ".$settings["field_border_color"]." ;} ";
        }

        return $widget_styles;
    }
}