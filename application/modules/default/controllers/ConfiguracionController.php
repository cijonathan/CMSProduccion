<?php

class ConfiguracionController extends Zend_Controller_Action
{

    public function init(){
        /* [ID EMPRESA] */
        $this->sesion = new Zend_Session_Namespace('login');        
        $this->id_empresa = $this->sesion->id_empresa;        
    }

    public function indexAction(){  
        /* [TITLE] */
        $this->view->headTitle()->prepend('Configuración - ');    
        /* [FORMULARIO] */
        $formulario = new Default_Form_Imagen();
        $this->view->formulario = $formulario;
        /* [FORMULARIO] */
        $respuesta = $this->getRequest();
        if($respuesta->isPost()){   
            if($formulario->isValid($this->_request->getPost())){     
                /* ARCHIVO */
                $elemento = $_FILES;
                /* EMPRESA */
                $modulo = new Default_Model_DbTable_Empresa();
                $datos = $modulo->obtener($this->id_empresa);              
                $ftp = new Default_Model_Ftp();
                /* RUTA */
                $extension = pathinfo($elemento['imagen']['name']);
                $nombre_archivo = date('YmdGis').'.'.$extension['extension'];
                $ruta = '/configuracion/'.$datos->nombre_empresa_slug;
                $ruta_subir = $ruta.'/logotipo_'.$nombre_archivo;
                if($ftp->crearCarpeta($ruta)){
                    if($ftp->subirArchivo($elemento['imagen']['tmp_name'], $ruta_subir)){
                        /* PROCESO */
                        $configuracion = new Default_Model_DbTable_Configuracion();                        
                        $datos_db = array(
                            'nombre_parametro'=>'logotipo',
                            'value_parametro'=>$ruta_subir,
                            'id_empresa'=>$this->id_empresa
                        );
                        if($configuracion->guardar($datos)){
                            $this->view->exito = true;                            
                        }else{
                        $this->view->error = true;
                        }
                    }else{
                        $this->view->error = true;
                    }
                }else{
                    $this->view->error = true;
                }
            }
        }      
    }
}

