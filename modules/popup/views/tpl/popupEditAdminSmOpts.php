<div class="ppsPopupOptRow">
	<label>
		<?php echo htmlPps::checkbox('params[tpl][enb_sm]', array(
			'checked' => htmlPps::checkedOpt($this->popup['params']['tpl'], 'enb_sm'),
			'attrs' => 'data-switch-block="smShell"',
		))?>
		<?php  _e('Enable Social Buttons', PPS_LANG_CODE)?>
	</label>
</div>
<span data-block-to-switch="smShell">
	<div class="ppsPopupOptRow">
	<?php foreach($this->smLinks as $smKey => $smData) { ?>
		<label>
			<?php echo htmlPps::checkbox('params[tpl][enb_sm_'. $smKey. ']', array('checked' => htmlPps::checkedOpt($this->popup['params']['tpl'], 'enb_sm_'. $smKey)));?>
			<?php echo $smData['label']?>
		</label>
	<?php }?>
	</div>
	<div class="ppsPopupOptRow">
		<fieldset class="ppoPopupSubFields" style="padding: 10px;">
			<legend><?php _e('Social links design', PPS_LANG_CODE)?></legend>
			<?php foreach($this->smDesigns as $smKey => $smData) { ?>
				<label>
					<?php echo htmlPps::radiobutton('params[tpl][sm_design]', array('value' => $smKey, 'checked' => htmlPps::checkedOpt($this->popup['params']['tpl'], 'sm_design', $smKey)));?>
					<?php echo $smData['label']?>
				</label>
			<?php }?>
		</fieldset>
	</div>
</span>