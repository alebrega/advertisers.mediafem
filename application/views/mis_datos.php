<style type="text/css">
    label.error{
        border: none !important;
        display: inline-block !important;
        width: 500px !important;
        font-size: 12px !important;
        text-align: left !important;
    }
</style>
<form id="form_actualizar_datos_cuenta" action="/micuenta/actualizar_datos_cuenta" method="post">
    <table class="tabla" style="border-spacing: 5px;">
        <tr>
            <td style="padding-right: 10px;">
                Nombre completo del beneficiario
            </td>
            <td>
                <input type="text" class="txt_default" style="width: 218px !important;" id="txt_nombre_beneficiario" name="txt_nombre_beneficiario" value="<?= $usuario->name ?>" />
            </td>
        </tr>
        <tr>
            <td style="padding-right: 10px;">
                Empresa
            </td>
            <td>
                <input type="text" class="txt_default" style="width: 218px !important;" id="txt_empresa" name="txt_empresa" value="<?= $usuario->empresa ?>" />
            </td>
        </tr>
        <tr>
            <td>
                Pa&iacute;s
            </td>
            <td>
                <select name="pais" class="combo" style="width: 232px" disabled="disabled">
                    <?php
                    foreach ($paises as $pais) {
                        if ($pais->id == $usuario->country) {
                            $select = 'selected="selected"';
                        } else {
                            $select = '';
                        }
                        ?>
                        <option value="<?= $pais->id ?>" <?= $select ?>><?= $pais->descripcion ?></option>
                        <?php
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <td>
                Domicilio
            </td>
            <td>
                <input type="text" class="txt_default" style="width: 218px !important;" id="txt_domicilio" name="txt_domicilio" value="<?= $usuario->address ?>" />
            </td>
        </tr>
        <tr>
            <td>
                Ciudad
            </td>
            <td>
                <input type="text" class="txt_default" style="width: 218px !important;" id="txt_ciudad" name="txt_ciudad" value="<?= $usuario->city ?>" />
            </td>
        </tr>
        <tr>
            <td>
                C&oacute;digo postal
            </td>
            <td>
                <input type="text" class="txt_default" style="width: 218px !important;" id="txt_codigo_postal" name="txt_codigo_postal" value="<?= $usuario->zip_code ?>" />
            </td>
        </tr>
        <tr>
            <td>
                Tel&eacute;fono (opcional)
            </td>
            <td>
                <input type="text" class="txt_default" style="width: 218px !important;" id="txt_telefono" name="txt_telefono" value="<?= $usuario->telefono ?>" />
            </td>
        </tr>

        <tr>
            <td>
                Notaci&oacute;n
            </td>
            <td>
                <select name="notacion" class="combo" style="width: 232px">
                    <option value="0" <?php if($usuario->notacion == 0){ echo 'selected="selected"'; } ?> >Inglesa</option>
                    <option value="1" <?php if($usuario->notacion == 1){ echo 'selected="selected"'; } ?> >Espa&ntilde;ola</option>
                </select>
            </td>
        </tr>
        
        <!--
        <tr>
            <td>Moneda</td>
            <td>
                <select name="moneda" class="combo" style="width: 232px" disabled="disabled">
                    <option value="USD" <?php if($usuario->moneda == 'USD'){ echo 'selected="selected"'; } ?> >Dólares (USD)</option>
                    <option value="ARS" <?php if($usuario->moneda == 'ARS'){ echo 'selected="selected"'; } ?> >Pesos argentinos (ARS)</option>
                    <option value="MXN" <?php if($usuario->moneda == 'MXN'){ echo 'selected="selected"'; } ?> >Pesos mexicanos (MXN)</option>
                    <option value="COP" <?php if($usuario->moneda == 'COP'){ echo 'selected="selected"'; } ?> >Pesos colombianos (COP)</option>
                    <option value="CLP" <?php if($usuario->moneda == 'CLP'){ echo 'selected="selected"'; } ?> >Pesos chilenos (CLP)</option>
                    <option value="PEN" <?php if($usuario->moneda == 'PEN'){ echo 'selected="selected"'; } ?> >Nuevos soles peruanos (PEN)</option>
                </select>
            </td>
        </tr>
        -->
        
        <tr>
            <td>
                &nbsp;
            </td>
        </tr>
        <tr>
            <td>
                <input type="submit" class="button_new" value="Aceptar" id="btn_actualizar_datos_cuenta" name="btn_actualizar_datos_cuenta" />
                <img id="loader_actualizar_datos_cuenta" style="display:none" height="14" src="images/ajax-loader.gif" />
            </td>
            <td id="mensaje_actualizar_datos_cuenta">
            </td>
        </tr>
    </table>
</form>

<script type="text/javascript">
     jQuery(function($) {
        $.datepicker.regional['es'] = {
            monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
            monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
            dayNames: ['Domingo', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado'],
            dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mie', 'Juv', 'Vie', 'Sab'],
            dayNamesMin: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa']};
        $.datepicker.setDefaults($.datepicker.regional['es']);
    });
    $().ready(function(){
    $.datepicker.setDefaults($.datepicker.regional['es']);
        $("#fecha_nacimiento").datepicker({dateFormat: 'dd-mm-yy'});

        $("#form_actualizar_datos_cuenta").validate({
            rules:{
                txt_nombre_beneficiario:{
                    required:true
                },
                txt_domicilio:{
                    required:true
                },
                txt_ciudad:{
                    required:true
                },
                txt_codigo_postal:{
                    required:true
                }
            },
            messages:{
                txt_nombre_beneficiario:{
                    required:"Debe completar su nombre completo"
                },
                txt_domicilio:{
                    required:"Debe completar su domicilio"
                },
                txt_ciudad:{
                    required:"Debe completar el nombre de su ciudad"
                },
                txt_codigo_postal:{
                    required:"Debe completar su c&oacute;digo postal"
                }
            },
            submitHandler:function(){
                $("#loader_actualizar_datos_cuenta").css("display", "inline");

                var txt_nombre_beneficiario = $.trim($("#txt_nombre_beneficiario").val());
                var txt_empresa = $.trim($("#txt_empresa").val());
                var txt_domicilio = $.trim($("#txt_domicilio").val());
                var txt_ciudad = $.trim($("#txt_ciudad").val());
                var fecha_nacimiento = $.trim($("#fecha_nacimiento").val());
                var txt_codigo_postal = $.trim($("#txt_codigo_postal").val());
                var txt_telefono = $.trim($("#txt_telefono").val());
                var pais = $('select[name="pais"]').find(':selected').val();
                var notacion = $('select[name="notacion"]').find(':selected').val();
                var moneda = $('select[name="moneda"]').find(':selected').val();

                var form_data = {
                    txt_nombre_beneficiario: txt_nombre_beneficiario,
                    txt_empresa: txt_empresa,
                    txt_domicilio: txt_domicilio,
                    txt_ciudad: txt_ciudad,
                    txt_codigo_postal: txt_codigo_postal,
                    txt_telefono: txt_telefono,
                    cmb_pais: pais,
                    notacion: notacion,
                    moneda: moneda
                };

                $.ajax({
                    type: "POST",
                    url: "/micuenta/actualizar_datos_cuenta/",
                    data: form_data,
                    dataType: "json",
                    success: function(msg){
                        if(msg.validate)
                        {
                            $('#mensaje_actualizar_datos_cuenta').fadeIn('fast');
                            $("#mensaje_actualizar_datos_cuenta").html('Sus datos se han actualizado correctamente');
                            $("#loader_actualizar_datos_cuenta").css("display", "none");
                            setTimeout("$('#mensaje_actualizar_datos_cuenta').fadeOut('fast');",3000);
                        }
                        else
                        {
                            $('#mensaje_actualizar_datos_cuenta').fadeIn('fast');
                            $("#mensaje_actualizar_datos_cuenta").html('Ha ocurrido un error, por favor intente mas tarde');
                            $("#loader_actualizar_datos_cuenta").css("display", "none");
                            setTimeout("$('#mensaje_actualizar_datos_cuenta').fadeOut('fast');",3000);
                        }
                    }
                });
            }
        });

        
    });
</script>