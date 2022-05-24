<?php 
    extract( get_query_var( 'slides' ) );
    extract( get_query_var( 'config' ) );
?>

<div class="main-simpleslider-container">

    <button class="prevArrow">
        <span class="only-semantics">Voltar slide</span>
        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="24" viewBox="0 0 12 24">
        <path d="M371.9-101.621a1.138,1.138,0,0,0,.849-.391,1.44,1.44,0,0,0,0-1.886L364-113.621l8.751-9.724a1.44,1.44,0,0,0,0-1.886,1.117,1.117,0,0,0-1.7,0l-9.6,10.667a1.412,1.412,0,0,0-.352.943,1.412,1.412,0,0,0,.352.943l9.6,10.667A1.138,1.138,0,0,0,371.9-101.621Z" transform="translate(-361.099 125.621)"/>
        </svg>
    </button>
    
    <div class="main-slider">

        <?php foreach ($slides as $slide): ?>
            <div class="slide <?php echo $slide['text_align']; ?>">            
                <?php echo $slide['image_html']; ?>                                    
                <div class="container">
                    <?php if ($slide['main_text']): ?>
                        <h2 style="color: <?php echo $slide['text_color']; ?>;">
                            <?php echo $slide['main_text']; ?>
                        </h2>
                    <?php endif; ?>
                    <?php if ($slide['secondary_text']): ?>
                        <p style="color: <?php echo $slide['text_color']; ?>;">
                            <?php echo $slide['secondary_text']; ?>
                        </p>
                    <?php endif; ?>
                    <?php if ($slide['button_text']): ?>
                        <a href="<?php echo $slide['button_link']; ?>" class="simpleslider-button <?php echo $config['buttons_class'] ?>" style="color: <?php echo $slide['button_text_color']; ?>; background-color: <?php echo $slide['button_color']; ?>;">
                            <?php echo $slide['button_text']; ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>

        <?php endforeach; ?>

    </div>

    <button class="nextArrow">
        <span class="only-semantics">Avançar slide</span>
        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="24" viewBox="0 0 12 24">
        <path d="M362.3-101.621a1.138,1.138,0,0,1-.849-.391,1.44,1.44,0,0,1,0-1.886l8.751-9.724-8.751-9.724a1.44,1.44,0,0,1,0-1.886,1.117,1.117,0,0,1,1.7,0l9.6,10.667a1.412,1.412,0,0,1,.352.943,1.412,1.412,0,0,1-.352.943l-9.6,10.667A1.138,1.138,0,0,1,362.3-101.621Z" transform="translate(-361.099 125.621)"/>
        </svg>
    </button>
    
</div>