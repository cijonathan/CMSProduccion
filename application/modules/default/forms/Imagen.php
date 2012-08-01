<?php

class Default_Form_Imagen extends Zend_Form
{

    public function init()
    {
        $this->setName('usuario');
        $this->setAttrib('id','formulario-login');
        $this->setAttrib('enctype', 'multipart/form-data');        
        
        $imagen = new Zend_Form_Element_File('imagen');
        $imagen->setRequired(true)
                ->setDestination('upload/configuracion')
                ->setLabel('Imagen:');              
        /*$imagen
                ->setRequired(true)
                ->setLabel('Email:')
                ->setAttrib('size','27')
                ->setAttrib('placeholder','Ingrese su email de acceso')
                ->setAttrib('class','required email');                */
        
        $boton = new Zend_Form_Element_Submit('Enviar');
        $boton->setAttrib('class','btn btn-primary btn-large')
              ->setLabel('Acceder');

        $this->addElements(array($imagen,$boton));         
        
    }


}


