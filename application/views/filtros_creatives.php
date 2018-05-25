<?php
foreach ($creatives as $row) {
    ?>
    <option value="<?= $row->id ?>"><?= $row->name ?></option>
    <?php
}
?>
