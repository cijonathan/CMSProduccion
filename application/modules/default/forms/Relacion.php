<?php

class Default_Form_Relacion extends Zend_Form
{
    public function __construct($options = null) {
        parent::__construct($options);
    }

    public function init()
    {
		# variables
        $id_cardinalidad = $this->getAttrib('cardinalidad');
        $id_empresa = $this->getAttrib('id_empresa');
        $id_padre = $this->getAttrib('id_padre');
        $id_hijo = $this->getAttrib('id_hijo');
		
		# obtener datos relacionados
        $modulo = new Default_Model_DbTable_Modulo();
		$tabla = $modulo->obtenerdato($id_hijo);
		$nombre_modulo = $tabla->nombre_modulo;
		$nombre_modulo_slug = $tabla->nombre_modulo_slug;
		
		# Datos de la tabla relacionada
		$datosTabla = $modulo->listarDatos($id_empresa,$id_hijo,$id_padre,$id_cardinalidad);
		if($id_cardinalidad != 2){
			$tipo = "select";
		}else{
			$tipo = "multiCheckbox";
		}
		
        /* [NEW OPCIONES FORMULARIO] */
		# Id modulo
		// $this->addElement('hidden','id',array(
            // 'value'=>$id_padre
        // ));
        // $id = $this->getElement('id');  
        // $id->removeDecorator('Label');
		
		# Checkboxes
        $formulario = new Default_Model_Formulario();
		$this->addElement($tipo,$nombre_modulo_slug,array(
			'label'=>$nombre_modulo.':',
			'class'=>'required'
		));
		$checkbox = $this->getElement($nombre_modulo_slug);
        foreach($datosTabla as $aux){
            $checkbox->addMultiOption($aux->id,$aux->nombre);
			$checkbox->setSeparator(PHP_EOL);
        }
		
        # Boton submit
        $this->addElement('submit','envio_formulario',array(
            'label'=>'Agregar',
            'class'=>'btn btn-primary'
        ));
    }


}


