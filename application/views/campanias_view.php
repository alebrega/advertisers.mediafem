<?php require_once 'application/views/top.php'; ?>

<ul class="Tabs" id="campanas_tabs">
    <?php if($tiene_campanias){ ?>
        <li><a href="#mis_campanas">Mis campa&ntilde;as</a></li>
        <li><a href="#status_campanas">Estado de mis campa&ntilde;as</a></li>
    <?php } ?>

    <?php if($mostrar_crear_campania || $mostrar_poner_tarjera || !$clientes_activos){ ?>
        <li><a href="#nueva_campana" <?php if(!$tiene_campanias){ echo 'class="selected"'; } ?>>Crear nueva campa&ntilde;a</a></li>
    <?php } ?>
</ul>

<div class="contenainer_tabs">
    <?php if($tiene_campanias){ ?>
        <div id="mis_campanas"></div>
        <div id="status_campanas" style="display: none;"></div>
    <?php } ?>

    <?php if($mostrar_crear_campania || $mostrar_poner_tarjera || $clientes_activos){ ?>
        <div id="nueva_campana" style="display: none;"></div>
    <?php } ?>
        
    <?php if(!$clientes_activos){ ?>
        <div id="nueva_campana" style="display: none;">
            <div class="alerta">No posees clientes activos en tu cuenta.</div>
        </div>
    <?php } ?>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        <?php if($tiene_campanias){ ?>
            $('#mis_campanas').append(divLoader);
            $('#mis_campanas').load('/campania/obtener_campanias/'+seconds);

            $('#status_campanas').append(divLoader);
            $('#status_campanas').load('/campania/status_campanias');
        <?php } ?>

        <?php if($mostrar_crear_campania){ ?>
            $('#nueva_campana').append(divLoader);
            $('#nueva_campana').load('/campania/tipo_campania/'+seconds);
        <?php } ?>

        <?php if($mostrar_poner_tarjera){ ?>
            $('#nueva_campana').append(divLoader);
            $('#nueva_campana').load('/campania/mostrar_cargar_saldo/'+seconds);
        <?php } ?>
    });
</script>

<?php require_once 'application/views/footer.php'; ?>