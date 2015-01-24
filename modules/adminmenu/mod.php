<?php
class adminmenuPps extends modulePps {
	protected $_mainSlug = 'popup-wp-supsystic';
	private $_mainCap = 'manage_options';
    public function init() {
        parent::init();
		add_action('admin_menu', array($this, 'initMenu'), 9);
		$plugName = plugin_basename(PPS_DIR. PPS_MAIN_FILE);
		add_filter('plugin_action_links_'. $plugName, array($this, 'addSettingsLinkForPlug') );
    }
	public function addSettingsLinkForPlug($links) {
		array_unshift($links, '<a href="'. $this->getMainLink(). '">'. __('Settings', PPS_LANG_CODE). '</a>');
		return $links;
	}
	public function initMenu() {
		$mainCap = $this->getMainCap();
		$mainSlug = dispatcherPps::applyFilters('adminMenuMainSlug', $this->_mainSlug);
		$mainMenuPageOptions = array(
			'page_title' => PPS_WP_PLUGIN_NAME, 
			'menu_title' => PPS_WP_PLUGIN_NAME, 
			'capability' => $mainCap,
			'menu_slug' => $mainSlug,
			'function' => array(framePps::_()->getModule('options'), 'getAdminPage'));
		$mainMenuPageOptions = dispatcherPps::applyFilters('adminMenuMainOption', $mainMenuPageOptions);
        add_menu_page($mainMenuPageOptions['page_title'], $mainMenuPageOptions['menu_title'], $mainMenuPageOptions['capability'], $mainMenuPageOptions['menu_slug'], $mainMenuPageOptions['function'], 'dashicons-format-gallery');

		$tabs = framePps::_()->getModule('options')->getTabs();
		$subMenus = array();
		foreach($tabs as $tKey => $tab) {
			if($tKey == 'main_page') continue;	// Top level menu item - is main page, avoid place it 2 times
			if(isset($tab['hidden']) && $tab['hidden']) continue;
			$subMenus[] = array(
				'title' => $tab['label'], 'capability' => $mainCap, 'menu_slug' => 'admin.php?page='. $mainSlug. '&tab='. $tKey, 'function' => ''
			);
		}
		$subMenus = dispatcherPps::applyFilters('adminMenuOptions', $subMenus);
		foreach($subMenus as $opt) {
			add_submenu_page($mainSlug, $opt['title'], $opt['title'], $opt['capability'], $opt['menu_slug'], $opt['function']);
		}
	}
	public function getMainLink() {
		return uriPps::_(array('baseUrl' => admin_url('admin.php'), 'page' => $this->getMainSlug()));
	}
	public function getMainSlug() {
		return $this->_mainSlug;
	}
	public function getMainCap() {
		return $this->_mainCap;
	}
}
