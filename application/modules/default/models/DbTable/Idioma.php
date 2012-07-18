<?php

class Default_Model_DbTable_Idioma extends Zend_Db_Table_Abstract
{
    protected $_name = 'idioma';
    
    public function listar(){
        /* [CONSULTA] */
        $consulta = $this->select()
                ->setIntegrityCheck(false)
                ->from(array('i'=>$this->_name),'*')
                ->order('nombre_idioma ASC');        
        
        return $consulta->query()->fetchAll(Zend_Db::FETCH_OBJ);
        
    }     
}
