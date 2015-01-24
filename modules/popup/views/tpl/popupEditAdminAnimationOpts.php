<div class="ppsPopupOptRow">
	<label>
		<?php _e('Duration', PPS_LANG_CODE)?>
		<?php echo htmlPps::text('params[tpl][anim_duration]', array('value' => $this->popup['params']['tpl']['anim_duration']))?>
		<?php _e('seconds', PPS_LANG_CODE)?>
	</label>
</div>
<div class="ppsPopupOptRow">
	<div id="ppsPopupAnimOptsShell">
		<?php foreach($this->animationList as $aKey => $aData) { ?>
		<div class="ppsPopupAnimEff">
			<div class="ppsPopupAnimEffLabel" 
				data-key="<?php echo $aKey?>" 
				<?php if($aKey != 'none') {?>
				data-show-class="<?php echo $aData['show_class']?>"
				data-hide-class="<?php echo $aData['hide_class']?>"
				<?php }?>
			 >
				 <?php echo $aData['label']?>
			</div>
		</div>
		<?php }?>
		<div style="clear: both;"></div>
	</div>
	<?php echo htmlPps::hidden('params[tpl][anim_key]', array('value' => $this->popup['params']['tpl']['anim_key']))?>
</div>