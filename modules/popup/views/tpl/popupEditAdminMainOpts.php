<?php
	// Check if PopUp use Google Maps shortcode and there are no Google Maps plugin on site - show warning
	$warnings = array();
	if(isset($this->popup['params']['opts_attrs']['txt_block_number']) 
		&& !empty($this->popup['params']['opts_attrs']['txt_block_number'])
	) {
		for($i = 0; $i < $this->popup['params']['opts_attrs']['txt_block_number']; $i++) {
			if(isset($this->popup['params']['tpl']['txt_'. $i]) 
				&& !empty($this->popup['params']['tpl']['txt_'. $i])
				&& strpos($this->popup['params']['tpl']['txt_'. $i], '[google_map_easy') !== false
				&& !class_exists('frameGmp')
			) {
				$warnings[] = sprintf(__('You are using Google Maps in this PopUp, but don\'t have Google Maps Easy plugin installed. You can get it for Free from WordPress site <a target="_blank" href="%s">here</a>, install and start using this PopUp with map.', PPS_LANG_CODE), 'https://wordpress.org/plugins/google-maps-easy/');
				break;
			}
		}
	}
?>
<?php if(!empty($warnings)) { ?>
	<div class="alert alert-warning"><?php echo implode('<br />', $warnings)?></div>
<?php }?>
<section class="ppsPopupMainOptSect">
	<span class="ppsOptLabel"><?php _e('When to show PopUp', PPS_LANG_CODE)?></span>
	<hr />
	<label class="ppsPopupMainOptLbl">
		<?php echo htmlPps::radiobutton('params[main][show_on]', array(
			'value' => 'page_load', 
			'checked' => htmlPps::checkedOpt($this->popup['params']['main'], 'show_on', 'page_load')))?>
		<?php _e('When page loads', PPS_LANG_CODE)?>
	</label>
	<div id="ppsOptDesc_params_main_show_on_page_load" style="display: none;" class="ppsOptDescParamsShell">
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
	<div id="ppsOptDesc_params_main_show_on_click_on_element" style="display: none;" class="ppsOptDescParamsShell">
		<?php _e('Copy & paste next code - into required link to open PopUp on Click', PPS_LANG_CODE)?>:<br />
		<?php echo htmlPps::text('ppsCopyTextCode', array(
			'value' => esc_html('['. PPS_SHORTCODE_CLICK. ' id='. $this->popup['id']. ']'),
			'attrs' => 'class="ppsCopyTextCode supsystic-tooltip-right" title="'. esc_html(sprintf(__('Check screenshot with details - <a onclick="ppsShowTipScreenPopUp(this); return false;" href="%s">here</a>.', PPS_LANG_CODE), $this->getModule()->getAssetsUrl(). 'img/show-on-element-click.png')). '"'));?>
		<br />
		<?php _e('Or, if you know HTML basics, - you can insert "onclick" attribute to any of your element from code below', PPS_LANG_CODE)?>:<br />
		<?php echo htmlPps::text('ppsCopyTextCode', array(
				'value' => esc_html('onclick="ppsShowPopup('. $this->popup['id'] .'); return false;"'),
				'attrs' => 'class="ppsCopyTextCode"'));?><br />
		<?php _e('Or you can even use it for your Menu item, just add code', PPS_LANG_CODE)?>:<br />
		<?php echo htmlPps::text('ppsCopyTextCode', array(
				'value' => esc_html('#ppsShowPopUp_'. $this->popup['id']),
				'attrs' => 'class="ppsCopyTextCode"'));?><br />
		<?php _e('into your menu item "Title Attribute" field. Don\'t worry - users will not see this code as menu item title on your site.', PPS_LANG_CODE)?>
	</div><br />
	<label class="ppsPopupMainOptLbl">
		<?php echo htmlPps::radiobutton('params[main][show_on]', array(
			'value' => 'scroll_window',
			'checked' => htmlPps::checkedOpt($this->popup['params']['main'], 'show_on', 'scroll_window')))?>
		<?php _e('Scroll window', PPS_LANG_CODE)?>
	</label>
	<div id="ppsOptDesc_params_main_show_on_scroll_window" style="display: none;" class="ppsOptDescParamsShell">
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
	<label class="ppsPopupMainOptLbl">
		<?php echo htmlPps::radiobutton('params[main][show_on]', array(
			'attrs' => 'class="ppsProOpt"',
			'value' => 'on_exit',
			'checked' => htmlPps::checkedOpt($this->popup['params']['main'], 'show_on', 'on_exit')))?>
		<span class="supsystic-tooltip-right" title="<?php echo esc_html(sprintf(__('Show when user tries to exit from your site. <a target="_blank" href="%s">Check example.</a>', PPS_LANG_CODE), 'http://supsystic.com/exit-popup/'))?>">
			<?php _e('On Exit from Site', PPS_LANG_CODE)?>
		</span>
		<?php if(!$this->isPro) {?>
			<span class="ppsProOptMiniLabel"><a target="_blank" href="<?php echo framePps::_()->getModule('supsystic_promo')->generateMainLink('utm_source=plugin&utm_medium=on_exit&utm_campaign=popup');?>"><?php _e('PRO option', PPS_LANG_CODE)?></a></span>
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
			<span class="ppsProOptMiniLabel"><a target="_blank" href="<?php echo framePps::_()->getModule('supsystic_promo')->generateMainLink('utm_source=plugin&utm_medium=page_bottom&utm_campaign=popup');?>"><?php _e('PRO option', PPS_LANG_CODE)?></a></span>
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
			<span class="ppsProOptMiniLabel"><a target="_blank" href="<?php echo framePps::_()->getModule('supsystic_promo')->generateMainLink('utm_source=plugin&utm_medium=after_inactive&utm_campaign=popup');?>"><?php _e('PRO option', PPS_LANG_CODE)?></a></span>
		<?php }?>
	</label>
	<?php if($this->isPro) {?>
	<div id="ppsOptDesc_params_main_show_on_after_inactive" style="display: none;" class="ppsOptDescParamsShell">
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
			<span class="ppsProOptMiniLabel"><a target="_blank" href="<?php echo framePps::_()->getModule('supsystic_promo')->generateMainLink('utm_source=plugin&utm_medium=after_comment&utm_campaign=popup');?>"><?php _e('PRO option', PPS_LANG_CODE)?></a></span>
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
			<span class="ppsProOptMiniLabel"><a target="_blank" href="<?php echo framePps::_()->getModule('supsystic_promo')->generateMainLink('utm_source=plugin&utm_medium=after_checkout&utm_campaign=popup');?>"><?php _e('PRO option', PPS_LANG_CODE)?></a></span>
		<?php }?>
	</label>
	<?php if($this->isPro) {?>
	<div id="ppsOptDesc_params_main_show_on_after_checkout" style="display: none;" class="ppsOptDescParamsShell">
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
	<label class="ppsPopupMainOptLbl">
		<?php echo htmlPps::radiobutton('params[main][show_on]', array(
			'attrs' => 'class="ppsProOpt"',
			'value' => 'link_follow',
			'checked' => htmlPps::checkedOpt($this->popup['params']['main'], 'show_on', 'link_follow')))?>
		<?php
			$openByFollowExl = get_bloginfo('wpurl'). '#ppsShowPopUp_'. $this->popup['id'];
		?>
		<span class="supsystic-tooltip-right" title="<?php echo esc_html(sprintf(__('Show when user open your site exactly by link, where in the end will be present #ppsShowPopUp_%d. For example <a href="%s" target="_blank">%s</a> (will work only with PRO version).', PPS_LANG_CODE), $this->popup['id'], $openByFollowExl, $openByFollowExl))?>">
			<?php _e('On Link Follow', PPS_LANG_CODE)?>
		</span>
		<?php if(!$this->isPro) {?>
			<span class="ppsProOptMiniLabel"><a target="_blank" href="<?php echo framePps::_()->getModule('supsystic_promo')->generateMainLink('utm_source=plugin&utm_medium=link_follow&utm_campaign=popup');?>"><?php _e('PRO option', PPS_LANG_CODE)?></a></span>
		<?php }?>
	</label>
	<div id="ppsOptDesc_params_main_show_on_link_follow" style="display: none;" class="ppsOptDescParamsShell">
		<?php _e('Copy & paste next code - into end of required link on your site', PPS_LANG_CODE)?>:<br />
		<?php echo htmlPps::text('ppsCopyTextCode', array(
			'value' => esc_html('#ppsShowPopUp_'. $this->popup['id']),
			'attrs' => 'class="ppsCopyTextCode"'));?>
	</div><br />
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
			<span class="ppsProOptMiniLabel"><a target="_blank" href="<?php echo framePps::_()->getModule('supsystic_promo')->generateMainLink('utm_source=plugin&utm_medium=close_on_after_action&utm_campaign=popup');?>"><?php _e('PRO option', PPS_LANG_CODE)?></a></span>
		<?php }?>
	</label><br />
	<label class="supsystic-tooltip-bottom ppsPopupMainOptLbl" title="<?php echo esc_html(__('Close PopUp after it will be visible specified time.', PPS_LANG_CODE))?>">
		<?php echo htmlPps::radiobutton('params[main][close_on]', array(
			'attrs' => 'class="ppsProOpt"',
			'value' => 'after_time',
			'checked' => htmlPps::checkedOpt($this->popup['params']['main'], 'close_on', 'after_time')))?>
		<?php _e('After time passed', PPS_LANG_CODE)?>
		<?php if(!$this->isPro) {?>
			<span class="ppsProOptMiniLabel"><a target="_blank" href="<?php echo framePps::_()->getModule('supsystic_promo')->generateMainLink('utm_source=plugin&utm_medium=close_on_after_time&utm_campaign=popup');?>"><?php _e('PRO option', PPS_LANG_CODE)?></a></span>
		<?php }?>
	</label>
	<?php if($this->isPro) {?>
	<div id="ppsOptDesc_params_main_close_on_after_time" class="ppsOptDesc_params_main_close_on ppsPopupMainOptDesc ppsOptDescParamsShell" style="display: none;">
		<label>
			<?php _e('Close after', PPS_LANG_CODE)?>
			<?php echo htmlPps::text('params[main][close_on_after_time_value]', array(
				'value' => isset($this->popup['params']['main']['close_on_after_time_value']) ? $this->popup['params']['main']['close_on_after_time_value'] : 5 /*Default - 5 seconds*/));?>
			<span class="supsystic-tooltip" title="<?php _e('Seconds', PPS_LANG_CODE)?>"><?php _e('sec', PPS_LANG_CODE)?></span>
		</label>
	</div><?php }?><br />
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
	<div id="ppsOptDesc_params_main_show_pages_show_on_pages" style="display: none;" class="ppsOptDescParamsShell">
		<?php echo htmlPps::selectlist('show_pages_list', array('options' => $this->allPagesForSelect, 'value' => $this->selectedShowPages, 'attrs' => 'class="chosen" data-placeholder="'. __('Choose Pages', PPS_LANG_CODE). '"'))?>
	</div><br />
	<label class="ppsPopupMainOptLbl">
		<?php echo htmlPps::radiobutton('params[main][show_pages]', array(
			'value' => 'not_show_on_pages',
			'checked' => htmlPps::checkedOpt($this->popup['params']['main'], 'show_pages', 'not_show_on_pages')))?>
		<?php _e('Don\'t show on next pages / posts', PPS_LANG_CODE)?>
	</label>
	<div id="ppsOptDesc_params_main_show_pages_not_show_on_pages" style="display: none;" class="ppsOptDescParamsShell">
		<?php echo htmlPps::selectlist('not_show_pages_list', array('options' => $this->allPagesForSelect, 'value' => $this->selectedHidePages, 'attrs' => 'class="chosen" data-placeholder="'. __('Choose Pages', PPS_LANG_CODE). '"'))?>
	</div>
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
	<div id="ppsOptDesc_params_main_show_to_first_time_visit" style="display: none;" class="ppsOptDescParamsShell">
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
	<div id="ppsOptDesc_params_main_show_to_until_make_action" style="display: none;" class="ppsOptDescParamsShell">
		<label class="supsystic-tooltip-left" title="<?php _e('Will remember user action for entered number of days and show PopUp to same user again - after this period. To remember only for one browser session - use 0 here, to remember forever - try to set big number - 99999 for example.')?>">
			<?php _e('Remember for', PPS_LANG_CODE)?>
			<?php echo htmlPps::text('params[main][show_to_until_make_action_days]', array(
				'value' => isset($this->popup['params']['main']['show_to_until_make_action_days']) ? $this->popup['params']['main']['show_to_until_make_action_days'] : 30,
				'attrs' => 'style="width: 50px;"'
			));?>
			<span><?php _e('days', PPS_LANG_CODE)?></span>
		</label>
	</div><br />
	<label class="ppsPopupMainOptLbl" id="ppsHideForDevicesLabel">
		<span class="supsystic-tooltip" title="<?php echo esc_html(__('Click to revert feature function: from Hide - to Show, and vice versa.', PPS_LANG_CODE))?>">
			<a href="#" class="ppsSwitchShowHideOptLink" data-input-value="0" data-input-name="params[main][hide_for_devices_show]"><?php _e('Hide', PPS_LANG_CODE)?></a>/<a href="#" class="ppsSwitchShowHideOptLink" data-input-value="1" data-input-name="params[main][hide_for_devices_show]"><?php _e('Show Only')?></a>
			<?php echo htmlPps::hidden('params[main][hide_for_devices_show]', array(
				'value' => (isset($this->popup['params']['main']['hide_for_devices_show']) ? $this->popup['params']['main']['hide_for_devices_show'] : 0)
			))?>
		</span>
		<?php _e('for Devices', PPS_LANG_CODE)?>
		<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('You can make PopUp visible or hidden only when users will view your site from selected devices.', PPS_LANG_CODE))?>"></i>
		:<div style="padding-bottom: 5px; clear: both;"></div>
		<?php echo htmlPps::selectlist('params[main][hide_for_devices][]', array(
			'options' => $this->hideForList, 
			'value' => (isset($this->popup['params']['main']['hide_for_devices']) ? $this->popup['params']['main']['hide_for_devices'] : array()), 
			'attrs' => 'class="chosen" data-placeholder="'. __('Choose devices', PPS_LANG_CODE). '"'))?>
	</label><br />
	<label class="ppsPopupMainOptLbl ppsPopupMainOptLbl" id="ppsHideForPostTypesLabel">
		<span class="supsystic-tooltip" title="<?php echo esc_html(__('Click to revert feature function: from Hide - to Show, and vice versa.', PPS_LANG_CODE))?>">
			<a href="#" class="ppsSwitchShowHideOptLink" data-input-value="0" data-input-name="params[main][hide_for_post_types_show]"><?php _e('Hide', PPS_LANG_CODE)?></a>/<a href="#" class="ppsSwitchShowHideOptLink" data-input-value="1" data-input-name="params[main][hide_for_post_types_show]"><?php _e('Show Only')?></a>
			<?php echo htmlPps::hidden('params[main][hide_for_post_types_show]', array(
				'value' => (isset($this->popup['params']['main']['hide_for_post_types_show']) ? $this->popup['params']['main']['hide_for_post_types_show'] : 0)
			))?>
		</span>
		<?php _e('for Post Types', PPS_LANG_CODE)?>
		<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('You can make PopUp visible or hidden only for specified Pos Types, for example - hide it on all Pages.', PPS_LANG_CODE))?>"></i>
		:<div style="padding-bottom: 5px; clear: both;"></div>
		<?php echo htmlPps::selectlist('params[main][hide_for_post_types][]', array(
			'options' => $this->hideForPostTypesList,
			'value' => (isset($this->popup['params']['main']['hide_for_post_types']) ? $this->popup['params']['main']['hide_for_post_types'] : array()),
			'attrs' => 'class="chosen" data-placeholder="'. __('Choose post types', PPS_LANG_CODE). '"'))?>
	</label><br />
	<label class="ppsPopupMainOptLbl" style="display: inline; vertical-align: middle; padding-top: 12px;">
		<span class="supsystic-tooltip" title="<?php echo esc_html(__('Click to revert feature function: from Hide - to Show, and vice versa.', PPS_LANG_CODE))?>">
			<a href="#" class="ppsSwitchShowHideOptLink" data-input-value="0" data-input-name="params[main][hide_for_ips_show]"><?php _e('Hide', PPS_LANG_CODE)?></a>/<a href="#" class="ppsSwitchShowHideOptLink" data-input-value="1" data-input-name="params[main][hide_for_ips_show]"><?php _e('Show Only')?></a>
			<?php echo htmlPps::hidden('params[main][hide_for_ips_show]', array(
				'value' => (isset($this->popup['params']['main']['hide_for_ips_show']) ? $this->popup['params']['main']['hide_for_ips_show'] : 0)
			))?>
		</span>
		<?php _e('for IP', PPS_LANG_CODE)?>
		<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(sprintf(__('For those IPs PopUp will not be displayed (or vice versa - depending on Hide/Show Only option). Please be advised that your IP - %s', PPS_LANG_CODE), $this->currentIp))?>"></i>
		:<div style="padding-bottom: 5px; clear: both;"></div>
		<a href="#" id="ppsHideForIpBtn" class="button"><?php _e('Show IPs List')?></a><br />
		<?php echo htmlPps::hidden('params[main][hide_for_ips]', array(
			'value' => (isset($this->popup['params']['main']['hide_for_ips']) ? $this->popup['params']['main']['hide_for_ips'] : '')
		))?>
		<div id="ppsHiddenIpStaticList" class="alert alert-info" style="padding: 5px 0 0; margin: 0;"></div>
	</label><br />
	<label class="ppsPopupMainOptLbl">
		<span class="supsystic-tooltip" title="<?php echo esc_html(__('Click to revert feature function: from Hide - to Show, and vice versa.', PPS_LANG_CODE))?>">
			<a href="#" class="ppsSwitchShowHideOptLink" data-input-value="0" data-input-name="params[main][hide_for_countries_show]"><?php _e('Hide', PPS_LANG_CODE)?></a>/<a href="#" class="ppsSwitchShowHideOptLink" data-input-value="1" data-input-name="params[main][hide_for_countries_show]"><?php _e('Show Only')?></a>
			<?php echo htmlPps::hidden('params[main][hide_for_countries_show]', array(
				'value' => (isset($this->popup['params']['main']['hide_for_countries_show']) ? $this->popup['params']['main']['hide_for_countries_show'] : 0)
			))?>
		</span>
		<?php _e('for Countries', PPS_LANG_CODE)?>
		<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(sprintf(__('For those Countries PopUp will not be displayed (or vice versa - depending on Hide/Show Only option). Please be advised that your Country code is %s', PPS_LANG_CODE), ($this->currentCountryCode ? $this->currentCountryCode : 'undefined (when using localhosts for example)')))?>"></i>
		:<div style="padding-bottom: 5px; clear: both;"></div>
		<?php echo htmlPps::selectlist('params[main][hide_for_countries][]', array(
			'options' => $this->countriesForSelect, 
			'value' => (isset($this->popup['params']['main']['hide_for_countries']) ? $this->popup['params']['main']['hide_for_countries'] : array()), 
			'attrs' => 'class="chosen" data-placeholder="'. __('Choose countries', PPS_LANG_CODE). '"'))?>
	</label><br />
	<label class="ppsPopupMainOptLbl">
		<span class="supsystic-tooltip" title="<?php echo esc_html(__('Click to revert feature function: from Hide - to Show, and vice versa.', PPS_LANG_CODE))?>">
			<a href="#" class="ppsSwitchShowHideOptLink" data-input-value="0" data-input-name="params[main][hide_for_languages_show]"><?php _e('Hide', PPS_LANG_CODE)?></a>/<a href="#" class="ppsSwitchShowHideOptLink" data-input-value="1" data-input-name="params[main][hide_for_languages_show]"><?php _e('Show Only')?></a>
			<?php echo htmlPps::hidden('params[main][hide_for_languages_show]', array(
				'value' => (isset($this->popup['params']['main']['hide_for_languages_show']) ? $this->popup['params']['main']['hide_for_languages_show'] : 0)
			))?>
		</span>
		<?php _e('for Languages', PPS_LANG_CODE)?>
		<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(sprintf(__('For those Languages PopUp will not be displayed. Language is defined by visitor browser language. Please be advised that your browser language is %s', PPS_LANG_CODE), $this->currentLanguage))?>"></i>
		:<div style="padding-bottom: 5px; clear: both;"></div>
		<?php if(!empty($this->languagesForSelect)) {?>
		<?php echo htmlPps::selectlist('params[main][hide_for_languages][]', array(
			'options' => $this->languagesForSelect, 
			'value' => (isset($this->popup['params']['main']['hide_for_languages']) ? $this->popup['params']['main']['hide_for_languages'] : array()), 
			'attrs' => 'class="chosen" data-placeholder="'. __('Choose languages', PPS_LANG_CODE). '"'))?>
		<?php } else { ?>
			<div class="alert alert-danger"><?php _e('This feature is supported only in WordPress version 4.0.0 or higher', PPS_LANG_CODE)?></div>
		<?php }?>
	</label><br />
	<label class="supsystic-tooltip-left ppsPopupMainOptLbl" title="<?php _e('Hide PopUp for Logged-in users and show it only for not Logged site visitors.')?>" style="">
		<?php echo htmlPps::checkbox('params[main][hide_for_logged_in]', array(
			'checked' => htmlPps::checkedOpt($this->popup['params']['main'], 'hide_for_logged_in')))?>
		<?php _e('Hide for Logged-in', PPS_LANG_CODE)?>
	</label><br />
</section>
<div id="ppsHideForIpWnd" style="display: none;" title="<?php _e('IPs List', PPS_LANG_CODE)?>">
	<label>
		<?php _e('Type here IPs that will not see PopUp, each IP - from new line', PPS_LANG_CODE)?>:<br />
		<?php echo htmlPps::textarea('hide_for_ips', array(
			'attrs' => 'id="ppsHideForIpTxt" style="width: 100%; height: 300px;"'
		))?>
	</label>
</div>