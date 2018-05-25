<?php
$email = array(
    'name' => 'email',
    'id' => 'email',
    'value' => $email,
    'maxlength' => 50,
    'size' => 39,
    'class' => 'txt_default',
    'style' => 'width:257px !important;'
);

$nombre_beneficiario = array(
    'name' => 'nombre_beneficiario',
    'id' => 'nombre_beneficiario',
    'value' => $nombre_beneficiario,
    'maxlength' => 100,
    'size' => 39,
    'class' => 'txt_default'
);

$empresa = array(
    'name' => 'empresa',
    'id' => 'empresa',
    'value' => $empresa,
    'maxlength' => 100,
    'size' => 39,
    'class' => 'txt_default'
);

$telefono = array(
    'name' => 'telefono',
    'id' => 'telefono',
    'value' => $telefono,
    'maxlength' => 50,
    'size' => 39,
    'class' => 'txt_default'
);

$direccion = array(
    'name' => 'direccion',
    'id' => 'direccion',
    'value' => $direccion,
    'maxlength' => 50,
    'size' => 39,
    'class' => 'txt_default'
);

$codigo_postal = array(
    'name' => 'codigo_postal',
    'id' => 'codigo_postal',
    'value' => $codigo_postal,
    'maxlength' => 50,
    'size' => 39,
    'class' => 'txt_default'
);

$ciudad = array(
    'name' => 'ciudad',
    'id' => 'ciudad',
    'value' => $ciudad,
    'maxlength' => 50,
    'size' => 39,
    'class' => 'txt_default'
);

$provincia = array(
    'name' => 'provincia',
    'id' => 'provincia',
    'value' => $provincia,
    'maxlength' => 50,
    'size' => 39,
    'class' => 'txt_default'
);

$password = array(
    'name' => 'password',
    'id' => 'password',
    'value' => set_value('password'),
    'maxlength' => $this->config->item('password_max_length', 'tank_auth'),
    'size' => 30,
    'style' => "width: 257px"
);
$confirm_password = array(
    'name' => 'confirm_password',
    'id' => 'confirm_password',
    'value' => set_value('confirm_password'),
    'maxlength' => $this->config->item('password_max_length', 'tank_auth'),
    'size' => 30,
    'style' => "width: 257px"
);
$captcha = array(
    'name' => 'captcha',
    'id' => 'captcha',
    'maxlength' => 8,
);
?>

<?php require_once 'application/views/auth/top.php'; ?>

<style type="text/css">
    table tr td{ padding: 5px 0 !important; }
</style>

