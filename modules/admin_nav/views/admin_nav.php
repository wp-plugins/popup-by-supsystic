<?php
class admin_navViewPps extends viewPps {
	public function getBreadcrumbs() {
		$this->assign('breadcrumbsList', $this->getModule()->getBreadcrumbsList());
		return parent::getContent('adminNavBreadcrumbs');
	}
}
