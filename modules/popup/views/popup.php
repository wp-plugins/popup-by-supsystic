<?php
class popupViewPps extends viewPps {
	protected $_twig;
	private $_closeBtns = array();
	private $_bullets = array();
	private $_animationList = array();
	public function getTabContent() {
		framePps::_()->getModule('templates')->loadJqGrid();
		framePps::_()->addScript('admin.popup', $this->getModule()->getModPath(). 'js/admin.popup.js');
		framePps::_()->addScript('admin.popup.list', $this->getModule()->getModPath(). 'js/admin.popup.list.js');
		framePps::_()->addJSVar('admin.popup.list', 'ppsTblDataUrl', uriPps::mod('popup', 'getListForTbl', array('reqType' => 'ajax')));
		
		$this->assign('addNewLink', framePps::_()->getModule('options')->getTabUrl('popup_add_new'));
		return parent::getContent('popupAdmin');
	}
	public function getAddNewTabContent() {
		framePps::_()->getModule('templates')->loadJqueryUi();
		framePps::_()->addStyle('admin.popup', $this->getModule()->getModPath(). 'css/admin.popup.css');
		framePps::_()->addScript('admin.popup', $this->getModule()->getModPath(). 'js/admin.popup.js');
		framePps::_()->addStyle('magic.min', PPS_CSS_PATH. 'magic.min.css');
		
		$changeFor = (int) reqPps::getVar('change_for', 'get');
		//framePps::_()->addJSVar('admin.popup', 'ppsChangeFor', array($changeFor));
		if($changeFor) {
			$originalPopup = $this->getModel()->getById( $changeFor );
			$editLink = $this->getModule()->getEditLink( $changeFor );
			$this->assign('originalPopup', $originalPopup);
			$this->assign('editLink', $editLink);
			framePps::_()->addJSVar('admin.popup', 'ppsOriginalPopup', $originalPopup);
			dispatcherPps::addFilter('mainBreadcrumbs', array($this, 'modifyBreadcrumbsForChangeTpl'));
		}
		$this->assign('types', $this->getModel()->getTypes());
		$this->assign('list', $this->getModel()->getSimpleList(array('active' => 1, 'original_id' => 0)));
		$this->assign('changeFor', $changeFor);
		
		return parent::getContent('popupAddNewAdmin');
	}
	public function modifyBreadcrumbsForChangeTpl($crumbs) {
		$crumbs[ count($crumbs) - 1 ]['label'] = __('Modify PopUp Template', PPS_LANG_CODE);
		return $crumbs;
	}
	public function adminBreadcrumbsClassAdd() {
		echo ' supsystic-sticky';
	}
	public function getEditTabContent($id) {
		global $wpdb;
		$popup = $this->getModel()->getById($id);
		if(empty($popup)) {
			return __('Can not find required PopUp', PPS_LANG_CODE);
		}
		dispatcherPps::doAction('beforePopupEdit', $popup);
		
		dispatcherPps::addAction('afterAdminBreadcrumbs', array($this, 'showEditPopupFormControls'));
		dispatcherPps::addAction('adminBreadcrumbsClassAdd', array($this, 'adminBreadcrumbsClassAdd'));
		
		$useCommonTabs = in_array($popup['type'], array(PPS_COMMON, PPS_VIDEO));
		// !remove this!!!!
		/*$popup['params']['opts_attrs'] = array(
			'bg_number' => 2,
			'txt_block_number' => 1,
			'video_height_as_popup' => 1,
		);*/
		/*$popup['params']['opts_attrs']['txt_block_number'] = 0;
		$popup['params']['opts_attrs']['video_width_as_popup'] = 1;
		$popup['params']['opts_attrs']['video_height_as_popup'] = 1;*/
		// !remove this!!!!
		if(!is_array($popup['params']))
			$popup['params'] = array();
		
		framePps::_()->getModule('templates')->loadJqueryUi();
		
		framePps::_()->addStyle('ppsCodemirror', PPS_CSS_PATH. 'codemirror.css');
		framePps::_()->addStyle('codemirror-addon-hint', PPS_JS_PATH. 'codemirror/addon/hint/show-hint.css');
		framePps::_()->addScript('ppsCodemirror', PPS_JS_PATH. 'codemirror/codemirror.js');
		framePps::_()->addScript('codemirror-addon-show-hint', PPS_JS_PATH. 'codemirror/addon/hint/show-hint.js');
		framePps::_()->addScript('codemirror-addon-xml-hint', PPS_JS_PATH. 'codemirror/addon/hint/xml-hint.js');
		framePps::_()->addScript('codemirror-addon-html-hint', PPS_JS_PATH. 'codemirror/addon/hint/html-hint.js');
		framePps::_()->addScript('codemirror-mode-xml', PPS_JS_PATH. 'codemirror/mode/xml/xml.js');
		framePps::_()->addScript('codemirror-mode-javascript', PPS_JS_PATH. 'codemirror/mode/javascript/javascript.js');
		framePps::_()->addScript('codemirror-mode-css', PPS_JS_PATH. 'codemirror/mode/css/css.js');
		framePps::_()->addScript('codemirror-mode-htmlmixed', PPS_JS_PATH. 'codemirror/mode/htmlmixed/htmlmixed.js');
		
		$ppsAddNewUrl = framePps::_()->getModule('options')->getTabUrl('popup_add_new');
		framePps::_()->addStyle('admin.popup', $this->getModule()->getModPath(). 'css/admin.popup.css');
		framePps::_()->addScript('admin.popup', $this->getModule()->getModPath(). 'js/admin.popup.js');
		framePps::_()->addScript('admin.popup.edit', $this->getModule()->getModPath(). 'js/admin.popup.edit.js');
		framePps::_()->addJSVar('admin.popup.edit', 'ppsPopup', $popup);
		framePps::_()->addJSVar('admin.popup.edit', 'ppsAddNewUrl', $ppsAddNewUrl);
		
		framePps::_()->addScript('wp.tabs', PPS_JS_PATH. 'wp.tabs.js');
		
		$bgType = array(
			'none' => __('None', PPS_LANG_CODE),
			'img' => __('Image', PPS_LANG_CODE),
			'color' => __('Color', PPS_LANG_CODE),
		);
		
		$hideForList = array(
			'mobile' => __('Mobile', PPS_LANG_CODE),
			'tablet' => __('Tablet', PPS_LANG_CODE),
			'desktop' => __('Desktop PC', PPS_LANG_CODE),
		);

		$subDestList = framePps::_()->getModule('subscribe')->getDestList();
		$subDestListForSelect = array();
		foreach($subDestList as $key => $data) {
			$subDestListForSelect[ $key ] = $data['label'];
		}
		// We are not using wp methods here - as list can be very large - and it can take too much memory
		$allPages = dbPps::get("SELECT ID, post_title FROM $wpdb->posts WHERE post_type IN ('page', 'post') AND post_status IN ('publish','draft') ORDER BY post_title");
		$allPagesForSelect = array();
		if(!empty($allPages)) {
			foreach($allPages as $p) {
				$allPagesForSelect[ $p['ID'] ] = $p['post_title'];
			}
		}
		$selectedShowPages = array();
		$selectedHidePages = array();
		if(isset($popup['show_pages_list']) && !empty($popup['show_pages_list'])) {
			foreach($popup['show_pages_list'] as $showPage) {
				if($showPage['not_show']) {
					$selectedHidePages[] = $showPage['post_id'];
				} else {
					$selectedShowPages[] = $showPage['post_id'];
				}
			}
		}
		if(in_array($popup['type'], array(PPS_FB_LIKE))) {
			$this->assign('fbLikeOpts', $this->getFbLikeOpts());
		}
		$this->assign('ppsAddNewUrl', $ppsAddNewUrl);
		$this->assign('bgTypes', $bgType);
		$this->assign('previewUrl', uriPps::mod('popup', 'getPreviewHtml', array('id' => $id)));
		$this->assign('popup', $popup);
		
		$this->assign('closeBtns', $this->getCloseBtns());
		$this->assign('bullets', $this->getBullets());
		$this->assign('subDestListForSelect', $subDestListForSelect);
		
		$this->assign('allPagesForSelect', $allPagesForSelect);
		$this->assign('selectedShowPages', $selectedShowPages);
		$this->assign('selectedHidePages', $selectedHidePages);
		
		$this->assign('smLinks', framePps::_()->getModule('sm')->getAvailableLinks());
		$this->assign('smDesigns', framePps::_()->getModule('sm')->getAvailableDesigns());
		
		$this->assign('hideForList', $hideForList);
		$designTabs = array(	// Used in $this->getMainPopupTplTab()
			'ppsPopupDesign' => array(
				'title' => __('Appearance', PPS_LANG_CODE), 
				'content' => $this->getMainPopupDesignTab(),
				'fa_icon' => 'fa-picture-o',
				'sort_order' => 0),
			'ppsPopupAnimation' => array(
				'title' => __('Animation', PPS_LANG_CODE), 
				'content' => $this->getMainPopupAnimationTab(),
				'fa_icon' => 'fa-cog fa-spin',
				'sort_order' => 50),
		);
		if($useCommonTabs) {
			$designTabs['ppsPopupSubscribe'] = array(
				'title' => __('Subscribe', PPS_LANG_CODE), 
				'content' => $this->getMainPopupSubTab(),
				'fa_icon' => 'fa-users',
				'sort_order' => 30);
			$designTabs['ppsPopupSm'] = array(
				'title' => __('Social', PPS_LANG_CODE), 
				'content' => $this->getMainPopupSmTab(),
				'fa_icon' => 'fa-thumbs-o-up',
				'sort_order' => 40);
		}
		$designTabs = dispatcherPps::applyFilters('popupEditDesignTabs', $designTabs, $popup);
		uasort($designTabs, array($this, 'sortEditPopupTabsClb'));
		$this->assign('designTabs', $designTabs);
		
		$tabs = array(
			'ppsPopupMainOpts' => array(
				'title' => __('Main', PPS_LANG_CODE), 
				'content' => $this->getMainPopupOptsTab(),
				'fa_icon' => 'fa-tachometer',
				'sort_order' => 0),
			'ppsPopupTpl' => array(
				'title' => __('Design', PPS_LANG_CODE), 
				'content' => $this->getMainPopupTplTab(),
				'fa_icon' => 'fa-picture-o',
				'sort_order' => 10),
			'ppsPopupEditors' => array(
				'title' => __('Code', PPS_LANG_CODE), 
				'content' => $this->getMainPopupCodeTab(),
				'fa_icon' => 'fa-code',
				'sort_order' => 999),
		);
		if($useCommonTabs) {
			$tabs['ppsPopupTexts'] = array(
				'title' => __('Texts', PPS_LANG_CODE), 
				'content' => $this->getMainPopupTextsTab(),
				'fa_icon' => 'fa-pencil-square-o',
				'sort_order' => 20);
		}
		$tabs = dispatcherPps::applyFilters('popupEditTabs', $tabs, $popup);
		uasort($tabs, array($this, 'sortEditPopupTabsClb'));
		$this->assign('tabs', $tabs);
		return parent::getContent('popupEditAdmin');
	}
	
