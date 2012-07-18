$(document).ready(function(){
    /* FORM VALIDATE */
    $('#formulario-campo').validate({       
        errorPlacement: function(){
            return true;
        }
    });   
    /*
     *eliminado estaba generando error en el firebug
    /*$('#formulario-campo #fecha').rules('add', { date: true } );*/
    /* DATEPICKER */
    $('.datepicker').datepicker(); 
    /* [DATEPICKER ESPAÑOL] */
    jQuery(function($){
        $.datepicker.regional['es-cl'] = {
        closeText: 'Cerrar',
        prevText: 'Ant',
        nextText: 'Sig',
        currentText: 'Hoy',
        monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
        monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'],
        dayNames: ['Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'],
        dayNamesShort: ['Dom','Lun','Mar','Mié','Juv','Vie','Sáb'],
        dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sá'],
        weekHeader: 'Sm',
        dateFormat: 'dd/mm/yy',
        firstDay: 1,
        isRTL: false,
        showMonthAfterYear: false,
        yearSuffix: ''};
        $.datepicker.setDefaults($.datepicker.regional['es-cl']);
    });   
    /* EDITOR */
    $('.editor').wysihtml5();
    $('.timepicker').timepicker();    
})