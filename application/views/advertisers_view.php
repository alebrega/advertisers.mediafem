<?php
header("Cache-Control: no-store, no-cache, must-revalidate");
?>
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
        </style>

        <script src="/js/cache.js" type="text/javascript"></script>

        <script type="text/javascript">

            jQuery.fn.dataTableExt.oSort['uk_date-asc'] = function(a, b) {
                var ukDatea = a.split('/');
                var ukDateb = b.split('/');

                var x = (ukDatea[2] + ukDatea[1] + ukDatea[0]) * 1;
                var y = (ukDateb[2] + ukDateb[1] + ukDateb[0]) * 1;

                return ((x < y) ? -1 : ((x > y) ? 1 : 0));
            };

            jQuery.fn.dataTableExt.oSort['uk_date-desc'] = function(a, b) {
                var ukDatea = a.split('/');
                var ukDateb = b.split('/');

                var x = (ukDatea[2] + ukDatea[1] + ukDatea[0]) * 1;
                var y = (ukDateb[2] + ukDateb[1] + ukDateb[0]) * 1;

                return ((x < y) ? 1 : ((x > y) ? -1 : 0));
            };

            jQuery.fn.dataTableExt.oSort['slo-asc'] = function(a, b) {
                var x = (a == "-") ? 0 : a.replace(/\./g, "").replace(/,/, ".");
                var y = (b == "-") ? 0 : b.replace(/\./g, "").replace(/,/, ".");
                x = parseFloat(x);
                y = parseFloat(y);
                return ((x < y) ? -1 : ((x > y) ? 1 : 0));
            };

            jQuery.fn.dataTableExt.oSort['slo-desc'] = function(a, b) {
                var x = (a == "-") ? 0 : a.replace(/\./g, "").replace(/,/, ".");
                var y = (b == "-") ? 0 : b.replace(/\./g, "").replace(/,/, ".");
                x = parseFloat(x);
                y = parseFloat(y);
                return ((x < y) ? 1 : ((x > y) ? -1 : 0));
            };

            function reemplazar(texto, s1, s2) {
                return texto.split(s1).join(s2);
            }

            $().ready(function() {

                // Store in cache - Returns current object
                $("#mainNav").cache("main_navigation");
                // Retrieve from cache - Returns cached object
                $$("main_navigation"); // or jQueryCache("main_navigation");
                // Remove from cache
                $$.remove("main_navigation");
                // Clear Cache
                $$.clear();
                // Load jQueryCache with noConflict to avoid overriding window.$$
                $$.noConflict();

                var now = new Date()
                var seconds = now.getSeconds() + 'o' + now.getMinutes();

                $('#actualizar_listado_adserver').attr('disabled', 'disabled');

                $("#loader_tabla").css("display", "inline");
                $("#loader_div_anunciantes_adservers").css("display", "inline");

                $("#div_advertisers").load('/advertisers/get_advertisers/' + seconds, function() {
                    $("#loader_tabla").css("display", "none");
                });

                $("#div_advertisers_appnexus").load('/advertisers/get_advertisers_adserver/' + seconds, function() {
                    $("#loader_div_anunciantes_adservers").css("display", "none");
                    $('#actualizar_listado_adserver').attr('disabled', '');
                });

                $('#actualizar_listado_adserver').click(function() {
                    $('#actualizar_listado_adserver').attr('disabled', 'disabled');
                    $('#loader_actualizar_listado_adserver').css('display', 'inline');
                    $('#div_advertisers_appnexus').html(' ');

                    $("#div_advertisers_appnexus").load('/advertisers/actualizar_advertisers_adserver/', function() {
                        $("#div_advertisers_appnexus").load('/advertisers/get_advertisers_adserver/' + seconds, function() {
                            $('#actualizar_listado_adserver').attr('disabled', '');
                            $('#loader_actualizar_listado_adserver').css("display", "none");
                        });
                    });
                });

                /*
                 $("#load_by_name").click(function(event){
                 event.preventDefault();
                 $("#loader_x_name").css("display", "inline");
                 var name = fixedEncodeURIComponent($("#query_name").val());
                 name = reemplazar(name, "%", "_");
                 now = new Date()
                 var seconds = now.getSeconds()+'o'+now.getMinutes();
                 if(name==""){
                 alert('Debe escribir el nombre del Anunciante');
                 $("#loader_x_name").css("display", "none");
                 }else{
                 $("#tbl_advs_appnexus").load('/advertisers/get_data_by_name/'+name+'/'+seconds, function(){
                 $("#loader_x_name").css("display", "none");
                 });
                 }
                 });
                 */

                $(".link_ver").click(function(event) {
                    event.preventDefault();
                    var id = $(this).attr('id');
                    $("#loader_" + id).css("display", "inline");
                    now = new Date()
                    var seconds = now.getSeconds() + 'o' + now.getMinutes();

                    $("#tbl_advs_appnexus").load('/advertisers/get_data_by_id/' + id + '/' + seconds, function() {
                        $("#loader_" + id).css("display", "none");
                    });

                });

                $("#load_by_name_appnexus").click(function(event) {
                    event.preventDefault();

                    var id = $("#id_adv_appnexus").val();

                    var now = new Date()
                    var seconds = now.getSeconds() + "o" + now.getMinutes();

                    $("#loader_x_name_appnexus").css("display", "inline");

                    $("#tbl_modif_adv_appnexus").load('/advertisers/get_modificacion_adv/' + id + '/' + seconds, function() {
                        $("#loader_x_name_appnexus").css("display", "none");
                    });

                });

                $("#form_alta").validate({
                    rules: {
                        'username_anunciante': {required: true, remote: {
                                url: '/advertisers/check_user',
                                type: 'post'
                            }},
                        'nombre_anunciante': {required: true},
                        'passField': {required: true},
                        /*'ids': { required: true},*/
                        'correo_anunciante': {required: true, email: true}
                    },
                    messages: {
                        'username_anunciante': {
                            required: "Ingrese el nombre de usuario",
                            remote: "El usuario ya existe"
                        },
                        'nombre_anunciante': {
                            required: "Ingrese el nombre del Anunciante"
                        },
                        'passField': {
                            required: "Debe generar una contrase&ntilde;a"
                        }, /*
                         'ids':{
                         required:"Debe seleccionar Anunciantes de los Adservers"
                         },*/
                        'correo_anunciante': {
                            required: "Ingrese la casilla de correo del Anunciante"
                        }
                    },
                    debug: true,
                    errorElement: "label",
                    submitHandler: function(form) {
                        form.submit();
                    }
                });

                $("#chk_agencia").change(function() {
                    var thisCheck = $(this);
                    if (thisCheck.is(':checked'))
                    {
                        $("#es_agencia").attr('value', '1');
                    } else {
                        $("#es_agencia").attr('value', '0');
                    }
                });

                $("#chk_limite_de_compra").change(function() {
                    var thisCheck = $(this);
                    if (thisCheck.is(':checked'))
                    {
                        $("#habilitar_limite_de_compra").attr('value', '1');
                    } else {
                        $("#habilitar_limite_de_compra").attr('value', '0');
                    }
                });

                $("#chk_agrupar_por_sitio").change(function() {
                    var thisCheck = $(this);
                    if (thisCheck.is(':checked'))
                    {
                        $("#agrupar_por_sitio").attr('value', '1');
                    } else {
                        $("#agrupar_por_sitio").attr('value', '0');
                    }
                });

                $("#chk_habilitar_descuentos").change(function() {
                    var thisCheck = $(this);
                    if (thisCheck.is(':checked'))
                    {
                        $("#habilitar_descuentos").attr('value', '1');
                    } else {
                        $("#habilitar_descuentos").attr('value', '0');
                    }
                });
            });

            function GeneratePassword() {

                if (parseInt(navigator.appVersion) <= 3) {
                    alert("Sorry this only works in 4.0+ browsers");
                    return true;
                }

                var length = 8;
                var sPassword = "";
                //length = document.aForm.charLen.options[document.aForm.charLen.selectedIndex].value;
                //length = document.aForm.charLen.options[document.aForm.charLen.selectedIndex].value;

                var noPunction = true;
                var randomLength = false;

                if (randomLength) {
                    length = Math.random();

                    length = parseInt(length * 100);
                    length = (length % 7) + 6
                }


                for (i = 0; i < length; i++) {

                    numI = getRandomNum();
                    if (noPunction) {
                        while (checkPunc(numI)) {
                            numI = getRandomNum();
                        }
                    }

                    sPassword = sPassword + String.fromCharCode(numI);
                }

                document.getElementById("passField").value = sPassword;
                return true;
            }

            function getRandomNum() {

                // between 0 - 1
                var rndNum = Math.random()

                // rndNum from 0 - 1000
                rndNum = parseInt(rndNum * 1000);

                // rndNum from 33 - 127
                rndNum = (rndNum % 94) + 33;

                return rndNum;
            }

            function checkPunc(num) {

                if ((num >= 33) && (num <= 47)) {
                    return true;
                }
                if ((num >= 58) && (num <= 64)) {
                    return true;
                }
                if ((num >= 91) && (num <= 96)) {
                    return true;
                }
                if ((num >= 123) && (num <= 126)) {
                    return true;
                }

                return false;
            }

        </script>
        <?php require_once BASEPATH . '/application/views/analytics.html'; ?>
    </head>

    <body>
        <?php
        require_once BASEPATH . '/application/views/top.php';
        ?>

        <input type="hidden" id="id_adv_appnexus" name="id_adv_appnexus" value=""/>
        <input type="hidden" id="id_adv_appnexus_2" name="id_adv_appnexus_2" value=""/>

        <?php
        if (isset($mensaje)) {
            ?>
            <table class="tabla">
                <tr>
                    <td style="color:green"><?= $mensaje ?></td>
                </tr>
            </table>
            <?php
        }
        ?>

        <table class="tabla">
            <tr class="encabezado">
                <td colspan="4">Anunciantes MediaFem</td>
            </tr>
        </table>

        <div class="div_tabla" id="div_advertisers"></div>

        <table class="tabla" id="tbl_advs_appnexus"></table>

        <table class="tabla" >
            <tr>
                <td id="tbl_advs_appnexus1"></td>
            </tr>    
        </table>
        
        <table class="tabla">
            <tr class="encabezado">
                <td>Anunciantes Adservers</td>
            </tr>
            <tr>
                <td>
                    <input type="button" name="actualizar_listado_adserver" id="actualizar_listado_adserver" value="Actualizar listado" class="button_new" />
                    <img id="loader_actualizar_listado_adserver" alt="cargando..." height="10px" src="/images/ajax-loader.gif" style="display: none;" />

                    <div id="loader_div_anunciantes_adservers" style="display:none">
                        <img alt="agregar" height="10px" src="/images/ajax-loader.gif" />
                    </div>
                </td>
            </tr>
        </table>

        <div class="div_tabla" id="div_advertisers_appnexus"></div>

        <table class="tabla" id="tbl_modif_adv_appnexus"></table>

        <form id="form_alta" action="/advertisers/add" method="post">
            <input type="hidden" name="ids" id="ids" value="" />
            <input type="hidden" name="es_agencia" id="es_agencia" value="0"/>
            <input type="hidden" name="habilitar_descuentos" id="habilitar_descuentos" value="0"/>
            <input type="hidden" name="agrupar_por_sitio" id="agrupar_por_sitio" value="1"/>
            <input type="hidden" name="habilitar_limite_de_compra" id="habilitar_limite_de_compra" value="0"/>

            <table id="tbl_crear_anunciante" class="tabla">
                <tr class="encabezado_celeste">
                    <td colspan="2">Crear Anunciante MediaFem</td>
                </tr>
                <tr class="encabezado">
                    <td style="width: 40%;">Anunciantes Adservers Asociados</td>
                    <td style="width: 60%;">Acciones</td>
                </tr>
                <tr>
                    <td colspan="2">
                        <label for="ids" generated="true" class="error" style="padding-left: 0px;"></label>
                    </td>
                </tr>
                <table id="tbl_crear_anunciante" class="tabla">
                    <tr class="encabezado">
                        <td colspan="2">Nombre de Anunciante</td>
                    </tr>
                    <tr>
                        <td colspan="2"><input type="text" size="30" id="nombre_anunciante" name="nombre_anunciante" value="" /></td>
                    </tr>
                    <tr class="encabezado">
                        <td colspan="2">Usuario</td>
                    </tr>
                    <tr>
                        <td colspan="2"><input type="text" size="30" id="username_anunciante" name="username_anunciante" value="" /></td>
                    </tr>
                    <tr class="encabezado">
                        <td colspan="2">Avanzadas</td>
                    </tr>
                    <tr>
                        <td colspan="2"><input type="checkbox" name="chk_agencia" id="chk_agencia" value="0" />Es agencia?</td>
                    </tr>
                    <tr>
                        <td colspan="2"><input checked type="checkbox" name="chk_agrupar_por_sitio" id="chk_agrupar_por_sitio" value="1" />Permitir agrupar por sitio</td>
                    </tr>
                    <tr>
                        <td colspan="2"><input type="checkbox" name="chk_habilitar_descuentos" id="chk_habilitar_descuentos" value="0" />Habilitar opciones de descuentos</td>
                    </tr>
                    <tr class="encabezado">
                        <td colspan="2">L&iacute;mite de compra</td>
                    </tr>
                    <tr>
                        <td colspan="2"><input type="checkbox" name="chk_limite_de_compra" id="chk_limite_de_compra" value="0" />Habilitar l&iacute;mite de compra (No necesita tarjeta de cr&eacute;dito para correr campa&ntilde;as)</td>
                    </tr>
                    <tr>
                        <td colspan="2"><input type="text" name="limite_de_compra" id="limite_de_compra" value="<?= $limite_de_compra ?>" /></td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr class="encabezado">
                        <td colspan="2">Contrase&ntilde;a</td>
                    </tr>
                    <tr>
                        <td><input type="text" size="15" value="" name="passField" id="passField"/></td>
                        <td><input type="button" onclick="GeneratePassword()" value=" Generar Contrase&ntilde;a"/></td>
                    </tr>
                    <tr class="encabezado">
                        <td colspan="2">Casilla de correo</td>
                    </tr>
                    <tr>
                        <td colspan="2"><input type="text" size="30" id="correo_anunciante" name="correo_anunciante" value="" /></td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align: center"><input type="submit" value="Guardar" /></td>
                    </tr>
                </table>
            </table>
        </form>

        <?php
        require_once BASEPATH . '/application/views/footer.html';
        ?>
    </body>
</html>