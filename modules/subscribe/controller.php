<?php
class subscribeControllerPps extends controllerPps {
	public function subscribe() {
		$res = new responsePps();
		$data = reqPps::get('post');
		$id = isset($data['id']) ? (int) $data['id'] : 0;
		$nonce = $_REQUEST['_wpnonce'];
		if(!wp_verify_nonce($_REQUEST['_wpnonce'], 'subscribe-'. $id)) {
			die('Some error with your request.........');
		}
		if($this->getModel()->subscribe(reqPps::get('post'), true)) {
			$res->addMessage(__('Confirnation link was sent to your email address. Check your email!', PPS_LANG_CODE));
		} else
			$res->pushError ($this->getModel()->getErrors());
		return $res->ajaxExec();
	}
	public function confirm() {
		$res = new responsePps();
		if($this->getModel()->confirm(reqPps::get('get'))) {
			$res->addMessage(__('Thank you for subscribe!', PPS_LANG_CODE));
		} else
			$res->pushError ($this->getModel()->getErrors());
		// Just simple redirect for now
		$siteUrl = get_bloginfo('wpurl');
		redirectPps($siteUrl);
	}
	public function getPermissions() {
		return array(
			PPS_USERLEVELS => array(
				PPS_ADMIN => array()
			),
		);
	}
}

