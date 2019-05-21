<?php
//06May2010   ACS  Llave ASB para Circular X I		
include("eztransaccion/user/include/controles_javascript.inc");
//06May2010   ACS  Llave ASB para Circular X F	

($parametros['Access']!=""?$Access=$parametros['Access']:0);
($parametros['CustID']!=""?$CustID=$parametros['CustID']:0);
($parametros['Apocope']!=""?$Apocope=$parametros['Apocope']:0);
($parametros['DiasPzo']!=""?$DiasPzo=$parametros['DiasPzo']:0);
($parametros['Empresa']!=""?$Empresa=$parametros['Empresa']:0);
($parametros['Cq']!=""?$Cq=$parametros['Cq']:0);
($parametros['FrAccount']!=""?$FrAccount=$parametros['FrAccount']:0);
($parametros['AgruPagIni']!=""?$AgruPagIni=$parametros['AgruPagIni']:0);



//06May2010   ACS  Llave ASB para Circular X I	

$secure_site = $ini->read_var( "site", "SecureServer" ) . $ini->read_var( "site", "ServerNumber" ) . $ini->read_var( "site", "DomainPostfix" );

$cliente_token="
					
		<form id=token_form method=post action='/procesarAjaxMenu.php' onsubmit= \"validaTknUser('ConArcPP_ajax'); return false;\">	
		<input type='hidden' id='nomFunc' name='nomFunc' value='clienteasb'>
		<input type='hidden' id='btn_opcion' name='btn_opcion'>
		<table width=98% align=center border=0 class=form_tbl>
			<tr>
				<td colspan=3 align=center>
					<table border=1 width=60% height=60px>
						<tr>
							<td colspan=2 border=1><b> Es necesario capturar su Clave ASB (Acceso Seguro Bajio)</b></td>
						</tr>
						<tr>
							<td colspan=2 align=center > 
								<b>Clave ASB (Acceso Seguro Bajio):</b> 
								<input type=password name=token id=token_value class=inputbox  size=15 maxlength=10 onKeyPress=\"if (token_valido(event,this) == true){ if (is_Enter(event) == true){validaTknUser('ConArcPP_ajax');}}else{return false}\" ></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td>
				</td>
				<td align=center>
					<input type=button name=btn_opcion id=btn_opcionA value=Aceptar class=button onmouseover=\"this.className='buttonON'\" onmouseout=\"this.className='button'\" onclick=\"if (validar_token('token_value','btn_opcionA')) { validaTknUser('ConArcPP_ajax');}\" > 
					<input type=button name=btn_opcion id=btn_opcionC value=Cancelar class=button onmouseover=\"this.className='buttonON'\" onmouseout=\"this.className='button'\" onclick=\"document.location.href='https://$secure_site/article/articleview/196/1/7/'\" > 
				</td>
				<td align=left></td>
				<td></td>
			</tr>
		</table>
	</form>";
//06May2010   ACS  Llave ASB para Circular X F


function modSubmit ($submit)
{
	
	$submit=str_replace("TYPE=SUBMIT", "type=\"button\" onclick=\"procesarAjax();\"",$submit);
	$submit=str_replace("TYPE=\"SUBMIT\"","TYPE=\"BUTTON\" ONCLICK=\"javascript:procesarAjax();\"", $submit);
	$submit=str_replace("type=\"submit\"","type=\"button\" onclick=\"procesarAjax();\"",$submit);
	$submit=str_replace("type=submit","type=\"button\" onclick=\"procesarAjax();\"",$submit);
	$submit=str_replace("select name=\"DiasPzo\"","select name=\"selectDiasPzo\"", $submit);
	$submit=str_replace("SELECT NAME=\"DiasPzo\"","select name=\"selectDiasPzo\"", $submit);
	return $submit;
}

	$procesarAjax="<script>
						function procesarAjax()
						{
							var parametros=	'CustID:'+document.getElementsByName('CustID')[0].value;
							
							if(document.getElementsByName('Access')[0]!=null)
							{
								parametros+=',Access:'+document.getElementsByName('Access')[0].value;
							}
							
							if(document.getElementsByName('FrAccount')[0]!=null)
							{
							
								if (document.getElementsByName('FrAccount')[0].type!='HIDDEN' && document.getElementsByName('FrAccount')[0].type!='hidden')
								{
									parametros+=',FrAccount:'+document.getElementsByName('FrAccount')[0].options[document.getElementsByName('FrAccount')[0].selectedIndex].value;
								}
								else
								{
									parametros+=',FrAccount:'+document.getElementsByName('FrAccount')[0].value;
								}
							}
							
							if(document.getElementsByName('AgruPagIni')[0]!=null)
							{
								parametros+=',AgruPagIni:'+document.getElementsByName('AgruPagIni')[0].value;
							}
							
							if(document.getElementsByName('DiasPzo')[0]!=null)
							{
							
								if (document.getElementsByName('DiasPzo')[0].type!='HIDDEN' && document.getElementsByName('DiasPzo')[0].type!='hidden')
								{
									parametros+=',DiasPzo:'+document.getElementsByName('DiasPzo')[0].options[document.getElementsByName('DiasPzo')[0].selectedIndex].value;
								}
								else
								{
									parametros+=',DiasPzo:'+document.getElementsByName('DiasPzo')[0].value;
								}
							}
							
							if(document.getElementsByName('selectDiasPzo')[0]!=null)
							{
								parametros+=',DiasPzo:'+document.getElementsByName('selectDiasPzo')[0].options[document.getElementsByName('selectDiasPzo')[0].selectedIndex].value;
							}
							
							if(document.getElementsByName('Empresa')[0]!=null)
							{
								parametros+=',Empresa:'+document.getElementsByName('Empresa')[0].value;
							}
							
							if(document.getElementsByName('Cq')[0]!=null)
							{
								parametros+=',Cq:'+document.getElementsByName('Cq')[0].value;
							}
							
							if(document.getElementsByName('Apocope')[0]!=null)
							{
								parametros+=',Apocope:'+document.getElementsByName('Apocope')[0].value;
							}
							
							iniProcesarMenu('ConArcPP', parametros);
							
						}
						
						
						$(document).ready(function (){ 	
											$('#token_value').focus()
											$('form').bind('submit', function() {
												return false;
											}
										)
									});
					</script>";

    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "ConArcPP.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "ConArcPP_tpl" => "ConArcPP.tpl"
        ) );


	$particularFields = "";
	$ret_code = 0;
	$sna_ret_code = 0;

	if(empty($Access)) {
		$Access = "FrAc";
	}
	$Accion  = "ConArcPP";
	switch($Access) {
	case "FrAc":
		/*
		if (isset($Access)) {
			setcookie ( "QueAccess", $Access, time() + ( 3600 * 24 * 365 ), "/",  "bb.com.mx", 0 ) or print( "Error: could not set cookie." );
		}
		*/
		$DiasPzo = "";
		$particularFields = "&Accion=".urlencode($Accion);
		$tr->blog($qki,"ConArcPP", $FrAccount, $ToAccount, $DiasPzo, $Parent1, $Accion);
		break;
	case "Confirm":
	case "Process":
		$Parent3 = "LIGA";
/*
		if (isset($Access)) {
			setcookie ( "QueAccess", $Access, time() + ( 3600 * 24 * 365 ), "/",  "bb.com.mx", 0 ) or print( "Error: could not set cookie." );
		}
*/
		$particularFields = "&Accion=".urlencode($Accion)."&DiasPzo=".urlencode($DiasPzo)."&Parent1=".urlencode($Parent1)."&Parent3=".urlencode($Parent3);
		$tr->blog($qki,"ConArcPP", $FrAccount, $ToAccount, $DiasPzo, $Parent1, $Accion);
		break;
	}
	$ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=arc&Access=".urlencode($Access)."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // intercambio de archivos de Pago a Proveedores