	public function showEditPopupFormControls() {
		parent::display('popupEditFormControls');
	}
	public function sortEditPopupTabsClb($a, $b) {
		if($a['sort_order'] > $b['sort_order'])
			return 1;
		if($a['sort_order'] < $b['sort_order'])
			return -1;
		return 0;
	}
	public function getFbLikeOpts() {
		return array(
			'href' => array(
				'label' => __('Facebook page URL', PPS_LANG_CODE), 
				'html' => 'text', 
				'desc' => __('The absolute URL of the Facebook Page that will be liked. This is a required setting.', PPS_LANG_CODE)),
			'colorscheme' => array(
				'label' => __('Color scheme', PPS_LANG_CODE), 
				'html' => 'selectbox', 
				'options' => array('light' => __('Light', PPS_LANG_CODE), 'dark' => __('Dark', PPS_LANG_CODE)),
				'desc' => __('The color scheme used by the plugin. Can be "light" or "dark".', PPS_LANG_CODE)),
			'force_wall' => array(
				'label' => __('Force wall', PPS_LANG_CODE), 
				'html' => 'checkbox', 
				'desc' => __('For "place" Pages (Pages that have a physical location that can be used with check-ins), this specifies whether the stream contains posts by the Page or just check-ins from friends.', PPS_LANG_CODE)),
			'header' => array(
				'label' => __('Header', PPS_LANG_CODE), 
				'html' => 'checkbox', 
				'desc' => __('Specifies whether to display the Facebook header at the top of the plugin.', PPS_LANG_CODE)),
			'show_border' => array(
				'label' => __('Show border', PPS_LANG_CODE), 
				'html' => 'checkbox', 
				'desc' => __('Specifies whether or not to show a border around the plugin.', PPS_LANG_CODE)),
			'show_faces' => array(
				'label' => __('Show faces', PPS_LANG_CODE), 
				'html' => 'checkbox', 
				'desc' => __('Specifies whether to display profile photos of people who like the page.', PPS_LANG_CODE)),
			'stream' => array(
				'label' => __('Stream', PPS_LANG_CODE), 
				'html' => 'checkbox', 
				'desc' => __('Specifies whether to display a stream of the latest posts by the Page.', PPS_LANG_CODE)),
		);
	}
	public function getMainPopupDesignTab() {
		return parent::getContent('popupEditAdminDesignOpts');
	}
	public function getMainPopupOptsTab() {
		return parent::getContent('popupEditAdminMainOpts');
	}
	public function getMainPopupTplTab() {
		return parent::getContent('popupEditAdminTplOpts');
	}
	public function getMainPopupTextsTab() {
		return parent::getContent('popupEditAdminTextsOpts');
	}
	public function getMainPopupSubTab() {
		framePps::_()->getModule('subscribe')->loadAdminEditAssets();
		$this->assign('availableUserRoles', framePps::_()->getModule('subscribe')->getAvailableUserRolesForSelect());
		return parent::getContent('popupEditAdminSubOpts');
	}
	public function getMainPopupSmTab() {
		return parent::getContent('popupEditAdminSmOpts');
	}
	public function getMainPopupCodeTab() {
		return parent::getContent('popupEditAdminCodeOpts');
	}
	public function getMainPopupAnimationTab() {
		framePps::_()->addStyle('magic.min', PPS_CSS_PATH. 'magic.min.css');
		$this->assign('animationList', $this->getAnimationList());
		return parent::getContent('popupEditAdminAnimationOpts');
	}
	public function getAnimationList() {
		if(empty($this->_animationList)) {
			$this->_animationList = array(
				'none' => array('label' => __('None', PPS_LANG_CODE)),
				'puff' => array('label' => __('Puff', PPS_LANG_CODE), 'show_class' => 'puffIn', 'hide_class' => 'puffOut'),
				'vanish' => array('label' => __('Vanish', PPS_LANG_CODE), 'show_class' => 'vanishIn', 'hide_class' => 'vanishOut'),
				
				'open_down_left' => array('label' => __('Open down left', PPS_LANG_CODE), 'show_class' => 'openDownLeftRetourn', 'hide_class' => 'openDownLeft'),
				'open_down_right' => array('label' => __('Open down right', PPS_LANG_CODE), 'show_class' => 'openDownRightRetourn', 'hide_class' => 'openDownRight'),
				
				'perspective_down' => array('label' => __('Perspective down', PPS_LANG_CODE), 'show_class' => 'perspectiveDownRetourn', 'hide_class' => 'perspectiveDown'),
				'perspective_up' => array('label' => __('Perspective up', PPS_LANG_CODE), 'show_class' => 'perspectiveUpRetourn', 'hide_class' => 'perspectiveUp'),
				
				'slide_down' => array('label' => __('Slide down', PPS_LANG_CODE), 'show_class' => 'slideDownRetourn', 'hide_class' => 'slideDown'),
				'slide_up' => array('label' => __('Slide up', PPS_LANG_CODE), 'show_class' => 'slideUpRetourn', 'hide_class' => 'slideUp'),
				
				'swash' => array('label' => __('Swash', PPS_LANG_CODE), 'show_class' => 'swashIn', 'hide_class' => 'swashOut'),
				'foolis' => array('label' => __('Foolis', PPS_LANG_CODE), 'show_class' => 'foolishIn', 'hide_class' => 'foolishOut'),
				
				'tin_right' => array('label' => __('Tin right', PPS_LANG_CODE), 'show_class' => 'tinRightIn', 'hide_class' => 'tinRightOut'),
				'tin_left' => array('label' => __('Tin left', PPS_LANG_CODE), 'show_class' => 'tinLeftIn', 'hide_class' => 'tinLeftOut'),
				'tin_up' => array('label' => __('Tin up', PPS_LANG_CODE), 'show_class' => 'tinUpIn', 'hide_class' => 'tinUpOut'),
				'tin_down' => array('label' => __('Tin down', PPS_LANG_CODE), 'show_class' => 'tinDownIn', 'hide_class' => 'tinDownOut'),
				
				'boing' => array('label' => __('Boing', PPS_LANG_CODE), 'show_class' => 'boingInUp', 'hide_class' => 'boingOutDown'),
				
				'space_right' => array('label' => __('Space right', PPS_LANG_CODE), 'show_class' => 'spaceInRight', 'hide_class' => 'spaceOutRight'),
				'space_left' => array('label' => __('Space left', PPS_LANG_CODE), 'show_class' => 'spaceInLeft', 'hide_class' => 'spaceOutLeft'),
				'space_up' => array('label' => __('Space up', PPS_LANG_CODE), 'show_class' => 'spaceInUp', 'hide_class' => 'spaceOutUp'),
				'space_down' => array('label' => __('Space down', PPS_LANG_CODE), 'show_class' => 'spaceInDown', 'hide_class' => 'spaceOutDown'),
			);
		}
		return $this->_animationList;
	}
	public function getAnimationByKey($key) {
		$this->getAnimationList();
		return isset($this->_animationList[ $key ]) ? $this->_animationList[ $key ] : false;
	}
	public function adjustBrightness($hex, $steps) {
		 // Steps should be between -255 and 255. Negative = darker, positive = lighter
		$steps = max(-255, min(255, $steps));

		// Normalize into a six character long hex string
		$hex = str_replace('#', '', $hex);
		if (strlen($hex) == 3) {
			$hex = str_repeat(substr($hex, 0, 1), 2). str_repeat(substr($hex, 1, 1), 2). str_repeat(substr($hex, 2, 1), 2);
		}

		// Split into three parts: R, G and B
		$color_parts = str_split($hex, 2);
		$return = '#';

		foreach ($color_parts as $color) {
			$color   = hexdec($color); // Convert to decimal
			$color   = max(0, min(255, $color + $steps)); // Adjust color
			$return .= str_pad(dechex($color), 2, '0', STR_PAD_LEFT); // Make two char hex code
		}

		return $return;
	}
	private function _generateCloseBtnCss($popup) {
		if(isset($popup['params']['tpl']['close_btn']) 
			&& !empty($popup['params']['tpl']['close_btn']) 
			&& $popup['params']['tpl']['close_btn'] !== 'none'
		) {
			$this->getCloseBtns();
			$btn = $this->_closeBtns[ $popup['params']['tpl']['close_btn'] ];
			$styles = array(
				'position' => 'absolute',
				'background-image' => 'url("'. $btn['img_url']. '")',
				'background-repeat' => 'no-repeat'
			);
			if(isset($btn['add_style']))
				$styles = array_merge($styles, $btn['add_style']);
			return '#ppsPopupShell_'. $popup['view_id']. ' .ppsPopupClose { '. utilsPps::arrToCss($styles). ' }';
		} else {
			return '#ppsPopupShell_'. $popup['view_id']. ' .ppsPopupClose { display: none; }';
		}
	}
	private function _generateBulletsCss($popup) {
		if(isset($popup['params']['tpl']['bullets']) 
			&& !empty($popup['params']['tpl']['bullets']) 
			&& $popup['params']['tpl']['bullets'] !== 'none'
		) {
			$this->getBullets();
			$bullets = $this->_bullets[ $popup['params']['tpl']['bullets'] ];
			$styles = array(
				'background-image' => 'url("'. $bullets['img_url']. '");'
			);
			if(isset($bullets['add_style']))
				$styles = array_merge($styles, $bullets['add_style']);
			return '#ppsPopupShell_'. $popup['view_id']. ' ul li { '. utilsPps::arrToCss($styles). ' }';
		} else {
			return '';	// Just use default bullets styles
		}
	}
	private function _generateVideoHtml($popup) {
		$res = '';
		if(isset($popup['params']['tpl']['video_url']) && !empty($popup['params']['tpl']['video_url'])) {
			add_filter('oembed_result', array($this,'modifyEmbRes'), 10, 3);
			$attrs = array();
			if(isset($popup['params']['opts_attrs']['video_width_as_popup']) && $popup['params']['opts_attrs']['video_width_as_popup']) {
				$attrs['width'] = $popup['params']['tpl']['width'];
			}
			if(isset($popup['params']['opts_attrs']['video_height_as_popup']) && $popup['params']['opts_attrs']['video_height_as_popup']) {
				$attrs['height'] = $popup['params']['tpl']['height'];
			}
			if(isset($popup['params']['tpl']['video_autoplay']) && $popup['params']['tpl']['video_autoplay']) {
				$attrs['autoplay'] = 1;
			}
			if(isset($popup['params']['tpl']['vide_hide_controls']) && $popup['params']['tpl']['vide_hide_controls']) {
				$attrs['vide_hide_controls'] = 1;
			}
			$res = wp_oembed_get($popup['params']['tpl']['video_url'], $attrs);
		}
		return $res;
	}
	public function modifyEmbRes($html, $url, $attrs) {
		if(isset($attrs['autoplay']) && $attrs['autoplay']) {
			preg_match('/\<iframe.+src\=\"(?<SRC>.+)\"/iUs', $html, $matches);
			if($matches && isset($matches['SRC']) && !empty($matches['SRC'])) {
				$newSrc = $matches['SRC']. (strpos($matches['SRC'], '?') ? '&' : '?'). 'enablejsapi=1';
				$html = str_replace($matches['SRC'], $newSrc, $html);
			}
		}
		if(isset($attrs['vide_hide_controls']) && $attrs['vide_hide_controls']) {
			preg_match('/\<iframe.+src\=\"(?<SRC>.+)\"/iUs', $html, $matches);
			if($matches && isset($matches['SRC']) && !empty($matches['SRC'])) {
				$newSrc = $matches['SRC']. (strpos($matches['SRC'], '?') ? '&' : '?'). 'controls=0';
				$html = str_replace($matches['SRC'], $newSrc, $html);
			}
		}		
		return $html;
	}
	public function generateHtml($popup) {
		if(is_numeric($popup)) {
			$popup = $this->getModel()->getById($popup);
		}
		$this->_initTwig();
		
		$popup['css'] .= $this->_generateCloseBtnCss( $popup );
		$popup['css'] .= $this->_generateBulletsCss( $popup );
		if(isset($popup['params']['tpl']['enb_subscribe']) && !empty($popup['params']['tpl']['enb_subscribe'])) {
			$popup['params']['tpl']['sub_form_start'] = framePps::_()->getModule('subscribe')->generateFormStart( $popup );
			$popup['params']['tpl']['sub_form_end'] = framePps::_()->getModule('subscribe')->generateFormEnd( $popup );
		}
		
		if(isset($popup['params']['tpl']['enb_sm']) && !empty($popup['params']['tpl']['enb_sm'])) {
			$popup['params']['tpl']['sm_html'] = framePps::_()->getModule('sm')->generateHtml( $popup );
			$popup['css'] .= framePps::_()->getModule('sm')->generateCss( $popup );
		}
		if(in_array($popup['type'], array(PPS_FB_LIKE))) {
			$popup['params']['tpl']['fb_like_widget_html'] = $this->_generateFbLikeWidget( $popup );
		}
		if(in_array($popup['type'], array(PPS_VIDEO))) {
			$popup['params']['tpl']['video_html'] = $this->_generateVideoHtml( $popup );
		}
		$popup['css'] = $this->_replaceTagsWithTwig( $popup['css'], $popup );
		$popup['html'] = $this->_replaceTagsWithTwig( $popup['html'], $popup );
		return $this->_twig->render(
				'<style type="text/css">'. $popup['css']. '</style>'. $popup['html'],
			array('popup' => $popup)
		);
	}
	private function _generateFbLikeWidget($popup) {
		$res = '';
		$res .= '<div id="fb-root"></div>
		<script>(function(d, s, id) {
		  var js, fjs = d.getElementsByTagName(s)[0];
		  if (d.getElementById(id)) return;
		  js = d.createElement(s); js.id = id;
		  js.src = "//connect.facebook.net/'. PPS_WPLANG. '/sdk.js#xfbml=1&version=v2.0";
		  fjs.parentNode.insertBefore(js, fjs);
		}(document, \'script\', \'facebook-jssdk\'));</script>';
		$res .= '<div class="fb-like-box"';
		$fbLikeOpts = $this->getFbLikeOpts();
		foreach($fbLikeOpts as $fKey => $fData) {
			$dataKey = 'data-'. str_replace('_', '-', $fKey);
			$value = '';
			if($fData['html'] == 'checkbox') {
				$value = isset($popup['params']['tpl']['fb_like_opts'][ $fKey ]) && $popup['params']['tpl']['fb_like_opts'][ $fKey ]
					? 'true'
					: 'false';
			} else {
				$value = $popup['params']['tpl']['fb_like_opts'][ $fKey ];
			}
			$res .= ' '. $dataKey.'="'. $value. '"';
		}
		if(isset($popup['params']['tpl']['width']) && !empty($popup['params']['tpl']['width'])) {
			$res .= ' data-width="'. $popup['params']['tpl']['width']. '"';
		}
		if(isset($popup['params']['tpl']['height']) && !empty($popup['params']['tpl']['height'])) {
			$res .= ' data-height="'. $popup['params']['tpl']['height']. '"';
		}
		$res .= '></div>';
		return $res;
	}
	private function _replaceTagsWithTwig($string, $popup) {
		$string = preg_replace('/\[if (.+)\]/iU', '{% if popup.params.tpl.$1 %}', $string);
		$string = preg_replace('/\[elseif (.+)\]/iU', '{% elseif popup.params.tpl.$1 %}', $string);
		
		$replaceFrom = array('ID', 'endif', 'else');
		$replaceTo = array($popup['view_id'], '{% endif %}', '{% else %}');
		if(isset($popup['params']) && isset($popup['params']['tpl'])) {
			foreach($popup['params']['tpl'] as $key => $val) {
				if(is_array($val)) {
					foreach($val as $key2 => $val2) {
						$replaceFrom[] = $key. '_'. $key2;
						$replaceTo[] = $val2;
					}
				} else {
					// Do shortcodes for all text type data in popup
					if(strpos($key, 'txt_') === 0 || strpos($key, 'label') === 0 || strpos($key, 'foot_note')) {
						$val = do_shortcode( $val );
					}
					$replaceFrom[] = $key;
					$replaceTo[] = $val;
				}
			}
		}
		foreach($replaceFrom as $i => $v) {
			$replaceFrom[ $i ] = '['. $v. ']';
		}
		return str_replace($replaceFrom, $replaceTo, $string);
		
	}
	public function getCloseBtns() {
		if(empty($this->_closeBtns)) {
			$this->_closeBtns = array(
				'none' => array('label' => __('None', PPS_LANG_CODE)),
				'classy_grey' => array('img' => 'classy_grey.png', 'add_style' => array('top' => '-16px', 'right' => '-16px', 'width' => '42px', 'height' => '42px')),
				'close-orange' => array('img' => 'close-orange.png', 'add_style' => array('top' => '-16px', 'right' => '-16px', 'width' => '42px', 'height' => '42px')),
				'close-red-in-circle' => array('img' => 'close-red-in-circle.png', 'add_style' => array('top' => '-16px', 'right' => '-16px', 'width' => '42px', 'height' => '42px')),
				'lists_black' => array('img' => 'lists_black.png', 'add_style' => array('top' => '-10px', 'right' => '-10px', 'width' => '25px', 'height' => '25px')),
				'while_close' => array('img' => 'while_close.png', 'add_style' => array('top' => '15px', 'right' => '15px', 'width' => '20px', 'height' => '19px')),
				'red_close' => array('img' => 'close-red.png', 'add_style' => array('top' => '15px', 'right' => '20px', 'width' => '25px', 'height' => '25px')),
				'yellow_close' => array('img' => 'close-yellow.png', 'add_style' => array('top' => '-16px', 'right' => '-16px', 'width' => '42px', 'height' => '42px')),
				'sqr_close' => array('img' => 'sqr-close.png', 'add_style' => array('top' => '25px', 'right' => '20px', 'width' => '25px', 'height' => '25px')),
			);
			foreach($this->_closeBtns as $key => $data) {
				if(isset($data['img'])) {
					if(!isset($data['img_url']))
						$this->_closeBtns[ $key ]['img_url'] = $this->getModule()->getModPath(). 'img/assets/close_btns/'. $data['img'];
				}
			}
		}
		return $this->_closeBtns;
	}
	public function getBullets() {
		if(empty($this->_bullets)) {
			$this->_bullets = array(
				'none' => array('label' => __('None (standard)', PPS_LANG_CODE)),
				'classy_blue' => array('img' => 'classy_blue.png', 'add_style' => array('list-style' => 'outside none none !important', 'background-repeat' => 'no-repeat', 'padding-left' => '30px', 'line-height' => '100%', 'height' => '38px')),
				'circle_green' => array('img' => 'circle_green.png', 'add_style' => array('list-style' => 'outside none none !important', 'background-repeat' => 'no-repeat', 'padding-left' => '30px', 'line-height' => '100%', 'height' => '30px')),
				'lists_green' => array('img' => 'lists_green.png', 'add_style' => array('list-style' => 'outside none none !important', 'background-repeat' => 'no-repeat', 'padding-left' => '30px', 'line-height' => '100%', 'height' => '38px')),
				'tick' => array('img' => 'tick.png', 'add_style' => array('list-style' => 'outside none none !important', 'background-repeat' => 'no-repeat', 'padding-left' => '30px', 'line-height' => '100%', 'height' => '30px')),
				'tick_blue' => array('img' => 'tick_blue.png', 'add_style' => array('list-style' => 'outside none none !important', 'background-repeat' => 'no-repeat', 'padding-left' => '30px', 'line-height' => '100%', 'height' => '30px')),
				'ticks' => array('img' => 'ticks.png', 'add_style' => array('list-style' => 'outside none none !important', 'background-repeat' => 'no-repeat', 'padding-left' => '30px', 'line-height' => '100%', 'height' => '30px')),
			);
			foreach($this->_bullets as $key => $data) {
				if(isset($data['img']) && !isset($data['img_url'])) {
					$this->_bullets[ $key ]['img_url'] = $this->getModule()->getModPath(). 'img/assets/bullets/'. $data['img'];
				}
			}
		}
		return $this->_bullets;
	}
	protected function _initTwig() {
		if(!$this->_twig) {
			require_once(PPS_CLASSES_DIR. 'Twig'. DS. 'Autoloader.php');
			Twig_Autoloader::register();
			$this->_twig = new Twig_Environment(new Twig_Loader_String(), array('debug' => 1));
			$this->_twig->addFunction(
				new Twig_SimpleFunction('adjust_brightness', array(
						$this,
						'adjustBrightness'
					)
				)
			);
		}
	}
}
