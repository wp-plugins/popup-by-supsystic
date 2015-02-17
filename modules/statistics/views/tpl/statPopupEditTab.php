<?php if($this->haveData) { ?>
	<span class="ppsOptLabel"><?php _e('Main PopUp Usage Statistics', PPS_LANG_CODE)?></span>
	<hr>
	<div style="clear: both;"></div>
	<a href="#" class="button ppsPopupStatChartTypeBtn" data-type="line">
		<i class="fa fa-line-chart"></i>
	</a>
	<a href="#" class="button ppsPopupStatChartTypeBtn" data-type="bar">
		<i class="fa fa-bar-chart"></i>
	</a>
	<a href="#" id="ppsPopupStatClear" data-id="<?php echo $this->popup['id']?>" class="button" style="float: right;">
		<i class="fa fa-trash"></i>
		<?php _e('Clear data', PPS_LANG_CODE)?>
	</a>
	<div style="clear: both;"></div>
	<div id="ppsPopupStatGraph"></div>
	<div style="clear: both; padding-bottom: 20px;"></div>
	<div class="supsistic-half-side-box">
		<span class="ppsOptLabel"><?php _e('Ratio of All Actions', PPS_LANG_CODE)?></span>
		<hr>
		<div style="clear: both;"></div>
		<div id="ppsPopupStatAllActionsPie"></div>
		<div id="ppsPopupStatAllActionsNoData" style="display: none;" class="description">
			<?php _e('Once you will have enought different statistics - like shares, subscribes, likes, - you will be able to see here - what action is used more often, and what - not.', PPS_LANG_CODE)?>
		</div>
	</div>
	<div class="supsistic-half-side-box">
		<span class="ppsOptLabel"><?php _e('Ratio of All Social Share', PPS_LANG_CODE)?></span>
		<hr>
		<div style="clear: both;"></div>
		<div id="ppsPopupStatAllSharePie"></div>
		<div id="ppsPopupStatAllShareNoData" style="display: none;" class="description">
			<?php _e('Once you will have enought different statistics about share from PopUp on social media - you will be able to see here - what social is is used more often, and what - not.', PPS_LANG_CODE)?>
		</div>
	</div>
<?php } else { ?>
	<h4><?php printf(__('You have no statistics for "%s" PopUp for now. Setup it\'s options - and wait until users will view it on your site.', PPS_LANG_CODE), $this->popup['params']['tpl']['label'])?></h4>
<?php }?>
