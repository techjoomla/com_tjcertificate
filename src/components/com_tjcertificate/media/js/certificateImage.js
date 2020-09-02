jQuery(document).ready(function() {
   certificateImage.generateImage(document.querySelector("#certificateContent"));
});

var certificateImage = {

	printCertificate: function (elementId) {
		var printContent = document.getElementById(elementId).innerHTML;
		var originalContent = document.body.innerHTML;
		document.body.innerHTML = printContent;
		window.print();
		document.body.innerHTML = originalContent;
	},

	uploadImage: function (image) {
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
	},

	generateImage: function (element) {
		var url = false;
		var certificateId = jQuery("#certificateId").val();
		html2canvas(element,
		{
			scale:(2),
			width:element.offsetWidth+20,
			scrollX:0,
			scrollY: -window.scrollY, 
			backgroundColor:null,
			allowTaint:true
		}).then(function(canvas) {
			element.style.display = 'none';
			url = certificateImage.uploadImage(canvas.toDataURL('image/png'));	
			jQuery("#previewImage").append(canvas);
			jQuery('meta[property="og:image"]').attr('content', url);
			jQuery('meta[name="twitter:image"]').attr('content', url);
			jQuery('#certificateUrl').val(url);
			jQuery("#btn-Convert-Html2Image").attr( 
			"download", certificateId+'.png').attr( 
			"href", canvas.toDataURL('image/png')); 

			return url;
	  });
	}
}
