/**
 * @package     TJCertificate
 * @subpackage  com_tjcertificate
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2020 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

var certificate = {
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
					certificate.renderMessage(messages);
				}
			).done(function(response) {

				if (!response.success && response.message) {
					var messages = {
						"error": [response.message]
					};
					certificate.renderMessage(messages);
				}

				if (response.messages) {
					certificate.renderMessage(response.messages);
				}

				if (response.success) {
					certificate.renderMessage(response.message);
				}

				jQuery(currentElement).closest("span.fileupload").remove();
			});
		}
	},
	deleteItem: function(certificateId, obj) {
		if (confirm(Joomla.JText._('COM_TJCERTIFICATE_DELETE_CERTIFICATE_MESSAGE')) == true) {
			var formData = {};

			if (certificateId == '' || certificateId === undefined) {
				return false;
			}

			formData['certificateId'] = certificateId;

			var promise = tjCertificateService.deleteItem(formData);

			promise.fail(
				function(response) {
					var messages = {
						"error": [response.responseText]
					};
					certificate.renderMessage(messages);
				}
			).done(function(response) {

				if (!response.success && response.message) {
					var messages = {
						"error": [response.message]
					};
					certificate.renderMessage(messages);
				}

				if (response.messages) {
					certificate.renderMessage(response.messages);
				}

				if (response.success) {
					certificate.renderMessage(response.message);
				}

				jQuery(obj).closest("tr").remove();
			});
		}
	},
    renderMessage: function(msg) {
        Joomla.renderMessages({
            'alert alert-success': [msg]
        });
        jQuery("html, body").animate({
            scrollTop: 0
        }, 2000);
    },
	validationEndDate: function(expDateObj) {
		var expDate   = jQuery(expDateObj).val();
		var issueDate = jQuery('#jform_issued_on').val();

		jQuery(document).ready(function(){
			document.formvalidator.setHandler('expdate', function (value) {
				if (issueDate > expDate) {
				  certificate.renderMessage(Joomla.JText._('COM_TJCERTIFICATE_EXPIRY_DATE_VALIDATION_MESSAGE'));
				  jQuery('#jform_expired_on').val("");

				  return false;
				}

		    return true;

			});
		});
	},
	getAgencyUsers: function(agencyObj) {
		var formData = {};
		var clusterusers = jQuery('#jform_assigned_user_id');
		var assignedUser = jQuery('#assigned_user_id').val();
		formData['agency_id'] = jQuery(agencyObj).val();

		var promise = tjCertificateService.getAgencyUsers(formData);

			promise.fail(
				function(response) {
					var messages = {
						"error": [response.responseText]
					};
					Joomla.renderMessage(messages);
				}
			).done(function(response) {

				if (!response)
				{
					return false;
				}

				if (response.success) {
					clusterusers.empty();
					clusterusers.trigger("liszt:updated");

					var data = response.data;

					for(var index = 0; index < data.length; ++index)
					{
						selectOption = '';
						if (assignedUser == data[index].value)
						{
							selectOption = ' selected="selected" ';
						}
						op="<option value='"+data[index].value+"' "+selectOption+" > " + data[index]['text'] + "</option>" ;
						clusterusers.append(op);
					}

					/* IMP : to update to chz-done selects*/
					clusterusers.trigger("liszt:updated");
				}
			});
		},
		addRecords: function() {
			certificate.showLoader();
			var formData    = jQuery('.add-records').serialize();
			var params      = {};
			params['async'] = true;
			var promise     = tjCertificateService.addRecords(formData,params);

			promise.fail(
				function(response) {
					var messages = {"error": [response.responseText]};
					Joomla.renderMessages(messages);
				}
			).done(function(response) {
				jQuery.LoadingOverlay("hide");

				if (!response.success && response.message){
					var messages = { "error": [response.message]};
					Joomla.renderMessages(messages);
				}

				if (response.success) {
					certificate.renderMessage(response.data.msg);
					jQuery('#adminForm').trigger("reset");
					jQuery('#jform_assigned_user_id').trigger("liszt:updated");
				}
			});
		},
		showLoader: function() {
			jQuery.LoadingOverlay("show", {
				image : Joomla.getOptions('system.paths').root + "/media/com_tjcertificate/images/loader/loader.gif",
			});
		}
};


