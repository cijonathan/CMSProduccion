<?php

class Default_Model_Formulario
{
    public function tipo($id){
        if(is_numeric($id)){
            /*
             * Existen en Zend_Form para el metodo addElement() los siguientes tipos:
             * button, checkbox (or many checkboxes at once with multiCheckbox)
             * hidden, image, password, radio, reset, select(both regular and multi-select types)
             * submit, text, textarea
             * @source http://framework.zend.com/manual/en/zend.form.quickstart.html
             * @version 0.1             
             * file
             * @source http://stackoverflow.com/questions/2526286/zend-form-the-mimetype-of-file-foto-jpg-could-not-be-detected
             * @version 0.2             
             */
            #if($id == 1) : $tipo = 'file';
            if($id == 2) : $tipo = 'textarea';
            elseif($id == 3) : $tipo = 'select';
            elseif($id == 4) : $tipo = 'text';
            #elseif($id == 5) : $tipo = 'text';
            elseif($id == 6) : $tipo = 'text';
            elseif($id == 8) : $tipo = 'text';
            elseif($id == 9) : $tipo = 'textarea';
            elseif($id == 10) : $tipo = 'hidden';
            else : $tipo = 'text';
            endif;
            return $tipo;
            
        }else return false;
    }
    public function validacion($id){
        if(is_numeric($id)){
            /*
             * Existe en jQuery validate los siguientes clases que representan validaciones:
             * required, email, url, date, number
             */
            if($id == 1) : $validacion = 'required';
            elseif($id == 2) : $validacion = 'required number';
            elseif($id == 3) : $validacion = 'required email';
            elseif($id == 4) : $validacion = 'required dateISO datepicker';
            elseif($id == 5) : $validacion = 'required url';
            elseif($id == 6) : $validacion = '';
            else : $validacion = 'required';
            endif;
            return $validacion;
        }        
    }
    public function extraclass($tipo){
        if(is_numeric($tipo)){
            /* [TIPO] */
            if($tipo == 9) : $extra = 'span10 editor';       
            elseif($tipo == 2) : $extra = 'span10';       
            elseif($tipo == 6) : $extra = 'span6 dropdown-timepicker';       
            else : $extra = 'span6';            
            endif;
            return $extra;
        }
    }
    public function extrarows($tipo){
        /* [ALTO TEXTAREA] */        
        if(is_numeric($tipo)){
            if($tipo == 9) : $rows = 15;       
            elseif($tipo == 2) : $rows = 5;       
            else : $rows = 0;            
            endif;
            return $rows;            
        }        
    }
    public function extratrib($tipo){
        if(is_numeric($tipo)){
            if($tipo == 6) : $atrib = 'timepicker';
            else : $atrib = null;
            endif;
            
            return $atrib;
        }        
    }
    public function normaralizacion($id_modulo,$datos){
        if(is_numeric($id_modulo) && is_array($datos)){
            $campo = new Default_Model_DbTable_Campo();       
            /*foreach($modulo->listaranormar($id_modulo) as $retorno){                
                if(array_key_exists($retorno->nombre_campo_slug,$datos)){
                    /* [TIPO FECHA] */
                    /*if($retorno->id_tipo == 4){
                        $fecha = new Zend_Date($datos[$retorno->nombre_campo_slug],'dd/MM/yyyy');
                        $datos[$retorno->nombre_campo_slug] = $fecha->toString("YYYY-MM-dd");
                    /* [TIPO HORA] */
                    /*}elseif($retorno->id_tipo == 6){
                        $hora = explode(' ', $datos[$retorno->nombre_campo_slug]);
                        $datos[$retorno->nombre_campo_slug] = $hora[0].':00';
                    }
                }
            }*/
            $data = $campo->listaranormar($id_modulo);
            if(count($data)>0){
                foreach($data as $retorno){     
                    #if(array_key_exists($campo, $search))
                    /* [TIPO FECHA] */
                    if($retorno->id_tipo == 4){
                        $fecha = new Zend_Date($datos[$retorno->nombre_campo_slug],'dd/MM/yyyy');
                        $datos[$retorno->nombre_campo_slug] = $fecha->toString("YYYY-MM-dd");
                    /* [TIPO HORA] */
                    }elseif($retorno->id_tipo == 6){
                        $hora = explode(' ', $datos[$retorno->nombre_campo_slug]);
                        $datos[$retorno->nombre_campo_slug] = $hora[0].':00';
                    }else{
                        return true;
                    }              
                }
            }
            return $this->creardatos($datos);
        }        
    }
    private function creardatos($datos){
        if(is_array($datos)){
            /* [NUEVO ARREGLO] */
            $data = array();
            /* [LISTAR CAMPO DEL MODULO] */
            $id_modulo = $datos['id'];
            $modulo = new Default_Model_DbTable_Campo();
            foreach($modulo->obtenercampo($id_modulo) AS $retorno){                
                if(array_key_exists($retorno->nombre_campo_slug, $datos)){
                    $data[$retorno->nombre_campo_slug] = $datos[$retorno->nombre_campo_slug];
                    if($retorno->nombre_campo_slug == 'nombre'){
                        $data['nombre_slug'] = $this->amigable($datos['nombre'],$id_modulo);
                    }
                }
            }
            /* DOS PARAMETROS EXTRA */
            $data['id_idioma'] = $datos['id_idioma'];
            $data['id_estado'] = $datos['id_estado'];                
            
            return $data;
        }else return false;
    }
    private function amigable($valor,$id_modulo){
        /* [CODIFICACIÓN] */
        $nombre = trim($valor);
        $nombre = strtolower(preg_replace('/\s+/','-',$nombre));
        /*if($codificacion){
            $nombre = utf8_decode($nombre);
        }*/
        $datos = array(
        'Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'C'=>'C', 'c'=>'c', 'C'=>'C', 'c'=>'c',
        'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
        'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O',
        'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'S',
        'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e',
        'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o',
        'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b',
        'ÿ'=>'y', 'R'=>'R', 'r'=>'r', ','=>'','á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u'
        );
        $nombre = strtr($nombre,$datos);
        $nombre = preg_replace('/[^A-Za-z0-9-]+/','',$nombre);   
        $modulo = new Default_Model_DbTable_Modulo();

        /* PRIMER INGRESO (AGREGAR) */
        $existe = $modulo->existeamigable($nombre,$id_modulo);
        $nombre = $nombre.'-'.($existe+1);
        if($existe>0){
            if($modulo->amigablevalidacion($nombre,$id_modulo)){
                return $nombre;
            }else{                      
                return $modulo->amigablecoherente($nombre,$id_modulo);
            }
        }else{
            return $nombre;        
        }
    }
}