/*
	if (isset($DiasPzo)) {
		setcookie ( "Datos", $DiasPzo, time() + ( 3600 * 24 * 365 ), "/",  "bb.com.mx", 0 ) or print( "Error: could not set cookie." );
	}
	if (isset($Cq)) {
		setcookie ( "Cliente", $Cq, time() + ( 3600 * 24 * 365 ), "/",  "bb.com.mx", 0 ) or print( "Error: could not set cookie." );
	}
	if (isset($Apocope)) {
		setcookie ( "Quien", $Apocope, time() + ( 3600 * 24 * 365 ), "/",  "bb.com.mx", 0 ) or print( "Error: could not set cookie." );
	}
	if (isset($Empresa)) {
		setcookie ( "Que", $Empresa, time() + ( 3600 * 24 * 365 ), "/",  "bb.com.mx", 0 ) or print( "Error: could not set cookie." );
	}
	if (isset($Accion)) {
		setcookie ( "QueAccion", $Accion, time() + ( 3600 * 24 * 365 ), "/",  "bb.com.mx", 0 ) or print( "Error: could not set cookie." );
	}
*/
	$t->set_var( "transaccion_buffer", $transaccion_buffer );
	//$t->pparse( "output", "ConArcPP_tpl" );
	$transaccion_buffer=$t->parse( "output", "ConArcPP_tpl" );
//06May2010   ACS  Llave ASB para Circular X I	
//	$transaccion_buffer=modSubmit($transaccion_buffer);

	if ($Access == "FrAc" && $session->variable( "r_tknOp" ) == "si" )
	{	
		$transaccion_buffer=str_replace("<INPUT TYPE=SUBMIT NAME=\"Button\" VALUE=\"Aceptar\">",$cliente_token, $transaccion_buffer);
		$transaccion_buffer=str_replace("<INPUT TYPE=\"SUBMIT\" NAME=\"Button\" VALUE=\"Aceptar\">",$cliente_token, $transaccion_buffer);
		$transaccion_buffer=str_replace("select name=\"DiasPzo\"","select name=\"selectDiasPzo\"", $transaccion_buffer);
		$transaccion_buffer=str_replace("SELECT NAME=\"DiasPzo\"","select name=\"selectDiasPzo\"", $transaccion_buffer);
		
		//Se Agrega instricción para el token
		$instruccion="<LI>Es necesario capturar su <b>Clave ASB (Acceso Seguro Bajio)</b>, la cual est&aacute; formada por 10 d&iacute;gitos (4 d&iacute;gitos de su <b>NIP</b> actual m&aacute;s 6 d&iacute;gitos de su <b>Clave Din&aacute;mica</b>), para completar la transacci&oacute;n.<LI>Presione bot&oacute;n <EM>Aceptar</EM>";
		$transaccion_buffer=str_replace("<LI>Presione bot&oacute;n <EM>Aceptar</EM>",$instruccion, $transaccion_buffer);
		$transaccion_buffer=str_replace("<li>Presione el bot&oacute;n <em>Aceptar</em>",$instruccion, $transaccion_buffer);
	}
	else
	{
		$transaccion_buffer=modSubmit($transaccion_buffer);
	}
//06May2010   ACS  Llave ASB para Circular X I	
	$transaccion_buffer=$procesarAjax.$transaccion_buffer;



?>

