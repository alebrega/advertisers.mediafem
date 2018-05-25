<style type="text/css">
    .canal_tematico{
        margin: 5px 0;
    }
    .category_name{
        font-weight: bold;
    }
</style>

<script type="text/javascript">
    $().ready(function(){
        $('.plus').click(function(e){
            e.preventDefault();

            var display = $("#" + $(this).attr('data-category')).css('display');

            if(display == 'none'){
                $("#" + $(this).attr('data-category')).css('display', 'block');
            }else{
                $("#" + $(this).attr('data-category')).css('display', 'none');
            }
        });
    });
</script>

<?php foreach ($categorias as $categoria) { ?>

    <div class="canal_tematico">
        <span class="category_name"><?= $categoria['name'] ?></span>

        <a href="#" class="plus" data-category="<?= $categoria['id'] ?>">v</a>

        <table id="<?= $categoria['id'] ?>" style="display: none; margin: 5px 0 15px 15px;">
            <tr>
                <th>&nbsp;</th>
                <th><b>Sitio Web</b></th>
            </tr>

            <?php foreach ($categoria['sites'] as $sitio) { ?>
                <tr>
                    <td>
                        <input type="checkbox" name="chk_site" class="chk_site" value="<?= $sitio['id'] ?>" />
                    </td>
                    <td><?= $sitio['nombre'] ?></td>
                </tr>
            <?php } ?>

        </table>
    </div>

<?php } ?>