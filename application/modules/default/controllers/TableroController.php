<?php

class TableroController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
    }
    public function cerrarAction(){
        /* [DESAHIBILITAR LAYOUT y VIEW] */
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        if(Zend_Auth::getInstance()->hasIdentity()){
            /* [CERRAR SESSION] */
            Zend_auth::getInstance()->clearIdentity();
        }       
        /* [REDIRECCIONAR] */
        $this->_redirect('/');             
    }
}

