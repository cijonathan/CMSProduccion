<?php

class LayoutController extends Zend_Controller_Action
{

    protected $sesion;

    public function init()
    {
        /* Initialize action controller here */
    }
    public function preDispatch() {
        $this->sesion = new Zend_Session_Namespace('login');
    }
    public function topAction(){
        /* [CAPTURAR ZEND ZEN REGISTRY] */
        $registro = Zend_Registry::getInstance();
        /* [CAPTURAR DATOS] */
        $usuario = new Default_Model_DbTable_Usuario();
        $datosusuario = $usuario->capturar($registro->get('id_usuario'));
        $empresa = new Default_Model_DbTable_Empresa();
        $datosempresa = $empresa->obtener($datosusuario->id_empresa);
        /* [NUEVOS REGISTROS] */
        $this->sesion->nombre_empresa = utf8_encode($datosempresa->nombre_empresa);
        $this->sesion->id_empresa = $datosempresa->id_empresa;
        /* [ENVIAR DATOS VISTA] */
        $this->view->nombre_usuario = $registro->get('nombre_usuario');
        $this->view->email_usuario = $registro->get('email_usuario');
        $this->view->nombre_empresa = utf8_decode($this->sesion->nombre_empresa);
        /* [MENU] */
        if($registro->get('id_tipo') == 1) $tipo = 0; else $tipo = 1;
        $modulo = new Default_Model_DbTable_Modulo();
        $this->view->modulo = $modulo->listaracceso($datosusuario->id_empresa,$registro->get('id_usuario'), $tipo);
        /* [LIMPIEZA DATOS] */
        unset($usuario,$datosusuario,$empresa,$datosempresa);
    }

}

