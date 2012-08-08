<?php

class Default_Model_DbTable_Modulo extends Zend_Db_Table_Abstract
{
    protected $_name = 'modulo';
    
    /* [LISTAR MODULOS TODOS LOS DATOS ]*/
    public function listar($id_empresa){       
        if(is_numeric($id_empresa)){
            /* [BASE] PERSONALIZADA */
            $base = $this->base();
            /* [CONSULTA] */
            $consulta = $base->select()
                    ->from(array('m'=>$this->_name),'*')
                    ->where('id_empresa = ?',$id_empresa)
                    ->where('id_estado = ?',1)
                    ->order('nombre_modulo ASC');
            return $base->fetchAll($consulta);
        }
    }  
    /* [LISTAR LOS ACCESOS DE LOS USUARIOS] */
    public function listaracceso($id_empresa,$id_usuario,$tipo){
        if(is_numeric($id_empresa) && is_numeric($id_usuario) && is_numeric($tipo)){
            /* [BASE] PERSONALIZADA */
            $base = $this->base();
            /* [CONSULTA] */
            $consulta = $base->select()
                    ->from(array('m'=>$this->_name),array('id_modulo'=>'m.id_modulo','nombre_modulo'=>'m.nombre_modulo'))
                    ->where('m.id_empresa = ?',$id_empresa);
            if($tipo == 1){
                $consulta->joinInner(array('mu'=>'modulo_has_usuario'),'m.id_modulo = mu.id_modulo',null)
                        ->where('mu.id_usuario = ?',$id_usuario);
            }
            $consulta->where('m.id_estado = ?',1)
                    ->order('m.nombre_modulo ASC');                    
            return $base->fetchAll($consulta);
        }else return false;                
        
    }
    public function totaldatos($id_modulo,$id_empresa){
        if(is_numeric($id_modulo) && is_numeric($id_empresa)){  
            /* DATOS MODULO */
            $modulo = $this->obtenerdato($id_modulo);            
            /* CONSULTA */
            $id = 'id_'.$modulo->nombre_modulo_slug;
            $base = $this->basepersonalizado($id_empresa);
            $consulta = $base->select()
                    ->from($modulo->nombre_modulo_slug,'*')
                    ->order($id.' DESC');
            
            $datos = array();
            foreach($consulta->query()->fetchAll() as $retorno){
               $datos[] = $retorno->$id;
            }
            return $datos;
        }        
    }
    public function showdato($id_campo,$id_registro){
        if(is_numeric($id_campo) && is_numeric($id_registro)){  
            /* CAMPO */
            $campo = new Default_Model_DbTable_Campo();
            $datocampo = $campo->obtener($id_campo);
            /* [ID_EMPRESA] */
            $modulo = $this->obtenerdato($datocampo->id_modulo);
            /* [BASE] */
            $base = $this->basepersonalizado($modulo->id_empresa);
            $id = 'id_'.$modulo->nombre_modulo_slug;
            /* [CONSULTA] */
            $consulta = $base->select()
                    ->from($modulo->nombre_modulo_slug,array('campo'=>$datocampo->nombre_campo_slug))
                    ->where('`'.$id.'` = ?', $id_registro);                        
            
            $dato = $consulta->query()->fetch(Zend_Db::FETCH_OBJ);            
            /* [FECHA] */
            if($datocampo->id_tipo == 4){
                $fecha = new Zend_Date($dato->campo,'yyyy-MM-dd');
                return $fecha->toString('dd/MM/yyyy');
            /* [HORA] */
            }elseif($datocampo->id_tipo == 6){               
                return substr($dato->campo,0,5);
            /* [BOLEANO] */
            }elseif($datocampo->id_tipo == 3){               
                return ($dato->campo) ? 'SI' : 'NO';
            /* [N/A] */
            }else{
                return $dato->campo;
            }
            
        }
    }
    /* [OBTENER LOS PERMISOS DE MODULO] */
    public function obtener($id_usuario){
        if(is_numeric($id_usuario)){
            /* [BASE] PERSONALIZADA */
            $base = $this->base();
            /* [CONSULTA] */
            $consulta = $base->select()
                    ->from(array('mu'=>'modulo_has_usuario'),array('id_modulo'=>'mu.id_modulo'))
                    ->where('id_usuario = ?',$id_usuario);
            $datos = array();
            foreach($base->fetchAll($consulta) as $retorno){
                $datos[] = $retorno->id_modulo;
            }
            return $datos;
        }else return false;
            
    }
    /* [OBTENER DATOS DE UN MODULO] */
    public function obtenerdato($id_modulo){
        if(is_numeric($id_modulo)){
            /* [CONSULTA] */
            $consulta = $this->select()
                    ->setIntegrityCheck(false)
                    ->from(array('m'=>$this->_name),'*')
                    ->where('id_modulo = ?',$id_modulo);
            return $consulta->query()->fetch(Zend_Db::FETCH_OBJ);
        }
    }
    /* [AGREGAR PERMISOS A LOS USUARIOS] */
    public function agregar($id_usuario,$id_modulo){
        if(is_numeric($id_usuario) && is_array($id_modulo)){
            if($this->eliminar($id_usuario)){
                /* [BASE] PERSONALIZADA */
                $base = $this->base();                
                /* [OBJETO a MODULO] */               
                $modulo = (object)$id_modulo;
                foreach($modulo as $retorno){
                    $base->insert('modulo_has_usuario',array('id_usuario'=>$id_usuario,'id_modulo'=>$retorno));
                }
                return true;
            }else return false;
        }
    }
    /* [AGREGAR DATOS DE MODULO] */
    public function agregardatos($datos,$id_modulo){
        if(is_array($datos) && is_numeric($id_modulo)){
            /* [OBTENER ID EMPRESA] */
            $dato = $this->obtenerdato($id_modulo);
            /* [BASE PERSONALIZADA] */
            $base = $this->basepersonalizado($dato->id_empresa);
            if($base->insert($dato->nombre_modulo_slug, $datos)){
                return $base->lastInsertId();
            }else return false;
        }        
    }
    /* [EDITAR DATOS] */
    public function editardatos($datos,$id_registro,$id_empresa,$id_modulo){        
        if(is_array($datos) && is_numeric($id_empresa) && is_numeric($id_registro) && is_numeric($id_modulo)){
            /* [BASE PERSONALIZADA] */
            $base = $this->basepersonalizado($id_empresa);
            /* MODULO */
            $modulo = $this->obtenerdato($id_modulo);
            $id = (string)'id_'.$modulo->nombre_modulo_slug;
            
            #$consulta = $base->select()
             #       ->from($modulo->nombre_modulo_slug,'*');
            
            
            if($base->update($modulo->nombre_modulo_slug,$datos,"`".$id."` = ".$id_registro)){
                return true;
            }else return false;
            
            /* [CONSULTA] */
            #$consulta = $base->update($modulo->nombre_modulo_slug,$datos,$id.' = '.$id_registro);
            #echo $consulta; exit;
            /* [NORMALIZACION] */
            /*$formulario = new Default_Model_Formulario();
            echo $datos['id'];
            $data = $formulario->normaralizacion($datos['id'],$data);
            var_dump($data);
            exit;*/
            /*foreach($campos as $retorno){
                $fila = $retorno->nombre_campo_slug;
                if(in_array($fila, $data)){
                    
                }else return false;
            
            exit;*/
            /* COMPRUEBO LOS CAMPOS */
            /*foreach() as $retorno){
                $item = $retorno->nombre_campo_slug;
                if($datos->$item == $retorno->nombre_campo_slug){
                    $data[$item] = $datos->$item;
                }               
            }
            var_dump($data); exit;*/
        }
    }
    /* [DETERMINAR SI EXISTE OTRO TITULO IGUAL] */
    public function existeamigable($amigable,$id_modulo){ 
            /* [OBTENER ID EMPRESA] */
            $modulo = $this->obtenerdato($id_modulo);
            /* [BASE PERSONALIZADA] */                    
            $base = $this->basepersonalizado($modulo->id_empresa);   
            /* [CONSULTA] */
            $columna = $base->quoteIdentifier('nombre_slug');
            $consulta = $base->select()
                    ->from($modulo->nombre_modulo_slug,'*')
                    ->where($base->quoteInto($columna.' LIKE ?','%'.$amigable.'%'));
            
            return $consulta->query()->rowCount();
            
    } 
    /* [AMIGABLE EXISTENTE] */
    public function amigablevalidacion($nombre,$id_modulo){
        if(is_string($nombre) && is_numeric($id_modulo)){
            /* [OBTENER ID EMPRESA] */
            $modulo = $this->obtenerdato($id_modulo);
            /* [BASE PERSONALIZADA] */                    
            $base = $this->basepersonalizado($modulo->id_empresa);   
            /* CONSULTA */
            
            $consulta = $base->select()
                    ->from($modulo->nombre_modulo_slug,'*')
                    ->where('nombre_slug = ?',$nombre);
            if($consulta->query()->rowCount()>0){
                return false;
            }else{
                return true;
            }
        }else return false;
    }
    public function amigablecoherente($nombre){
        if(is_string($nombre)){
            return $nombre.'-1';
        }else return false;     
    }
    /* ELIMINAR PERMISOS DEL LOS USUARIOS */
    private function eliminar($id_usuario){        
        if(is_numeric($id_usuario)){
            /* [BASE] PERSONALIZADA */
            $base = $this->base();
            /*
             * [CONSULTA]
             * @return true en ambos casos porque no siempre tendrá modulos asignados
             */
            if($base->delete('modulo_has_usuario','id_usuario = '.$id_usuario)) return true;
            else return true;
        }
    }   
    private function base(){
        /* [BASE DE DATOS PERSONALIZADA] */
        $config = new Zend_Config_Ini('../application/configs/application.ini', 'production');
        $db = Zend_Db::factory('Pdo_Mysql', $config->resources->db->params);
        $db->setFetchMode(Zend_Db::FETCH_OBJ);        
        return $db;        
    }   
    private function basepersonalizado($id_empresa){                
        if($id_empresa>0 && is_numeric($id_empresa)){     
            /* [OBTENER DATOS DE CONEXION] */
            $empresa = new Default_Model_DbTable_Proyecto();
            $datos = $empresa->obtener($id_empresa);            
            /* [INSTANCIA DE CONEXION] */
            $conexion = array(
                'host'       => $datos->servidor_empresa,
                'username'   => $datos->usuario_empresa,
                'password'   => $datos->clave_empresa,
                'dbname'     => $datos->basededatos_empresa,
				'charset'	 => "utf8",
                'persistent' => true
            );
            #var_dump($conexion); die;
            /* [CONEXION] */
            $db = Zend_Db::factory('Pdo_Mysql',$conexion);  
            $db->setFetchMode(Zend_Db::FETCH_OBJ); 
            return $db;
        }
    }
	
