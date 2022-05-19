/* Opções de imagens do post */

const imageOptions = [...document.querySelectorAll(`.wp-simpleslider-option-container[data-type="image"]`)];

if (imageOptions.length) {
    
    updateImageButtons();

}

function updateImageButtons() {
    imageOptions.forEach(imageOption => {

        let inputImage        = imageOption.querySelector(`input`);
        let selectImageButton = imageOption.querySelector(`.select-image`);
        let updateImageButton = imageOption.querySelector(`.update-image`);
        let removeImageButton = imageOption.querySelector(`.remove-image`);

        (inputImage.value != "") ? imageOption.classList.add('has-value') : imageOption.classList.remove('has-value');
        
        selectImageButton.addEventListener('click', openMediaPanel);
        updateImageButton.addEventListener('click', openMediaPanel);
        removeImageButton.addEventListener('click', removeMediaValue);
    });
}


function openMediaPanel() {
    let optionName = this.getAttribute('data-target');

    let inputImage = this.parentElement.querySelector(`input`);

    console.log(optionName);

    const imageFrame = wp.media({
        title: 'Imagem',
        button: {
            text: 'Select',
        },
        library: {
            type: 'image'
        },
        multiple: false,
        frame:    'select'
    });

    imageFrame.on( 'select', function() {
        attachment = imageFrame.state().get( 'selection' ).first().toJSON();
        
        inputImage.value = attachment.id;
        updateImageButtons();
    });

    imageFrame.on( 'open', function() {
        let selection = imageFrame.state().get( 'selection' );

        attachment = wp.media.attachment( inputImage.value );
        attachment.fetch();
        selection.add(attachment ? [attachment] : []);
    });

    imageFrame.open();
}

function removeMediaValue() {
    let optionName = this.getAttribute( 'data-target' );

    let inputImage   = document.querySelector(` input[name="${optionName}"] `);
    inputImage.value = "";
    updateImageButtons();
}


/* Botao minimizar slide */

const minimizeSliderButtons = [...document.querySelectorAll(`#simpleslider_metabox .minimize-slider`)];

if (minimizeSliderButtons.length) {
    
    minimizeSliderButtons.forEach(minimizeSliderButton => {

        minimizeSliderButton.addEventListener('click', minimizeSlide);

    });
}

function minimizeSlide( event ) {
    let lineBody = this.parentElement.nextElementSibling;

    event.preventDefault();
    event.stopPropagation();
    lineBody.classList.toggle( 'closed' );
}

/* Botao de remover slide */

const removeSliderButtons = [...document.querySelectorAll(`#simpleslider_metabox .remove-slider`)];

if (removeSliderButtons.length) {
    
    removeSliderButtons.forEach(removeSliderButton => {

        removeSliderButton.addEventListener('click', removeSlide);

    });
}

function removeSlide( event ) {
    let option = this.parentElement.parentElement;

    event.preventDefault();
    event.stopPropagation();
    option.remove();
}

/* Botão para adicionar slide */

const addSlideButton = document.querySelector(`#simpleslider_metabox .add-slide`);

if (addSlideButton) {

    addSlideButton.addEventListener('click', function( event ) {
        event.preventDefault();
        event.stopPropagation();

        let wrapper = document.createElement('div');
        wrapper.innerHTML = simpleSliderEmptyLineHTML;
        
        let line = wrapper.querySelector('.wp-simpleslider-line');
        let minimizeSlideBtn = wrapper.querySelector('.minimize-slider');
        let removeSlideBtn = wrapper.querySelector('.remove-slider');
        let selectImageBtn = wrapper.querySelector('.select-image');
        let updateImageBtn = wrapper.querySelector('.update-image');

        minimizeSlideBtn.addEventListener( 'click', minimizeSlide );
        removeSlideBtn.addEventListener( 'click', removeSlide );
        selectImageBtn.addEventListener( 'click', openMediaPanel );
        updateImageBtn.addEventListener( 'click', openMediaPanel );

        document.querySelector(`#simpleslider_metabox .inside`).appendChild( line );
    });

}