<section class="ppsPopupMainOptSect">
	<span class="ppsOptLabel"><?php _e('When to show PopUp', PPS_LANG_CODE)?></span>
	<hr />
	<label class="ppsPopupMainOptLbl">
		<?php echo htmlPps::radiobutton('params[main][show_on]', array(
			'value' => 'page_load', 
			'checked' => htmlPps::checkedOpt($this->popup['params']['main'], 'show_on', 'page_load')))?>
		<?php _e('When page load', PPS_LANG_CODE)?>
	</label>
	<div id="ppsOptDesc_params_main_show_on_page_load" style="display: none;">
		<label>
			<?php echo htmlPps::checkbox('params[main][show_on_page_load_enb_delay]', array('checked' => htmlPps::checkedOpt($this->popup['params']['main'], 'show_on_page_load_enb_delay')))?>
			<?php _e('Delay for', PPS_LANG_CODE)?>
		</label>
		<label>
			<?php echo htmlPps::text('params[main][show_on_page_load_delay]', array('value' => $this->popup['params']['main']['show_on_page_load_delay']));?>
			<span class="supsystic-tooltip" title="<?php _e('Seconds', PPS_LANG_CODE)?>"><?php _e('sec', PPS_LANG_CODE)?></span>
		</label>
	</div><br />
	<label class="ppsPopupMainOptLbl">
		<?php echo htmlPps::radiobutton('params[main][show_on]', array(
			'value' => 'click_on_page',
			'checked' => htmlPps::checkedOpt($this->popup['params']['main'], 'show_on', 'click_on_page')))?>
		<?php _e('User click on the page', PPS_LANG_CODE)?>
	</label><br />
	<label class="ppsPopupMainOptLbl">
		<?php echo htmlPps::radiobutton('params[main][show_on]', array(
			'value' => 'click_on_element',
			'checked' => htmlPps::checkedOpt($this->popup['params']['main'], 'show_on', 'click_on_element')))?>
		<?php _e('Click on certain link / button / other element', PPS_LANG_CODE)?>
	</label>
	<div id="ppsOptDesc_params_main_show_on_click_on_element" style="display: none;">
		<span>
			<?php _e('Copy & paste next code - into required link to open PopUp on Click', PPS_LANG_CODE)?>:<br />
			<?php echo htmlPps::text('ppsCopyTextCode', array(
				'value' => esc_html('['. PPS_SHORTCODE_CLICK. ' id='. $this->popup['id']. ']'),
				'attrs' => 'class="ppsCopyTextCode supsystic-tooltip-right" title="'. esc_html(sprintf(__('Check screenshot with details - <a onclick="ppsShowTipScreenPopUp(this); return false;" href="%s">here</a>.', PPS_LANG_CODE), $this->getModule()->getModPath(). 'img/show-on-element-click.png')). '"'));?>
		</span>
		<br />
		<?php _e('Or, if you know HTML basics, - you can insert "onclick" attribute to any of your element from code below')?>:<br />
		<?php echo htmlPps::text('ppsCopyTextCode', array(
				'value' => esc_html('onclick="ppsShowPopup('. $this->popup['id'] .'); return false;"'),
				'attrs' => 'class="ppsCopyTextCode"'));?>
	</div><br />
	<label class="ppsPopupMainOptLbl">
		<?php echo htmlPps::radiobutton('params[main][show_on]', array(
			'value' => 'scroll_window',
			'checked' => htmlPps::checkedOpt($this->popup['params']['main'], 'show_on', 'scroll_window')))?>
		<?php _e('Scroll window', PPS_LANG_CODE)?>
	</label>
	<div id="ppsOptDesc_params_main_show_on_scroll_window" style="display: none;">
		<label>
			<?php echo htmlPps::checkbox('params[main][show_on_scroll_window_enb_delay]', array('checked' => htmlPps::checkedOpt($this->popup['params']['main'], 'show_on_scroll_window_enb_delay')))?>
			<?php _e('Delay for', PPS_LANG_CODE)?>
		</label>
		<label>
			<?php echo htmlPps::text('params[main][show_on_scroll_window_delay]', array('value' => isset($this->popup['params']['main']['show_on_scroll_window_delay']) ? $this->popup['params']['main']['show_on_scroll_window_delay'] : 0));?>
			<?php _e('seconds after first scroll', PPS_LANG_CODE)?>
		</label><br />
		<label>
			<?php echo htmlPps::checkbox('params[main][show_on_scroll_window_enb_perc_scroll]', array('checked' => htmlPps::checkedOpt($this->popup['params']['main'], 'show_on_scroll_window_enb_perc_scroll')))?>
			<?php _e('Scrolled to', PPS_LANG_CODE)?>
		</label>
		<label>
			<?php echo htmlPps::text('params[main][show_on_scroll_window_perc_scroll]', array('value' => isset($this->popup['params']['main']['show_on_scroll_window_perc_scroll']) ? $this->popup['params']['main']['show_on_scroll_window_perc_scroll'] : 0));?>
			<?php _e('percents of total scroll', PPS_LANG_CODE)?>
		</label>
	</div><br />
	<?php //dispatcherPps::doAction('editPopupMainOptsShowOn', $this->popup)?>
	<label class="ppsPopupMainOptLbl">
		<?php echo htmlPps::radiobutton('params[main][show_on]', array(
			'attrs' => 'class="ppsProOpt"',
			'value' => 'on_exit',
			'checked' => htmlPps::checkedOpt($this->popup['params']['main'], 'show_on', 'on_exit')))?>
		<span class="supsystic-tooltip-right" title="<?php echo esc_html(sprintf(__('Show when user try to exit from your site. <a target="_blank" href="%s">Check example.</a>', PPS_LANG_CODE), 'http://supsystic.com/exit-popup/'))?>">
			<?php _e('On Exit from Site', PPS_LANG_CODE)?>
		</span>
		<?php if(!$this->isPro) {?>
			<span class="ppsProOptMiniLabel"><a target="_blank" href="<?php echo $this->mainLink. '?utm_source=plugin&utm_medium=on_exit&utm_campaign=popup';?>"><?php _e('PRO option', PPS_LANG_CODE)?></a></span>
		<?php }?>
	</label><br />
	<label class="ppsPopupMainOptLbl">
		<?php echo htmlPps::radiobutton('params[main][show_on]', array(
			'attrs' => 'class="ppsProOpt"',
			'value' => 'page_bottom',
			'checked' => htmlPps::checkedOpt($this->popup['params']['main'], 'show_on', 'page_bottom')))?>
		<span class="supsystic-tooltip-right" title="<?php echo esc_html(__('When user are on the bottom of the page: scroll it down to the bottom, or if there are no vertical scroll on his device - just show it right after page load.', PPS_LANG_CODE))?>">
			<?php _e('Bottom of the page', PPS_LANG_CODE)?>
		</span>
		<?php if(!$this->isPro) {?>
			<span class="ppsProOptMiniLabel"><a target="_blank" href="<?php echo $this->mainLink. '?utm_source=plugin&utm_medium=page_bottom&utm_campaign=popup';?>"><?php _e('PRO option', PPS_LANG_CODE)?></a></span>
		<?php }?>
	</label><br />
	<label class="ppsPopupMainOptLbl">
		<?php echo htmlPps::radiobutton('params[main][show_on]', array(
			'attrs' => 'class="ppsProOpt"',
			'value' => 'after_inactive',
			'checked' => htmlPps::checkedOpt($this->popup['params']['main'], 'show_on', 'after_inactive')))?>
		<span class="supsystic-tooltip-right" title="<?php echo esc_html(__('After user was inactive on your page for some time.', PPS_LANG_CODE))?>">
			<?php _e('After Inactivity', PPS_LANG_CODE)?>
		</span>
		<?php if(!$this->isPro) {?>
			<span class="ppsProOptMiniLabel"><a target="_blank" href="<?php echo $this->mainLink. '?utm_source=plugin&utm_medium=after_inactive&utm_campaign=popup';?>"><?php _e('PRO option', PPS_LANG_CODE)?></a></span>
		<?php }?>
	</label>
	<?php if($this->isPro) {?>
	<div id="ppsOptDesc_params_main_show_on_after_inactive" style="display: none;">
		<?php _e('After user was inactive for', PPS_LANG_CODE)?>
		<?php echo htmlPps::text('params[main][show_on_after_inactive_value]', array(
			'value' => isset($this->popup['params']['main']['show_on_after_inactive_value']) ? $this->popup['params']['main']['show_on_after_inactive_value'] : 10 /*Default - 5 seconds*/));?>
		<span class="supsystic-tooltip" title="<?php _e('Seconds', PPS_LANG_CODE)?>"><?php _e('sec', PPS_LANG_CODE)?></span>
	</div><?php }?><br />
	<label class="ppsPopupMainOptLbl">
		<?php echo htmlPps::radiobutton('params[main][show_on]', array(
			'attrs' => 'class="ppsProOpt"',
			'value' => 'after_comment',
			'checked' => htmlPps::checkedOpt($this->popup['params']['main'], 'show_on', 'after_comment')))?>
		<span class="supsystic-tooltip-right" title="<?php echo esc_html(__('User add comment on your site - and will see this PopUp after comment was placed. This will help you interest active users on your site.', PPS_LANG_CODE))?>">
			<?php _e('After User Comment', PPS_LANG_CODE)?>
		</span>
		<?php if(!$this->isPro) {?>
			<span class="ppsProOptMiniLabel"><a target="_blank" href="<?php echo $this->mainLink. '?utm_source=plugin&utm_medium=after_comment&utm_campaign=popup';?>"><?php _e('PRO option', PPS_LANG_CODE)?></a></span>
		<?php }?>
	</label><br />
	<label class="ppsPopupMainOptLbl">
		<?php echo htmlPps::radiobutton('params[main][show_on]', array(
			'attrs' => 'class="ppsProOpt"',
			'value' => 'after_checkout',
			'checked' => htmlPps::checkedOpt($this->popup['params']['main'], 'show_on', 'after_checkout')))?>
		<span class="supsystic-tooltip-right" title="<?php echo esc_html(__('Show PopUp after success checkout process on your online store.', PPS_LANG_CODE))?>">
			<?php _e('After Purchasing (Checkout)', PPS_LANG_CODE)?>
		</span>
		<?php if(!$this->isPro) {?>
			<span class="ppsProOptMiniLabel"><a target="_blank" href="<?php echo $this->mainLink. '?utm_source=plugin&utm_medium=after_checkout&utm_campaign=popup';?>"><?php _e('PRO option', PPS_LANG_CODE)?></a></span>
		<?php }?>
	</label>
	<?php if($this->isPro) {?>
	<div id="ppsOptDesc_params_main_show_on_after_checkout" style="display: none;">
		<?php _e('Copy & Paste next code on your Success checkout page content editor', PPS_LANG_CODE)?>:
		<?php echo htmlPps::text('ppsCopyTextCode', array(
			'value' => esc_html('['. PPS_SHORTCODE. ' id='. $this->popup['id']. ']'),
			'attrs' => 'class="ppsCopyTextCode"'));?><br />
		<?php _e('Or, if you are using your own html/php for this page - insert there next code', PPS_LANG_CODE)?>:
		<?php echo htmlPps::textarea('ppsCopyTextCode', array(
			'value' => esc_html($this->afterCheckoutCode),
			/*'value' => esc_html('<?php echo do_shortcode("['. PPS_SHORTCODE. ' id='. $this->popup['id']. ']")?>'),*/
			'attrs' => 'class="ppsCopyTextCode"'));?>
	</div><?php }?><br />