	##########################################################################################################################################
	# GALERIA FOTOGRAFICA 03-07-2012 ~ 05-07-2012 /
	##########################################################################################################################################
	# LISTAR IMAGENES
    public function listarimagenes($id_empresa,$id_registro,$id_modulo){        
        if(is_numeric($id_empresa) && is_numeric($id_registro) && is_numeric($id_modulo)){
            /* [BASE PERSONALIZADA] */
            $base = $this->basepersonalizado($id_empresa);
            /* MODULO */
            $modulo = $this->obtenerdato($id_modulo);
            $tabla = $modulo->nombre_modulo_slug."_galeria";
			
			$consulta = $base->select()
				->from($tabla,"*")
				->where("`id_".$modulo->nombre_modulo_slug."` = ".$id_registro)
				->order("orden_galeria");
            
            $datos = array();
            foreach($consulta->query()->fetchAll() as $retorno){
				$obj = new stdClass();
				$obj->id = $retorno->id_galeria;
				$obj->tipo = $retorno->id_tipo;
				$obj->descripcion = $retorno->descripcion_galeria;
				$obj->ruta_chica = $retorno->ruta_chica_galeria;
				$obj->ruta_grande = $retorno->ruta_grande_galeria;
				$obj->orden = $retorno->orden_galeria;
				$datos[] = $obj;
            }
            return $datos;
        }
    }
	
