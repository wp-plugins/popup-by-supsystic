jQuery(document).ready(function(){
	if(typeof(ppsPopups) !== 'undefined' && ppsPopups && ppsPopups.length) {
		ppsInitBgOverlay();
		for(var i = 0; i < ppsPopups.length; i++) {
			jQuery('body').append( ppsPopups[ i ].rendered_html );
			ppsBindPopupShow( ppsPopups[ i ] );
			ppsBindPopupClose( ppsPopups[ i ] );
			ppsBindPopupActions( ppsPopups[ i ] );
			ppsBindPopupSubscribers( ppsPopups[ i ] );
		}
		jQuery(window).resize(function(){
			for(var i = 0; i < ppsPopups.length; i++) {
				if(ppsPopups[ i ].is_visible) {
					_ppsPositionPopup({popup: ppsPopups[ i ]});
				}
			}
		});
	}
});
function ppsBindPopupShow( popup ) {
	switch(popup.params.main.show_on) {
		case 'page_load':
			var delay = 0;
			if(popup.params.main.show_on_page_load_enb_delay && parseInt(popup.params.main.show_on_page_load_enb_delay)) {
				popup.params.main.show_on_page_load_delay = parseInt( popup.params.main.show_on_page_load_delay );
				if(popup.params.main.show_on_page_load_delay) {
					delay = popup.params.main.show_on_page_load_delay * 1000;
				}
			}
			setTimeout(function(){
				ppsCheckShowPopup( popup );
			}, delay);
			break;
		case 'click_on_page':
			jQuery(document).click(function(){
				if(!popup.click_on_page_displayed) {
					ppsCheckShowPopup( popup );
					popup.click_on_page_displayed = true;
				}
			});
			break;
	}
}
function ppsBindPopupClose( popup ) {
	// For now - only one method - click on close btn
	var shell = ppsGetPopupShell( popup );
	shell.find('.ppsPopupClose').click(function(){
		ppsClosePopup( popup );
		return false;
	});
}
function ppsBindPopupSubscribers(popup) {
	if(popup.params.tpl.enb_subscribe) {
		var shell = ppsGetPopupShell( popup );
		switch(popup.params.tpl.sub_dest) {
			case 'wordpress': case 'mailchimp':
				shell.find('.ppsSubscribeForm').submit(function(){
					var submitBtn = jQuery(this).find('input[type=submit]')
					,	self = this
					,	msgEl = jQuery(this).find('.ppsSubMsg');
					submitBtn.attr('disabled', 'disabled');
					jQuery(this).sendFormPps({
						msgElID: msgEl
					,	onSuccess: function(res){
							jQuery(self).find('input[type=submit]').removeAttr('disabled');
							if(!res.error) {
								var parentShell = jQuery(self).parents('.ppsSubscribeShell');
								msgEl.appendTo( parentShell );
								jQuery(self).animateRemovePps( 300 );
								ppsPopupSubscribeSuccess( popup );
							}
						}
					});
					return false;
				});
				break;
		}
	}
}
/**
 * Will check - was popup shown before and it's setting, and deside - should it be shown now or not
 * @param {mixed} popup Popup object or it's ID
 */
function ppsCheckShowPopup( popup ) {
	if(jQuery.isNumeric( popup ))
		popup = ppsGetPopupById( popup );
	var showKey = 'pps_show_'+ popup.id
	,	prevShow = getCookiePps( showKey );
	if(popup.params.main.show_to == 'first_time_visit' && prevShow)
		return;
	if(!prevShow)
		setCookiePps('pps_show_'+ popup.id, (new Date()).toString());
	var actionDone = _ppsPopupGetActionDone( popup );
	if(popup.params.main.show_to == 'until_make_action' && actionDone)
		return;
	_ppsPopupAddStat( popup, 'show' );
	ppsShowPopup( popup );
}
/**
 * Check - was action done in this popup or not (any action will be checked)
 * @param {mixed} popup Popup object or it's ID
 */
function _ppsPopupGetActionDone( popup ) {
	if(jQuery.isNumeric( popup ))
		popup = ppsGetPopupById( popup );
	var actionsKey = 'pps_actions_'+ popup.id
	,	actions = getCookiePps( actionsKey );
	if(actions) {
		// TODO: make priority check here - if subscribe enabled and user just shared popup - return false
		return true;
	}
	return false;
}
/**
 * Set done action in popup
 * @param {mixed} popup Popup object or it's ID
 * @param {type} action Action that was done
 */
function _ppsPopupSetActionDone( popup, action ) {
	if(jQuery.isNumeric( popup ))
		popup = ppsGetPopupById( popup );
	var actionsKey = 'pps_actions_'+ popup.id
	,	actions = getCookiePps( actionsKey );
	if(!actions)
		actions = {};
	actions[ action ] = (new Date()).toString();
	setCookiePps(actionsKey, actions)
	_ppsPopupAddStat( popup, action );
}
function _ppsPopupAddStat( popup, action ) {
	jQuery.sendFormPps({
		msgElID: 'noMessages'
	,	data: {mod: 'statistics', action: 'add', id: popup.id, type: action, 'connect_hash': popup.connect_hash}
	});
}

/**
 * Show popup
 * @param {mixed} popup Popup object or it's ID
 */
