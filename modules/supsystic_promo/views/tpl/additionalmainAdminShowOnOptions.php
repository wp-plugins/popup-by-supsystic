<label class="supsystic-tooltip-right" title="<?php echo esc_html(sprintf(__('Show when user try to exit from your site. <a target="_blank" href="%s">Check example.</a>', PPS_LANG_CODE), 'http://supsystic.com/exit-popup/'))?>">
	<?php echo htmlPps::radiobutton('promo_show_on_opt', array(
		'value' => 'on_exit_promo',
		'checked' => false,
		'attrs' => 'disabled="disabled"'))?>
	<?php _e('On Exit from Site', PPS_LANG_CODE)?>
	<a target="_blank" href="http://supsystic.com/plugins/popup-plugin/"><?php _e('Available in PRO')?></a>
</label>