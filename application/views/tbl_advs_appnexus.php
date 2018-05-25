<tr>
    <td id="prueba"></td>
</tr>
<tr class="encabezado_celeste">
    <td colspan="5">Anunciantes Adservers de <b><?= $anunciante->username ?></b></td>
</tr>
<tr class="encabezado">
    <td style="width:30%">Nombre</td>
    <td style="width:20%">Adserver</td>
    <td style="width:20%">Fecha alta</td>
    <td style="width:15%">&nbsp;</td>
    <td style="width:15%">&nbsp;</td>
</tr>
<?php
foreach ($anunciante_adserver as $row) {
    ?>
    <tr>
        <td><?= $row['nombre'] ?></td>
        <td><?= $row['adserver'] ?></td>
        <td><?= $row['fecha_alta'] ?></td>
        <td>
            <a href="/advertisers" id="<?= $row['id'] ?>" class="link_ver_2" >Ver</a>
            <div id="loader_ver_<?= $row['id'] ?>" style="display:none"><img height="10px" alt="agregar" src="/images/ajax-loader.gif" /></div>
        </td>
        <td>
            <a href="/advertisers" id="<?= $row['id'] ?>" name="<?= $id_anunciante ?>" class="link_eliminar" >Eliminar</a>
            <div id="loader_eliminar_<?= $row['id'] ?>" style="display:none"><img height="10px" alt="agregar" src="/images/ajax-loader.gif" /></div>
        </td>
    </tr>
    <?php
}
?>
<tr>
    <td>&nbsp;</td>
</tr>
<tr class="encabezado_celeste">
    <td colspan="5"><b>Modificar</b></td>
</tr>
<tr class="encabezado">
    <td colspan="5">Datos de la cuenta</td>
</tr>
<tr>
    <td>Casilla de correo:</td>
    <td colspan="4"><input type="text" size="30" id="correo_anunciante_mod" name="correo_anunciante_mod" value="<?= $anunciante->email ?>" /></td>
</tr>
<tr class="encabezado">
    <td colspan="5">Avanzadas</td>
</tr>
<?php
$checked_agencia = "";
$checked_agrupor_por_sitio = "";
$modificar_duplicar_campanias = "";
$checked_habilitar_descuentos = "";
$checked_mostrar_filtro_pais = "";
$value_chk_limite_de_compra = "";
$limite_de_compra_disable = "";
$checked_representante_oficial = "";

if ($anunciante->agencia)
    $checked_agencia = "checked";
if ($anunciante->agrupar_por_sitio)
    $checked_agrupor_por_sitio = "checked";
if ($anunciante->agrupar_por_sitio)
    $checked_agrupor_por_sitio = "checked";
if ($anunciante->modificar_duplicar_campanias)
    $modificar_duplicar_campanias = "checked";
if ($anunciante->habilitar_descuentos)
    $checked_habilitar_descuentos = "checked";
if ($anunciante->mostrar_filtro_pais)
    $checked_mostrar_filtro_pais = "checked";
if ($anunciante->representante_oficial)
    $checked_representante_oficial = "checked";

if ($anunciante->limite_de_compra > 0) {
    $checked_limite_de_compra = "checked";
    $limite_de_compra_disable = "disabled";
    $value_chk_limite_de_compra = 1;
} else {
    $value_chk_limite_de_compra = 0;
}
?>
<tr>
    <td><input type="checkbox" <?= $checked_agencia ?> name="chk_agencia_mod" id="chk_agencia_mod" value="<?= $anunciante->agencia ?>" />Ocultar columna Costo</td>
</tr>
<tr>
    <td><input type="checkbox" <?= $checked_agrupor_por_sitio ?> name="chk_agrupar_por_sitio_mod" id="chk_agrupar_por_sitio_mod" value="<?= $anunciante->agrupar_por_sitio ?>" />Permitir agrupar por sitio</td>
</tr>
<tr>
    <td><input type="checkbox" <?= $checked_habilitar_descuentos ?> name="chk_habilitar_descuentos_mod" id="chk_habilitar_descuentos_mod" value="<?= $anunciante->habilitar_descuentos ?>" />Habilitar opciones de descuentos</td>
</tr>
<tr>
    <td><input type="checkbox" <?= $checked_mostrar_filtro_pais ?> name="chk_mostrar_filtro_pais" id="chk_mostrar_filtro_pais" value="<?= $anunciante->mostrar_filtro_pais ?>" />Habilitar filtro por paises</td>
</tr>
<tr>
    <td><input type="checkbox" <?= $modificar_duplicar_campanias ?> name="chk_modificar_duplicar_campanias" id="chk_modificar_duplicar_campanias" value="<?= $anunciante->modificar_duplicar_campanias ?>" />Habilitar modificar y duplicar campa&ntilde;as</td>
