<style type="text/css" title="currentStyle">
    .tabla{
        border-spacing: 15px
    }

    #mostrar_sitios_div, #mostrar_canales_div{
        margin:10px 0px;
        max-height:100px;
        overflow-y:scroll;
        width:422px;
        border:1px solid #ccc;
        margin:10px 25px 0;
        display:none;
    }
</style>

<script type="text/javascript">
    $().ready(function() {
        $('#mostrar_canales').click(function() {
            if ($(this).attr('title') == 'Mostrar') {
                $('#mostrar_canales_div').slideDown('slow');
                $(this).attr('title', 'Ocultar');
                $(this).html('ocultar');
            } else {
                $('#mostrar_canales_div').slideUp('slow');
                $(this).attr('title', 'Mostrar');
                $(this).html('mostrar');
            }
        });

        $('#mostrar_sitios').click(function() {
            if ($(this).attr('title') == 'Mostrar') {
                $('#mostrar_sitios_div').slideDown('slow');
                $(this).attr('title', 'Ocultar');
                $(this).html('ocultar');
            } else {
                $('#mostrar_sitios_div').slideUp('slow');
                $(this).attr('title', 'Mostrar');
                $(this).html('mostrar');
            }
        });
    });
</script>


<?php if ($mostrar_exportar) { ?>
    <div class="button_bar">
        <a href="campania/exportar_orden_PDF/<?= $campania->id ?>" id="exportar_PDF" class="button_new">
            <img src="images/icon_pdf2.png" height="10" /> Exportar a PDF
        </a>
    </div>
<?php } ?>