	# SUBIR FOTOGRAFIAS
    public function subirfotografia($datos,$id_empresa,$id_registro,$id_modulo){        
        if(is_array($datos) && is_numeric($id_empresa) && is_numeric($id_registro) && is_numeric($id_modulo)){
            /* [BASE PERSONALIZADA] */
            $base = $this->basepersonalizado($id_empresa);
            /* MODULO */
            $modulo = $this->obtenerdato($id_modulo);
            $tabla = $modulo->nombre_modulo_slug."_galeria";
			
			# Asignar mayor orden
            $consulta = $base->select()
				->from($tabla,'MAX(orden_galeria) as max')
				->where("`id_".$modulo->nombre_modulo_slug."` = ".$id_registro);
            if($max = $consulta->query()->fetch(Zend_Db::FETCH_OBJ)->max)
				$datos["orden_galeria"] = (int)$max + 1;
			else
				$datos["orden_galeria"] = 1;
            
			# Asignar imagen principal
			$consulta = $base->select()
				->from($tabla,'COUNT(id_galeria) as total')
				->where("`id_".$modulo->nombre_modulo_slug."` = ".$id_registro);
            if($consulta->query()->fetch(Zend_Db::FETCH_OBJ)->total > 0)
				$datos["id_tipo"] = 0;
			else
				$datos["id_tipo"] = 1;
			
			$base->insert($tabla,$datos);
			return true;
        }
    }
	
