<?php
class popupModelPps extends modelPps {
	private $_showToList = array();
	private $_showPagesList = array();
	private $_showOnList = array();
	private $_types = array();
	public function __construct() {
		$this->_setTbl('popup');
	}
	public function abDeactivated() {
		if(framePps::_()->licenseDeactivated()) {
			return (bool) dbPps::exist('@__'. $this->_tbl, 'ab_id');
		}
		return false;
	}
	/**
	 * Exclude some data from list - to avoid memory overload
	 */
	public function getSimpleList($where = array(), $params = array()) {
		if($where)
			$this->setWhere ($where);
		return $this->setSelectFields('id, label, original_id, img_preview, type_id')->getFromTbl( $params );
	}
	protected function _prepareParamsAfterDb($params) {
		if(is_array($params)) {
			foreach($params as $k => $v) {
				$params[ $k ] = $this->_prepareParamsAfterDb( $v ); 
			}
		} else
			$params = stripslashes ($params);
		return $params;
	}
	protected function _beforeDbReplace($data) {
		static $modUrl, $siteUrl;
		if(is_array($data)) {
			foreach($data as $k => $v) {
				$data[ $k ] = $this->_beforeDbReplace($v);
			}
		} else {
			if(!$modUrl)
				$modUrl = $this->getModule()->getModPath();
			if(!$siteUrl)
				$siteUrl = PPS_SITE_URL;
			$data = str_replace($siteUrl, '[PPS_SITE_URL]', str_replace($modUrl, '[PPS_MOD_URL]', $data));
		}
		return $data;
	}
	protected function _afterDbReplace($data) {
		static $modUrl, $siteUrl;
		if(is_array($data)) {
			foreach($data as $k => $v) {
				$data[ $k ] = $this->_afterDbReplace($v);
			}
		} else {
			if(!$modUrl)
				$modUrl = $this->getModule()->getModPath();
			if(!$siteUrl)
				$siteUrl = PPS_SITE_URL;
			$data = str_replace('[PPS_SITE_URL]', $siteUrl, str_replace('[PPS_MOD_URL]', $modUrl, $data));
		}
		return $data;
	}
	protected function _afterGetFromTbl($row) {
		if(isset($row['params']))
			$row['params'] = $this->_prepareParamsAfterDb( utilsPps::unserialize( base64_decode($row['params']) ) );
		if(empty($row['img_preview'])) {
			$row['img_preview'] = str_replace(' ', '-', strtolower( trim($row['label']) )). '.jpg';
		}
		$row['img_preview_url'] = uriPps::_($this->getModule()->getModPath(). 'img/preview/'. $row['img_preview']);
		$row['view_id'] = $row['id']. '_'. mt_rand(1, 999999);
		$row = $this->_afterDbReplace($row);
		$this->getTypes();
		$row['type'] = isset($row['type_id']) && isset($this->_types[ $row['type_id'] ]) ? $this->_types[ $row['type_id'] ]['code'] : 'common';
		return $row;
	}
	protected function _dataSave($data, $update = false) {
		$data = $this->_beforeDbReplace($data);
		$data['params'] = base64_encode(utilsPps::serialize( $data['params'] ));
		return $data;
	}
	protected function _escTplData($data) {
		$data['html'] = dbPps::escape($data['html']);
		$data['css'] = dbPps::escape($data['css']);
		return $data;
	}
	public function createFromTpl($d = array()) {
		$d['label'] = isset($d['label']) ? trim($d['label']) : '';
		$d['original_id'] = isset($d['original_id']) ? (int) $d['original_id'] : 0;
		if(!empty($d['label'])) {
			if(!empty($d['original_id'])) {
				$original = $this->getById($d['original_id']);
				unset($original['id']);
				$original['label'] = $d['label'];
				$original['original_id'] = $d['original_id'];
				framePps::_()->getModule('supsystic_promo')->getModel()->saveUsageStat('create_from_tpl.'. strtolower(str_replace(' ', '-', $original['label'])));
				return $this->insertFromOriginal( $original );
			} else
				$this->pushError (__('Please select PopUp template from list below', PPS_LANG_CODE), 'label');
		} else
			$this->pushError (__('Please enter Name', PPS_LANG_CODE), 'label');
		return false;
	}
	public function insertFromOriginal($original) {
		$original = $this->_escTplData( $original );
		return $this->insert( $original );
	}
	public function remove($id) {
		$id = (int) $id;
		if($id) {
			if(framePps::_()->getTable( $this->_tbl )->delete(array('id' => $id))) {
				return true;
			} else
				$this->pushError (__('Database error detected', PPS_LANG_CODE));
		} else
			$this->pushError(__('Invalid ID', PPS_LANG_CODE));
		return false;
	}
	/**
	 * Do not remove pre-set templates
	 */
	public function clear() {
		if(framePps::_()->getTable( $this->_tbl )->delete(array('additionalCondition' => 'original_id != 0'))) {
			return true;
		} else 
			$this->pushError (__('Database error detected', PPS_LANG_CODE));
		return false;
	}
	public function save($d = array()) {
		$popup = $this->getById($d['id']);
		if(in_array($popup['type'], array(PPS_FB_LIKE))) {
			$d['params']['tpl']['fb_like_opts']['href'] = trim( $d['params']['tpl']['fb_like_opts']['href'] );
			if(empty($d['params']['tpl']['fb_like_opts']['href'])) {
				$this->pushError(__('Enter your Facebook page URL', PPS_LANG_CODE), 'params[tpl][fb_like_opts][href]');
				return false;
			}
		}
		if(in_array($popup['type'], array(PPS_VIDEO))) {
			$d['params']['tpl']['video_url'] = trim( $d['params']['tpl']['video_url'] );
			if(empty($d['params']['tpl']['video_url'])) {
				$this->pushError(__('Enter your video URL', PPS_LANG_CODE), 'params[tpl][video_url]');
				return false;
			}
		}
		if(isset($d['params']['opts_attrs']['txt_block_number']) && !empty($d['params']['opts_attrs']['txt_block_number'])) {
			for($i = 0; $i < (int) $d['params']['opts_attrs']['txt_block_number']; $i++) {
				$sendValKey = 'params_tpl_txt_val_'. $i;
				if(isset($d[ $sendValKey ])) {
					$d['params']['tpl']['txt_'. $i] = urldecode( $d[ $sendValKey ] );
				}
			}
		}
		$this->getShowOnList();
		$this->getShowToList();
		$this->getShowPagesList();
		
		$d['show_on'] = isset($d['params']['main']['show_on']) ? $this->_showOnList[ $d['params']['main']['show_on'] ]['id'] : 0;
		$d['show_to'] = isset($d['params']['main']['show_to']) ? $this->_showToList[ $d['params']['main']['show_to'] ]['id'] : 0;
		$d['show_pages'] = isset($d['params']['main']['show_pages']) ? $this->_showPagesList[ $d['params']['main']['show_pages'] ]['id'] : 0;
		
		$res = $this->updateById($d);
		if($res) {
			$currentPopup = $this->getById($d['id']);
			$difs = $this->getDifferences($popup, $currentPopup);
			if(!empty($difs)) {
				foreach($difs as $dif) {
					framePps::_()->getModule('supsystic_promo')->getModel()->saveUsageStat('save_popup.'. $dif);
				}
			}
			$this->_bindShowToPages( $d );
		}
		return $res;
	}
	private function _bindShowToPages( $d ) {
		$id = (int) $d['id'];
		if($id) {
			framePps::_()->getTable('popup_show_pages')->delete(array('popup_id' => $id));
			$insertArr = array();
			if(isset($d['show_pages_list']) && !empty($d['show_pages_list'])) {
				foreach($d['show_pages_list'] as $postId) {
					$insertArr[] = "($id, $postId, 0)";
				}
			}
			if(isset($d['not_show_pages_list']) && !empty($d['not_show_pages_list'])) {
				foreach($d['not_show_pages_list'] as $postId) {
					$insertArr[] = "($id, $postId, 1)";
				}
			}
			if(!empty($insertArr)) {
				dbPps::query('INSERT INTO @__popup_show_pages (popup_id, post_id, not_show) VALUES '. implode(',', $insertArr));
			}
		}
	}
	public function getShowToList() {
		if(empty($this->_showToList)) {
			$this->_showToList = array(
				'everyone' => array('id' => 1),
				'first_time_visit' => array('id' => 2),
				'for_countries' => array('id' => 3),
			);
		}
		return $this->_showToList;
	}
	public function getShowPagesList() {
		if(empty($this->_showPagesList)) {
			$this->_showPagesList = array(
				'all' => array('id' => 1),
				'show_on_pages' => array('id' => 2),
				'not_show_on_pages' => array('id' => 3),
			);
		}
		return $this->_showPagesList;
	}
	public function getShowOnList() {
		if(empty($this->_showOnList)) {
			$this->_showOnList = dispatcherPps::applyFilters('popupShowOnList', array(
				'page_load' => array('id' => 1),
				'click_on_page' => array('id' => 2),
				'click_on_element' => array('id' => 3),
				'scroll_window' => array('id' => 4),
			));
		}
		return $this->_showPagesList;
	}
	public function getById($id) {
		$data = parent::getById($id);
		if($data) {
			$data['show_pages_list'] = framePps::_()->getTable('popup_show_pages')->get('*', array('popup_id' => $id));
		}
		return $data;
	}
	public function getTypes() {
		if(empty($this->_types)) {
			$this->_types =  dispatcherPps::applyFilters('popupTypesList', array(
				1 => array('code' => 'common', 'label' => __('Common', PPS_LANG_CODE)),
				2 => array('code' => 'fb_like', 'label' => __('Facebook Like', PPS_LANG_CODE)),
				3 => array('code' => 'video', 'label' => __('Video', PPS_LANG_CODE)),
			));
		}
		return $this->_types;
	}
	public function changeTpl($d = array()) {
		$d['id'] = isset($d['id']) ? (int) $d['id'] : 0;
		$d['new_tpl_id'] = isset($d['new_tpl_id']) ? (int) $d['new_tpl_id'] : 0;
		if($d['id'] && $d['new_tpl_id']) {
			$currentPopup = $this->getById( $d['id'] );
			$newTpl = $this->getById( $d['new_tpl_id'] );
			$originalPopup = $this->getById( $currentPopup['original_id'] );
			$diffFromOriginal = $this->getDifferences($currentPopup, $originalPopup);
			if(!empty($diffFromOriginal)) {
				if(isset($newTpl['params'])) {
					$keysForMove = array('params.tpl.label', 'params.tpl.anim_key', 'params.tpl.enb_foot_note', 'params.tpl.foot_note',
						'params.tpl.enb_sm',
						'params.tpl.enb_subscribe');
					foreach($diffFromOriginal as $k) {
						if(in_array($k, $keysForMove)
							|| strpos($k, 'params.tpl.enb_sm_') === 0 
							|| strpos($k, 'params.tpl.sm_') === 0 
							|| strpos($k, 'params.tpl.enb_sub_') === 0 
							|| strpos($k, 'params.tpl.sub_') === 0
							|| strpos($k, 'params.tpl.enb_txt_') === 0
							|| strpos($k, 'params.tpl.txt_') === 0
						) {
							$this->_assignKeyArr($currentPopup, $newTpl, $k);
						}
					}
				}
			}
			$newTpl['original_id'] = $newTpl['id'];	// It will be our new original
			$newTpl['id'] = $currentPopup['id'];
			$newTpl['label'] = $currentPopup['label'];
			$newTpl = dispatcherPps::applyFilters('popupChangeTpl', $newTpl, $currentPopup);
			$newTpl = $this->_escTplData( $newTpl );
			framePps::_()->getModule('supsystic_promo')->getModel()->saveUsageStat('change_to_tpl.'. strtolower(str_replace(' ', '-', $newTpl['label'])));
			return $this->update( $newTpl, array('id' => $newTpl['id']) );
		} else
			$this->pushError (__('Provided data was corrupted', PPS_LANG_CODE));
		return false;
	}
	private function _assignKeyArr($from, &$to, $key) {
		$subKeys = explode('.', $key);	
		// Yeah, hardcode, I know.............
		switch(count($subKeys)) {
			case 4:
				if(isset( $from[ $subKeys[0] ][ $subKeys[1] ][ $subKeys[2] ][ $subKeys[3] ] ))
					$to[ $subKeys[0] ][ $subKeys[1] ][ $subKeys[2] ][ $subKeys[3] ] = $from[ $subKeys[0] ][ $subKeys[1] ][ $subKeys[2] ][ $subKeys[3] ];
				else
					unset($to[ $subKeys[0] ][ $subKeys[1] ][ $subKeys[2] ][ $subKeys[3] ]);
				break;
			case 3:
				if(isset( $from[ $subKeys[0] ][ $subKeys[1] ][ $subKeys[2] ] ))
					$to[ $subKeys[0] ][ $subKeys[1] ][ $subKeys[2] ] = $from[ $subKeys[0] ][ $subKeys[1] ][ $subKeys[2] ];
				else
					unset($to[ $subKeys[0] ][ $subKeys[1] ][ $subKeys[2] ]);
				break;
			case 2:
				if(isset( $from[ $subKeys[0] ][ $subKeys[1] ] ))
					$to[ $subKeys[0] ][ $subKeys[1] ] = $from[ $subKeys[0] ][ $subKeys[1] ];
				else
					unset($to[ $subKeys[0] ][ $subKeys[1] ]);
				break;
			case 1:
				if(isset( $from[ $subKeys[0] ] ))
					$to[ $subKeys[0] ] = $from[ $subKeys[0] ];
				else
					unset( $to[ $subKeys[0] ] );
				break;
		}
	}
	public function getDifferences($popup, $original) {
		$difsFromOriginal = $this->_computeDifferences($popup, $original);
		$difsOfOriginal = $this->_computeDifferences($original, $popup);	// Some options may be present in original, but not present in current popup
		if(!empty($difsFromOriginal) && empty($difsOfOriginal)) {
			return $difsFromOriginal;
		} elseif(empty($difsFromOriginal) && !empty($difsOfOriginal)) {
			return $difsOfOriginal;
		} else {
			$difs = array_merge($difsFromOriginal, $difsOfOriginal);
			return array_unique($difs);
		}
	}
	private function _computeDifferences($popup, $original, $key = '', $keysImplode = array()) {
		$difs = array();
		if(is_array($popup)) {
			$excludeKey = array('id', 'label', 'active', 'original_id', 'img_preview', 'type_id', 
				'date_created', 'view_id', 'img_preview_url', 'show_on', 'show_to', 'show_pages');
			if(!empty($key))
				$keysImplode[] = $key;
			foreach($popup as $k => $v) {
				if(in_array($k, $excludeKey) && empty($key)) continue;
				if(!isset($original[ $k ])) {
					$difs[] = $this->_prepareDiffKeys($k, $keysImplode);
					continue;
				}
				$currDifs = $this->_computeDifferences($popup[ $k ], $original[ $k ], $k, $keysImplode);
				if(!empty($currDifs)) {
					$difs = array_merge($difs, $currDifs);
				}
			}
		} else {
			if($popup != $original) {
				$difs[] = $this->_prepareDiffKeys($key, $keysImplode);
			}
		}
		return $difs;
	}
	private function _prepareDiffKeys($key, $keysImplode) {
		return empty($keysImplode) ? $key : implode('.', $keysImplode). '.'. $key;
	}
	public function clearCachedStats($id) {
		$tbl = $this->getTbl();
		$id = (int) $id;
		return dbPps::query("UPDATE @__$tbl SET `views` = 0, `unique_views` = 0, `actions` = 0 WHERE `id` = $id");
	}
	public function addCachedStat($id, $statColumn) {
		$tbl = $this->getTbl();
		$id = (int) $id;
		return dbPps::query("UPDATE @__$tbl SET `$statColumn` = `$statColumn` + 1 WHERE `id` = $id");
	}
	public function addViewed($id) {
		return $this->addCachedStat($id, 'views');
	}
	public function addUniqueViewed($id) {
		return $this->addCachedStat($id, 'unique_views');
	}
	public function addActionDone($id) {
		return $this->addCachedStat($id, 'actions');
	}
	public function recalculateStatsForPopups() {
		$recalculated = (int)get_option('pps_stats_recalculated');
		if(!$recalculated) {
			update_option('pps_stats_recalculated', 1);
			$allPopups = $this->getSimpleList();
			if(!empty($allPopups)) {
				$statsModel = framePps::_()->getModule('statistics')->getModel();
				foreach($allPopups as $p) {
					if(empty($p['original_id'])) continue;
					$stats = $statsModel->getPreparedStats(array('id' => $p['id']));
					if(!empty($stats)) {
						$total = array_shift($stats);
						foreach($stats as $s) {
							foreach($s as $statKey => $statData) {
								if(is_numeric($statData)) {
									$total[ $statKey ] += $statData;
								}
							}
						}
						$tbl = $this->getTbl();
						framePps::_()->getTable($tbl)->update(array(
							'views' => $total['views'],
							'unique_views' => $total['unique_requests'],
							'actions' => $total['actions'],
						), array(
							'id' => $p['id']
						));
					}
				}
			}
		}
	}
	public function saveAsCopy($d = array()) {
		$d['copy_label'] = isset($d['copy_label']) ? trim($d['copy_label']) : '';
		$d['id'] = isset($d['id']) ? (int) $d['id'] : 0;
		if(!empty($d['copy_label'])) {
			if(!empty($d['id'])) {
				$original = $this->getById($d['id']);
				unset($original['id']);
				unset($original['date_created']);
				$original['label'] = $d['copy_label'];
				$original['views'] = $original['unique_views'] = $original['actions'] = 0;
				framePps::_()->getModule('supsystic_promo')->getModel()->saveUsageStat('save_as_copy');
				return $this->insertFromOriginal( $original );
			} else
				$this->pushError (__('Invalid ID', PPS_LANG_CODE));
		} else
			$this->pushError (__('Please enter Name', PPS_LANG_CODE), 'copy_label');
		return false;
	}
	public function switchActive($d = array()) {
		$d['active'] = isset($d['active']) ? (int)$d['active'] : 0;
		$d['id'] = isset($d['id']) ? (int) $d['id'] : 0;
		if(!empty($d['id'])) {
			$tbl = $this->getTbl();
			return framePps::_()->getTable($tbl)->update(array(
				'active' => $d['active'],
			), array(
				'id' => $d['id'],
			));
		} else
			$this->pushError (__('Invalid ID', PPS_LANG_CODE));
		return false;
	}
}
