<?php
class tableStatisticsPps extends tablePps {
    public function __construct() {
        $this->_table = '@__statistics';
        $this->_id = 'id';
        $this->_alias = 'sup_statistics';
        $this->_addField('id', 'hidden', 'int')
			->_addField('popup_id', 'text', 'int')
			->_addField('type', 'text', 'int')
			->_addField('date_created', 'text', 'varchar');
    }
}