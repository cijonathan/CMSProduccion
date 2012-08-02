<?php

class PerfilController extends Zend_Controller_Action
{
    protected $id_usuario;

    public function init(){
        /* TITLE SECCIONES */
        $this->view->titulo = 'MI PERFIL';
        $this->view->resumen = 'Control de mis datos de acceso';    
        /* id_usuario */
        $registro = new Zend_Registry();
        $this->id_usuario = $registro->get('id_usuario');
    }

    public function indexAction(){    
        /* [EXITO y ERROR] */
        $mensaje = new Zend_Session_Namespace('mensaje');
        $this->view->exito = $mensaje->exito;
        $this->view->error = $mensaje->error;
        $mensaje->setExpirationSeconds(1);
        unset($mensaje);        
        /* FORMULARIO */
        $formulario = new Default_Form_Perfil();
        $this->view->formulario = $formulario;
        /* new class(); */
        $usuario = new Default_Model_DbTable_Usuario();
        /* [PROCESAR FORMULARIO] */
        $respuesta = $this->getRequest();
        if($respuesta->isPost()){   
            if($formulario->isValid($this->_request->getPost())){
                $datos = $formulario->getValues();
                /* new Class() */
                $mensaje = new Zend_Session_Namespace('mensaje');                 
                if($usuario->actualizar($datos,$this->id_usuario)){
                    $mensaje->exito = true;                    
                }else{
                    $mensaje->error = true;                     
                }
                $this->_redirect('/perfil/');                
            }   
        }else{
            $formulario->populate($usuario->obtener($this->id_usuario));
            #var_dump($usuario->obtener($this->id_usuario)); exit;      
        }          
    }
}

