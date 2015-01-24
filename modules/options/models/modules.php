<?php
class modulesModelPps extends modelPps {
    public function get($d = array()) {
        if($d['id'] && is_numeric($d['id'])) {
            $fields = framePps::_()->getTable('modules')->fillFromDB($d['id'])->getFields();
            $fields['types'] = array();
            $types = framePps::_()->getTable('modules_type')->fillFromDB();
            foreach($types as $t) {
                $fields['types'][$t['id']->value] = $t['label']->value;
            }
            return $fields;
        } elseif(!empty($d)) {
            $data = framePps::_()->getTable('modules')->get('*', $d);
            return $data;
        } else {
            return framePps::_()->getTable('modules')
                ->innerJoin(framePps::_()->getTable('modules_type'), 'type_id')
                ->getAll(framePps::_()->getTable('modules')->alias().'.*, '. framePps::_()->getTable('modules_type')->alias(). '.label as type');
        }
        parent::get($d);
    }
    public function put($d = array()) {
        $res = new responsePps();
        $id = $this->_getIDFromReq($d);
        $d = prepareParamsPps($d);
        if(is_numeric($id) && $id) {
            if(isset($d['active']))
                $d['active'] = ((is_string($d['active']) && $d['active'] == 'true') || $d['active'] == 1) ? 1 : 0;           //mmm.... govnokod?....)))
           /* else
                 $d['active'] = 0;*/
            
            if(framePps::_()->getTable('modules')->update($d, array('id' => $id))) {
                $res->messages[] = __('Module Updated', PPS_LANG_CODE);
                $mod = framePps::_()->getTable('modules')->getById($id);
                $newType = framePps::_()->getTable('modules_type')->getById($mod['type_id'], 'label');
                $newType = $newType['label'];
                $res->data = array(
                    'id' => $id, 
                    'label' => $mod['label'], 
                    'code' => $mod['code'], 
                    'type' => $newType,
                    'params' => utilsPps::jsonEncode($mod['params']),
                    'description' => $mod['description'],
                    'active' => $mod['active'], 
                );
            } else {
                if($tableErrors = framePps::_()->getTable('modules')->getErrors()) {
                    $res->errors = array_merge($res->errors, $tableErrors);
                } else
                    $res->errors[] = __('Module Update Failed', PPS_LANG_CODE);
            }
        } else {
            $res->errors[] = __('Error module ID', PPS_LANG_CODE);
        }
        parent::put($d);
        return $res;
    }
    protected function _getIDFromReq($d = array()) {
        $id = 0;
        if(isset($d['id']))
            $id = $d['id'];
        elseif(isset($d['code'])) {
            $fromDB = $this->get(array('code' => $d['code']));
            if($fromDB[0]['id'])
                $id = $fromDB[0]['id'];
        }
        return $id;
    }
	public function activatePlugin($d = array()) {
		$plugName = isset($d['plugName']) ? $d['plugName'] : '';
		if(!empty($plugName)) {
			$activationKey = isset($d['activation_key']) ? $d['activation_key'] : '';
			if(!empty($activationKey)) {
				$result = modInstallerPps::activatePlugin($plugName, $activationKey);
				if($result === true) {
					$allActivationModules = modInstallerPps::getActivationModules();
					// Activate all required modules
					if(!empty($allActivationModules)) {
						foreach($allActivationModules as $i => $m) {
							if($m['plugName'] == $plugName) {
								// We need to set this var here each time - as it will be detected on put() method bellow
								unset($allActivationModules[ $i ]);
								modInstallerPps::updateActivationModules($allActivationModules);
								$this->put(array(
									'code' => $m['code'],
									'active' => 1,
								));
}
						}
						modInstallerPps::updateActivationModules($allActivationModules);
					}
					$allActivationMessages = modInstallerPps::getActivationMessages();
					// Remove activation messages for this plugin
					if(!empty($allActivationMessages) && isset($allActivationMessages[ $plugName ])) {
						unset($allActivationMessages[ $plugName ]);
						modInstallerPps::updateActivationMessages($allActivationMessages);
					}
					return true;
				} elseif(is_array($result)) {	// Array with errors
					$this->pushError($result);
				} else {
					$this->pushError(__('Can not contact authorization server for now.', PPS_LANG_CODE));
					$this->pushError(__('Please try again latter.', PPS_LANG_CODE));
					$this->pushError(__('If problem will not stop - please contact us using this form <a href="http://supsystic.com/contacts/" target="_blank">http://supsystic.com/contacts/</a>.', PPS_LANG_CODE));
				}
			} else
				$this->pushError (__('Please enter activation key', PPS_LANG_CODE));
		} else
			$this->pushError (__('Empty plugin name', PPS_LANG_CODE));
		return false;
	}
	public function activateUpdate($d = array()) {
		$plugName = isset($d['plugName']) ? $d['plugName'] : '';
		if(!empty($plugName)) {
			$activationKey = isset($d['activation_key']) ? $d['activation_key'] : '';
			if(!empty($activationKey)) {
				$result = modInstallerPps::activateUpdate($plugName, $activationKey);
				if($result === true) {
					return true;
				} elseif(is_array($result)) {	// Array with errors
					$this->pushError($result);
				} else {
					$this->pushError(__('Can not contact authorization server for now.', PPS_LANG_CODE));
					$this->pushError(__('Please try again latter.', PPS_LANG_CODE));
					$this->pushError(__('If problem will not stop - please contact us using this form <a href="http://supsystic.com/contacts/" target="_blank">http://supsystic.com/contacts/</a>.', PPS_LANG_CODE));
				}
			} else
				$this->pushError (__('Please enter activation key', PPS_LANG_CODE));
		} else
			$this->pushError (__('Empty plugin name', PPS_LANG_CODE));
	}
}
