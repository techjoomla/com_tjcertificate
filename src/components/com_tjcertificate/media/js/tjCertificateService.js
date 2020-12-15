/**
 * @package     TJCertificate
 * @subpackage  com_tjcertificate
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2019 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

var tjCertificateService = {

	siteRoot: Joomla.getOptions("system.paths").base,
	loadDefaultTemplateUrl: '/index.php?option=com_tjcertificate&task=template.loadDefaultTemplate&format=json',
	loadCustomTemplateUrl: '/index.php?option=com_tjcertificate&task=template.loadCustomTemplate&format=json',
	deleteAttachmentUrl: '/index.php?option=com_tjcertificate&task=trainingrecord.deleteAttachment&format=json',
	deleteItemUrl: '/index.php?option=com_tjcertificate&task=trainingrecord.delete&format=json',

	postData: function(url, formData, params) {
		if(!params){
			params = {};
		}

		params['url']		    = this.siteRoot + url;
		params['data'] 		    = formData;
		params['type'] 		    = typeof params['type'] != "undefined" ? params['type'] : 'POST';
		params['async'] 	    = typeof params['async'] != "undefined" ? params['async'] :false;
		params['dataType'] 	    = typeof params['datatype'] != "undefined" ? params['datatype'] : 'json';
		params['contentType'] 	= typeof params['contentType'] != "undefined" ? params['contentType'] : 'application/x-www-form-urlencoded; charset=UTF-8';
		params['processData'] 	= typeof params['processData'] != "undefined" ? params['processData'] : true;

		var promise = jQuery.ajax(params);
		return promise;
	},
	loadDefaultTemplate: function (formData, params) {
		return this.postData(this.loadDefaultTemplateUrl, formData, params);
	},
	loadCustomTemplate: function (formData, params) {
		return this.postData(this.loadCustomTemplateUrl, formData, params);
	},
	deleteAttachment: function (formData, params) {
		return this.postData(this.deleteAttachmentUrl, formData, params);
	},
	deleteItem: function(certificateId) {
		if (confirm(Joomla.JText._('COM_TJCERTIFICATE_DELETE_CERTIFICATE_MESSAGE')) == true) {
			var formData = {};

			if (certificateId == '' || certificateId === undefined)
			{
				return false;
			}

			formData['certificateId'] = certificateId;
			var data = this.postData(this.deleteItemUrl, formData)

			data.fail(
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
				if (issueDate < expDate == false)
				{
					tjCertificateService.renderMessage(Joomla.JText._('COM_TJCERTIFICATE_EXPIRY_DATE_VALIDATION_MESSAGE'));
					jQuery('#jform_expired_on').val("");
				}
			});
		});
	}
}
