<input type="hidden" id="cupon_de_promocion" value="<?= $cupon_de_promocion ?>" />

<div class="button_bar">
    <?php if ($this->user_data->country == 'AR' || $this->user_data->country == 'MX') { ?>
        <a href="#" data-reveal-id="cargar_saldo_MercadoPago_modal" class="button_new">
            Cargar saldo con MercadoPago
        </a>
    <?php } ?>

    <?php if ($this->user_data->country != 'AR' && $this->user_data->country != 'MX') { ?>
        <a href="#" data-reveal-id="cargar_saldo_PayPal_modal" class="button_new">
            Cargar saldo con PayPal
        </a>
    <?php } ?>

    <a href="#" data-reveal-id="cargar_cupon_modal" class="button_new">
        Cargar cup&oacute;n de promoci&oacute;n
    </a>
</div>

<div id="mis_pagos"></div>

<?php if ($this->user_data->country == 'AR' || $this->user_data->country == 'MX') { ?>
    <div id="cargar_saldo_MercadoPago_modal" class="reveal-modal small" style="text-align:center;">
        <div class="monto_a_cargar">
            <b>Monto a cargar en <?= $this->user_data->moneda ?></b>

            <input type="text" placeholder="0,00" name="monto_a_cargar_mercadopago" style="margin: 7px 0;" />

            <input type="button" class="button_new btn_cancelar" name="btn_cancelar" value="Cancelar" />
            <input type="button" class="button_new" name="btn_cargar_mercadopago" value="Cargar saldo" />
        </div>

        <div class="monto_cargado" style="color: green; display: none;">Monto cargado con exito.</div>

        <div class="error_cargar" style="color: red; display: none;"></div>
    </div>
<?php } ?>


<?php if ($this->user_data->country != 'AR' && $this->user_data->country != 'MX') { ?>
    <div id="cargar_saldo_PayPal_modal" class="reveal-modal small" style="text-align:center;">
        <div class="monto_a_cargar">
            <b>Monto a cargar en USD</b>

            <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
                <input type="hidden" name="cmd" value="_s-xclick">
                <input type="hidden" name="hosted_button_id" value="U9K2XRGZRQRVQ">
                <input type="hidden" name="on0" value="Carga de saldo prepago">

                <select name="os0" style="margin: 7px 0;">
                    <option value="USD 25">USD 25</option>
                    <option value="USD 50">USD 50</option>
                    <option value="USD 100">USD 100</option>
                    <option value="USD 200">USD 200</option>
                    <option value="USD 500">USD 500</option>
                    <option value="USD 1000">USD 1000</option>
                </select>

                <input type="hidden" name="return" value="<?= base_url() ?>campania">
                <input type="hidden" name="notify_url" value="<?= base_url() ?>micuenta/success_paypal/<?= $id_anunciante ?>/<?= $id_cliente ?>">

                <input type="hidden" name="currency_code" value="USD">
                <input type="button" class="button_new btn_cancelar" name="btn_cancelar" value="Cancelar" />
                <input type="image" id="btn_cargar_paypal" src="<?= base_url() ?>images/cargar_saldo.png" name="submit" alt="Cargar saldo" style="border:0;padding:0;" border="0" />
            </form>
        </div>

        <div class="monto_cargado" style="color: green; display: none;">Monto cargado con exito.</div>

        <div class="error_cargar" style="color: red; display: none;"></div>
    </div>
<?php } ?>

<div id="cargar_cupon_modal" class="reveal-modal small" style="text-align:center;">
    <div class="monto_a_cargar">
        <b>C&oacute;digo del cup&oacute;n</b>

        <input type="text" placeholder="" name="codigo_cupon" style="margin: 7px 0;" />

        <input type="button" class="button_new btn_cancelar" name="btn_cancelar" value="Cancelar" />
        <input type="button" class="button_new" name="btn_cargar_cupon" value="Cargar cup&oacute;n" />
    </div>

    <div class="monto_cargado" style="color: green; display: none;">Monto cargado con exito.</div>

    <div class="error_cargar" style="color: red; display: none;"></div>
</div>

<div id="resultCarga"></div>


<?php if ($this->user_data->country == 'AR' || $this->user_data->country == 'MX') { ?>
    <script type="text/javascript" src="https://www.mercadopago.com/org-img/jsapi/mptools/buttons/render.js"></script>
    <script type="text/javascript">
        (function() {
            function $MPBR_load() {
                window.$MPBR_loaded !== true && (function() {
                    var s = document.createElement("script");
                    s.type = "text/javascript";
                    s.async = true;
                    s.src = ("https:" == document.location.protocol ? "https://www.mercadopago.com/org-img/jsapi/mptools/buttons/" : "http://mp-tools.mlstatic.com/buttons/") + "render.js";
                    var x = document.getElementsByTagName('script')[0];
                    x.parentNode.insertBefore(s, x);
                    window.$MPBR_loaded = true;
                })();
            }
            window.$MPBR_loaded !== true ? (window.attachEvent ? window.attachEvent('onload', $MPBR_load) : window.addEventListener('load', $MPBR_load, false)) : null;
        })();
    </script>
<?php } ?>


