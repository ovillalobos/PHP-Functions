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
function procesarCancelar()
{
	procesarAjaxCan('');
}
</script>

<?php $t->pparse( "output", "datoscliente_tpl" );  ?>

<br><br>

<table width="90%" align="center" border="0" class="form_tbl">
<tr><td>
<p>1. Presione <i>Aceptar</i> si desea dar de alta sus datos actuales.</p>
<p>1. Presione <i>Cancelar</i> si desea regresar a capturar nuevamente sus datos.</p>
<p>2. Es necesario capturar su <b>Clave ASB (Acceso Seguro Bajio)</b>, la cual est&aacute; formada por 10 d&iacute;gitos (4 d&iacute;gitos
de su <b>NIP</b> actual m&aacute;s 6 d&iacute;gitos de su <b>Clave Din&aacute;mica</b>), para completar la transacci&oacute;n. </p><br>
<br><br>
<p><b>Usted est&aacute; dando de alta los siguientes datos:</b></p>
</td></tr>

<?php
	$hpharea =$parametros['hpharea'];
	$hphone	 =$parametros['hphone'];
	$cpharea =$parametros['cpharea'];
	$cphone	 =$parametros['cphone'];
	$email	 =$parametros['email'];
?>

<input type='hidden' name='hpharea' id='hpharea' value='<?php echo($hpharea);?>'>
<input type='hidden' name='hphone'  id='hphone'  value='<?php echo($hphone);?>'>
<input type='hidden' name='cpharea' id='cpharea' value='<?php echo($cpharea);?>'>
<input type='hidden' name='cphone'  id='cphone'  value='<?php echo($cphone);?>'>
<input type='hidden' name='email'   id='email'   value='<?php echo($email);?>'>

<tr><td align="center">
	<table width="50%" align="center" border="0" class="form_tbl">
	<tr><td>
		<b>Tel&eacute;fono de Domicilio:</b></td><td><b><?php echo($hpharea);?> - <?php echo($hphone);?></b>
	</td></tr>
	<tr><td>
		<b>Tel&eacute;fono Celular:</b></td><td><b><?php echo($cpharea);?> - <?php echo($cphone);?></b>
	</td></tr>
	<tr><td>
		<b>Correo Electr&oacute;nico:</b></td><td><b><?php echo($email);?></b>
	</td></tr>
	</table>
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
	<td class="form_tbl" align="center"><b> Clave ASB (Acceso Seguro Bajio): </b><input type="password" name="token" id="token" class="inputbox"  size="15" maxlength="10" onKeyPress="if (isEnter(event) == true) { procesarAjax('<?php echo($AccessBoton);?>');}else { return token_valido(event,this);}"></td><!--MAOS falta punto y coma-->
</tr>
<tr>
	<td align="center">
	<input type="button" onclick="javascript:procesarAjax( '<?php echo($AccessBoton);?>');" value="Aceptar">
	<input type="button" onclick="javascript:procesarCancelar();" value="Cancelar">
	</td>
</tr>
</table>
</td></tr>
</table>
