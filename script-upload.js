jQuery(document).ready(function() {

	jQuery('#upload-image-button').click(function() {
		formfield = jQuery('#upload-image').attr('name');
		tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
		return false;
	});

	window.send_to_editor = function(html) {
		imgurl = jQuery('img',html).attr('src');
		jQuery('#upload-image').val(imgurl);
		tb_remove();
		generatePreview();
	}

});

function generatePreview() {
	if(document.getElementById('img-checkbox').checked) {
		var img = jQuery('#upload-image').val();
		var url = jQuery('#target-url').val();
		jQuery('#preview-box').html('<a href="' + url + '"><img src="' + img + '"></a>');
	} else {
		var code = jQuery('#the-code').val();
		jQuery('#preview-box').html(code);
	}
}

function toggleCheckbox( self ) {
	context = jQuery(self).parent().parent().parent().parent();
	if(jQuery(self).attr('checked') == 'checked')
		jQuery('input', context).attr('checked', 'checked');
	else
		jQuery('input', context).removeAttr('checked');
}