<script type="text/javascript">
    $().ready(function() {
        $('#mis_pagos').html(divLoader).load('/micuenta/obtener_historial_pagos_y_limite_de_compra/' + <?= $id_cliente; ?> + '/' + seconds);
        
        $('.btn_cancelar').click(function() {
            $('.reveal-modal-bg').click();
        });

        $('a[class="button_new"]').click(function() {
            $(".monto_cargado").css("display", "none");
            $(".error_cargar").css("display", "none");
            $(".monto_a_cargar").css("display", "block");

            $('input[name="monto_a_cargar_mercadopago"]').val('');

            $('input[name="codigo_cupon"]').val('');

            $('input[name="monto_a_cargar_mercadopago"]').attr('disabled', false);

            $('input[name="btn_cargar_cupon"]').attr('disabled', false);

            $('input[name="btn_cancelar"]').attr('disabled', false);

            $('input[name="btn_cargar_mercadopago"]').attr('value', 'Cargar saldo');
            $('input[name="btn_cargar_paypal"]').attr('value', 'Cargar saldo');
            $('input[name="btn_cargar_cupon"]').attr('value', 'Cargar cupón');
        });

<?php if ($this->user_data->country == 'AR' || $this->user_data->country == 'MX') { ?>
            // MERCADOPAGO *********************************************************
            $('input[name="btn_cargar_mercadopago"]').click(function() {
                $('input[name="monto_a_cargar_mercadopago"]').attr('disabled', 'disabled');
                $('input[name="btn_cancelar"]').attr('disabled', 'disabled');
                $(this).attr('value', 'Cargando...');

                var data_form = {
                    id_cliente: <?= $id_cliente; ?>,
                    metodo: 'mercadopago',
                    monto: $('input[name="monto_a_cargar_mercadopago"]').val()
                }

                $.ajax({
                    type: "post",
                    url: "/micuenta/cargar_saldo/",
                    data: data_form,
                    dataType: "json",
                    success: function(msg) {
                        if (msg.validate) {
                            $('input[name="btn_cancelar"]').click();

                            $MPC.openCheckout({
                                url: msg.init_point,
                                mode: "modal",
                                onreturn: function(data) {
                                    // guardo la transaccion realizada en la basey, segun el estado, habilito o no el saldo
                                    $('#resultCarga').load('/micuenta/guardar_transaccion_mercadopago/' + data.collection_id + '/' + data.collection_status + '/' + $('input[name="monto_a_cargar_mercadopago"]').val() + '/' + <?= $id_cliente ?>, function() {
                                        $('#saldo_disponible').load('/micuenta/get_saldo_disponible', function() {
                                            $('#mis_pagos').html(' ').append(divLoader);
                                            $('#mis_pagos').load('/micuenta/obtener_historial_pagos_y_limite_de_compra/' + seconds);

                                            $("#saldo_disponible").animate({color: '#00ff00'}, 'slow', function() {
                                                $("#saldo_disponible").animate({color: '#fff'}, 'slow');
                                            });
                                        });
                                    });
                                }
                            });
                        } else {
                            $(".monto_a_cargar").css("display", "none");
                            $(".error_cargar").html(msg.error).fadeIn('fast');

                            setTimeout(function() {
                                if (!msg.validate) {
                                    $(".error_cargar").html(msg.error).fadeIn('fast');
                                }
                            }, 1500);
                        }
                    }
                });
            });
            // end MERCADOPAGO *****************************************************
<?php } ?>

        // CARGAR CUPON *********************************************************
        $('input[name="btn_cargar_cupon"]').click(function() {
            $('input[name="btn_cargar_cupon"]').attr('disabled', 'disabled');
            $('input[name="btn_cargar_cupon"]').attr('disabled', 'disabled');
            $(this).attr('value', 'Cargando...');

            var data_form = {
                id_cliente: <?= $id_cliente; ?>,
                codigo: $.trim($('input[name="codigo_cupon"]').val())
            }

            $.ajax({
                type: "post",
                url: "/micuenta/cargar_cupon/",
                data: data_form,
                dataType: "json",
                success: function(msg) {
                    if (msg.validate) {
                        $('input[name="btn_cancelar"]').click();

                        $('#saldo_disponible').load('/micuenta/get_saldo_disponible', function() {
                            $('#mis_pagos').html(' ').append(divLoader);
                            $('#mis_pagos').load('/micuenta/obtener_historial_pagos_y_limite_de_compra/' + seconds);

                            $("#saldo_disponible").animate({color: '#00ff00'}, 'slow', function() {
                                $("#saldo_disponible").animate({color: '#fff'}, 'slow');
                            });
                        });
                    } else {
                        $(".monto_a_cargar").css("display", "none");
                        $(".error_cargar").html(msg.error).fadeIn('fast');

                        setTimeout(function() {
                            if (!msg.validate) {
                                $(".error_cargar").html(msg.error).fadeIn('fast');
                            }
                        }, 1500);
                    }
                }
            });
        });
        // end MERCADOPAGO *****************************************************

        if ($('#cupon_de_promocion').val() == '1')
            mensajeGeneral('warning', 'Si realiza una carga de 50 USD le duplicamos el saldo para su primer campa&ntilde;a', 100000);
    });
</script>