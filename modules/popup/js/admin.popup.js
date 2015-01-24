jQuery(document).ready(function(){
	if(typeof(ppsOriginalPopup) !== 'undefined') {	// Just changing template - for existing popup
		ppsInitChangePopupDialog();
	} else {			// Creating new popup
		ppsInitCreatePopupDialog();
	}
	jQuery('.popup-list-item').mouseover(function(){
		var desc = jQuery(this).find('.ppsNewPopupDesc');
		if(!desc.hasClass('perspectiveDownRetourn')) {
			desc.removeClass('perspectiveDown').addClass('magictime perspectiveDownRetourn').show();
		}
	}).mouseout(function(){
		var desc = jQuery(this).find('.ppsNewPopupDesc');
		if(!desc.hasClass('perspectiveDown')) {
			desc.removeClass('perspectiveDownRetourn').addClass('perspectiveDown');
		}
	}).animationDuration(0.5);
});
function ppsInitChangePopupDialog() {
	var $container = jQuery('#ppsChangeTplWnd').dialog({
		modal:    true
	,	autoOpen: false
	,	width: 460
	,	height: 180
	,	buttons:  {
			OK: function() {
				jQuery('#ppsChangeTplForm').submit();
			}
		,	Cancel: function() {
				$container.dialog('close');
			}
		}
	});
	jQuery('.popup-list-item').click(function(){
		var id = jQuery(this).data('id');
		if(ppsOriginalPopup.original_id == id) {
			var dialog = jQuery('<div />').html(toeLangPps('This is same template that was used for Pop-Up before')).dialog({
				modal:    true
			,	width: 460
			,	height: 180
			,	buttons: {
					OK: function() {
						dialog.dialog('close');
					}
				}
			,	close: function() {
					dialog.remove();
				}
			});
			return false;
		}
		jQuery('#ppsChangeTplForm').find('[name=id]').val( ppsOriginalPopup.id );
		jQuery('#ppsChangeTplForm').find('[name=new_tpl_id]').val( id );
		jQuery('#ppsChangeTplNewLabel').html( jQuery(this).find('.ppsTplLabel').html() )
		jQuery('#ppsChangeTplMsg').html('');
		$container.dialog('open');
		return false;
	});
	jQuery('#ppsChangeTplForm').submit(function(){
		jQuery(this).sendFormPps({
			msgElID: 'ppsChangeTplMsg'
		,	onSuccess: function(res) {
				if(!res.error && res.data.edit_link) {
					toeRedirect( res.data.edit_link );
				}
			} 
		});
		return false;
	});
}
function ppsInitCreatePopupDialog() {
	var $container = jQuery('#ppsCreatePopupWnd').dialog({
		modal:    true
	,	autoOpen: false
	,	width: 460
	,	height: 180
	,	buttons:  {
			OK: function() {
				jQuery('#ppsCreatePopupForm').submit();
			}
		,	Cancel: function() {
				$container.dialog('close');
			}
		}
	});
	jQuery('.popup-list-item').click(function(){
		jQuery('#ppsCreatePopupForm').find('[name=original_id]').val( jQuery(this).data('id') );
		jQuery('#ppsCreatePopupMsg').html('');
		$container.dialog('open');
		return false;
	});
	jQuery('#ppsCreatePopupForm').submit(function(){
		jQuery(this).sendFormPps({
			msgElID: 'ppsCreatePopupMsg'
		,	onSuccess: function(res) {
				if(!res.error && res.data.edit_link) {
					toeRedirect( res.data.edit_link );
				}
			} 
		});
		return false;
	});
}