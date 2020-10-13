/**
 * @package     TJCertificate
 * @subpackage  com_tjcertificate
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2019 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

var template = {

	previewTemplate: function (id) {
		jQuery(document).on('click', 'button[data-target="#templatePreview"]', function () {
			
			jQuery('#show-info').hide();
			var editorId = jQuery('#'+id);

			if (typeof tinyMCE != "undefined")
			{
			   tinyMCE.execCommand('mceToggleEditor', false, id);
			}
			else if (typeof CodeMirror != "undefined")
			{
				var editor = document.querySelector('.CodeMirror').CodeMirror;
				editorId.html(editor.getValue());
			}
			else
			{
				jQuery('#show-info').show();
			}

			jQuery('#previewTempl').empty();
			jQuery('<style>').html(jQuery('#jform_template_css').val()).appendTo('#previewTempl');
			jQuery('<div>').html(editorId.val()).appendTo('#previewTempl');
		});

		jQuery('#templatePreview').on('hidden.bs.modal', function () {

			if (typeof tinyMCE != "undefined")
			{
			   tinyMCE.execCommand('mceToggleEditor', false, id);
			}

			jQuery('#previewTempl').empty();
		});
	},
	loadDefaultTemplate: function (defaultTemplate) {

		var formData = {};

		if (defaultTemplate == '' || defaultTemplate === undefined)
		{
			return false;
		}

		formData['defaultTemplate'] = defaultTemplate;

		var promise = tjCertificateService.loadDefaultTemplate(formData);

		promise.fail(
			function(response) {
				var messages = { "error": [response.responseText]};
				Joomla.renderMessages(messages);
			}
		).done(function(response) {
			if (!response.success && response.message)
			{
				var messages = { "error": [response.message]};
				Joomla.renderMessages(messages);
			}

			if (response.messages){
				Joomla.renderMessages(response.messages);
			}

			if (response.success) {
				template.renderDefaultTemplate(response);
			}
		});
	},
	renderDefaultTemplate: function (response)
	{
		var templateBody = response.data;

		jQuery('#jform_body').empty().val(templateBody);

		if (typeof tinyMCE != "undefined")
		{
			tinyMCE.get('jform_body').setContent(templateBody);
		}
		else if (typeof CodeMirror != "undefined")
		{
			var editor = document.querySelector('.CodeMirror').CodeMirror;

			editor.setValue(templateBody);
		}
	},
	
	renderCustomTemplate: function(templateId) 
	{
		var siteRoot = Joomla.getOptions("system.paths").base;
		jQuery.ajax({
			url: siteRoot + "/index.php?option=com_tjcertificate&task=template.loadCustomTemplate&format=json",
			type: 'POST',
			data: {
				templateId: templateId
			},
			success: function(response) {
				var templateBody = response.data;

				jQuery('#jform_generated_body').empty().val(templateBody);

				if (typeof tinyMCE != "undefined") {
					tinyMCE.get('jform_generated_body').setContent(templateBody);
				} else if (typeof CodeMirror != "undefined") {
					var editor = document.querySelector('.CodeMirror').CodeMirror;

					editor.setValue(templateBody);
				}
			}
		});
	}
}
