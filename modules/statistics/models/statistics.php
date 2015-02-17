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
			$smId = 0;
			if($d['type'] == 'share' && isset($d['sm_type']) && !empty($d['sm_type'])) {
				$smId = framePps::_()->getModule('sm')->getTypeIdByCode( $d['sm_type'] );
			}
			return $this->insert(array(
				'popup_id' => $d['id'],
				'type' => $typeId,
				'sm_id' => $smId,
			));
		} else
			$this->pushError(__('Send me some info, pls', PPS_LANG_CODE));
		return false;
	}
	/**
	 * Get list for popup
	 * @param numeric $pid PopUp ID
	 * @param array $params Additional selection params, $params = array('type' => '')
	 * @return array List of statistics data
	 */
	public function getForPopup($popupId, $params = array()) {
		$where = array('popup_id' => $popupId);
		$typeId = isset($params['type']) ? $params['type'] : 0;
		if($typeId && !is_numeric($typeId)) {
			$typeId = $this->getModule()->getTypeIdByCode( $typeId );
		}
		if($typeId) {
			$where['type'] = $typeId;
		}
		return $this->setSelectFields('COUNT(*) AS total_requests, DATE_FORMAT(date_created, "%m-%d-%Y") AS date')
				->groupBy('date')
				->setOrderBy('date')
				->setSortOrder('DESC')
				->setWhere($where)
				->getFromTbl();
	}
	public function getSmActionForPopup($popupId) {
		$where = array('popup_id' => $popupId, 'additionalCondition' => ' sm_id != 0 ');
		$data = $this->setSelectFields('COUNT(*) AS total_requests, sm_id')
				->groupBy('sm_id')
				->setWhere($where)
				->getFromTbl();
		if(!empty($data)) {
			foreach($data as $i => $row) {
				$data[ $i ]['sm_type'] = framePps::_()->getModule('sm')->getTypeById( $row['sm_id'] );
			}
		}
		return $data;
	}
	public function clearForPopUp($d = array()) {
		$d['id'] = isset($d['id']) ? (int) $d['id'] : 0;
		if($d['id']) {
			return $this->delete(array('popup_id' => $d['id']));
		} else
			$this->pushError(__('Invalid ID', PPS_LANG_CODE));
		return false;
	}
	public function getAllForPopupId($id) {
		$allTypes = $this->getModule()->getTypes();
		$allStats = array();
		$haveData = false;
		$i = 0;
		foreach($allTypes as $typeCode => $type) {
			$allStats[ $i ] = $type;
			$allStats[ $i ]['code'] = $typeCode;
			$allStats[ $i ]['points'] = $this->getForPopup($id, array('type' => $type['id']));
			if(!empty($allStats[ $i ]['points'])) {
				$haveData = true;
			}
			$i++;
		}
		return $haveData ? $allStats : false;
	}
}