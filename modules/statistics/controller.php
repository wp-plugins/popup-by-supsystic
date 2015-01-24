<?php
class statisticsControllerPps extends controllerPps {
	public function add() {
		$res = new responsePps();
		$connectHash = reqPps::getVar('connect_hash', 'post');
		$id = reqPps::getVar('id', 'post');
		if(md5(date('m-d-Y'). $id. NONCE_KEY) != $connectHash) {
			$res->pushError('Some undefined for now.....');
		}
		if($this->getModel()->add( reqPps::get('post') )) {
			// Do nothing for now
		} else
			$res->pushError ($this->getModel()->getErrors());
		$res->ajaxExec();
	}
	public function getPermissions() {
		return array(
			PPS_USERLEVELS => array(
				PPS_ADMIN => array()
			),
		);
	}
}
