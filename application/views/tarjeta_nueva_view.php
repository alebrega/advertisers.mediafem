<style type="text/css">
    label.error {
        border: none;
        color: red;
        font-weight: normal;
        padding: 5px 0 0;
        text-align: left;
        width: 400px !important;
    }
</style>

<div class="alertaOk" style="display: none;">
    Tarjeta cargada con &eacute;xito.
</div>

<form id="form_crear_tarjeta" action="/campania/ingresar_tarjeta" method="post">
    <table class="tabla" style="border-spacing: 5px; width: 100%;">
        <tr>
            <td colspan="2">
                <b>Para crear su primer campa&ntilde;a primeramente debe ingresar su primer tarjeta de cr&eacute;dito.</b>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <div class="alerta" style="font-weight: normal; margin-bottom: 15px;">
                    Por motivos de seguridad, en un plazo de no m&aacute;s de 72hs se debitar&aacute; un importe de US$ 5.00 de su tarjeta de cr&eacute;dito. Luego del d&eacute;bito, su tarjeta quedara habilitada para pagar el consumo de sus campa&ntilde;as.
                </div>
            </td>
        </tr>
        <tr>
            <td style="width: 30%;">
                Tipo de tarjeta
            </td>
            <td>
                <select name="tipo_tarjeta" class="combo" style="width: 232px">
                    <?php
                    foreach ($tipos_tarjeta as $tipo_tarjeta) {
                        ?>
                        <option value="<?= $tipo_tarjeta->descripcion ?>"><?= $tipo_tarjeta->descripcion ?></option>
                        <?php
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <td style="padding-right: 10px;">
                N&uacute;mero de tarjeta
            </td>
            <td>
                <input type="text" class="txt_default" style="width: 218px !important;" name="numero_tarjeta" value="" />
            </td>
        </tr>
        <tr>
            <td>
                C&oacute;digo de seguridad
            </td>
            <td>
                <input type="text" class="txt_default" style="width: 218px !important;" name="codigo_seguridad_tarjeta" value="" />
            </td>
        </tr>
        <tr>
            <td>
                Fecha de vencimiento
            </td>
            <td>
                <select name="mes_vencimiento_tarjeta" class="combo" style="width: 78px">
                    <?php
                    for ($mes = 1; $mes <= 12; $mes++) {
                        if ($mes < 10)
                            $mes = 0 . $mes;
                        ?>
                        <option value="<?= $mes ?>"><?= $mes ?></option>
                        <?php
                    }
                    ?>
                </select>

                <select name="ano_vencimiento_tarjeta" class="combo" style="width: 150px">
                    <?php
                    for ($ano = 2030; $ano >= 2013; $ano--) {
                        $select = '';
                        if($ano == date ("Y"))
                            $select = 'selected="selected"';
                        ?>
                        <option value="<?= $ano ?>" <?= $select ?>><?= $ano ?></option>
                        <?php
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <td colspan="2">&nbsp;</td>
        </tr>
        <tr>
            <td>
                <input type="submit" class="button_new" value="Aceptar" name="btn_ingresar_tarjeta" />
                <img id="loader_actualizar_datos_facturacion" style="display:none" height="14" src="images/ajax-loader.gif" />
            </td>
            <td id="mensaje">
                <?= $error ?>
            </td>
        </tr>
    </table>
</form>

<script src="/js/jquery.validate.creditcard2.pack-1.0.1.js" type="text/javascript"></script>

<script type="text/javascript">
    $().ready(function(){
        $("#form_crear_tarjeta").validate({
            rules:{
                numero_tarjeta:{
                    creditcard2: function(){ return $('select[name="tipo_tarjeta"]').val(); }
                },
                mes_vencimiento_tarjeta:{
                    required:true
                },
                ano_vencimiento_tarjeta:{
                    required:true
                },
                codigo_seguridad_tarjeta:{
                    required:true
                }
            },
            messages:{
                numero_tarjeta:{
                    creditcard2:"Debe ingresar un n&uacute;mero de tarjeta v&aacute;lido"
                },
                mes_vencimiento_tarjeta:{
                    required:"Debe especificar el mes de vencimiento de la tarjeta"
                },
                ano_vencimiento_tarjeta:{
                    required:"Debe especificar el a&ntilde;o de vencimiento de la tarjeta"
                },
                codigo_seguridad_tarjeta:{
                    required:"Debe especificar el c&oacute;digo de seguridad de la tarjeta"
                }
            },
            submitHandler:function(){
                $("#loader_actualizar_datos_facturacion").css("display", "inline");

                var mes_expiracion = $("select[name='mes_vencimiento_tarjeta']").find(':selected').val();
                var anio_expiracion = $("select[name='ano_vencimiento_tarjeta']").find(':selected').val();
                var txt_ccv = $.trim($("input[name='codigo_seguridad_tarjeta']").val());
                var txt_nro_tarjeta = $.trim($("input[name='numero_tarjeta']").val());
                var tipo_tarjeta = $("select[name='tipo_tarjeta']").find(':selected').val();

                if(tipo_tarjeta == "MasterCard"){
                    tipo_tarjeta = 2;
                }else if(tipo_tarjeta == "Visa"){
                    tipo_tarjeta = 1;
                }

                var form_data = {
                    mes_vencimiento_tarjeta: mes_expiracion,
                    ano_vencimiento_tarjeta: anio_expiracion,
                    codigo_seguridad_tarjeta: txt_ccv,
                    tipo_tarjeta : tipo_tarjeta,
                    numero_tarjeta: txt_nro_tarjeta,
                    desde: '<?= $desde ?>'
                };

                $.ajax({
                    type: "POST",
                    url: "/campania/ingresar_tarjeta/",
                    data: form_data,
                    dataType: "json",
                    success: function(msg){
                        $('#form_crear_tarjeta').fadeOut();
                        $('.alertaOk').fadeIn('fast', function(){
                            $('.alertaOk').delay(2000);
                            if( msg.desde == 'mis_tarjetas'){
                                window.location.replace("/micuenta");
                            }else{
                                window.location.replace("/campania");
                            }
                        });
                    }
                });
            }
        });
    });
</script>