function ppsShowPopup( popup ) {
	if(jQuery.isNumeric( popup ))
		popup = ppsGetPopupById( popup );
	ppsShowBgOverlay( popup );
	var shell = ppsGetPopupShell( popup );
	_ppsPositionPopup({shell: shell});
	if(popup.params.tpl.anim) {
		shell.animationDuration( popup.params.tpl.anim_duration );
		shell.addClass('magictime '+ popup.params.tpl.anim.show_class).show();
	} else {
		shell.show();
	}
	popup.is_visible = true;
}
function _ppsPositionPopup( params ) {
	params = params || {};
	var shell = params.popup ? ppsGetPopupShell( params.popup ) : params.shell;
	if(shell) {
		var wndWidth = params.wndWidth ? params.wndWidth : jQuery(window).width()
		,	wndHeight = params.wndHeight ? params.wndHeight : jQuery(window).height()
		,	shellWidth = shell.width()
		,	shellHeight = shell.height()
		,	cmpareWidth = wndWidth - 0.1 * wndWidth	// less then 10%
		,	compareHeight = wndHeight - 0.1 * wndHeight;	// less then 10%
		
		if(shellHeight >= compareHeight) {
			var initialHeight = parseInt(shell.data('init-height'));
			if(!initialHeight) {
				initialHeight = shellHeight;
				shell.data('init-height', initialHeight);
			}
			var division = compareHeight / initialHeight;
			shell.zoom( division );
			shellWidth = shell.width();
			shellHeight = shell.height();
		}
		if(shellWidth >= cmpareWidth) {
			var initialWidth = parseInt(shell.data('init-width'));
			if(!initialWidth) {
				initialWidth = shellWidth;
				shell.data('init-width', initialWidth);
			}
			var division = cmpareWidth / initialWidth;
			shell.zoom( division );
			shellWidth = shell.width();
			shellHeight = shell.height();
		}

		shell.css({
			'left': (wndWidth - shellWidth) / 2
		,	'top': (wndHeight - shellHeight) / 2
		});
	} else {
		console.log('CAN NOT FIND POPUP SHELL TO RESIZE!');
	}
}
function ppsClosePopup(popup) {
	if(jQuery.isNumeric( popup ))
		popup = ppsGetPopupById( popup );
	var shell = ppsGetPopupShell( popup );
	if(popup.params.tpl.anim) {
		shell.removeClass(popup.params.tpl.anim.show_class).addClass(popup.params.tpl.anim.hide_class);
		setTimeout(function(){
			shell.hide();
			ppsHideBgOverlay();
		}, popup.params.tpl.anim_duration * 1000);
	} else {
		shell.hide();
		ppsHideBgOverlay();
	}
	popup.is_visible = false;
}
function ppsGetPopupShell(popup) {
	if(jQuery.isNumeric( popup ))
		popup = ppsGetPopupById( popup );
	return jQuery('#ppsPopupShell_'+ popup.view_id);;
}
function ppsGetPopupById( id ) {
	for(var i = 0; i < ppsPopups.length; i++) {
		if(ppsPopups[ i ].id == id)
			return ppsPopups[ i ];
	}
	return false;
}
function ppsInitBgOverlay() {
	jQuery('body').append('<div id="ppsPopupBgOverlay" />');
}
function ppsShowBgOverlay(popup) {
	if(popup && jQuery.isNumeric( popup ))
		popup = ppsGetPopupById( popup );
	if(popup && typeof(popup.params.tpl.bg_overlay_opacity) !== 'undefined') {
		if(!popup.params.tpl.bg_overlay_opacity || popup.params.tpl.bg_overlay_opacity == '')
			popup.params.tpl.bg_overlay_opacity = 0;
		var opacity = parseFloat( popup.params.tpl.bg_overlay_opacity );
		if(!isNaN(opacity)) {
			jQuery('#ppsPopupBgOverlay').css({
				'opacity': opacity
			});
		}
	}
	jQuery('#ppsPopupBgOverlay').show();
}
function ppsHideBgOverlay() {
	jQuery('#ppsPopupBgOverlay').hide();
}
function ppsBindPopupActions(popup) {
	var shell = ppsGetPopupShell( popup );
	// TODO: make usage of ppsPopupSubscribeSuccess() function only after success subscribe process, not after subscribe action
	if(shell.find('.ppsSubscribeForm_aweber').size()) {
		shell.find('.ppsSubscribeForm_aweber').submit(function(){
			if(jQuery(this).find('input[name=email]').val()) {
				ppsPopupSubscribeSuccess( popup );
			}
		});
	}
	if(shell.find('.ppsSmLink').size()) {
		shell.find('.ppsSmLink').click(function(){
			_ppsPopupSetActionDone(popup, 'share');
		});
	}
	if(shell.find('.fb-like-box').size()) {
		_ppsBindFbLikeBtnAction(popup);
	}
}
function _ppsBindFbLikeBtnAction(popup) {
	if(typeof(FB) === 'undefined') {
		// recurse until FB core will not be loaded
		setTimeout(function(){
			_ppsBindFbLikeBtnAction(popup);
		}, 500);
		return;
	}
	FB.Event.subscribe('edge.create', function(response) {
		_ppsPopupSetActionDone(popup, 'fb_like');
	});
	FB.Event.subscribe('xfbml.render', function(response) {
		_ppsPositionPopup({popup: popup});
	});
}
function ppsPopupSubscribeSuccess(popup) {
	if(popup && jQuery.isNumeric( popup ))
		popup = ppsGetPopupById( popup );
	_ppsPopupSetActionDone(popup, 'subscribe');
}
