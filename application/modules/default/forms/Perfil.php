<?php

class Default_Form_Perfil extends Zend_Form
{

    public function init()
    {
        $this->setName('perfil');
        $this->setAttrib('id','formulario');            
        /* NOMBRE */
        $nombre = new Zend_Form_Element_Text('nombre_usuario');
        $nombre->setRequired(true)
                ->setLabel('Nombre:')                
                ->setAttrib('class','required span6');         
        /* NOMBRE */
        /*$email = new Zend_Form_Element_Text('email_usuario');
        $email->setRequired(true)
                ->setLabel('Email:')                
                ->setAttrib('class','required email span6');        */
        /* CLAVE */
        $clave = new Zend_Form_Element_Text('clave_usuario');
        $clave->setRequired(true)
                ->setLabel('Clave:')                
                ->setAttrib('class','required span6');         
        /* BOTON */
        $boton = new Zend_Form_Element_Submit('Enviar');
        $boton->setAttrib('class','btn btn-primary btn-large')
              ->setLabel('ACTUALIZAR');

        $this->addElements(array($nombre,$email,$clave,$boton));        
    }



}