	# GUARDAR COMENTARIO
    public function guardarComentario($id_empresa,$id_imagen,$id_modulo,$comentario){
        if(is_numeric($id_empresa) && is_numeric($id_imagen) && is_numeric($id_modulo)){
            /* [BASE PERSONALIZADA] */
            $base = $this->basepersonalizado($id_empresa);
            /* MODULO */
            $modulo = $this->obtenerdato($id_modulo);
            $tabla = $modulo->nombre_modulo_slug."_galeria";
			
			return ($base->update($tabla,array("descripcion_galeria" => $comentario),"id_galeria = ".$id_imagen))?true:false;
        }
    }
	
	# ELIMINAR IMAGEN
    public function eliminarImagen($id_empresa,$id_imagen,$id_modulo){
        if(is_numeric($id_empresa) && is_numeric($id_imagen) && is_numeric($id_modulo)){
            /* [BASE PERSONALIZADA] */
            $base = $this->basepersonalizado($id_empresa);
            /* MODULO */
            $modulo = $this->obtenerdato($id_modulo);
            $tabla = $modulo->nombre_modulo_slug."_galeria";
			
			# Obtener ruta de imagen
			$consulta = $base->select()
				->from($tabla,"ruta_chica_galeria")
				->where("id_galeria = ".$id_imagen);
			$imagen = $consulta->query()->fetch(Zend_Db::FETCH_OBJ)->ruta_chica_galeria;
			return ($base->delete($tabla,"id_galeria = ".$id_imagen." AND id_tipo != 1"))?$imagen:false;
        }
    }
	
	# MARCAR COMO PRINCIPAL
    public function marcarImagenPrincipal($id_empresa,$id_imagen,$id_modulo,$id_registro){
        if(is_numeric($id_empresa) && is_numeric($id_imagen) && is_numeric($id_modulo) && is_numeric($id_registro)){
            /* [BASE PERSONALIZADA] */
            $base = $this->basepersonalizado($id_empresa);
            /* MODULO */
            $modulo = $this->obtenerdato($id_modulo);
            $tabla = $modulo->nombre_modulo_slug."_galeria";
			
			$base->update($tabla,array("id_tipo" => 0),"`id_".$modulo->nombre_modulo_slug."` = ".$id_registro);
			return ($base->update($tabla,array("id_tipo" => 1),"id_galeria = ".$id_imagen))?true:false;
        }
    }
	
	# REORDENAR GALERIA
    public function guardarPosicionesGaleria($id_empresa,$id_modulo,$id_registro,$datos){
        if(is_numeric($id_empresa) && is_numeric($id_modulo) && is_numeric($id_registro)){
            /* [BASE PERSONALIZADA] */
            $base = $this->basepersonalizado($id_empresa);
            /* MODULO */
            $modulo = $this->obtenerdato($id_modulo);
            $tabla = $modulo->nombre_modulo_slug."_galeria";
			
			foreach($datos['orden'] as $orden=>$imagen_id){
				$base->update($tabla,array("orden_galeria" => $orden),"`id_".$modulo->nombre_modulo_slug."` = ".$id_registro." AND id_galeria = ".$imagen_id);
			}
			return true;
        }
    }
	
	# OBTENER IMAGEN
    public function obtenerImagen($id_empresa,$id_imagen,$id_modulo){
        if(is_numeric($id_empresa) && is_numeric($id_imagen) && is_numeric($id_modulo)){
            /* [BASE PERSONALIZADA] */
            $base = $this->basepersonalizado($id_empresa);
            /* MODULO */
            $modulo = $this->obtenerdato($id_modulo);
            $tabla = $modulo->nombre_modulo_slug."_galeria";
			
			# Obtener ruta de imagen
			$consulta = $base->select()
				->from($tabla,"ruta_chica_galeria")
				->where("id_galeria = ".$id_imagen);
			$imagen = $consulta->query()->fetch(Zend_Db::FETCH_OBJ)->ruta_chica_galeria;
			return str_replace("_chica","",$imagen);
        }
    }
	
