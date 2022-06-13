<?php 
    extract( get_query_var( 'slides' ) );
    extract( get_query_var( 'config' ) );
    extract( get_query_var( 'style' ) );
?>

<div class="main-simpleslider-container">
    <?php echo $style; ?>

    <?php if (count($slides) > 1): ?>
    
        <button class="prevArrow">
            <span class="only-semantics">Voltar slide</span>
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="24" viewBox="0 0 12 24">
            <path d="M371.9-101.621a1.138,1.138,0,0,0,.849-.391,1.44,1.44,0,0,0,0-1.886L364-113.621l8.751-9.724a1.44,1.44,0,0,0,0-1.886,1.117,1.117,0,0,0-1.7,0l-9.6,10.667a1.412,1.412,0,0,0-.352.943,1.412,1.412,0,0,0,.352.943l9.6,10.667A1.138,1.138,0,0,0,371.9-101.621Z" transform="translate(-361.099 125.621)"/>
            </svg>
        </button>

    <?php endif; ?>
    
    <div class="main-slider">

        <?php foreach ($slides as $slide): ?>
            <div class="slide-<?= $slide['id']; ?> slide <?php echo $slide['text_align']; ?>">            
                
                <?php if (!$slide['button_text'] && $slide['button_link']): ?>
                    <a href="#" class="slide-link"></a>
                <?php endif; ?>

                <?php echo $slide['image_html']; ?>                                    
                <div class="container">
                    <?php if ($slide['main_text']): ?>
                        <h2 class="simpleslider-title">
                            <?php echo $slide['main_text']; ?>
                        </h2>
                    <?php endif; ?>
                    <?php if ($slide['secondary_text']): ?>
                        <p class="simpleslider-text">
                            <?php echo $slide['secondary_text']; ?>
                        </p>
                    <?php endif; ?>
                    <?php if ($slide['button_text']): ?>
                        <a href="<?php echo $slide['button_link']; ?>" class="simpleslider-button <?php echo $config['buttons_class'] ?> <?php echo $slide['button_class'] ?> <?php echo $config['enable_svg'] ? $slide['svg_position'] : ''; ?>">
                            <?php echo $config['enable_svg'] ? $slide['svg'] : ''; ?>
                            <span><?php echo $slide['button_text']; ?></span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>

        <?php endforeach; ?>

    </div>

    <?php if (count($slides) > 1): ?>

        <button class="nextArrow">
            <span class="only-semantics">Avançar slide</span>
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="24" viewBox="0 0 12 24">
            <path d="M362.3-101.621a1.138,1.138,0,0,1-.849-.391,1.44,1.44,0,0,1,0-1.886l8.751-9.724-8.751-9.724a1.44,1.44,0,0,1,0-1.886,1.117,1.117,0,0,1,1.7,0l9.6,10.667a1.412,1.412,0,0,1,.352.943,1.412,1.412,0,0,1-.352.943l-9.6,10.667A1.138,1.138,0,0,1,362.3-101.621Z" transform="translate(-361.099 125.621)"/>
            </svg>
        </button>
        
    <?php endif; ?>
    
</div>