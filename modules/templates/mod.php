<?php
class templatesPps extends modulePps {
    protected $_styles = array();
    public function init() {
        if (is_admin()) {
			if($isAdminPlugOptsPage = framePps::_()->isAdminPlugOptsPage()) {
				$this->loadCoreJs();
				$this->loadAdminCoreJs();
				$this->loadCoreCss();
				$this->loadChosenSelects();
				framePps::_()->addScript('adminOptionsPps', PPS_JS_PATH. 'admin.options.js', array(), false, true);
				add_action('admin_enqueue_scripts', array($this, 'loadMediaScripts'));
			}
			// Some common styles - that need to be on all admin pages - be careful with them
			framePps::_()->addStyle('supsystic-for-all-admin-'. PPS_CODE, PPS_CSS_PATH. 'supsystic-for-all-admin.css');
		}
        parent::init();
    }
	public function loadMediaScripts() {
		wp_enqueue_media();
	}
	public function loadAdminCoreJs() {
		framePps::_()->addScript('jquery-ui-dialog');
		framePps::_()->addScript('jquery-ui-slider');
		framePps::_()->addScript('wp-color-picker');
		framePps::_()->addScript('tooltipster', PPS_JS_PATH. 'jquery.tooltipster.min.js');
		framePps::_()->addScript('icheck', PPS_JS_PATH. 'icheck.min.js');
	}
	public function loadCoreJs() {
		framePps::_()->addScript('jquery');

		framePps::_()->addScript('commonPps', PPS_JS_PATH. 'common.js');
		framePps::_()->addScript('corePps', PPS_JS_PATH. 'core.js');
		
		//framePps::_()->addScript('selecter', PPS_JS_PATH. 'jquery.fs.selecter.min.js');
		
		$ajaxurl = admin_url('admin-ajax.php');
		$jsData = array(
			'siteUrl'					=> PPS_SITE_URL,
			'imgPath'					=> PPS_IMG_PATH,
			'cssPath'					=> PPS_CSS_PATH,
			'loader'					=> PPS_LOADER_IMG, 
			'close'						=> PPS_IMG_PATH. 'cross.gif', 
			'ajaxurl'					=> $ajaxurl,
			//'options'					=> framePps::_()->getModule('options')->getAllowedPublicOptions(),
			'PPS_CODE'					=> PPS_CODE,
			//'ball_loader'				=> PPS_IMG_PATH. 'ajax-loader-ball.gif',
			//'ok_icon'					=> PPS_IMG_PATH. 'ok-icon.png',
		);
		$jsData = dispatcherPps::applyFilters('jsInitVariables', $jsData);
		framePps::_()->addJSVar('corePps', 'PPS_DATA', $jsData);
	}
	public function loadCoreCss() {
		$this->_styles = array(
			'stylePps'			=> array('path' => PPS_CSS_PATH. 'style.css', 'for' => 'admin'), 
			'supsystic-uiPps'	=> array('path' => PPS_CSS_PATH. 'supsystic-ui.css', 'for' => 'admin'), 
			'dashicons'			=> array('for' => 'admin'),
			'bootstrap-alerts'	=> array('path' => PPS_CSS_PATH. 'bootstrap-alerts.css', 'for' => 'admin'),
			'tooltipster'		=> array('path' => PPS_CSS_PATH. 'tooltipster.css', 'for' => 'admin'),
			'icheck'			=> array('path' => PPS_CSS_PATH. 'jquery.icheck.css', 'for' => 'admin'),
			//'uniform'			=> array('path' => PPS_CSS_PATH. 'uniform.default.css', 'for' => 'admin'),
			//'selecter'			=> array('path' => PPS_CSS_PATH. 'jquery.fs.selecter.min.css', 'for' => 'admin'),
			'wp-color-picker'	=> array('for' => 'admin'),
		);
		foreach($this->_styles as $s => $sInfo) {
			if(!empty($sInfo['path'])) {
				framePps::_()->addStyle($s, $sInfo['path']);
			} else {
				framePps::_()->addStyle($s);
			}
		}
		$this->loadFontAwesome();
	}
	public function loadJqueryUi() {
		static $loaded = false;
		if(!$loaded) {
			framePps::_()->addStyle('jquery-ui', PPS_CSS_PATH. 'jquery-ui.min.css');
			framePps::_()->addStyle('jquery-ui.structure', PPS_CSS_PATH. 'jquery-ui.structure.min.css');
			framePps::_()->addStyle('jquery-ui.theme', PPS_CSS_PATH. 'jquery-ui.theme.min.css');
			framePps::_()->addStyle('jquery-slider', PPS_CSS_PATH. 'jquery-slider.css');
			$loaded = true;
		}
	}
	public function loadJqGrid() {
		static $loaded = false;
		if(!$loaded) {
			$this->loadJqueryUi();
			framePps::_()->addScript('jq-grid', PPS_JS_PATH. 'jquery.jqGrid.min.js');
			framePps::_()->addStyle('jq-grid', PPS_CSS_PATH. 'ui.jqgrid.css');
			$langToLoad = utilsPps::getLangCode2Letter();
			if(!file_exists(PPS_JS_DIR. 'i18n'. DS. 'grid.locale-'. $langToLoad. '.js')) {
				$langToLoad = 'en';
			}
			framePps::_()->addScript('jq-grid-lang', PPS_JS_PATH. 'i18n/grid.locale-'. $langToLoad. '.js');
			$loaded = true;
		}
	}
	public function loadFontAwesome() {
		framePps::_()->addStyle('font-awesomePps', PPS_CSS_PATH. 'font-awesome.css');
	}
	public function loadChosenSelects() {
		framePps::_()->addStyle('jquery.chosen', PPS_CSS_PATH. 'chosen.min.css');
		framePps::_()->addScript('jquery.chosen', PPS_JS_PATH. 'chosen.jquery.min.js');
	}
	public function loadDatePicker() {
		framePps::_()->addScript('jquery-ui-datepicker');
	}
	public function loadJqplot() {
		static $loaded = false;
		if(!$loaded) {
			$jqplotDir = 'jqplot/';

			framePps::_()->addStyle('jquery.jqplot', PPS_CSS_PATH. 'jquery.jqplot.min.css');

			framePps::_()->addScript('jplot', PPS_JS_PATH. $jqplotDir. 'jquery.jqplot.min.js');
			framePps::_()->addScript('jqplot.canvasAxisLabelRenderer', PPS_JS_PATH. $jqplotDir. 'jqplot.canvasAxisLabelRenderer.min.js');
			framePps::_()->addScript('jqplot.canvasTextRenderer', PPS_JS_PATH. $jqplotDir. 'jqplot.canvasTextRenderer.min.js');
			framePps::_()->addScript('jqplot.dateAxisRenderer', PPS_JS_PATH. $jqplotDir. 'jqplot.dateAxisRenderer.min.js');
			framePps::_()->addScript('jqplot.canvasAxisTickRenderer', PPS_JS_PATH. $jqplotDir. 'jqplot.canvasAxisTickRenderer.min.js');
			framePps::_()->addScript('jqplot.highlighter', PPS_JS_PATH. $jqplotDir. 'jqplot.highlighter.min.js');
			framePps::_()->addScript('jqplot.cursor', PPS_JS_PATH. $jqplotDir. 'jqplot.cursor.min.js');
			framePps::_()->addScript('jqplot.barRenderer', PPS_JS_PATH. $jqplotDir. 'jqplot.barRenderer.min.js');
			framePps::_()->addScript('jqplot.categoryAxisRenderer', PPS_JS_PATH. $jqplotDir. 'jqplot.categoryAxisRenderer.min.js');
			framePps::_()->addScript('jqplot.pointLabels', PPS_JS_PATH. $jqplotDir. 'jqplot.pointLabels.min.js');
			framePps::_()->addScript('jqplot.pieRenderer', PPS_JS_PATH. $jqplotDir. 'jqplot.pieRenderer.min.js');
			$loaded = true;
		}
	}
}
