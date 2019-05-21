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

<br><br>

<table width="90%" align="center" border="0" class="form_tbl">
<tr><td>
<?php $t->pparse( "output", "datoscliente_tpl" );  ?>
<p>1. Presione <i>Aceptar</i> si desea consultar sus datos actuales.</p>
<p>2. Es necesario capturar su <b>Clave ASB (Acceso Seguro Bajio)</b>, la cual est&aacute; formada por 10 d&iacute;gitos (4 d&iacute;gitos
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
	<td class="form_tbl" align="center"><b> Clave ASB (Acceso Seguro Bajio): </b><input type="password" name="token" id="token" class="inputbox"  size="15" maxlength="10" onKeyPress="if (isEnter(event) == true) { procesarAjax('<?php echo($AccessBoton);?>');} else {return token_valido(event,this);}" ></td>
</tr>
<tr>
	<td align="center"><input type="button" onclick="javascript:procesarAjax( '<?php echo($AccessBoton);?>');" value="Aceptar"></td>
</tr>
</table>
</td></tr>
</table>
