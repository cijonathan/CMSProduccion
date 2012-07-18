<?php

class ModuloController extends Zend_Controller_Action
{
    protected $id_modulo;
    protected $id_empresa;
    protected $id_usuario;
    protected $id_registro;

    public function init(){
        /* [ID MODULO] */  
        $this->id_modulo = $this->_getParam('id',0); 
        /* [OBTENER DATOS MODULO] */
        $modulo = new Default_Model_DbTable_Modulo();
        $this->view->dato = $modulo->obtenerdato($this->id_modulo);    
        /* [ID EMPRESA] */
        $this->sesion = new Zend_Session_Namespace('login');        
        $this->id_empresa = $this->sesion->id_empresa;
        /* [ID USUARIO] */
        $registro = new Zend_Registry();
        $this->id_usuario = $registro->get('id_usuario');
        /* [ID REGISTRO] */        
        $this->id_registro = $this->_getParam('idregistro',0);
    }
    public function topAction(){}

    public function indexAction(){ 
        /* [EXITO y ERROR] */
        $mensaje = new Zend_Session_Namespace('mensaje');
        $this->view->exito = $mensaje->exito;
        $this->view->error = $mensaje->error;
        $mensaje->setExpirationSeconds(1);
        unset($mensaje);          
        /* [TITLE] */
        $this->view->headTitle()->prepend('Modulo - ');        
        /* [CAMPO] */
        $campo = new Default_Model_DbTable_Campo();
        $this->view->campo = $campo->listartabla($this->id_modulo);
        /* [DATOS] */
        $modulo = new Default_Model_DbTable_Modulo();
        $this->view->total = $modulo->totaldatos($this->id_modulo,$this->id_empresa);
        /* [UN DATO] */
        $this->view->modulo = $modulo;
        
        /* [COMPONENTES] */
        $componente = new Default_Model_DbTable_Componente();
        $this->view->fotos = $componente->existeComponente($this->id_modulo,'fotos');
        $this->view->archivos = $componente->existeComponente($this->id_modulo,'archivos');
        /* [RELACIONES] */
        $relacion = new Default_Model_DbTable_Relacion();
        $this->view->relaciones = $relacion->existeRelacion($this->id_modulo);
    }
    public function nuevoAction(){       
        /* [TITLE] */
        $this->view->headTitle()->prepend('Nuevo item del Modulo - ');    
        /* ENVIO VIEW ID MODULO */
        $this->view->id_modulo = $this->id_modulo;
        /* [FORMULARIO] */
        $formulario = new Default_Form_Campo(array('id'=>$this->id_modulo)); 
        $this->view->formulario_agregar = $formulario;
        /* [CONSULTA SI HAY GOOGLE MAPS] */
        $campo = new Default_Model_DbTable_Campo();
        if($campo->googlemaps($this->id_modulo)>0){
            $this->view->headScript()->appendFile('http://maps.googleapis.com/maps/api/js?sensor=false&amp;language=es&amp;region=ES');
            $this->view->headScript()->appendFile('/js/jquery.googlemaps.js');            
        }    
        /* [PROCESAR FORMULARIO] */
        $respuesta = $this->getRequest();
        if($respuesta->isPost()){   
            if($formulario->isValid($this->_request->getPost())){      
                $datos = $formulario->getValues();                
                /* [NORMAR DATOS] */
                $formulario = new Default_Model_Formulario();
                $datos = $formulario->normaralizacion($this->id_modulo, $datos);               
                /* [INSERTAR DATOS */
                $modulo = new Default_Model_DbTable_Modulo();
                $mensaje = new Zend_Session_Namespace('mensaje');                 
                $retorno = $modulo->agregardatos($datos,$this->id_modulo);
                if(count($retorno)>0){
                    $mensaje->exito = true;
                }else{
                    $mensaje->error = true;                    
                }
                $this->_redirect('modulo/index/id/'.$this->id_modulo);
            }
        }           
    }
    public function relacionAction(){ 
        /* [TITLE] */
        $this->view->headTitle()->prepend('Relaciones del Modulo - ');       
        /* ENVIO VIEW ID MODULO */
        $this->view->id_modulo = $this->id_modulo;   
        /* [FORMULARIO] */
        $formulario = new Default_Form_Relacion(array('id'=>$this->id_modulo,'idregistro'=>$this->id_registro)); 
        $this->view->formulario_relacion = $formulario;  
        /* [RELACION] */
        #$relacion = new Default_Model_DbTable_Relacion();
        #$relacion->listar($this->id_empresa);
    }
    public function editarAction(){
        /* [TITLE] */
        $this->view->headTitle()->prepend('Editar registro - ');         
        /* ENVIO VIEW ID MODULO */
        $this->view->id_modulo = $this->id_modulo;           
        $this->view->id_registro = $this->id_registro;           
        /* [FORMULARIO] */
        $formulario = new Default_Form_Campo(array('id'=>$this->id_modulo)); 
        $formulario->getElement('envio_formulario')->setLabel('Actualizar');
        $this->view->formulario_agregar = $formulario;                
        /* [CONSULTA SI HAY GOOGLE MAPS] */
        $campo = new Default_Model_DbTable_Campo();
        if($campo->googlemaps($this->id_modulo)>0){
            $cordenadas = $campo->mapa($this->id_registro,$this->id_modulo);
            $this->view->headScript()->appendFile('http://maps.googleapis.com/maps/api/js?sensor=false&amp;language=es&amp;region=ES');          
            $this->view->mapa = true;
            $this->view->latitud = $cordenadas[0];
            $this->view->longitud = $cordenadas[1];
        }   
        /* [PROCESAR FORMULARIO] */
        $respuesta = $this->getRequest();
        if($respuesta->isPost()){  
            if($formulario->isValid($this->_request->getPost())){   
                /* DATOS */
                $datos = $formulario->getValues(); 
                /* [NORMAR DATOS] */
                $formulario = new Default_Model_Formulario();
                $data = $formulario->normaralizacion($this->id_modulo, $datos,false);     
                $modulo = new Default_Model_DbTable_Modulo();
                $mensaje = new Zend_Session_Namespace('mensaje');                
                if($modulo->editardatos($data,$this->id_registro,$this->id_empresa,$this->id_modulo)){
                    $mensaje->exito = true;                 
                }else{
                    $mensaje->error = true;
                }
                $this->_redirect('modulo/index/id/'.$this->id_modulo);                
            }            
        }else{  
            /* [RECUPERA LOS DATOS] */
            $datosformulario = $campo->obtenerdatos($this->id_registro,$this->id_modulo);
            $this->view->datosformulario = (object)$datosformulario;
            /* [LLENAR FORMULARIO] */
            $formulario->populate((array)$datosformulario);
        }                
        
        /* [COMPONENTES] */
        $componente = new Default_Model_DbTable_Componente();
        $this->view->fotos = $componente->existeComponente($this->id_modulo,'fotos');
        $this->view->archivos = $componente->existeComponente($this->id_modulo,'archivos');
        /* [RELACIONES] */
        $relacion = new Default_Model_DbTable_Relacion();
        $this->view->relaciones = $relacion->existeRelacion($this->id_modulo);        
    }
	
