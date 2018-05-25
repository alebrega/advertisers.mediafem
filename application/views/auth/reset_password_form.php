<?php require_once 'application/views/auth/top.php'; ?>

<form action="<?= base_url() . $this->uri->uri_string() ?>" method="post" id="form_login">
    <div class="container textCenter">
        <span class="alert_ok" style="display: none;text-align: center;">Contrase&ntilde;a modificada con exito.</span>

        <div class="errorText row">
            <?php echo form_error('new_password'); ?>
            <?php echo isset($errors['new_password']) ? $errors['new_password'] : ''; ?>
            <?php echo form_error('confirm_new_password'); ?>
            <?php echo isset($errors['confirm_new_password']) ? $errors['confirm_new_password'] : ''; ?>
        </div>

        <div class="row">
            <input type="password" name="new_password" value="" maxlength="20" size="30" placeholder="Nueva contrase&ntilde;a" />
        </div>

        <div class="row">
            <input type="password" name="confirm_new_password" value="" maxlength="20" size="30" placeholder="Repetir nueva contrase&ntilde;a" />
        </div>

        <div class="row">
            <input type="submit" name="change" value="Cambiar contrase&ntilde;a" />
        </div>
    </div>
</form>

<?php require_once 'application/views/footer.php'; ?>