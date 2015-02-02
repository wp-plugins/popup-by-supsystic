jQuery(document).ready(function(){
	// Show/hide additonal subscribe options
	jQuery('#ppsPopupEditForm').find('[name="params[tpl][sub_dest]"]').change(function(){
		jQuery('.ppsPopupSubDestOpts:visible').slideUp( 300 );
		var selectedShell = jQuery('#ppsPopupSubDestOpts_'+ jQuery(this).val());
		if(selectedShell && selectedShell.size()) {
			selectedShell.slideDown( 300 );
		}
	}).change();
	_ppsUpdateMailchimpLists();
	
	jQuery('#ppsPopupEditForm').find('[name="params[tpl][sub_mailchimp_api_key]"]').change(function(){
		_ppsUpdateMailchimpLists();
	});
});
function _ppsGetMailchimpKey() {
	return jQuery.trim( jQuery('#ppsPopupEditForm').find('[name="params[tpl][sub_mailchimp_api_key]"]').val() );
}
function _ppsUpdateMailchimpLists() {
	if(jQuery('#ppsPopupEditForm').find('[name="params[tpl][sub_dest]"]').val() == 'mailchimp') {
		var key = _ppsGetMailchimpKey();
		if(key && key != '') {
			jQuery('#ppsMailchimpLists').hide();
			jQuery('#ppsMailchimpNoApiKey').hide();
			jQuery.sendFormPps({
				msgElID: 'ppsMailchimpMsg'
			,	data: {mod: 'subscribe', action: 'getMailchimpLists', key: key}
			,	onSuccess: function(res) {
					if(!res.error) {
						jQuery('#ppsMailchimpLists').html('');
						var selectedListsIds = ppsPopup && ppsPopup.params.tpl && ppsPopup.params.tpl.sub_mailchimp_lists ? ppsPopup.params.tpl.sub_mailchimp_lists : [];
						for(var listId in res.data.lists) {
							var selected = toeInArrayPps(listId, selectedListsIds) ? 'selected="selected"' : '';
							jQuery('#ppsMailchimpLists').append('<option '+ selected+ ' value="'+ listId+ '">'+ res.data.lists[ listId ]+ '</option>');
						}
						jQuery('#ppsMailchimpLists').show().chosen();;
					}
				}
			});
		} else {
			jQuery('#ppsMailchimpNoApiKey').show();
			jQuery('#ppsMailchimpLists').hide();
		}
	}
}