<?php
class statisticsViewPps extends viewPps {
	public function getPopupEditTab($popup) {
		framePps::_()->getModule('templates')->loadJqplot();
		framePps::_()->addScript('admin.statistics.popup.edit', $this->getModule()->getModPath(). 'js/admin.statistics.popup.edit.js');
		
		$allStats = $this->getModel()->getAllForPopupId($popup['id']);
		$allStats = dispatcherPps::applyFilters('popupStatsAdminData', $allStats, $popup);
		$haveData = $allStats ? true : false;
		if($haveData) {
			framePps::_()->addJSVar('admin.statistics.popup.edit', 'ppsPopupAllStats', $allStats);
			$allSmAction = $this->getModel()->getSmActionForPopup( $popup['id'] );
			$allSmAction = dispatcherPps::applyFilters('popupShareStatsAdminData', $allSmAction, $popup);
			if(!empty($allSmAction)) {
				framePps::_()->addJSVar('admin.statistics.popup.edit', 'ppsPopupAllShareStats', $allSmAction);
			}
		}
		
		$this->assign('haveData', $haveData);
		$this->assign('popup', $popup);
		return parent::getContent('statPopupEditTab');
	}
}
