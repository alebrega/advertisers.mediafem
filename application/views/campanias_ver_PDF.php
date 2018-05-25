<style type="text/css">
    .tabla{
        border-spacing: 15px;
        color: #252C2E;
        font: normal normal 0.8em 'Calibri', Arial, Helvetica, sans-serif;
    }

    #mostrar_sitios_div, #mostrar_canales_div{
        margin:10px 0px;
        width:422px;
        border:1px solid #ccc;
        margin:10px 25px 0;
    }
</style>

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

    <?php if ($campania->empresa_campania == 0) { ?>
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

    <?php if ($campania->empresa_campania == 0) { ?>
        <tr>
            <td valign="top">Segmentaci&oacute;n:</td>
            <td>
                <b><?= $segmentacion ?></b>
                <?php
                if ($segmentacion_id == 3) {
                    ?>
                    <div id="mostrar_sitios_div">
                        <table>
                            <?php
                            foreach ($sitios as $sitio) {
                                echo '<tr><td style="padding:5px 10px;"> - ' . $sitio['nombre'] . '</td></tr>';
                            }
                            ?>
                        </table>
                    </div>
                    <?php
                } else if ($segmentacion_id == 2) {
                    ?>
                    <div id="mostrar_canales_div">
                        <table>
                            <?php
                            foreach ($canales_tematicos as $canal) {
                                echo '<tr><td style="padding:5px 10px;"> - ' . $canal['name'] . '</td></tr>';
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
            <td valign="top">Creatividades:</td>
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
            <td valign="top">Tama&ntilde;o:</td>
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
                            <td style="padding:5px 10px;border: 1px solid #ccc;text-align: left;"><?= $formato['pagina_destino'] ?></td>
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
                <td><b>
                        <?php
                        if ($formatos[1]['modalidad'] == '') {
                            echo "CPM";
                        } else {
                            echo $formatos[1]['modalidad'];
                        }
                        ?>
                    </b>
                </td>
            </tr>

            <tr>
                <td style="width:160px;">Cantidad:</td>
                <td><b><?= $cantidad_compra ?></b></td>
            </tr>
        <?php } ?>

        <?php if (isset($formatos)) { ?>
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

            <tr>
                <td>Inversi&oacute;n neta total:</td>
                <td><b><?= $inversion_neta_total . ' ' . $this->user_data->moneda . '</b> ( Puede variar si luego modifica la inversión diaria)' ?></td>
            </tr>
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

    <tr>
        <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
        <td colspan="2">&nbsp;</td>
    </tr>
</table>

<?php if ($campania->empresa_campania == 0) { ?>
    <div style="color: #252C2E; font: normal normal 9px 'Calibri', Arial, Helvetica, sans-serif; width:100%;">
        <h2 style="font-size: 12px;">Terminos y condiciones de la orden de compra de MediaFem</h2>

        <p><b>A.</b> MEDIAFEM contin&uacute;a con campa&ntilde;as publicitarias m&aacute;s all&aacute; de este acuerdo con el Anunciante.<br /></p>

        <p><b>B.</b> El Anunciante promocionar&aacute; sus productos o servicios mediante campa&ntilde;as de publicidad que ser&aacute;n entregadas por MEDIAFEM. Todo el material de las campa&ntilde;as ser&aacute; prove&iacute;do por el anunciante.<br /></p>

        <p><b>C.</b> Para la emisi&oacute;n de la factura de MEDIAFEM, se tendr&aacute; en cuenta los n&uacute;meros generados por el servidor de publicidad (adserver) de MEDIAFEM. Los valores expresados no incluyen comisi&oacute;n de agencia ni est&aacute;n sujetos a ning&uacute;n tipo de retenci&oacute;n o impuesto adicional.<br /></p>

        <p><b>D.</b> En caso que la campa&ntilde;a sea cancelada, MEDIAFEM facturara el consumo hasta la fecha de cancelaci&oacute;n, o la inversi&oacute;n m&iacute;nima, el que sea mayor.<br /></p>

        <p><b>E.</b> El Anunciante autoriza expresamente a MEDIAFEM a incluir su nombre en la lista de sus clientes y en todo material promocional que considere adecuado para promover las ventajas del servicio.<br /></p>

        <p><b>F.</b> Las facturas vence 30 d&iacute;as luego de su fecha de emisi&oacute;n. En caso que luego de los 30 d&iacute;as la factura no sea cancelada, la deuda devengar&aacute; un inter&eacute;s por mora del 5% mensual.<br /></p>

        <p><b>G.</b>Los puntos D y F no aplican en caso de que la campa&ntilde;a haya sido efectivamente pagada con nuestros servicios de pago online.<br /></p>

        <p>El Anunciante acepta los t&eacute;rminos y condiciones que figuran en esta orden de inserci&oacute;n, y para el caso en que no cumpla con el pago de las facturas acuerda someterse a las Leyes, Jurisdicci&oacute;n y Tribunales del Estado de Florida, Estados Unidos de Norteam&eacute;rica.<br /></p>
    </div>
<?php } else { ?>
    <div style="color: #252C2E; font: normal normal 9px 'Calibri', Arial, Helvetica, sans-serif; width:100%;">
        <h2 style="font-size: 12px;">Terminos y condiciones de la orden de compra de AdTomatik</h2>

        <p><b>A.</b> ADTOMATIK contin&uacute;a con campa&ntilde;as publicitarias m&aacute;s all&aacute; de este acuerdo con el Anunciante.<br /></p>

        <p><b>B.</b> El Anunciante promocionar&aacute; sus productos o servicios mediante campa&ntilde;as de publicidad que ser&aacute;n entregadas por ADTOMATIK. Todo el material de las campa&ntilde;as ser&aacute; prove&iacute;do por el anunciante.<br /></p>

        <p><b>C.</b> Para la emisi&oacute;n de la factura de ADTOMATIK, se tendr&aacute; en cuenta los n&uacute;meros generados por el servidor de publicidad (adserver) de ADTOMATIK. Los valores expresados no incluyen comisi&oacute;n de agencia ni est&aacute;n sujetos a ning&uacute;n tipo de retenci&oacute;n o impuesto adicional.<br /></p>

        <p><b>D.</b> En caso que la campa&ntilde;a sea cancelada, ADTOMATIK facturara el consumo hasta la fecha de cancelaci&oacute;n, o la inversi&oacute;n m&iacute;nima, el que sea mayor.<br /></p>

        <p><b>E.</b> El Anunciante autoriza expresamente a ADTOMATIK a incluir su nombre en la lista de sus clientes y en todo material promocional que considere adecuado para promover las ventajas del servicio.<br /></p>

        <p><b>F.</b> Las facturas vence 30 d&iacute;as luego de su fecha de emisi&oacute;n. En caso que luego de los 30 d&iacute;as la factura no sea cancelada, la deuda devengar&aacute; un inter&eacute;s por mora del 5% mensual.<br /></p>

        <p><b>G.</b>Los puntos D y F no aplican en caso de que la campa&ntilde;a haya sido efectivamente pagada con nuestros servicios de pago online.<br /></p>

        <p>El Anunciante acepta los t&eacute;rminos y condiciones que figuran en esta orden de inserci&oacute;n, y para el caso en que no cumpla con el pago de las facturas acuerda someterse a las Leyes, Jurisdicci&oacute;n y Tribunales del Estado de Florida, Estados Unidos de Norteam&eacute;rica.<br /></p>
    </div>
<?php } ?>
