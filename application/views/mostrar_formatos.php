<table id="personalizar_formatos">
    <tr>
        <th style="padding: 0 20px 10px 0;">Tipo</th>
        <th style="padding: 0 20px 10px 0;">P&aacute;gina de destino</th>
    </tr>

    <?php
    if(sizeof($formatos) > 1){ // si tiene mas de un formato por mostrar
    $a = 1;

        foreach ($formatos as $formato) {
            $descripcion = $formato->descripcion;

            if($tipo_campania == 'expandible')
                $descripcion = $formato->descripcion_expandible;

            $checked = $pagina_destino = '';
            if($formatos_campanias){
                foreach ($formatos_campanias as $formato_campania) {
                    if($formato_campania->id_formato == $formato->id){
                        $checked = 'checked="checked"';
                        $pagina_destino = $formato_campania->pagina_destino;
                        continue;
                    }
                }
            }

            if($descripcion != ''){
            ?>
                <tr>
                    <td style="padding: 0 20px 5px 0;">
                        <input type="checkbox" name="chk_formatos[]" value="<?= $formato->id ?>" rel="<?= $a ?>" <?= $checked ?> />&nbsp;<?= $descripcion ?>
                    </td>
                    <td style="padding: 0 20px 5px 0;">
                        <input type="text" name="pagina_destino" class="pagina_destino" style="width:500px !important;" id="pagina_destino_<?= $a ?>" value="<?= $pagina_destino ?>" placeholder="http://www.ejemplo.com/" rel="<?= $a ?>" />
                        <img src="/images/ajax-loader.gif" height="10px" id="loader_tamano_<?= $a ?>" style="display:none" />
                    </td>
                </tr>
            <?php
                $a++;
            }
        }

    }else{ // si es un unico formato a mostrar
        $pagina_destino = '';
        if($formatos_campanias)
            $pagina_destino = $formatos_campanias[0]->pagina_destino;
    ?>
            <tr>
                <td style="padding: 0 20px 5px 0;">
                    <input type="checkbox" name="chk_formatos[]" value="<?= $formatos->id ?>" rel="1" checked="checked" />&nbsp;<?= $formatos->descripcion ?>
                </td>
                <td style="padding: 0 20px 5px 0;">
                    <input type="text" name="pagina_destino" class="pagina_destino" style="width:250px !important;" id="pagina_destino_1" value="<?= $pagina_destino ?>" placeholder="http://www.ejemplo.com/" rel="1" />
                    <img src="/images/ajax-loader.gif" height="10px" id="loader_tamano_1" style="display:none" />
                </td>
            </tr>
    <?php
    }
    ?>

    <tr>
        <td colspan="2">
            <input type="checkbox" name="chk_agregar_mediafem" checked="checked" value="1" />&nbsp;
            Agregar a MediaFem como fuente de las visitas a su pagina (Solo para paginas web que cuenten con Google Analytics)
        </td>
    </tr>
</table>

<script type="text/javascript">
    $().ready(function(){
        $('input[type="checkbox"]').click(function(){
            if( $(this).attr('name') == 'chk_formatos[]' ){

                var rel = $(this).attr('rel');

                if( $(this).attr('checked') == 'checked' ){
                    $('#pagina_destino_' + rel).attr('disabled',false);
                }else{
                    $('#pagina_destino_' + rel).attr('disabled',true);
                }
            }
        });

        $('.pagina_destino').click(function(){
            var rel = $(this).attr('rel');

            $('input[name="chk_formatos[]"]').each(function(){
                if($(this).attr('rel') == rel){
                    $(this).attr('checked', 'checked');
                }
            });
        });
    });
</script>