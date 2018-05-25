<style type="text/css">
    .tabla{
        border-spacing: 15px
    }

    .tabla tr td ul li{
        list-style: square;
    }

    .preview{
        max-height: 300px;
        overflow-y: scroll;
        width: 900px;
    }

    .preview img{
        float: left;
        margin: 0 10px 10px 0;
    }

    .alerta{
        font-weight: normal;
        margin: 0;
        text-align: left;
    }
</style>

<table class="tabla">
    <tr>
        <td>Anunciante:</td>
        <td><b><?= $publinota->name_anunciante ?></b></td>
    </tr>

    <tr>
        <td style="width:160px;">Nombre de la Campa&ntilde;a:</td>
        <td><b><?= $publinota->nombre ?></b></td>
    </tr>

    <tr>
        <td>Periodo:</td>
        <td>Desde el <b><?= $publinota->fecha_inicio ?></b> al <b><?= $publinota->fecha_fin ?></b></td>
    </tr>

    <?php
    if($publinota->tipo == 'SITIOS'){
    ?>

    <tr>
        <td valign="top">Sitios seleccionados:</td>
        <td>
            <ul style="margin-left: 16px">
                <?php
                $cantidad_aceptados = 0;

                foreach ($sitios as $site) {
                ?>
                    <li>
                        <a href="http://<?= $site['nombre'] ?>" target="_BLANK"><?= $site['nombre'] ?></a>
                <?php
                    $mostrar_botones = FALSE;

                    if($publinota->alta_finalizada == 1){
                        $estado = '';

                        switch ($site['estado']) {
                            case 'P':
                            case 'p':
                                $estado = 'Pendiente';
                                break;

                            case 'A':
                            case 'a':
                                $estado = 'Aceptada por el sitio';

                                $cantidad_aceptados += 1;

                                $mostrar_botones = TRUE;

                                break;

                            case 'R':
                            case 'r':
                                $estado = 'Rechazada por el sitio';
                                break;

                            default:
                                $estado = 'Pendiente';
                                break;
                        }

                        if($site['url'] != ''){
                            echo ' ( ' . $estado . ' - ' . $site['url'] . ' )';
                        }else{
                            echo ' ( ' . $estado . ' )';
                        }

                        if($site['estado_anunciante'] != 'P')
                            $mostrar_botones = FALSE;

                        $publinota->precio_total = $publinota->precio_total * $cantidad_aceptados;
                    }
                ?>
                        <?php
                        if($mostrar_botones){
                        ?>
                            <span id="buttons_aceptar_rechazar_<?= $site['id'] ?>">
                                <a href="javascript:;" class="btn_aceptar_sitio" id="<?= $site['id'] ?>">Aceptar</a> -
                                <a href="javascript:;" class="btn_rechazar_sitio" id="<?= $site['id'] ?>">Rechazar</a>
                            </span>

                            <span id="loader_aceptar_rechazar_<?= $site['id'] ?>" style="display:none;">
                                <img src="/images/ajax-loader.gif" height="10px" /> Aguarde por favor...
                            </span>
                        <?php
                        }
                        ?>

                        <span id="estado_aceptar_rechazar_<?= $site['id'] ?>">
                        <?php
                        $estado_anunciante = '';
                        if($site['estado_anunciante'] != 'P'){
                            switch ($site['estado_anunciante']) {
                                case 'A':
                                case 'a':
                                    $estado_anunciante = 'Aceptada por el anunciante';

                                    $cantidad_aceptados += 1;

                                    $mostrar_botones = TRUE;

                                    break;

                                case 'R':
                                case 'r':
                                    $estado_anunciante = 'Rechazada por el anunciante';
                                    break;
                            }

                            echo '( ' . $estado_anunciante . ' )';
                        }
                        ?>
                        </span>
                    </li>
                <?php
                }
                ?>
            </ul>
        </td>
    </tr>

    <?php
    }
   
    if($publinota->tipo == 'EMBAJADORAS' || $publinota->tipo == 'MENCION_EMBAJADORAS'){
    ?>
        <tr>
            <td>Nombre de la Embajadora:</td>
            <td><b><?= $publinota->nombre_embajadora ?></b></td>
        </tr>

    <?php
    }if($publinota->tipo == 'MENCION_SITIOS'){
    ?>
        <tr>
            <td>URL del Sitio:</td>
            <td><b><?= $publinota->url_sitio ?></b></td>
        </tr>

    <?php
    }
    ?>


    <tr>
        <td>Inversi&oacute;n neta por unidad:</td>
        <td><b><?= $publinota->precio . ' ' . $publinota->moneda ?></b></td>
    </tr>

    <?php
    if($publinota->tipo == 'SITIOS'){
    ?>
    <tr>
        <td>Inversi&oacute;n neta total:</td>
        <td>
            <b><?= $publinota_precio_total . ' ' . $publinota->moneda; ?></b> 
             (La orden de compra no va a ser facturada en su totalidad si existen sitios que no acepten la publinota.)
        </td>
    </tr>
    <?php
    }
    ?>

    <tr>
        <td>Otros:</td>
        <td><b><?= $publinota->otros ?></b></td>
    </tr>

    <tr>
        <td colspan="2">Vista previa:</td>
    </tr>

    <tr>
        <td>&nbsp;</td>
        <td>
            <div class="preview">
                <h2><?= $publinota->titulo ?></h2>
                <div>
                    <img src="/creatividades/publinotas/<?= $publinota->imagen ?>" alt="<?= $publinota->titulo ?>" />
                    <?= $publinota->Contenido ?>
                </div>
            </div>
        </td>
    </tr>
</table>

<script>
    $().ready(function(){
        $('.btn_aceptar_sitio').click(function(){
            var id = $(this).attr('id');

            $('#buttons_aceptar_rechazar_' + id).css('display', 'none');
            $('#loader_aceptar_rechazar_' + id).css('display', 'inline');

            var form_data = {
                id_sitio: id,
                id_publinota: <?= $publinota->Id ?>
            };

            $.ajax({
                type: "POST",
                url: "/campania/aceptar_publinota/",
                data: form_data,
                dataType: "json",
                success: function(){
                    $('#loader_aceptar_rechazar_' + id).css('display', 'none');
                    $('#estado_aceptar_rechazar_' + id).html('( Aceptada por el anunciante )');

                    $('#saldo_disponible').load('/micuenta/get_saldo_disponible',function(){
                        $("#saldo_disponible").animate({ color: '#ff0000' }, 'slow', function(){
                            $("#saldo_disponible").animate({ color: '#fff' }, 'slow');
                        });
                    });
                }
            });
        });

        $('.btn_rechazar_sitio').click(function(){
            var id = $(this).attr('id');

            $('#buttons_aceptar_rechazar_' + id).css('display', 'none');
            $('#loader_aceptar_rechazar_' + id).css('display', 'inline');

            var form_data = {
                id_sitio: id,
                id_publinota: <?= $publinota->Id ?>
            };

            $.ajax({
                type: "POST",
                url: "/campania/rechazar_publinota/",
                data: form_data,
                dataType: "json",
                success: function(){
                    $('#loader_aceptar_rechazar_' + id).css('display', 'none');
                    $('#estado_aceptar_rechazar_' + id).html('( Rechazada por el anunciante )');
                }
            });
        });
    });
</script>