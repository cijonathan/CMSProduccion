<?php

class Default_Model_DbTable_Ayuda extends Zend_Db_Table_Abstract
{
    protected $_name = 'ayuda';
    
    public function guardar($datos){        
        if(is_array($datos)){
            if($this->insert($datos)){
                return true;
            }else return false;            
        }else return false;                
    }
}
