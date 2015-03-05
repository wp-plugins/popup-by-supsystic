<?php if(in_array($this->popup['type'], array(PPS_COMMON, PPS_VIDEO))) {?>
	<div class="ppsPopupOptRow">
		<?php echo htmlPps::checkbox('params[tpl][enb_label]', array('checked' => htmlPps::checkedOpt($this->popup['params']['tpl'], 'enb_label')))?>
		<?php echo htmlPps::text('params[tpl][label]', array(
			'value' => esc_html($this->popup['params']['tpl']['label']),
			'attrs' => 'class="ppsOptTxtCheck"',
		))?>
	</div>
<?php }?>
<?php for($i = 0; $i < $this->popup['params']['opts_attrs']['txt_block_number']; $i++) { ?>
	<fieldset>
		<legend>
			<label>
				<?php $switchBlock = 'txtBlock_'. $i;?>
				<?php echo htmlPps::checkbox('params[tpl][enb_txt_'. $i. ']', array(
					'checked' => htmlPps::checkedOpt($this->popup['params']['tpl'], 'enb_txt_'. $i),
					'attrs' => 'data-switch-block="'. $switchBlock. '"',
				))?>
				<?php $this->popup['params']['opts_attrs']['txt_block_number'] == 1 ? _e('Text block', PPS_LANG_CODE) : printf(__('Text block %d', PPS_LANG_CODE), $i + 1)?>
			</label>
		</legend>
		<span data-block-to-switch="<?php echo $switchBlock?>">
			<?php wp_editor($this->popup['params']['tpl']['txt_'. $i], 'params_tpl_txt_'. $i, array(
				'drag_drop_upload' => true,
			))?>
		</span>
	</fieldset>
<?php }?>
<fieldset>
	<legend>
		<label>
			<?php echo htmlPps::checkbox('params[tpl][enb_foot_note]', array(
				'checked' => htmlPps::checkedOpt($this->popup['params']['tpl'], 'enb_foot_note'),
				'attrs' => 'data-switch-block="txtFooter"',
			))?>
			<?php _e('Foot note', PPS_LANG_CODE)?>
		</label>
	</legend>
	<span data-block-to-switch="txtFooter">
		<?php echo htmlPps::textarea('params[tpl][foot_note]', array(
			'value' => $this->popup['params']['tpl']['foot_note'],
		))?>
	</span>
</fieldset>