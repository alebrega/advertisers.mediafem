<style type="text/css">
    .select{
        display: inline-block;
        padding: 4px 6px;
        width: 201px !important;
        height:160px;
        color: #555555;
        vertical-align: middle;
        border-radius: 3px;

        overflow-x:hidden;
        overflow-y:scroll;

        background-color: #ffffff;
        border: 1px solid #C1C5C8;
        -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
        -moz-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
        box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
        -webkit-transition: border linear 0.2s, box-shadow linear 0.2s;
        -moz-transition: border linear 0.2s, box-shadow linear 0.2s;
        -o-transition: border linear 0.2s, box-shadow linear 0.2s;
        transition: border linear 0.2s, box-shadow linear 0.2s;
    }

    .item {
        margin-bottom:4px;
        padding: 4px 6px 0;
        border-radius: 3px;
        border: 1px solid #C1C5C8;
        -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
        -moz-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
        box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
        -webkit-transition: border linear 0.2s, box-shadow linear 0.2s;
        -moz-transition: border linear 0.2s, box-shadow linear 0.2s;
        -o-transition: border linear 0.2s, box-shadow linear 0.2s;
        transition: border linear 0.2s, box-shadow linear 0.2s;
    }

    .name-audiencia {
        top:-3px;
        display: inline-block;
        position:relative;
        width: 104px;
        overflow:hidden;
        white-space:nowrap;
        text-overflow: ellipsis;
        font-size: 11px;
    }

    .item i{
        cursor: pointer;
        display: inline-block;
        width: 18px;
        height: 19px;
        background: #fff url('<?= base_url() ?>images/multiselect-sprite.png') no-repeat;
    }

    .include-btn {background-position: -1px -100px !important;}
    .exclude-btn {background-position: -21px -100px !important;}
    .erase-btn {background-position: -1px -120px !important; margin:0 !important;}

    .include-btn.active {background-position: -1px 0 !important;}
    .exclude-btn.active {background-position: -21px 0 !important;}
    .erase-btn.active {background-position: -21px 120px !important;}

    .include-btn:hover {background-position: -1px -60px !important;}
    .exclude-btn:hover  {background-position: -21px -60px !important;}
    .erase-btn:hover  {background-position: -21px -120px !important;}
</style>

<table style="margin-left:65px !important;">
    <tr>
        <td>
            <select style="width: 201px;height:170px;" size="10" id="cmb_audiencias" name="cmb_audiencias" multiple="multiple">
                <?php
                foreach ($audiencias as $audiencia) {
                    $insertar = true;
                    foreach ($audiencias_campania as $audiencia_campania) {
                        if ($audiencia->name == $audiencia_campania->name) {
                            $insertar = false;
                            break;
                        }
                    }

                    if ($insertar) {
                        ?>
                        <option value="<?php echo $audiencia->id; ?>"><?php echo $audiencia->name; ?></option>
                        <?php
                    }
                }
                ?>
            </select>
        </td>
        <td style="width: 10px; vertical-align: middle !important; padding:0 10px;">
            <table>
                <tr>
                    <td><input type="button" value=">>" id="btn_pasar_audiencia" class="button_new" /></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>
            </table>
        </td>
        <td>
            <div class="select">
                <?php
                foreach ($audiencias as $audiencia) {
                    $style = 'display: none;';
                    if ($audiencias_campania) {
                        foreach ($audiencias_campania as $audiencia_campania) {
                            if ($audiencia->name == $audiencia_campania->name) {
                                $style = '';
                                break;
                            }
                        }
                    }
                    ?>

                    <div class="item" id="item_<?php echo $audiencia->id; ?>" style="<?= $style ?>">
                        <input type="hidden" data-visible="off" data-action="include" data-name-audiencia="<?php echo $audiencia->name; ?>" data-id-audiencia="<?php echo $audiencia->id; ?>" />
                        <span class="inc-exc-toggle">
                            <i class="include-btn active" data-referrer="item_<?php echo $audiencia->id; ?>"></i>
                            <i class="exclude-btn" data-referrer="item_<?php echo $audiencia->id; ?>"></i>
                        </span>
                        <span class="name-audiencia" title="<?php echo $audiencia->name; ?>">
                            <?php echo $audiencia->name; ?>
                        </span>
                        <span>
                            <i class="erase-btn" data-referrer="item_<?php echo $audiencia->id; ?>"></i>
                        </span>
                    </div>
                    <?php
                }
                ?>
            </div>
        </td>
    </tr>

    <tr>
        <td colspan="3">
            <input type="button" class="button_new" name="audiencia_guardar_seleccion" id="audiencia_guardar_seleccion" value="Guardar" style="margin-top: 10px;" />
        </td>
    </tr>
</table>

<script type="text/javascript">
    $().ready(function(){
        // AUDIENCIAS **************************************************************
        $("#btn_pasar_audiencia").click( function (){
            $('#cmb_audiencias option:selected').appendTo("#cmb_audiencias_2");

            var seleccionado = $('#cmb_audiencias option:selected');

            $('#item_' + seleccionado.val()).css('display', 'block');

            $('#item_' + seleccionado.val() + ' input[type="hidden"]').attr('data-visible', 'on');

            $('#cmb_audiencias option:selected').remove();
        });

        $('.erase-btn').click(function(){
            var id = $(this).attr('data-referrer');

            var name_audiencia = $('#' + id + ' input[type="hidden"]').attr('data-name-audiencia');
            var id_audiencia = $('#' + id + ' input[type="hidden"]').attr('data-id-audiencia');

            $('#' + id + ' input[type="hidden"]').attr('data-visible', 'off');

            var html = $('#cmb_audiencias').html();

            $('#' + id).css('display', 'none');

            $('#cmb_audiencias').html(html + '<option value="' + id_audiencia + '">' + name_audiencia + '</option>');
        });

        $("#cmb_audiencias").dblclick( function (){
            $("#btn_pasar_audiencia").click();
        });

        $(".include-btn").click( function (){
            var id = $(this).attr('data-referrer');

            $('#' + id + " .exclude-btn").removeClass('active');
            $(this).addClass('active');

            $('#' + id + ' input[type="hidden"]').attr('data-action', 'include');
        });

        $(".exclude-btn").click( function (){
            var id = $(this).attr('data-referrer');

            $('#' + id + " .include-btn").removeClass('active');
            $(this).addClass('active');

            $('#' + id + ' input[type="hidden"]').attr('data-action', 'exclude');
        });

        $('#audiencia_guardar_seleccion').click(function(){
            var audiencias_seleccionadas = '';

            $('.select div.item input[type="hidden"]').each(function(){
                if($(this).attr('data-visible') == 'on'){
                    audiencias_seleccionadas = audiencias_seleccionadas + $(this).attr('data-id-audiencia') + ',' + $(this).attr('data-action') + ';';
                }
            });

            var form_data = {
                id_campania: <?php echo $id_campania; ?>,
                audiencias_seleccionadas: audiencias_seleccionadas
            };

            $.ajax({
                type: "POST",
                url: "/campania/asociar_audiencias_a_campania/",
                data: form_data,
                dataType: "json",
                success: function(msg){

                    $('.reveal-modal-bg').click();

                    if(msg.validate){

                    }else{

                    }
                }
            });
        });
    });
</script>