</section>
<section class="ppsPopupMainOptSect">
	<span class="ppsOptLabel"><?php _e('When to close PopUp', PPS_LANG_CODE)?></span>
	<hr />
	<label class="ppsPopupMainOptLbl">
		<?php echo htmlPps::radiobutton('params[main][close_on]', array(
			'value' => 'user_close',
			'checked' => !isset($this->popup['params']['main']['close_on']) ? true : htmlPps::checkedOpt($this->popup['params']['main'], 'close_on', 'user_close')))?>
		<?php _e('After user close it', PPS_LANG_CODE)?>
	</label><br />
	<label class="ppsPopupMainOptLbl">
		<?php echo htmlPps::radiobutton('params[main][close_on]', array(
			'value' => 'overlay_click',
			'checked' => !isset($this->popup['params']['main']['close_on']) ? false : htmlPps::checkedOpt($this->popup['params']['main'], 'close_on', 'overlay_click')))?>
		<span class="supsystic-tooltip-right" title="<?php echo esc_html(__('Close PopUp when user click outside of the actually PopUp window.', PPS_LANG_CODE))?>">
			<?php _e('Click outside PopUp', PPS_LANG_CODE)?>
		</span>
	</label><br />
	<label class="ppsPopupMainOptLbl">
		<?php echo htmlPps::radiobutton('params[main][close_on]', array(
			'attrs' => 'class="ppsProOpt"',
			'value' => 'after_action',
			'checked' => htmlPps::checkedOpt($this->popup['params']['main'], 'close_on', 'after_action')))?>
		<span class="supsystic-tooltip-right" title="<?php echo esc_html(__('Will not allow user to close your PopUp - until finish at least one action: Subscribe, Share or Like.', PPS_LANG_CODE))?>">
			<?php _e('Only after action (Subscribe / Share / Like)', PPS_LANG_CODE)?>
		</span>
		<?php if(!$this->isPro) {?>
			<span class="ppsProOptMiniLabel"><a target="_blank" href="<?php echo $this->mainLink. '?utm_source=plugin&utm_medium=close_on_after_action&utm_campaign=popup';?>"><?php _e('PRO option', PPS_LANG_CODE)?></a></span>
		<?php }?>
	</label><br />
	<label class="supsystic-tooltip-bottom ppsPopupMainOptLbl" title="<?php echo esc_html(__('Close PopUp after it will be visible specified time.', PPS_LANG_CODE))?>">
		<?php echo htmlPps::radiobutton('params[main][close_on]', array(
			'attrs' => 'class="ppsProOpt"',
			'value' => 'after_time',
			'checked' => htmlPps::checkedOpt($this->popup['params']['main'], 'close_on', 'after_time')))?>
		<?php _e('After time passed', PPS_LANG_CODE)?>
		<?php if(!$this->isPro) {?>
			<span class="ppsProOptMiniLabel"><a target="_blank" href="<?php echo $this->mainLink. '?utm_source=plugin&utm_medium=close_on_after_time&utm_campaign=popup';?>"><?php _e('PRO option', PPS_LANG_CODE)?></a></span>
		<?php }?>
	</label>
	<?php if($this->isPro) {?>
	<div id="ppsOptDesc_params_main_close_on_after_time" class="ppsOptDesc_params_main_close_on ppsPopupMainOptDesc" style="display: none;">
		<label>
			<?php _e('Close after', PPS_LANG_CODE)?>
			<?php echo htmlPps::text('params[main][close_on_after_time_value]', array(
				'value' => isset($this->popup['params']['main']['close_on_after_time_value']) ? $this->popup['params']['main']['close_on_after_time_value'] : 5 /*Default - 5 seconds*/));?>
			<span class="supsystic-tooltip" title="<?php _e('Seconds', PPS_LANG_CODE)?>"><?php _e('sec', PPS_LANG_CODE)?></span>
		</label>
	</div><?php }?><br />
