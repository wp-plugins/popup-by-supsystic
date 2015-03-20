<?php
class supsystic_promoPps extends modulePps {
	private $_mainLink = '';
	private $_specSymbols = array(
		'from'	=> array('?', '&'),
		'to'	=> array('%', '^'),
	);
	private $_minDataInStatToSend = 20;	// At least 20 points in table shuld be present before send stats
	public function __construct($d) {
		parent::__construct($d);
		$this->getMainLink();
	}
	public function init() {
		parent::init();
		add_action('admin_footer', array($this, 'displayAdminFooter'), 9);
		if(is_admin()) {
			$this->checkStatisticStatus();
		}
		$this->weLoveYou();
		dispatcherPps::addFilter('mainAdminTabs', array($this, 'addAdminTab'));
	}
	public function addAdminTab($tabs) {
		$tabs['overview'] = array(
			'label' => __('Overview', PPS_LANG_CODE), 'callback' => array($this, 'getOverviewTabContent'), 'fa_icon' => 'fa-info', 'sort_order' => 5,
		);
		return $tabs;
	}
	public function getOverviewTabContent() {
		return $this->getView()->getOverviewTabContent();
	}
	// We used such methods - _encodeSlug() and _decodeSlug() - as in slug wp don't understand urlencode() functions
	private function _encodeSlug($slug) {
		return str_replace($this->_specSymbols['from'], $this->_specSymbols['to'], $slug);
	}
	private function _decodeSlug($slug) {
		return str_replace($this->_specSymbols['to'], $this->_specSymbols['from'], $slug);
	}
	public function decodeSlug($slug) {
		return $this->_decodeSlug($slug);
	}
	public function modifyMainAdminSlug($mainSlug) {
		$firstTimeLookedToPlugin = !installerPps::isUsed();
		if($firstTimeLookedToPlugin) {
			$mainSlug = $this->_getNewAdminMenuSlug($mainSlug);
		}
		return $mainSlug;
	}
	private function _getWelcomMessageMenuData($option, $modifySlug = true) {
		return array_merge($option, array(
			'page_title'	=> __('Welcome to Supsystic Secure', PPS_LANG_CODE),
			'menu_slug'		=> ($modifySlug ? $this->_getNewAdminMenuSlug( $option['menu_slug'] ) : $option['menu_slug'] ),
			'function'		=> array($this, 'showWelcomePage'),
		));
	}
	public function addWelcomePageToMenus($options) {
		$firstTimeLookedToPlugin = !installerPps::isUsed();
		if($firstTimeLookedToPlugin) {
			foreach($options as $i => $opt) {
				$options[$i] = $this->_getWelcomMessageMenuData( $options[$i] );
			}
		}
		return $options;
	}
	private function _getNewAdminMenuSlug($menuSlug) {
		// We can't use "&" symbol in slug - so we used "|" symbol
		$newSlug = $this->_encodeSlug(str_replace('admin.php?page=', '', $menuSlug));
		return 'welcome-to-'. framePps::_()->getModule('adminmenu')->getMainSlug(). '|return='. $newSlug;
	}
	public function addWelcomePageToMainMenu($option) {
		$firstTimeLookedToPlugin = !installerPps::isUsed();
		if($firstTimeLookedToPlugin) {
			$option = $this->_getWelcomMessageMenuData($option, false);
		}
		return $option;
	}
	public function showWelcomePage() {
		$this->getView()->showWelcomePage();
	}
	public function displayAdminFooter() {
		if(framePps::_()->isAdminPlugPage()) {
			$this->getView()->displayAdminFooter();
		}
	}
	private function _preparePromoLink($link, $ref = '') {
		if(empty($ref))
			$ref = 'user';
		$link .= '?ref='. $ref;
		return $link;
	}
	public function weLoveYou() {
		if(!framePps::_()->getModule(implode('', array('l','ic','e','ns','e')))) {
			dispatcherPps::addFilter('popupEditTabs', array($this, 'addUserExp'));
			dispatcherPps::addFilter('editPopupMainOptsShowOn', array($this, 'showAdditionalmainAdminShowOnOptions'));
		}
	}
	public function showAdditionalmainAdminShowOnOptions($popup) {
		$this->getView()->showAdditionalmainAdminShowOnOptions($popup);
	}
	public function addUserExp($tabs) {
		$url = $this->getMainLink();
		$modPath = $this->getModPath();
		$tabs['ppsPopupAbTesting'] = array(
			'title' => __('Testing', PPS_LANG_CODE), 
			'content' => '<a href="'. $this->_preparePromoLink($url). '" target="_blank" class="button button-primary">'. __('Get PRO', PPS_LANG_CODE). '</a><br /><a href="'. $this->_preparePromoLink($url). '" target="_blank"><img style="max-width: 100%;" src="'. $modPath. 'img/AB-testing-pro.jpg" /></a>',
			'icon_content' => '<b>A/B</b>',
			'avoid_hide_icon' => true,
			'sort_order' => 55,
		);
		$tabs['ppsPopupLayeredPopup'] = array(
			'title' => __('Layered Style', PPS_LANG_CODE), 
			'content' => $this->getView()->getLayeredStylePromo(),
			'fa_icon' => 'fa-arrows',
			'sort_order' => 15,
		);
		return $tabs;
	}
	/**
	 * Public shell for private method
	 */
	public function preparePromoLink($link, $ref = '') {
		return $this->_preparePromoLink($link, $ref);
	}
	public function checkStatisticStatus(){
		// Do not send usage functionas statitistics
		return;
		if(framePps::_()->getModule('options')->isEmpty('send_stats')) {	// Enabled by default
			framePps::_()->getModule('options')->getModel()->save('send_stats', 1);
		}
		$canSend = (int) framePps::_()->getModule('options')->get('send_stats');
		if($canSend) {
			$this->getModel()->checkAndSend();
		}
	}
	public function getMinStatSend() {
		return $this->_minDataInStatToSend;
	}
	public function getMainLink() {
		if(empty($this->_mainLink)) {
			$this->_mainLink = 'http://supsystic.com/plugins/popup-plugin/';
		}
		return $this->_mainLink ;
	}
	public function getContactFormFields() {
		$fields = array(
            'name' => array('label' => __('Your name', PPS_LANG_CODE), 'valid' => 'notEmpty', 'html' => 'text'),
			'email' => array('label' => __('Your email', PPS_LANG_CODE), 'html' => 'email', 'valid' => array('notEmpty', 'email'), 'placeholder' => 'example@mail.com', 'def' => get_bloginfo('admin_email')),
			'website' => array('label' => __('Website', PPS_LANG_CODE), 'html' => 'text', 'placeholder' => 'http://example.com', 'def' => get_bloginfo('url')),
			'subject' => array('label' => __('Subject', PPS_LANG_CODE), 'valid' => 'notEmpty', 'html' => 'text'),
            'category' => array('label' => __('Topic', PPS_LANG_CODE), 'valid' => 'notEmpty', 'html' => 'selectbox', 'options' => array(
				'plugins_options' => __('Plugin options', PPS_LANG_CODE),
				'bug' => __('Report a bug', PPS_LANG_CODE),
				'functionality_request' => __('Require a new functionallity', PPS_LANG_CODE),
				'other' => __('Other', PPS_LANG_CODE),
			)),
			'message' => array('label' => __('Message', PPS_LANG_CODE), 'valid' => 'notEmpty', 'html' => 'textarea', 'placeholder' => __('Hello Supsystic Team!', PPS_LANG_CODE)),
        );
		foreach($fields as $k => $v) {
			if(isset($fields[ $k ]['valid']) && !is_array($fields[ $k ]['valid']))
				$fields[ $k ]['valid'] = array( $fields[ $k ]['valid'] );
		}
		return $fields;
	}
}