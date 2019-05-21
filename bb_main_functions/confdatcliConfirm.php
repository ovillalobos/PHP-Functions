<?php echo($procesarAjax);?>

<h1>Claves de validaci&oacute;n </h1>
<hr noshade="noshade" size="4" />

<br><br>
<?php
$errorD = consulta_ValidaToken($session->variable( "r_usr" ), $parametros["token"]);
if ($errorD != "")
{
	echo ($errorD);
}
else
{
?>
<TABLE border="0" width="90%">
<TR><TD>
</TD></TR>
<!-- NEX Inicio DatCli 17Jun2010-->
<p>Ingrese los c&oacute;digos de validaci&oacute;n que recibi&oacute; por correo electr&oacute;nico y presione Aceptar para confirmar su direcci&oacute;n de correo y/o tel&eacute;fono celular</p>
<!-- NEX Fin DatCli 17Jun2010-->
<TR><TD>
<hr noshade="noshade" size="1"/>
</TD></TR>
<TR><TD>
&nbsp;<br>
</TD></TR>
<TR><TD>
<TABLE border="0" width="70%">
<?php 
	if (strpos($session->variable( "r_dcl" ), "all") != 0 )
	{?>
<TR>
	<TD align="center">
		<h2>Validar Correo Electr&oacute;nico</h2>
	</TD>
	<TD align="center">
		<h2>Validar Tel&eacute;fono Celular</h2>
	</TD>
</TR>
<TR>
	<TD align="center">
		<input type='text' name='cemail' id='cemail' class='inputbox'  size='8'  maxlength='6'  value=''>
	</TD>
	<TD align="center">
		<input type='text' name='cphone' id='cphone' class='inputbox'  size='8'  maxlength='6'  value=''>
	</TD>
</TR>
<?php }
else if (strpos($session->variable( "r_dcl" ), "mail") != 0 )
{
?>
<TR>
	<TD align="center">
		<h2>Validar Correo Electr&oacute;nico</h2>
	</TD>
	<TD align="center">
		
	</TD>
</TR>
<TR>
	<TD align="center">
		<input type='text' name='cemail' id='cemail' class='inputbox'  size='8'  maxlength='6'  value=''>
	</TD>
	<TD align="center">
		<input type='hidden' name='cphone' id='cphone' class='inputbox'  size='8'  maxlength='6'  value=''>
	</TD>
</TR>
<?php }
else if (strpos($session->variable( "r_dcl" ), "tel") != 0 )
{
?>
<TR>
	<TD align="center">
		
	</TD>
	<TD align="center">
		<h2>Validar Tel&eacute;fono Celular</h2>
	</TD>
</TR>
<TR>
	<TD align="center">
		<input type='hidden' name='cemail' id='cemail' class='inputbox'  size='8'  maxlength='6'  value=''>
	</TD>
	<TD align="center">
		<input type='text' name='cphone' id='cphone' class='inputbox'  size='8'  maxlength='6'  value=''>
	</TD>
</TR>
<?php }?>
<TR>
	<TD align="center">
		&nbsp;
	</TD>
	<TD align="center">
		&nbsp;
	</TD>
</TR>
<TR>
	<TD align="center" colspan="2">
	<input type="button" onclick="javascript:procesarAjax( '<?php echo($AccessBoton);?>');" value="Aceptar">
	</TD>
</TR>
</TABLE>


</TD></TR>
<TR><TD align="center">
</TD></TR>
<TR><TD>
&nbsp;<br>
</TD></TR>
<TR><TD>

</TD></TR>
<TR><TD>
<p><FONT COLOR="RED">Estimado Usuario recibir&aacute; un correo electr&oacute;nico con la confirmaci&oacute;n de que sus datos han sido actualizados.</FONT></p>
</TD></TR>
</TABLE>
<?php
}
?>
