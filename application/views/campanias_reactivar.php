<script type="text/javascript">
    $().ready(function(){
        var id_campana = '<?= $id_campania ?>';
        var id_dfp_campana = '<?= $id_dfp_campania ?>';

        $('#cancelar_pausa').click(function(){
            $('.reveal-modal-bg').click();
        });

        $('#aceptar_pausa').click(function(){
            $('#ok_pausar').css('display', 'none');
            $('#loader_pausar').css('display', 'none');
            $('#error_pausar').html( ' ' ).css('display', 'none');

            $('#loader_pausar').css('display', 'inline');

            var form_data = {
                id_campana: id_campana,
                id_dfp_campana: id_dfp_campana
            };

            $.ajax({
                type: "POST",
                url: "/campania/reactivar/",
                data: form_data,
                dataType: "json",
                success: function(msg){
                    if(msg.validate){
                        $('#loader_pausar').fadeOut(500, function(){
                            $('#ok_pausar').fadeIn(500, function(){
                                window.location.replace("/campania");
                            });
                        });
                    }else{
                        $('#loader_pausar').fadeOut(500, function(){
                            $('#error_pausar').fadeIn(500).html( msg.error );
                        });
                    }
                }
            });
        });
    });
</script>

<h2 class="border_bottom">Reactivar campa&ntilde;a.</h2>

<div class="row">
    Est&aacute;s a punto de reactivar la campa&ntilde;a:
</div>

<div class="row">
    <b><?= $nombre ?></b>
</div>

<hr class="border_bottom" />

<div>
    <input type="button" class="button_new superButton" value="Cancelar" id="cancelar_pausa" />
    <input type="button" class="button_new superButton" value="Aceptar" id="aceptar_pausa" />

    <span id="loader_pausar" style="display: none;">
        <img style="display:none;" src="/images/ajax-loader.gif" height="10px" /> Reactivando campa&ntilde;a, espere por favor...
    </span>

    <span id="error_pausar" style="display: none; color: red;"></span>

    <span id="ok_pausar" style="display: none; color: green;">
        Campa&ntilde;a reactivada correctamente, espere...
    </span>
</div>