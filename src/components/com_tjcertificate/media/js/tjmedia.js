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
        if (jQuery(thisFile).attr('type') != 'file') {
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

        if ((fileExtension[fileExtension.length - 1] !== '' || fileExtension[fileExtension.length - 1] !== null) && (jQuery.inArray(fileType, allowedExtensionsArray) == -1)) {
            invalid = "1";
            errorMsg.push(Joomla.JText._('COM_TJCERTIFICATE_MEDIA_INVALID_FILE_TYPE'));
        }

        var uploadedFileSize = uploadedfile.size;


        /** global: attachmentMaxSize */
        if (uploadedFileSize > attachmentMaxSize * 1024 * 1024) {

            invalid = "1";
            errorMsg.push(Joomla.JText._('COM_TJCERTIFICATE_MEDIA_UPLOAD_ERROR'));
            console.log("COM_TIMELOG_FILE_SIZE_ERROR");
        }

        if (invalid) {
            Joomla.renderMessages({
                'error': errorMsg
            });

            jQuery("html, body").animate({
                scrollTop: 0
            }, 500);

            return false;
        }
    },
	deleteAttachment: function(currentElement) {
		if (confirm(Joomla.JText._('COM_TJCERTIFICATE_CONFIRM_DELETE_ATTACHMENT')) == true) {
			var formData = {};

			if (currentElement == '' || currentElement === undefined) {
				return false;
			}

			formData['certificateId'] = jQuery(currentElement).attr('data-aid');
			formData['mediaId'] = jQuery(currentElement).attr('data-mid');

			var promise = tjCertificateService.deleteAttachment(formData);

			promise.fail(
				function(response) {
					var messages = {
						"error": [response.responseText]
					};
					tjCertificateService.renderMessage(messages);
				}
			).done(function(response) {

				if (!response.success && response.message) {
					var messages = {
						"error": [response.message]
					};
					tjCertificateService.renderMessage(messages);
				}

				if (response.messages) {
					tjCertificateService.renderMessage(response.messages);
				}

				if (response.success) {
					tjCertificateService.renderMessage(response.message);
				}

				setTimeout(function() {
					window.location.reload(1);
				}, 2000);
			});
		}
	}
};
