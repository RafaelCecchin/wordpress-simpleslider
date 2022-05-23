/* Opções de imagens do post */

if (document.querySelector(`.wp-simpleslider-option-container[data-type="image"]`)) {    
    updateImageButtons();
}

function updateImageButtons() {
    let imageOptions = [...document.querySelectorAll(`.wp-simpleslider-option-container[data-type="image"]`)];
    
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
    // let optionName = this.getAttribute('data-target');
    let inputImage = this.parentElement.querySelector(`input`);
    let imagePreview = this.parentElement.querySelector(`img`);

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
        imagePreview.src = attachment.url;

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
    let imagePreview = this.parentElement.querySelector(`img`);

    let inputImage   = document.querySelector(` input[name="${optionName}"] `);
    inputImage.value = "";
    imagePreview.src = "";
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
        
        let selectImageBtns = wrapper.querySelectorAll('.select-image');
        let updateImageBtns = wrapper.querySelectorAll('.update-image');
        let removeImageBtns = wrapper.querySelectorAll('.remove-image');

        let optionID = new Date().getTime();
        let options = [...wrapper.querySelectorAll(`.wp-simpleslider-option-container`)];
        if (options.length) {
            options.forEach( option => {
                let inputs = [...option.querySelectorAll(`input`)];
                let labels = [...option.querySelectorAll(`label`)];

                inputs.forEach(input => {
                    let name = input.name;
                    let id = input.id;

                    let new_name = name.replace( '[]', '['+optionID+']' );
                    input.name = new_name;

                    let new_id = id.replace( '[]', '['+optionID+']' );
                    input.id = new_id;
                });
                
                labels.forEach(label => {
                    let forID = label.getAttribute("for");
                    let newForID = forID.replace( '[]', '['+optionID+']' );

                    label.setAttribute( "for", newForID );
                });
            });      
        }

        minimizeSlideBtn.addEventListener( 'click', minimizeSlide );
        removeSlideBtn.addEventListener( 'click', removeSlide );
        
        selectImageBtns.forEach(element => {
            element.addEventListener( 'click', openMediaPanel );
        });

        updateImageBtns.forEach(element => {
            element.addEventListener( 'click', openMediaPanel );
        });

        removeImageBtns.forEach(element => {
            element.addEventListener( 'click', removeMediaValue );
        });

        document.querySelector(`#simpleslider_metabox .inside`).appendChild( line );
    });

}