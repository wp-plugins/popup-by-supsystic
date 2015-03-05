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
			jQuery(this).find('.ppsTplPrevImg').stop(true, true).animate({
				opacity: 0.15
			}, 500);
		}
	}).mouseleave(function(e){
		var desc = jQuery(this).find('.ppsNewPopupDesc');
		if(!desc.hasClass('perspectiveDown')) {
			desc.removeClass('perspectiveDownRetourn').addClass('perspectiveDown');
			jQuery(this).find('.ppsTplPrevImg').stop(true, true).animate({
				opacity: 1
			}, 500);
		}
	}).animationDuration(0.5);
	if(jQuery('.ppsTplPrevImg').size()) {	// If on creation page
		ppsAdjustPreviewSize();
		jQuery(window).resize(function(){
			ppsAdjustPreviewSize();
		});
	}
});

function ppsAdjustPreviewSize() {
	var shellWidth = parseInt(jQuery('.popup-list').width())
	,	initialMaxWidth = 400
	,	startFrom = 860
	,	endFrom = 500;
	if(shellWidth < startFrom && shellWidth > endFrom) {
		jQuery('.ppsTplPrevImg').css('max-width', initialMaxWidth - Math.floor((startFrom - shellWidth) / 2));
	} else if(shellWidth < endFrom || shellWidth > startFrom) {
		jQuery('.ppsTplPrevImg').css('max-width', initialMaxWidth);
	}
}
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
			,	width: 480
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
	jQuery('.popup-list-item').click(function(){
		jQuery('.popup-list-item').removeClass('active');
		jQuery(this).addClass('active');
		jQuery('#ppsCreatePopupForm').find('[name=original_id]').val( jQuery(this).data('id') );
		//jQuery('#ppsCreatePopupMsg').html('');
		return false;
	});
	jQuery('#ppsCreatePopupForm').submit(function(){
		jQuery(this).sendFormPps({
			btn: jQuery(this).find('button')
		,	onSuccess: function(res) {
				if(!res.error && res.data.edit_link) {
					toeRedirect( res.data.edit_link );
				}
			} 
		});
		return false;
	});
}
function ppsPopupRemoveRow(id, link) {
	var tblId = jQuery(link).parents('table.ui-jqgrid-btable:first').attr('id');
	if(confirm(toeLangPps('Are you sure want to remove "'+ ppsGetGridColDataById(id, 'label', tblId)+ '" Pop-Up?'))) {
		jQuery.sendFormPps({
			btn: link
		,	data: {mod: 'popup', action: 'remove', id: id}
		,	onSuccess: function(res) {
				if(!res.error) {
					jQuery('#'+ tblId).trigger( 'reloadGrid' );
				}
			}
		});
	}
}