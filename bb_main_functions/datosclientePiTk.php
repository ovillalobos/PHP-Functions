<?php echo $procesarAjax;?>

<?php 
// $errorD = consulta_ValidaToken($session->variable( "r_usr" ), $parametros["token"]);
// if ($errorD != "")
// {
	// echo ($errorD);
// }
// else
// {

$res_oper = consulta_DatoCliente($session->variable( "r_usr" ), $parametros["token"]);


?>

<h1>Informaci&oacute;n Actual</h1>
<hr noshade="noshade" size="4" />
<?
if (strpos($res_oper,"Error.") === false)
{
?>
<p>1. Si desea cambiar los datos de su tel&eacute;fono de domicilio, celular o correo electr&oacute;nico de click en el bot&oacute;n <i>Aceptar</i></p>
<?
}
?>
<TABLE border=0><TR><TD>
<?php
echo str_replace("Error.<br>","",$res_oper);
?>
</TD></TR>
<TR><TD align='left'>
<?
if (strpos($res_oper,"Error.") === false)
{
?>
<p><i>* Este campo es opcional.<br>
** Para cualquier modificaci&oacute;n de los campos no editables favor de acudir a su sucursal.</i></p>

</TD></TR>
<TR><TD align='center'>
<input type="button" name='btnAceptar' onclick="javascript:procesarAjax( '<?php echo($AccessBoton);?>');" value="Aceptar">
<?
}
?>
</TD></TR></TABLE>
<?php
//}
?>
