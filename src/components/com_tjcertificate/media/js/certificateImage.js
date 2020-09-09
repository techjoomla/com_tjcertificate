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
            url: certRootUrl + "index.php?option=com_tjcertificate&task=certificate.uploadCertificate",
            type: 'POST',
            data: {
                image: image,
                certificateId: certificateId
            },
            success: function(data) {
                result = data;
                Joomla.loadingLayer('hide');
            }
        });

        return result;
    },

    generateImage: function(element) {
        var certificateId = jQuery("#certificateId").val();
        var imagePath = certRootUrl + 'media/com_tjcertificate/certificates/';
		jQuery('#certificateContent').width(element.offsetWidth).height(element.offsetHeight);
        Joomla.loadingLayer('show');

        html2canvas(element, {
            scale: (2),
            scrollX: 0,
            scrollY: -window.scrollY,
            allowTaint: true
        }).then(function(canvas) {
			jQuery("#downloadImage").attr("href", canvas.toDataURL('image/png'));
			certificateImage.enableDownloadShareBtns();
            certificateImage.uploadImage(canvas.toDataURL('image/png'));
        });
    }
}
