<?php echo($procesarAjax);?>



<script type="text/javascript">

$(document).ready(function()
{
	var options = {
		target:        '#mainContents',   // target element(s) to be updated with server response
		url: '<?php echo("https://" . $ini->read_var( "site", "SecureServer" ) . $ini->read_var( "site", "ServerNumber" ) . $ini->read_var( "site", "DomainPostfix" ));?>/procesarAjaxMenu.php',
		type: 'post'
	};
	$('#myForm').ajaxForm(options);
});

function esInteger( e, textbox)
{
	var charCode

	if (navigator.appName == "Netscape")
			charCode = e.which // leo la tecla que ingreso
	else
			charCode = e.keyCode // leo la tecla que ingreso

	if (charCode == 46 && textbox.value.indexOf('.')==-1)
			return true;

	if (charCode > 31 && (charCode < 48 || charCode > 57)) { // Chequeamos que sea un numero comparandolo con los valores ASCII
			return false;
	}
	return true;
}

</script>

<h1>Bienvenido</h1>
<hr noshade="noshade" size="4" />
<table   width='800px'>
	<tr>
		<td width = '300px' style='padding-top:0px;'>
			<p style='padding-top:0px;'><font color="#4e4e86"><b>Bienvenido a la parte SEGURA de BAJIONET</b></font></p>
		</td>
		<td align =left style='padding-top:15px;'width = '440px'><?php echo consulta_DatoClienteInf($session->variable( "r_usr" ));//ACS 07Jun2010 DatCli Información del cliente 
			?></td>
	</tr>
</table>
<br>
<br>
<p><b>Estimado Usuario:</b></p>
<p><b>En breve Bajionet le ofrecer&aacute; un respaldo a&uacute;n mayor para su comodidad y seguridad:</b></p>
<p><b>Notificaciones: Mayor control de los movimientos en sus cuentas mediante el aviso de transacciones, a su correo electr&oacute;nico o tel&eacute;fono celular.</b></p>
<p><b>Para activar estos servicios, por favor actualice la informaci&oacute;n que se muestra a continuaci&oacute;n. <font color="#E0212A">Banco del Baj&iacute;o hace patente su compromiso por conservar la integridad y confidencialidad de todos sus datos.</font></b></p>
<br><br>

<table width="90%" align="center" border="0" class="form_tbl">
<tr><td>

<p>1. Es necesario capturar su <b>Clave ASB (Acceso Seguro Bajio)</b>, la cual est&aacute; formada por 10 d&iacute;gitos (4 d&iacute;gitos
de su <b>NIP</b> actual m&aacute;s 6 d&iacute;gitos de su <b>Clave Din&aacute;mica</b>), para completar la transacci&oacute;n. </p><br>
</td></tr>
</table>

<hr noshade="noshade" size="1"/>
<table width="98%" align="center" border="0" class="form_tbl">
<tr><td>
<table align="center" border="1" class="form_tbl">
<tr>
	<td class="form_tbl" align="center"><b> Autorice la Transacci&oacute;n Capturando su Clave ASB (Acceso Seguro Bajio)</b></td>
</tr>
<tr>
	<td class="form_tbl" align="center"><b> Clave ASB (Acceso Seguro Bajio): </b><input type="password" name="token" id="token" class="inputbox"  size="15" maxlength="10" onKeyPress="if (isEnter(event) == true) { procesarAjax('<?php echo($AccessBoton);?>');} else {return token_valido(event,this);}"></td>
</tr>
<tr>
	<td align="center"><input type="button" name='btnAceptar' onclick="javascript:procesarAjax( '<?php echo($AccessBoton);?>');" value="Aceptar"></td>
</tr>
</table>
</td></tr>
</table>
<?php exit();?>
