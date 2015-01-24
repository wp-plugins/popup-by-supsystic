<?php
class optionsControllerPps extends controllerPps {
	public function saveGroup() {
		$res = new responsePps();
		if($this->getModel()->saveGroup(reqPps::get('post'))) {
			$res->addMessage(__('Done', PPS_LANG_CODE));
		} else
			$res->pushError ($this->getModel('options')->getErrors());
		return $res->ajaxExec();
	}
	public function activatePlugin() {
		$res = new responsePps();
		if($this->getModel('modules')->activatePlugin(reqPps::get('post'))) {
			$res->addMessage(__('Plugin was activated', PPS_LANG_CODE));
		} else {
			$res->pushError($this->getModel('modules')->getErrors());
		}
		return $res->ajaxExec();
	}
	public function activateUpdate() {
		$res = new responsePps();
		if($this->getModel('modules')->activateUpdate(reqPps::get('post'))) {
			$res->addMessage(__('Very good! Now plugin will be updated.', PPS_LANG_CODE));
		} else {
			$res->pushError($this->getModel('modules')->getErrors());
		}
		return $res->ajaxExec();
	}
	public function getPermissions() {
		return array(
			PPS_USERLEVELS => array(
				PPS_ADMIN => array('saveGroup', 'activatePlugin', 'activateUpdate')
			),
		);
	}
}