	##########################################################################################################################################
	# GALERIA FOTOGRAFICA, 03-07-2012 ~ 05-07-2012 /
	##########################################################################################################################################
	# GALERIA FOTOGRAFICA
	public function galeriaFotograficaAction(){		
        # Variables 
        $this->view->id_modulo = $this->id_modulo;
        $this->view->id_registro = $this->id_registro;
        $this->view->id_empresa = $this->id_empresa;
        
        /* [RECUPERA LOS DATOS] */
        $campo = new Default_Model_DbTable_Campo();
        $datosformulario = $campo->obtenerdatos($this->id_registro,$this->id_modulo);
        $this->view->datosformulario = (object)$datosformulario;        
		
		# Mensaje de exito
		$msg_img = new Zend_Session_Namespace('mensaje-imagen');
		if(is_string($msg_img->estado)){
			$this->view->mensaje = '
			<div class="alert '.(($msg_img->estado == "exito")?"alert-success":"alert-error").'">
				<h2 class="alert-heading">'.$msg_img->titulo.'</h2>
				'.$msg_img->texto.'
			</div>
			';
			$msg_img->setExpirationSeconds(1);
			unset($msg_img);
		}
		
		# Listado de imagenes
		$modulo = new Default_Model_DbTable_Modulo();
		$this->view->imagenes = $modulo->listarimagenes($this->id_empresa,$this->id_registro,$this->id_modulo);
		
		# Titulo y librerias
		$this->view->headTitle()->prepend('Galeria fotogrífica - ');
		
		$this->view->headLink()
			->appendStylesheet('/js/plupload/jquery.plupload.queue/css/jquery.plupload.queue.css')
			->appendStylesheet('/js/zoombox/zoombox.css');
		$this->view->headScript()
			->appendFile('/js/plupload/plupload.full.js')
			->appendFile('/js/plupload/jquery.plupload.queue/jquery.plupload.queue.js')
			->appendFile('/js/plupload/plupload-config-galeria.js')
			->appendFile('/js/zoombox/zoombox.js');
			
	}

