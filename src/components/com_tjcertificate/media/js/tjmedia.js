/**
 * @package     TJCertificate
 * @subpackage  com_tjcertificate
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2020 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

var tjMediaFile = {
	validateFile: function(thisFile) {
		/** Validation is for file field only */
		if (jQuery(thisFile).attr('type') != 'file')
		{
			return false;
		}

		/** Clear error message */
		jQuery('#system-message-container').empty();

		var uploadedfile = jQuery(thisFile)[0].files[0];
		var fileType = uploadedfile.type;
		var fileExtension = uploadedfile.name.split(".");

		/** global: allowedAttachments */
		var allowedExtensionsArray = allowedAttachments.split(",");

		var invalid = 0;
		var errorMsg = new Array();

		if ((fileExtension[fileExtension.length-1] !== ''|| fileExtension[fileExtension.length-1] !== null) && (jQuery.inArray(fileType , allowedExtensionsArray) == -1))
		{
			invalid = "1";
			errorMsg.push(Joomla.JText._('COM_TJCERTIFICATE_MEDIA_INVALID_FILE_TYPE'));
		}

		var uploadedFileSize       = uploadedfile.size;


		/** global: attachmentMaxSize */
		if (uploadedFileSize > attachmentMaxSize * 1024 *1024)
		{

			invalid = "1";
			errorMsg.push(Joomla.JText._('COM_TJCERTIFICATE_MEDIA_UPLOAD_ERROR'));
			console.log("COM_TIMELOG_FILE_SIZE_ERROR");
		}

		if (invalid)
		{
			Joomla.renderMessages({'error': errorMsg});

			jQuery("html, body").animate({
				scrollTop: 0
			}, 500);

			return false;
		}
	},
	deleteAttachment: function(task, currentElement, jtoken)
	{
		if(confirm(Joomla.JText._('COM_TJCERTIFICATE_CONFIRM_DELETE_ATTACHMENT')) == true)
		{
			var certificateId = jQuery(currentElement).attr('data-aid');
			var mediaId    = jQuery(currentElement).attr('data-mid');

			jQuery.ajax({
				url: Joomla.getOptions('system.paths').base + "/index.php?option=com_tjcertificate&" + jtoken + "=1",
				data: {
					certificateId: certificateId,
					mediaId: mediaId,
					task: task
				},
				type: 'POST',
				dataType:'JSON',
				success: function(data) {
					let msg = data.message;
					if (data.success === true)
					{
						Joomla.renderMessages({'alert alert-success': [msg]});
						jQuery("html, body").animate({
							scrollTop: 0
						}, 2000);
					}
					else
					{
						Joomla.renderMessages({'alert alert-error': [msg]});
						jQuery("html, body").animate({
							scrollTop: 0
						}, 2000);
					}
					setTimeout(function(){
						window.location.reload(1);
					}, 2000);
				}
			});
		}
		else
		{
			return false;
		}
	}
};
