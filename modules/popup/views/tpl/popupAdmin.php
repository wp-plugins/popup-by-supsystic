<section>
	<div class="supsystic-item supsystic-panel">
		<div id="containerWrapper">
			<ul class="supsystic-bar-controls">
				<li title="<?php _e('Delete selected')?>">
					<button class="button" id="ppsPopupRemoveGroupBtn" disabled data-toolbar-button>
						<i class="fa fa-fw fa-trash-o"></i>
						<?php _e('Delete selected', PPS_LANG_CODE)?>
					</button>
				</li>
				<li title="<?php _e('Clear All')?>">
					<button class="button" id="ppsPopupClearBtn" disabled data-toolbar-button>
						<?php _e('Clear', PPS_LANG_CODE)?>
					</button>
				</li>
				<li title="<?php _e('Search', PPS_LANG_CODE)?>">
					<input id="ppsPopupTblSearchTxt" type="text" name="tbl_search" placeholder="<?php _e('Search', PPS_LANG_CODE)?>">
				</li>
			</ul>
			<div id="ppsPopupTblNavShell" class="supsystic-tbl-pagination-shell"></div>
			<div style="clear: both;"></div>
			<hr />
			<table id="ppsPopupTbl"></table>
			<div id="ppsPopupTblNav"></div>
			<div id="ppsPopupTblEmptyMsg" style="display: none;">
				<h3><?php _e('No data found', PPS_LANG_CODE)?></h3>
			</div>
		</div>
		<div style="clear: both;"></div>
	</div>
</section>