<table class="tabla">
    <?php if ($campania_padre) { ?>
        <tr>
            <td>Campa&ntilde;a unificada con:</td>
            <td><b><?= $campania_padre ?></b></td>
        </tr>
    <?php } ?>

        <tr>
            <td>Cliente:</td>
            <td><b><?= $cliente_nombre ?></b></td>
        </tr>

    <tr>
        <td>Anunciante:</td>
        <td><b><?= $anunciante_nombre ?></b></td>
    </tr>

    <tr>
        <td style="width:160px;">Nombre de la Campa&ntilde;a:</td>
        <td><b><?= $nombre_campania ?></b></td>
    </tr>

    <?php if ($fecha_inicio != '' && $fecha_fin != '') { ?>
        <tr>
            <td>Periodo:</td>
            <td>Desde el <b><?= $fecha_inicio ?></b> al <b><?= $fecha_fin ?></b></td>
        </tr>
    <?php } ?>

    <?php if (isset($frecuencia)) { ?>
        <tr>
            <td>Rotaci&oacute;n de anuncios:</td>
            <td><b><?= $frecuencia ?></b></td>
        </tr>
    <?php } ?>

    <?php if ($paises != '') { ?>
        <tr>
            <td>Paises:</td>
            <td><b><?= $paises ?></b></td>
        </tr>
    <?php } ?>

    <?php if ($campania->empresa_campania == 0 && $segmentacion_id > 0) { ?>
        <tr>
            <td valign="top">Segmentaci&oacute;n:</td>
            <td>
                <b><?= $segmentacion ?></b>
                <?php
                if ($segmentacion_id == 3) {
                    ?>
                    ( <a href="javascript:;" id="mostrar_sitios" title="Mostrar">mostrar</a> )
                    <div id="mostrar_sitios_div">
                        <table>
                            <?php
                            foreach ($sitios as $sitio) {
                                echo '<tr><td style="padding:5px 10px;">' . $sitio['nombre'] . '</td></tr>';
                            }
                            ?>
                        </table>
                    </div>
                    <?php
                } else if ($segmentacion_id == 2) {
                    ?>
                    ( <a href="javascript:;" id="mostrar_canales" title="Mostrar">mostrar</a> )
                    <div id="mostrar_canales_div">
                        <table>
                            <?php
                            foreach ($canales_tematicos as $canal) {
                                echo '<tr><td style="padding:5px 10px;">' . $canal['name'] . '</td></tr>';
                            }
                            ?>
                        </table>
                    </div>
                    <?php
                }
                ?>
            </td>
        </tr>
    <?php } ?>

    <?php if ($campania->empresa_campania == 1 && isset($audiencias)) { ?>
        <tr>
            <td valign="top">Audiencias:</td>
            <td>
                <ul>
                    <?php
                    foreach ($audiencias as $audiencia) {
                        echo '<li style=" list-style-type: square;margin-left:20px;"><b>' . $audiencia->name . '</b></li>';
                    }
                    ?>
                </ul>
            </td>
        </tr>
    <?php } ?>

    <?php if ($creatividades) { ?>
        <tr>
            <td>Creatividades:</td>
            <td><ul>
                    <?php
                    if ($creatividades) {
                        foreach ($creatividades as $row) {
                            echo '<li style=" list-style-type: square;margin-left:20px;"><b>' . $row->nombre_real . '</b></li>';
                        }
                    }
                    ?>
                </ul>
            </td>
        </tr>
    <?php } ?>

    <?php if (isset($formatos)) { ?>
        <tr>
            <td>Tama&ntilde;o:</td>
            <td>
                <table style="text-align: center;">
                    <tr>
                        <th style="padding:5px 10px;border:1px solid #ccc;background-color:#dcdcdc;">Formato</th>
                        <th style="padding:5px 10px;border:1px solid #ccc;background-color:#dcdcdc;">P&aacute;gina destino</th>
                    </tr>
                    <?php
                    foreach ($formatos as $formato) {
                        ?>
                        <tr>
                            <td style="padding:5px 10px;border: 1px solid #ccc;"><?= $formato['descripcion'] ?></td>
                            <td style="padding:5px 10px;border: 1px solid #ccc;text-align: left;"><?= $formato['pagina_destino'] ?>
                                <?php
                                if ($formato['pagina_destino'] != "") {
                                    if (strpos($formato['pagina_destino'], 'http') === false)
                                        $formato['pagina_destino'] = 'http://' . $formato['pagina_destino'];
                                    ?>
                                    <a target="_blank" href="<?= $formato['pagina_destino'] ?>">[ Ir a la pagina ]</a>
                                <?php } else {
                                    ?><p>No posee paginas de destino</p>
                                    <?php
                                }
                                ?>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>
            </td>
        </tr>
    <?php } ?>


    <?php if (!$usuario->agencia) { ?>

        <?php if (isset($formatos)) { ?>
            <tr>
                <td style="width:160px;">Modalidad de compra:</td>
                <td><b><?= $modalidad_compra ?></b></td>
            </tr>

            <tr>
                <td style="width:160px;">Cantidad:</td>
                <td><b><?= $cantidad_compra ?></b></td>
            </tr>

            <tr>
                <td style="width:160px;">Valor:</td>
                <td><b><?= $diario ?></b></td>
            </tr>
        <?php } ?>

        <?php if ($campania->type_DFP == 'PRICE_PRIORITY') { ?>
            <tr>
                <td style="width:160px;">Inversi&oacute;n neta diaria:</td>
                <td><b><?= $inversion_neto . ' ' . $this->user_data->moneda . '</b>' ?></td>
            </tr>

            <?php if ($mostrar_inversion_total) { ?>
                <tr>
                    <td>Inversi&oacute;n neta total:</td>
                    <td><b><?= $inversion_neta_total . ' ' . $this->user_data->moneda . '</b> ( Puede variar si luego modifica la inversión diaria)' ?></td>
                </tr>
            <?php } ?>
        <?php } else { ?>
            <tr>
                <td style="width:160px;">Inversi&oacute;n neta:</td>
                <td><b><?= $inversion_neto . ' ' . $this->user_data->moneda . '</b>' ?></td>
            </tr>
        <?php } ?>

    <?php } ?>

    <?php if ($fecha_alta != '') { ?>
        <tr>
            <td style="width:160px;">Fecha creaci&oacute;n:</td>
            <td><b><?= $fecha_alta ?></b></td>
        </tr>
    <?php } ?>

    <tr><td colspan="2">&nbsp;</td></tr>

    <?php if ($mostrar_exportar) { ?>
        <tr>
            <td colspan="2">
                Acepto los <a href="https://ayuda.mediafem.com/mediafem-sitios/conceptos-basicos-mediafem-sitios/politicas-del-programa-mediafem-para-sitios" target="_BLANK">términos y condiciones</a> <a href="http://ayuda.mediafem.com/mediafem-anunciantes/terminos-y-condiciones-de-la-orden-de-compra-de-mediafem" target="_BLANK">de la orden de compra</a>.
            </td>
        </tr>
    <?php } ?>
</table>