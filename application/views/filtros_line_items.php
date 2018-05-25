<?php
foreach ($line_items as $row) {
    if(!strstr($row->name, '(**)') && !strstr($row->name, '(++)')){
    ?>
        <option value="<?= $row->id ?>"><?= $row->name ?></option>
    <?php
    }
}
?>

