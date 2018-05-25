<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head><title>Crear una nueva contraseña</title></head>
<body>
<div>
    <p style="color:#F385B6;font-weight:bold;font-size:24px;font-family: arial;">MediaFem</p>

    <p>Para crear una nueva contraseña haga un click <a id="link_nueva_pass_anunciantes" href="<?php echo site_url('/auth/reset_password/'.$user_id.'/'.$new_pass_key); ?>">aquí</a> o visite el siguiente link: </p>

    <p><a href="<?php echo site_url('/auth/reset_password/'.$user_id.'/'.$new_pass_key); ?>"><?php echo site_url('/auth/reset_password/'.$user_id.'/'.$new_pass_key); ?></a></p>

    <br />
    El equipo de MediaFem <br />

    <a href="http://www.mediafem.com">http://www.mediafem.com </a>
</div>
</body>
</html>