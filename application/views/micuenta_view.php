<?php require_once 'application/views/top.php'; ?>

<ul class="Tabs" id="micuenta_tabs">
    <li><a href="#mis_datos">Mis datos</a></li>
    <!--<li><a href="#mis_pagos">Mis pagos</a></li>-->
    <li><a href="#mis_saldos">Facturaci&oacute;n</a></li>
    <!--<li><a href="#mis_tarjetas">Mis tarjetas</a></li>-->
</ul>

<div class="contenainer_tabs">
    <div id="mis_datos"></div>

    <div id="mis_saldos" style="display: none;"></div>

    <!--<div id="mis_tarjetas"></div>-->
</div>

<script type="text/javascript">
    $(document).ready(function(){        
        $('#mis_datos').html(divLoader).load('/micuenta/mis_datos');
        
        $('#mis_saldos').html(divLoader).load('/micuenta/mi_facturacion/');
/*
        $('#mis_tarjetas').append(divLoader);
        $('#mis_tarjetas').load('/micuenta/mis_tarjetas/');
*/
    });
</script>

<?php require_once 'application/views/footer.php'; ?>