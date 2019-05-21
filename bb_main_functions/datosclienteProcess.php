<?php echo($procesarAjax);?>


<?php $t->pparse( "output", "datoscliente_tpl" );  ?>
<TABLE border=0><TR><TD>
<?php

echo( muestra_DatoCliente($session->variable( "r_usr" ), $parametros["token"], $parametros["hpharea"], $parametros["hphone"], $parametros["cpharea"], $parametros["cphone"], $parametros["email"] ) );

?>
</TD></TR></TABLE>

