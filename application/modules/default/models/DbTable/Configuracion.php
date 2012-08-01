<?php

class Default_Model_DbTable_Configuracion extends Zend_Db_Table_Abstract
{
    protected $_name = 'configuracion_parametro';
    
    public function guardar($datos){        
        if(is_array($datos)){
            if($this->insert($datos)){
                return true;
            }else return false;            
        }else return false;                
    }
}
