<table class="form-table" style="width: auto;">
	<?php if(in_array($this->popup['type'], array(PPS_VIDEO))) {?>
	<tr>
		<th scope="row" class="col-w-1perc">
			<?php _e('Video URL', PPS_LANG_CODE)?>&nbsp;
			<i class="fa fa-question supsystic-tooltip" title="<?php _e('Copy and paste here URL of your video source', PPS_LANG_CODE)?>"></i>
		</th>
		<td class="col-w-1perc">
			<?php echo htmlPps::text('params[tpl][video_url]', array('value' => $this->popup['params']['tpl']['video_url'], 'attrs' => 'style="width: 100%;"'))?>
		</td>
	</tr>
	<?php }?>
	<tr>
		<th scope="row" class="col-w-1perc">
			<?php _e('Width', PPS_LANG_CODE)?>
		</th>
		<td class="col-w-1perc">
			<?php echo htmlPps::text('params[tpl][width]', array('value' => $this->popup['params']['tpl']['width']))?>
		</td>
		<td class="col-w-1perc" colspan="3">
			<?php if(in_array($this->popup['type'], array(PPS_COMMON))) {?>
			<label style="margin-right: 10px;" class="supsystic-tooltip" title="<?php _e('Max width for percentage - is 100', PPS_LANG_CODE)?>">
				<?php echo htmlPps::radiobutton('params[tpl][width_measure]', array('value' => '%', 'checked' => htmlPps::checkedOpt($this->popup['params']['tpl'], 'width_measure', '%')))?>
				<?php _e('Percents', PPS_LANG_CODE)?>
			</label>
			<label>
				<?php echo htmlPps::radiobutton('params[tpl][width_measure]', array('value' => 'px', 'checked' => htmlPps::checkedOpt($this->popup['params']['tpl'], 'width_measure', 'px')))?>
				<?php _e('Pixels', PPS_LANG_CODE)?>
			</label>
			<?php } else {
				echo htmlPps::hidden('params[tpl][width_measure]', array('value' => 'px'));
			}?>
		</td>
	</tr>
	<?php if(in_array($this->popup['type'], array(PPS_FB_LIKE, PPS_VIDEO))) {?>
	<tr>
		<th scope="row" class="col-w-1perc">
			<?php _e('Height', PPS_LANG_CODE)?>
		</th>
		<td class="col-w-1perc">
			<?php echo htmlPps::text('params[tpl][height]', array('value' => $this->popup['params']['tpl']['height']))?>
		</td>
		<td class="col-w-1perc" colspan="3">
			<?php echo htmlPps::hidden('params[tpl][height_measure]', array('value' => 'px')); ?>
		</td>
	</tr>
	<?php if(in_array($this->popup['type'], array(PPS_VIDEO))) {?>
	<tr>
		<th scope="row" class="col-w-1perc">
			<?php _e('Video Autoplay', PPS_LANG_CODE)?>&nbsp;
			<i class="fa fa-question supsystic-tooltip" title="<?php _e('Play video - right after PopUp show', PPS_LANG_CODE)?>"></i>
		</th>
		<td class="col-w-1perc">
			<?php echo htmlPps::checkbox('params[tpl][video_autoplay]', array(
				'checked' => htmlPps::checkedOpt($this->popup['params']['tpl'], 'video_autoplay')
			))?>
		</td>
	</tr>
	<tr>
		<th scope="row" class="col-w-1perc">
			<?php _e('Hide controls', PPS_LANG_CODE)?>&nbsp;
			<i class="fa fa-question supsystic-tooltip" title="<?php _e('Hide standard video player controls', PPS_LANG_CODE)?>"></i>
		</th>
		<td class="col-w-1perc">
			<?php echo htmlPps::checkbox('params[tpl][vide_hide_controls]', array(
				'checked' => htmlPps::checkedOpt($this->popup['params']['tpl'], 'vide_hide_controls')
			))?>
		</td>
	</tr>
	<?php }?>
	
		<?php if(in_array($this->popup['type'], array(PPS_FB_LIKE))) {?>
			<?php foreach($this->fbLikeOpts as $fKey => $fData) { ?>
				<?php 
					$html = $fData['html'];
					$htmlParams = array();
					if($html == 'selectbox') {
						$htmlParams['options'] = $fData['options'];
					}
					if($html == 'checkbox') {
						$htmlParams['checked'] = htmlPps::checkedOpt($this->popup['params']['tpl']['fb_like_opts'], $fKey);
					} else {
						$htmlParams['value'] = $this->popup['params']['tpl']['fb_like_opts'][ $fKey ];
					}
					if($fKey == 'href') {
						$htmlParams['attrs'] = 'style="width: 100%"';
					}
				?>
				<tr>
					<th scope="row" class="col-w-1perc">
						<?php echo $fData['label']?>&nbsp;
						<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html($fData['desc'])?>"></i>
					</th>
					<td class="col-w-1perc" colspan="4">
						<?php echo htmlPps::$html('params[tpl][fb_like_opts]['. $fKey. ']', $htmlParams)?>
					</td>
				</tr>
			<?php }?>
		<?php }?>
	<?php }?>
	<tr>
		<th scope="row" class="col-w-1perc">
			<?php _e('Background overlay opacity', PPS_LANG_CODE)?>
		</th>
		<td class="col-w-1perc" colspan="4">
			<?php echo htmlPps::slider('params[tpl][bg_overlay_opacity]', array('value' => $this->popup['params']['tpl']['bg_overlay_opacity'], 'min' => 0, 'max' => 1, 'step' => 0.1))?>
		</td>
	</tr>