	# OBTENER DIMENSIONES
    public function obtenerDimensiones($id_modulo){
        if(is_numeric($id_modulo)){
			$base = $this->base();
            $consulta = $base->select()
				->from("modulo_imagen_configuracion",'*')
				->where("id_modulo = ".$id_modulo);
            $dimensiones = $base->fetchAll($consulta);
			
			$obj = new stdClass();
			if(count($dimensiones)){
				$obj->ancho_grande  = $dimensiones[0]->ancho_grande;
				$obj->ancho_mediana = $dimensiones[0]->ancho_mediana;
				$obj->ancho_chica   = $dimensiones[0]->ancho_chica;
				$obj->alto_grande   = $dimensiones[0]->alto_grande;
				$obj->alto_mediana  = $dimensiones[0]->alto_mediana;
				$obj->alto_chica    = $dimensiones[0]->alto_chica;
			}else{
				$obj->ancho_grande  = 800;
				$obj->alto_grande   = 600;
				$obj->ancho_mediana = 320;
				$obj->alto_mediana  = 240;
				$obj->ancho_chica   = 100;
				$obj->alto_chica    = 75;
			}
			return $obj;
		}
    }
	
	##########################################################################################################################################
	# ARCHIVOS 05-07-2012 /
	##########################################################################################################################################
	# LISTAR ARCHIVOS
    public function listarArchivos($id_empresa,$id_registro,$id_modulo){        
        if(is_numeric($id_empresa) && is_numeric($id_registro) && is_numeric($id_modulo)){
            /* [BASE PERSONALIZADA] */
            $base = $this->basepersonalizado($id_empresa);
            /* MODULO */
            $modulo = $this->obtenerdato($id_modulo);
            $tabla = $modulo->nombre_modulo_slug."_archivo";
			
			$consulta = $base->select()
				->from($tabla,"*")
				->where("`id_".$modulo->nombre_modulo_slug."` = ".$id_registro)
				->order("orden_archivo");
            
            $datos = array();
            foreach($consulta->query()->fetchAll() as $retorno){
				$obj = new stdClass();
				$obj->id = $retorno->id_archivo;
				$obj->nombre = $retorno->nombre_archivo;
				$obj->peso = $retorno->peso_archivo;
				$obj->formato = $retorno->formato_archivo;
				$obj->ruta_archivo = $retorno->ruta_archivo;
				$datos[] = $obj;
            }
            return $datos;
        }
    }
	
	# SUBIR ARCHIVOS
    public function subirArchivo($datos,$id_empresa,$id_registro,$id_modulo){        
        if(is_array($datos) && is_numeric($id_empresa) && is_numeric($id_registro) && is_numeric($id_modulo)){
            /* [BASE PERSONALIZADA] */
            $base = $this->basepersonalizado($id_empresa);
            /* MODULO */
            $modulo = $this->obtenerdato($id_modulo);
            $tabla = $modulo->nombre_modulo_slug."_archivo";
			
            $consulta = $base->select()
				->from($tabla,'MAX(orden_archivo) as max')
				->where("`id_".$modulo->nombre_modulo_slug."` = ".$id_registro);
			
            if($max = $consulta->query()->fetch(Zend_Db::FETCH_OBJ)->max)
				$datos["orden_archivo"] = (int)$max + 1;
			else
				$datos["orden_archivo"] = 1;
            
			$base->insert($tabla,$datos);
			return true;
        }
    }
	
	# ELIMINAR ARCHIVO
    public function eliminarArchivo($id_empresa,$id_archivo,$id_modulo){
        if(is_numeric($id_empresa) && is_numeric($id_archivo) && is_numeric($id_modulo)){
            /* [BASE PERSONALIZADA] */
            $base = $this->basepersonalizado($id_empresa);
            /* MODULO */
            $modulo = $this->obtenerdato($id_modulo);
            $tabla = $modulo->nombre_modulo_slug."_archivo";
			
			# Obtener ruta de imagen
			$consulta = $base->select()
				->from($tabla,"ruta_archivo")
				->where("id_archivo = ".$id_archivo);
			$imagen = $consulta->query()->fetch(Zend_Db::FETCH_OBJ)->ruta_archivo;
			return ($base->delete($tabla,"id_archivo = ".$id_archivo))?$imagen:false;
        }
    }
	
