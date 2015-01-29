<?php
class statisticsPps extends modulePps {
	private $_types = array();
	public function getTypes() {
		if(empty($this->_types)) {
			$this->_types = array(
				'show' => array('id' => 1),
				'subscribe' => array('id' => 2),
				'share' => array('id' => 3),
				'fb_like' => array('id' => 4),
			);
		}
		return $this->_types;
	}
	public function getTypeIdByCode($code) {
		$this->getTypes();
		return isset($this->_types[ $code ]) ? $this->_types[ $code ]['id'] : false;
	}
}