	# SUBIR FOTOGRAFIAS
	public function subirFotografiasAction(){
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
		# Cargar libreria de recortes
		require_once "phpthumb/ThumbLib.php";
		
		# Variables
		$id_empresa  = $this->id_empresa;
		$id_registro = $this->_getParam("codigo_registro");
		$id_modulo   = $this->_getParam("codigo_modulo");
	
		# Verificar que vengan el archivo
		if(!empty($_FILES)){
			# Conectar a FTP
			$ftp_server = "cms.hostprimario.com";
			$ftp_user_name = "upload@cms.hostprimario.com";
			$ftp_user_pass = "4hNOXu7hnwX9";
			$conn_id = ftp_connect($ftp_server);
			$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);

			# checkear coneccion
			if((!$conn_id) || (!$login_result)){
				echo "FTP connection has failed!";
				echo "Attempted to connect to $ftp_server for user $ftp_user_name";
				exit;
			}else{
				
				# Nombre de la empresa
				$empresa = new Default_Model_DbTable_Empresa();
				$datos_empresa = $empresa->obtener($id_empresa);
				
				# Nombre del modulo
				$modulo = new Default_Model_DbTable_Modulo();
				$dimensiones = $modulo->obtenerDimensiones($id_modulo); 
				$datos_module = $modulo->obtenerdato($id_modulo);
				
				# Crear carpeta del proyecto si no existe
				if(!is_dir('/'.$datos_empresa->nombre_empresa_slug."/")){
					ftp_mkdir($conn_id,'/'.$datos_empresa->nombre_empresa_slug);
				}
					
				# Crear carpeta fotos si no existe
				if(!is_dir("/".$datos_empresa->nombre_empresa_slug."/fotos/"))
					ftp_mkdir($conn_id,"/".$datos_empresa->nombre_empresa_slug."/fotos");
				
				# Crear carpeta del modulo si no existe
				if(!is_dir("/".$datos_empresa->nombre_empresa_slug."/fotos/".$datos_module->nombre_modulo_slug."/"))
					ftp_mkdir($conn_id,"/".$datos_empresa->nombre_empresa_slug."/fotos/".$datos_module->nombre_modulo_slug."/");
				
				# Crear carpeta del registro si no existe
				if(!is_dir("/".$datos_empresa->nombre_empresa_slug."/fotos/".$datos_module->nombre_modulo_slug."/".$id_registro."/"))
					ftp_mkdir($conn_id,"/".$datos_empresa->nombre_empresa_slug."/fotos/".$datos_module->nombre_modulo_slug."/".$id_registro."/");

				# Carpeta y nombre del archivo
				$dir = "/".$datos_empresa->nombre_empresa_slug."/fotos/".$datos_module->nombre_modulo_slug."/".$id_registro."/";
				$dir_root = $dir;
				$info = pathinfo($_FILES['file']['name']);
				$nombreImagen = $id_registro."_".date("YmdGis").".".$info['extension'];
				$archivoTemp = $_FILES['file']['tmp_name'];
				$archivoNuevo = rtrim($dir_root,'/').'/'.$nombreImagen;

				# Subir imagen original y realizar recortes grande, mediano y chico
				sleep(1);
				if(ftp_put($conn_id, $archivoNuevo, $archivoTemp, FTP_BINARY)){
					# Nombre de recortes
					$imagen_grande  = str_replace(".","_grande.",$archivoNuevo);
					$imagen_mediana = str_replace(".","_mediana.",$archivoNuevo);
					$imagen_chica   = str_replace(".","_chica.",$archivoNuevo);
					
					# Subir recortes y cambiar permisos
					ftp_put($conn_id, $imagen_grande, $archivoTemp, FTP_BINARY);
					ftp_chmod($conn_id, 0777, $imagen_grande);
					ftp_put($conn_id, $imagen_mediana, $archivoTemp, FTP_BINARY);
					ftp_chmod($conn_id, 0777, $imagen_mediana);
					ftp_put($conn_id, $imagen_chica, $archivoTemp, FTP_BINARY);
					ftp_chmod($conn_id, 0777, $imagen_chica);
					
					#echo 'pase subida'; exit;
					
					# Recortar
					$root = '/home/cmshostp/public_html/upload';
					$ruta_grande_root = (string)$root.$imagen_grande;
					#echo $ruta_grande_2; exit;
					$thumb = phpthumb_ThumbLib::create($ruta_grande_root);					
					$thumb->adaptiveResize($dimensiones->ancho_grande, $dimensiones->alto_grande);
					$thumb->save($root.$imagen_grande, 'jpg');
					
					$thumb->adaptiveResize($dimensiones->ancho_mediana, $dimensiones->alto_mediana);
					$thumb->save($root.$imagen_mediana, 'jpg');
					
					$thumb->adaptiveResize($dimensiones->ancho_chica, $dimensiones->alto_chica);
					$thumb->save($root.$imagen_chica, 'jpg');
					
					$mensaje = new Zend_Session_Namespace('mensaje-imagen');
					$mensaje->estado = "exito";
					$mensaje->titulo = "¡EXITO!";
					$mensaje->texto = "Las imágenes han sido almacenadas exitosamente.";

					# Subir imagen a la BD
					$datos = array(
						"id_".$datos_module->nombre_modulo_slug => $id_registro,
						"ruta_grande_galeria" => '/upload/'.$dir.str_replace(".","_grande.",$nombreImagen),
						"ruta_mediana_galeria" => '/upload/'.$dir.str_replace(".","_mediana.",$nombreImagen),
						"ruta_chica_galeria" => '/upload/'.$dir.str_replace(".","_chica.",$nombreImagen),
						"descripcion_galeria" => $info['filename']
					);
					
					
					$modulo->subirfotografia($datos,$id_empresa,$id_registro,$id_modulo);
				}
			}
		}
	}
	
	# GUARDAR COMENTARIO
	public function guardarComentarioGaleriaAction(){
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
		
		# Guardar comentario imagen
		$modulo = new Default_Model_DbTable_Modulo();
		if($modulo->guardarComentario($this->id_empresa,$this->_getParam("imagen"),$this->_getParam("modulo"),$this->_getParam("comentario"))){
			echo json_encode(array("msg" => '
			<div class="alert alert-success">
				<h2 class="alert-heading">¡EXITO!</h2>
				El comentario de la imagen ha sido almacenado exitosamente.
			</div>
			'));
		}else{
			echo json_encode(array("msg" => '
			<div class="alert alert-error">
				<h2 class="alert-heading">¡ERROR!</h2>
				El comentario no se pudo almacenar o no cambió el texto.
			</div>
			'));
		}
	}
	
	# ELIMINAR IMAGEN
	public function eliminarImagenGaleriaAction(){
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
		
		# Eliminar imagen
		$modulo = new Default_Model_DbTable_Modulo();
		if($imagen = $modulo->eliminarImagen($this->id_empresa,$this->_getParam("imagen"),$this->_getParam("modulo"))){
			# Conectar a FTP
			$ftp_server = "cms.hostprimario.com";
			$ftp_user_name = "upload@cms.hostprimario.com";
			$ftp_user_pass = "4hNOXu7hnwX9";
			$conn_id = ftp_connect($ftp_server);
			$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);

			# checkear coneccion
			if($login_result){
				$archivo = str_replace('/upload/','',$imagen);
				ftp_delete($conn_id, $archivo);
				ftp_delete($conn_id, str_replace("_chica","_grande",$archivo));
				ftp_delete($conn_id, str_replace("_chica","_mediana",$archivo));
				ftp_delete($conn_id, str_replace("_chica","",$archivo));
			}
			
			echo json_encode(array(
			"result" => true,
			"msg" => '
			<div class="alert alert-success">
				<h2 class="alert-heading">¡EXITO!</h2>
				La imagen ha sido eliminada exitosamente.
			</div>
			'));
		}else{
			echo json_encode(array(
			"result" => false,
			"msg" => '
			<div class="alert alert-error">
				<h2 class="alert-heading">¡ERROR!</h2>
				La imagen no se pudo eliminar por: <br /><ul><li>La imagen está marcada como principal.</li><li>Error en el sistema</li></ul>
			</div>
			'));
		}
	}
	
	# MARCAR IMAGEN PRINCIPAL
	public function marcarImagenPrincipalAction(){
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
		
		# Guardar comentario imagen
		$modulo = new Default_Model_DbTable_Modulo();
		if($modulo->marcarImagenPrincipal($this->id_empresa,$this->_getParam("imagen"),$this->_getParam("modulo"),$this->_getParam("registro"))){
			echo json_encode(array(
			"result" => true,
			"msg" => '
			<div class="alert alert-success">
				<h2 class="alert-heading">¡EXITO!</h2>
				La imagen ha sido marcada como principal.
			</div>
			'));
		}else{
			echo json_encode(array(
			"result" => false,
			"msg" => '
			<div class="alert alert-error">
				<h2 class="alert-heading">¡ERROR!</h2>
				La imagen no se pudo marcar como principal.
			</div>
			'));
		}
	}
	
	# REORDENAR GALERIA
	public function reordenarGaleriaAction(){
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
		
		# Obtener nuevas posiciones 
		parse_str($this->_getParam("order"), $aux);

		# Almacenar nuevas posiciones 
		$modulo = new Default_Model_DbTable_Modulo();
		$modulo->guardarPosicionesGaleria($this->id_empresa,$this->_getParam("modulo"),$this->_getParam("registro"),$aux);
		
		return true;
	}
	
	# REDIMENSIONAR IMAGENES
	public function redimensionarImagenAction(){
		$this->view->id_imagen = $this->_getParam("imagen");
		$this->view->id_modulo = $this->id_modulo;
		$this->view->id_registro = $this->id_registro;
		# Listado de imagenes
		$modulo = new Default_Model_DbTable_Modulo();
		$this->view->imagen = $modulo->obtenerImagen($this->id_empresa,$this->_getParam("imagen"),$this->id_modulo);
		
		# Titulo y librerias
		$this->view->headTitle()->prepend('Redimensionar imagen - ');
		
		$this->view->headScript()
			->appendFile('/js/jcrop/jquery.Jcrop.min.js');
		$this->view->headLink()
			->appendStylesheet('/js/jcrop/css/jquery.Jcrop.min.css');
			
	}

	# PROCESAR RECORTE
	public function procesarRecorteImagenAction(){
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
		print_r($_POST); die;
		if(1){
			# Mensaje de exito
			$msg_img = new Zend_Session_Namespace('mensaje-imagen');
			$msg_img->estado = 'exito';
			$msg_img->titulo = '¡EXITO!';
			$msg_img->texto = 'La imagen fue recortada exitosamente.';
		}else{
			echo json_encode(array(
			"result" => false,
			"msg" => '
			<div class="alert alert-error">
				<h2 class="alert-heading">¡ERROR!</h2>
				La imagen no se pudo redimensionar.
			</div>
			'));
		}
	}
	
	##########################################################################################################################################
	# ARCHIVOS, 05-07-2012 /
	##########################################################################################################################################
	# ARCHIVOS
	public function archivosAction(){		
        # Variables 
        $this->view->id_modulo = $this->id_modulo;
        $this->view->id_registro = $this->id_registro;
        $this->view->id_empresa = $this->id_empresa;
        
        
        /* [RECUPERA LOS DATOS] */
        $campo = new Default_Model_DbTable_Campo();
        $datosformulario = $campo->obtenerdatos($this->id_registro,$this->id_modulo);
        $this->view->datosformulario = (object)$datosformulario;           
		
		# Mensaje de exito
		$msg_img = new Zend_Session_Namespace('mensaje-archivo');
		if(is_string($msg_img->estado)){
			$this->view->mensaje = '
			<div class="alert '.(($msg_img->estado == "exito")?"alert-success":"alert-error").'">
				<h2 class="alert-heading">'.$msg_img->titulo.'</h2>
				'.$msg_img->texto.'
			</div>
			';
			$msg_img->setExpirationSeconds(1);
			unset($msg_img);
		}
		
		# Listado de archivos
		$modulo = new Default_Model_DbTable_Modulo();
		$this->view->archivos = $modulo->listarArchivos($this->id_empresa,$this->id_registro,$this->id_modulo);
		
		# Titulo y librerias
		$this->view->headTitle()->prepend('Archivos - ');
		
		$this->view->headLink()
			->appendStylesheet('/js/plupload/jquery.plupload.queue/css/jquery.plupload.queue.css');
		$this->view->headScript()
			->appendFile('/js/plupload/plupload.full.js')
			->appendFile('/js/plupload/jquery.plupload.queue/jquery.plupload.queue.js')
			->appendFile('/js/plupload/plupload-config-archivo.js');
			
	}

	# SUBIR ARCHIVOS
	public function subirArchivosAction(){
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
		
		# Variables
		$id_empresa  = $this->id_empresa;
		$id_registro = $this->_getParam("codigo_registro");
		$id_modulo   = $this->_getParam("codigo_modulo");
		
		# Verificar que venga el archivo
		if(!empty($_FILES)){
			# Conectar a FTP
			$ftp_server = "cms.hostprimario.com";
			$ftp_user_name = "upload@cms.hostprimario.com";
			$ftp_user_pass = "4hNOXu7hnwX9";
			$conn_id = ftp_connect($ftp_server);
			$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);

			# checkear coneccion
			if((!$conn_id) || (!$login_result)){
				echo "FTP connection has failed!";
				echo "Attempted to connect to $ftp_server for user $ftp_user_name";
				exit;
			}else{
				# Nombre de la empresa
				$empresa = new Default_Model_DbTable_Empresa();
				$datos_empresa = $empresa->obtener($id_empresa);
				
				# Nombre del modulo
				$modulo = new Default_Model_DbTable_Modulo();
				$datos_module = $modulo->obtenerdato($id_modulo);
				
				# Crear carpeta del proyecto si no existe
				if(!is_dir("/".$datos_empresa->nombre_empresa_slug."/"))
					ftp_mkdir($conn_id, "/".$datos_empresa->nombre_empresa_slug);
					
				# Crear carpeta archivos si no existe
				if(!is_dir("/".$datos_empresa->nombre_empresa_slug."/archivos/"))
					ftp_mkdir($conn_id, "/".$datos_empresa->nombre_empresa_slug."/archivos");
				
				# Crear carpeta del modulo si no existe
				if(!is_dir("/".$datos_empresa->nombre_empresa_slug."/archivos/".$datos_module->nombre_modulo_slug."/"))
					ftp_mkdir($conn_id, "/".$datos_empresa->nombre_empresa_slug."/archivos/".$datos_module->nombre_modulo_slug."/");
				
				# Crear carpeta del registro si no existe
				if(!is_dir("/".$datos_empresa->nombre_empresa_slug."/archivos/".$datos_module->nombre_modulo_slug."/".$id_registro."/"))
					ftp_mkdir($conn_id, "/".$datos_empresa->nombre_empresa_slug."/archivos/".$datos_module->nombre_modulo_slug."/".$id_registro."/");

				# Carpeta y nombre del archivo
				$dir = "/".$datos_empresa->nombre_empresa_slug."/archivos/".$datos_module->nombre_modulo_slug."/".$id_registro."/";
				$dir_root = $dir;
				$info = pathinfo($_FILES['file']['name']);
				$nombreArchivo = $id_registro."_".date("YmdGis").".".$info['extension'];
				$archivoTemp = $_FILES['file']['tmp_name'];
				$archivoNuevo = rtrim($dir_root,'/').'/'.$nombreArchivo;

				# Subir archivo
				if(ftp_put($conn_id, $archivoNuevo, $archivoTemp, FTP_BINARY)){
					$mensaje = new Zend_Session_Namespace('mensaje-archivo');
					$mensaje->estado = "exito";
					$mensaje->titulo = "¡EXITO!";
					$mensaje->texto = "Los archivos han sido almacenados exitosamente.";
				
					$clase = array(" Bytes", " KB", " MB", " GB", " TB"); 
					$peso = $_FILES['file']['size'];
					$peso = round($peso/pow(1024,($i = floor(log($peso, 1024)))),0).$clase[$i];
					
					# Subir archivo a la BD
					$datos = array(
						"id_".$datos_module->nombre_modulo_slug => $id_registro,
						"nombre_archivo" => $info['filename'],
						"formato_archivo" => $info['extension'],
						"peso_archivo" => $peso,
						"ruta_archivo" => $dir.$nombreArchivo
					);
					$modulo->subirArchivo($datos,$id_empresa,$id_registro,$id_modulo);
				}
			}
		}
	}
	
	# ELIMINAR ARCHIVO
	public function eliminarArchivoAction(){
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
		
		# Eliminar archivo
		$modulo = new Default_Model_DbTable_Modulo();
		if($archivo = $modulo->eliminarArchivo($this->id_empresa,$this->_getParam("archivo"),$this->_getParam("modulo"))){
			# Conectar a FTP
			$ftp_server = "cms.hostprimario.com";
			$ftp_user_name = "upload@cms.hostprimario.com";
			$ftp_user_pass = "4hNOXu7hnwX9";
			$conn_id = ftp_connect($ftp_server);
			$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);

			# checkear coneccion
			if($login_result){
				$archivo = str_replace('/upload/','',$archivo);
				ftp_delete($conn_id, $archivo);
			}
			
			echo json_encode(array(
			"result" => true,
			"msg" => '
			<div class="alert alert-success">
				<h2 class="alert-heading">¡EXITO!</h2>
				El archivo ha sido eliminado exitosamente.
			</div>
			'));
		}else{
			echo json_encode(array(
			"result" => false,
			"msg" => '
			<div class="alert alert-error">
				<h2 class="alert-heading">¡ERROR!</h2>
				El archivo no se pudo eliminar.
			</div>
			'));
		}
	}
	
	# REORDENAR ARCHIVOS
	public function reordenarArchivosAction(){
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
		
		# Obtener nuevas posiciones 
		parse_str($this->_getParam("order"), $aux);

		# Almacenar nuevas posiciones 
		$modulo = new Default_Model_DbTable_Modulo();
		$modulo->guardarPosicionesArchivos($this->id_empresa,$this->_getParam("modulo"),$this->_getParam("registro"),$aux);
		
		return true;
	}
	
	
	##########################################################################################################################################
	# RELACIONES, 06-07-2012 /
	##########################################################################################################################################
	# RELACIONES
	public function relacionesAction(){
		/* [VARIABLES] */
		$this->view->id_modulo = $this->id_modulo;
		$this->view->id_registro = $this->id_registro;
		/* [EXITO y ERROR] */
        $mensaje = new Zend_Session_Namespace('mensaje');
        $this->view->exito = $mensaje->exito;
        $this->view->error = $mensaje->error;
        $mensaje->setExpirationSeconds(1);
        unset($mensaje);          
		
        /* [RECUPERA LOS DATOS] */
        $campo = new Default_Model_DbTable_Campo();
        $datosformulario = $campo->obtenerdatos($this->id_registro,$this->id_modulo);
        $this->view->datosformulario = (object)$datosformulario;           
		
		# Obtener modulos relacionados
        $relacion = new Default_Model_DbTable_Relacion();
		$relaciones = $relacion->listar($this->id_modulo);
		
		# Obtener formularios
		foreach($relaciones as $aux){
			# Generar formularios
			$formulario = new Default_Form_Relacion(array("id_empresa"=>$this->id_empresa, "id_padre"=>$aux->id_padre, "id_hijo"=>$aux->id_hijo, "cardinalidad" => $aux->id_cardinalidad)); 
			$this->view->formulario_relaciones = $formulario;
			
            # Datos de modulos relacionados
			$modulo = new Default_Model_DbTable_Modulo();
			$this->view->datos = $modulo->listarDatos($this->id_empresa,$aux->id_hijo,$aux->id_padre,$aux->id_cardinalidad,$this->id_registro);
			if(count($this->view->datos)){
				$this->view->datos[0]->id_cardinalidad = $aux->id_cardinalidad;
				$this->view->datos[0]->id_hijo = $aux->id_hijo;
				$this->view->datos[0]->id_padre = $aux->id_padre;
			}
		}
		/* [PROCESAR FORMULARIO] */
        $respuesta = $this->getRequest();
        if($respuesta->isPost()){  
            if($formulario->isValid($this->_request->getPost())){   
                /* DATOS */
                $datos = $formulario->getValues(); 
				
				# Almacenar relaciones por modulo
				foreach($relaciones as $aux){
					$modulo = new Default_Model_DbTable_Modulo();
						/* [MENSAJE DE EXITO */
					$mensaje = new Zend_Session_Namespace('mensaje');
					if($modulo->guardarRelaciones($this->id_empresa,$aux->id_hijo,$aux->id_padre,$aux->id_cardinalidad,$datos,$this->id_registro)){
						$mensaje->exito = true;
					}else{
						$mensaje->error = true;                    
					}
				}
                $this->_redirect('modulo/relaciones/id/'.$this->id_modulo."/idregistro/".$this->id_registro);
            }
        }else{
            /* [LLENAR FORMULARIO] */
			if(count($datosTabla)){
				foreach($datosTabla as $aux)
					$array[] = $aux->id;
				$formulario->populate(array("categorias" => $array));
			}
        }
	}
	
	# Eliminar una relacion
	public function eliminarRelacionAction(){
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

		# Eliminar
		$modulo = new Default_Model_DbTable_Modulo();
		$mensaje = new Zend_Session_Namespace('mensaje');
		if($modulo->eliminarRelacion($this->id_empresa,$this->_getParam("registro_hijo"),$this->_getParam("modulo_hijo"),$this->_getParam("modulo_padre"),$this->_getParam("cardinalidad"))){
			$mensaje->exito = true;
			return true;
		}else{
			$mensaje->error = true;
			return false;
		}
	}
	
	public function eliminarAction(){
		/* [DESAHIBILITAR LAYOUT] */
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);            
		$mensaje = new Zend_Session_Namespace('mensaje');		
		/* ELIMINAR */
		$modulo = new Default_Model_DbTable_Modulo();
		if($modulo->eliminarRegistro($this->id_registro,$this->id_modulo)){
			$mensaje->exito = true;
		}else{
			$mensaje->error = true;		
		}		
		$this->_redirect('/modulo/index/id/'.$this->id_modulo);
	}
}

