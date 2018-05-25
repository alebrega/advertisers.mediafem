<style type="text/css">
    label{
        font: normal bold 12px Arial, Helvetica, sans-serif;
        width: 140px;
        display: inline-block;
    }

    input[type="text"]{
        border-radius: 2px; /* El estándar.*/
        font: normal normal 11px Arial, Helvetica, sans-serif;
        padding: 3px 3px 3px 0;
        width: 303px;
    }

    input[type="text"]{
        padding: 4px 2px !important;
        width: 297px !important;
    }

    hr{
        border: none;
        border-bottom:1px solid #ddd;
        margin:20px 0 20px;
    }

    .msg_error {
        color: red;
        display: none;
    }
</style>

<script type="text/javascript">
    jQuery(function($){
        $.datepicker.regional['es'] = {
            monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
            monthNamesShort: ['Ene','Feb','Mar','Abr', 'May','Jun','Jul','Ago','Sep', 'Oct','Nov','Dic'],
            dayNames: ['Domingo', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado'],
            dayNamesShort: ['Dom','Lun','Mar','Mie','Juv','Vie','Sab'],
            dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sa']};
        $.datepicker.setDefaults($.datepicker.regional['es']);
    });

    $().ready(function(){
        var id_campana = '<?= $id_campania ?>';

        // FECHA DE INICIO Y FIN ***********************************************
        $.datepicker.setDefaults($.datepicker.regional['es']);
        $("#fecha_inicio_duplicar").datepicker({ dateFormat:'dd-mm-yy' });
        $("#fecha_fin_duplicar").datepicker({ dateFormat:'dd-mm-yy' });

        $("#aceptar_duplicacion").click(function(){
            if($('#chk_acepto_terminos').is(":checked")){
                var error = false;
                $("#error_duplicar").css("display", "none");
                $("#loader_duplicar").css("display", "inline");

                // valido nombre de la campania
                var nombre_campania = $.trim($('#nombre_campania_duplicar').val());
                if( nombre_campania.length <= 0 ){
                    $("#error_duplicar").html('Por favor indique un nombre para la campaña.').css("display", "inline");
                    error = true;
                }

                //valido la fecha de inicio
                var fecha_inicio = $.trim( $('#fecha_inicio_duplicar').val() );
                var fecha_fin = $.trim( $('#fecha_fin_duplicar').val() );

                if( fecha_inicio == '' || fecha_fin == '' ){
                    $("#error_duplicar").html('Por favor ingrese la fecha de inicio y fin de la campaña.').css("display", "inline");
                    error = true;
                }else{
                    $("#error_duplicar").css("display", "none");
                }

                if(error == true){
                    $("#loader_duplicar").css("display", "none");

                    return false;
                }

                var form_data = {
                    id_campania: id_campana,
                    nombre: nombre_campania,
                    fecha_inicio: fecha_inicio,
                    fecha_fin: fecha_fin
                };

                // alert(id_campana + ' *** ' + nombre_campania + ' *** ' + fecha_inicio + ' *** ' + fecha_fin);

                $.ajax({
                    type: "POST",
                    url: "/campania/duplicar/",
                    data: form_data,
                    dataType: "json",
                    success: function(msg){
                        $("#loader_duplicar").css("display", "none");

                        if(msg.validate){
                            $("#ok_duplicar").css("display", "inline");
                            window.location.replace("/campania");
                        }else{
                            $("#error_duplicar").html(msg.error).css("display", "inline");
                        }
                    }
                });

            }else{
                $("#error_duplicar").html('Por favor acepte los terminos y condiciones para poder crear la campaña.').fadeIn('fast');
            }
        });
    });
</script>

<h2 class="border_bottom">Duplicar campa&ntilde;a.</h2>
<p style="margin-bottom: 25px; font-weight: bold;">Ingrese los nuevos datos de la campa&ntilde;a que desea duplicar y pulse "Aceptar".</p>

<div class="row">
    <label>Anunciante: </label>
    <span><?= $nombre_anunciante ?></span>
</div>

<div class="row">
    <label>Campa&ntilde;a: </label>
    <span>
        <input type="text" name="nombre_campania_duplicar" maxlength="128" id="nombre_campania_duplicar" value="<?= $nombre ?>" />
    </span>
    <div style="font-style: italic; margin: 5px 0 0 165px; font-size:0.8em">
        Recomendamos que elija nombres que sean f&aacute;cilmente de identificar.<br />
        Es una buena practica agregar el nombre del pa&iacute;s o regi&oacute;n al nombre de la campa&ntilde;a.
    </div>
</div>

<!-- FECHA DE INICIO Y DE FIN -->
<div class="row">
    <label>Fecha: </label>
    <input type="text" name="fecha_inicio" id="fecha_inicio_duplicar" value="<?= $fecha_inicio ?>" style="width:135px !important;" /> al
    <input type="text" name="fecha_fin" id="fecha_fin_duplicar" value="<?= $fecha_fin ?>" style="width:136px !important;" />
</div>

<div class="row">
    <input type="checkbox" name="chk_acepto_terminos" id="chk_acepto_terminos" value="1" /> Acepto los <a href="https://ayuda.mediafem.com/mediafem-sitios/conceptos-basicos-mediafem-sitios/politicas-del-programa-mediafem-para-sitios" target="_BLANK">t&eacute;rminos y condiciones</a> <a href="http://ayuda.mediafem.com/mediafem-anunciantes/terminos-y-condiciones-de-la-orden-de-compra-de-mediafem" target="_BLANK">de la orden de compra</a>.
</div>

<hr>

<div>
    <input type="button" class="button_new superButton" value="Duplicar" id="aceptar_duplicacion" />
    <img id="loader_duplicar" style="display:none;" src="/images/ajax-loader.gif" height="10px" />
    <span class="msg_error" style="display:none;" id="error_duplicar"></span>
    <span class="msg_error" style="display:none; color: green !important" id="ok_duplicar">Campa&ntilde;a duplicada con exito, espere por favor.</span>
</div>