jQuery(document).ready(function() {
    jQuery('#btn-Convert-Html2Image').click(function() {
        download(document.querySelector("#certificateContent"));
    });
});

function printCertificate(elementId) {
    var printContent = document.getElementById(elementId).innerHTML;
    var originalContent = document.body.innerHTML;
    document.body.innerHTML = printContent;
    window.print();
    document.body.innerHTML = originalContent;
}

function download(element) {
    var certificateId = jQuery("#certificateId").val();
    html2canvas(element, {
        width: 750,
        scrollX: 0,
        scrollY: -window.scrollY,
        backgroundColor: null,
    }).then(function(canvas) {
        var link = document.createElement("a");
        document.body.appendChild(link);
        link.download = certificateId + '.jpeg';
        link.href = canvas.toDataURL("image/png");
        link.target = '_blank';
        link.click();
    });
}


function uploadImage(image) {
    var result = false;
    var certificateId = jQuery("#certificateId").val();
    jQuery.ajax({
        url: Joomla.getOptions('system.paths').base + "/index.php?option=com_tjcertificate&task=certificate.uploadCertificate",
        type: 'POST',
        async: false,
        dataType: "text",
        data: {
            image: image,
            certificateId: certificateId
        },
        success: function(data) {
            result = data;
        }
    });

    return result;
}

function saveCapture(element) {
	var url = false;
	html2canvas(element,
	{
		width:750,
		scrollX:0,
		scrollY: -window.scrollY, 
		backgroundColor:null 
	}).then(function(canvas) {
		url = uploadImage(canvas.toDataURL("image/png"));	
		jQuery('meta[property="og:image"]').attr('content', url);
		jQuery('meta[name="twitter:image"]').attr('content', url);
		jQuery('#certificateUrl').val(url);
	});
}