</section>
<section class="ppsPopupMainOptSect">
	<span class="ppsOptLabel"><?php _e('Whom to show', PPS_LANG_CODE)?></span>
	<hr />
	<label class="ppsPopupMainOptLbl">
		<?php echo htmlPps::radiobutton('params[main][show_to]', array(
			'value' => 'everyone',
			'checked' => htmlPps::checkedOpt($this->popup['params']['main'], 'show_to', 'everyone')))?>
		<?php _e('Everyone', PPS_LANG_CODE)?>
	</label><br />
	<label class="ppsPopupMainOptLbl">
		<?php echo htmlPps::radiobutton('params[main][show_to]', array(
			'value' => 'first_time_visit',
			'checked' => htmlPps::checkedOpt($this->popup['params']['main'], 'show_to', 'first_time_visit')))?>
		<?php _e('For first-time visitors', PPS_LANG_CODE)?>
	</label>
	<div id="ppsOptDesc_params_main_show_to_first_time_visit" style="display: none;">
		<label class="supsystic-tooltip-left" title="<?php _e('Will remember user visit for entered number of days and show PopUp to same user again - after this period. To remember only for one browser session - use 0 here, to remember forever - try to set big number - 99999 for example.')?>">
			<?php _e('Remember for', PPS_LANG_CODE)?>
			<?php echo htmlPps::text('params[main][show_to_first_time_visit_days]', array(
				'value' => isset($this->popup['params']['main']['show_to_first_time_visit_days']) ? $this->popup['params']['main']['show_to_first_time_visit_days'] : 30,
				'attrs' => 'style="width: 50px;"'
			));?>
			<span><?php _e('days', PPS_LANG_CODE)?></span>
		</label>
	</div><br />
	<label class="supsystic-tooltip-left ppsPopupMainOptLbl" title="<?php _e('Subscribe, share, like, etc.')?>" style="">
		<?php echo htmlPps::radiobutton('params[main][show_to]', array(
			'value' => 'until_make_action',
			'checked' => htmlPps::checkedOpt($this->popup['params']['main'], 'show_to', 'until_make_action')))?>
		<?php _e('Until user makes an action', PPS_LANG_CODE)?>
	</label>
	<div id="ppsOptDesc_params_main_show_to_until_make_action" style="display: none;">
		<label class="supsystic-tooltip-left" title="<?php _e('Will remember user action for entered number of days and show PopUp to same user again - after this period. To remember only for one browser session - use 0 here, to remember forever - try to set big number - 99999 for example.')?>">
			<?php _e('Remember for', PPS_LANG_CODE)?>
			<?php echo htmlPps::text('params[main][show_to_until_make_action_days]', array(
				'value' => isset($this->popup['params']['main']['show_to_until_make_action_days']) ? $this->popup['params']['main']['show_to_until_make_action_days'] : 30,
				'attrs' => 'style="width: 50px;"'
			));?>
			<span><?php _e('days', PPS_LANG_CODE)?></span>
		</label>
	</div><br />
	<label class="ppsPopupMainOptLbl ppsPopupMainOptLbl" id="ppsHideForDevicesLabel">
		<?php _e('Hide for', PPS_LANG_CODE)?>:
		<?php echo htmlPps::selectlist('params[main][hide_for_devices][]', array(
			'options' => $this->hideForList, 
			'value' => (isset($this->popup['params']['main']['hide_for_devices']) ? $this->popup['params']['main']['hide_for_devices'] : array()), 
			'attrs' => 'class="chosen" data-placeholder="'. __('Choose devices', PPS_LANG_CODE). '"'))?>
	</label><br />
	<label class="supsystic-tooltip-left ppsPopupMainOptLbl" title="<?php _e('Hide PopUp for Logged-in users and show it only for not Logged site visitors.')?>" style="">
		<?php echo htmlPps::checkbox('params[main][hide_for_logged_in]', array(
			'checked' => htmlPps::checkedOpt($this->popup['params']['main'], 'hide_for_logged_in')))?>
		<?php _e('Hide for Logged-in', PPS_LANG_CODE)?>
	</label><br />
	<?php /*?><br />
	<label>
		<?php echo htmlPps::radiobutton('params[main][show_to]', array(
			'value' => 'for_countries',
			'checked' => htmlPps::checkedOpt($this->popup['params']['main'], 'show_to', 'for_countries')))?>
		<?php _e('Specify country', PPS_LANG_CODE)?>
	</label><?php */?>
	<div style="clear: both;"></div>
	<span class="ppsOptLabel"><?php _e('Show on next pages', PPS_LANG_CODE)?></span>
	<hr />
	<label class="ppsPopupMainOptLbl">
		<?php echo htmlPps::radiobutton('params[main][show_pages]', array(
			'value' => 'all',
			'checked' => htmlPps::checkedOpt($this->popup['params']['main'], 'show_pages', 'all')))?>
		<?php _e('All pages', PPS_LANG_CODE)?>
	</label><br />
	<label class="ppsPopupMainOptLbl">
		<?php echo htmlPps::radiobutton('params[main][show_pages]', array(
			'value' => 'show_on_pages',
			'checked' => htmlPps::checkedOpt($this->popup['params']['main'], 'show_pages', 'show_on_pages')))?>
		<?php _e('Show on next pages / posts', PPS_LANG_CODE)?>
	</label>
	<div id="ppsOptDesc_params_main_show_pages_show_on_pages" style="display: none;">
		<?php echo htmlPps::selectlist('show_pages_list', array('options' => $this->allPagesForSelect, 'value' => $this->selectedShowPages, 'attrs' => 'class="chosen" data-placeholder="'. __('Choose Pages', PPS_LANG_CODE). '"'))?>
	</div><br />
	<label class="ppsPopupMainOptLbl">
		<?php echo htmlPps::radiobutton('params[main][show_pages]', array(
			'value' => 'not_show_on_pages',
			'checked' => htmlPps::checkedOpt($this->popup['params']['main'], 'show_pages', 'not_show_on_pages')))?>
		<?php _e('Don\'t show on next pages / posts', PPS_LANG_CODE)?>
	</label>
	<div id="ppsOptDesc_params_main_show_pages_not_show_on_pages" style="display: none;">
		<?php echo htmlPps::selectlist('not_show_pages_list', array('options' => $this->allPagesForSelect, 'value' => $this->selectedHidePages, 'attrs' => 'class="chosen" data-placeholder="'. __('Choose Pages', PPS_LANG_CODE). '"'))?>
	</div>
</section>