<?php
class popupControllerPps extends controllerPps {
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
				$data[ $i ]['action'] = '<a class="button" style="margin-right: 10px;" href="'. $this->getModule()->getEditLink($data[ $i ]['id']). '"><i class="fa fa-fw fa-2x fa-pencil" style="margin-top: 2px;"></i></a>';
				$data[ $i ]['action'] .= '<button href="#" onclick="ppsPopupRemoveRow('. $data[ $i ]['id']. ', this); return false;" title="'. __('Remove', PPS_LANG_CODE). '" class="button"><i class="fa fa-fw fa-2x fa-trash-o" style="margin-top: 5px;"></i></button>';
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
		//$model->setSelectFields('id, label, date_created');
		$model->addWhere('original_id != 0');
		return $model;
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
		$id = (int) reqPps::getVar('id', 'get');
		if($id) {
			echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
			<html><head>
				<style type="text/css">
					.ppsPopupShell {
						/*position: absolute;
						top: 0;
						left: 0;*/
						/*max-width: calc(100% - 610px) !important;*/
					}
				</style></head><body>';
			echo $this->getView()->generateHtml($id);
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
		$popupList = dbPps::get('SELECT * FROM @__popup WHERE original_id = 0');
		$valuesArr = array();
		
		foreach($popupList as $popup) {
			$arr = array();
			foreach($popup as $k => $v) {
				$arr[] = '"'. mysql_real_escape_string($v). '"';
			}
			$valuesArr[] = '('. implode(',', $arr). ')';
		}
		$query = 'INSERT INTO @__popup ('. implode(',', array_keys($popupList[0])). ') VALUES '. $eol. implode(','. $eol, $valuesArr);
		echo $query;
		exit();
	}
	public function getPermissions() {
		return array(
			PPS_USERLEVELS => array(
				PPS_ADMIN => array('createFromTpl', 'getListForTbl', 'remove', 'removeGroup', 'clear', 'save', 'getPreviewHtml', 'exportForDb', 'changeTpl')
			),
		);
	}
}

