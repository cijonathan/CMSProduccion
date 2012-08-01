<?php

class Default_Form_Ayuda extends Zend_Form
{

    public function init()
    {
        $this->setName('usuario');
        $this->setAttrib('id','formulario-campo');
        
        $nombre = new Zend_Form_Element_Text('nombre_ayuda');
        $nombre
                ->setRequired(true)
                ->setLabel('Nombre:')
                ->setAttrib('size','50')
                ->setAttrib('placeholder','Ingrese su nombre')
                ->setAttrib('class','required span6');     
        
        $email = new Zend_Form_Element_Text('email_ayuda');
        $email
                ->setRequired(true)
                ->setLabel('Email:')
                ->setAttrib('size','27')
                ->setAttrib('placeholder','Ingrese su email')
                ->setAttrib('class','required email span6');        
        
        $dominio = new Zend_Form_Element_Text('dominio_ayuda');
        $dominio
                ->setRequired(true)
                ->setLabel('Dominio (http://www.ejemplo.cl):')
                ->setAttrib('size','27')
                ->setAttrib('placeholder','Ingrese el dominio del Sitio Web')
                ->setAttrib('class','required url span6');        
        
        $comentario = new Zend_Form_Element_Textarea('comentario_ayuda');
        $comentario->setRequired(true)    
                ->setLabel('Comentario:')                
                ->setAttrib('class','required span6')
                ->setAttrib('rows',9)
                ->setAttrib('placeholder','Ingrese su comentario');
        
        $boton = new Zend_Form_Element_Submit('Enviar');
        $boton->setAttrib('class','btn btn-primary btn-large')
              ->setLabel('Enviar Comentario');

        $this->addElements(array($nombre,$email,$dominio,$comentario,$boton));         
        
    }


}


