<section>
	<div class="supsystic-item supsystic-panel">
		<h3 style="line-height: 30px;">
			<?php if($this->changeFor) {
				printf(__('Change Template to any other from list bellow or <a class="button" href="%s">return to Pop-Up edit</a>', PPS_LANG_CODE), $this->editLink);
			} else {
				_e('Choose Pop-Up Template. You can change it later.', PPS_LANG_CODE);
			}?>
		</h3>
		<hr />
		<div id="containerWrapper" class="popup-list">
			<?php foreach($this->list as $popup) { ?>
				<div class="popup-list-item" data-id="<?php echo $popup['id']?>">
					<a href="#" class="ppsCreatePopupFromTplBtn">
						<img src="<?php echo $popup['img_preview_url']?>" />
					</a>
					<div class="ppsNewPopupDesc">
						<span class="ppsTplLabel"><?php echo $popup['label']?></span><br />
						<?php echo $this->types[ $popup['type_id'] ]['label']?>&nbsp;<?php _e('type')?>
					</div>
				</div>
			<?php }?>
			<div style="clear: both;"></div>
		</div>
	</div>
</section>
<!--Create popup wnd-->
<div id="ppsCreatePopupWnd" title="<?php _e('Create new popup', PPS_LANG_CODE)?>" style="display: none;">
	<form id="ppsCreatePopupForm">
		<label>
			<?php _e('Enter popup label')?>:
			<?php echo htmlPps::text('label')?>
		</label>
		<?php echo htmlPps::hidden('original_id')?>
		<?php echo htmlPps::hidden('mod', array('value' => 'popup'))?>
		<?php echo htmlPps::hidden('action', array('value' => 'createFromTpl'))?>
	</form>
	<div id="ppsCreatePopupMsg"></div>
</div>
<!---->
<!--Change tpl wnd-->
<div id="ppsChangeTplWnd" title="<?php _e('Change Template', PPS_LANG_CODE)?>" style="display: none;">
	<form id="ppsChangeTplForm">
		<?php _e('Are you sure want to change your current template - to ')?><span id="ppsChangeTplNewLabel"></span> ?
		<?php echo htmlPps::hidden('id')?>
		<?php echo htmlPps::hidden('new_tpl_id')?>
		<?php echo htmlPps::hidden('mod', array('value' => 'popup'))?>
		<?php echo htmlPps::hidden('action', array('value' => 'changeTpl'))?>
	</form>
	<div id="ppsChangeTplMsg"></div>
</div>
<!---->