<?php

    class WordpressSimpleslider {

        private $metaboxName = "simpleslider_metabox";

        private $metaboxMainTextFieldName = "simpleslider_main_text_field";
        private $metaboxSecondaryTextFieldName = "simpleslider_secondary_text_field";
        private $metaboxButtonTextFieldName = "simpleslider_button_text_field";
        private $metaboxButtonLinkFieldName = "simpleslider_button_link_field";
        private $metaboxDesktopBackgroundImageFieldName = "simpleslider_desktop_background_image_field";
        private $metaboxMobileBackgroundImageFieldName = "simpleslider_mobile_background_image_field";

        function __construct() {            
            add_action( 'init', array(&$this, 'registerSliderCPT') );
            add_action( 'add_meta_boxes', array(&$this, 'createMetabox') );
            add_action( 'admin_enqueue_scripts', array(&$this, 'adminEnqueueScripts') );
            add_action( 'wp_enqueue_scripts', array(&$this, 'userEnqueueScripts') );
            add_action( 'save_post', array(&$this, 'saveFieldValues'), 10, 2 );
            add_shortcode( 'simpleslider', array(&$this, 'showSliders') );
        }      

        function showSliders( $atts ) {

            $args = [
                'post_type'     => WORDPRESS_SIMPLESLIDER_POST_TYPE,
                'post_status'   => 'publish'
            ];
            
            $query = new WP_Query( $args );
            
            if ( $query->have_posts() ) {     

                echo '
                    <div class="main-simpleslider-container">
            
                        <button class="prevArrow">
                            <span class="only-semantics">Voltar slide</span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="24" viewBox="0 0 12 24">
                            <path d="M371.9-101.621a1.138,1.138,0,0,0,.849-.391,1.44,1.44,0,0,0,0-1.886L364-113.621l8.751-9.724a1.44,1.44,0,0,0,0-1.886,1.117,1.117,0,0,0-1.7,0l-9.6,10.667a1.412,1.412,0,0,0-.352.943,1.412,1.412,0,0,0,.352.943l9.6,10.667A1.138,1.138,0,0,0,371.9-101.621Z" transform="translate(-361.099 125.621)"/>
                            </svg>
                        </button>
                        
                        <div class="main-slider">';
                                        
                        while ( $query->have_posts() ) {
                            $query->the_post();
                            
                            $slider = $this->getSliderMeta( get_the_ID() );
            
                            echo '
                                <div class="slide">
                                
                                    '.$slider['desktop_background_image'].'
                                                        
                                    <div class="container">
                                        <h2>
                                            '.$slider['main_text'].'
                                        </h2>
                                        <p>
                                            '.$slider['secondary_text'].'
                                        </p>
                                        <a href="'.$slider['button_link'].'" class="main-button">
                                            '.$slider['button_text'].'
                                        </a>
                                    </div>
                                </div>
                            ';
                        }

                        echo '
                        </div>
            
                        <button class="nextArrow">
                            <span class="only-semantics">Avançar slide</span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="24" viewBox="0 0 12 24">
                            <path d="M362.3-101.621a1.138,1.138,0,0,1-.849-.391,1.44,1.44,0,0,1,0-1.886l8.751-9.724-8.751-9.724a1.44,1.44,0,0,1,0-1.886,1.117,1.117,0,0,1,1.7,0l9.6,10.667a1.412,1.412,0,0,1,.352.943,1.412,1.412,0,0,1-.352.943l-9.6,10.667A1.138,1.138,0,0,1,362.3-101.621Z" transform="translate(-361.099 125.621)"/>
                            </svg>
                        </button>
                        
                    </div>
                ';
            
                wp_reset_postdata();

            } else {

                echo "Slider não encontrado.";

            }    

        }
        function getSliderMeta( $post_id ) {            
            $data = array(
                'main_text' => get_post_meta( $post_id, $this->metaboxMainTextFieldName, true ),
                'secondary_text' => get_post_meta( $post_id, $this->metaboxSecondaryTextFieldName, true ),
                'button_text' => get_post_meta( $post_id, $this->metaboxButtonTextFieldName, true ),
                'button_link' => get_post_meta( $post_id, $this->metaboxButtonLinkFieldName, true ),
                'desktop_background_image' => wp_get_attachment_image( get_post_meta( $post_id, $this->metaboxDesktopBackgroundImageFieldName, true ), 'full' ),
                'mobile_background_image' => wp_get_attachment_image( get_post_meta( $post_id, $this->metaboxMobileBackgroundImageFieldName, true ), 'full' )
            );

            return $data;
        }
        function adminEnqueueScripts() {
            // js
            wp_enqueue_script( 'admin-simpleslider-js', WORDPRESS_SIMPLESLIDER_URL . 'assets/script/admin-simpleslider-script.js', false, "1.0.0", true );    
            wp_enqueue_media();
            
            // stylesheet
            wp_enqueue_style( 'admin-simpleslider-css', WORDPRESS_SIMPLESLIDER_URL . 'assets/style/admin-simpleslider-style.css', array(), "1.0.0", 'all' );
        }
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
            if ($post->post_type == WORDPRESS_SIMPLESLIDER_POST_TYPE) {
                                
                update_post_meta( $post_ID, $this->metaboxMainTextFieldName, $_POST[ $this->metaboxMainTextFieldName ] );
                update_post_meta( $post_ID, $this->metaboxSecondaryTextFieldName, $_POST[ $this->metaboxSecondaryTextFieldName ] );
                update_post_meta( $post_ID, $this->metaboxButtonTextFieldName, $_POST[ $this->metaboxButtonTextFieldName ] );
                update_post_meta( $post_ID, $this->metaboxButtonLinkFieldName, $_POST[ $this->metaboxButtonLinkFieldName ] );
                update_post_meta( $post_ID, $this->metaboxDesktopBackgroundImageFieldName, $_POST[ $this->metaboxDesktopBackgroundImageFieldName ] );
                update_post_meta( $post_ID, $this->metaboxMobileBackgroundImageFieldName, $_POST[ $this->metaboxMobileBackgroundImageFieldName ] );
                
            }
        }
        function createMetabox() {
            
            add_meta_box(
                $this->metaboxName,                    
                'Slider',
                array(&$this, 'createFields'),
                WORDPRESS_SIMPLESLIDER_POST_TYPE
            );          
            
        }
        function createFields( $post ) {
            
            $this->showField(
                $post,
                $this->metaboxMainTextFieldName, 
                'Texto principal', 
                'Texto exibido com fonte maior.', 
                'text',
                
            );

            $this->showField( 
                $post,
                $this->metaboxSecondaryTextFieldName, 
                'Texto secundário', 
                'Texto exibido com a fonte menor, logo abaixo do texto principal.', 
                'text' 
            );

            $this->showField( 
                $post,
                $this->metaboxButtonTextFieldName, 
                'Texto do botão', 
                'Texto exibido dentro do botão.', 
                'text' 
            );

            $this->showField( 
                $post,
                $this->metaboxButtonLinkFieldName, 
                'Link do botão', 
                'Link da página para qual o botão deverá redirecionar.', 
                'text'
            );

            $this->showField( 
                $post,
                $this->metaboxDesktopBackgroundImageFieldName, 
                'Imagem de fundo desktop', 
                'Define a imagem que será exibida no fundo do slider. Recomendamos que a imagem tenha 1920 pixels de 
                 largura por 800 pixels de altura. Além disso, para evitar que o carregamento seja prejudicado, sugerimos 
                 que a imagem tenha um tratamento prévio para reduzir o tamanho.', 
                'image' 
            );

            $this->showField( 
                $post,
                $this->metaboxMobileBackgroundImageFieldName, 
                'Imagem de fundo mobile', 
                'Define a imagem de fundo em dispositivos móveis. Este campo é opcional. Caso não seja preenchido, a 
                 imagem de fundo padrão será ajustada para exibição em celulares. Recomendamos que a imagem tenha 600 pixels 
                 de largura por 800 pixels de altura.', 
                'image' 
            );

        }
        function showField( $post, $optionName, $optionTitle, $optionDesc = false, $type = 'text' ) {

            $value = get_post_meta( $post->ID, $optionName, true );

            echo '
                <div class="wp_simpleslider_option_container" data-type="'.$type.'">
                    <label for="wp_simpleslider_option_field_'.$optionName.'">'.$optionTitle.'</label>
                    <p>'.($optionDesc ? $optionDesc : "").'</p>';

                    switch($type) {
                        case 'text':
                            $this->showInputTypeText( $post, $optionName, $value );
                            break;

                        case 'image':
                            $this->showInputTypeImage( $post, $optionName, $value );
                            break;
                    }

            echo '
                </div>';
        }
        function showInputTypeText( $post, $optionName, $value ) {

            printf(
                '<input type="text" id="wp_simpleslider_option_field_%s" name="%s" value="%s" />',
                $optionName, 
                $optionName, 
                esc_attr( $value )
            );

        }
        function showInputTypeImage( $post, $optionName, $value ) {
    
            printf(
                '<input type="hidden" id="wp_simpleslider_option_field_%s" name="%s" value="%s" /><span class="button button-primary select-image" data-target="%s"></span><span class="button button-primary update-image" data-target="%s"></span><span class="button remove-image" data-target="%s"></span>',
                $optionName,
                $optionName,
                esc_attr( $value ),
                $optionName,
                $optionName,
                $optionName
            );

        }        
        function userEnqueueScripts() {
            // js
            wp_enqueue_script( 'slick-js', 'http://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js', array( 'jquery' ), false, true );    
            wp_enqueue_script( 'user-simpleslider-js', WORDPRESS_SIMPLESLIDER_URL . 'assets/script/user-simpleslider-script.js', array( 'slick-js', 'jquery' ), "1.0.0", true );    
                        
            // stylesheet
            wp_enqueue_style( 'slick-css','http://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css', false, "1.0.0", 'all' );
            wp_enqueue_style( 'user-simpleslider-css', WORDPRESS_SIMPLESLIDER_URL . 'assets/style/user-simpleslider-style.css', array(), "1.0.0", 'all' );
        }
    }