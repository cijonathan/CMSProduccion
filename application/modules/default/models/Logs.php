<?php

class Default_Model_Logs
{
    public function __construct($frontal = null,$config = null) {
        $this->frontal = $frontal;
        $this->datos = (object)$config;
    }
    private function guardar($datos){        
        if(is_array($datos)){
            $base = $this->base();
            if($base->insert('logs',$datos)){
                return true;
            }else return false;            
        }else return false;                
    }    
    public function generarLogs(){
        /* GUARDAR LOGS */
        $datos = array(
            'module_logs'=>$this->frontal->getModuleName(),
            'controller_logs'=>$this->frontal->getControllerName(),
            'action_logs'=>$this->frontal->getActionName(),
            'usuario_logs'=>$this->datos->id_usuario,
            'modulo_logs'=>$this->datos->id_modulo,
            'empresa_logs'=>$this->datos->id_empresa,
            'registro_logs'=>$this->datos->id_registro,
            'fecha_logs'=>date('Y-m-d H:i:s')
        );
        $this->guardar($datos);
    }
    private function base(){
        /* [BASE DE DATOS PERSONALIZADA] */
        $config = new Zend_Config_Ini('../application/configs/application.ini', 'production');
        $db = Zend_Db::factory('Pdo_Mysql', $config->resources->db->params);
        $db->setFetchMode(Zend_Db::FETCH_OBJ);        
        return $db;        
    }     
}

