<?php

class AyudaController extends Zend_Controller_Action
{

    public function init(){
    }

    public function indexAction(){  
        /* [TITLE] */
        $this->view->headTitle()->prepend('Ayuda - ');    
        /* [FORMULARIO] */
        $formulario = new Default_Form_Ayuda();
        $this->view->formulario = $formulario;
        /* [FORMULARIO] */
        $respuesta = $this->getRequest();
        if($respuesta->isPost()){   
            if($formulario->isValid($this->_request->getPost())){     
                /* [DATOS] */
                $datos = $formulario->getValues(); 
                $datos['fecha_ayuda'] = date('Y-m-d H:i:s');
                /* PROCESAR */
                $ayuda = new Default_Model_DbTable_Ayuda();
                if($ayuda->guardar($datos)){
                    $email = new Default_Model_Email();
                    if($email->emailAyuda($datos)){                        
                        $this->view->exito = true;                         
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

