<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
    <head>
        <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
        <title>Administrador MediaFem</title>
        <?php require_once 'head_links.php'; ?>

        <script src="/js/jquery.dataTables.js" type="text/javascript" charset="utf-8"></script>

        <style type="text/css" title="currentStyle">
            @import "/css/demo_page.css";
            @import "/css/demo_table_jui.css";

            #datos_campanias table{
                border-spacing: 12px;
            }

            input[disabled]{
                border:1px solid #bbb;
                background-color: #ccc;
                cursor: no-drop;
            }
        </style>

        <script type="text/javascript">

            jQuery.fn.dataTableExt.oSort['uk_date-asc']  = function(a,b) {
                var ukDatea = a.split('/');
                var ukDateb = b.split('/');

                var x = (ukDatea[2] + ukDatea[1] + ukDatea[0]) * 1;
                var y = (ukDateb[2] + ukDateb[1] + ukDateb[0]) * 1;

                return ((x < y) ? -1 : ((x > y) ?  1 : 0));
            };

            jQuery.fn.dataTableExt.oSort['uk_date-desc'] = function(a,b) {
                var ukDatea = a.split('/');
                var ukDateb = b.split('/');

                var x = (ukDatea[2] + ukDatea[1] + ukDatea[0]) * 1;
                var y = (ukDateb[2] + ukDateb[1] + ukDateb[0]) * 1;

                return ((x < y) ? 1 : ((x > y) ?  -1 : 0));
            };

            jQuery(function($){
                $.datepicker.regional['es'] = {
                    monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                    monthNamesShort: ['Ene','Feb','Mar','Abr', 'May','Jun','Jul','Ago','Sep', 'Oct','Nov','Dic'],
                    dayNames: ['Domingo', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado'],
                    dayNamesShort: ['Dom','Lun','Mar','Mie','Juv','Vie','Sab'],
                    dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sa']};
                $.datepicker.setDefaults($.datepicker.regional['es']);
            });

            $().ready(function() {
                var now = new Date();
                var seconds = now.getMinutes()+'o'+now.getSeconds();

                $.datepicker.setDefaults($.datepicker.regional['es']);
                $("#fecha_nueva_fin_campania").datepicker({ dateFormat:'dd-mm-yy' });

                var oTable = $('#tbl_campanias').dataTable( {
                    "sPaginationType": "full_numbers",
                    "aLengthMenu": [[10, 20, 50, 100, -1], [10, 20, 50, 100, "Todos"]],
                    iDisplayLength: 20,
                    "bJQueryUI": true,
                    "aaSorting": [[ 2, "desc" ]],
                    "bInfo": false,
                    "oLanguage": {
                        "sSearch": "Buscar"
                    },
                    "aoColumns": [
                        null,
                        null,
                        { "sType": "uk_date" },
                        { "sType": "uk_date" },
                        null,
                        null,
                        { "bSortable": false }
                    ]
                });


                $('.link_ver').click(function(){
                    var id_campania = $(this).attr('id');

                    $(this).fadeOut('fast', function(){
                        $('#loader_ver_'+id_campania).fadeIn();
                    });

                    $('input[name="id_campania"]').val(id_campania);

                    var form_data = {id_campania: id_campania}

                    $.ajax({
                        type: "POST",
                        url: "/facturacion/datos_facturacion/",
                        data: form_data,
                        dataType: "json",
                        success: function(data){
                            $('#nombre_campania').html(data.campania.nombre);
                            $('#fecha_alta').html(data.campania.fecha_alta);
                            $('#fecha_inicio').html(data.campania.fecha_inicio);
                            $('#fecha_fin').html(data.campania.fecha_fin);

                            $('#lineItems').html(' ');

                            // genero la tabla para la cantidad de lineItems en caso de reactivar
                            var code = '<table><tr><th style="width: 200px;">Nombre Line Item</th><th style="width: 200px; text-align:left;">Cantidad</th></tr>';
                            for(x in data.lineItems){
                                var li = data.lineItems[x];
                                var modalidad = 'Impresiones.';
                                if(li.unitType == 'CLICKS'){
                                    modalidad = 'Clicks.'
                                }
                                code += '<tr><td>' + li.name + '</td><td><input type="text" value="0" name="cantidad_clicks" id="' + li.id + '" style="width: 50px;" /> ' + modalidad + '</td></tr>' ;
                            }
                            code += '</table>';

                            $('#lineItems').append(code);

                            // data.formatos_seleccionados.descripcion;

                            $('#facturacion_bruta').html('U$S' + data.campania.inversion_bruta);
                            $('#comision').html(data.campania.comision + '%');
                            $('#descuento').html(data.campania.descuento + '%');
                            $('#facturacion_neta').html('U$S ' + data.campania.inversion_neta);
                            $('#costo').html('U$S ' + data.costo);
                            $('#ganancia').html('U$S ' + (data.campania.inversion_neta - data.costo));

                            $('select[name="facturada"] option').each(function(){
                                if( $(this).val() == data.control.facturada ){
                                    $(this).attr('selected','selected');
                                }

                                $('select[name="facturada"]').change();
                            });


                            $('input[name="fecha_factura"]').val(data.control.factura_fecha);
                            $('input[name="numero_factura"]').val(data.control.factura_num);

                            $('select[name="facturada_por"] option').each(function(){
                                if( $(this).val() == data.control.user_facturada ){
                                    $(this).attr('selected','selected');
                                }
                            });

                            $('select[name="cobrada"] option').each(function(){
                                if( $(this).val() == data.control.cobrada ){
                                    $(this).attr('selected','selected');
                                }

                                $('select[name="cobrada"]').change();
                            });

                            $('input[name="fecha_recibo"]').val(data.control.recibo_fecha);
                            $('input[name="numero_recibo"]').val(data.control.recibo_num);

                            $('select[name="cobrada_por"] option').each(function(){
                                if( $(this).val() == data.control.user_cobrada ){
                                    $(this).attr('selected','selected');
                                }
                            });


                            if( data.control.reactivada == 'SI' || data.control.facturada == 'SI'){
                                $('input[name="reactivar"]').css('display' , 'none');
                            }else{
                                $('input[name="reactivar"]').css('display' , 'inline-block');
                            }


                            $('#datos_facturacion').fadeIn();


                            $('#loader_ver_'+id_campania).fadeOut('fast', function(){
                                $('a[id="'+id_campania+'"]').fadeIn();
                            });
                        }
                    });
                });


                $('select[name="facturada"]').change(function(){
                    if( $(this).val() == '1'){
                      //  $('input[name="fecha_factura"]').attr('disabled', false);
                        $('input[name="numero_factura"]').attr('disabled', false);
                        $('select[name="facturada_por"]').attr('disabled', false);
                    }else{
                       // $('input[name="fecha_factura"]').attr('disabled', 'disabled');
                        $('input[name="numero_factura"]').attr('disabled', 'disabled');
                        $('select[name="facturada_por"]').attr('disabled', 'disabled');
                    }
                });

                $('select[name="cobrada"]').change(function(){
                    if( $(this).val() == '1'){
                        //$('input[name="fecha_recibo"]').attr('disabled', false);
                        $('input[name="numero_recibo"]').attr('disabled', false);
                        $('select[name="cobrada_por"]').attr('disabled', false);
                    }else{
                        //$('input[name="fecha_recibo"]').attr('disabled', 'disabled');
                        $('input[name="numero_recibo"]').attr('disabled', 'disabled');
                        $('select[name="cobrada_por"]').attr('disabled', 'disabled');
                    }
                });

                $('input[name="enviar"]').click(function(){
                    $('#loader_submit').fadeIn("fast");

                    $(".msg_error").css("display", "none");

                    var error = false;

                    // valido si esta "Facturada"
                    var facturada = $('select[name="facturada"]').val();
                    var fecha_factura = $.trim( $('input[name="fecha_factura"]').val() );
                    var numero_factura = $.trim( $('input[name="numero_factura"]').val() );
                    var facturada_por = $('select[name="facturada_por"]').val();

                    var cobrada = $('select[name="cobrada"]').val();
                    var fecha_recibo = $.trim( $('input[name="fecha_recibo"]').val() );
                    var numero_recibo = $.trim( $('input[name="numero_recibo"]').val() );
                    var cobrada_por = $('select[name="cobrada_por"]').val();

                    if( facturada == '1' ){
                        if(fecha_factura.length <= 0){
                            $('#error_fecha_factura').fadeIn();
                            error = true;
                        }

                        if(numero_factura.length <= 0){
                            $('#error_numero_factura').fadeIn();
                            error = true;
                        }
                    }

                    if( cobrada == '1' ){
                        if(fecha_recibo.length <= 0){
                            $('#error_fecha_recibo').fadeIn();
                            error = true;
                        }

                        if(numero_recibo.length <= 0){
                            $('#error_numero_recibo').fadeIn();
                            error = true;
                        }
                    }

                    // si surgió algun error durante la validación.
                    if(error == true){
                        $('#loader_submit').fadeOut("fast", function(){
                            $("#error_facturacion").css("display", "inline");
                        });

                        return false;
                    }

                    var form_data = {
                        id_campania: $('input[name="id_campania"]').val(),
                        facturada: facturada,
                        fecha_factura: fecha_factura,
                        numero_factura: numero_factura,
                        facturada_por: facturada_por,
                        cobrada: cobrada,
                        fecha_recibo: fecha_recibo,
                        numero_recibo: numero_recibo,
                        cobrada_por: cobrada_por
                    }

                    $.ajax({
                        type: "POST",
                        url: "/facturacion/update_datos_facturacion/",
                        data: form_data,
                        dataType: "json",
                        success: function(){
                            $('#loader_submit').fadeOut("fast", function(){
                                alert('Cambios almacenados correctamente.');

                                $('#datos_facturacion').fadeOut();
                            });
                        }
                    });
                });

                $('input[name="cancelar_reactivar"]').click(function(){
                    $('#div_motivo').css('display', 'none');
                    $('input[name="enviar"]').css('display', 'inline-block');
                });

                $('input[name="reactivar"]').click(function(){
                    $('#div_motivo').css('display', 'inline-block');
                    $('input[name="enviar"]').css('display', 'none');
                });

                $('input[name="aceptar_reactivar"]').click(function(){
                    if($('#fecha_nueva_fin_campania').val() == ''){
                        alert('Por favor seleccione una nueva fecha fin para la campania.')
                        return false;
                    }

                    $('#loader_submit').fadeIn("fast");

                    var lineItems = new Array();
                    var x = 0;
                    $('input[name="cantidad_clicks"]').each(function(){
                        var cantidad = 0;
                        if($(this).val() != ''){
                            cantidad = $(this).val();
                        }
                        lineItems[x] = new Array($(this).attr('id'), cantidad);
                        x++;
                    });

                    var form_data = {
                        id_campania: $('input[name="id_campania"]').val(),
                        motivo: $('select[name="motivo"]').val(),
                        fecha_fin: $('#fecha_nueva_fin_campania').val(),
                        lineItems: lineItems
                    }

                    $.ajax({
                        type: "POST",
                        url: "/facturacion/reactivar_campania/",
                        data: form_data,
                        dataType: "json",
                        success: function(){
                            $('#loader_submit').fadeOut("fast", function(){
                                window.location.replace('/facturacion');
                            });
                        }
                    });
                });
$("#campofecha_factura").datepicker();
$("#campofecha_recibo").datepicker();
            });


        </script>
        <?php require_once BASEPATH . '/application/views/analytics.html'; ?>
    </head>

    <body>
        <?php
        require_once BASEPATH . '/application/views/top.php';
        ?>

        <div id="prueba"></div>

        <table class="tabla">
            <tr class="encabezado_celeste">
                <td colspan="2">Datos de facturaci&oacute;n de campa&ntilde;as.</td>
            </tr>
            <tr>
                <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="2">
                    <?php
                    if ($campanias == FALSE) {
                        echo 'No se encontraron campanias';
                    } else {
                        ?>
                        <table class="display" id="tbl_campanias" style="width: 100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Fecha de inicio</th>
                                    <th>Fecha fin</th>
                                    <th>Facturada</th>
                                    <th>Cobrada</th>
                                    <th>&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($campanias as $campania) {
                                    $style = '';
                                    if ($campania['control']->reactivada == '1') {
                                        $style = 'style="color:red; font-weight:bold;"';
                                    }
                                    ?>
                                    <tr <?php echo $style; ?>>
                                        <td><?= $campania['campania']->id ?></td>
                                        <td><?= $campania['campania']->nombre ?></td>
                                        <td style="text-align:center;"><?= MySQLDateToDate($campania['campania']->fecha_inicio) ?></td>
                                        <td style="text-align:center;"><?= MySQLDateToDate($campania['campania']->fecha_fin) ?></td>
                                        <td style="text-align:center;">
                                            <?php
                                            if ($campania['control']->facturada == 0) {
                                                echo 'NO';
                                            } else {
                                                echo 'SI';
                                            }
                                            ?>
                                        </td>
                                        <td style="text-align:center;">
                                            <?php
                                            if ($campania['control']->cobrada == 0) {
                                                echo 'NO';
                                            } else {
                                                echo 'SI';
                                            }
                                            ?>
                                        </td>
                                        <td style="text-align:center;">
                                            <a href="javascript:;" class="link_ver" id="<?= $campania['campania']->id ?>" >Ver</a>
                                            <div id="loader_ver_<?= $campania['campania']->id ?>" style="display:none"><img src="/images/ajax-loader.gif" /></div>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>
                            </tbody>
                        </table>
                        <?php
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td colspan="2" id="datos_campanias">
                    <table width="100%" id="datos_facturacion" style="display: none;">
                        <tr class="encabezado_celeste">
                            <td colspan="2">
                                Modificar facturaci&oacute;n de campa&ntilde;as.
                                <input type="hidden" name="id_campania" value="" />
                            </td>
                        </tr>
                        <tr>
                            <td style="width:160px;">Nombre de la campa&ntilde;a:</td>
                            <td><b id="nombre_campania">ASD</b></td>
                        </tr>
                        <tr>
                            <td>Fecha Creada:</td>
                            <td><b id="fecha_alta">18/05/65</b></td>
                        </tr>
                        <tr>
                            <td>Periodo:</td>
                            <td>del <b id="fecha_inicio">19/04/86</b> al <b id="fecha_fin">18/05/65</b></td>
                        </tr>
                        <tr>
                            <td colspan="2">&nbsp;</td>
                        </tr>
                        <tr>
                            <td>Facturaci&oacute;n bruta:</td>
                            <td><b id="facturacion_bruta">18/05/65</b></td>
                        </tr>
                        <tr>
                            <td>Comisi&oacute;n de agencia:</td>
                            <td><b id="comision">18/05/65</b></td>
                        </tr>
                        <tr>
                            <td>Descuento:</td>
                            <td><b id="descuento">18/05/65</b></td>
                        </tr>
                        <tr>
                            <td>Facturaci&oacute;n neta:</td>
                            <td><b id="facturacion_neta">18/05/65</b></td>
                        </tr>
                        <tr>
                            <td>Costo:</td>
                            <td><b id="costo">18/05/65</b></td>
                        </tr>
                        <tr>
                            <td>Ganancia:</td>
                            <td><b id="ganancia">18/05/65</b></td>
                        </tr>
                        <tr>
                            <td colspan="2">&nbsp;</td>
                        </tr>
                        <tr>
                            <td>Facturada:</td>
                            <td>
                                <select name="facturada">
                                    <option value="0">NO</option>
                                    <option value="1">SI</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            
                            <td>Fecha Factura:</td>
                            <td>
                                <input type="text" name="fecha_factura" id="campofecha_factura"></input>
                                <span class="msg_error" style="display:none;" id="error_fecha_factura">Por favor ingrese la fecha de la factura.</span>
                            </td>
                        </tr>
                        <tr>
                            <td>N&uacute;mero Factura:</td>
                            <td>
                                <input type="text" name="numero_factura" value="" disabled="disabled" />
                                <span class="msg_error" style="display:none;" id="error_numero_factura">Por favor ingrese el n&uacute;mero de la factura.</span>
                            </td>
                        </tr>
                        <tr>
                            <td>Facturada por:</td>
                            <td>
                                <select name="facturada_por" disabled="disabled">
                                    <?php
                                    foreach ($usuarios as $row) {
                                        echo '<option value="' . $row->id . '">' . $row->nombre_completo . ' (' . $row->email . ')</option>';
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Cobrada:</td>
                            <td>
                                <select name="cobrada">
                                    <option value="0">NO</option>
                                    <option value="1">SI</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Fecha Recibo:</td>
                            <td>
                                
                                  <input type="text" name="fecha_recibo" id="campofecha_recibo"></input>
                                <span class="msg_error" style="display:none;" id="error_fecha_recibo">Por favor ingrese la fecha del recibo.</span>
                            </td>
                        </tr>
                        <tr>
                            <td>N&uacute;mero Recibo:</td>
                            <td>
                                <input type="text" name="numero_recibo" value="" disabled="disabled" />
                                <span class="msg_error" style="display:none;" id="error_numero_recibo">Por favor ingrese el n&uacute;mero del recibo.</span>
                            </td>
                        </tr>
                        <tr>
                            <td>Cobrada por:</td>
                            <td>
                                <select name="cobrada_por" disabled="disabled">
                                    <?php
                                    foreach ($usuarios as $row) {
                                        echo '<option value="' . $row->id . '">' . $row->nombre_completo . ' (' . $row->email . ')</option>';
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top;">
                                <input type="button" name="reactivar" value="Reactivar campa&ntilde;a" class="button_new" />
                            </td>
                            <td>
                                <span id="div_motivo" style="display:none;">
                                    <table style="border-spacing: 0px 12px;">
                                        <tr>
                                            <td colspan="2">
                                                <select name="motivo">
                                                    <option value="1">No se consumio la inversión en la fecha esperada.</option>
                                                    <option value="2">Se consumio la inversion antes de la fecha esperada.</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding-left: 4px;width: 160px">
                                                Nueva fecha de finalizaci&oacute;n:
                                            </td>
                                            <td>
                                                <input type="text" size="10" id="fecha_nueva_fin_campania" name="fecha_nueva_fin_campania" readonly="true"/>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td colspan="2" id="lineItems"></td>
                                        </tr>

                                        <tr>
                                            <td colspan="2">
                                                <input type="button" name="aceptar_reactivar" value="Aceptar" class="button_new" />
                                                <input type="button" name="cancelar_reactivar" value="Cancelar" class="button_new" />
                                            </td>
                                        </tr>
                                    </table>
                                </span>

                                <input type="button" name="enviar" value="Guardar cambios" class="button_new" />
                                <span id="loader_submit" style="display:none"><img src="/images/ajax-loader.gif" />
                                    Almacenando datos, aguarde por favor...
                                </span>
                                <span class="msg_error" style="display:none;" id="error_facturacion">Se encontraron errores al intentar modificar los datos.</span>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <?php
        require_once BASEPATH . '/application/views/footer.html';
        ?>
    </body>
</html>