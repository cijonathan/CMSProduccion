<?php

class Default_Model_DbTable_Proyecto extends Zend_Db_Table_Abstract
{
    protected $_name = 'empresa';
    
    public function obtener($id){
        if(is_numeric($id) && $id>0){
            $consulta = $this->select()->setIntegrityCheck(false)
                    ->from(array('e'=>$this->_name),array(
                    'id_empresa'=>'e.id_empresa',
                    'nombre_empresa'=>'e.nombre_empresa',
                    'usuario_empresa'=>'e.usuario_empresa',
                    'clave_empresa'=>'e.clave_empresa',
                    'basededatos_empresa'=>'e.basededatos_empresa',
                    'url_empresa'=>'e.url_empresa',
                    'servidor_empresa'=>'e.servidor_empresa'))
                    ->where('id_empresa = ?',$id);
            $retorno = $consulta->query()->fetch();
            $fila = new stdClass();
            $fila->id_empresa = $retorno['id_empresa'];
            $fila->nombre_empresa = $retorno['nombre_empresa'];
            $fila->usuario_empresa = $retorno['usuario_empresa'];
            $fila->clave_empresa = $retorno['clave_empresa'];
            $fila->basededatos_empresa = $retorno['basededatos_empresa'];
            $fila->url_empresa = $retorno['url_empresa'];
            $fila->servidor_empresa = $retorno['servidor_empresa'];      
            return $fila;
        }
    }
}

