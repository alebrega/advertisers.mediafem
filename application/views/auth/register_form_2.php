<?php

if ($use_username) {
    $username = array(
        'name' => 'username',
        'id' => 'username',
        'value' => set_value('username'),
        'maxlength' => $this->config->item('username_max_length', 'tank_auth'),
        'size' => 39,
        'class' => 'txt_default'
    );
}

$email = array(
    'name' => 'email',
    'id' => 'email',
    'value' => set_value('email'),
    'maxlength' => 50,
    'size' => 39,
    'class' => 'txt_default'
);

$nombre_beneficiario = array(
    'name' => 'nombre_beneficiario',
    'id' => 'nombre_beneficiario',
    'value' => set_value('nombre_beneficiario'),
    'maxlength' => 100,
    'size' => 39,
    'class' => 'txt_default'
);

$empresa = array(
    'name' => 'empresa',
    'id' => 'empresa',
    'value' => set_value('empresa'),
    'maxlength' => 100,
    'size' => 39,
    'class' => 'txt_default'
);

$telefono = array(
    'name' => 'telefono',
    'id' => 'telefono',
    'value' => set_value('telefono'),
    'maxlength' => 50,
    'size' => 40,
    'class' => 'txt_default'
);

$confirm_password = array(
    'name' => 'confirm_password',
    'id' => 'confirm_password',
    'value' => set_value('confirm_password'),
    'maxlength' => $this->config->item('password_max_length', 'tank_auth'),
    'size' => 39,
    'class' => 'txt_default'
);
$captcha = array(
    'name' => 'captcha',
    'id' => 'captcha',
    'maxlength' => 8
);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
    <head>
        <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
        <title>MediaFem para Anunciantes</title>

        <link rel="STYLESHEET" type="text/css" href="/css/pwdwidget.css?1" />
        <script src="/js/pwdwidget.js" type="text/javascript"></script>

        <?php
            require_once 'head_links.php';
            require_once 'application/views/analytics.html';
        ?>


        <script language="JavaScript" type="text/JavaScript">
            $.validator.methods.equal = function(value, element, param) {
                return value == $("#randomNumTotal").val();
            };

            jQuery.validator.addMethod("complete_url", function(val, elem) {
                if (val.length == 0) { return true; }

                if(!/^(https?|ftp):\/\//i.test(val)) {
                }

                var urlregex = new RegExp("^(http:\/\/www.|https:\/\/www.|ftp:\/\/www.|www.){1}([0-9A-Za-z]+\.)");

                return urlregex.test(val);
            });

            $(document).ready(function(){
                $("#divCaptchaImage").click(function(event){
                    $.ajax({
                        type: "POST",
                        url: "/auth/get_captcha/",
                        dataType: "json",
                        success: function(msg){
                            $("#captchaImage").val(msg.randomNum+"+"+msg.randomNum2);
                            $("#randomNumTotal").val(msg.randomNumTotal);
                        }
                    });
                });

                $.ajax({
                    type: "POST",
                    url: "/auth/get_captcha/",
                    dataType: "json",
                    success: function(msg){
                        $("#captchaImage").val(msg.randomNum+"+"+msg.randomNum2);
                        $("#randomNumTotal").val(msg.randomNumTotal);
                    }
                });

                $("#register").click(function(){
                    var codigo = $("#recaptcha_challenge_field").val();
                    var respuesta = $("#recaptcha_response_field").val();
                    $("#recaptcha_field").val(codigo);
                    $("#recaptcha_response").val(respuesta);
                });

                $("#form_register").validate ({
                    rules:{
                        'username': { required: true},
                        'email': {  required: true,email:true},
                        'password': { required: true},
                        'confirm_password': { equalTo:"#password_id", required:true},
                        'nombre_beneficiario': { required: true},
                        'recaptcha_response_field':{ required: true}
                    },
                    messages: {
                        'username':{
                            required:"Ingrese su nombre de usuario"
                        },
                        'email':{
                            required:"Ingrese su correo electr&oacute;nico",
                            email:"Ingrese una direcci&oacute;n de correo v&aacute;lida"
                        },
                        'password':{
                            required:"Ingrese su contrase&ntilde;a"
                        },
                        'confirm_password':{
                            equalTo:"Las contrase&ntilde;as no coinciden",
                            required:"Debe repetir su contrase&ntilde;a"
                        },
                        'nombre_beneficiario':{
                            required:"Ingrese el nombre completo del beneficiario"
                        },
                        'recaptcha_response_field':{
                            required:"Debe completar las 2 palabras"
                        }
                    },
                    debug: true,
                    errorElement: "label",
                    submitHandler: function(form){
                        $("#register").attr("disabled","disabled");
                        form.submit();
                    }
                });
            });

            var RecaptchaOptions = {
                theme : 'white',
                lang : 'es'
            };

            $("#recaptcha_area").css("margin-left", "20px");

            $('#cmb_country2').attr('class','new');
        </script>
    </head>

    <body>
        <?php require_once 'application/views/top_register.html'; ?>

        <table class="tabla_register">
            <tr>
                <td colspan="3" class="encabezado_bienvenida">&nbsp;Bienvenido a MediaFem para Anunciantes</td>
            </tr>
            <tr>
                <td colspan="3">&nbsp;&nbsp;Por favor, complete el siguiente formulario de solicitud</td>
            </tr>
        </table>

        <?php
        $attributes = array('id' => 'form_register');
        echo form_open($this->uri->uri_string(), $attributes);
        ?>

        <table class="tabla_register" cellpadding="3">
            <tr>
                <td colspan="3" class="encabezado">
                    &nbsp;&nbsp;Informaci&oacute;n sobre la cuenta
                </td>
            </tr>
            <tr>
                <td style="width:200px"><p>Nombre de usuario:</p></td>
                <td style="width:560px"><?php echo form_input($username); ?></td>
            </tr>
            <tr>
                <td colspan="2" style="color: red;"><?php echo form_error($username['name']); ?><?php echo isset($errors[$username['name']]) ? $errors[$username['name']] : ''; ?></td>
            </tr>
            <tr>
                <td style="width:200px"><p>Correo Electr&oacute;nico:</p></td>
                <td style="width:560px"><?php echo form_input($email); ?></td>
            </tr>
            <tr>
                <td colspan="2" style="color: red;"><?php echo form_error($email['name']); ?><?php echo isset($errors[$email['name']]) ? $errors[$email['name']] : ''; ?></td>
            </tr>
            <tr>
                <td><label for='regpwd'>Contrase&ntilde;a:</label></td>
                <td>
                    <div class='pwdwidgetdiv' id='thepwddiv'></div>
                    <script  type="text/javascript" >
                    var pwdwidget = new PasswordWidget('thepwddiv','password');
                    pwdwidget.txtWeak='débil';
                    pwdwidget.txtMedium='mediano';
                    pwdwidget.txtGood='bueno';
                    pwdwidget.enableShowMask=false;
                    pwdwidget.enableGenerate=false;
                    pwdwidget.MakePWDWidget();

                    $('#password_id').addClass('txt_default');
                    </script>
                    <noscript>
                    <div><input type='password' id='regpwd' name='password' /></div>
                    </noscript>
                </td>
            </tr>
            <tr>
                <td><p><label>Confirmar Contrase&ntilde;a:</label></p></td>
                <td><?php echo form_password($confirm_password); ?></td>
            </tr>

            <tr>
                <td>&nbsp;</td>
            </tr>

            <tr>
                <td colspan="3" class="encabezado">&nbsp;&nbsp;Informaci&oacute;n personal</td>
            </tr>

            <tr>
                <td><p>Nombre completo:</p></td>
                <td><?php echo form_input($nombre_beneficiario); ?></td>
            </tr>
            <tr>
                <td><p>Empresa (opcional):</p></td>
                <td><?php echo form_input($empresa); ?></td>
            </tr>
            <tr>
                <td><p>Pa&iacute;s:</p></td>
                <td>
                    <select id="cmb_country2" name="cmb_country" class="txt_default" style="width:222px !important;">
                        <?php
                        foreach ($paises as $pais) {
                        ?>
                            <option value="<?= $pais->id ?>"><?= $pais->descripcion ?></option>
                        <?php
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td><p>Tel&eacute;fono (opcional):</p></td>
                <td><?php echo form_input($telefono); ?></td>
            </tr>
        </table>

        <table class="tabla_captcha" style="margin-top: 30px;">
            <tr>
                <td style="width:220px" valign="top"><p>Palabras de verificaci&oacute;n:</p></td>
                <td><?php echo isset($recaptcha_html) ? $recaptcha_html : ''; ?></td>
            </tr>
            <tr>
                <td colspan="2" style="color: red;">
                    <?php echo form_error('recaptcha_response_field'); ?>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <label style="padding-left: 0px" for="recaptcha_response_field" generated="true" class="error"></label>
                </td>
            </tr>
        </table>

        <table stlye="margin-top: 0px;" class="tabla">
            <tr>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td colspan="2" align="left">
                    <input type="submit" value="Registrarse ahora" id="register" class="btn_registrarse" style="margin-bottom: 10px;" />
                </td>
            </tr>
        </table>

        <?php echo form_close(); ?>

        <?php require_once 'application/views/footer.php'; ?>
    </body>
</html>