	# REORDENAR ARCHIVOS
    public function guardarPosicionesArchivos($id_empresa,$id_modulo,$id_registro,$datos){
        if(is_numeric($id_empresa) && is_numeric($id_modulo) && is_numeric($id_registro)){
            /* [BASE PERSONALIZADA] */
            $base = $this->basepersonalizado($id_empresa);
            /* MODULO */
            $modulo = $this->obtenerdato($id_modulo);
            $tabla = $modulo->nombre_modulo_slug."_archivo";
			
			foreach($datos['orden'] as $orden=>$archivo_id){
				$base->update($tabla,array("orden_archivo" => $orden),"`id_".$modulo->nombre_modulo_slug."` = ".$id_registro." AND id_archivo = ".$archivo_id);
			}
			return true;
        }
    }
	
	# LISTAR DATOS
    public function listarDatos($id_empresa,$id_hijo,$id_padre,$id_cardinalidad,$id_registro = false){
        if(is_numeric($id_empresa) && is_numeric($id_hijo) && is_numeric($id_padre) && is_numeric($id_cardinalidad)){
            /* [BASE PERSONALIZADA] */
            $base = $this->basepersonalizado($id_empresa);
			
            /* MODULO */
            $modulo_hijo = $this->obtenerdato($id_hijo);
            $tabla_hijo = $modulo_hijo->nombre_modulo_slug;
			$modulo_padre = $this->obtenerdato($id_padre);
			$tabla_rel = $modulo_padre->nombre_modulo_slug."_has_".$tabla_hijo;
			
			$consulta = $base->select()
				->from(array("m" => $tabla_hijo),array("id" => "id_".$tabla_hijo, "nombre" => "nombre"))
				->order("nombre");
			if($id_cardinalidad == 2 && $id_registro){
				$consulta->joinInner(array("rel" => $tabla_rel),"rel.id_".$tabla_hijo." = m.id_".$tabla_hijo, false)
					->where("`rel`.`id_".$modulo_padre->nombre_modulo_slug."` = ".$id_padre);
			}elseif($id_cardinalidad != 2 && $id_registro){
				$consulta->where("`id_".$modulo_padre->nombre_modulo_slug."` = ".$id_registro);
			}
            return $base->fetchAll($consulta);
        }
    }
	
	# GUARDAR RELACIONES
    public function guardarRelaciones($id_empresa,$id_hijo,$id_padre,$cardinalidad,$datos,$id_registro){
        if(is_numeric($id_empresa) && is_numeric($id_hijo)){
            /* [BASE PERSONALIZADA] */
            $base = $this->basepersonalizado($id_empresa);
            

			# Modulo hijo
            $modulo = $this->obtenerdato($id_hijo);
            $tabla = $modulo->nombre_modulo_slug;
            
            $consulta = $base->select()
                    ->from($tabla);
            
			
			# Modulo Padre
			$modulo_padre = $this->obtenerdato($id_padre);
			$tabla_rel = $modulo_padre->nombre_modulo_slug."_has_".$tabla;
		
			// print_r(array('`id_'.$modulo_padre->nombre_modulo_slug.'`' => $id_registro)); die;
		
			# Guardar en BD
			if($cardinalidad == 2){
				$base->delete('`'.$tabla_rel.'`',array('`id_'.$modulo_padre->nombre_modulo_slug.'`' => $id_padre));
				foreach($datos[$tabla] as $dato){
					$base->insert('`'.$tabla_rel.'`',array('`id_'.$modulo_padre->nombre_modulo_slug.'`' => $id_padre, 'id_'.$tabla => $dato));
				}
			}else{                                  
                            $base->update($tabla,array('id_'.$modulo_padre->nombre_modulo_slug=>$id_registro),'id_'.$tabla.'='.$datos[$tabla]);                                                     
			}
			return true;
        }
    }

