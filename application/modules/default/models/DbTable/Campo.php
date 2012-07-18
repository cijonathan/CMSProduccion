<?php

class Default_Model_DbTable_Campo extends Zend_Db_Table_Abstract
{
    protected $_name = 'campo';
    /* LISTAR CAMPOS QUE MUESTRAN LISTADO DE LA TABLA */
    public function listartabla($id_modulo){
        /* [CONSULTA] */
        $consulta = $this->select()
                ->setIntegrityCheck(false)
                ->from(array('c'=>'campo'),'*')
                ->where('id_modulo = ?',$id_modulo)
                ->where('listado_campo = ?',1)
                ->where('id_estado = ?',1)
                ->order('orden_campo ASC');        
        
        return $consulta->query()->fetchAll(Zend_Db::FETCH_OBJ);
        
    } 
    /* [CAMPO FECHA Y HORA PARA NORMAR] */
    public function listaranormar($id_modulo){
        if(is_numeric($id_modulo)){
            $consulta = $this->select()
                    ->from($this->_name,array('nombre_campo_slug','id_tipo'))
                    ->setIntegrityCheck(false)                    
                    ->where('id_modulo = ?',$id_modulo)
                    ->where("id_tipo = '4' OR id_tipo = '6'");
            return $consulta->query()->fetchAll(Zend_Db::FETCH_OBJ);
        }else return false;
    }
    /* [OBTENER TODOS LOS CAMPOS DE UN MODULO] */
    public function obtenercampo($id_modulo){
        if(is_numeric($id_modulo)){            
            /* [CONSULTA] */
            $consulta = $this->select()
                    ->setIntegrityCheck(false)
                    ->from(array('c'=>$this->_name),'*')
                    ->where('id_modulo = ?',$id_modulo)
                    ->where('id_estado = ?',1)
                    ->order('orden_campo ASC');
            return $consulta->query()->fetchAll(Zend_Db::FETCH_OBJ);
        }        
    } 
    public function obtener($id_campo){
        if(is_numeric($id_campo)){
            $consulta = $this->select()
                    ->setIntegrityCheck(false)
                    ->from($this->_name,'*')
                    ->where('id_campo = ?',$id_campo);
            return $consulta->query()->fetch(Zend_Db::FETCH_OBJ);
        }
        
    }
    public function googlemaps($id_modulo){
        if(is_numeric($id_modulo)){    
            /* [CONSULTA] */
            $consulta = $this->select()
                    ->setIntegrityCheck(false)
                    ->from(array('c'=>$this->_name),'*')
                    ->where('id_modulo = ?',$id_modulo)
                    ->where('id_estado = ?',1)
                    ->where('id_tipo = ?',10);
            return $consulta->query()->rowCount();
            
        }        
    }
    public function editordetexto($id_modulo){
        if(is_numeric($id_modulo)){    
            /* [CONSULTA] */
            $consulta = $this->select()
                    ->setIntegrityCheck(false)
                    ->from(array('c'=>$this->_name),'*')
                    ->where('id_modulo = ?',$id_modulo)
                    ->where('id_estado = ?',1)
                    ->where('id_tipo = ?',9);
            return $consulta->query()->rowCount();
            
        }        
    }
    /* OBTENER DATOS */
    public function obtenerdatos($id_registro,$id_modulo){
        if(is_numeric($id_modulo) && is_numeric($id_registro)){
            /* MODULO */
            $modulo = new Default_Model_DbTable_Modulo();
            $datomodulo = $modulo->obtenerdato($id_modulo);

            /* CAMPO */
            $datocampo = $this->obtenercampo($id_modulo);		
            /* [BASE PERSONALIZADA] */
            $base = $this->basepersonalizado($datomodulo->id_empresa);
            $id_modulo_campo = 'id_'.$datomodulo->nombre_modulo_slug;
            $consulta = $base->select()
                    ->from($datomodulo->nombre_modulo_slug,'*')
                    ->where('`'.$id_modulo_campo.'` = ?',$id_registro);            
            
            $datos = $consulta->query()->fetch();				
            /* [NORMAR] */            
            foreach($datocampo as $retorno){
                $campo = $retorno->nombre_campo_slug;
                /* [FECHA] */
                if($retorno->id_tipo == 4){
                    #echo $datos->$campo;                    
                    $fecha = new Zend_Date($datos->$campo,'yyyy-MM-dd');
                    $datos->$campo = $fecha->toString('dd/MM/yyyy');
                }elseif($retorno->id_tipo == 6){
                    $datos->$campo = substr($datos->$campo,0,5);
                }else{
                    $datos->$campo = utf8_encode($datos->$campo);
                }               
            }
            /* [ID DEL REGISTRO] */
            $datos->idregistro = $id_registro;
             #var_dump($datos); exit;
            return $datos;           
        }
    }
    public function mapa($id_registro,$id_modulo){
        if(is_numeric($id_modulo) && is_numeric($id_registro)){
            /* MODULO */
            $modulo = new Default_Model_DbTable_Modulo();
            $datomodulo = $modulo->obtenerdato($id_modulo);       
            /* CAMPO */
            $datocampo = $this->obtenercampo($id_modulo); 
            /* [BASE PERSONALIZADA] */
            $base = $this->basepersonalizado($datomodulo->id_empresa);
            $id_modulo_campo = 'id_'.$datomodulo->nombre_modulo_slug;
            $consulta = $base->select()
                    ->from($datomodulo->nombre_modulo_slug,'*')
                    ->where($id_modulo_campo.' = ?',$id_registro);
            $datos = $consulta->query()->fetch();            
            /* [CAPTURAR] */
            foreach($datocampo as $retorno){
                $campo = $retorno->nombre_campo_slug;
                if($retorno->id_tipo == 10){ 
                    return explode(',',$datos->$campo);
                }
            }
        }                
    }
    private function base(){
        /* [BASE DE DATOS PERSONALIZADA] */
        $config = new Zend_Config_Ini('../application/configs/application.ini', 'production');
        $db = Zend_Db::factory('Pdo_Mysql', $config->resources->db->params);
        $db->setFetchMode(Zend_Db::FETCH_OBJ);        
        return $db;        
    }   
    private function basepersonalizado($id_empresa){                
        if($id_empresa>0 && is_numeric($id_empresa)){     
            /* [OBTENER DATOS DE CONEXION] */
            $empresa = new Default_Model_DbTable_Proyecto();
            $datos = $empresa->obtener($id_empresa);            
            /* [INSTANCIA DE CONEXION] */
            $conexion = array(
                'host'       => $datos->servidor_empresa,
                'username'   => $datos->usuario_empresa,
                'password'   => $datos->clave_empresa,
                'dbname'     => $datos->basededatos_empresa,
                'persistent' => true
            );
            #var_dump($conexion); die;
            /* [CONEXION] */
            $db = Zend_Db::factory('Pdo_Mysql',$conexion);  
            $db->setFetchMode(Zend_Db::FETCH_OBJ); 
            return $db;
        }
    }        
}

