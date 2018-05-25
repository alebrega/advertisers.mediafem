<table id="personalizar_formatos">
    <tr>
        <th style="padding: 0 20px 10px 0;">Tipo</th>
        <th class="textCenter" style="padding: 0 20px 10px 0;">Modalidad de compra</th>
        <th class="textCenter">Valor</th>
        <th class="textCenter" style="padding: 0 20px 10px 0;">% a consumir</th>
        <th style="padding: 0 20px 10px 0;">P&aacute;gina de destino</th>
    </tr>

    <?php
    $a = 1;
    $porcentaje_total = 0;
    $total_formatos = sizeof($formatos);
    foreach ($formatos as $formato) {
        ?>
        <tr>
            <td style="padding: 0 20px 5px 0;">
                <input type="checkbox" name="chk_formatos[]" value="<?= $formato->id ?>" rel="<?= $a ?>" />&nbsp;<?= $formato->descripcion ?>
            </td>
            <td class="textCenter" style="padding: 0 20px 5px 0;">
                <select name="modalidad" id="modalidad_<?= $a ?>" style="width:104px !important;" formato="<?= $formato->id ?>" acepta="true"  rel="<?= $a ?>" disabled="disabled">
                    <option selected="selected" value="CPM">CPM</option>
                    <option value="CPC">CPC</option>
                </select>
            </td>
            <td class="textCenter" style="padding: 0 20px 5px 0;">
                US$ <input type="text" name="monto" style="width:50px !important;" id="monto_<?= $a ?>" value="" size="5"  rel="<?= $a ?>" />
                <input type="hidden" name="monto_oculto" id="monto_oculto_<?= $a ?>" value="" />
            </td>
            <td class="textCenter" style="padding: 0 20px 5px 0;">
                <?php
                if ($a == $total_formatos)
                    $porcentaje_default = 100 - $porcentaje_total;
                ?>
                <input type="text" name="cantidad" style="width:56px !important;" id="cantidad_<?= $a ?>" value="<?= $porcentaje_default ?>" maxlength="3" rel="<?= $a ?>" disabled="disabled" />
            </td>
            <td style="padding: 0 20px 5px 0;">
                <input type="text" name="pagina_destino" style="width:250px !important;" id="pagina_destino_<?= $a ?>" value="" placeholder="http://www.ejemplo.com/" rel="<?= $a ?>" disabled="disabled" />
                <img src="/images/ajax-loader.gif" height="10px" id="loader_tamano_<?= $a ?>" style="display:none" />
            </td>
        </tr>
        <?php
        $porcentaje_total += $porcentaje_default;
        $a++;
    }
    ?>
</table>

