<?php
class subscribeViewPps extends viewPps {
	public function generateFormStart_wordpress($popup) {
		return $this->_generateFormStartCommon($popup, 'wordpress');
	}
	public function generateFormEnd_wordpress($popup) {
		return $this->_generateFormEndCommon($popup);
	}
	public function generateFormStart_aweber($popup) {
		return '<form class="ppsSubscribeForm ppsSubscribeForm_aweber" method="post" action="http://www.aweber.com/scripts/addlead.pl">';
	}
	public function generateFormEnd_aweber($popup) {
		$res = '';
		$res .= htmlPps::hidden('listname', array('value' => $popup['params']['tpl']['sub_aweber_listname']));
		$res .= htmlPps::hidden('meta_message', array('value' => '1'));
		$res .= htmlPps::hidden('meta_required', array('value' => 'email'));
		$res .= htmlPps::hidden('redirect', array('value' => uriPps::getFullUrl()));
		$res .= '</form>';
		return $res;
	}
	public function generateFormStart_mailchimp($popup) {
		return $this->_generateFormStartCommon($popup, 'mailchimp');
	}
	public function generateFormEnd_mailchimp($popup) {
		return $this->_generateFormEndCommon($popup);
	}
	private function _generateFormStartCommon($popup, $key = '') {
		return '<form class="ppsSubscribeForm'. (empty($key) ? '' : ' ppsSubscribeForm_'. $key).'" action="">';
	}
	private function _generateFormEndCommon($popup) {
		$res = '';
		$res .= htmlPps::hidden('mod', array('value' => 'subscribe'));
		$res .= htmlPps::hidden('action', array('value' => 'subscribe'));
		$res .= htmlPps::hidden('id', array('value' => $popup['id']));
		$res .= htmlPps::hidden('_wpnonce', array('value' => wp_create_nonce('subscribe-'. $popup['id'])));
		$res .= '<div class="ppsSubMsg"></div>';
		$res .= '</form>';
		return $res;
	}
	public function displaySuccessPage($popup, $res) {
		$this->assign('popup', $popup);
		$this->assign('res', $res);
		parent::display('subSuccessPage');
	}
}
