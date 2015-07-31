<?php
class subscribeControllerPps extends controllerPps {
	public function subscribe() {
		$res = new responsePps();
		$data = reqPps::get('post');
		$id = isset($data['id']) ? (int) $data['id'] : 0;
		$nonce = $_REQUEST['_wpnonce'];
		if(!wp_verify_nonce($_REQUEST['_wpnonce'], 'subscribe-'. $id)) {
			die('Some error with your request.........');
		}
		if($this->getModel()->subscribe(reqPps::get('post'), true)) {
			$dest = $this->getModel()->getDest();
			$destData = $this->getModule()->getDestByKey( $dest );
			$lastPopup = $this->getModel()->getLastPopup();
			$withoutConfirm = (isset($lastPopup['params']['tpl']['sub_ignore_confirm']) && $lastPopup['params']['tpl']['sub_ignore_confirm'])
				|| (isset($lastPopup['params']['tpl']['sub_dsbl_dbl_opt_id']) && $lastPopup['params']['tpl']['sub_dsbl_dbl_opt_id']);
			if(isset($lastPopup['params']['tpl']['sub_dest']) 
				&& $lastPopup['params']['tpl']['sub_dest'] == 'mailpoet' 
				&& class_exists('WYSIJA')
				&& ($wisijaConfigModel = WYSIJA::get('config', 'model'))
			) {
				$withoutConfirm = !(bool) $wisijaConfigModel->getValue('confirm_dbleoptin');
			}
			$isSubInternal = $this->getModel()->isSubscribedInternal();
			$forceRequireConfirm = false;
			if(!$isSubInternal && framePps::_()->getModule($dest)) {	// Confirm can be required by other subscribe engines
				$forceRequireConfirm = framePps::_()->getModule($dest)->getModel()->requireConfirm();
			}
			if(($destData && isset($destData['require_confirm']) && $destData['require_confirm'] && !$withoutConfirm) || $forceRequireConfirm)
				$res->addMessage(isset($lastPopup['params']['tpl']['sub_txt_confirm_sent']) 
						? $lastPopup['params']['tpl']['sub_txt_confirm_sent'] : 
						__('Confirmation link was sent to your email address. Check your email!', PPS_LANG_CODE));
			else
				$res->addMessage(isset($lastPopup['params']['tpl']['sub_txt_success'])
						? $lastPopup['params']['tpl']['sub_txt_success']
						: __('Thank you for subscribing!', PPS_LANG_CODE));
			$redirectUrl = isset($lastPopup['params']['tpl']['sub_redirect_url']) && !empty($lastPopup['params']['tpl']['sub_redirect_url'])
					? $lastPopup['params']['tpl']['sub_redirect_url']
					: false;
			if(!empty($redirectUrl)) {
				/*$redirectUrl = trim($redirectUrl);
				if(strpos($redirectUrl, 'http') !== 0) {
					$redirectUrl = 'http://'. $redirectUrl;
				}*/
				$res->addData('redirect', uriPps::normal($redirectUrl));
			}
		} else {
			$lastPopup = $this->getModel()->getLastPopup();
			if($lastPopup 
				&& isset($lastPopup['params']['tpl']['sub_redirect_email_exists']) 
				&& !empty($lastPopup['params']['tpl']['sub_redirect_email_exists'])
				&& $this->getModel()->getEmailExists()
			) {
				$res->addData('emailExistsRedirect', uriPps::normal($lastPopup['params']['tpl']['sub_redirect_email_exists']));
			}
			$res->pushError ($this->getModel()->getErrors());
		}
		if(!$res->isAjax()) {
			if(!$res->error()) {
				$popupActions = reqPps::getVar('pps_actions_'. $id, 'cookie');
				if(empty($popupActions)) {
					$popupActions = array();
				}
				$popupActions['subscribe'] = date('m-d-Y H:i:s');
				reqPps::setVar('pps_actions_'. $id, $popupActions, 'cookie', array('expire' => 7 * 24 * 3600));
				framePps::_()->getModule('statistics')->getModel()->add(array(
					'id' => $id,
					'type' => 'subscribe',
				));
			}
			$res->mainRedirect(isset($redirectUrl) && $redirectUrl ? $redirectUrl : '');
		}
		return $res->ajaxExec();
	}
	public function confirm() {
		
		$res = new responsePps();
		if(!$this->getModel()->confirm(reqPps::get('get'))) {
			$res->pushError ($this->getModel()->getErrors());
		}
		$lastPopup = $this->getModel()->getLastPopup();
		$this->getView()->displaySuccessPage($lastPopup, $res);
		exit();
		// Just simple redirect for now
		//$siteUrl = get_bloginfo('wpurl');
		//redirectPps($siteUrl);
	}
	public function getMailchimpLists() {
		$res = new responsePps();
		if(($lists = $this->getModel()->getMailchimpLists(reqPps::get('post'))) !== false) {
			$res->addData('lists', $lists);
		} else
			$res->pushError ($this->getModel()->getErrors());
		return $res->ajaxExec();
	}
	public function getPermissions() {
		return array(
			PPS_USERLEVELS => array(
				PPS_ADMIN => array('getMailchimpLists')
			),
		);
	}
}

