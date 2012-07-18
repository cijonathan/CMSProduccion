<?php

class Default_Form_Usuario extends Zend_Form
{

    public function init()
    {
        $this->setName('usuario');
        $this->setAttrib('id','formulario-login');
        
        $id = new Zend_Form_Element_Hidden('id_empresa');
        $id->addFilter('Int')
                ->removeDecorator('Label');         
        
        $nombre = new Zend_Form_Element_Text('nombre_usuario');
        $nombre
                ->setRequired(true)
                ->setLabel('Nombre/Apellido:')
                ->setAttrib('placeholder','Ingrese su nombre y apellido')
                ->setAttrib('class','required');        
        
        $email = new Zend_Form_Element_Text('email_usuario');
        $email
                ->setRequired(true)
                ->setLabel('Email:')
                ->setAttrib('placeholder','Ingrese su email de acceso')
                ->setAttrib('class','required email');        
        
        $clave = new Zend_Form_Element_Password('clave_usuario');
        $clave
                ->setRequired(true)
                ->setLabel('Contraseña:')   
                ->setAttrib('placeholder','Ingrese su clave de acceso')                
                ->setAttrib('class','required login')
                ->addFilter('StringtoLower')
                ->addFilter('StripTags');
        
        $tipo = new Default_Model_DbTable_Tipousuario();
        
        $id_tipo = new Zend_Form_Element_Select('id_tipo');
        $id_tipo ->setRequired(true)
                ->setLabel('Tipo usuario:');        
        
        foreach($tipo->listar() as $retorno){
            $id_tipo->addMultiOption($retorno->id_tipo,$retorno->nombre_tipo);
        }        
        
        $boton = new Zend_Form_Element_Submit('submit_form_usuario');
        $boton->setAttrib('class','btn btn-primary btn-large')
              ->setLabel('Agregar');

        $this->addElements(array($id,$nombre,$email,$clave,$id_tipo,$boton));         
        
    }


}