	# ELIMIANR RELACION
	public function eliminarRelacion($id_empresa,$id_registro,$id_hijo,$id_padre,$id_cardinalidad){
        if(is_numeric($id_empresa) && is_numeric($id_registro) && is_numeric($id_hijo) && is_numeric($id_padre) && is_numeric($id_cardinalidad)){
            /* [BASE PERSONALIZADA] */
            $base = $this->basepersonalizado($id_empresa);
			# Modulo hijo
            $modulo_hijo = $this->obtenerdato($id_hijo);
            $tabla_hijo = $modulo_hijo->nombre_modulo_slug;
			
			# Modulo Padre
			$modulo_padre = $this->obtenerdato($id_padre);
			$tabla_rel = $modulo_padre->nombre_modulo_slug."_has_".$tabla_hijo;
			
			# Datos padre
			if($id_cardinalidad == 2){
				# eliminar relacion N,N
				return ($base->delete($tabla_rel,"id_".$modulo_padre->nombre_modulo_slug." = ".$id_padre." AND id_".$tabla_hijo." = ".$id_registro))?true:false;
			}else{
				return ($base->update($tabla_hijo,array("id_".$modulo_padre->nombre_modulo_slug => ""), "`id_".$tabla_hijo."` = ".$id_registro))?true:false;
			}
        }
    }
	public function eliminarRegistro($id_registro,$id_modulo){
        if(is_numeric($id_registro) && is_numeric($id_modulo)){
            /* DATO MODULO */
            $modulo = $this->obtenerdato($id_modulo);
            /* CONEXION PERSONALIZADA */
            $base = $this->basepersonalizado($modulo->id_empresa);
			/* FTP */
			# Conectar a FTP
			$ftp_server = "cms.hostprimario.com";
			$ftp_user_name = "upload@cms.hostprimario.com";
			$ftp_user_pass = "4hNOXu7hnwX9";
			$conn_id = ftp_connect($ftp_server);
			$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);			
            /* ELIMINAR REGISTRO */  
            /* GALERIA */
            if($this->existeComponente($id_modulo,true)>0){
                $nombre_componente = $modulo->nombre_modulo_slug.'_galeria';
                $id_componente = 'id_'.$modulo->nombre_modulo_slug;
                $consulta = $base->select()
                        ->from($nombre_componente,'*')
                        ->where('`'.$id_componente.'` = ?', $id_registro);
                foreach($consulta->query()->fetchAll() as $retorno){
					if($login_result){
						$archivo = str_replace('/upload/','',$retorno->ruta_chica_galeria);						
						ftp_delete($conn_id, $archivo);
						ftp_delete($conn_id, str_replace("_chica","_grande",$archivo));
						ftp_delete($conn_id, str_replace("_chica","_mediana",$archivo));
						ftp_delete($conn_id, str_replace("_chica","",$archivo));
					}
				}
				$base->delete($nombre_componente,'`'.$id_componente.'` = '.$id_registro);
				unset($nombre_componente,$id_componente,$consulta,$archivo);
            }
			/* ARCHIVO */
            if($this->existeComponente($id_modulo,false)>0){
               $nombre_componente = $modulo->nombre_modulo_slug.'_archivo';
                $id_componente = 'id_'.$modulo->nombre_modulo_slug;
                $consulta = $base->select()
                        ->from($nombre_componente,'*')
                        ->where('`'.$id_componente.'` = ?', $id_registro);		
                foreach($consulta->query()->fetchAll() as $retorno){
					if($login_result){
						$archivo = str_replace('/upload/','',$retorno->ruta_archivo);						
						ftp_delete($conn_id, $archivo);					
					}
				}	
				$base->delete($nombre_componente,'`'.$id_componente.'` = '.$id_registro);
				unset($nombre_componente,$id_componente,$consulta,$archivo);
			}			
			/* ELIMINAR REGISTR PRINCIPAL */
            $id_componente = 'id_'.$modulo->nombre_modulo_slug;			
			if($base->delete($modulo->nombre_modulo_slug,'`'.$id_componente.'` = '.$id_registro)){
				return true;
			}
        }
    }
    public function existeComponente($id_modulo,$tipo){
        if(is_numeric($id_modulo)){
            $consulta = $this->select()
                    ->setIntegrityCheck(false)
                    ->from('modulo_has_componente','*')
                    ->where('id_modulo = ?', $id_modulo);
                    if($tipo){
                        $consulta->where('id_componente = ?',1);
                    }else{
                        $consulta->where('id_componente = ?',2);                       
                    }
            return $consulta->query()->rowCount();
        }        
    }
}

