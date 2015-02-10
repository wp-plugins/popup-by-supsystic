<section class="ppsPopupMainOptSect">
	<span class="ppsOptLabel"><?php _e('When to show PopUp', PPS_LANG_CODE)?></span>
	<hr />
	<label>
		<?php echo htmlPps::radiobutton('params[main][show_on]', array(
			'value' => 'page_load', 
			'checked' => htmlPps::checkedOpt($this->popup['params']['main'], 'show_on', 'page_load')))?>
		<?php _e('When page load', PPS_LANG_CODE)?>
	</label>
	<div id="ppsPopupShowOnDelay" style="display: none;">
		<label>
			<?php echo htmlPps::checkbox('params[main][show_on_page_load_enb_delay]', array('checked' => htmlPps::checkedOpt($this->popup['params']['main'], 'show_on_page_load_enb_delay')))?>
			<?php _e('Delay for', PPS_LANG_CODE)?>
		</label>
		<label>
			<?php echo htmlPps::text('params[main][show_on_page_load_delay]', array('value' => $this->popup['params']['main']['show_on_page_load_delay']));?>
			<?php _e('seconds', PPS_LANG_CODE)?>
		</label>
	</div><br />
	<label>
		<?php echo htmlPps::radiobutton('params[main][show_on]', array(
			'value' => 'click_on_page',
			'checked' => htmlPps::checkedOpt($this->popup['params']['main'], 'show_on', 'click_on_page')))?>
		<?php _e('User click on the page', PPS_LANG_CODE)?>
	</label><br />
	<label>
		<?php echo htmlPps::radiobutton('params[main][show_on]', array(
			'value' => 'click_on_element',
			'checked' => htmlPps::checkedOpt($this->popup['params']['main'], 'show_on', 'click_on_element')))?>
		<?php _e('Click on certain link / button / other element', PPS_LANG_CODE)?>
	</label>
	<div id="ppsPopupShowOnElClick" style="display: none;">
		<span>
			<?php _e('Copy & paste next code - into required link to open PopUp on Click', PPS_LANG_CODE)?>:<br />
			<span class="sup-shortcode supsystic-tooltip-right" title="<?php echo esc_html(sprintf(__('Check screenshot with details - <a target="_blank" href="%s">here</a>.', PPS_LANG_CODE), 'https://supsystic.com/download/show-on-element-click.png'))?>">
				[<?php echo PPS_SHORTCODE_CLICK?> id=<?php echo $this->popup['id']?>]
			</span>
		</span>
		<br />
		<?php _e('Or, if you know HTML basics, - you can insert "onclick" attribute to any of your element from code below')?>:<br />
		<span class="sup-shortcode">onclick="ppsShowPopup(<?php echo $this->popup['id']?>); return false;"</span>
	</div><br />
</section>
<section class="ppsPopupMainOptSect">
	<span class="ppsOptLabel"><?php _e('Whom to show', PPS_LANG_CODE)?></span>
	<hr />
	<label>
		<?php echo htmlPps::radiobutton('params[main][show_to]', array(
			'value' => 'everyone',
			'checked' => htmlPps::checkedOpt($this->popup['params']['main'], 'show_to', 'everyone')))?>
		<?php _e('Everyone', PPS_LANG_CODE)?>
	</label><br />
	<label>
		<?php echo htmlPps::radiobutton('params[main][show_to]', array(
			'value' => 'first_time_visit',
			'checked' => htmlPps::checkedOpt($this->popup['params']['main'], 'show_to', 'first_time_visit')))?>
		<?php _e('For first-time visitors', PPS_LANG_CODE)?>
	</label><br />
	<label class="supsystic-tooltip-bottom" title="<?php _e('Subscribe, share, like, etc.')?>" style="padding-bottom: 0px;">
		<?php echo htmlPps::radiobutton('params[main][show_to]', array(
			'value' => 'until_make_action',
			'checked' => htmlPps::checkedOpt($this->popup['params']['main'], 'show_to', 'until_make_action')))?>
		<?php _e('Until user will not make an action', PPS_LANG_CODE)?>
	</label><?php /*?><br />
	<label>
		<?php echo htmlPps::radiobutton('params[main][show_to]', array(
			'value' => 'for_countries',
			'checked' => htmlPps::checkedOpt($this->popup['params']['main'], 'show_to', 'for_countries')))?>
		<?php _e('Specify country', PPS_LANG_CODE)?>
	</label><?php */?>
</section>
<section class="ppsPopupMainOptSect">
	<span class="ppsOptLabel"><?php _e('Show on next pages', PPS_LANG_CODE)?></span>
	<hr />
	<label>
		<?php echo htmlPps::radiobutton('params[main][show_pages]', array(
			'value' => 'all',
			'checked' => htmlPps::checkedOpt($this->popup['params']['main'], 'show_pages', 'all')))?>
		<?php _e('All pages', PPS_LANG_CODE)?>
	</label><br />
	<label>
		<?php echo htmlPps::radiobutton('params[main][show_pages]', array(
			'value' => 'show_on_pages',
			'checked' => htmlPps::checkedOpt($this->popup['params']['main'], 'show_pages', 'show_on_pages')))?>
		<?php _e('Show on next pages', PPS_LANG_CODE)?>
	</label>
	<div id="ppsPopupShowOnPages" style="display: none;">
		<?php echo htmlPps::selectlist('show_pages_list', array('options' => $this->allPagesForSelect, 'value' => $this->selectedShowPages, 'attrs' => 'class="chosen" data-placeholder="'. __('Choose Pages', PPS_LANG_CODE). '"'))?>
	</div><br />
	<label>
		<?php echo htmlPps::radiobutton('params[main][show_pages]', array(
			'value' => 'not_show_on_pages',
			'checked' => htmlPps::checkedOpt($this->popup['params']['main'], 'show_pages', 'not_show_on_pages')))?>
		<?php _e('Don\'t show on next pages', PPS_LANG_CODE)?>
	</label>
	<div id="ppsPopupNotShowOnPages" style="display: none;">
		<?php echo htmlPps::selectlist('not_show_pages_list', array('options' => $this->allPagesForSelect, 'value' => $this->selectedHidePages, 'attrs' => 'class="chosen" data-placeholder="'. __('Choose Pages', PPS_LANG_CODE). '"'))?>
	</div>
</section>