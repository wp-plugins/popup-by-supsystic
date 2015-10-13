<?php
class popupPps extends modulePps {
	private $_renderedIds = array();
	private $_addToFooterIds = array();

	private $_assetsUrl = '';
	private $_oldAssetsUrl = 'https://supsystic.com/_assets/popup/';

	public function init() {
		dispatcherPps::addFilter('mainAdminTabs', array($this, 'addAdminTab'));
		add_action('template_redirect', array($this, 'checkPopupShow'));
		add_shortcode(PPS_SHORTCODE_CLICK, array($this, 'showPopupOnClick'));
		add_action('wp_footer', array($this, 'collectFooterRender'), 0);
		add_filter('wp_nav_menu_objects', array($this, 'checkMenuItemsForPopUps'));
	}
	public function addAdminTab($tabs) {
		$tabs[ $this->getCode(). '_add_new' ] = array(
			'label' => __('Add New PopUp', PPS_LANG_CODE), 'callback' => array($this, 'getAddNewTabContent'), 'fa_icon' => 'fa-plus-circle', 'sort_order' => 10, 'add_bread' => $this->getCode(),
		);
		$tabs[ $this->getCode(). '_edit' ] = array(
			'label' => __('Edit', PPS_LANG_CODE), 'callback' => array($this, 'getEditTabContent'), 'sort_order' => 20, 'child_of' => $this->getCode(), 'hidden' => 1, 'add_bread' => $this->getCode(),
		);
		$tabs[ $this->getCode() ] = array(
			'label' => __('Show All PopUps', PPS_LANG_CODE), 'callback' => array($this, 'getTabContent'), 'fa_icon' => 'fa-list', 'sort_order' => 20, //'is_main' => true,
		);
		return $tabs;
	}
	public function getTabContent() {
		return $this->getView()->getTabContent();
	}
	public function getAddNewTabContent() {
		return $this->getView()->getAddNewTabContent();
	}
	public function getEditTabContent() {
		$id = (int) reqPps::getVar('id', 'get');
		return $this->getView()->getEditTabContent( $id );
	}
	public function getEditLink($id, $popupTab = '') {
		$link = framePps::_()->getModule('options')->getTabUrl( $this->getCode(). '_edit' );
		$link .= '&id='. $id;
		if(!empty($popupTab)) {
			$link .= '#'. $popupTab;
		}
		return $link;
	}
	public function checkPopupShow() {
		global $wp_query;
		$currentPageId = (int) get_the_ID();
		$isHome = is_home();
		/*show_pages = 1 -> All, 2 -> show on selected, 3 -> do not show on selected*/
		/*show_on = 1 -> Page load, 2 -> click on page, 3 -> click on certain element (shortcode)*/
		$condition = "original_id != 0 AND active = 1 AND (show_pages = 1";
		$havePostsListing = $wp_query && is_object($wp_query) && isset($wp_query->posts) && is_array($wp_query->posts) && !empty($wp_query->posts);
		// Check if we can show popup on this page
		if(($currentPageId && $havePostsListing && count($wp_query->posts) == 1) || $isHome) {
			if($isHome)
				$currentPageId = PPS_HOME_PAGE_ID;
			$condition .= " OR (show_pages = 2 AND id IN (SELECT popup_id FROM @__popup_show_pages WHERE post_id = $currentPageId AND not_show = 0))
				OR (show_pages = 3 AND id NOT IN (SELECT popup_id FROM @__popup_show_pages WHERE post_id = $currentPageId AND not_show = 1))";
		}
		$condition .= ")";
		// Check if there are popups that need to be rendered by click on some element
		$condition .= " AND (show_on != 3";
		if($havePostsListing) {
			$allowForPosts = array();
			// Check if show popup shortcode or at least it's show js function ppsShowPopup() - exists on any post content
			foreach($wp_query->posts as $post) {
				if(is_object($post) && isset($post->post_content)) {
					if((preg_match_all('/\[\s*'. PPS_SHORTCODE_CLICK. '.+id\s*\=.*(?P<POPUP_ID>\d+)\]/iUs', $post->post_content, $matches) 
						|| preg_match_all('/ppsShowPopup\s*\(\s*(?P<POPUP_ID>\d+)\s*\)\s*;*/iUs', $post->post_content, $matches)
						|| preg_match_all('/\"\#ppsShowPopUp_(?P<POPUP_ID>\d+)\"/iUs', $post->post_content, $matches)
						) && isset($matches['POPUP_ID'])
					) {
						if(!is_array($matches['POPUP_ID']))
							$matches['POPUP_ID'] = array( $matches['POPUP_ID'] );
						$matches['POPUP_ID'] = array_map('intval', $matches['POPUP_ID']);
						$allowForPosts = array_merge($allowForPosts, $matches['POPUP_ID']);
					}
				}
			}
			if(!empty($allowForPosts)) {
				$condition .= " OR id IN (". implode(',', $allowForPosts). ")";
			}
		}
		$condition .= ")";
		$condition = dispatcherPps::applyFilters('popupCheckCondition', $condition);
		if($this->getModel()->abDeactivated()) {
			$condition .= ' AND ab_id = 0';
		}
		$popups = $this->_beforeRender( $this->getModel()->addWhere( $condition )->getFromTbl() );
 		if(!empty($popups)) {
			$popups = dispatcherPps::applyFilters('popupListBeforeRender', $popups);
			$this->renderList( $popups );
		}
	}
	private function _beforeRender($popups) {
		global $wp_query;
		$dataRemoved = false;
		if(!empty($popups)) {
			$mobileDetect = NULL;
			$isMobile = false;
			$isTablet = false;
			$isDesktop = false;
			$isUserLoggedIn = framePps::_()->getModule('user')->isLoggedIn();
			$postType = false;
			
			$userIp = false;
			$countryCode = false;
			$langCode = false;
			
			foreach($popups as $i => $p) {
				if(isset($p['params']['main']['hide_for_devices']) 
					&& !empty($p['params']['main']['hide_for_devices'])
				) {	// Check if popup need to be hidden for some devices
					if(!$mobileDetect) {
						importClassPps('Mobile_Detect', PPS_HELPERS_DIR. 'mobileDetect.php');
						$mobileDetect = new Mobile_Detect();
						$isTablet = $mobileDetect->isTablet();
						$isMobile = !$isTablet && $mobileDetect->isMobile();
						$isDesktop = !$isMobile && !$isTablet;
					}
					$hideShowRevert = isset($p['params']['main']['hide_for_devices_show']) && (int) $p['params']['main']['hide_for_devices_show'];
					if((!$hideShowRevert && in_array('mobile', $p['params']['main']['hide_for_devices']) && $isMobile)
						|| ($hideShowRevert && !in_array('mobile', $p['params']['main']['hide_for_devices']) && $isMobile)
					) {
						unset($popups[ $i ]);
						$dataRemoved = true;
					} elseif((!$hideShowRevert && in_array('tablet', $p['params']['main']['hide_for_devices']) && $isTablet)
						|| ($hideShowRevert && !in_array('tablet', $p['params']['main']['hide_for_devices']) && $isTablet)
					) {
						unset($popups[ $i ]);
						$dataRemoved = true;
					} elseif((!$hideShowRevert && in_array('desktop', $p['params']['main']['hide_for_devices']) && $isDesktop)
						|| ($hideShowRevert && !in_array('desktop', $p['params']['main']['hide_for_devices']) && $isDesktop)
					) {
						unset($popups[ $i ]);
						$dataRemoved = true;
					}
				}
				if(isset($p['params']['main']['hide_for_post_types'])
					&& !empty($p['params']['main']['hide_for_post_types'])
				) { // Check if popup need to be hidden for some post types
					if(!$postType) {
						$postType = get_post_type();
					}
					$hideShowRevert = isset($p['params']['main']['hide_for_post_types_show']) && (int) $p['params']['main']['hide_for_post_types_show'];
					if(((!$hideShowRevert && count($wp_query->posts) === 1 && in_array($postType, $p['params']['main']['hide_for_post_types'])) 
						|| ($hideShowRevert && (!in_array($postType, $p['params']['main']['hide_for_post_types']) || count($wp_query->posts) !== 1))
					)) {
						unset($popups[ $i ]);
						$dataRemoved = true;
					}
				}
				if(isset($p['params']['main']['hide_for_logged_in']) 
					&& !empty($p['params']['main']['hide_for_logged_in'])
					&& $isUserLoggedIn
				) {	// Check if we need to hide it from logged-in users
					unset($popups[ $i ]);
					$dataRemoved = true;
				}
				if(isset($p['params']['main']['hide_for_ips']) 
					&& !empty($p['params']['main']['hide_for_ips'])
				) {	// Check if we need to hide it for IPs
					$hideForIpsArr = array_map('trim', explode(',', $p['params']['main']['hide_for_ips']));
					if(!empty($hideForIpsArr)) {
						if(!$userIp) {
							$userIp = utilsPps::getIP();
						}
						$hideShowRevert = isset($p['params']['main']['hide_for_ips_show']) && (int) $p['params']['main']['hide_for_ips_show'];
						if((!$hideShowRevert && in_array($userIp, $hideForIpsArr)) 
							|| ($hideShowRevert && !in_array($userIp, $hideForIpsArr))
						) {
							unset($popups[ $i ]);
							$dataRemoved = true;
						}
					}
				}
				if(isset($p['params']['main']['hide_for_countries']) 
					&& !empty($p['params']['main']['hide_for_countries'])
				) {	// Check if we need to hide it for Counties
					if(!$countryCode) {
						$countryCode = $this->getCountryCode();
					}
					$hideShowRevert = isset($p['params']['main']['hide_for_countries_show']) && (int) $p['params']['main']['hide_for_countries_show'];
					if((!$hideShowRevert && in_array($countryCode, $p['params']['main']['hide_for_countries']))
						|| ($hideShowRevert && !in_array($countryCode, $p['params']['main']['hide_for_countries']))
					) {
						unset($popups[ $i ]);
						$dataRemoved = true;
					}
				}
				if(isset($p['params']['main']['hide_for_languages']) 
					&& !empty($p['params']['main']['hide_for_languages'])
				) {	// Check if we need to hide it for Languages
					if(!$langCode) {
						$langCode = utilsPps::getBrowserLangCode();
					}
					$hideShowRevert = isset($p['params']['main']['hide_for_languages_show']) && (int) $p['params']['main']['hide_for_languages_show'];
					if((!$hideShowRevert && in_array($langCode, $p['params']['main']['hide_for_languages']))
						|| ($hideShowRevert && !in_array($langCode, $p['params']['main']['hide_for_languages']))
					) {
						unset($popups[ $i ]);
						$dataRemoved = true;
					}
				}
			}
		}
		if($dataRemoved) {
			$popups = array_values( $popups );
		}
		return $popups;
	}
	public function renderList($popups, $jsListVarName = 'ppsPopups') {
		static $renderedBefore = false;
		foreach($popups as $i => $p) {
			if(isset($p['params']['tpl']['anim_key']) && !empty($p['params']['tpl']['anim_key']) && $p['params']['tpl']['anim_key'] != 'none') {
				$popups[ $i ]['params']['tpl']['anim'] = $this->getView()->getAnimationByKey( $p['params']['tpl']['anim_key'] );
			}
			if(isset($p['params']['tpl']['anim_duration']) && !empty($p['params']['tpl']['anim_duration'])) {
				$popups[ $i ]['params']['tpl']['anim_duration'] = (float) $p['params']['tpl']['anim_duration'];
			}
			if(!isset($p['params']['tpl']['anim_duration']) || $p['params']['tpl']['anim_duration'] <= 0) {
				$popups[ $i ]['params']['tpl']['anim_duration'] = 1000;	// 1 second by default
			}
			$popups[ $i ]['rendered_html'] = $this->getView()->generateHtml( $p, array('replace_style_tag' => true) );
			// Unset those parameters - make data lighter
			unset($popups[ $i ]['css']);
			unset($popups[ $i ]['html']);
			$popups[ $i ]['connect_hash'] = md5(date('m-d-Y'). $popups[ $i ]['id']. NONCE_KEY);
			$this->_renderedIds[] = $p['id'];
		}
		if(!$renderedBefore) {
			framePps::_()->getModule('templates')->loadCoreJs();
			framePps::_()->addScript('frontend.popup', $this->getModPath(). 'js/frontend.popup.js');
			framePps::_()->addJSVar('frontend.popup', $jsListVarName, $popups);
			framePps::_()->addStyle('frontend.popup', $this->getModPath(). 'css/frontend.popup.css');
			framePps::_()->getModule('templates')->loadMagicAnims();
			$renderedBefore = true;
		} else {
			// We use such "un-professional" method - because in comon - we don't want to collect data for wp_footer output - because unfortunatelly not all themes has it, 
			// so, to make it work for most part of users - we try to out all scripts before footer
			// but some popups wil still need this - wp_footer for example - additional output - so that's why it is here
			framePps::_()->addScript('frontend.dummy.popup', $this->getModPath(). 'js/frontend.dummy.popup.js');
			framePps::_()->addJSVar('frontend.dummy.popup', $jsListVarName, $popups);
		}
	}
	public function collectFooterRender() {
		if(!empty($this->_addToFooterIds)) {
			$idsToRender = array();
			foreach($this->_addToFooterIds as $id) {
				if((!empty($this->_renderedIds) && in_array($id, $this->_renderedIds)) || in_array($id, $idsToRender)) continue;
				$idsToRender[] = $id;
			}
			if(!empty($idsToRender)) {
				$popups = $this->_beforeRender( $this->getModel()->addWhere('id IN ('. implode(',', $idsToRender). ')')->getFromTbl() );
				if(!empty($popups)) {
					$popups = dispatcherPps::applyFilters('popupListBeforeRender', $popups);
					$this->renderList( $popups, 'ppsPopupsFromFooter' );
				}
			}
		}
	}
	public function showPopupOnClick($params) {
		$id = isset($params['id']) ? (int) $params['id'] : 0;
		if(!$id && isset($params[0]) && !empty($params[0])) {	// For some reason - for some cases it convert space in shortcode - to %20 im this place
			$id = explode('=', $params[0]);
			$id = isset($id[1]) ? (int) $id[1] : 0;
		}
		$this->_addToFooterIds[] = $id;
		return '#ppsShowPopUp_'. $id;
	}
	public function getCountryCode( $ip = false ) {
		// Don't save this object in static - we will try to use this method only one time
		/*static $sxGeo;
		if(!$sxGeo) {*/
			importClassPps('SxGeo', PPS_HELPERS_DIR. 'SxGeo.php');
			$sxGeo = new SxGeo(PPS_FILES_DIR. 'SxGeo.dat');
		/*}*/
		if(!$ip)
			$ip = utilsPps::getIP ();
		return $sxGeo->getCountry($ip);
	}
	public function getAssetsUrl() {
		if(empty($this->_assetsUrl)) {
			$this->_assetsUrl = framePps::_()->getModule('templates')->getCdnUrl(). '_assets/popup/';
		}
		return $this->_assetsUrl;
	}
	public function getOldAssetsUrl() {
		return $this->_oldAssetsUrl;
	}
	public function checkMenuItemsForPopUps($menuItems) {
		if(!empty($menuItems)) {
			foreach($menuItems as $item) {
				if(is_object($item) && isset($item->attr_title) && !empty($item->attr_title) && strpos($item->attr_title, '#ppsShowPopUp_') !== false) {
					preg_match('/\#ppsShowPopUp_(\d+)/', $item->attr_title, $matched);
					$popupId = isset($matched[1]) ? (int) $matched[1] : 0;
					if($popupId) {
						$this->_addToFooterIds[] = $popupId;
					}
				}
			}
		}
		return $menuItems;
	}
}

