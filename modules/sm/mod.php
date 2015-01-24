<?php
class smPps extends modulePps {	//sm == socialmedia
	private $_availableLinks = array();
	private $_availableDesigns = array();
	public function generateHtml($popup) {
		$res = '';
		$this->getAvailableLinks();
		$this->getAvailableDesigns();
		$currFullUrl = uriPps::getFullUrl();
		$designKey = isset($popup['params']['tpl']['sm_design']) && isset($this->_availableDesigns[ $popup['params']['tpl']['sm_design'] ])
				? $popup['params']['tpl']['sm_design']
				: 'boxy';
		$res .= '<div class="ppsSmLinksShell ppsSmLinksShell_'. $designKey. '">';
		foreach($this->_availableLinks as $lKey => $lData) {
			if(isset($popup['params']['tpl']['enb_sm_'. $lKey]) && !empty($popup['params']['tpl']['enb_sm_'. $lKey])) {
				$res .= '<a class="ppsSmLink '. $lKey. ' '. $designKey. '" href="'. $lData['share_link']. urlencode($currFullUrl). '"></a>';
			}
		}
		$res .= '<div style="clear: both;"></div>';
		$res .= '</div>';
		return $res;
	}
	public function getAvailableLinks() {
		if(empty($this->_availableLinks)) {
			$this->_availableLinks = array(
				'facebook' => array('label' => __('Facebook', PPS_LANG_CODE), 'share_link' => 'https://www.facebook.com/sharer/sharer.php?u='),
				'googleplus' => array('label' => __('Google+', PPS_LANG_CODE), 'share_link' => 'https://plus.google.com/share?url='),
				'twitter' => array('label' => __('Twitter', PPS_LANG_CODE), 'share_link' => 'https://twitter.com/home?status='),
			);
		}
		return $this->_availableLinks;
	}
	public function getAvailableDesigns() {
		if(empty($this->_availableDesigns)) {
			$this->_availableDesigns = array(
				'simple' => array('label' => __('Simple', DPR_LANG_CODE)),
				'boxy' => array('label' => __('Boxy', DPR_LANG_CODE)),
			);
		}
		return $this->_availableDesigns;
	}
	public function generateCss($popup) {
		return str_replace('[PPS_MOD_PATH]', $this->getModPath(), file_get_contents( $this->getModDir(). 'sm.css' ));
	}
}

