<?php
class subscribeModelPps extends modelPps {
	public function __construct() {
		$this->_setTbl('subscribers');
	}
	public function subscribe($d = array(), $validateIp = false) {
		$id = isset($d['id']) ? $d['id'] : 0;
		if($id) {
			$popup = framePps::_()->getModule('popup')->getModel()->getById($id);
			if($popup && isset($popup['params']) 
				&& isset($popup['params']['tpl']['enb_subscribe']) 
				&& isset($popup['params']['tpl']['sub_dest'])
			) {
				$dest = $popup['params']['tpl']['sub_dest'];
				$subMethod = 'subscribe_'. $dest;
				if(method_exists($this, $subMethod)) {
					return $this->$subMethod($d, $popup, $validateIp);
				} else
					$this->pushError (__('Something goes wrong', PPS_LANG_CODE));
			} else
				$this->pushError (__('Empty or invalid ID', PPS_LANG_CODE));
		} else
			$this->pushError (__('Empty or invalid ID', PPS_LANG_CODE));
		return false;
	}
	public function subscribe_wordpress($d, $popup, $validateIp = false) {
		$email = isset($d['email']) ? trim($d['email']) : false;
		if(!empty($email)) {
			if(is_email($email)) {
				if(!email_exists($email)) {
					if(!$validateIp || $validateIp && $this->_checkOftenAccess()) {
						$username = '';
						if(isset($popup['params']['tpl']['enb_sub_name']) && $popup['params']['tpl']['enb_sub_name']) {
							$username = trim($d['name']);
						}
						$username = $this->_getUsernameFromEmail($email, $username);
						$confirmHash = md5($email. NONCE_KEY);
						if($this->insert(array(
							'username' => $username,
							'email' => $email,
							'hash' => $confirmHash,
						))) {
							$this->sendWpUserConfirm($username, $email, $confirmHash);
							return true;
						}
						/*$password = wp_generate_password();
						$userId = wp_create_user($username, $password, $email);
						if($userId && !is_wp_error($userId)) {
							if(!function_exists('wp_new_user_notification')) {
								framePps::_()->loadPlugins();
							}
							wp_new_user_notification($userId, $password);
							return true;
						} else {
							$this->pushError (is_wp_error($userId) ? $userId->get_error_message() : __('Can\'t subscribe for now. Please try again latter.', PPS_LANG_CODE));
						}*/
					}
				} else
					$this->pushError (__('Empty or invalid email', PPS_LANG_CODE), 'email');
			} else
				$this->pushError (__('Empty or invalid email', PPS_LANG_CODE), 'email');
		} else
			$this->pushError (__('Empty or invalid email', PPS_LANG_CODE), 'email');
		return false;
	}
	public function sendWpUserConfirm($username, $email, $confirmHash) {
		$blogName = get_bloginfo('name');
		$adminEmail = get_bloginfo('admin_email');
		$siteUrl = get_bloginfo('wpurl');
		$confirmUrl = uriPps::mod('subscribe', 'confirm', array('email' => $email, 'hash' => $confirmHash));
		framePps::_()->getModule('mail')->send($email,
			sprintf(__('Confirm subscription on %s', PPS_LANG_CODE), $blogName),
			sprintf(__('You subscribed on site <a href="%s">%s</a>. Follow <a href="%s">this link</a> to complete your subscription. If you did not subscribe here - just ignore this message.', PPS_LANG_CODE), $siteUrl, $blogName, $confirmUrl),
			$blogName,
			$adminEmail,
			$blogName,
			$adminEmail);
	}
	public function confirm($d = array()) {
		$d['email'] = isset($d['email']) ? trim($d['email']) : '';
		$d['hash'] = isset($d['hash']) ? trim($d['hash']) : '';
		if(!empty($d['email']) && !empty($d['hash'])) {
			$subscriber = $this->setWhere(array(
				'email' => $d['email'],
				'hash' => $d['hash'], 
				'activated' => 0))->getFromTbl(array('return' => 'row'));
			if(!empty($subscriber)) {
				$password = wp_generate_password();
				$userId = wp_create_user($subscriber['username'], $password, $subscriber['email']);
				if($userId && !is_wp_error($userId)) {
					if(!function_exists('wp_new_user_notification')) {
						framePps::_()->loadPlugins();
					}
					wp_new_user_notification($userId, $password);
					$this->update(array('activated' => 1), array('id' => $subscriber['id']));
					return true;
				} else {
					$this->pushError (is_wp_error($userId) ? $userId->get_error_message() : __('Can\'t subscribe for now. Please try again latter.', PPS_LANG_CODE));
				}
			}
		}
		// One and same error for all other cases
		$this->pushError(__('Send me some info, pls', PPS_LANG_CODE));
		return false;
	}
	private function _checkOftenAccess() {
		$ip = utilsPps::getIP();
		if(empty($ip)) {
			$this->pushError(__('Can\'t detect your IP, please don\'t spam', PPS_LANG_CODE));
			return false;
		}
		$accessByIp = get_option(PPS_CODE. '_access_py_ip');
		if(empty($accessByIp)) {
			$accessByIp = array();
		}
		$time = time();
		$break = false;
		// Clear old values
		if(!empty($accessByIp)) {
			foreach($accessByIp as $k => $v) {
				if($time - (int) $v >= 3600)
					unset($accessByIp[ $k ]);
			}
		}
		if(isset($accessByIp[ $ip ])) {
			if($time - (int) $accessByIp[ $ip ] <= 30 * 60) {
				$break = true;
			} else
				$accessByIp[ $ip ] = $time;
		} else {
			$accessByIp[ $ip ] = $time;
		}
		update_option(PPS_CODE. '_access_py_ip', $accessByIp);
		if($break) {
			$this->pushError(__('You just subscribed from this IP', PPS_LANG_CODE));
			return false;
		}
		return true;
	}
	private function _getUsernameFromEmail($email, $username = '') {
		if(!empty($username)) {
			if(username_exists($username)) {
				return $this->_getUsernameFromEmail($email, $username. mt_rand(1, 9999));
			}
			return $username;
		} else {
			$nameHost = explode('@', $email);
			if(username_exists($nameHost[0])) {
				return $this->_getUsernameFromEmail($nameHost[0]. mt_rand(1, 9999). '@'. $nameHost[1], $name);
			}
			return $nameHost[0];
		}
	}
}
