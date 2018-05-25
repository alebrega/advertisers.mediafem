<div style="width: 450px; float: left; margin: 0 50px 0 60px; text-align: center;">
    <img src="images/mediafem_logo.png" alt="MediaFem" />
    <p style="font-size: 1em; margin: 10px 0;">¿Interesado en campa&ntilde;as en sitios web premium de tem&aacute;ticas  femeninas? Tu elecci&oacute;n debe ser MediaFem.</p>
    <a href="javascript:;" id="campania_mediafem"
       style="background-color: #61BA4D; border-radius: 3px; color: #FFF; display: inline-block; font: bold 1em 'Calibri',sans-serif; margin-top: 15px; padding: 8px 0; text-transform: uppercase; width: 100%;;"
       >Crear campa&ntilde;a</a>
</div>

<div style="width: 450px; float: left; margin: 0 50px; text-align: center;">
    <img src="images/adtomatik_logo.png" alt="AdTomatik" />
    <p style="font-size: 1em; margin: 10px 0;">¿Interesado en campa&ntilde;as a resultados o campa&ntilde;as orientadas a audiencias? Tu elecci&oacute;n debe ser Adtomatik.</p>
    <a href="javascript:;" id="campania_adtomatik"
       style="background-color: #61BA4D; border-radius: 3px; color: #FFF; display: inline-block; font: bold 1em 'Calibri',sans-serif; margin-top: 15px; padding: 8px 0; text-transform: uppercase; width: 100%;;"
       >Crear campa&ntilde;a</a>
</div>


<script type="text/javascript">
    $(document).ready(function(){
        $('#campania_mediafem').click(function(){
            $('#nueva_campana').html('').append(divLoader);
            $('#nueva_campana').load('/campania/crear/'+seconds);
        });

        $('#campania_adtomatik').click(function(){
            $('#nueva_campana').html('').append(divLoader);
            $('#nueva_campana').load('/campania/crear_appnexus/'+seconds);
        });
    });
</script>