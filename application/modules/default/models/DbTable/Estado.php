<?php

class Default_Model_DbTable_Estado extends Zend_Db_Table_Abstract
{
    protected $_name = 'estado_sistema';
    
    public function listar(){
        /* [CONSULTA] */
        $consulta = $this->select()
                ->setIntegrityCheck(false)
                ->from(array('e'=>$this->_name),'*')
                ->order('nombre_estado ASC');        
        

         return $consulta->query()->fetchAll(Zend_Db::FETCH_OBJ);
        
    }     
}
