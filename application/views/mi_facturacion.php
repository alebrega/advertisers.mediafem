<?php if (!$clientes) { ?>
    <div class="alerta">No posee ningun cliente asociado a su cuenta.</div>
<?php } else { ?>
    <select name="cmb_clientes" <?php if (sizeof($clientes) == 1) {
        echo 'style="display:none;"'; } ?> >
            <?php foreach ($clientes as $cliente) { ?>
                <option value="<?= $cliente->id; ?>"><?= $cliente->razon_social; ?></option>
        <?php } ?>
        </select>

        <div id="facturacion_cliente"></div>

        <script>
            $().ready(function(){
                $('#facturacion_cliente').html(divLoader).load('/micuenta/mis_saldos/' + $('select[name="cmb_clientes"] option:selected').val());
                
                $('select[name="cmb_clientes"]').change(function(){
                    $('#facturacion_cliente').html(divLoader).load('/micuenta/mis_saldos/' + $('select[name="cmb_clientes"] option:selected').val());
                });
            });
        </script>
    <?php } ?>