</tr>
<tr>
    <td><input type="checkbox" <?= $checked_representante_oficial ?> name="checked_representante_oficial" id="checked_representante_oficial" value="<?= $anunciante->representante_oficial ?>" />Marcar usuario como representante oficial.</td>
</tr>
<tr class="encabezado">
    <td colspan="5">L&iacute;mite de compra</td>
</tr>
<tr>
    <td colspan="5">
        <input type="checkbox" <?= $checked_limite_de_compra ?> name="chk_limite_de_compra_mod" id="chk_limite_de_compra_mod" value="<?= $value_chk_limite_de_compra ?>" />Habilitar l&iacute;mite de compra (No necesita tarjeta de cr&eacute;dito para correr campa&ntilde;as)
    </td>
</tr>
<tr>
    <td>
        <input type="text" name="limite_de_compra_mod" id="limite_de_compra_mod" value="<?= $anunciante->limite_de_compra ?>" />
    </td>
</tr>
<tr>
    <td>&nbsp;</td>
</tr>
<tr>
    <td colspan="5">
        <input type="button" id="btn_actualizar" value="Actualizar" class="button_new"/>
        <img height="10px" style="display: none" id="loader_btn_actualizar" alt="agregar" src="/images/ajax-loader.gif" />
        <label  id="lbl_mensaje_actualizar" class="error"></label>
    </td>
</tr>
<input type="hidden" id="id_anunciante_mod" name="id_anunciante_mod" value="<?= $id_anunciante ?>" />
<input type="hidden" id="es_agencia_mod" name="agencia_mod" value="<?= $anunciante->agencia ?>" />
<input type="hidden" id="modificar_duplicar_campanias_mod" name="modificar_duplicar_campanias_mod" value="<?= $anunciante->modificar_duplicar_campanias ?>" />
<input type="hidden" id="habilitar_descuentos_mod" name="habilitar_descuentos_mod" value="<?= $anunciante->habilitar_descuentos ?>" />
<input type="hidden" id="agrupar_por_sitio_mod" name="agrupar_por_sitio_mod" value="<?= $anunciante->agrupar_por_sitio ?>" />
<input type="hidden" id="habilitar_limite_de_compra_mod" name="habilitar_limite_de_compra_mod" value="<?= $value_chk_limite_de_compra ?>" />
<input type="hidden" id="mostrar_filtro_pais" name="mostrar_filtro_pais" value="<?= $anunciante->mostrar_filtro_pais ?>" />
<input type="hidden" id="limite_de_compra_default" name="limite_de_compra_default" value ="<?= $anunciante->limite_de_compra ?>" />
<input type="hidden" id="representante_oficial" name="representante_oficial" value ="<?= $anunciante->limite_de_compra ?>" />
<input type="hidden" id="nombre_anunciante" name="nombre_anunciante" value ="<?= $row['nombre'] ?>" />

