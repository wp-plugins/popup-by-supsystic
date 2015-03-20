<?php
class subscribeModelPps extends modelPps {
	private $_dest = '';
	private $_lastPopup = null;	// Some small internal caching
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
					$this->_dest = $dest;
					$this->_lastPopup = $popup;
					return $this->$subMethod($d, $popup, $validateIp);
				} else
					$this->pushError (__('Something goes wrong', PPS_LANG_CODE));
			} else
				$this->pushError (__('Empty or invalid ID', PPS_LANG_CODE));
		} else
			$this->pushError (__('Empty or invalid ID', PPS_LANG_CODE));
		return false;
	}
	public function getDest() {
		return $this->_dest;
	}
	public function getLastPopup() {
		return $this->_lastPopup;
	}
	private function _checkOftenAccess($d = array()) {
		$onlyCheck = isset($d['only_check']) ? $d['only_check'] : false;
		$onlyAdd = isset($d['only_add']) ? $d['only_add'] : false;
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
		if($onlyAdd) {
			$accessByIp[ $ip ] = $time;
			update_option(PPS_CODE. '_access_py_ip', $accessByIp);
			return true;
		}
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
		if(!$onlyCheck)
			update_option(PPS_CODE. '_access_py_ip', $accessByIp);
		if($break) {
			$this->pushError(__('You just subscribed from this IP', PPS_LANG_CODE));
			return false;
		}
		return true;
	}
	private function _getInvalidEmailMsg($popup) {
		return isset($popup['params']['tpl']['sub_txt_invalid_email'])
			? $popup['params']['tpl']['sub_txt_invalid_email']
			: __('Empty or invalid email', PPS_LANG_CODE);
	}
	/**
	 * WordPress subscribe functionality
	 */
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
						if(isset($popup['params']['tpl']['sub_ignore_confirm']) && $popup['params']['tpl']['sub_ignore_confirm']) {
							return $this->createWpSubscriber($popup, $email, $username);
						} else {
							$confirmHash = md5($email. NONCE_KEY);
							if($this->insert(array(
								'username' => $username,
								'email' => $email,
								'hash' => $confirmHash,
								'popup_id' => $popup['id'],
							))) {
								$this->sendWpUserConfirm($username, $email, $confirmHash, $popup);
								return true;
							}
						}
					}
				} else
					$this->pushError ($this->_getInvalidEmailMsg($popup), 'email');
			} else
				$this->pushError ($this->_getInvalidEmailMsg($popup), 'email');
		} else
			$this->pushError ($this->_getInvalidEmailMsg($popup), 'email');
		return false;
	}
	public function createWpSubscriber($popup, $email, $username) {
		$password = wp_generate_password();
		$userId = wp_create_user($username, $password, $email);
		if($userId && !is_wp_error($userId)) {
			if(!function_exists('wp_new_user_notification')) {
				framePps::_()->loadPlugins();
			}
			// If there was selected some special role - check it here
			$this->_lastPopup = $popup;
			if(isset($popup['params']['tpl']['sub_wp_create_user_role']) 
				&& !empty($popup['params']['tpl']['sub_wp_create_user_role']) 
				&& $popup['params']['tpl']['sub_wp_create_user_role'] != 'subscriber'
			) {
				$user = new WP_User($userId);
				$user->set_role( $popup['params']['tpl']['sub_wp_create_user_role'] );
			}
			wp_new_user_notification($userId, $password);
			return true;
		} else {
			$this->pushError (is_wp_error($userId) ? $userId->get_error_message() : __('Can\'t subscribe for now. Please try again latter.', PPS_LANG_CODE));
		}
		return false;
	}
	public function sendWpUserConfirm($username, $email, $confirmHash, $popup) {
		$blogName = get_bloginfo('name');
		$replaceVariables = array(
			'sitename' => $blogName,
			'siteurl' => get_bloginfo('wpurl'),
			'confirm_link' => uriPps::mod('subscribe', 'confirm', array('email' => $email, 'hash' => $confirmHash)),
		);
		$adminEmail = get_bloginfo('admin_email');
		$confirmSubject = isset($popup['params']['tpl']['sub_txt_confirm_mail_subject']) && !empty($popup['params']['tpl']['sub_txt_confirm_mail_subject'])
				? $popup['params']['tpl']['sub_txt_confirm_mail_subject']
				: __('Confirm subscription on [sitename]', PPS_LANG_CODE);
		$confirmContent = isset($popup['params']['tpl']['sub_txt_confirm_mail_message']) && !empty($popup['params']['tpl']['sub_txt_confirm_mail_message'])
				? $popup['params']['tpl']['sub_txt_confirm_mail_message']
				: __('You subscribed on site <a href="[siteurl]">[sitename]</a>. Follow <a href="[confirm_link]">this link</a> to complete your subscription. If you did not subscribe here - just ignore this message.', PPS_LANG_CODE);
		foreach($replaceVariables as $k => $v) {
			$confirmSubject = str_replace('['. $k. ']', $v, $confirmSubject);
			$confirmContent = str_replace('['. $k. ']', $v, $confirmContent);
		}
		framePps::_()->getModule('mail')->send($email,
			$confirmSubject,
			$confirmContent,
			$blogName,
			$adminEmail,
			$blogName,
			$adminEmail);
	}
	public function confirm($d = array()) {
		$d['email'] = isset($d['email']) ? trim($d['email']) : '';
		$d['hash'] = isset($d['hash']) ? trim($d['hash']) : '';
		$popup = array();
		if(!empty($d['email']) && !empty($d['hash'])) {
			$subscriber = $this->setWhere(array(
				'email' => $d['email'],
				'hash' => $d['hash'], 
				'activated' => 0))->getFromTbl(array('return' => 'row'));
			if(!empty($subscriber)) {
				if(isset($subscriber['popup_id']) && !empty($subscriber['popup_id'])) {
					$popup = framePps::_()->getModule('popup')->getModel()->getById($subscriber['popup_id']);
					$this->_lastPopup = $popup;
				}
				$res = $this->createWpSubscriber($popup, $subscriber['email'], $subscriber['username']);
				if($res) {
					$this->update(array('activated' => 1), array('id' => $subscriber['id']));
				}
				return $res;
			}
		}
		// One and same error for all other cases
		$this->pushError(__('Send me some info, pls', PPS_LANG_CODE));
		return false;
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
	/**
	 * MailChimp functions
	 */
	private function _getMailchimpInst($key) {
		static $instances = array();
		if(!isset($instances[ $key ])) {
			if(!class_exists('mailChimpClientPps'))
				require_once($this->getModule()->getModDir(). 'classes'. DS. 'mailChimpClient.php');
			$instances[ $key ] = new mailChimpClientPps( $key );
		}
		return $instances[ $key ];
	}
	public function isMailchimpSupported() {
		if(!function_exists('curl_init')) {
			$this->pushError(__('MailChimp require CURL to be setup on your server. Please contact your hosting provider and ask them to setup CURL libruary for you.', PPS_LANG_CODE));
			return false;
		}
		return true;
	}
	public function getMailchimpLists($d = array()) {
		if(!$this->isMailchimpSupported())
			return false;
		$key = isset($d['key']) ? trim($d['key']) : '';
		if(!empty($key)) {
			$client = $this->_getMailchimpInst( $key );
			$apiRes = $client->call('lists/list');
			if($apiRes && is_array($apiRes) && isset($apiRes['data']) && !empty($apiRes['data'])) {
				$listsDta = array();
				foreach($apiRes['data'] as $list) {
					$listsDta[ $list['id'] ] = $list['name'];
				}
				return $listsDta;
			} else {
				if(isset($apiRes['errors']) && !empty($apiRes['errors'])) {
					$this->pushError($apiRes['errors']);
				} else {
					$this->pushError(__('There was some problem while trying to get your lists. Make sure that your API key is correct.', PPS_LANG_CODE));
				}
			}
		} else
			$this->pushError(__('Empty API key', PPS_LANG_CODE));
		return false;
	}
	public function subscribe_mailchimp($d, $popup, $validateIp = false) {
		$email = isset($d['email']) ? trim($d['email']) : false;
		if(!empty($email)) {
			if(is_email($email)) {
				if(!$this->isMailchimpSupported())
					return false;
				$lists = isset($popup['params']['tpl']['sub_mailchimp_lists']) ? $popup['params']['tpl']['sub_mailchimp_lists'] : array();
				$apiKey = isset($popup['params']['tpl']['sub_mailchimp_api_key']) ? $popup['params']['tpl']['sub_mailchimp_api_key'] : array();
				if(!empty($lists)) {
					if(!empty($apiKey)) {
						if(!$validateIp || $validateIp && $this->_checkOftenAccess(array('only_check' => true))) {
							$name = '';
							if(isset($popup['params']['tpl']['enb_sub_name']) && $popup['params']['tpl']['enb_sub_name']) {
								$name = trim($d['name']);
							}
							$client = $this->_getMailchimpInst( $apiKey );
							$member = array(
								'email' => $email,
							);
							$dataToSend = array('email' => $member);
							if(!empty($name)) {
								$firstLastNames = array_map('trim', explode(' ', $name));
								$dataToSend['merge_vars']['FNAME'] = $firstLastNames[ 0 ];
								if(isset($firstLastNames[ 1 ]) && !empty($firstLastNames[ 1 ])) {
									$dataToSend['merge_vars']['LNAME'] = $firstLastNames[ 1 ];
								}
							}
							foreach($lists as $listId) {
								$dataToSend['id'] = $listId;
								$res = $client->call('lists/subscribe', $dataToSend);
								if(!$res) {
									$this->pushError (__('Something going wrong while trying to send data to MailChimp. Please contact site owner.', PPS_LANG_CODE));
									return false;
								} elseif(isset($res['status']) && $res['status'] == 'error') {
									$this->pushError ( $res['error'] );
									return false;
								}
							}
							if($validateIp) {
								$this->_checkOftenAccess(array('only_add' => true));
							}
							return true;
						}
					} else
						$this->pushError (__('No API key entered in admin area - contact site owner to resolve this issue.', PPS_LANG_CODE));
				} else
					$this->pushError (__('No lists to add selected in admin area - contact site owner to resolve this issue.', PPS_LANG_CODE));
			} else
				$this->pushError ($this->_getInvalidEmailMsg($popup), 'email');
		} else
			$this->pushError ($this->_getInvalidEmailMsg($popup), 'email');
		return false;
	}
}
