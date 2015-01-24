<?php
class subscribePps extends modulePps {
	private $_destList = array();
	public function getDestList() {
		if(empty($this->_destList)) {
			$this->_destList = array(
				'wordpress' => array('label' => __('WordPress', PPS_LANG_CODE)),
				'aweber' => array('label' => __('Aweber', PPS_LANG_CODE)),
			);
		}
		return $this->_destList;
	}
	public function generateFormStart($popup) {
		if(isset($popup['params']['tpl']['sub_dest']) && !empty($popup['params']['tpl']['sub_dest'])) {
			$subDest = $popup['params']['tpl']['sub_dest'];
			$view = $this->getView();
			$generateMethod = 'generateFormStart_'. $subDest;
			if(method_exists($view, $generateMethod)) {
				return $view->$generateMethod( $popup );
			}
		}
		return '';
	}
	public function generateFormEnd($popup) {
		if(isset($popup['params']['tpl']['sub_dest']) && !empty($popup['params']['tpl']['sub_dest'])) {
			$subDest = $popup['params']['tpl']['sub_dest'];
			$view = $this->getView();
			$generateMethod = 'generateFormEnd_'. $subDest;
			if(method_exists($view, $generateMethod)) {
				return $view->$generateMethod( $popup );
			}
		}
		return '';
	}
}

