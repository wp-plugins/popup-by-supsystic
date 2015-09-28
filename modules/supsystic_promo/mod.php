<?php
class supsystic_promoPps extends modulePps {
	private $_mainLink = '';
	private $_minDataInStatToSend = 20;	// At least 20 points in table shuld be present before send stats
	private $_assetsUrl = '';
	public function __construct($d) {
		parent::__construct($d);
		$this->getMainLink();
		dispatcherPps::addFilter('jsInitVariables', array($this, 'addMainOpts'));
	}
	public function init() {
		parent::init();
		add_action('admin_footer', array($this, 'displayAdminFooter'), 9);
		if(is_admin()) {
			add_action('init', array($this, 'checkWelcome'));
			add_action('init', array($this, 'checkStatisticStatus'));
		}
		$this->weLoveYou();
		dispatcherPps::addFilter('mainAdminTabs', array($this, 'addAdminTab'));
		dispatcherPps::addFilter('subDestList', array($this, 'addSubDestList'));
		dispatcherPps::addAction('beforeSaveOpts', array($this, 'checkSaveOpts'));
	}
	public function addAdminTab($tabs) {
		$tabs['overview'] = array(
			'label' => __('Overview', PPS_LANG_CODE), 'callback' => array($this, 'getOverviewTabContent'), 'fa_icon' => 'fa-info', 'sort_order' => 5,
		);
		return $tabs;
	}
	public function addSubDestList($subDestList) {
		if(!$this->isPro()) {
			$subDestList = array_merge($subDestList, array(
				'constantcontact' => array('label' => __('Constant Contact - PRO', PPS_LANG_CODE), 'require_confirm' => true),
				'campaignmonitor' => array('label' => __('Campaign Monitor - PRO', PPS_LANG_CODE), 'require_confirm' => true),
				'verticalresponse' => array('label' => __('Vertical Response - PRO', PPS_LANG_CODE), 'require_confirm' => true),
				'sendgrid' => array('label' => __('SendGrid - PRO', PPS_LANG_CODE), 'require_confirm' => true),
				'get_response' => array('label' => __('GetResponse - PRO', PPS_LANG_CODE), 'require_confirm' => true),
				'activecampaign' => array('label' => __('Active Campaign', PPS_LANG_CODE), 'require_confirm' => true),
				'mailrelay' => array('label' => __('Mailrelay - PRO', PPS_LANG_CODE), 'require_confirm' => true),
				'arpreach' => array('label' => __('arpReach - PRO', PPS_LANG_CODE), 'require_confirm' => true),
				'sgautorepondeur' => array('label' => __('SG Autorepondeur - PRO', PPS_LANG_CODE), 'require_confirm' => true),
			));
		}
		return $subDestList;
	}
	public function getOverviewTabContent() {
		return $this->getView()->getOverviewTabContent();
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
		return $link;
	}
	public function weLoveYou() {
		if(!$this->isPro()) {
			dispatcherPps::addFilter('popupEditTabs', array($this, 'addUserExp'));
			dispatcherPps::addFilter('popupEditDesignTabs', array($this, 'addUserExpDesign'));
			dispatcherPps::addFilter('editPopupMainOptsShowOn', array($this, 'showAdditionalmainAdminShowOnOptions'));
		}
	}
	public function showAdditionalmainAdminShowOnOptions($popup) {
		$this->getView()->showAdditionalmainAdminShowOnOptions($popup);
	}
	public function addUserExp($tabs) {
		$modPath = $this->getAssetsUrl();
		$tabs['ppsPopupAbTesting'] = array(
			'title' => __('Testing', PPS_LANG_CODE), 
			'content' => '<a href="'. $this->generateMainLink('utm_source=plugin&utm_medium=abtesting&utm_campaign=popup'). '" target="_blank" class="button button-primary">'
				. __('Get PRO', PPS_LANG_CODE). '</a><br /><a href="'. $this->generateMainLink('utm_source=plugin&utm_medium=abtesting&utm_campaign=popup'). '" target="_blank">'
				. '<img style="max-width: 100%;" src="'. $modPath. 'img/AB-testing-pro.jpg" />'
			. '</a>',
			'icon_content' => '<b>A/B</b>',
			'avoid_hide_icon' => true,
			'sort_order' => 55,
		);
		return $tabs;
	}
	public function addUserExpDesign($tabs) {
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
		$canSend = (int) framePps::_()->getModule('options')->get('send_stats');
		if($canSend && framePps::_()->getModule('user')->isAdmin()) {
			// Before this version we had many wrong data collected taht we don't need at all. Let's clear them.
			if(PPS_VERSION == '1.3.5') {
				$clearedTrashStatData = (int) get_option(PPS_DB_PREF. 'cleared_trash_stat_data');
				if(!$clearedTrashStatData) {
					$this->getModel()->clearUsageStat();
					update_option(PPS_DB_PREF. 'cleared_trash_stat_data', 1);
					return;	// We just cleared whole data - so don't need to even check send stats
				}
			}
			$this->getModel()->checkAndSend();
		}
	}
	public function getMinStatSend() {
		return $this->_minDataInStatToSend;
	}
	public function getMainLink() {
		if(empty($this->_mainLink)) {
			$affiliateQueryString = '';
			$this->_mainLink = 'http://supsystic.com/plugins/popup-plugin/' . $affiliateQueryString;
		}
		return $this->_mainLink ;
	}
	public function generateMainLink($params = '') {
		$mainLink = $this->getMainLink();
		if(!empty($params)) {
			return $mainLink. (strpos($mainLink , '?') ? '&' : '?'). $params;
		}
		return $mainLink;
	}
	public function getContactFormFields() {
		$fields = array(
            'name' => array('label' => __('Name', PPS_LANG_CODE), 'valid' => 'notEmpty', 'html' => 'text'),
			'email' => array('label' => __('Email', PPS_LANG_CODE), 'html' => 'email', 'valid' => array('notEmpty', 'email'), 'placeholder' => 'example@mail.com', 'def' => get_bloginfo('admin_email')),
			'website' => array('label' => __('Website', PPS_LANG_CODE), 'html' => 'text', 'placeholder' => 'http://example.com', 'def' => get_bloginfo('url')),
			'subject' => array('label' => __('Subject', PPS_LANG_CODE), 'valid' => 'notEmpty', 'html' => 'text'),
            'category' => array('label' => __('Topic', PPS_LANG_CODE), 'valid' => 'notEmpty', 'html' => 'selectbox', 'options' => array(
				'plugins_options' => __('Plugin options', PPS_LANG_CODE),
				'bug' => __('Report a bug', PPS_LANG_CODE),
				'functionality_request' => __('Require a new functionality', PPS_LANG_CODE),
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
	public function isPro() {
		static $isPro;
		if(is_null($isPro)) {
			// license is always active with PRO - even if license key was not entered, 
			// add_options module was from the begining of the times in PRO, and will be active only once user will activate license on site
			$isPro = framePps::_()->getModule('license') && framePps::_()->getModule('on_exit');
		}
		return $isPro;
	}
	public function getAssetsUrl() {
		if(empty($this->_assetsUrl)) {
			$this->_assetsUrl = framePps::_()->getModule('popup')->getAssetsUrl(). 'promo/';
		}
		return $this->_assetsUrl;
	}
	public function checkWelcome() {
		$from = reqPps::getVar('from', 'get');
		$pl = reqPps::getVar('pl', 'get');
		if($from == 'welcome-page' && $pl == PPS_CODE && framePps::_()->getModule('user')->isAdmin()) {
			$welcomeSent = (int) get_option(PPS_DB_PREF. 'welcome_sent');
			if(!$welcomeSent) {
				$this->getModel()->welcomePageSaveInfo();
				update_option(PPS_DB_PREF. 'welcome_sent', 1);
			}
		}
	}
	public function getContactLink() {
		return $this->getMainLink(). '#contact';
	}
	public function addMainOpts($opts) {
		$title = 'WordPress PopUp Plugin';
		$opts['options']['love_link_html'] = '<a title="'. $title. '" style="color: #26bfc1 !important; font-size: 9px; position: absolute; bottom: 15px; right: 15px;" href="'. $this->generateMainLink('utm_source=plugin&utm_medium=love_link&utm_campaign=popup'). '" target="_blank">'
			. $title
			. '</a>';
		return $opts;
	}
	public function checkSaveOpts($newValues) {
		$loveLinkEnb = (int) framePps::_()->getModule('options')->get('add_love_link');
		$loveLinkEnbNew = isset($newValues['opt_values']['add_love_link']) ? (int) $newValues['opt_values']['add_love_link'] : 0;
		if($loveLinkEnb != $loveLinkEnbNew) {
			$this->getModel()->saveUsageStat('love_link.'. ($loveLinkEnbNew ? 'enb' : 'dslb'));
		}
	}
}