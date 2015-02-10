var ppsPopupSaveTimeout = null
,	ppsPopupIsSaving = false
,	ppsTinyMceEditorUpdateBinded = false;
jQuery(document).ready(function(){
	jQuery('#ppsPopupEditTabs').wpTabs({
		change: function(selector) {
			if(selector == '#ppsPopupEditors') {
				jQuery(selector).find('textarea').each(function(i, el){
					if(typeof(this.CodeMirrorEditor) !== 'undefined') {
						this.CodeMirrorEditor.refresh();
					}
				});
			}
		}
	});
	jQuery('.ppsPopupSaveBtn').click(function(){
		jQuery('#ppsPopupEditForm').submit();
		return false;
	});
	jQuery('#ppsPopupEditForm').submit(function(){
		// Don't save if form isalready submitted
		if(ppsPopupIsSaving) {
			ppsMakeAutoUpdate();
			return false;
		}
		ppsShowPreviewUpdating();
		ppsPopupIsSaving = true;
		var addData = {};
		if(ppsPopup.params.opts_attrs.txt_block_number) {
			for(var i = 0; i < ppsPopup.params.opts_attrs.txt_block_number; i++) {
				var textId = 'params_tpl_txt_'+ i
				,	sendValKey = 'params_tpl_txt_val_'+ i;
				addData[ sendValKey ] = ppsGetTxtEditorVal( textId );
			}
		}
		if(jQuery('#ppsPopupCssEditor').get(0).CodeMirrorEditor)
			jQuery('#ppsPopupCssEditor').val( jQuery('#ppsPopupCssEditor').get(0).CodeMirrorEditor.getValue());
		if(jQuery('#ppsPopupHtmlEditor').get(0).CodeMirrorEditor)
			jQuery('#ppsPopupHtmlEditor').val( jQuery('#ppsPopupHtmlEditor').get(0).CodeMirrorEditor.getValue());
		jQuery(this).sendFormPps({
			btn: jQuery('.ppsPopupSaveBtn')
		,	appendData: addData
		,	onSuccess: function(res) {
				ppsPopupIsSaving = false;
				if(!res.error) {
					ppsRefreshPreview();
				}
			}
		});
		return false;
	});
	
	jQuery('.ppsBgTypeSelect').change(function(){
		var iter = jQuery(this).data('iter');
		jQuery('.ppsBgTypeShell_'+ iter).hide();
		switch(jQuery(this).val()) {
			case 'img':
				jQuery('.ppsBgTypeImgShell_'+ iter).show();
				break;
			case 'color':
				jQuery('.ppsBgTypeColorShell_'+ iter).show();
				break;
		}
	}).change();
	var cssEditor = CodeMirror.fromTextArea(jQuery('#ppsPopupCssEditor').get(0), {
		mode: 'css'
	,	lineWrapping: true
	,	lineNumbers: true
	,	matchBrackets: true
    ,	autoCloseBrackets: true
	});
	jQuery('#ppsPopupCssEditor').get(0).CodeMirrorEditor = cssEditor;
	cssEditor.on('change', function(){
		ppsMakeAutoUpdate( 3000 );
	});
	var htmlEditor = CodeMirror.fromTextArea(jQuery('#ppsPopupHtmlEditor').get(0), {
		mode: 'text/html'
	,	lineWrapping: true
	,	lineNumbers: true
	,	matchBrackets: true
    ,	autoCloseBrackets: true
	});
	jQuery('#ppsPopupHtmlEditor').get(0).CodeMirrorEditor = htmlEditor;
	htmlEditor.on('change', function(){
		ppsMakeAutoUpdate( 3000 );
	});
	setTimeout(function(){
		ppsBindTinyMceUpdate();
		if(!ppsTinyMceEditorUpdateBinded) {
			jQuery('.wp-switch-editor.switch-tmce').click(function(){
				setTimeout(ppsBindTinyMceUpdate, 500);
			});
		}
	}, 500);
	// Close btn selection
	jQuery('#ppsPopupCloseBtnList li').click(function(){
		jQuery('#ppsPopupCloseBtnList li').removeClass('active');
		jQuery(this).addClass('active');
		jQuery('#ppsPopupEditForm').find('[name="params[tpl][close_btn]"]').val( jQuery(this).data('key') ).trigger('change');
	});
	if(ppsPopup.params.tpl && ppsPopup.params.tpl.close_btn) {
		jQuery('#ppsPopupCloseBtnList li[data-key="'+ ppsPopup.params.tpl.close_btn+ '"]').addClass('active');
		jQuery('#ppsPopupEditForm').find('[name="params[tpl][close_btn]"]').val( ppsPopup.params.tpl.close_btn );
	}
	// Bullets selection
	jQuery('#ppsPopupBulletsList li').click(function(){
		jQuery('#ppsPopupBulletsList li').removeClass('active');
		jQuery(this).addClass('active');
		jQuery('#ppsPopupEditForm').find('[name="params[tpl][bullets]"]').val( jQuery(this).data('key') ).trigger('change');
	});
	if(ppsPopup.params.tpl && ppsPopup.params.tpl.bullets) {
		jQuery('#ppsPopupBulletsList li[data-key="'+ ppsPopup.params.tpl.bullets+ '"]').addClass('active');
		jQuery('#ppsPopupEditForm').find('[name="params[tpl][bullets]"]').val( ppsPopup.params.tpl.bullets );
	}
	// Show/hide on pages selection
	jQuery('#ppsPopupEditForm').find('[name="params[main][show_pages]"]').change(function(){
		if(toeInArrayPps(jQuery(this).val(), ['show_on_pages', 'not_show_on_pages'])) {
			var checked = jQuery(this).attr('checked')
			,	boxElement = jQuery(this).val() == 'show_on_pages' ? jQuery('#ppsPopupShowOnPages') : jQuery('#ppsPopupNotShowOnPages')
			,	onFinishAnimate = function() {
					boxElement.find('.chosen').chosen();
				};
			checked ? boxElement.slideDown( 300, onFinishAnimate ) : boxElement.slideUp( 300 );
			if(checked) {
				boxElement.find('.chosen').chosen();
			}
		}
	}).change();
	
	jQuery('#ppsPopupEditForm').find('[name="params[main][show_on]"]').change(function(){
		// Show/hide show delay options
		if(jQuery(this).val() == 'page_load') {
			jQuery(this).attr('checked') ? jQuery('#ppsPopupShowOnDelay').slideDown( 300 ) : jQuery('#ppsPopupShowOnDelay').slideUp( 300 );
		}
		// Show/hide click-on-element show options
		if(jQuery(this).val() == 'click_on_element') {
			jQuery(this).attr('checked') ? jQuery('#ppsPopupShowOnElClick').slideDown( 300 ) : jQuery('#ppsPopupShowOnElClick').slideUp( 300 );
		}
	}).change();
	// Animation effect change
	jQuery('.ppsPopupAnimEffLabel').each(function(){
		var key = jQuery(this).data('key');
		if(key != 'none') {
			jQuery(this).addClass('magictime');
			jQuery(this).mouseover(function(){
				if(!jQuery(this).data('anim-started')) {
					jQuery(this).data('anim-started', 1);
					ppsHideEndlessAnim(jQuery(this), jQuery(this).data('show-class'), jQuery(this).data('hide-class'));
				}
			});
			jQuery(this).mouseout(function(){
				jQuery(this).data('anim-started', 0);
			});
		}
	});
	jQuery('.ppsPopupAnimEff').click(function(){
		jQuery('.ppsPopupAnimEff').removeClass('active');
		jQuery(this).addClass('active');
		var key = jQuery(this).find('.ppsPopupAnimEffLabel:first').data('key');
		jQuery('#ppsPopupEditForm').find('[name="params[tpl][anim_key]"]').val( key ).trigger('change');
		return false;
	});
	var activeAnimKey = ppsPopup.params.tpl && ppsPopup.params.tpl.anim_key ? ppsPopup.params.tpl.anim_key : 'none';
	if(activeAnimKey) {
		jQuery('.ppsPopupAnimEffLabel[data-key="'+ activeAnimKey+ '"]').parents('.ppsPopupAnimEff:first').addClass('active');
		jQuery('#ppsPopupEditForm').find('[name="params[tpl][anim_key]"]').val( activeAnimKey );
	}
	jQuery('.ppsPopupPreviewBtn').click(function(){
		jQuery('html, body').animate({
			scrollTop: jQuery("#ppsPopupPreview").offset().top
		}, 1000);
		return false;
	});
	// Delete btn init
	jQuery('.ppsPopupRemoveBtn').click(function(){
		if(confirm(toeLangPps('Are you sure want to remove this Pop-Up?'))) {
			jQuery.sendFormPps({
				btn: this
			,	data: {mod: 'popup', action: 'remove', id: ppsPopup.id}
			,	onSuccess: function(res) {
					if(!res.error) {
						toeRedirect( ppsAddNewUrl );
					}
				}
			});
		}
		return false;
	});
	// Change tpl btn init
	jQuery('.ppsPopupSelectTpl').click(function(){
		toeRedirect( ppsAddNewUrl+ '&change_for='+ ppsPopup.id );
		return false;
	});
	// Auto update bind
	var autoUpdateBoxes = ['#ppsPopupTpl', '#ppsPopupTexts', '#ppsPopupSubscribe', '#ppsPopupSm'];
	for(var i = 0; i < autoUpdateBoxes.length; i++) {
		jQuery( autoUpdateBoxes[i] ).find('input[type=checkbox],input[type=radio],input[type=hidden],select').change(function(){
			ppsSavePopupChanges();
		});
		jQuery( autoUpdateBoxes[i] ).find('input[type=text],textarea').keyup(function(){
			ppsMakeAutoUpdate();
		});
	}
});
function ppsShowImgPrev(url, attach, buttonId) {
	var iter = jQuery('#'+ buttonId).data('iter');
	jQuery('.ppsBgImgPrev_'+ iter).attr('src', url);
}
function ppsSavePopupChanges() {
	// Triger save
	jQuery('.ppsPopupSaveBtn').click();
}
function ppsRefreshPreview() {
	document.getElementById('ppsPopupPreviewFrame').contentWindow.location.reload();
}
function ppsMakeAutoUpdate(delay) {
	delay = delay ? delay : 1500;
	if(ppsPopupSaveTimeout)
		clearTimeout( ppsPopupSaveTimeout );
	ppsPopupSaveTimeout = setTimeout(ppsSavePopupChanges, delay);
}
function ppsBindTinyMceUpdate() {
	if(!ppsTinyMceEditorUpdateBinded && typeof(tinyMCE) !== 'undefined' && tinyMCE.editors && tinyMCE.editors.length) {
		for (var edId in tinyMCE.editors) {
			tinyMCE.editors[edId].onKeyUp.add(function(){
				ppsMakeAutoUpdate();
			});
		}
		ppsTinyMceEditorUpdateBinded = true;
	}
}
function ppsShowPreviewUpdating() {
	this._posSet;
	if(!this._posSet) {
		this._posSet = true;
		jQuery('#ppsPopupPreviewUpdatingMsg').css({
			'left': 'calc(50% - '+ (jQuery('#ppsPopupPreviewUpdatingMsg').width() / 2)+ 'px)'
		});
	}
	jQuery('#ppsPopupPreviewFrame').css({
		'opacity': 0.5
	});
	jQuery('#ppsPopupPreviewUpdatingMsg').slideDown( 300 );
}
function ppsHidePreviewUpdating() {
	jQuery('#ppsPopupPreviewFrame').show().css({
		'opacity': 1
	});
	jQuery('#ppsPopupPreviewUpdatingMsg').slideUp( 100 );
}
function ppsShowEndlessAnim(element, showClass, hideClass) {
	if(!jQuery(element).data('anim-started')) {
		element.removeClass(showClass).removeClass(hideClass);
		return;
	}
	var animationDuration = parseFloat(jQuery('#ppsPopupEditForm').find('[name="params[tpl][anim_duration]"]').val());
	if(animationDuration) {
		jQuery(element).animationDuration( animationDuration );
		animationDuration *= 1000;
	} else {
		jQuery(element).animationDuration( 1 );
		animationDuration = 1000;
	}
	element.removeClass(hideClass).addClass(showClass);
	setTimeout(function(){
		ppsHideEndlessAnim( element, showClass, hideClass );
	}, animationDuration);
}
function ppsHideEndlessAnim(element, showClass, hideClass) {
	if(!jQuery(element).data('anim-started')) {
		element.removeClass(showClass).removeClass(hideClass);
		return;
	}
	var animationDuration = parseFloat(jQuery('#ppsPopupEditForm').find('[name="params[tpl][anim_duration]"]').val());
	if(animationDuration) {
		jQuery(element).animationDuration( animationDuration );
		animationDuration *= 1000;
	} else {
		jQuery(element).animationDuration( 1 );
		animationDuration = 1000;
	}
	element.removeClass(showClass).addClass(hideClass);
	setTimeout(function(){
		ppsShowEndlessAnim( element, showClass, hideClass );
	}, animationDuration);
}