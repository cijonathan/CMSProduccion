<?php

class Default_Model_DbTable_Tipousuario extends Zend_Db_Table_Abstract
{
    protected $_name = 'usuario_tipo';
    
    public function listar(){
        $consulta = $this->select()
                ->setIntegrityCheck(false)
                ->from($this->_name,'*')
                ->order('nombre_tipo DESC');
        return $this->fetchAll($consulta);
    }
    private function base(){
        /* [BASE DE DATOS PERSONALIZADA] */
        $config = new Zend_Config_Ini('../application/configs/application.ini', 'production');
        $db = Zend_Db::factory('Pdo_Mysql', $config->resources->db->params);
        $db->setFetchMode(Zend_Db::FETCH_OBJ);        
        return $db;        
    }   
}