<?php for($i = 0; $i < $this->popup['params']['opts_attrs']['bg_number']; $i++) { ?>
	<tr>
		<th scope="row" class="col-w-1perc">
			<?php $this->popup['params']['opts_attrs']['bg_number'] == 1 ? _e('Background', PPS_LANG_CODE) : printf(__('Background %d', PPS_LANG_CODE), $i + 1)?>
		</th>
		<td class="col-w-1perc">
			<?php echo htmlPps::selectbox('params[tpl][bg_type_'. $i. ']', array('options' => $this->bgTypes, 'value' => $this->popup['params']['tpl']['bg_type_'. $i], 'attrs' => 'data-iter="'. $i. '" class="ppsBgTypeSelect"'))?>
		</td>
		<td class="col-w-1perc ppsBgTypeShell ppsBgTypeShell_<?php echo $i?> ppsBgTypeImgShell_<?php echo $i?>">
			<?php echo htmlPps::imgGalleryBtn('params[tpl][bg_img_'. $i. ']', array('onChange' => 'ppsShowImgPrev', 'attrs' => 'data-iter="'. $i. '" class="button button-sup-small"', 'value' => $this->popup['params']['tpl']['bg_img_'. $i]))?>
		</td>
		<td class="col-w-1perc ppsBgTypeShell ppsBgTypeShell_<?php echo $i?> ppsBgTypeImgShell_<?php echo $i?>" style="padding-top: 10px; min-width: 100px;">
			<img src="" style="max-width: 500px;" class="ppsBgImgPrev_<?php echo $i?>" />
		</td>
		<td class="col-w-1perc ppsBgTypeShell ppsBgTypeShell_<?php echo $i?> ppsBgTypeColorShell_<?php echo $i?>" style="line-height: 40px;">
			<?php echo htmlPps::colorpicker('params[tpl][bg_color_'. $i. ']', array('value' => $this->popup['params']['tpl']['bg_color_'. $i]))?>
		</td>
	</tr>
<?php }?>
<tr>
	<th scope="row" class="col-w-1perc">
		<?php _e('Close button', PPS_LANG_CODE)?>
	</th>
	<td colspan="4">
		<ul id="ppsPopupCloseBtnList" class="ppsListItems">
			<?php foreach($this->closeBtns as $key => $data) { ?>
				<li data-key="<?php echo $key?>">
					<?php if(isset($data['img_url'])) {?>
						<img src="<?php echo $data['img_url']?>" />
					<?php } elseif(isset($data['label'])) {
						echo $data['label'];
					}?>
				</li>
			<?php }?>
		</ul>
		<?php echo htmlPps::hidden('params[tpl][close_btn]')?>
	</td>
</tr>
<?php if(in_array($this->popup['type'], array(PPS_COMMON))) {?>
<tr>
	<th scope="row" class="col-w-1perc">
		<?php _e('Bullets', PPS_LANG_CODE)?>
	</th>
	<td colspan="4">
		<ul id="ppsPopupBulletsList" class="ppsListItems">
			<?php foreach($this->bullets as $key => $data) { ?>
				<li data-key="<?php echo $key?>">
					<?php if(isset($data['img_url'])) {?>
						<img src="<?php echo $data['img_url']?>" />
					<?php } elseif(isset($data['label'])) {
						echo $data['label'];
					}?>
				</li>
			<?php }?>
		</ul>
		<?php echo htmlPps::hidden('params[tpl][bullets]')?>
	</td>
</tr>
<?php }?>
</table>