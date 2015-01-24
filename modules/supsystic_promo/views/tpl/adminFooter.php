<div class="ppsAdminFooterShell">
	<div class="ppsAdminFooterCell">
		<?php echo PPS_WP_PLUGIN_NAME?>
		<?php _e('Version', PPS_LANG_CODE)?>:
		<a target="_blank" href="http://worppsess.org/plugins/supsystic-secure/changelog/"><?php echo PPS_VERSION?></a>
	</div>
	<div class="ppsAdminFooterCell">|</div>
	<?php if(!framePps::_()->getModule(implode('', array('l','ic','e','ns','e')))) {?>
	<div class="ppsAdminFooterCell">
		<?php _e('Go', PPS_LANG_CODE)?>&nbsp;<a target="_blank" href="<?php echo $this->getModule()->preparePromoLink('http://supsystic.com/product/supsystic-secure/');?>"><?php _e('PRO', PPS_LANG_CODE)?></a>
	</div>
	<div class="ppsAdminFooterCell">|</div>
	<?php }?>
	<div class="ppsAdminFooterCell">
		<a target="_blank" href="http://worppsess.org/support/plugin/supsystic-secure"><?php _e('Support', PPS_LANG_CODE)?></a>
	</div>
	<div class="ppsAdminFooterCell">|</div>
	<div class="ppsAdminFooterCell">
		Add your <a target="_blank" href="http://worppsess.org/support/view/plugin-reviews/supsystic-secure?filter=5#postform">&#9733;&#9733;&#9733;&#9733;&#9733;</a> on worppsess.org.
	</div>
</div>