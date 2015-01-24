<div class="ppsPopupOptRow">
	<label>
		<?php echo htmlPps::checkbox('params[tpl][enb_subscribe]', array('checked' => htmlPps::checkedOpt($this->popup['params']['tpl'], 'enb_subscribe')))?>
		<?php  _e('Enable Subscription', PPS_LANG_CODE)?>
	</label>
</div>
<div class="ppsPopupOptRow">
	<label>
		<?php _e('Subscribe to', PPS_LANG_CODE)?>
		<?php echo htmlPps::selectbox('params[tpl][sub_dest]', array(
			'options' => $this->subDestListForSelect, 
			'value' => (isset($this->popup['params']['tpl']['sub_dest']) ? $this->popup['params']['tpl']['sub_dest'] : '')))?>
	</label>
</div>
<div id="ppsPopupSubDestOpts_aweber" class="ppsPopupOptRow ppsPopupSubDestOpts" style="display: none;">
	<label>
		<?php _e('Aweber Listname', PPS_LANG_CODE)?>
		<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(sprintf(__('Check <a href="%" target="_blank">this page</a> for more details', PPS_LANG_CODE), 'https://help.aweber.com/hc/en-us/articles/204028426-What-Is-The-Unique-List-ID-'))?>"></i>
		<?php echo htmlPps::text('params[tpl][sub_aweber_listname]', array(
			'value' => (isset($this->popup['params']['tpl']['sub_aweber_listname']) ? $this->popup['params']['tpl']['sub_aweber_listname'] : '')))?>
	</label>
</div>
<div class="ppsPopupOptRow">
	<fieldset class="ppoPopupSubFields" style="padding: 10px;">
		<legend><?php _e('Subscription fields', PPS_LANG_CODE)?></legend>
		<label class="supsystic-tooltip" title="Email field is mandatory for most of subscribe engines - so it should be always enabled">
			<?php echo htmlPps::checkbox('enabled_email_subscribe', array('checked' => 1, 'attrs' => 'disabled'))?>
			<?php _e('Email', PPS_LANG_CODE)?>
		</label>
		<label>
			<?php echo htmlPps::checkbox('params[tpl][enb_sub_name]', array('checked' => htmlPps::checkedOpt($this->popup['params']['tpl'], 'enb_sub_name')))?>
			<?php _e('Name', PPS_LANG_CODE)?>
		</label>
	</fieldset>
</div>
<div class="ppsPopupOptRow">
	<label>
		<?php _e('Submit button name', PPS_LANG_CODE)?>
		<?php echo htmlPps::text('params[tpl][sub_btn_label]', array('value' => $this->popup['params']['tpl']['sub_btn_label']))?>
	</label>
</div>