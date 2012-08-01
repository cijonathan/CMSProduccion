<?php

class UsuarioController extends Zend_Controller_Action
{
    protected $id_empresa;  
    protected $id_usuario;


    public function init(){
        /* [ID EMPRESA] */
        $this->sesion = new Zend_Session_Namespace('login');        
        $this->id_empresa = $this->sesion->id_empresa;
        /* [ID USUARIO] */
        $registro = new Zend_Registry();
        $this->id_usuario = $registro->get('id_usuario');           
        /* [ID REGISTRO] */        
        $this->id_registro = $this->_getParam('id',0);              
        /* [LOGS] */      
        $frontal = Zend_Controller_Front::getInstance()->getRequest();       
        $datos = array(
            'id_empresa'=>$this->id_empresa,
            'id_usuario'=>$this->id_usuario,
            'id_registro'=>$this->id_registro,
        );
        $logs = new Default_Model_Logs($frontal,$datos);
        $logs->generarLogs();          
    }
    public function indexAction()
    {
        /* [EXITO y ERROR] */
        $mensaje = new Zend_Session_Namespace('mensaje');
        $this->view->exito = $mensaje->exito;
        $this->view->error = $mensaje->error;
        $mensaje->setExpirationSeconds(1);
        unset($mensaje);   
        /* [USUARIO] */
        $usuario = new Default_Model_DbTable_Usuario();        
        /* [LISTAR DATOS] */
        $this->view->datos = $usuario->listar($this->id_empresa,$this->id_usuario);        
        /* [TITLE] */
        $this->view->headTitle()->prepend('Usuario - ');
        /* [FORMULARIO USUARIO] */
        $formulario = new Default_Form_Usuario();
        $this->view->formulario = $formulario;
        /* [FORMULARIO MODULO] */    
        $formulariomodulo = new Default_Form_Modulo(array('id_empresa'=>$this->id_empresa));
        $this->view->formulariomodulo = $formulariomodulo;
        /* [INSTANCIA MODULO a VIEW] */
        $modulo = new Default_Model_DbTable_Modulo();        
        $this->view->modulo = $modulo;
        /* [FORMULARIO] */
        $respuesta = $this->getRequest();
        if($respuesta->isPost()){      
            if($this->_request->getPost('submit_form_usuario', false)){
                if($formulario->isValid($this->_request->getPost())){                     
                    /*[DATOS FORMULARIO] */
                    $campos = (object)$formulario->getValues();
                    $datos = array(
                        'nombre_usuario'=>$campos->nombre_usuario,
                        'email_usuario'=>$campos->email_usuario,
                        'clave_usuario'=>$campos->clave_usuario,
                        'id_tipo'=>$campos->id_tipo
                    );
                    /* [BD] */
                    if($usuario->agregar($datos,$this->id_empresa)){                          
                        $formulario->reset();
                        $formulario->getElement('id_empresa')->setValue($this->id_empresa);
                        /* [MENSAJE EXITO] */
                        $mensaje = new Zend_Session_Namespace('mensaje');
                        $mensaje->exito = true;
                        $this->_redirect('usuario/');                          
                    }else{    
                        $formulario->reset();
                        $formulario->getElement('id_empresa')->setValue($this->id_empresa);                        
                        $this->view->error = true;
                    }
                }else return false;
            }elseif($this->_request->getPost('submit_form_modulo', false)){
                if($formulariomodulo->isValid($this->_request->getPost())){ 
                    /* [CAPTURAR DATOS] */
                    $id_usuario = $formulariomodulo->getValue('id_usuario');
                    $id_modulo = $formulariomodulo->getValue('id_modulo');
                    /* [MODULOS] */
                    if($modulo->agregar($id_usuario, $id_modulo)){
                        $this->view->exito = true;                        
                    }else $this->view->error = true;
                }else return false;
            }else return false;                                           
        }else{
            /* [INGRESAR ID_EMPRESA  AL USUARIO] */
            $formulario->populate(array('id_empresa'=>$this->id_empresa));
        }
    }
    public function eliminarAction(){
        /* [CONFIGURACIÓN] */
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);         
        /* [PARAMETROS] */
        $id_usuario = $this->_getParam('id',0);
        /* [ELIMINAR] */
        $usuario = new Default_Model_DbTable_Usuario();
        $mensaje = new Zend_Session_Namespace('mensaje');
        if($usuario->eliminar($id_usuario)){
           $mensaje->exito = true;           
        }else{
            $mensaje->error = true;
        }
        /* [REDIRECCIONAR] */
        $this->_redirect('usuario/');          
    }
    public function topAction(){}
}

