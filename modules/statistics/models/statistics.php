<?php
class statisticsModelPps extends modelPps {
	public function __construct() {
		$this->_setTbl('statistics');
	}
	public function add($d = array()) {
		$d['id'] = isset($d['id']) ? (int) $d['id'] : 0;
		$d['type'] = isset($d['type']) ? $d['type'] : '';
		if(!empty($d['id']) && !empty($d['type'])) {
			$typeId = $this->getModule()->getTypeIdByCode( $d['type'] );
			return $this->insert(array(
				'popup_id' => $d['id'],
				'type' => $typeId,
			));
		} else
			$this->pushError(__('Send me some info, pls', PPS_LANG_CODE));
		return false;
	}
}