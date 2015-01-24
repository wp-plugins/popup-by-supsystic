<?php for($i = 0; $i < $this->popup['params']['opts_attrs']['txt_block_number']; $i++) { ?>
	<fieldset>
		<legend>
			<label>
				<?php echo htmlPps::checkbox('params[tpl][enb_txt_'. $i. ']', array('checked' => htmlPps::checkedOpt($this->popup['params']['tpl'], 'enb_txt_'. $i)))?>
				<?php $this->popup['params']['opts_attrs']['txt_block_number'] == 1 ? _e('Text block', PPS_LANG_CODE) : printf(__('Text block %d', PPS_LANG_CODE), $i + 1)?>
			</label>
		</legend>
		<?php wp_editor($this->popup['params']['tpl']['txt_'. $i], 'params_tpl_txt_'. $i, array(
			'drag_drop_upload' => true,
		))?>
	</fieldset>
<?php }?>
<fieldset>
	<legend>
		<label>
			<?php echo htmlPps::checkbox('params[tpl][enb_foot_note]', array('checked' => htmlPps::checkedOpt($this->popup['params']['tpl'], 'enb_foot_note')))?>
			<?php _e('Foot note', PPS_LANG_CODE)?>
		</label>
	</legend>
	<?php echo htmlPps::textarea('params[tpl][foot_note]', array(
		'value' => $this->popup['params']['tpl']['foot_note'],
	))?>
</fieldset>