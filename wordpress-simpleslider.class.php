<?php

    class WordpressSimpleslider {

        private $metaboxName = "simpleslider_metabox";

        private $metaboxMainTextFieldName = "simpleslider_main_text_field";
        private $metaboxSecondaryTextFieldName = "simpleslider_secondary_text_field";
        private $metaboxButtonTextFieldName = "simpleslider_button_text_field";
        private $metaboxSliderLinkFieldName = "simpleslider_slider_link_field";
        private $metaboxNewTabFieldName = "simpleslider_new_tab_field";
        private $metaboxButtonTextColor = "simpleslider_button_text_color_field";
        private $metaboxDesktopBackgroundImageFieldName = "simpleslider_desktop_background_image_field";
        private $metaboxMobileBackgroundImageFieldName = "simpleslider_mobile_background_image_field";
        private $metaboxTemplate = "simpleslider_template_field";
        private $metaboxEnableSVG = "simpleslider_svg_field";
        private $metaboxSVGPosition = "simpleslider_svg_position_field";
        private $metaboxButtonClass = "simpleslider_button_class_field";

        private $metaboxTextColor = "simpleslider_text_color";
        private $metaboxButtonColor = "simpleslider_btn_color";

        private $configPageSlug = "simpleslider_config_page";
        private $configSectionSlug = "simpleslider_config_section";
        private $configGroupSlug = "simpleslider_config_group";

        private $optionLoadSlick = "simpleslider_load_slick";
        private $optionButtonsClass = "simpleslider_buttons_class";
        private $optionActiveDotColor = "simpleslider_active_dot_color";
        private $optionImageWidth = "simpleslider_image_width";
        private $optionImageHeight = "simpleslider_image_height";
        private $optionBlackGradient = "simpleslider_black_gradient";
        private $optionEnableSVG = "simpleslider_enable_svg";

        function __construct() {            
            register_activation_hook( WORDPRESS_SIMPLESLIDER_FILE, array( &$this, 'activate' ) );

            add_action( 'init', array( &$this, 'registerSliderCPT' ) );
            add_action( 'admin_menu', array( &$this, 'generalConfigPage' ) );
            add_action( 'add_meta_boxes', array( &$this, 'createMetabox' ) );
            add_action( 'admin_enqueue_scripts', array( &$this, 'adminEnqueueScripts' ) );
            add_action( 'wp_enqueue_scripts', array( &$this, 'userEnqueueScripts' ) );
            add_action( 'save_post', array( &$this, 'saveFieldValues' ), 10, 2 );
            
            add_filter( 'manage_'.WORDPRESS_SIMPLESLIDER_POST_TYPE.'_posts_columns', array( &$this, 'setShortcodeColumn' ) );
            add_action( 'manage_'.WORDPRESS_SIMPLESLIDER_POST_TYPE.'_posts_custom_column' , array( &$this, 'showShortcodeInColumn' ), 10, 2 );

            add_shortcode( 'simpleslider', array( &$this, 'showSliders' ) );
        }

        function getSliderMeta( $post_id ) {            
            $dados = array(
                'main_text'                 => get_post_meta( $post_id, $this->metaboxMainTextFieldName, true ),
                'secondary_text'            => get_post_meta( $post_id, $this->metaboxSecondaryTextFieldName, true ),
                'button_text'               => get_post_meta( $post_id, $this->metaboxButtonTextFieldName, true ),
                'slider_link'               => get_post_meta( $post_id, $this->metaboxSliderLinkFieldName, true ),
                'new_tab'                   => get_post_meta( $post_id, $this->metaboxNewTabFieldName, true ),
                'text_color'                => get_post_meta( $post_id, $this->metaboxTextColor, true ),
                'button_class'              => get_post_meta( $post_id, $this->metaboxButtonClass, true ),
                'text_align'                => get_post_meta( $post_id, $this->metaboxTemplate, true ),
                'button_color'              => get_post_meta( $post_id, $this->metaboxButtonColor, true ),
                'button_text_color'         => get_post_meta( $post_id, $this->metaboxButtonTextColor, true ),
                'desktop_background_image'  => get_post_meta( $post_id, $this->metaboxDesktopBackgroundImageFieldName, true ),
                'mobile_background_image'   => get_post_meta( $post_id, $this->metaboxMobileBackgroundImageFieldName, true ),
                'svg'                       => get_post_meta( $post_id, $this->metaboxEnableSVG, true ),
                'svg_position'              => get_post_meta( $post_id, $this->metaboxSVGPosition, true ),
            );

            $indexes = [];
            if ( is_array($dados['main_text']) ) {
                foreach ($dados['main_text'] as $key => $value) {
                    array_push($indexes, $key);
                }
            }
            
            $array = [];
            foreach ($indexes as $index) {
                foreach ($dados as $key => $dado ) {
                    $array[$index][$key] = isset( $dado[$index] ) ? $dado[$index] : "";          
                }

                $array[$index]['image_html'] = $this->getSlideHTML( $array[$index]['desktop_background_image'], $array[$index]['mobile_background_image'] );
                $array[$index]['id'] = $index;
            }   

            return $array;
        }
        function getSlideHTML( $desktop_id, $mobile_id ) {
            $slider = wp_get_attachment_image( $desktop_id, 'full' );
                                    
            $dekstop_meta = wp_get_attachment_metadata( $desktop_id );
            $mobile_meta = wp_get_attachment_metadata( $mobile_id );
            
            if ($mobile_meta) {
            $slider = '
                <picture>
                    <source 
                        srcset="' . wp_get_attachment_url( $mobile_id ) . '"
                        width="' . $mobile_meta["width"] . '"
                        height="' . $mobile_meta["height"] . '"
                        media="(max-width: 767px)"
                        alt=""
                    >
                    <img 
                        src="' . wp_get_attachment_url( $desktop_id ) . '"
                        width="' . $dekstop_meta["width"] . '"
                        height="' . $dekstop_meta["height"] . '"
                        alt=""
                    >
                ';
            }
                    
            $slider .= '</picture>';

            return $slider;
        }

        // All Config
        function activate() {
            $this->setDefaultConfig();
        }
        function setDefaultConfig() {
            update_option( $this->optionLoadSlick, 'true' );
            update_option( $this->optionActiveDotColor, '#FFFFFF' );
            update_option( $this->optionImageWidth, '1920' );
            update_option( $this->optionImageHeight, '800' );
        }
        function adminEnqueueScripts() {
            // js
            wp_enqueue_script( 'admin-simpleslider-js', WORDPRESS_SIMPLESLIDER_URL . 'assets/scripts/admin-simpleslider-script-min.js', false, "1.0.0", true );    
            wp_enqueue_media();

            add_action( 'admin_head', function() {
                echo "<script>
    
                    var simpleSliderEmptyLineHTML = `";
                    
                        $this->showPostLine( false, true );

                echo "`;
                </script>";
            });
            
            // stylesheet
            wp_enqueue_style( 'admin-simpleslider-css', WORDPRESS_SIMPLESLIDER_URL . 'assets/styles/admin-simpleslider-style.css', array(), "1.0.0", 'all' );
        }
        function showInputTypeText( $optionName, $value, $required, $array = false, $position = false ) {

            $pos = $array ? '['.( is_numeric( $position ) ? $position : '' ).']' : '';

            printf(
                '<input type="text" id="wp-simpleslider-option-field-%s" name="%s%s" value="%s" %s/>',
                $optionName,                 
                $optionName, 
                $pos,
                esc_attr( $array && !empty($value) ? $value[ $position ] : $value ),
                $required ? 'required' : ''
            );

        }
        function showInputTypeTextarea( $optionName, $value, $required, $array = false, $position = false ) {

            $pos = $array ? '['.( is_numeric( $position ) ? $position : '' ).']' : '';

            printf(
                '<textarea id="wp-simpleslider-option-field-%s" name="%s%s" %s/>%s</textarea>',
                $optionName,                 
                $optionName,
                $pos,
                $required ? 'required' : '',
                esc_attr( $array && !empty($value) ? $value[ $position ] : $value )
            );

        }
        function showInputTypeNumber( $optionName, $value, $required, $array = false, $position = false ) {

            $pos = $array ? '['.( is_numeric( $position ) ? $position : '' ).']' : '';

            printf(
                '<input type="number" id="wp-simpleslider-option-field-%s" name="%s%s" value="%s" %s/>',
                $optionName,                 
                $optionName, 
                $pos,
                esc_attr( $array && !empty($value) ? $value[ $position ] : $value ),
                $required ? 'required' : ''
            );

        }
        function showInputTypeColor( $optionName, $value, $required, $array = false, $position = false ) {

            $pos = $array ? '['.( is_numeric( $position ) ? $position : '' ).']' : '';
            $val = esc_attr( $array && !empty($value) ? $value[ $position ] : $value );

            printf(
                '<div class="color-preview" id="wp-simpleslider-option-field-%s" style="background: %s"/></div><input type="text" color-target="wp-simpleslider-option-field-%s" class="aux-color" name="%s%s" value="%s" %s/>',
                $optionName,
                $val,   
                $optionName,              
                $optionName, 
                $pos,
                $val,
                $required ? 'required' : ''
            );

        }
        function showInputTypeImage( $optionName, $value, $required, $array = false, $position = false ) {
            
            $pos = $array ? '['.( is_numeric( $position ) ? $position : '' ).']' : '';
            $val = esc_attr( $array && !empty($value) ? $value[ $position ] : $value );

            printf(
                '<input type="text" style="width: 0; height: 0; padding: 0; border: 1px solid transparent;" id="wp-simpleslider-option-field-%s" name="%s%s" value="%s" %s/><span class="button button-primary select-image" data-target="%s">Selecionar imagem</span><span class="button button-primary update-image" data-target="%s">Atualizar imagem</span><span class="button remove-image" data-target="%s">Remover imagem</span><img class="image-preview" src="%s"/>',
                $optionName,                
                $optionName,
                $pos,
                $val,
                $required ? 'required' : '',
                $optionName.$pos,
                $optionName.$pos,
                $optionName.$pos,
                ( $val ? wp_get_attachment_image_url( $val, 'full' ) : '' )
            );

        }  
        function showInputTypeCheckbox( $optionName, $value, $required, $array = false, $position = false ) {

            $pos = $array ? '['.( is_numeric( $position ) ? $position : '' ).']' : '';

            printf(
                '<input type="checkbox" id="wp-simpleslider-option-field-%s" name="%s%s" %s %s/>',
                $optionName,                 
                $optionName,
                $pos, 
                esc_attr( $array && !empty($value) ? $value[ $position ] : $value ) ? 'checked="checked"' : '',
                $required ? 'required' : ''
            );

        }
        function showInputTypeRadio( $optionName, $value, $required, $array = false, $position = false, $options = array() ) {
            $pos = $array ? '['.( is_numeric( $position ) ? $position : '' ).']' : '';
            $val = esc_attr( $array && !empty($value) ? $value[ $position ] : $value );
            $first = true;

            foreach ($options as $key => $desc) {

                $fieldID = 'wp-simpleslider-option-field-'.$optionName.$pos.'['.$key.']';

                printf(
                    '<div class="radio-container"><input type="radio" id="%s" name="%s%s" value="%s" %s %s/><label for="%s">%s</label></div>',
                    $fieldID,
                    $optionName,
                    $pos,
                    $key,
                    (($val == $key) || (!$val && $first)) ? 'checked="checked"' : '', // se o valor for igual do banco ou se não tiver valor marca a primeira opção
                    $required ? 'required' : '',
                    $fieldID,
                    $desc
                );

                $first = false;
            }

        }
        function setShortcodeColumn( $columns ) {
            unset( $columns['date'] );
            $columns['shortcode'] = "Shortcode";

            return $columns;
        }
        function showShortcodeInColumn( $column, $post_id ) {

            $title = get_the_title( $post_id );


            if ( $column == 'shortcode' ) {

                echo $this->getShortcode( $post_id, $title );

            }

        }
        function getShortcode( $post_id, $post_title = false ) {
            return '[simpleslider id="'.$post_id.'" title="'.$post_title.'"]';
        }

        // General config
        function generalConfigPage() {
            add_submenu_page(
                'edit.php?post_type='.WORDPRESS_SIMPLESLIDER_POST_TYPE,
                'Simpleslider Config',
                'Configurações',
                'manage_options',
                $this->configPageSlug,
                array( &$this, 'showGeneralConfigPage' )
            );

            $this->createOptions();
        }
        function showGeneralConfigPage() {
            echo '<div id="simpleslider_metabox" class="wrap simpleslider-configuracoes">
                    <img width="150" height="150" class="dashboard-image" src="'.WORDPRESS_SIMPLESLIDER_URL.'assets/images/admin-dashboard.svg" alt="Menu administrativo"/>

                    <h1>Simpleslider</h1>

                    <form method="post" action="options.php">';
                            
                        settings_fields( $this->configGroupSlug );
                        do_settings_sections( $this->configPageSlug );
                        submit_button();
            echo '
                    </form>
                </div>';
        }
        function createOptions() {
            add_settings_section( $this->configSectionSlug, '', '', $this->configPageSlug );

            register_setting( $this->configGroupSlug, $this->optionLoadSlick );
            add_settings_field(
                $this->optionLoadSlick,
                "Carregar slick.js",
                array($this, 'showOptionLoadSlickCheckbox'),
                $this->configPageSlug,
                $this->configSectionSlug,       
                array( 
                    'label_for' => $this->optionLoadSlick
                )
            );  

            register_setting( $this->configGroupSlug, $this->optionBlackGradient );
            add_settings_field(
                $this->optionBlackGradient,
                "Gradiente preto",
                array($this, 'showOptionBlackGradient'),
                $this->configPageSlug,
                $this->configSectionSlug,       
                array( 
                    'label_for' => $this->optionBlackGradient
                )
            ); 

            register_setting( $this->configGroupSlug, $this->optionEnableSVG );
            add_settings_field(
                $this->optionEnableSVG,
                "Habilitar campo de SVG",
                array($this, 'showOptionEnableSVG'),
                $this->configPageSlug,
                $this->configSectionSlug,       
                array( 
                    'label_for' => $this->optionEnableSVG
                )
            );

            register_setting( $this->configGroupSlug, $this->optionActiveDotColor );
            add_settings_field(
                $this->optionActiveDotColor,
                "Cor ponto para o slide ativo",
                array($this, 'showOptionActiveDotColor'),
                $this->configPageSlug,
                $this->configSectionSlug,       
                array( 
                    'label_for' => $this->optionActiveDotColor
                )
            );  

            register_setting( $this->configGroupSlug, $this->optionButtonsClass );
            add_settings_field(
                $this->optionButtonsClass,
                "Classe para todos os botões",
                array($this, 'showOptionButtonsClass'),
                $this->configPageSlug,
                $this->configSectionSlug,       
                array( 
                    'label_for' => $this->optionButtonsClass
                )
            );  

            register_setting( $this->configGroupSlug, $this->optionImageWidth );
            add_settings_field(
                $this->optionImageWidth,
                "Largura da imagem (px)",
                array($this, 'showOptionImageWidth'),
                $this->configPageSlug,
                $this->configSectionSlug,       
                array( 
                    'label_for' => $this->optionImageWidth
                )
            ); 

            register_setting( $this->configGroupSlug, $this->optionImageHeight );
            add_settings_field(
                $this->optionImageHeight,
                "Altura da imagem (px)",
                array($this, 'showOptionImageHeight'),
                $this->configPageSlug,
                $this->configSectionSlug,       
                array( 
                    'label_for' => $this->optionImageHeight
                )
            ); 
            
              
        }
        function showOptionLoadSlickCheckbox() {
            $this->showInputTypeCheckbox( $this->optionLoadSlick, get_option( $this->optionLoadSlick ), false );
        }
        function showOptionActiveDotColor() {
            $this->showInputTypeColor( $this->optionActiveDotColor, get_option( $this->optionActiveDotColor ), false );
        }
        function showOptionButtonsClass() {
            $this->showInputTypeText( $this->optionButtonsClass, get_option( $this->optionButtonsClass ), false );
        }
        function showOptionImageWidth() {
            $this->showInputTypeNumber( $this->optionImageWidth, get_option( $this->optionImageWidth ), true );
        }
        function showOptionImageHeight() {
            $this->showInputTypeNumber( $this->optionImageHeight, get_option( $this->optionImageHeight ), true );
        }
        function showOptionBlackGradient() {
            $this->showInputTypeCheckbox( $this->optionBlackGradient, get_option( $this->optionBlackGradient ), false );
        }
        function showOptionEnableSVG() {
            $this->showInputTypeCheckbox( $this->optionEnableSVG, get_option( $this->optionEnableSVG ), false );
        }
        function getOptions() {
            $config = array(
                "load_slick" => get_option( $this->optionLoadSlick ),
                "buttons_class" => get_option( $this->optionButtonsClass ),
                "slick_active_dot" => get_option( $this->optionActiveDotColor ),
                "image_width" => get_option( $this->optionImageWidth ),
                "image_height" => get_option( $this->optionImageHeight ),
                "black_gradient" => get_option( $this->optionBlackGradient ),
                "enable_svg" => get_option( $this->optionEnableSVG )
            );
            
            /* Padding */
            $razao = ( $config['image_height'] / $config['image_width'] );
            $config = array_merge( $config, array(
                "padding_desktop" => $razao * 100,
                "padding_tablet" => $razao * 180,
                "padding_mobile" => $razao * 320
            ) );

            return $config;
        }

        // Post type config
        function registerSliderCPT() {
            register_post_type(WORDPRESS_SIMPLESLIDER_POST_TYPE,
                array('labels' => array(
                        'name'						=> 'Sliders',
                        'singular_name' 			=> 'Slider',
                        'all_items' 				=> 'Todos os sliders',
                        'add_new' 					=> 'Adicionar',
                        'add_new_item'				=> 'Adicionar slider',
                        'edit'						=> 'Editar',
                        'edit_item' 				=> 'Editar slider',
                        'new_item'					=> 'Novo slider',
                        'view_item' 				=> 'Ver slider',
                        'search_items'				=> 'Procurar slider',
                        'not_found' 				=> 'Slider não encontrado',
                        'not_found_in_trash'	    => 'Nada encontrado na lixeira',
                    ),
                    'description' 				=>  'Sliders presentes na home',
                    'public'					=> false,
                    'publicly_queryable'		=> false,
                    'exclude_from_search'		=> true,
                    'show_ui' 					=> true,
                    'query_var' 				=> true,
                    'has_archive' 				=> false,
                    'hierarchical'				=> false,
                    'show_in_menu'      		=> true,
                    'show_in_nav_menus' 		=> false,
                    'menu_position' 			=> null,
                    'menu_icon' 				=> 'dashicons-images-alt2',
                    'capability_type' 			=> 'post',
                    'supports'					=> array( 'title' )
                )
            );
        }
        function saveFieldValues( $post_ID, $post ) {
            if ($post->post_type == WORDPRESS_SIMPLESLIDER_POST_TYPE  && $post->post_status != 'auto-draft') {
                                
                update_post_meta( $post_ID, $this->metaboxMainTextFieldName, isset($_POST[ $this->metaboxMainTextFieldName ]) ? $_POST[ $this->metaboxMainTextFieldName ] : false );
                update_post_meta( $post_ID, $this->metaboxSecondaryTextFieldName, isset($_POST[ $this->metaboxSecondaryTextFieldName ]) ? $_POST[ $this->metaboxSecondaryTextFieldName ] : false );
                update_post_meta( $post_ID, $this->metaboxButtonTextFieldName, isset($_POST[ $this->metaboxButtonTextFieldName ]) ? $_POST[ $this->metaboxButtonTextFieldName ] : false );
                update_post_meta( $post_ID, $this->metaboxSliderLinkFieldName, isset($_POST[ $this->metaboxSliderLinkFieldName ]) ? $_POST[ $this->metaboxSliderLinkFieldName ] : false );
                update_post_meta( $post_ID, $this->metaboxNewTabFieldName, isset($_POST[ $this->metaboxNewTabFieldName ]) ? $_POST[ $this->metaboxNewTabFieldName ] : false );
                update_post_meta( $post_ID, $this->metaboxButtonTextColor, isset($_POST[ $this->metaboxButtonTextColor ]) ? $_POST[ $this->metaboxButtonTextColor ] : false );
                update_post_meta( $post_ID, $this->metaboxTextColor, isset($_POST[ $this->metaboxTextColor ]) ? $_POST[ $this->metaboxTextColor ] : false );
                update_post_meta( $post_ID, $this->metaboxButtonColor, isset($_POST[ $this->metaboxButtonColor ]) ? $_POST[ $this->metaboxButtonColor ] : false );
                update_post_meta( $post_ID, $this->metaboxDesktopBackgroundImageFieldName, isset($_POST[ $this->metaboxDesktopBackgroundImageFieldName ]) ? $_POST[ $this->metaboxDesktopBackgroundImageFieldName ] : false );
                update_post_meta( $post_ID, $this->metaboxMobileBackgroundImageFieldName, isset($_POST[ $this->metaboxMobileBackgroundImageFieldName ]) ? $_POST[ $this->metaboxMobileBackgroundImageFieldName ] : false );
                update_post_meta( $post_ID, $this->metaboxTemplate, isset($_POST[ $this->metaboxTemplate ]) ? $_POST[ $this->metaboxTemplate ] : false );
                update_post_meta( $post_ID, $this->metaboxButtonClass, isset($_POST[ $this->metaboxButtonClass ]) ? $_POST[ $this->metaboxButtonClass ] : false );   
                update_post_meta( $post_ID, $this->metaboxEnableSVG, isset($_POST[ $this->metaboxEnableSVG ]) ? $_POST[ $this->metaboxEnableSVG ] : false );
                update_post_meta( $post_ID, $this->metaboxSVGPosition, isset($_POST[ $this->metaboxSVGPosition ]) ? $_POST[ $this->metaboxSVGPosition ] : false );

            }
        }
        function createMetabox() {
            
            add_meta_box(
                $this->metaboxName,                    
                'Slider',
                array(&$this, 'showMetabox'),
                WORDPRESS_SIMPLESLIDER_POST_TYPE
            );          
            
        }
        function showMetabox( $post ) {
            echo '
                <div class="shortcode">
                    '.$this->getShortcode( $post->ID, $post->post_title ).'
                </div>
                <div class="slider-menu">
                    <button class="button button-secondary button-large add-slide">Adicionar slide</button>
                </div>
                <div class="lines">
            ';

            $this->createPostLines( $post );
            
            echo '
                </div>';
        }
        function createPostLines( $post ) {  
            
            $slides = $this->getSliderMeta( $post->ID );
            

            if (!empty($slides)) {
                foreach ($slides as $key => $slide) {
                    $this->showPostLine( $post, false, $key ); 
                }
            } else {
                $this->showPostLine( $post, true ); 
            }
            
                   
        }
        function showPostLine( $post, $free = false, $position = false ) {
            echo '
                <div class="wp-simpleslider-line"> 
                    <div class="line-header">
                        <button class="button-secondary move-top">
                            <svg width="12px" height="8px" viewBox="0 0 12 8" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                <title>expand_less</title>
                                <desc>Created with Sketch.</desc>
                                <g id="Icons" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <g id="Outlined" transform="translate(-276.000000, -3484.000000)">
                                        <g id="Navigation" transform="translate(100.000000, 3378.000000)">
                                            <g id="Outlined-/-Navigation-/-expand_less" transform="translate(170.000000, 98.000000)">
                                                <g>
                                                    <polygon id="Path" points="0 0 24 0 24 24 0 24"></polygon>
                                                    <polygon id="🔹-Icon-Color" fill="#2271b1" points="12 8 6 14 7.41 15.41 12 10.83 16.59 15.41 18 14"></polygon>
                                                </g>
                                            </g>
                                        </g>
                                    </g>
                                </g>
                            </svg>
                        </button>
                        <button class="button-secondary move-down">
                        <svg width="12px" height="8px" viewBox="0 0 12 8" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                            <title>expand_more</title>
                            <desc>Created with Sketch.</desc>
                            <g id="Icons" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <g id="Outlined" transform="translate(-242.000000, -3484.000000)">
                                    <g id="Navigation" transform="translate(100.000000, 3378.000000)">
                                        <g id="Outlined-/-Navigation-/-expand_more" transform="translate(136.000000, 98.000000)">
                                            <g>
                                                <polygon id="Path" opacity="0.87" points="24 24 0 24 0 0 24 0"></polygon>
                                                <polygon id="🔹-Icon-Color" fill="#2271b1" points="16.59 8.59 12 13.17 7.41 8.59 6 10 12 16 18 10"></polygon>
                                            </g>
                                        </g>
                                    </g>
                                </g>
                            </g>
                        </svg>
                        </button>
                        <button class="button-secondary minimize-slider">
                            <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                            viewBox="0 0 352.054 352.054" style="enable-background:new 0 0 352.054 352.054;" xml:space="preserve">
                                <g>
                                    <polygon fill="#2271b1" points="144.206,186.634 30,300.84 30,238.059 0,238.059 0,352.054 113.995,352.054 113.995,322.054 51.212,322.054 
                                        165.419,207.847 	"/>
                                    <polygon fill="#2271b1" points="238.059,0 238.059,30 300.84,30 186.633,144.208 207.846,165.42 322.054,51.213 322.054,113.995 352.054,113.995 
                                        352.054,0 	"/>
                                </g>
                            </svg>   
                        </button>                        
                        <button class="button-secondary remove-slider">
                            <svg version="1.1" id="cross-11" xmlns="http://www.w3.org/2000/svg" width="11px" height="11px" viewBox="0 0 11 11">
                                <path fill="#2271b1" d="M2.2,1.19l3.3,3.3L8.8,1.2C8.9314,1.0663,9.1127,0.9938,9.3,1C9.6761,1.0243,9.9757,1.3239,10,1.7&#xA;&#x9;c0.0018,0.1806-0.0705,0.3541-0.2,0.48L6.49,5.5L9.8,8.82C9.9295,8.9459,10.0018,9.1194,10,9.3C9.9757,9.6761,9.6761,9.9757,9.3,10&#xA;&#x9;c-0.1873,0.0062-0.3686-0.0663-0.5-0.2L5.5,6.51L2.21,9.8c-0.1314,0.1337-0.3127,0.2062-0.5,0.2C1.3265,9.98,1.02,9.6735,1,9.29&#xA;&#x9;C0.9982,9.1094,1.0705,8.9359,1.2,8.81L4.51,5.5L1.19,2.18C1.0641,2.0524,0.9955,1.8792,1,1.7C1.0243,1.3239,1.3239,1.0243,1.7,1&#xA;&#x9;C1.8858,0.9912,2.0669,1.06,2.2,1.19z"/>
                            </svg>
                        </button>
                        
                    </div>
                    <div class="line-body '.($free ? "" : "closed").'">';
                    
                        $this->createPostFields( $post, $free, $position );

            echo '  </div> 
                </div> ';
        }
        function createPostFields( $post, $free, $position ) {
            $this->showPostField(
                $post,
                $this->metaboxMainTextFieldName, 
                'Texto principal', 
                'Texto exibido com fonte maior.', 
                'text',
                $free,
                $position,
                false
            );

            $this->showPostField( 
                $post,
                $this->metaboxSecondaryTextFieldName, 
                'Texto secundário', 
                'Texto exibido com a fonte menor, logo abaixo do texto principal.', 
                'text',
                $free,
                $position,
                false
            );

            $this->showPostField( 
                $post,
                $this->metaboxButtonTextFieldName, 
                'Texto do botão', 
                'Texto exibido dentro do botão.', 
                'text',
                $free,
                $position,
                false
            );

            $this->showPostField( 
                $post,
                $this->metaboxSliderLinkFieldName, 
                'Link do slider', 
                'Link da página para qual o slider deverá redirecionar.', 
                'text',
                $free,
                $position,
                false
            );

            $this->showPostField( 
                $post,
                $this->metaboxNewTabFieldName, 
                'Link em nova guia', 
                'Ative essa opção para abrir o link do slider em nova guia.', 
                'checkbox',
                $free,
                $position,
                false
            );

            $this->showPostField( 
                $post,
                $this->metaboxTextColor, 
                'Cor do texto', 
                'Hexadecimal rerefente a cor do texto.', 
                'color',
                $free,
                $position,
                false
            );

            $this->showPostField( 
                $post,
                $this->metaboxButtonColor, 
                'Cor do botão', 
                'Hexadecimal rerefente a cor do botão.', 
                'color',
                $free,
                $position,
                false
            );

            $this->showPostField( 
                $post,
                $this->metaboxButtonTextColor, 
                'Cor do texto do botão', 
                'Hexadecimal rerefente a cor do texto do botão.', 
                'color',
                $free,
                $position,
                false
            );

            if ( get_option( $this->optionEnableSVG ) ) {
                $this->showPostField( 
                    $post,
                    $this->metaboxEnableSVG, 
                    'SVG do botão', 
                    'Cole no campo abaixo o SVG.', 
                    'textarea',
                    $free,
                    $position,
                    false
                );

                $this->showPostField( 
                    $post,
                    $this->metaboxSVGPosition, 
                    'Posição do SVG', 
                    'Escolha a posição do SVG.', 
                    'radio',
                    $free,
                    $position,
                    false,
                    array(
                        'svg-left' => "SVG ajustado a esquerda do botão",
                        'svg-right' => "SVG ajustado a direita do botão"
                    )
                ); 
            }

            $this->showPostField( 
                $post,
                $this->metaboxButtonClass, 
                'Classe do botão', 
                'Classe para estilizar o botão.', 
                'text',
                $free,
                $position,
                false
            );

            $this->showPostField( 
                $post,
                $this->metaboxTemplate, 
                'Template', 
                'Escolha o template que combina com seu slide.', 
                'radio',
                $free,
                $position,
                false,
                array(
                    'left' => "Texto ajustado a esquerda",
                    'center' => "Texto ajustado ao centro",
                    'right' => "Texto ajustado a direita"
                )
            ); 
           
            $this->showPostField( 
                $post,
                $this->metaboxDesktopBackgroundImageFieldName, 
                'Imagem de fundo desktop', 
                'Define a imagem que será exibida no fundo do slider. Recomendamos que a imagem tenha '.get_option( $this->optionImageWidth ).' pixels de 
                largura por '.get_option( $this->optionImageHeight ).' pixels de altura. Além disso, para evitar que o carregamento seja prejudicado, sugerimos 
                que a imagem tenha um tratamento prévio para reduzir o tamanho.', 
                'image',
                $free,
                $position,
                true
            );

            $this->showPostField( 
                $post,
                $this->metaboxMobileBackgroundImageFieldName, 
                'Imagem de fundo mobile', 
                'Define a imagem de fundo em dispositivos móveis. Este campo é opcional. Caso não seja preenchido, a 
                imagem de fundo padrão será ajustada para exibição em celulares.', 
                'image',
                $free,
                $position,
                false
            );                         
        }
        function showPostField( $post, $optionName, $optionTitle, $optionDesc = false, $type = 'text', $free, $position, $required = false, $options = array() ) {

            $value = !$free ? get_post_meta( $post->ID, $optionName, true ) : "";

            echo '
                <div class="wp-simpleslider-option-container" data-type="'.$type.'">
                    <label class="option-title" for="wp-simpleslider-option-field-'.$optionName.'">'.$optionTitle.' '.( $required ? '<span class="required">*</span>' : '' ).'</label>
                    <p>'.($optionDesc ? $optionDesc : "").'</p>';

                    switch($type) {
                        case 'text':
                            $this->showInputTypeText( $optionName, $value, $required, true, $position );
                            break;

                        case 'textarea':
                            $this->showInputTypeTextarea( $optionName, $value, $required, true, $position );
                            break;

                        case 'image':
                            $this->showInputTypeImage( $optionName, $value, $required, true, $position );
                            break;

                        case 'color':
                            $this->showInputTypeColor( $optionName, $value, $required, true, $position );
                            break;

                        case 'radio':
                            $this->showInputTypeRadio( $optionName, $value, $required, true, $position, $options );
                            break;

                        case 'checkbox':
                            $this->showInputTypeCheckbox( $optionName, $value, $required, true, $position, $options );
                            break;
                    }

            echo '
                </div>';
        }

        // User
        function userEnqueueScripts() {
            
            if ( get_option( $this->optionLoadSlick ) ) {
                wp_enqueue_script( 'slick-js', 'http://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js', array( 'jquery' ), false, true );
                wp_enqueue_style( 'slick-css','http://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css', false, "1.0.0", 'all' );
            }

            add_action('wp_head', function() {

                $options = $this->getOptions();

                echo '
                    <style>
                        .main-simpleslider-container .slide {
                            padding-top: '.$options['padding_desktop'].'vw;
                        }

                        @media screen and (max-width: 992px) {
                            .main-simpleslider-container .slide {
                                padding-top: '.$options['padding_tablet'].'vw;
                            }
                        }

                        @media screen and (max-width: 575px) {
                            .main-simpleslider-container .slide {
                                padding-top: '.$options['padding_mobile'].'vw;
                            }
                        }';

                        if ($options['black_gradient']) {
                            echo '
                                .main-simpleslider-container .slide:before {
                                    background: linear-gradient(to right, rgba(0,0,0, 0.85), transparent);
                                }

                                @media screen and (max-width: 575px) {
                                    .main-simpleslider-container .slide:before {
                                        background: linear-gradient(to bottom, transparent, black) !important;
                                    }
                                }
                            ';
                        } else {
                            echo '
                                .main-simpleslider-container .slide:before {
                                    display: none !important;
                                }
                            ';
                        }


                echo '</style>
                ';
            });

            wp_enqueue_script( 'user-simpleslider-js', WORDPRESS_SIMPLESLIDER_URL . 'assets/scripts/user-simpleslider-script-min.js', array( 'jquery' ), "1.0.0", true );            
            wp_enqueue_style( 'user-simpleslider-css', WORDPRESS_SIMPLESLIDER_URL . 'assets/styles/user-simpleslider-style.css', array(), "1.0.0", 'all' );
        }
        function slidersDinamicCSS( $slides, $config ) {

            $style = '<style id="simpleslider-slides-style">

            .slick-active button {
                background:'.$config['slick_active_dot'].';
            }
            
            ';                    

            foreach ( $slides as $key => $slide ) {
                $style .= '.main-simpleslider-container .slide-'.$key.' .simpleslider-title, .main-simpleslider-container .slide-'.$key.' .simpleslider-text {
                        '.( $slide['text_color'] ? 'color: '.$slide['text_color'].';' : '' ).'
                    }

                    .main-simpleslider-container .slide-'.$key.' .simpleslider-button {
                        '.( $slide['button_color'] ? 'background-color: '.$slide['button_color'].';' : '' ).'
                    }

                    .main-simpleslider-container .slide-'.$key.' .simpleslider-button span {
                        '.( $slide['button_text_color'] ? 'color: '.$slide['button_text_color'].';' : '' ).'
                    } 

                    .main-simpleslider-container .slide-'.$key.' .simpleslider-button svg {
                        '.( $slide['button_text_color'] ? 'fill: '.$slide['button_text_color'].';' : '' ).'
                    } 
                    
                    ';
            }

            $style .= '</style>';

            return $style;
            
        }
        function showSliders( $atts ) {

            if ( isset( $atts["id"] ) ) {

                $id = $atts["id"];

                $args = [
                    'post_type'     => WORDPRESS_SIMPLESLIDER_POST_TYPE,
                    'post_status'   => 'publish',
                    'p'             => $id
                ];
                
                $query = new WP_Query( $args );
                
                if ( $query->have_posts() ) {  
                    
                    $slides = $this->getSliderMeta( $id );
                    $config = $this->getOptions();

                    if ( $slides ) {
                        
                        $style = [ "style" => $this->slidersDinamicCSS( $slides, $config ) ];

                        set_query_var( 'slides', $slides ); 
                        set_query_var( 'config', $config ); 
                        set_query_var( 'style', $style ); 

                        include(WORDPRESS_SIMPLESLIDER_PLUGIN_DIR."views/slider.php");
                        
                    } else {
                        echo "Nenhum slide encontrado no slider selecionado.";
                    }

                } else {
                    echo "Slider não encontrado.";
                }

            } else {
                echo "ID não informado.";
            }
        }

    }