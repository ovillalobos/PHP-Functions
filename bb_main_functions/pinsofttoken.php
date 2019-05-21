<?php

//NXN  29Ago2011 BBST Implementación de Bajionet Movil	Creación	
($parametros['Access']!=""?$Access=ltrim(rtrim($parametros['Access'])):0);
($parametros['PIN']!=""?$PIN=ltrim(rtrim($parametros['PIN'])):0);
($parametros['ConfirmPIN']!=""?$ConfirmPIN=ltrim(rtrim($parametros['ConfirmPIN'])):0);
($parametros['PassCode']!=""?$PassCode=ltrim(rtrim($parametros['PassCode'])):0);

$host = $ini->read_var( "site", "RSAHost" );
$port = $ini->read_var( "site", "RSAPort" );
require_once("nusoap-0.7.3/lib/nusoap.php");
include_once( "classes/ezlog.php" );
include_once("eztransaccion/user/include/sendVT.inc");
include_once("eztransaccion/user/include/general_functions.inc");
?>
	
<script type="text/javascript">
	function isEnter(e)
	{
		var characterCode;

		if(e && e.which)
		{
			e = e;
			characterCode = e.which;
		}
		else
		{
			characterCode = e.keyCode;
		}

		if(characterCode == 13)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	function procesarAjax()
	{
		
		var parametros=	'Access:'+document.getElementsByName('Access')[0].value;
		
		if(document.getElementsByName('PIN')[0]!=null)
		{
			parametros+=',PIN:'+document.getElementsByName('PIN')[0].value;
		}
		
		if(document.getElementsByName('PassCode')[0]!=null)
		{
			parametros+=',PassCode:'+document.getElementsByName('PassCode')[0].value;
		}
		
		if(document.getElementsByName('ConfirmPIN')[0]!=null)
		{
			parametros+=',ConfirmPIN:'+document.getElementsByName('ConfirmPIN')[0].value;
		}
		
		iniProcesarMenu('pinsofttoken', parametros);
	}
	
	function onUp (e)
	{
		if  ( 	( document.getElementById( 'PassCode' ).value.length == 10 ) && 
				( document.getElementById( 'NIP' ).value.length == 4 ) && 
				( document.getElementById( 'ConfirmNIP' ).value.length == 4 ) 
			) 
		{
			document.getElementById( 'OK' ).disabled = false; 
			if (isEnter(e) == true) 
			{ 
				procesarAjax();  
			}
			
		}
		else 
		{
			document.getElementById( 'OK' ).disabled = true;
		}
	}
</script>

	<h1>Cambio del NIP Soft Token</h1>
	<hr noshade="noshade" size="4" />
	<br />
	<?php
	if ( !isset( $Access ) )
		$Access = "FrAc";
		
	switch ( $Access )
	{
		case "FrAc":
			?>
			<form method="POST">
				<input type="HIDDEN" name="Access" value="Process">
				<table align="CENTER" border="0" cellspacing="0" cellpadding="0" width="85%">
					<tr>
						<td>
							<ul type="DISC">
								<li>Para cambiar su <b>NIP</b>, ejecute los siguientes pasos:</li>
								<ol>
									<li>Registre su <b>Clave ASB</b>, la cual est&aacute; formada por 10 d&iacute;gitos (4 d&iacute;gitos de su <b>NIP</b> actual m&aacute;s 6 d&iacute;gitos de su <b>Clave Din&aacute;mica</b>).</li>
									<li>Capture su nuevo <b>NIP</b> de 4 d&iacute;gitos a su elecci&oacute;n (&uacute;nicamente n&uacute;meros), para ser usados en combinaci&oacute;n con su <b>Clave Din&aacute;mica</b>.</li>
									<li>Confirme su <b>NIP</b>.</li>
								</ol>
								</li>
								<li>La <b>Clave ASB</b> y el <b>NIP</b> no ser&aacute;n desplegados en pantalla, solamente aparecer&aacute;n asteriscos.</li>
								<li>Presione el bot&oacute;n <em>Aceptar</em> para realizar el cambio.</li>
								<li>El bot&oacute;n <em>Aceptar</em> ser&aacute; habilitado cuando los datos est&eacute;n completos.</li>
							</ul>
							<hr size="1" noshade>
						</td>
					</tr>
					<tr>
						<td align="CENTER">
							<table align="CENTER" border="0" cellspacing="0" cellpadding="0">
								<tr>
									<th>Clave ASB</th>
									<th>&nbsp;</th>
									<th>Nuevo NIP</th>
									<th>&nbsp;</th>
									<th>Confirmaci&oacute;n NIP</th>
								</tr>
								<tr>
									<td align="CENTER"><input align="LEFT" id="PassCode" maxlength="10" name="PassCode" onkeyup="onUp(event)" size="10" type="PASSWORD"></td>
									<td>&nbsp;&nbsp;&nbsp;</td>
									<td align="CENTER"><input align="LEFT" id="NIP" maxlength="4" name="PIN" onkeyup="onUp(event)" size="4" type="PASSWORD" ></td>
									<td>&nbsp;&nbsp;&nbsp;</td>
									<td align="CENTER"><input align="LEFT" id="ConfirmNIP" maxlength="4" name="ConfirmPIN" onkeyup="onUp(event)" size="4" type="PASSWORD" ></td>
								</tr>
								<tr>
									<td colspan="5">&nbsp;</td>
								</tr>
								<tr>
									<td colspan="5" align="CENTER"><input disabled="true" id="OK" name="OK" onclick="javascript:{ document.getElementById( 'OK' ).disabled = true; procesarAjax(); }" type="BUTTON" value="Aceptar"></td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</form>
			<?php
			break;
		case "Process":
			if ( !is_numeric( $PassCode ) )
			{
				?>
				<br />
				<p>Su <b>Clave ASB</b> debe estar conformado &uacute;nicamente por n&uacute;meros.</p>
				<?php
				break;
			}
			if
				(
						!is_numeric( $PIN )
					||	!is_numeric( $ConfirmPIN )
				)
			{
				?>
				<br />
				<p>Su <b>NIP</b> y la confirmaci&oacute;n del mismo deben estar conformados &uacute;nicamente por n&uacute;meros.</p>
				<?php
				break;
			}
			if ( $PIN != $ConfirmPIN )
			{
				?>
				<br />
				<p>Su <b>NIP</b> y la confirmaci&oacute;n del mismo son diferentes.</p>
				<?php
				break;
			}
			
			$dat = array('operation'=>'changeUserPsw','version'=>'2.1', 'user' => strtolower($usr), 'domain' => 'BNBJ', 'currUserPassword' => $PassCode, 'newUserPassword' => $PIN, 'requestNode'=>'changeUserPswRequest');

			$ret_code = sendVTWS($dat); //Llama a función de PHP
			

			if (strlen($ret_code["hCode"]) > 3 or !is_numeric($ret_code["hCode"]) )
			{?>
				<table align="CENTER" border="0" cellspacing="0" cellpadding="0" width="85%">
				<tr>
					<td>
						<p align="JUSTIFY"><b>Error: &nbsp;</b><?php echo formatCaracter($ret_code["hDesc"]);?></p>
					</td>
				</tr>
				<tr>
					<td>
						<p align="JUSTIFY">C&oacute;digo: ( <?php echo $ret_code["hCode"];?>)</p>
					</td>
				</tr>
				</table>
			<?php
			}
			else
			{
				?>
				<table align="CENTER" border="0" cellspacing="0" cellpadding="0" width="85%">
						<tr>
							<td>
								<p align="JUSTIFY">Su <b>NIP</b> ha sido cambiada con &eacute;xito.</p>
							</td>
						</tr>
					<?php
					if ($ret_code["hCode"] != "000")						
					{
					?>
						<tr>
							<td>
								<p align="JUSTIFY">C&oacute;digo: ( <?php echo $ret_code["hCode"];?>)</p>
							</td>
						</tr>
					<?php }?>
					</table>
					
					
				<?php
				
			}
		
			
		
	}
	//$tr->blog( $qki, "NIPASB", "", "", "", "", isset( $Serial ) ? $Serial : "" );
	//$tr->PostToHost( $backend, "/IBnkIIS.dll", "Trxn=pas&Access=" . urlencode( $Access ) . "&CustID=".urlencode( $usr ) . "&Cadpriv=" . urlencode( $priv ) . "&Action=JustLog" . ( isset( $Serial ) ? "&desc1=" . urlencode( $Serial ) : "" ) . ( isset( $ret_code ) ? "&Error=" . urlencode( $ret_code ) : "" ) . ( isset( $transaction_buffer ) ? "&Message=" . urlencode( $transaction_buffer ) : "" ), $qki, $usr, $qki, $priv, $transaction_buffer );
	$transaction_buffer = "";

?>