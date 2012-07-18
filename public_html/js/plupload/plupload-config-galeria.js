$(function(){
	$("#uploader").pluploadQueue({
		// General settings
		runtimes : 'html5,flash',
		url : '/modulo/subir-fotografias/',
		max_file_size : '2mb',
		chunk_size : '10mb',
		unique_names : true,
		multipart_params : {
			"codigo_modulo" : $("#codigo_modulo").val(),
			"codigo_registro" : $("#codigo_registro").val()
		},
		
		// Specify what files to browse for
		filters : [
			{title : "Imágenes", extensions : "jpg,gif,png"},
		],

		// Flash settings
		flash_swf_url : '/js/plupload/plupload.flash.swf'
	});

	// Client side form validation
	$('form').submit(function(e) {
        var uploader = $('#uploader').pluploadQueue();
		$("#submit").attr("disabled",true);
		
        // Files in queue upload them first
        if (uploader.files.length > 0){
            // When all files are uploaded submit form
            uploader.bind('StateChanged', function(){
                if (uploader.files.length === (uploader.total.uploaded + uploader.total.failed)) {
                    window.location.reload();
                }
			});
            uploader.start();
        } else {
            alert('No has seleccionado ninguna imagen.');
        }

        return false;
	});
});

plupload.addI18n({
	'Select files' : 'Elija archivos:',
	'Add files to the upload queue and click the start button.' : 'Agregue archivos a la cola de subida y haga click en el boton de iniciar.',
	'Filename' : 'Nombre de archivo',
	'Status' : 'Estado',
	'Size' : 'Tama&ntilde;o',
	'Add files' : 'Agregue archivos',
	'Stop current upload' : 'Detener subida actual',
	'Start uploading queue' : 'Iniciar subida de cola',
	'Uploaded %d/%d files': 'Subidos %d/%d archivos',
	'N/A' : 'No disponible',
	'Drag files here.' : 'Arrastre archivos aqu&iacute;',
	'File extension error.': 'Error de extensi&oacute;n de archivo.',
	'File size error.': 'Error de tama&ntilde;o de archivo.',
	'Init error.': 'Error de inicializaci&oacute;n.',
	'HTTP Error.': 'Error de HTTP.',
	'Security error.': 'Error de seguridad.',
	'Generic error.': 'Error gen&eacute;rico.',
	'IO error.': 'Error de entrada/salida.',
	'Stop Upload': 'Detener Subida.',
	'Add Files': 'Agregar Archivos',
	'Start Upload': 'Comenzar Subida.',
	'Start upload': 'Comenzar subida.',
	'%d files queued': '%d archivos en cola.'
});