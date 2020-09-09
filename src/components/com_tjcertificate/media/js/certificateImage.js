var certificateImage = {

    printCertificate: function(elementId) {
        var printContent = document.getElementById(elementId).innerHTML;
        var originalContent = document.body.innerHTML;
        document.body.innerHTML = printContent;
        window.print();
        document.body.innerHTML = originalContent;

        certificateImage.enableDownloadShareBtns();
    },

    enableDownloadShareBtns: function()
    {
        jQuery("#download-popover").popover({
            trigger: 'focus',
            html: true,
            content: jQuery('#download-popover-content').html()
        });

        jQuery("#sharing-popover").popover({
            trigger: 'focus',
            html: true,
            content: jQuery('#sharing-popover-content').html()
        });
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
		jQuery('#certificateContent').width(element.offsetWidth).height(element.offsetHeight);

        html2canvas(element, {
            scale: (2),
            scrollX: 0,
            scrollY: -window.scrollY,
            allowTaint: true
        }).then(function(canvas) {
            certificateImage.uploadImage(canvas.toDataURL('image/png'));
			jQuery("#downloadImage").attr("href", canvas.toDataURL('image/png')).attr(
			"download", certificateId + '.png');
        });
    }
}
