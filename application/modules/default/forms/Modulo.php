<?php

class Default_Form_Modulo extends Zend_Form
{
    public function __construct($options = null){
        parent::__construct($options);
    }    

    public function init(){
        
        $id_empresa = $this->getAttrib('id_empresa');
        
        $this->setName('usuario');
        $this->setAttrib('id','formulario-login');
        
        $id = new Zend_Form_Element_Hidden('id_usuario');
        $id->addFilter('Int')
                ->removeDecorator('Label');       
        
        $modulos = new Zend_Form_Element_MultiCheckbox('id_modulo');       
        $modulos->setLabel('Modulos:')
                ->setSeparator('');
        
        $modulo = new Default_Model_DbTable_Modulo();       
        
        foreach($modulo->listar($id_empresa) as $retorno){
            $modulos->addMultiOption($retorno->id_modulo, $retorno->nombre_modulo);
        }
        
        
        $boton = new Zend_Form_Element_Submit('submit_form_modulo');
        $boton->setAttrib('class','btn btn-primary btn-small')
              ->setLabel('Actualizar');

        $this->addElements(array($id,$modulos,$boton));         
        
    }


}


