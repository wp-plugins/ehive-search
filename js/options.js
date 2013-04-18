jQuery(document).ready(function(){
	
	if (jQuery('#results_view_lightbox_enabled_default').attr('checked') == 'checked') {
		jQuery('#results_view_lightbox_enabled').attr('checked', 'checked');
	}
	
	if (jQuery('#results_view_list_enabled_default').attr('checked') == 'checked') {
		jQuery('#results_view_list_enabled').attr('checked', 'checked');
	}
	
	jQuery('#results_view_list_enabled_default').live('click', function() {
		if (jQuery('#results_view_list_enabled').attr('checked') != 'checked') {
			jQuery('#results_view_list_enabled').attr('checked', 'checked');
		}
	});
	
	jQuery('#results_view_lightbox_enabled_default').live('click', function() {
		if (jQuery('#results_view_lightbox_enabled').attr('checked') != 'checked') {
			jQuery('#results_view_lightbox_enabled').attr('checked', 'checked');
		}
	});
	
	jQuery('#results_view_lightbox_enabled').live('click', function() {
		if (jQuery('#results_view_lightbox_enabled_default').attr('checked') == 'checked') {
			alert('"Lightbox" must be enabled if it is the default view.');
			jQuery(this).attr('checked', 'checked');
		}
	});
	
	jQuery('#results_view_list_enabled').live('click', function() {
		if (jQuery('#results_view_list_enabled_default').attr('checked') == 'checked') {
			alert('"List" must be enabled if it is the default view.');
			jQuery(this).attr('checked', 'checked');
		}
	});
	
});