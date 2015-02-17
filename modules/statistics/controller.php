<?php
class statisticsControllerPps extends controllerPps {
	public function add() {
		$res = new responsePps();
		$connectHash = reqPps::getVar('connect_hash', 'post');
		$id = reqPps::getVar('id', 'post');
		if(md5(date('m-d-Y'). $id. NONCE_KEY) != $connectHash) {
			$res->pushError('Some undefined for now.....');
			$res->ajaxExec( true );
		}
		if($this->getModel()->add( reqPps::get('post') )) {
			// Do nothing for now
		} else
			$res->pushError ($this->getModel()->getErrors());
		$res->ajaxExec();
	}
	public function clearForPopUp() {
		$res = new responsePps();
		if($this->getModel()->clearForPopUp( reqPps::get('post') )) {
			$res->addMessage(__('Done', PPS_LANG_CODE));
		} else
			$res->pushError ($this->getModel()->getErrors());
		$res->ajaxExec();
	}
	public function getPermissions() {
		return array(
			PPS_USERLEVELS => array(
				PPS_ADMIN => array('clearForPopUp')
			),
		);
	}
}
