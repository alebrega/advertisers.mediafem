<table id="lista_materiales_<?= $id_campania ?>">
    <thead>
        <tr>
            <th>Nombre</th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($materiales as $row) {
            ?>
            <tr>
                <td><?= $row->nombre_real ?></td>
                <td class="textCenter">
                    <a href="javascript:;" data-accion="eliminar_material_<?= $id_campania ?>" id="<?= $row->id ?>">Eliminar</a>
                </td>
            </tr>
            <?php
        }
        ?>
    </tbody>
</table>

<script type="text/javascript">
    $(document).ready(function(){
        $('a[data-accion="eliminar_material_<?= $id_campania ?>"]').click(function(){
            var id = $(this).attr('id');
            $('#tbl_materiales_modificar_<?= $id_campania ?>').html(' ').append(divLoader).load('/campania/eliminar_material/' + id + '/<?= $id_campania ?>');
        });

        $('#lista_materiales_<?= $id_campania ?>').dataTable({
            <?php if(sizeof($materiales) > 5){ ?>
            "bPaginate": true,
            <?php }else{ ?>
            "bPaginate": false,
            <?php } ?>
            "sPaginationType": "full_numbers",
            "iDisplayLength": 5,
            "bLengthChange": false,
            "bFilter": false,
            "bInfo": false,
            "bLength": false,
            'oLanguage': {
                "oPaginate": {
                    "sFirst": "<<",
                    "sLast": ">>",
                    "sNext": ">",
                    "sPrevious": "<"
                },
                "sSearch": 'Buscar'
            },
            "aoColumns": [
                null,
                null
            ]
        });
    });
</script>