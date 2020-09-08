jQuery(document).ready(function() {
    certificateImage.generateImage(document.querySelector("#certificateContent"));
});

var certificateImage = {

    printCertificate: function(elementId) {
        var printContent = document.getElementById(elementId).innerHTML;
        var originalContent = document.body.innerHTML;
        document.body.innerHTML = printContent;
        window.print();
        document.body.innerHTML = originalContent;
    },

    uploadImage: function(image) {
        var result = false;
        var certificateId = jQuery("#certificateId").val();
        jQuery.ajax({
            url: Joomla.getOptions('system.paths').base + "/index.php?option=com_tjcertificate&task=certificate.uploadCertificate",
            type: 'POST',
            data: {
                image: image,
                certificateId: certificateId
            },
            success: function(data) {
                result = data;
            }
        });

        return result;
    },

    generateImage: function(element) {
        var certificateId = jQuery("#certificateId").val();
        var imagePath = Joomla.getOptions('system.paths').base + '/media/com_tjcertificate/certificates/';
        var img = document.createElement('img');

        html2canvas(element, {
            scale: (2),
            scrollX: 0,
            scrollY: -window.scrollY,
            backgroundColor: null,
            allowTaint: true
        }).then(function(canvas) {
            jQuery('#certificateContent').hide();
            certificateImage.uploadImage(canvas.toDataURL('image/png'));
            img.src = imagePath + certificateId + ".png";
            jQuery("#previewImage").append(img);
            jQuery("#btn-Convert-Html2Image").attr(
                "download", certificateId + '.png').attr(
                "href", canvas.toDataURL('image/png'));
        });
    }
}
