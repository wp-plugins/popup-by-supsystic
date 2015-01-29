<div id="ppsPopupEditTabs">
	<section class="supsystic-bar supsystic-sticky sticky-save-width sticky-padd-next">
		<ul class="supsystic-bar-controls" style="float: left; width: 90px;">
			<li title="<?php _e('Save all changes', PPS_LANG_CODE)?>">
				<button class="button button-primary ppsPopupSaveBtn">
					<i class="fa fa-fw fa-save"></i>
					<?php _e('Save', PPS_LANG_CODE)?>
				</button>
			</li>
			<li>
				<button style="position: absolute; top: -32px; left: 380px; height: 30px; line-height: 26px;" class="button button-primary ppsPopupPreviewBtn">
					<i class="fa fa-fw fa-eye"></i>
					<?php _e('Preview', PPS_LANG_CODE)?>
				</button>
			</li>
			<li>
				<button style="position: absolute; top: -32px; left: 490px; height: 30px; line-height: 26px;" class="button button-primary ppsPopupRemoveBtn">
					<i class="fa fa-fw fa-trash-o"></i>
					<?php _e('Delete', PPS_LANG_CODE)?>
				</button>
			</li>
		</ul>
		<h3 class="nav-tab-wrapper" style="margin-bottom: 0px; margin-top: 12px;">
			<?php $i = 0;?>
			<?php foreach($this->tabs as $tKey => $tData) { ?>
				<a class="nav-tab <?php if($i == 0) { echo 'nav-tab-active'; }?>" href="#<?php echo $tKey?>">
					<?php echo $tData['title']?>
				</a>
			<?php $i++; }?>
		</h3>
	</section>
	<section>
		<div class="supsystic-item supsystic-panel" style="padding-left: 10px;">
			<div id="containerWrapper">
				<form id="ppsPopupEditForm">
					<?php foreach($this->tabs as $tKey => $tData) { ?>
						<div id="<?php echo $tKey?>" class="ppsTabContent">
							<?php echo $tData['content']?>
						</div>
					<?php }?>
					<?php if(isset($this->popup['params']['opts_attrs'])) {?>
						<?php foreach($this->popup['params']['opts_attrs'] as $optKey => $attr) {
							echo htmlPps::hidden('params[opts_attrs]['. $optKey. ']', array('value' => $attr));
						}?>
					<?php }?>
					<?php echo htmlPps::hidden('mod', array('value' => 'popup'))?>
					<?php echo htmlPps::hidden('action', array('value' => 'save'))?>
					<?php echo htmlPps::hidden('id', array('value' => $this->popup['id']))?>
				</form>
				<div style="clear: both;"></div>
				<div id="ppsPopupPreview" style="">
					<iframe id="ppsPopupPreviewFrame" width="" height="" frameborder="0" src="<?php echo $this->previewUrl?>" style="display: none;"></iframe>
					<script type="text/javascript">
					jQuery('#ppsPopupPreviewFrame').load(function(){
						ppsHidePreviewUpdating();
						var paddingSize = 40;
						jQuery(this).height( (jQuery(this).get(0).contentWindow.document.body.scrollHeight + paddingSize)+ 'px' );
						jQuery(this).width( (jQuery(this).get(0).contentWindow.document.body.scrollWidth + paddingSize)+ 'px' );
						<?php if(in_array($this->popup['type'], array(PPS_FB_LIKE))) {?>
							jQuery(this).height( '500px' );
						<?php }?>
						jQuery(jQuery(this).get(0).contentWindow.document).find('.ppsPopupShell').css({
							'position': 'absolute'
						,	'top': '15px'
						});
					});
					</script>
				</div>
			</div>
		</div>
	</section>
</div>
<div id="ppsPopupPreviewUpdatingMsg">
	<?php _e('Loading preview...', PPS_LANG_CODE)?>
</div>
<div id="ppsPopupGoToTop">
	<a id="ppsPopupGoToTopBtn" href="#">
		<img src="<?php echo uriPps::_(PPS_IMG_PATH)?>pointer-up.png" /><br />
		<?php _e('Back to top', PPS_LANG_CODE)?>
	</a>
</div>