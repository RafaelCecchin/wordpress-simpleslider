const imageOptions = [...document.querySelectorAll(`.wp_simpleslider_option_container[data-type="image"]`)];

if (imageOptions.length) {
    
    updateImageButtons();

}

function updateImageButtons() {
    const selectImageText = 'Selecionar imagem';
    const updateImageText = 'Atualizar imagem';
    const removeImageText = 'Remover imagem';

    imageOptions.forEach(imageOption => {

        let inputImage        = imageOption.querySelector(`input`);
        let selectImageButton = imageOption.querySelector(`.select-image`);
        let updateImageButton = imageOption.querySelector(`.update-image`);
        let removeImageButton = imageOption.querySelector(`.remove-image`);

        selectImageButton.innerHTML = selectImageText;
        updateImageButton.innerHTML = updateImageText;
        removeImageButton.innerHTML = removeImageText;

        (inputImage.value != "") ? imageOption.classList.add('has-value') : imageOption.classList.remove('has-value');
        
        selectImageButton.addEventListener('click', openMediaPanel);
        updateImageButton.addEventListener('click', openMediaPanel);
        removeImageButton.addEventListener('click', removeMediaValue);
    });
}


function openMediaPanel() {
    let optionName = this.getAttribute('data-target');

    let inputImage        = document.querySelector(`input[name="${optionName}"]`);

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