jQuery(document).ready(function ($) {
    const config = Joomla.getOptions('tjcertificate.bulkDownload');

    $('#bulkCertBtn').on('click', function (e) {
        e.preventDefault();

        $('#ziploader').fadeIn();

        $.ajax({
            url: Joomla.getOptions('system.paths').base + '/index.php?option=com_tjcertificate&task=certificates.fetchCertificatesForBulkDownload',
            type: 'POST',
            dataType: 'json',
            data: {
                user_id: config.user_id,
                client: config.client,
                state: config.state,
                [config.token]: 1
            },
            success: function (response) {
                if (response.success && response.data.length > 0) {
                    let html = '<h4>Downloaded Certificates</h4><ul>';
                    $('#certificateModalOverlay').fadeIn();

                    response.data.forEach(function (cert) {
                        html += '<div id="certificateContent' + cert.id + '" style="width: 1196px; height: auto;">' + cert.generated_body + '</div>';
                    });

                    $('#certificateModalContent').html(html);

                    const generatePromises = response.data.map(cert => {
                        const imageUrl = `${config.certRootUrl}media/com_tjcertificate/certificates/${cert.unique_certificate_id}.png`;
                        const certEl = document.getElementById('certificateContent' + cert.id);

                        return checkImageExists(imageUrl).then(exists => {
                            return exists ? Promise.resolve() : generateImage(certEl, cert.unique_certificate_id);
                        });
                    });

                    Promise.all(generatePromises).then(() => {
                        const form = $('<form>', {
                            method: 'POST',
                            action: Joomla.getOptions('system.paths').base + '/index.php?option=com_tjcertificate&task=certificates.bulkCertificateDownload',
                            style: 'display: none;'
                        });

                        form.append($('<input>', {
                            type: 'hidden',
                            name: 'user_id',
                            value: config.user_id
                        }));

                        response.data.forEach((cert, index) => {
                            form.append($('<input>', {
                                type: 'hidden',
                                name: 'certificates[' + index + '][unique_certificate_id]',
                                value: cert.unique_certificate_id
                            }));
                        });

                        form.append($('<input>', {
                            type: 'hidden',
                            name: config.token,
                            value: 1
                        }));

                        $('body').append(form);
                        $('#ziploader').fadeOut();
                        form.submit();
                        form.remove();

                        $('#certificateModalContent').html('<div class="alert alert-success">Certificates downloaded successfully.</div>');
                        setTimeout(() => $('#certificateModalOverlay').fadeOut(), 2000);
                    });

                } else {
                    $('#ziploader').fadeOut();
                    alert(response.message || 'No certificates found.');
                }
            },
            error: function (xhr) {
                $('#ziploader').fadeOut();
                console.error("AJAX Error", xhr.responseText);
            }
        });
    });

    $('#closeModalBtn').on('click', function () {
        $('#certificateModalOverlay').fadeOut();
    });
});

function generateImage($html, $item) {
    return new Promise((resolve, reject) => {
        if (typeof html2canvas === 'undefined') {
            console.error('html2canvas is not available');
            reject('Missing html2canvas');
            return;
        }

        html2canvas($html, {
            scrollX: 0,
            scrollY: -window.scrollY,
            allowTaint: true,
            useCORS: true
        }).then(function (canvas) {
            const imageData = canvas.toDataURL('image/png');

            jQuery.ajax({
                url: Joomla.getOptions('system.paths').base + '/index.php?option=com_tjcertificate&task=certificate.uploadCertificate',
                type: 'POST',
                data: {
                    image: imageData,
                    certificateId: $item
                },
                success: function () {
                    resolve();
                },
                error: function (xhr) {
                    console.error("Upload failed for certificate " + $item, xhr.responseText);
                    reject(xhr.responseText);
                }
            });

        }).catch(function (error) {
            console.error("html2canvas failed:", error);
            reject(error);
        });
    });
}

function checkImageExists(imageUrl) {
    return new Promise((resolve) => {
        const xhr = new XMLHttpRequest();
        xhr.open('HEAD', imageUrl, true);
        xhr.onload = () => resolve(xhr.status === 200);
        xhr.onerror = () => resolve(false);
        xhr.send();
    });
}