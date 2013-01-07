function toggleCheckbox( self ) {
	context = jQuery(self).parent().parent().parent().parent();
	if(jQuery(self).attr('checked') == 'checked')
		jQuery('input', context).attr('checked', 'checked');
	else
		jQuery('input', context).removeAttr('checked');
}

function saveAd( self ) {
	self   = jQuery(self).parent().parent();
	id     = jQuery('.id a', self).html();
	name   = jQuery('.name a', self).html();
	added  = jQuery('.added', self).html();
	views  = jQuery('.views', self).val();
	clicks = jQuery('.clicks', self).val();
	if( views > 0 )
		ctr = (clicks/views * 100) + '%';
	else
		ctr = '0%';
	
	code = jQuery('<tr></tr>');
	jQuery(code).append('<td><input type="checkbox" name="delete[]" value="' + id + '"></td>');
	jQuery(code).append('<td>' + id + '</td>');
	jQuery(code).append('<td>' + name + '</td>');
	jQuery(code).append('<td>' + added + '</td>');
	jQuery(code).append('<td>' + views + '</td>');
	jQuery(code).append('<td>' + clicks + '</td>');
	jQuery(code).append('<td>' + ctr + '</td>');
	jQuery(code).append('<input type="hidden" name="ads[]" value="' + id + '">');
	
	if( noadsyet ) {
		jQuery('#ads-table').html(code);
		noadsyet = false;
	} else {
		jQuery('#ads-table').append(code);
	}
	
	tb_remove();
}

function generatePopup() {
	tb_show('Пошук реклами', adm_popup_path);
}

function ajaxResults() {
	jQuery('#popup-ads-table').html('<td></td><td style="text-align:center"><img src="' + adm_loading_gif_path + '" alt="Завантажується..." width="16" height="11"></td><td></td>');
	jQuery('#popup-ads-table').load(adm_search_path + encodeURI(jQuery('#search-term').val()));
}

function deleteSelected() {
	jQuery('#ads-table input').each(function(){
		if(jQuery(this).attr('checked') == 'checked')
			jQuery(this).parent().parent().detach();
	});
}