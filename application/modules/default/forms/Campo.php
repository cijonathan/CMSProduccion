<?php

class Default_Form_Campo extends Zend_Form
{
    public function __construct($options = null) {
        parent::__construct($options);
    }

    public function init()
    {
        /* [ID_MODULO] */
        $id_modulo = $this->getAttrib('id');
        /* [NEW CAMPO] */
        $campo = new Default_Model_DbTable_Campo();
        /* [NEW OPCIONES FORMULARIO] */
        $formulario = new Default_Model_Formulario();
        
        $this->setName('campo');
        $this->setAttrib('id','formulario-campo');
        /* [ID - ID DEL MODULO] */
        $this->addElement('hidden','id',array(
            'value'=>$id_modulo
        ));
        $id = $this->getElement('id');  
        $id->removeDecorator('Label');
        /* [ID - ID REGISTRO] */
        $this->addElement('hidden','idregistro');
        $idregistro = $this->getElement('idregistro');  
        $idregistro->removeDecorator('Label');
        /* CAMPOS DINAMICOS */  
        foreach($campo->obtenercampo($id_modulo) as $retorno) :
            /* [TIPO DE CAMPO] */
            $tipo = $formulario->tipo($retorno->id_tipo);
            /* [TIPO DE VALIDACION] */
            $validacion = $formulario->validacion($retorno->id_validacion);
            /* [EXTRA] */
            $extraclass = $formulario->extraclass($retorno->id_tipo);  
            $extrarows = $formulario->extrarows($retorno->id_tipo); 
            $extratrib = $formulario->extratrib($retorno->id_tipo);
            /* [CREACION DE CAMPOS] */
            /*
             * CAMPO TEXTAREA
             * id_tipo = 2 (area de texto sin editor)
             * id_tipo = 9 (area de texto con editor)
             */
            if($retorno->id_tipo == 2 || $retorno->id_tipo == 9){
                $this->addElement($tipo,$retorno->nombre_campo_slug,array(
                    'placeholder'=>'Ingrese su '.$retorno->nombre_campo,
                    'label'=>$retorno->nombre_campo.':',
                    'class'=>$validacion.' '.$extraclass,                    
                    'rows'=>$extrarows                    
                ));
            /*
             * CAMPO SELECT
             * id_tipo == 3 (BOLEANO, opciones SI o NO)
             */
            }elseif($retorno->id_tipo == 3){						
                $this->addElement($tipo,$retorno->nombre_campo_slug,array(
                    'placeholder'=>'Ingrese su '.$retorno->nombre_campo,
                    'label'=>$retorno->nombre_campo.':',
                    'class'=>$validacion.' '.$extraclass,
                    'multiOptions'=>array(''=>'','1'=>'Si','0'=>'No')                    
                ));
            }else{
            /*
             * CAMPO INPUT (TEXT)
             * id_tipo == 6 (campo con atributo de hora)
             * id_tipo == cualquiera (cualquier campo de texto)
             */
                if($retorno->id_tipo == 6){
                    $this->addElement($tipo,$retorno->nombre_campo_slug,array(
                        'placeholder'=>'Ingrese su '.$retorno->nombre_campo,
                        'label'=>$retorno->nombre_campo.':',
                        'class'=>$validacion.' '.$extraclass,
                        'data-provide'=>$extratrib                        
                    ));    
                }elseif($retorno->id_tipo == 10){ 
                    $this->addElement($tipo,$retorno->nombre_campo_slug,array(
                        'placeholder'=>'Ingrese su '.$retorno->nombre_campo,
                        'label'=>$retorno->nombre_campo.':',
                        'class'=>$validacion.' '.$extraclass,
                        'id'=>'cordenadagooglemaps'
                    ));                      
                }else{
                    $this->addElement($tipo,$retorno->nombre_campo_slug,array(
                        'placeholder'=>'Ingrese su '.$retorno->nombre_campo,
                        'label'=>$retorno->nombre_campo.':',
                        'class'=>$validacion.' '.$extraclass 
                    ));                    
                }
            }
        endforeach;
        /* [IDIOMA] */
        $idioma = new Default_Model_DbTable_Idioma();
        $this->addElement('select','id_idioma',array(
            'label'=>'Idioma :',
            'class'=>'span6'
        ));  
        $id_idioma = $this->getElement('id_idioma');
        foreach($idioma->listar() as $retorno){
            $id_idioma->addMultiOption($retorno->id_idioma,utf8_decode($retorno->nombre_idioma));            
        }
        /* [ESTADO] */
        $estado = new Default_Model_DbTable_Estado();
        $this->addElement('select','id_estado',array(
            'label'=>'Estado :',
            'class'=>'span6'
        ));  
        $id_estado = $this->getElement('id_estado');
        foreach($estado->listar() as $retorno){
            $id_estado->addMultiOption($retorno->id_estado,utf8_decode($retorno->nombre_estado));            
        }
        /* [BOTON ENVIAR] */
        $this->addElement('submit','envio_formulario',array(
            'label'=>'Crear',
            'class'=>'btn btn-primary btn-large'
        ));
        unset($formulario,$campo);
    }


}


