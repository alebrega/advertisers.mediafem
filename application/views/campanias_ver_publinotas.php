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
                    echo '<li>';
                    echo '<a href="http://' . $site['nombre'] . '" target="_BLANK"> ' . $site['nombre'] . '</a>';

                    if($publinota->alta_finalizada == 1){
                        $estado = '';

                        switch ($site['estado']) {
                            case 'P':
                            case 'p':
                                $estado = 'Pendiente';
                                break;

                            case 'A':
                            case 'a':
                                $estado = 'Aceptada';

                                $cantidad_aceptados += 1;

                                break;

                            case 'R':
                            case 'r':
                                $estado = 'Rechazada';
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

                        $publinota->precio_total = $publinota->precio_total * $cantidad_aceptados;
                    }

                    echo '</li>';
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