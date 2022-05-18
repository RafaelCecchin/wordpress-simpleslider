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
            add_action( 'init', array(&$this, 'RegisterSliderCPT') );
            add_action( 'add_meta_boxes', array(&$this, 'CreateMetabox') );
            add_action( 'save_post', array(&$this, 'SaveFieldValues'), 10, 2 );
        }      

        function RegisterSliderCPT() {
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
                    'supports'					=> array( '' )
                )
            );
        }
        function SaveFieldValues( $post_ID, $post ) {
            if ($post->post_type == WORDPRESS_SIMPLESLIDER_POST_TYPE) {
                                
                update_post_meta( $post_ID, $this->metaboxMainTextFieldName, $_POST[ $this->metaboxMainTextFieldName ] );
                update_post_meta( $post_ID, $this->metaboxSecondaryTextFieldName, $_POST[ $this->metaboxSecondaryTextFieldName ] );
                update_post_meta( $post_ID, $this->metaboxButtonTextFieldName, $_POST[ $this->metaboxButtonTextFieldName ] );
                update_post_meta( $post_ID, $this->metaboxButtonLinkFieldName, $_POST[ $this->metaboxButtonLinkFieldName ] );
                update_post_meta( $post_ID, $this->metaboxDesktopBackgroundImageFieldName, $_POST[ $this->metaboxDesktopBackgroundImageFieldName ] );
                update_post_meta( $post_ID, $this->metaboxMobileBackgroundImageFieldName, $_POST[ $this->metaboxMobileBackgroundImageFieldName ] );
                
            }
        }
        function CreateMetabox() {
            
            add_meta_box(
                $this->metaboxName,                    
                'Slider',
                array(&$this, 'CreateFields'),
                WORDPRESS_SIMPLESLIDER_POST_TYPE
            );          
            
        }
        function CreateFields( $post ) {
            
            $this->ShowField(
                $post,
                $this->metaboxMainTextFieldName, 
                'Texto principal', 
                'Texto exibido com fonte maior.', 
                'text',
                
            );

            $this->ShowField( 
                $post,
                $this->metaboxSecondaryTextFieldName, 
                'Texto secundário', 
                'Texto exibido com a fonte menor, logo abaixo do texto principal.', 
                'text' 
            );

            $this->ShowField( 
                $post,
                $this->metaboxButtonTextFieldName, 
                'Texto do botão', 
                'Texto exibido dentro do botão.', 
                'text' 
            );

            $this->ShowField( 
                $post,
                $this->metaboxButtonLinkFieldName, 
                'Link do botão', 
                'Link da página para qual o botão deverá redirecionar.', 
                'text'
            );

            $this->ShowField( 
                $post,
                $this->metaboxDesktopBackgroundImageFieldName, 
                'Imagem de fundo desktop', 
                'Define a imagem que será exibida no fundo do slider. Recomendamos que a imagem tenha 1920 pixels de 
                 largura por 800 pixels de altura. Além disso, para evitar que o carregamento seja prejudicado, sugerimos 
                 que a imagem tenha um tratamento prévio para reduzir o tamanho.', 
                'text' 
            );

            $this->ShowField( 
                $post,
                $this->metaboxMobileBackgroundImageFieldName, 
                'Imagem de fundo mobile', 
                'Define a imagem de fundo em dispositivos móveis. Este campo é opcional. Caso não seja preenchido, a 
                 imagem de fundo padrão será ajustada para exibição em celulares. Recomendamos que a imagem tenha 600 pixels 
                 de largura por 800 pixels de altura.', 
                'text' 
            );

        }
        function ShowField( $post, $optionName, $optionTitle, $optionDesc = false, $type = 'text' ) {
            echo '
                <div class="wp_simpleslider_option_container">
                    <label for="wp_simpleslider_option_field_'.$optionName.'">'.$optionTitle.'</label>
                    <p>'.($optionDesc ? $optionDesc : "").'</p>';

                    switch($type) {
                        case 'text':
                            $this->ShowInputTypeText( $post, $optionName );
                            break;
                    }

            echo '
                </div>';
        }
        function ShowInputTypeText( $post, $optionName ) {

            $value = get_post_meta( $post->ID, $optionName, true );

            printf(
                '<input type="text" id="wp_simpleslider_option_field_%s" name="%s" value="%s" />',
                $optionName, 
                $optionName, 
                esc_attr( $value )
            );

        }

    }