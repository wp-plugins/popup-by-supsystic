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
		<div id="containerWrapper" style="width: 90%; margin: 40px auto;">
			<?php if(!$this->changeFor) { ?>
				<form id="ppsCreatePopupForm">
					<label>
						<h3 style="float: left; margin: 10px;"><?php _e('PopUp Name', PPS_LANG_CODE)?>:</h3>
						<?php echo htmlPps::text('label', array('attrs' => 'style="float: left; width: 60%;"', 'required' => true))?>
					</label>
					<button class="button button-primary" style="margin-top: 1px;">
						<i class="fa fa-check"></i>
						<?php _e('Save', PPS_LANG_CODE)?>
					</button>
					<?php echo htmlPps::hidden('original_id')?>
					<?php echo htmlPps::hidden('mod', array('value' => 'popup'))?>
					<?php echo htmlPps::hidden('action', array('value' => 'createFromTpl'))?>
				</form>
				<div style="clear: both;"></div>
				<div id="ppsCreatePopupMsg"></div>
			<?php }?>
			<div  class="popup-list">
				<?php foreach($this->list as $popup) { ?>
					<div class="popup-list-item" data-id="<?php echo $popup['id']?>">
						<a href="#" class="ppsCreatePopupFromTplBtn">
							<img src="<?php echo $popup['img_preview_url']?>" class="ppsTplPrevImg" />
						</a>
						<div class="ppsNewPopupDesc">
							<button class="ppsSelectTpl button button-primary"><?php _e('Select', PPS_LANG_CODE)?></button>
							<span class="ppsTplLabel"><?php echo $popup['label']?></span><br />
							<?php echo $this->types[ $popup['type_id'] ]['label']?>&nbsp;<?php _e('type')?>
						</div>
					</div>
				<?php }?>
				<div style="clear: both;"></div>
			</div>
		</div>
	</div>
</section>
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