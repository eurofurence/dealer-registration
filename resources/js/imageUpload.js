/*
 This file replaces the inline javascript on the profile form.
 Previously there was only code to update the preview image from the file input field.
*/

//   document.getElementById('image_thumbnail_preview').src = window.URL.createObjectURL(this.files[0]);
//   document.getElementById('image_thumbnail_preview_large').src = window.URL.createObjectURL(this.files[0]);

// Install the appropriate event listeners in all candidate elements
function installImageInputEventHandlers() {

    /*
     Handle <input type="file" data-file-size-limit="NNN"> where NNN is the maximum length in bytes.
     Will show an alert box and clear the file selection.
    */
    document.querySelectorAll('input[type="file"][data-file-size-limit]').forEach((el) => {
        const fileSizeLimit = parseInt(el.dataset.fileSizeLimit);
        // Fired anytime the selected file is changed (this also applies to "cancelling" the picker, removing the image).
        el.addEventListener('change', () => {
            const file = el.files[0];
            console.log({fsl:file})
            if (file && file.size > fileSizeLimit) {
                alert('The file you chose is too big (file size)!');
                // Clear the file field, this also changes the value for subsequent onChange events (see below).
                el.value = null;
            }
        });
    });

    /*
     Handle <input type="file" data-image-preview-targets="AAA,BBB"> where AAA, BBB are IDs of img-Tags.
     Will replace the src of all img tags given by the selected source file.
    */
    document.querySelectorAll('input[type="file"][data-image-preview-targets]').forEach((el) => {
        // Split the list of IDs into an array
        const imagePreviewTargets = el.dataset.imagePreviewTargets.split(',');
        // Get the elements, filter out those which did not resolve (just for safety)
        const targetElements =  imagePreviewTargets.map((id) => document.getElementById(id)).filter((el) => !!el);
        // Store the initial src
        targetElements.forEach((tel) => {
            if (!tel.dataset.originalSrcValue) {
                tel.dataset.originalSrcValue = tel.src;
            }
        });
        // Fired anytime the selected file is changed (this also applies to "cancelling" the picker, removing the image).
        el.addEventListener('change', () => {
            const file = el.files[0];
            console.log({pec:file})
            if (file) {
                const newSrc = window.URL.createObjectURL(file);
                targetElements.forEach((tel) => tel.src = newSrc);
            } else {
                targetElements.forEach((tel) => tel.src = tel.dataset.originalSrcValue);
            }
        });
    });

}

installImageInputEventHandlers();
