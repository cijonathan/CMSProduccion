<?php

class Default_Model_DbTable_Componente extends Zend_Db_Table_Abstract
{
    protected $_name = 'modulo_has_componente';
    
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
    public function obtenerhijo($id_hijo){
        if(is_numeric($id_hijo)){
            
        }else return false;
    }
    public function existeComponente($id_modulo,$tipo){        
        if(is_numeric($id_modulo) && is_string($tipo)){
            /* [CONSULTA] */
            $base = $this->base();
            $consulta = $base->select()
                    ->from($this->_name,'*')
                    ->where('id_modulo = ?',$id_modulo);
            if($tipo == 'fotos'){
                $consulta->where('id_componente = ?',1);
            }elseif($tipo == 'archivos'){
                $consulta->where('id_componente = ?',2);                
            }else{
                return false;
            }
            /* RETORNO */
            if($consulta->query()->rowCount()>0){
                return true;
            }else return false;
        }        
    }
    private function base(){
        /* [BASE DE DATOS PERSONALIZADA] */
        $config = new Zend_Config_Ini('../application/configs/application.ini', 'production');
        $db = Zend_Db::factory('Pdo_Mysql', $config->resources->db->params);
        $db->setFetchMode(Zend_Db::FETCH_OBJ);        
        return $db;        
    }       
}
