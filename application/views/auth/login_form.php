<?php require_once 'application/views/auth/top.php'; ?>

<script type="text/javascript">
    $(document).ready(function() {
        $('#myModal').click(function() {
            $('#myModal form').fadeIn(0);
        });

        $('a[data-reveal-id="myModal"]').click(function() {
            $('.alert_ok').fadeOut(0).html(' ');
            $('.alert_error').fadeOut(0).html(' ');
            $('#myModal form').fadeIn(0);
            $('input[name="correo_electronico"]').val('');
        });

        $('input[name="cancelar"]').click(function(e) {
            e.preventDefault();
            $('.reveal-modal-bg').click();
        });

        $('input[name="aceptar"]').click(function(e) {
            e.preventDefault();

            $('.alert_ok').fadeOut(0).html(' ');
            $('.alert_error').fadeOut(0).html(' ');

            $('#img_loader').fadeIn(0);

            var form_data = {
                login: $.trim($('input[name="correo_electronico"]').val())
            };

            $.ajax({
                type: "post",
                url: "/auth/forgot_password/",
                data: form_data,
                dataType: "json",
                success: function(rta) {
                    $('#img_loader').fadeOut(0);

                    if (rta.estado) {
                        $('#myModal form').fadeOut(500, function() {
                            $('.alert_ok').html(rta.mensaje);
                            $('.alert_ok').fadeIn('fast');
                        });
                    } else {
                        $('.alert_error').html(rta.mensaje);
                        $('.alert_error').fadeIn('fast');
                    }
                }
            });
        });
    });
</script>

<form action="<?= base_url() ?>auth/login" method="post" id="form_login">
    <div class="container textCenter">

        <?php
        $error_User = FALSE;
        $error_Pass = FALSE;

        $name_User = isset($_POST['login']) ? trim($_POST['login']) : '';
        $pass_User = isset($_POST['password']) ? trim($_POST['password']) : '';

        if (isset($errors['login'])) {
            str_replace('Login', 'Usuario', form_error('login'));
            $error_User = isset($errors['login']) ? '<p class="errorText">' . $errors['login'] . '</p>' : '';
        }

        if (isset($errors['password'])) {
            str_replace('Password', 'contrase&ntilde;a', form_error('password'));
            $error_Pass = isset($errors['password']) ? '<p class="errorText">' . $errors['password'] . '</p>' : '';
        }
        ?>

        <?php
        if (isset($_GET['register']) && $_GET['register'] == "ok") {
            ?>
            <div class="alerta" style="font-weight: normal; margin-bottom: 15px;">
                Se ha registrado correctamente. Por favor revise su buz&oacute;n de correo electr&oacute;nico para activar su cuenta.
            </div>
            <?php
        }
        ?>

         <?php
        if (isset($_GET['activate']) && $_GET['activate'] == "ok") {
            ?>
            <div class="alerta" style="font-weight: normal; margin-bottom: 15px;">
                Su cuenta se ha activado correctamente. Ya puede ingresar con su correo electr&oacute;nico y contrase&ntilde;a.
            </div>
            <?php
        }
        ?>

        <div class="row">
            <input type="text" name="login" id="login" value="<?= $name_User ?>" placeholder="Usuario o correo electr&oacute;nico" <?php
            if ($error_User) {
                echo 'class="error"';
            }
            ?> /><?= $error_User ?>
        </div>

        <div class="row">
            <input type="password" name="password" id="password" value="<?= $pass_User ?>" placeholder="Contrase&ntilde;a" <?php
                   if ($error_Pass) {
                       echo 'class="error"';
                   }
            ?> /><?= $error_Pass ?>
        </div>

        <div class="row">
            <a href="#" data-reveal-id="myModal" style="margin-left:135px;">Olvide mi contrase&ntilde;a</a>
        </div>

        <div class="row">
            <input type="submit" name="entrar" id="entrar" value="Ingresar" />
        </div>

        <div class="row">
            <span style="width: 274px;">
                ¿A&uacute;n no estas registrado&quest; <a href="/auth/register">Registrarse</a>
            </span>
        </div>
    </div>
</form>


<div id="myModal" class="reveal-modal">
    <div class="content" style="text-align: center;">
        <div class="alert_ok okText"></div>

        <form action="#" method="post">
            Ingrese su nombre de usuario o correo electr&oacute;nico:<br />
            <input type="text" name="correo_electronico" value="" style="width:285px !important; padding:5px !important; margin: 5px !important;" />
            <input type="submit" name="aceptar" value="Aceptar" class="button_new" />
            <input type="button" name="cancelar" value="Cancelar" class="button_new" />

            <img src="<?= base_url() ?>/images/ajax-loader.gif" height="10" id="img_loader" style="display: none;" />
        </form>

        <div class="alert_error errorText"></div>
    </div>
</div>

<?php require_once 'application/views/footer.php'; ?>