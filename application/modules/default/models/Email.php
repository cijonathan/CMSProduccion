<?php

class Default_Model_Email
{
    public function emailAyuda($datos){
        if(is_array($datos)){
            $datos = (object)$datos;
            /* CUERPO */
            $cuerpo = 'El siguiente cliente presenta problemas en el cms. Los datos de contacto son los siguientes:<br /><br />';
            $cuerpo.= '<strong>Nombre:</strong> '.$datos->nombre_ayuda.'<br />';;
            $cuerpo.= '<strong>Email:</strong> '.$datos->email_ayuda.'<br />';;
            $cuerpo.= '<strong>Dominio:</strong> '.$datos->dominio_ayuda.'<br />';
            $cuerpo.= '<strong>Fecha solicitud:</strong> '.$datos->fecha_ayuda.'<br /><br />';
            $cuerpo.= '<strong>Comentario:</strong> '.$datos->comentario_ayuda;
            /* EMAIL */
            $email = new Zend_Mail();
            $email->setFrom($datos->email_ayuda,$datos->nombre_ayuda);
            $email->addTo('jramirez@creatividadeinteligencia.cl','Jonathan Ramírez');
            $email->addCc('ftoloza@creatividadeinteligencia.cl','Fernando Toloza');
            $email->addCc('ahidalgo@creatividadeinteligencia.cl','Aaron Hidalgo');
            $email->setSubject('[AYUDA] - CMS '.$datos->dominio_ayuda.' - '.$datos->fecha_ayuda);
            $email->setBodyHtml($cuerpo,'utf-8');           
            if($email->send()){
                return true;
            }else{
                return false;
            }
        }
    }
}

