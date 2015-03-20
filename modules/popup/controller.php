<?php
class popupControllerPps extends controllerPps {
	private $_prevPopupId = 0;
	public function createFromTpl() {
		$res = new responsePps();
		if(($id = $this->getModel()->createFromTpl(reqPps::get('post'))) != false) {
			$res->addMessage(__('Done', PPS_LANG_CODE));
			$res->addData('edit_link', $this->getModule()->getEditLink( $id ));
		} else
			$res->pushError ($this->getModel()->getErrors());
		return $res->ajaxExec();
	}
	protected function _prepareListForTbl($data) {
		if(!empty($data)) {
			foreach($data as $i => $v) {
				$data[ $i ]['label'] = '<a class="" href="'. $this->getModule()->getEditLink($data[ $i ]['id']). '">'. $data[ $i ]['label']. '&nbsp;<i class="fa fa-fw fa-pencil" style="margin-top: 2px;"></i></a>';
				$conversion = 0;
				if(!empty($data[ $i ]['unique_views']) && !empty($data[ $i ]['actions'])) {
					$conversion = number_format( ((int) $data[ $i ]['actions'] / (int) $data[ $i ]['unique_views']), 3);
				}
				$data[ $i ]['conversion'] = $conversion;
				$data[ $i ]['active'] = $data[ $i ]['active'] ? __('Yes', PPS_LANG_CODE) : __('No', PPS_LANG_CODE);
				
				//$data[ $i ]['action'] = '<a class="button" style="margin-right: 10px;" href="'. $this->getModule()->getEditLink($data[ $i ]['id']). '"><i class="fa fa-fw fa-2x fa-pencil" style="margin-top: 2px;"></i></a>';
				//$data[ $i ]['action'] .= '<button href="#" onclick="ppsPopupRemoveRow('. $data[ $i ]['id']. ', this); return false;" title="'. __('Remove', PPS_LANG_CODE). '" class="button"><i class="fa fa-fw fa-2x fa-trash-o" style="margin-top: 5px;"></i></button>';
			}
		}
		return $data;
	}
	protected function _prepareTextLikeSearch($val) {
		$query = '(label LIKE "%'. $val. '%"';
		if(is_numeric($val)) {
			$query .= ' OR id LIKE "%'. (int) $val. '%"';
		}
		$query .= ')';
		return $query;
	}
	protected function _prepareModelBeforeListSelect($model) {
		$this->getModel()->recalculateStatsForPopups();	// This was done for old users - from version 1.0.9, can be removed in future
		$where = 'original_id != 0';
		if($this->getModel()->abDeactivated()) {
			$where .= ' AND ab_id = 0';
		}
		$model->addWhere( $where );
		dispatcherPps::doAction('popupModelBeforeGetList', $model);
		return $model;
	}
	protected function _prepareSortOrder($sortOrder) {
		if($sortOrder == 'conversion') {
			$sortOrder = '(actions / unique_views)';	// Conversion in real-time calculation
		}
		return $sortOrder;
	}
	public function remove() {
		$res = new responsePps();
		if($this->getModel()->remove(reqPps::getVar('id', 'post'))) {
			$res->addMessage(__('Done', PPS_LANG_CODE));
		} else
			$res->pushError($this->getModel()->getErrors());
		$res->ajaxExec();
	}
	public function save() {
		$res = new responsePps();
		if($this->getModel()->save( reqPps::get('post') )) {
			$res->addMessage(__('Done', PPS_LANG_CODE));
		} else
			$res->pushError($this->getModel()->getErrors());
		$res->ajaxExec();
	}
	public function getPreviewHtml() {
		$this->_prevPopupId = (int) reqPps::getVar('id', 'get');
		add_action('init', array($this, 'outPreviewHtml'));
	}
	public function outPreviewHtml() {
		if($this->_prevPopupId) {
			echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
			<html><head></head><body>';
			echo $this->getView()->generateHtml( $this->_prevPopupId );
			echo '<body></html>';
		}
		exit();
	}
	public function changeTpl() {
		$res = new responsePps();
		if($this->getModel()->changeTpl(reqPps::get('post'))) {
			$res->addMessage(__('Done', PPS_LANG_CODE));
			$id = (int) reqPps::getVar('id', 'post');
			$res->addData('edit_link', $this->getModule()->getEditLink( $id ));
		} else
			$res->pushError ($this->getModel()->getErrors());
		return $res->ajaxExec();
	}
	public function exportForDb() {
		$eol = "\r\n";
		$selectColumns = array('id','label','active','original_id','params','html','css','img_preview','show_on','show_to','show_pages','type_id','date_created');
		$popupList = dbPps::get('SELECT '. implode(',', $selectColumns). ' FROM @__popup WHERE original_id = 0');
		$valuesArr = array();
		
		$allKeys = array();
		foreach($popupList as $popup) {
			$arr = array();
			$addToKeys = empty($allKeys);
			foreach($popup as $k => $v) {
				if(!in_array($k, $selectColumns)) continue;
				if($addToKeys) {
					$allKeys[] = $k;
				}
				$arr[] = '"'. mysql_real_escape_string($v). '"';
			}
			$valuesArr[] = '('. implode(',', $arr). ')';
		}
		$query = 'INSERT INTO @__popup ('. implode(',', $allKeys). ') VALUES '. $eol. implode(','. $eol, $valuesArr);
		echo $query;
		exit();
	}
	public function saveAsCopy() {
		$res = new responsePps();
		if(($id = $this->getModel()->saveAsCopy(reqPps::get('post'))) != false) {
			$res->addMessage(__('Done, redirecting to new PopUp...', PPS_LANG_CODE));
			$res->addData('edit_link', $this->getModule()->getEditLink( $id ));
		} else
			$res->pushError ($this->getModel()->getErrors());
		return $res->ajaxExec();
	}
	public function switchActive() {
		$res = new responsePps();
		if($this->getModel()->switchActive(reqPps::get('post'))) {
			$res->addMessage(__('Done', PPS_LANG_CODE));
		} else
			$res->pushError ($this->getModel()->getErrors());
		return $res->ajaxExec();
	}
	public function getPermissions() {
		return array(
			PPS_USERLEVELS => array(
				PPS_ADMIN => array('createFromTpl', 'getListForTbl', 'remove', 'removeGroup', 'clear', 
					'save', 'getPreviewHtml', 'exportForDb', 'changeTpl', 'saveAsCopy', 'switchActive', 'outPreviewHtml')
			),
		);
	}
}

