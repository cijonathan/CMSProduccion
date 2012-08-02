<?php

class Default_Model_DbTable_Usuario extends Zend_Db_Table_Abstract
{
    protected $_name = 'usuario';
   
    public function listar($id_empresa,$id){
        if(is_numeric($id_empresa) && is_numeric($id)){
            /* [BASE PERSONALIZADA] */
            $base = $this->base();
            /* [CONSULTA] */
            $consulta = $base->select()
                    ->from(array('u'=>$this->_name),
                        array(
                            'id_usuario'=>'u.id_usuario',
                            'nombre_usuario'=>'u.nombre_usuario',
                            'email_usuario'=>'u.email_usuario',
                            'clave_usuario'=>'u.clave_usuario',                        
                            'tipo_usuario'=>'u.id_tipo',                        
                            ))
                    ->joinInner(array('eu'=>'empresa_has_usuario'),'u.id_usuario = eu.id_usuario',array('id_empresa'=>'eu.id_empresa'))
                    ->joinInner(array('ut'=>'usuario_tipo'),'u.id_tipo = ut.id_tipo',array('nombre_tipo'=>'ut.nombre_tipo'))
                    ->where('eu.id_empresa = ?',$id_empresa)
                    ->where('u.id_usuario <> ?',$id)
                    ->order('ut.nombre_tipo ASC');
            return $base->fetchAll($consulta);
        }
    }
    
    public function capturar($id_usuario){
        if($id_usuario>0 && is_numeric($id_usuario)){
            $base = $this->base();
            /* [CONSULTA] */
            $consulta = $base->select()->from(array('ue'=>'empresa_has_usuario'),'*')
                    ->where('id_usuario = ?', $id_usuario);
            return $consulta->query()->fetch();
        }
        
    }
    public function eliminar($id_usuario){        
        if(is_numeric($id_usuario)){
            $base = $this->base();
            if($base->delete('empresa_has_usuario','id_usuario = '.$id_usuario)){
                if($base->delete('usuario','id_usuario = '.$id_usuario)){
                    if($base->delete('logs','usuario_logs = '.$id_usuario)) return true;
                    else return true;
                }else return false;
            }else return false;
                
        }
    }
    public function agregar($datos,$id_empresa){
        if(is_array($datos)){
            /* [DATOS DE VALIDACION] */
            $valida = array(
                'email'=>$datos['email_usuario'],
                'id'=>$id_empresa
            );
            /* [VALIDA EMAIL]  */
            if($this->valida($valida)){
                /* [INSERTA #usuario] */
                if($this->insert($datos)){
                    /* [ULTIMA ID] */
                    $id_usuario = $this->getAdapter()->lastInsertId();
                    /* [BASE PERSONALIZADA] */
                    $base = $this->base();
                    /* [DATOS DE #empresa_has_usuario] */
                    $datos_usuario_empresa = array(
                        'id_empresa'=>$id_empresa,
                        'id_usuario'=>$id_usuario
                    );
                    /* [BD] */
                    if($base->insert('empresa_has_usuario', $datos_usuario_empresa)){
                        return true;
                    }else{
                        return false;
                    }
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    private function valida($datos){
        if(is_array($datos)){
            $datos = (object)$datos;
            /* [BASE PERSONALIZADA] */
            $base = $this->base();
            /* [CONSULTA] */
            $consulta = $base->select()
                    ->from(array('u'=>$this->_name),'*')
                    ->joinInner(array('eu'=>'empresa_has_usuario'),'u.id_usuario = eu.id_usuario','*')
                    ->where('u.email_usuario = "'.$datos->email.'"');
            if(count($base->fetchAll($consulta))>0){
                return false;
            }else{
                return true;
            }            
        }        
    }
    public function obtener($id_usuario){
        if(is_numeric($id_usuario)){
            $consulta = $this->select()
                    ->setIntegrityCheck(false)
                    ->from($this->_name,array('nombre_usuario','email_usuario','clave_usuario'))
                    ->where('id_usuario = ?',$id_usuario);
            return $consulta->query()->fetch();
        }else return false;
    }
    public function actualizar($datos,$id_usuario){
        if(is_array($datos) && is_numeric($id_usuario)){            
            if($this->update($datos,'id_usuario = '.$id_usuario)) return true; else return false;
        }else return false;
    }
    private function base(){
        /* [BASE DE DATOS PERSONALIZADA] */
        $config = new Zend_Config_Ini('../application/configs/application.ini', 'production');
        $db = Zend_Db::factory('Pdo_Mysql', $config->resources->db->params);
        $db->setFetchMode(Zend_Db::FETCH_OBJ);        
        return $db;        
    }   
}

