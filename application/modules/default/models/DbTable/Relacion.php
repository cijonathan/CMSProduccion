<?php

class Default_Model_DbTable_Relacion extends Zend_Db_Table_Abstract
{
    protected $_name = 'modulo_relacion';
    
    /* LISTAR RELACIONES DEL MODULO */
    public function listar($id_modulo){
        /*if(is_numeric($id_empresa)){
            $consulta = $this->select()
                    ->setIntegrityCheck(false)
                    ->from(array('m'=>'modulo'),array('id_modulo'=>'m.id_modulo','nombre_modulo'=>'m.nombre_modulo','nombre_modulo_slug'=>'m.nombre_modulo_slug'))
                    ->joinInner(array('r'=>'modulo_relacion'),'m.id_modulo = r.id_padre',array('id_hijo'=>'r.id_hijo','id_tipo'=>'r.id_tipo','id_cardinalidad'=>'r.id_cardinalidad'))
                    ->where('m.id_empresa = ?',$id_empresa)
                    ->where('m.id_estado = ?',1);
            
            return $consulta->query()->fetchAll();
            
        }else return false;*/
        if(is_numeric($id_modulo)){
            $consulta = $this->select()
                    ->setIntegrityCheck(false)
                    ->from($this->_name,'*')
                    ->where('id_padre = ?', $id_modulo);
                    #->where('id_estado = ?',1);
            return $consulta->query()->fetchAll(Zend_Db::FETCH_OBJ);
        }else return false;
    }
    public function existeRelacion($id_modulo){
        if(is_numeric($id_modulo)){
            /* [CONSULTA] */
            $base = $this->base();
            $consulta = $base->select()
                    ->from($this->_name,'*')
                    ->where('id_padre = ?',$id_modulo);
            /* RETORNO */
            if($consulta->query()->rowCount()>0){
                return true;
            }else return false;            
        }
    }
	
    public function obtenerDatosModulo($id){
        if(is_numeric($id)){
            /* [INSTANCEAR BASE PERSONALIZADA] */
            $base = $this->base();    
            /* [CONSULTA] */
            $consulta = $base->select()->from(array('m'=>'modulo'),array('nombre_modulo'=>'m.nombre_modulo','nombre_modulo_slug'=>'m.nombre_modulo_slug'))
                    ->where('id_modulo = ?',$id);
            $dato = $consulta->query()->fetch();
            return $dato;
        }
    }
    // public function obtenerhijo($id_hijo){
        // if(is_numeric($id_hijo)){
            
        // }else return false;
    // }
    
    private function base(){
        /* [BASE DE DATOS PERSONALIZADA] */
        $config = new Zend_Config_Ini('../application/configs/application.ini', 'production');
        $db = Zend_Db::factory('Pdo_Mysql', $config->resources->db->params);
        $db->setFetchMode(Zend_Db::FETCH_OBJ);        
        return $db;        
    }          
    
}