<script src="<?= base_url() ?>js/jquery.validate.js" type="text/javascript"></script>

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

        $('#cmb_country2').change(function(){
            var moneda = $('#cmb_country2').find(':selected').attr('data-moneda');

            if(moneda == 'USD'){
                $('#cmb_moneda').html('<option value="USD">USD</option>');
            }else{
                $('#cmb_moneda').html('<option value="' + moneda + '">' + moneda + '</option><option value="USD">USD</option>');
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
                'email': {  required: true,email:true},
                'password': { required: true},
                'confirm_password': { equalTo:"#password", required:true},
                'nombre_beneficiario': { required: true},
                'direccion': { required: true},
                'codigo_postal': { required: true},
                'ciudad': { required: true},
                'provincia': { required: true},
                'recaptcha_response_field':{ required: true}
            },
            messages: {
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
                'direccion':{
                    required:"Ingrese una direcci&oacute;n v&aacute;lida"
                },
                'codigo_postal':{
                    required:"Ingrese un c&oacute;digo postal v&aacute;lido"
                },
                'ciudad':{
                    required:"Ingrese una ciudad v&aacute;lida"
                },
                'provincia':{
                    required:"Ingrese una provincia v&aacute;lida"
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

    $('#cmb_country2').change();

    $("#recaptcha_area").css("margin-left", "20px");

    $('#cmb_country2').attr('class','new');
</script>

<table>
    <tr>
        <td><h2>Bienvenido a MediaFem para Anunciantes</h2></td>
    </tr>
    <tr>
        <td><b>Por favor, complete el siguiente formulario de solicitud</b></td>
    </tr>
    <tr>
        <td>&nbsp;</td>
    </tr>
</table>

<?php
$attributes = array('id' => 'form_register');
echo form_open($this->uri->uri_string(), $attributes);
?>

<table style="width: 100%">
    <tr>
        <td colspan="2">
            <b>Informaci&oacute;n sobre la cuenta</b>
        </td>
    </tr>
    <tr>
        <td style="width:200px"><p>Correo Electr&oacute;nico:</p></td>
        <td>
            <?php echo form_input($email); ?>
            <label for="email" generated="true" class="error" style="color:red;border: 0;font-weight: normal;display: inline;font-size: 12px;">
                <?php echo form_error($email['name']); ?><?php echo isset($errors[$email['name']]) ? $errors[$email['name']] : ''; ?>
            </label>
        </td>
    </tr>
    <tr>
        <td><p>Contrase&ntilde;a:</p></td>
        <td>
            <?php echo form_password($password); ?>
            <label for="password" generated="true" class="error" style="color:red;border: 0;font-weight: normal;display: inline;font-size: 12px;"></label>
        </td>
    </tr>
    <tr>
        <td><p>Confirmar Contrase&ntilde;a:</p></td>
        <td><?php echo form_password($confirm_password); ?>
            <label for="confirm_password" generated="true" class="error" style="color:red;border: 0;font-weight: normal;display: inline;font-size: 12px;"></label>
        </td>
    </tr>
    <tr>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td colspan="2">
            <b>Informaci&oacute;n personal.</b>
        </td>
    </tr>
    <tr>
        <td><p>Nombre completo:</p></td>
        <td>
            <?php echo form_input($nombre_beneficiario); ?>
            <label for="nombre_beneficiario" generated="true" class="error" style="color:red;border: 0;font-weight: normal;display: inline;font-size: 12px;"></label>
        </td>
        </td>
    </tr>
    <tr>
        <td><p>Empresa (opcional):</p></td>
        <td><?php echo form_input($empresa); ?></td>
    </tr>
    <tr>
        <td><p>Direcci&oacute;n:</p></td>
        <td>
            <?php echo form_input($direccion); ?>
            <label for="direccion" generated="true" class="error" style="color:red;border: 0;font-weight: normal;display: inline;font-size: 12px;"></label>
        </td>
    </tr>
    <tr>
        <td><p>C&oacute;digo postal:</p></td>
        <td>
            <?php echo form_input($codigo_postal); ?>
            <label for="codigo_postal" generated="true" class="error" style="color:red;border: 0;font-weight: normal;display: inline;font-size: 12px;"></label>
        </td>
    </tr>
    <tr>
        <td><p>Ciudad:</p></td>
        <td>
            <?php echo form_input($ciudad); ?>
            <label for="ciudad" generated="true" class="error" style="color:red;border: 0;font-weight: normal;display: inline;font-size: 12px;"></label>
        </td>
    </tr>
    <tr>
        <td><p>Provincia:</p></td>
        <td>
            <?php echo form_input($provincia); ?>
            <label for="provincia" generated="true" class="error" style="color:red;border: 0;font-weight: normal;display: inline;font-size: 12px;"></label>
        </td>
    </tr>
    <tr>
        <td><p>Pa&iacute;s:</p></td>
        <td>
            <select id="cmb_country2" name="cmb_country" class="txt_default" style="width:271px !important;">
                <?php
                foreach ($paises as $pais) {
                    $selected = '';
                    if($pais->id == $cmb_country)
                        $selected = 'selected="selected"';
                    ?>
                    <option value="<?= $pais->id ?>" data-moneda="<?= $pais->moneda ?>" <?= $selected ?>><?= $pais->descripcion ?></option>
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

    <tr style="display: none;">
        <td><p>Moneda:</p></td>
        <td>
            <select id="cmb_moneda" name="cmb_moneda" class="txt_default" style="width:271px !important;">
            </select>
        </td>
    </tr>
</table>

<table style="width: 100%">
    <tr>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td style="width:200px;vertical-align: top" valign="top"><p>Palabras de verificaci&oacute;n:</p></td>
        <td style="vertical-align: top">
            <?php echo isset($recaptcha_html) ? $recaptcha_html : ''; ?>
        </td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td style="color: red;">
            <?php echo form_error('recaptcha_response_field'); ?>
            <label for="recaptcha_response_field" generated="true" class="error" style="padding-left: 0px; color:red;border: 0;font-weight: normal;display: inline;font-size: 12px;"></label>
        </td>
    </tr>
</table>

<table stlye="margin-top: 0px;" class="tabla">
    <tr>
        <td colspan="2" align="left">
            <input type="submit" value="Registrarse ahora" id="register" class="btn_registrarse" style="margin-bottom: 10px;" />
        </td>
    </tr>
</table>

<?php echo form_close(); ?>

<?php require_once 'application/views/footer.php'; ?>