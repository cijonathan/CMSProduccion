<?php

class Default_Model_Ftp
{
    private $servidor = 'cms.hostprimario.com';
    private $usuario = 'upload@cms.hostprimario.com';
    private $clave = '4hNOXu7hnwX9';
    private $id_conexion = null;
    private $exito_conexion = null;
    
    public function __construct() {        
        $this->id_conexion = ftp_connect($this->servidor);
        $this->exito_conexion = ftp_login($this->id_conexion,$this->usuario,$this->clave);
    }
    public function crearCarpeta($ruta){
        if(is_string($ruta)){
            if(ftp_mkdir($this->id_conexion, $ruta)){
                return true;
            }else{
                return true;
            }
        }else return false;
    }   
    public function subirArchivo($temporal,$ruta){
        if(is_string($temporal) && is_string($ruta)){
            if(ftp_put($this->id_conexion,$ruta,$temporal,FTP_BINARY)){
                return true;
            }else{
                return false;
            }
        }else return false;
    }
}