<script type="text/javascript">
    $().ready(function() {
        $(".link_eliminar").click(function(event){
            var id_adv_appnexus = $(this).attr('id');
            var id_adv_redvlog = $(this).attr('name');
            event.preventDefault();
            eliminar_adv_asociado(id_adv_redvlog, id_adv_appnexus);
        });

        $(".link_ver_2").click(function(event){
            event.preventDefault();

            var id = $(this).attr('id');

            var now = new Date();
            var seconds = now.getSeconds()+"o"+now.getMinutes();

            $("#loader_ver_"+id).css("display", "inline");

            $("#tbl_modif_adv_appnexus").load('/advertisers/get_modificacion_adv/'+id+'/'+seconds, function(){
                irA("tbl_modif_adv_appnexus");
                $("#loader_ver_"+id).css("display", "none");
            });
        });

        $("#chk_agencia_mod").change(function(){
            var thisCheck = $(this);
            if(thisCheck.is(':checked'))
            {
                $("#es_agencia_mod").attr('value', '1');
            }else{
                $("#es_agencia_mod").attr('value', '0');
            }
        });

        $("#checked_representante_oficial").change(function(){
            var thisCheck = $(this);
            if(thisCheck.is(':checked'))
            {
                $("#representante_oficial").attr('value', '1');
            }else{
                $("#representante_oficial").attr('value', '0');
            }
        });

        $("#chk_modificar_duplicar_campanias").change(function(){
            var thisCheck = $(this);
            if(thisCheck.is(':checked'))
            {
                $("#modificar_duplicar_campanias_mod").attr('value', '1');
            }else{
                $("#modificar_duplicar_campanias_mod").attr('value', '0');
            }
        });

        $("#chk_limite_de_compra_mod").change(function(){
            var thisCheck = $(this);
            if(thisCheck.is(':checked'))
            {
                $("#habilitar_limite_de_compra_mod").attr('value', '1');
            }else{
                $("#habilitar_limite_de_compra_mod").attr('value', '0');
            }
        });

        $("#chk_agrupar_por_sitio_mod").change(function(){
            var thisCheck = $(this);
            if(thisCheck.is(':checked'))
            {
                $("#agrupar_por_sitio_mod").attr('value', '1');
            }else{
                $("#agrupar_por_sitio_mod").attr('value', '0');
            }
        });

         $("#chk_habilitar_descuentos_mod").change(function(){
            var thisCheck = $(this);
            if(thisCheck.is(':checked'))
            {
                $("#habilitar_descuentos_mod").attr('value', '1');
            }else{
                $("#habilitar_descuentos_mod").attr('value', '0');
            }
        });

        $("#chk_mostrar_filtro_pais").change(function(){
            var thisCheck = $(this);
            if(thisCheck.is(':checked'))
            {
                $("#mostrar_filtro_pais").attr('value', '1');
            }else{
                $("#mostrar_filtro_pais").attr('value', '0');
            }
        });

        $("#btn_actualizar").click(function(event){
            event.preventDefault();
            var id = $('#id_anunciante_mod').val();

            var email =  $('#correo_anunciante_mod').val();
            var agencia =  $('#es_agencia_mod').val();
            var modificar_duplicar_campanias =  $('#modificar_duplicar_campanias_mod').val();
            var agrupar_por_sitio =  $('#agrupar_por_sitio_mod').val();
            var habilitar_limite_de_compra =  $('#habilitar_limite_de_compra_mod').val();
            var limite_de_compra =  $('#limite_de_compra_mod').val();
            var habilitar_descuentos =  $('#habilitar_descuentos_mod').val();
            var limite_de_compra_default =  $('#limite_compra_default').val();
            var mostrar_filtro_pais =  $('#mostrar_filtro_pais').val();
            var representante_oficial =  $('#representante_oficial').val();

            var form_data = {
                id_anunciante: id,
                email: email,
                agencia: agencia,
                modificar_duplicar_campanias: modificar_duplicar_campanias,
                agrupar_por_sitio: agrupar_por_sitio,
                habilitar_limite_de_compra : habilitar_limite_de_compra,
                limite_de_compra: limite_de_compra,
                habilitar_descuentos : habilitar_descuentos,
                limite_de_compra_default : limite_de_compra_default,
                mostrar_filtro_pais : mostrar_filtro_pais,
                representante_oficial : representante_oficial
            };

            $("#loader_btn_actualizar").css("display", "inline");

            $.ajax({
                type: "POST",
                url: "/advertisers/actualizar_anunciante/",
                data: form_data,
                dataType: "json",
                success: function(msg){
                    if(msg.validate)
                    {
                        $("#lbl_mensaje_actualizar").html("Se ha actualizado correctamente");
                        $("#loader_btn_actualizar").css("display", "none");
                        setTimeout(function(){
                            $("#lbl_mensaje_actualizar").fadeOut("fast");

                            var now = new Date();
                            var seconds = now.getSeconds()+'o'+now.getMinutes();
                            $("#tbl_advs_appnexus").load('/advertisers/get_data_by_id/'+id+'/'+seconds, function(){
                            });

                        }, 2000);
                    }
                    else
                    {
                        $("#lbl_mensaje_actualizar").html("Ha ocurrido un error, o no ha realizado ningún cambio");
                        $("#loader_btn_actualizar").css("display", "none");
                        setTimeout(function(){
                            $("#lbl_mensaje_actualizar").fadeOut("fast");
                        }, 2000);
                    }
                }
            });
        });

    });

    function eliminar_adv_asociado(id_adv_redvlog, id_adv_appnexus)
    {
        ///alert("#loader_eliminar_"+id_adv_appnexus);
         var nombre_del_anunciante =  $('#nombre_anunciante').val();
       var ventana=confirm("Desea eliminarghjgjghjghjghjghjghjghjghjghjg el Anunciante "+nombre_del_anunciante+"?");

        var now = new Date();
        var seconds = now.getSeconds()+"o"+now.getMinutes();

        if (ventana) {
            $("#loader_eliminar_"+id_adv_appnexus).css("display", "inline");
            $("#prueba").load('/advertisers/delete_anunciante_asociado/'+id_adv_redvlog+'/'+id_adv_appnexus+'/'+seconds, function(){
                $("#tbl_advs_appnexus").load('/advertisers/get_data_by_id/'+id_adv_redvlog+'/'+seconds, function(){
                    $("#loader_eliminar_"+id_adv_appnexus).css("display", "none");
                });
            });
        }

    }

</script>