<script type="text/javascript">
    $('input[name="monto"]').keyup(function(){
        $(this).val( $(this).val().replace(/,/g,".") );
        if(isNaN($(this).val()) || $(this).val() <= 0){
            $("#error_formato").html('Por favor ingrese un monto valido.').css("display", "inline");
            $(this).css('borderColor', 'red');
            return false;
        }else{
            $("#error_formato").html('').css("display", "none");
            $(this).css('borderColor', '');
        }
    });

    $().ready(function(){
        $('input[type="checkbox"]').each(function(){
            if( $(this).attr('name') == 'chk_formatos[]' ){

                var rel = $(this).attr('rel');

                $(this).attr('checked', 'checked')

                $('#modalidad_' + rel).each(function(){
                    $(this).attr('disabled',false);
                });

                $('#pagina_destino_' + rel).each(function(){
                    $(this).attr('disabled',false);
                });

                if($('input[name="chk_formatos[]"]').length > 1){
                    $('#cantidad_' + rel).each(function(){
                        $(this).attr('disabled',false);
                    });
                }

                var formato = $('select[id="modalidad_'+rel+'"]').attr('formato');
                var segmentacion = $('#cmb_segmentaciones').val();
                var modalidad = $('select[id="modalidad_'+rel+'"]').val();
                var select = $('select[name="modalidad_'+rel+'"]');

                $('#monto_oculto_'+rel).load('/tarifario/get_valor/'+formato+'/'+segmentacion+'/'+modalidad, function(monto){
                    monto = monto.replace('"','');
                    monto = monto.replace('"','');
                    monto = monto.replace('\\','');

                    if($('#monto_'+rel).val() == ''){
                        $('#monto_oculto_'+rel).val(monto);
                        $('#monto_'+rel).val(monto);
                    }

                    if( monto == 'N/A' ){
                        // $('#monto_'+rel).attr('disabled', 'disabled');
                        $('#pagina_destino_'+rel).attr('disabled', 'disabled');
                        $('#cantidad_'+rel).attr('disabled', 'disabled');

                        alert('Mensaje de que no corresponde la modalidad en el formato seleccionado.');
                        $(select).attr('acepta','false');
                    }else{
                        if($('input[name="chk_formatos[]"]').length > 1){
                            $('#cantidad_'+rel).attr('disabled', false);
                        }
                        $(select).attr('acepta','true');
                    }

                    $('#loader_tamano_'+rel).css('display','none');
                });
            }
        });


        /***************** CHECKBOX FORMATOS *********************/
        $('input[type="checkbox"]').click(function(){
            if( $(this).attr('name') == 'chk_formatos[]' ){
                var rel = $(this).attr('rel');

                // traigo el valor correspondiente.
                if($(this).attr('checked') == 'checked'){
                    var formato = $('select[id="modalidad_'+rel+'"]').attr('formato');
                    var segmentacion = $('#cmb_segmentaciones').val();
                    var modalidad = $('select[id="modalidad_'+rel+'"]').val();

                    var select = $('select[name="modalidad_'+rel+'"]');

                    $('#loader_tamano_'+rel).css('display','inline');

                    $('#monto_oculto_'+rel).load('/tarifario/get_valor/'+formato+'/'+segmentacion+'/'+modalidad, function(monto){
                        monto = monto.replace('"','');
                        monto = monto.replace('"','');
                        monto = monto.replace('\\','');

                        if($('#monto_'+rel).val() == ''){
                            $('#monto_oculto_'+rel).val(monto);
                            $('#monto_'+rel).val(monto);
                        }

                        if( monto == 'N/A' ){
                            //  $('#monto_'+rel).attr('disabled', 'disabled');
                            $('#pagina_destino_'+rel).attr('disabled', 'disabled');
                            $('#cantidad_'+rel).attr('disabled', 'disabled');

                            alert('Mensaje de que no corresponde la modalidad en el formato seleccionado.');
                            $(select).attr('acepta','false');
                        }else{
                            //$('#monto_'+rel).attr('disabled', false);
                            $('#pagina_destino_'+rel).attr('disabled', false);

                            if($('input[name="chk_formatos[]"]').length > 1){
                                $('#cantidad_'+rel).attr('disabled', false);
                            }
                            $(select).attr('acepta','true');
                        }

                        $('#loader_tamano_'+rel).css('display','none');
                    });
                }

                // si clikie en todos los tipos
                if(rel == '0'){
                    if($(this).attr('checked') == 'checked'){
                        // bloqueo todos los elementos y al finalizar activo los de "Todos los tipos"
                        $('select[name="modalidad"]').each(function(){
                            $(this).attr('disabled','disabled');
                        });
                        /* $('input[name="monto"]').each(function(){
                          $(this).attr('disabled','disabled');
                     });*/
                        $('input[name="pagina_destino"]').each(function(){
                            $(this).attr('disabled','disabled');
                        });

                        $('input[name="cantidad"]').each(function(){
                            $(this).attr('disabled','disabled');
                        });

                        $('input[type="checkbox"]').each(function(){
                            if( $(this).attr('name') == 'chk_formatos[]' ){
                                $(this).attr('checked',false);
                            }
                        });

                        $('input[rel="0"]').attr('checked','checked');

                        $('#modalidad_0').each(function(){
                            $(this).attr('disabled',false);
                        });

                        if($('input[name="chk_formatos[]"]').length > 1){
                            $('#cantidad_0').each(function(){
                                $(this).attr('disabled',false);
                            });
                        }
                    }else{
                        // bloqueo los elementos de "Todos los tipos"
                        $('select[name="modalidad"]').each(function(){
                            $(this).attr('disabled','disabled');
                        });
                        /* $('input[name="monto"]').each(function(){
                            $(this).attr('disabled','disabled');
                        });*/
                        $('input[name="pagina_destino"]').each(function(){
                            $(this).attr('disabled','disabled');
                        });
                        $('input[name="cantidad"]').each(function(){
                            $(this).attr('disabled','disabled');
                        });
                    }
                }else{
                    if($(this).attr('checked') == 'checked'){
                        // si selecciono otro entonces bloqueo "Todos los tipos" y habilito el seleccionado
                        $('#modalidad_0').each(function(){
                            $(this).attr('disabled','disabled');
                        });
                        /* $('#monto_0').each(function(){
                            $(this).attr('disabled','disabled');
                        });*/
                        $('#pagina_destino_0').each(function(){
                            $(this).attr('disabled','disabled');
                        });

                        $('#cantidad_0').each(function(){
                            $(this).attr('disabled','disabled');
                        });

                        $('input[rel="0"]').attr('checked',false);

                        $('#modalidad_' + rel).each(function(){
                            $(this).attr('disabled',false);
                        });

                        if($('input[name="chk_formatos[]"]').length > 1){
                            $('#cantidad_' + rel).each(function(){
                                $(this).attr('disabled',false);
                            });
                        }
                    }else{
                        $('#modalidad_' + rel).each(function(){
                            $(this).attr('disabled', 'disabled');
                        });
                        /* $('#monto_' + rel).each(function(){
                            $(this).attr('disabled','disabled');
                        });*/
                        $('#pagina_destino_' + rel).each(function(){
                            $(this).attr('disabled','disabled');
                        });

                        $('#cantidad_' + rel).each(function(){
                            $(this).attr('disabled','disabled');
                        });
                    }
                }

                if($('input[name="chk_formatos[]"]').length <= 1){
                    $('#cantidad_1').attr('disabled', 'disabled');
                }
            }
        });


        $('select[name="modalidad"]').change(function(){
            $('select[name="modalidad"]').change();
        });


        $('select[name="modalidad"]').change(function(){
            var rel = $(this).attr('rel');
            var formato = $(this).attr('formato');
            var segmentacion = $('#cmb_segmentaciones').val();
            var modalidad = $(this).val();

            var select = this;

            $('#loader_tamano_'+rel).css('display','inline');

            $('#monto_oculto_'+rel).load('/tarifario/get_valor/'+formato+'/'+segmentacion+'/'+modalidad, function(monto){
                monto = monto.replace('"','');
                monto = monto.replace('"','');
                monto = monto.replace('\\','');

                $('#monto_oculto_'+rel).attr('value', monto);
                $('#monto_'+rel).val(monto);

                if( monto == 'N/A' ){
                    // $('#monto_'+rel).attr('disabled', 'disabled');
                    $('#pagina_destino_'+rel).attr('disabled', 'disabled');
                    $('#cantidad_'+rel).attr('disabled', 'disabled');

                    alert('Mensaje de que no corresponde la modalidad en el formato seleccionado.');
                    $(select).attr('acepta','false');
                }else{
                    //$('#monto_'+rel).attr('disabled', false);
                    $('#pagina_destino_'+rel).attr('disabled', false);

                    if($('input[name="chk_formatos[]"]').length > 1){
                        $('#cantidad_'+rel).attr('disabled', false);
                    }

                    $(select).attr('acepta','true');
                }

                $('#loader_tamano_'+rel).css('display','none');
            });
        });

        if($('input[name="chk_formatos[]"]').length <= 1){
            $('#cantidad_1').attr('disabled', 'disabled');
        }
    });
</script>