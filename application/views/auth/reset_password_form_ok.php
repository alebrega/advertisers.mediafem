<?php require_once 'application/views/auth/top.php'; ?>
<body >
<div class="container textCenter" id="form_login">

    <div class="row"><span class="okText">Contrase&ntilde;a modificada con exito.</span><br />Presiona <a href="/auth/login/">aqu&iacute;</a> para continuar o espere <span id="contador" style="font-weight: bold;">6</span> segundos y sera redirigido automaticamente</div>
</div>



<?php require_once 'application/views/footer.php'; ?>

<script type="text/javascript">
var cont = 6;
var refreshIntervalId = setInterval(contador, 1000);
function contador(){
    
	//var contador = document.getElementById("contador");
	$("#contador").html(cont);
	cont--;
        if(cont < 0){
          clearInterval(refreshIntervalId);
          $(location).attr('href',"auth/login");
        }
}

</script>

</body>