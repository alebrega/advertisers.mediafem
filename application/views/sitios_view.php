<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
    <head>
        <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
        <title>MediaFem para Anunciantes - Inventario</title>
        <?php require_once 'head_links.php'; ?>
        <script src="/js/jquery.dataTables.js" type="text/javascript" charset="utf-8"></script>

        <style type="text/css" title="currentStyle">
            @import "/css/demo_page.css";
            @import "/css/demo_table_jui.css";

            h2{ margin: 0 0 10px !important; }
        </style>

        <style type="text/css">
            .ex_highlight #tbl_report_sites tbody tr.even:hover, #tbl_report_sites tbody tr.even td.highlighted {
                background-color: #FF66CC;
            }

            .ex_highlight #tbl_report_sites tbody tr.odd:hover, #tbl_report_sites tbody tr.odd td.highlighted {
                background-color: #FF66CC;
            }

            .ex_highlight_row #tbl_report_sites tr.even:hover {
                /*background-color: #ECFFB3;*/
                background-color: #FF66CC;
            }

            .ex_highlight_row #tbl_report_sites tr.even:hover td.sorting_1 {
                /* background-color: #DDFF75;*/
                background-color: #FF66CC;
            }

            .ex_highlight_row #tbl_report_sites tr.even:hover td.sorting_2 {
                background-color: #E7FF9E;
            }

            .ex_highlight_row #tbl_report_sites tr.even:hover td.sorting_3 {
                background-color: #E2FF89;
            }

            .ex_highlight_row #tbl_report_sites tr.odd:hover {
                /*background-color: #E6FF99;*/
                background-color: #FF66CC;
            }

            .ex_highlight_row #tbl_report_sites tr.odd:hover td.sorting_1 {
                /*background-color: #D6FF5C;*/
                background-color: #FF66CC;
            }

            .ex_highlight_row #tbl_report_sites tr.odd:hover td.sorting_2 {
                background-color: #E0FF84;
            }

            .ex_highlight_row #tbl_report_sites tr.odd:hover td.sorting_3 {
                background-color: #DBFF70;
            }


            #cmb_canales_tematicos, 
            #cmb_canales_tematicos_2, 
            #cmb_paises, 
            #cmb_paises_2,
            #buscar_canal,
            #buscar_pais{            
                width: 222px;
                color: #333;
                font:normal normal 11px Arial, Helvetica, sans-serif;
                border: 1px solid #C7C7C7;
                -moz-border-radius: 2px; /* Firefox*/
                -ms-border-radius: 2px; /* IE 8.*/
                -webkit-border-radius: 2px; /* Safari,Chrome.*/
                border-radius: 2px; /* El estándar.*/
            }

            #buscar_canal,
            #buscar_pais{ 
                width: 208px !important;
                color: #bbb !important;
            }

            #buscar_canal:focus,
            #buscar_pais:focus{ 
                color: #333 !important;
            }
        </style>

        <script type="text/javascript">
            
            (function($) {
                $.fn.dataTableExt.oApi.fnGetColumnData = function ( oSettings, iColumn, bUnique, bFiltered, bIgnoreEmpty ) {
                     if ( typeof iColumn == "undefined" ) return new Array();
                     if ( typeof bUnique == "undefined" ) bUnique = true;
                     if ( typeof bFiltered == "undefined" ) bFiltered = true;
                     if ( typeof bIgnoreEmpty == "undefined" ) bIgnoreEmpty = true;
                     var aiRows;
                     if (bFiltered == true) aiRows = oSettings.aiDisplay; 
                    else aiRows = oSettings.aiDisplayMaster;
                     var asResultData = new Array();
                     for (var i=0,c=aiRows.length; i<c; i++) {
                         iRow = aiRows[i];
                         var aData = this.fnGetData(iRow);
                         var sValue = aData[iColumn];
                         if (bIgnoreEmpty == true && sValue.length == 0) continue;
                         else if (bUnique == true && jQuery.inArray(sValue, asResultData) > -1) continue;
                         else asResultData.push(sValue);
                     }
                     return asResultData;
                }
            }(jQuery));
            
            function strpos(cadena, busqueda){
                var i = (cadena.toLowerCase()).indexOf(busqueda.toLowerCase());
                return i === -1 ? false : true;
            }
             
            function fnCreateSelect( aData )
            {
                var r='<select><option value="">Todos</option>', i, iLen=aData.length;
                for ( i=0 ; i<iLen ; i++ )
                {
                    r += '<option value="'+aData[i]+'">'+aData[i]+'</option>';
                }
                return r+'</select>';
            }
            
            jQuery.fn.dataTableExt.oSort['formatted-num-asc'] = function(x,y){
                x = x.replace('.','');
                y = y.replace('.','');
                if(x.indexOf('/')>=0)x = eval(x);
                if(y.indexOf('/')>=0)y = eval(y);
                return x/1 - y/1;
            }
            jQuery.fn.dataTableExt.oSort['formatted-num-desc'] = function(x,y){
                x = x.replace('.','');
                y = y.replace('.','');
                if(x.indexOf('/')>=0)x = eval(x);
                if(y.indexOf('/')>=0)y = eval(y);
                return y/1 - x/1;
            }

            jQuery(function($){
                $.datepicker.regional['es'] = {
                    monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                    monthNamesShort: ['Ene','Feb','Mar','Abr', 'May','Jun','Jul','Ago','Sep', 'Oct','Nov','Dic'],
                    dayNames: ['Domingo', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado'],
                    dayNamesShort: ['Dom','Lun','Mar','Mie','Juv','Vie','Sab'],
                    dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sa']};
                $.datepicker.setDefaults($.datepicker.regional['es']);
            });
            
            function reemplazar(texto,s1,s2){
                return texto.split(s1).join(s2);
            }

            $().ready(function() {
                var now = new Date()
                var seconds = now.getSeconds()+'o'+now.getMinutes();
                
                
                // CARGAR PAISES
                // esto es cosa de negros pero estoy apurado para terminar el ticket
                var paises = [];
<?php
$a = 0;
foreach ($paises as $row) {
    ?>
                            paises[<?= $a ?>] = ["<?= $row->id ?>", "<?= $row->descripcion ?>"];                  
    <?php
    $a++;
}
?>                        
                        for(var a = 0 ; a < paises.length ; a++){
                            $('#cmb_paises').append('<option value="'+paises[a][0]+'">'+paises[a][1]+'</option>');
                        }
                        // FIN CARGAR PAISES
                
                
                        // BUSCAR PAISES
                        $("#buscar_pais").keyup( function(event){
                            $('#cmb_paises').html('');
                
                            var busqueda = $(this).val();
                    
                            if(busqueda != ''){
                                for(var a=0; a < paises.length; a++){
                                    if(strpos( paises[a][1], busqueda ) != false){
                                        $('#cmb_paises').append('<option value="'+paises[a][0]+'">'+paises[a][1]+'</option>');
                                    }
                                }
                            }else{
                                for(var a=0; a < paises.length; a++){
                                    $('#cmb_paises').append('<option value="'+paises[a][0]+'">'+paises[a][1]+'</option>');
                                }
                            } 
                        });
                        // FIN BUSCAR PAISES                                
                

                        $("#loader_cmb_cats").css("display", "inline");
                        $.blockUI({ css: {
                                border: 'none',
                                padding: '2px',
                                backgroundColor: '#000',
                                '-webkit-border-radius': '10px',
                                '-moz-border-radius': '10px',
                                opacity: .5,
                                color: '#fff'
                            },
                            message: '<h2>Por favor espere un momento ...<h2/>'
                        });
                
                        /*$("#cmb_canales_tematicos").load('/inventario/getCategorias/'+seconds, function(){
                    $("#loader_cmb_cats").css("display", "none");
                    $.unblockUI();
                });*/
                
                        // CARGAR CANALES TEMATICOS
                        var canales_tematicos = [];
<?php
for ($a = 0; $a < sizeOf($canales_tematicos); $a++) {
    ?>
                            canales_tematicos[<?= $a ?>] = ["<?= $canales_tematicos[$a][0] ?>", "<?= $canales_tematicos[$a][1] ?>"];                  
    <?php
}
?>                        
                        for(var a = 0 ; a < canales_tematicos.length ; a++){
                            $('#cmb_canales_tematicos').append('<option value="'+canales_tematicos[a][0]+'">'+canales_tematicos[a][1]+'</option>');
                        }
                        // FIN CARGAR CANALES TEMATICOS
                
                        // BUSCAR CANALES TEMATICOS
                        $("#buscar_canal").keyup( function(event){
                            $('#cmb_canales_tematicos').html('');
                
                            var busqueda = $(this).val();
                    
                            if(busqueda != ''){
                                for(var a = 0 ; a < canales_tematicos.length ; a++){
                                    if(strpos( canales_tematicos[a][1], busqueda ) != false){
                                        $('#cmb_canales_tematicos').append('<option value="'+canales_tematicos[a][0]+'">'+canales_tematicos[a][1]+'</option>');
                                    }
                                }
                            }else{
                                for(var a = 0 ; a < canales_tematicos.length ; a++){
                                    $('#cmb_canales_tematicos').append('<option value="'+canales_tematicos[a][0]+'">'+canales_tematicos[a][1]+'</option>');
                                }
                            } 
                        });
                        // FIN BUSCAR CANALES TEMATICOS 

                        $.datepicker.setDefaults($.datepicker.regional['es']);
                        $("#fecha_desde").datepicker({ dateFormat:'dd-mm-yy' });
                        $("#fecha_hasta").datepicker({ dateFormat:'dd-mm-yy' });

                        $("#cmb_rango").click(function(){
                            var rango = $("#cmb_rango").find(':selected').val();

                            if(rango=='especific'){
                                $("#div_datapickers").css("display", "inline");
                            }else{
                                $("#div_datapickers").css("display", "none");
                            }
                        });

                        $("#btn_ejecutar_reporte").click( function (){
                            $("#loader_btn_ejecutar_reporte").css("display", "inline");
                            $.blockUI({ css: {
                                    border: 'none',
                                    padding: '2px',
                                    backgroundColor: '#000',
                                    '-webkit-border-radius': '10px',
                                    '-moz-border-radius': '10px',
                                    opacity: .5,
                                    color: '#fff'
                                },
                                message: '<h2>Por favor espere un momento ...<h2/>'
                            });
                
                            var now = new Date();
                            var seconds = now.getMinutes()+'o'+now.getSeconds();
                            var rango = $("#cmb_rango").find(':selected').val();
                    
                            var imps_minimas = $.trim($("#txt_imps_minimas").val());
                    
                            if(imps_minimas == "")
                                imps_minimas = 0;
                    
                            var id_paises = "";

                            $("#cmb_paises_2 option").each(function(){
                                // si seleccione los desconocidos entonces
                                if( $(this).val() == 'desconocido' ){
                                    // separo la coma de los desconocidos
                                    var id_desconocidos = $('#paises_desconocidos').val().split(',');
                                    // recorro los ids
                                    for(var a = 0; a < id_desconocidos.length; a++){
                                        id_paises = id_paises + id_desconocidos[a] + "o";
                                    }
                                }else{
                                    id_paises = id_paises + $(this).attr('value') + "o";
                                }
                            });
                    
                            var id_canales_tematicos = "";

                            $("#cmb_canales_tematicos_2 option").each(function(){
                                id_canales_tematicos = id_canales_tematicos + $(this).attr('value') + "o";
                            });

                            if(id_paises == ""){
                                id_paises = 0;
                            }
                    
                            if(id_canales_tematicos == ""){
                                id_canales_tematicos = 0;
                            }
                    
                            if(rango=='especific'){
                                var fecha_desde = $("#fecha_desde").val();
                                var fecha_hasta = $("#fecha_hasta").val();
                        
                                $("#tbl_sites").load("/inventario/obtenerReportePorSitiosFechaEspecifica/"+id_canales_tematicos+"/"+id_paises+"/"+imps_minimas+"/"+fecha_desde+"/"+fecha_hasta+"/"+seconds, function(){
                                    $("#div_exportar").css("display", "inline");
                                    $("#loader_btn_ejecutar_reporte").css("display", "none");
                                    $.unblockUI();
                                });
                            }else{
                                $("#tbl_sites").load("/inventario/obtenerReportePorSitios/"+id_canales_tematicos+"/"+id_paises+"/"+rango+"/"+imps_minimas+"/"+seconds, function(){
                                    $("#div_exportar").css("display", "inline");
                                    $("#loader_btn_ejecutar_reporte").css("display", "none");
                                    $.unblockUI();
                                });
                            }
                        });
                
                        $("#btn_ejecutar_reporte_x_categoria").click( function (){
                            $("#loader_btn_ejecutar_reporte").css("display", "inline");
                            $.blockUI({ css: {
                                    border: 'none',
                                    padding: '2px',
                                    backgroundColor: '#000',
                                    '-webkit-border-radius': '10px',
                                    '-moz-border-radius': '10px',
                                    opacity: .5,
                                    color: '#fff'
                                },
                                message: '<h2>Por favor espere un momento ...<h2/>'
                            });
                    
                            var now = new Date();
                            var seconds = now.getMinutes()+'o'+now.getSeconds();
                            var rango = $("#cmb_rango").find(':selected').val();

                            var id_canales_tematicos = "";

                            $("#cmb_canales_tematicos_2 option").each(function(){
                                id_canales_tematicos = id_canales_tematicos + $(this).attr('value') + "o";
                            });

                            var id_paises = "";

                            $("#cmb_paises_2 option").each(function(){
                                id_paises = id_paises + $(this).attr('value') + "o";
                            });

                            if(id_canales_tematicos == ""){
                                id_canales_tematicos = 0;
                            }

                            if(id_paises == ""){
                                id_paises = 0;
                            }

                            if(rango=='especific'){
                                var fecha_desde = $("#fecha_desde").val();
                                var fecha_hasta = $("#fecha_hasta").val();
                                $("#tbl_sites").load("/inventario/getSitesByDateByCategory/"+id_canales_tematicos+"/"+id_paises+"/"+fecha_desde+"/"+fecha_hasta+'/'+seconds, function(){
                                    $("#div_exportar").css("display", "inline");
                                    $("#loader_btn_ejecutar_reporte").css("display", "none");
                                    $.unblockUI();
                                });
                            }else{
                                $("#tbl_sites").load("/inventario/getSitesByCategory/"+id_canales_tematicos+"/"+id_paises+'/'+rango+"/"+seconds, function(){
                                    $("#div_exportar").css("display", "inline");
                                    $("#loader_btn_ejecutar_reporte").css("display", "none");
                                    $.unblockUI();
                                });
                            }
                    
                        });
                
                        $("#btn_ejecutar_reporte_por_sitio_formato").click( function (){
                            $("#loader_btn_ejecutar_reporte").css("display", "inline");
                            $.blockUI({ css: {
                                    border: 'none',
                                    padding: '2px',
                                    backgroundColor: '#000',
                                    '-webkit-border-radius': '10px',
                                    '-moz-border-radius': '10px',
                                    opacity: .5,
                                    color: '#fff'
                                },
                                message: '<h2>Por favor espere un momento ...<h2/>'
                            });
                    
                            var now = new Date();
                            var seconds = now.getMinutes()+'o'+now.getSeconds();
                            var rango = $("#cmb_rango").find(':selected').val();
                    
                            var imps_minimas = $.trim($("#txt_imps_minimas").val());
                    
                            if(imps_minimas == "")
                                imps_minimas = 0;
                    
                            var id_paises = "";

                            $("#cmb_paises_2 option").each(function(){
                                // si seleccione los desconocidos entonces
                                if( $(this).val() == 'desconocido' ){
                                    // separo la coma de los desconocidos
                                    var id_desconocidos = $('#paises_desconocidos').val().split(',');
                                    // recorro los ids
                                    for(var a = 0; a < id_desconocidos.length; a++){
                                        id_paises = id_paises + id_desconocidos[a] + "o";
                                    }
                                }else{
                                    id_paises = id_paises + $(this).attr('value') + "o";
                                }
                            });
                    
                            var id_canales_tematicos = "";

                            $("#cmb_canales_tematicos_2 option").each(function(){
                                id_canales_tematicos = id_canales_tematicos + $(this).attr('value') + "o";
                            });

                            if(id_paises == ""){
                                id_paises = 0;
                            }
                    
                            if(id_canales_tematicos == ""){
                                id_canales_tematicos = 0;
                            }
                    
                            if(rango=='especific'){
                                var fecha_desde = $("#fecha_desde").val();
                                var fecha_hasta = $("#fecha_hasta").val();
                        
                                $("#tbl_sites").load("/inventario/obtenerReportePorSitiosFormatoFechaEspecifica/"+id_canales_tematicos+"/"+id_paises+"/"+imps_minimas+"/"+fecha_desde+"/"+fecha_hasta+"/"+seconds, function(){
                                    $("#div_exportar").css("display", "inline");
                                    $("#loader_btn_ejecutar_reporte").css("display", "none");
                                    $.unblockUI();
                                });
                            }else{
                                $("#tbl_sites").load("/inventario/obtenerReportePorSitiosFormato/"+id_canales_tematicos+"/"+id_paises+"/"+rango+"/"+imps_minimas+"/"+seconds, function(){
                                    $("#div_exportar").css("display", "inline");
                                    $("#loader_btn_ejecutar_reporte").css("display", "none");
                                    $.unblockUI();
                                });
                            }
                        });
                
                        $("#btn_ejecutar_reporte_por_formato").click( function (){
                            $("#loader_btn_ejecutar_reporte").css("display", "inline");
                            $.blockUI({ css: {
                                    border: 'none',
                                    padding: '2px',
                                    backgroundColor: '#000',
                                    '-webkit-border-radius': '10px',
                                    '-moz-border-radius': '10px',
                                    opacity: .5,
                                    color: '#fff'
                                },
                                message: '<h2>Por favor espere un momento ...<h2/>'
                            });
                    
                            var now = new Date();
                            var seconds = now.getMinutes()+'o'+now.getSeconds();
                            var rango = $("#cmb_rango").find(':selected').val();
                    
                            var imps_minimas = $.trim($("#txt_imps_minimas").val());
                    
                            if(imps_minimas == "")
                                imps_minimas = 0;
                    
                            var id_paises = "";

                            $("#cmb_paises_2 option").each(function(){
                                // si seleccione los desconocidos entonces
                                if( $(this).val() == 'desconocido' ){
                                    // separo la coma de los desconocidos
                                    var id_desconocidos = $('#paises_desconocidos').val().split(',');
                                    // recorro los ids
                                    for(var a = 0; a < id_desconocidos.length; a++){
                                        id_paises = id_paises + id_desconocidos[a] + "o";
                                    }
                                }else{
                                    id_paises = id_paises + $(this).attr('value') + "o";
                                }
                            });
                    
                            var id_canales_tematicos = "";

                            $("#cmb_canales_tematicos_2 option").each(function(){
                                id_canales_tematicos = id_canales_tematicos + $(this).attr('value') + "o";
                            });

                            if(id_paises == ""){
                                id_paises = 0;
                            }
                    
                            if(id_canales_tematicos == ""){
                                id_canales_tematicos = 0;
                            }
                    
                            if(rango=='especific'){
                                var fecha_desde = $("#fecha_desde").val();
                                var fecha_hasta = $("#fecha_hasta").val();
                        
                                $("#tbl_sites").load("/inventario/obtenerReportePorFormatoFechaEspecifica/"+id_canales_tematicos+"/"+id_paises+"/"+imps_minimas+"/"+fecha_desde+"/"+fecha_hasta+"/"+seconds, function(){
                                    $("#div_exportar").css("display", "inline");
                                    $("#loader_btn_ejecutar_reporte").css("display", "none");
                                    $.unblockUI();
                                });
                            }else{
                                $("#tbl_sites").load("/inventario/obtenerReportePorFormato/"+id_canales_tematicos+"/"+id_paises+"/"+rango+"/"+imps_minimas+"/"+seconds, function(){
                                    $("#div_exportar").css("display", "inline");
                                    $("#loader_btn_ejecutar_reporte").css("display", "none");
                                    $.unblockUI();
                                });
                            }
                        });
                
                        $("#btn_ejecutar_reporte_por_pais").click( function (){
                            $("#loader_btn_ejecutar_reporte").css("display", "inline");
                            $.blockUI({ css: {
                                    border: 'none',
                                    padding: '2px',
                                    backgroundColor: '#000',
                                    '-webkit-border-radius': '10px',
                                    '-moz-border-radius': '10px',
                                    opacity: .5,
                                    color: '#fff'
                                },
                                message: '<h2>Por favor espere un momento ...<h2/>'
                            });
                    
                            var now = new Date();
                            var seconds = now.getMinutes()+'o'+now.getSeconds();
                            var rango = $("#cmb_rango").find(':selected').val();
                    
                            var imps_minimas = $.trim($("#txt_imps_minimas").val());
                    
                            if(imps_minimas == "")
                                imps_minimas = 0;
                    
                            var id_paises = "";

                            $("#cmb_paises_2 option").each(function(){
                                // si seleccione los desconocidos entonces
                                if( $(this).val() == 'desconocido' ){
                                    // separo la coma de los desconocidos
                                    var id_desconocidos = $('#paises_desconocidos').val().split(',');
                                    // recorro los ids
                                    for(var a = 0; a < id_desconocidos.length; a++){
                                        id_paises = id_paises + id_desconocidos[a] + "o";
                                    }
                                }else{
                                    id_paises = id_paises + $(this).attr('value') + "o";
                                }
                            });
                    
                            var id_canales_tematicos = "";

                            $("#cmb_canales_tematicos_2 option").each(function(){
                                id_canales_tematicos = id_canales_tematicos + $(this).attr('value') + "o";
                            });

                            if(id_paises == ""){
                                id_paises = 0;
                            }
                    
                            if(id_canales_tematicos == ""){
                                id_canales_tematicos = 0;
                            }
                    
                            if(rango=='especific'){
                                var fecha_desde = $("#fecha_desde").val();
                                var fecha_hasta = $("#fecha_hasta").val();
                        
                                $("#tbl_sites").load("/inventario/obtenerReportePorPaisFechaEspecifica/"+id_canales_tematicos+"/"+id_paises+"/"+imps_minimas+"/"+fecha_desde+"/"+fecha_hasta+"/"+seconds, function(){
                                    $("#div_exportar").css("display", "inline");
                                    $("#loader_btn_ejecutar_reporte").css("display", "none");
                                    $.unblockUI();
                                });
                            }else{
                                $("#tbl_sites").load("/inventario/obtenerReportePorPais/"+id_canales_tematicos+"/"+id_paises+"/"+rango+"/"+imps_minimas+"/"+seconds, function(){
                                    $("#div_exportar").css("display", "inline");
                                    $("#loader_btn_ejecutar_reporte").css("display", "none");
                                    $.unblockUI();
                                });
                            }
                        });
                
                        $("#btn_pasar_pais").click( function (){
                            $('#cmb_paises option:selected').appendTo("#cmb_paises_2");
                            $("#select_all_paises").attr('checked', false);
                            if($('#cmb_paises option').length <= 0){
                                $("#select_all_paises").attr('disabled', 'disabled');
                            }else{
                                $("#select_all_paises").attr('disabled', false);
                            }
                        });

                        $("#btn_borrar_pais").click( function (){
                            $('#cmb_paises_2 option:selected').appendTo("#cmb_paises");
                            $("#select_all_paises").attr('checked', false);
                            if($('#cmb_paises option').length <= 0){
                                $("#select_all_paises").attr('disabled', 'disabled');
                            }else{
                                $("#select_all_paises").attr('disabled', false);
                            }
                        });

                        $("#cmb_paises").dblclick( function (){
                            //$('#cmb_paises option:selected').appendTo("#cmb_paises_2");
                            $("#btn_pasar_pais").click();
                        });

                        $("#cmb_paises_2").dblclick( function (){
                            //$('#cmb_paises_2 option:selected').appendTo("#cmb_paises");
                            $("#btn_borrar_pais").click();
                        });
                
                        $("#btn_pasar_canal_tematico").click( function (){
                            $('#cmb_canales_tematicos option:selected').appendTo("#cmb_canales_tematicos_2");
                            $("#select_all_canales").attr('checked', false);
                            if($('#cmb_canales_tematicos option').length <= 0){
                                $("#select_all_canales").attr('disabled', 'disabled');
                            }else{
                                $("#select_all_canales").attr('disabled', false);
                            }
                        });

                        $("#btn_borrar_canal_tematico").click( function (){
                            $('#cmb_canales_tematicos_2 option:selected').appendTo("#cmb_canales_tematicos");
                            $("#select_all_canales_2").attr('checked', false);
                            if($('#cmb_canales_tematicos option').length <= 0){
                                $("#select_all_canales").attr('disabled', 'disabled');
                            }else{
                                $("#select_all_canales").attr('disabled', false);
                            }
                        });

                        $("#cmb_canales_tematicos").dblclick( function (){
                            //$('#cmb_canales_tematicos option:selected').appendTo("#cmb_canales_tematicos_2");
                            $("#btn_pasar_canal_tematico").click();
                        });

                        $("#cmb_canales_tematicos_2").dblclick( function (){
                            //$('#cmb_canales_tematicos_2 option:selected').appendTo("#cmb_canales_tematicos");
                            $("#btn_borrar_canal_tematico").click();
                        });
                
                        /*$("#btn_pasar_canales_1").click( function (){
                    $("#cmb_canales_tematicos option").each(function(){
                        $(this).appendTo("#cmb_canales_tematicos_2");
                    });
                });*/
                
                        $("#select_all_canales").click( function (){
                            if( $(this).attr('checked') ){
                                $('#cmb_canales_tematicos option').each( function(){
                                    $(this).attr('selected', 'selected');
                                })
                            }else{
                                $('#cmb_canales_tematicos option').each( function(){
                                    $(this).attr('selected', false);
                                })
                            }
                        });

                        /*$("#btn_pasar_canales_2").click( function (){
                    $("#cmb_canales_tematicos_2 option").each(function(){
                        $(this).appendTo("#cmb_canales_tematicos");
                    });
                });*/
                
                        $("#btn_pasar_paises_1").click( function (){
                            $("#cmb_paises option").each(function(){
                                $(this).appendTo("#cmb_paises_2");
                            });
                        });

                        $("#btn_pasar_paises_2").click( function (){
                            $("#cmb_paises_2 option").each(function(){
                                $(this).appendTo("#cmb_paises");
                            });
                        });
                
                        $("#select_all_paises").click( function (){
                            if( $(this).attr('checked') ){
                                $('#cmb_paises option').each( function(){
                                    $(this).attr('selected', 'selected');
                                })
                            }else{
                                $('#cmb_paises option').each( function(){
                                    $(this).attr('selected', false);
                                })
                            }
                        });
                
                
                        $("#loader_cmb_cats").css("display", "none");
                        $.unblockUI();
                    }); 
        </script>
        <?php //require_once BASEPATH . '/application/views/analytics.html'; ?>
    </head>

    <body class="ex_highlight_row">

        <?php require_once 'application/views/top.html'; ?>

        <table cellpadding="0px" class="tabla" style="margin-bottom: 30px"> 
            <tr>
                <td>
                    <table id="tbl_cmb_canales_tematicos">
                        <tr><td colspan="3"><h2>Canales Tem&aacute;ticos</h2></td></tr>
                        <tr>
                            <td>
                                <input type="text" name="buscar_canal" id="buscar_canal" value="Buscar"
                                       onblur="if(this.value == ''){this.value='Buscar';}"
                                       onfocus="if(this.value == 'Buscar'){this.value='';}" />
                            </td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="width: 180px">
                                <div id="loader_cmb_cats" style="display:none"><img height="10px" alt="agregar" src="images/ajax-loader.gif" /></div>                                
                                <select style="width: 210px" size="7" id="cmb_canales_tematicos" name="cmb_sitios" multiple="multiple">
                                </select>
                            </td>
                            <td style="width: 10px">
                                <table>
                                    <tr>
                                        <td><input type="button" value=">>" id="btn_pasar_canal_tematico" class="button_new" /></td>
                                    </tr>
                                    <tr>
                                        <td><input type="button" value="<<" id="btn_borrar_canal_tematico" class="button_new" /></td>
                                    </tr>
                                </table>
                            </td>
                            <td>
                                <select style="width: 210px" size="7" id="cmb_canales_tematicos_2" name="cmb_sitios_2" multiple="multiple">
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label><input type="checkbox" id="select_all_canales" name="select_all_canales" value="1" />
                                    Seleccionar todos</label>
                            </td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                    </table>
                </td>

                <td>
                    <table>
                        <tr><td colspan="3"><h2>Pa&iacute;s</h2></td></tr>
                        <tr>
                            <td>
                                <input type="text" name="buscar_pais" id="buscar_pais" value="Buscar"
                                       onblur="if(this.value == ''){this.value='Buscar';}"
                                       onfocus="if(this.value == 'Buscar'){this.value='';}" />
                            </td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="width: 180px">
                                <select style="width: 210px" size="7" id="cmb_paises" name="cmb_paises" multiple="multiple">                                    
                                </select>
                            </td>
                            <td style="width: 10px">
                                <table>
                                    <tr>
                                        <td><input type="button" value=">>" id="btn_pasar_pais" class="button_new" /></td>
                                    </tr>
                                    <tr>
                                        <td><input type="button" value="<<" id="btn_borrar_pais" class="button_new" /></td>
                                    </tr>
                                </table>
                            </td> 
                            <td>
                                <select style="width: 210px" size="7" id="cmb_paises_2" name="cmb_paises_2" multiple="multiple">
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label><input type="checkbox" id="select_all_paises" name="select_all_paises" value="1" />
                                    Seleccionar todos</label>
                            </td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="3">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="3"><b>Rango de fechas del reporte</b></td>
            </tr>
            <tr>
                <td>
                    <select id="cmb_rango" name="cmb_rango" class="combo">
                        <?php
                        foreach ($arr_rango as $c => $v) {
                            ?>
                            <option value="<?= $c ?>"><?= $v ?></option>
                            <?php
                        }
                        ?>
                    </select>                    
                    <span id="div_datapickers" style="display: none;position:relative;top:-25px;left:240px;">
                        <table style="margin-left: 20px;" cellspacing="0" cellpadding="2" border="0">
                            <tr>
                                <td style="background-color: #E6E6FF"><input type="text" size="12" id="fecha_desde" name="fecha_desde" /></td>
                                <td style="background-color: #E6E6FF">-</td>
                                <td style="background-color: #E6E6FF"><input type="text" size="12" id="fecha_hasta" name="fecha_hasta" /></td>
                            </tr>
                        </table>
                    </span>
                    <div id="loader_cmb_fechas" style="display:none"><img height="14px" alt="agregar" src="/images/ajax-loader.gif" /></div>
                </td>
            </tr>        
            <tr>
                <td>&nbsp;</td>
            </tr>            
            <tr>
                <td colspan="5">
                    <input type="button" class="button_new" value="Por sitio" id="btn_ejecutar_reporte" />
                    <input type="button" class="button_new" value="Por canal tem&aacute;tico" id="btn_ejecutar_reporte_x_categoria" />
                    <input type="button" class="button_new" value="Por sitio-formato" id="btn_ejecutar_reporte_por_sitio_formato" />
                    <input type="button" class="button_new" value="Por formato" id="btn_ejecutar_reporte_por_formato" />
                    <input type="button" class="button_new" value="Por pa&iacute;s" id="btn_ejecutar_reporte_por_pais" />
                    <span id="loader_btn_ejecutar_reporte" style="display:none"><img height="14px" alt="agregar" src="images/ajax-loader.gif" /></span>
                </td>
            </tr>
        </table>

        <!--<table class="tabla" style="margin-bottom: 30px">            
            <tr>
                <td>
                    <h2>Canales Tem&aacute;ticos</h2>
                    <div id="loader_cmb_cats" style="display:none"><img height="10px" alt="agregar" src="images/ajax-loader.gif" /></div>
                </td>
                <td><h2>Pa&iacute;s</h2></td>
            </tr>
            <tr>
                <td style="width: 400px">
                    <table cellpadding="0px" id="tbl_cmb_canales_tematicos" style="width: 400px">
                        <tr>
                            <td style="width: 160px">
                                <input type="button" id="btn_pasar_canales_1" value="&gt;&gt;&gt;" />
                                <select style="width: 160px" size="6" id="cmb_canales_tematicos" name="cmb_sitios">
                                </select>
                            </td>
                            <td style="width: 10px">
                                <table>
                                    <tr>
                                        <td><input type="button" value=">>" id="btn_pasar_canal_tematico" /></td>
                                    </tr>
                                    <tr>
                                        <td><input type="button" value="<<" id="btn_borrar_canal_tematico" /></td>
                                    </tr>
                                </table>
                            </td>
                            <td>
                                <input type="button" id="btn_pasar_canales_2" value="&lt;&lt;&lt;" style="float:right;margin-right:28px;" />
                                <select style="width: 160px" size="6" id="cmb_canales_tematicos_2" name="cmb_sitios_2">
                                </select>
                            </td>
                        </tr>
                    </table>
                </td>
                <td>
                    <table cellpadding="0px" id="tbl_cmb_paises" style="width: 400px">
                        <tr>
                            <td style="width: 160px">
                                <input type="button" id="btn_pasar_paises_1" value="&gt;&gt;&gt;" />
                                <select style="width: 160px" size="6" id="cmb_paises" name="cmb_paises">
        <?php
        //$ids_desconocidos = '';
        foreach ($paises as $row) {
            if ($row->descripcion != 'Desconocido') {
                ?>
                                                            <option value="<?= $row->id ?>"><?= $row->descripcion ?></option>                                        
                <?php
            }/* else{
              $ids_desconocidos .= $row->id . ',';
              } */
        }
        ?>
                                </select>
                            </td>
                            <td style="width: 10px">
                                <table>
                                    <tr>
                                        <td><input type="button" value=">>" id="btn_pasar_pais" /></td>
                                    </tr>
                                    <tr>
                                        <td><input type="button" value="<<" id="btn_borrar_pais" /></td>
                                    </tr>
                                </table>
                            </td>
                            <td>
                                <input type="button" id="btn_pasar_paises_2" value="&lt;&lt;&lt;" style="float:right;margin-right:28px;" />
                                <select style="width: 160px" size="6" id="cmb_paises_2" name="cmb_paises_2">
                                </select>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td style="width: 250px"><b>Rango de fechas del reporte</b></td>
            </tr>
            <tr>
                <td>
                    <select id="cmb_rango" name="cmb_rango" class="combo">
        <?php
        foreach ($arr_rango as $c => $v) {
            ?>
                                    <option value="<?= $c ?>"><?= $v ?></option>
            <?php
        }
        ?>
                    </select>
                    <div id="loader_cmb_fechas" style="display:none"><img height="14px" alt="agregar" src="/images/ajax-loader.gif" /></div>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <div id="div_datapickers" style="display:none;margin-bottom: 10px">
                        <table style="margin-left: 20px;">
                            <tr>
                                <td style="width:100px">Fecha Desde</td>
                                <td><input type="text" size="12" id="fecha_desde" name="fecha_desde" /></td>
                            </tr>
                            <tr>
                                <td>Fecha Hasta</td>
                                <td><input type="text" size="12" id="fecha_hasta" name="fecha_hasta" /></td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>            
            <tr>
                <td>&nbsp;</td>
            </tr>            
            <tr>
                <td colspan="5">
                    <input type="button" class="btn_default" value="Por sitio" id="btn_ejecutar_reporte" />
                    <input type="button" class="btn_default" value="Por sitio-formato" id="btn_ejecutar_reporte_por_sitio_formato" />
                    <input type="button" class="btn_default" value="Por formato" id="btn_ejecutar_reporte_por_formato" />
                    <input type="button" class="btn_default" value="Por pa&iacute;s" id="btn_ejecutar_reporte_por_pais" />
                    <span id="loader_btn_ejecutar_reporte" style="display:none"><img height="14px" alt="agregar" src="images/ajax-loader.gif" /></span>
                </td>
            </tr>            
        </table>-->

        <table class="tabla">
            <tr>
                <td id="tbl_sites">
                </td>
            </tr>
        </table>

        <?php require_once 'application/views/footer.php'; ?>
    </body>
</html>