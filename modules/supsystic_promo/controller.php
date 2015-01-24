<?php
class supsystic_promoControllerPps extends controllerPps {
    public function welcomePageSaveInfo() {
		$res = new responsePps();
		installerPps::setUsed();
		if($this->getModel()->welcomePageSaveInfo(reqPps::get('get'))) {
			$res->addMessage(__('Information was saved. Thank you!', PPS_LANG_CODE));
		} else {
			$res->pushError($this->getModel()->getErrors());
		}
		$originalPage = reqPps::getVar('original_page');
		$http = isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) ? 'https://' : 'http://';
		if(strpos($originalPage, $http. $_SERVER['HTTP_HOST']) !== 0) {
			$originalPage = '';
		}
		redirectPps($originalPage);
	}
	/**
	 * @see controller::getPermissions();
	 */
	public function getPermissions() {
		return array(
			PPS_USERLEVELS => array(
				PPS_ADMIN => array('welcomePageSaveInfo')
			),
		);
	}
}