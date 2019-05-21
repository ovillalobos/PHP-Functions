<?php
include_once( "classes/INIFile.php" );
include_once( "classes/eztemplate.php" );
include_once( "eztransaccion/classes/eztransaccion.php" );
include_once( "ezsession/classes/ezsession.php" );

$session =& eZSession::globalSession();

if( !$session->fetch() )
	$session->store();

$ini =& $GLOBALS[ "GlobalSiteIni" ];
$ini =& INIFile::globalINI();

$host = $ini->read_var( "site", "RSAHost" );
$port = $ini->read_var( "site", "RSAPort" );
$backend = "gw" . $ini->read_var( "site", "ServerNumber" ) . $ini->read_var( "site", "DomainPostfix" );

include_once( "ezuserbb/classes/ezuser.php" );

$user =& eZUserBB::currentUser();

if ( $user )
{
	?>
	<h1>Cambio del NIP</h1>
	<hr noshade="noshade" size="4" />
	<br />
	<?php
	if ( !isset( $Access ) )
		$Access = "FrAc";
	$tr = new eZTransaccion( );
	$usr = $session->variable( "r_usr" );
	$qki = $session->variable( "r_qki" );
	$priv = $session->variable( "r_priv" );
	$transaction_buffer = "";
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
									<td align="CENTER"><input align="LEFT" id="PassCode" maxlength="10" name="PassCode" onkeyup="javascript:{ if ( ( document.getElementById( 'PassCode' ).value.length == 10 ) && ( document.getElementById( 'NIP' ).value.length == 4 ) && ( document.getElementById( 'ConfirmNIP' ).value.length == 4 ) ) document.getElementById( 'OK' ).disabled = false; else document.getElementById( 'OK' ).disabled = true; }" size="10" type="PASSWORD"></td>
									<td>&nbsp;&nbsp;&nbsp;</td>
									<td align="CENTER"><input align="LEFT" id="NIP" maxlength="4" name="PIN" onkeyup="javascript:{ if ( ( document.getElementById( 'PassCode' ).value.length == 10 ) && ( document.getElementById( 'NIP' ).value.length == 4 ) && ( document.getElementById( 'ConfirmNIP' ).value.length == 4 ) ) document.getElementById( 'OK' ).disabled = false; else document.getElementById( 'OK' ).disabled = true; }" size="4" type="PASSWORD" ></td>
									<td>&nbsp;&nbsp;&nbsp;</td>
									<td align="CENTER"><input align="LEFT" id="ConfirmNIP" maxlength="4" name="ConfirmPIN" onkeyup="javascript:{ if ( ( document.getElementById( 'PassCode' ).value.length == 10 ) && ( document.getElementById( 'NIP' ).value.length == 4 ) && ( document.getElementById( 'ConfirmNIP' ).value.length == 4 ) ) document.getElementById( 'OK' ).disabled = false; else document.getElementById( 'OK' ).disabled = true; }" size="4" type="PASSWORD" ></td>
								</tr>
								<tr>
									<td colspan="5">&nbsp;</td>
								</tr>
								<tr>
									<td colspan="5" align="CENTER"><input disabled="true" id="OK" name="OK" onclick="javascript:{ document.getElementById( 'OK' ).disabled = true; document.forms[ 0 ].submit(); }" type="SUBMIT" value="Aceptar"></td>
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
			$ret_code = $tr->PostToRSA( $host, $port, "01", $usr, "", substr( $PassCode, 0, 4 ), $PIN, substr( $PassCode, 4, 6 ), "" ,$transaction_buffer );
			switch ( $ret_code )
			{
				case "00":
					?>
					<table align="CENTER" border="0" cellspacing="0" cellpadding="0" width="85%">
						<tr>
							<td>
								<p align="JUSTIFY">Su <b>NIP</b> ha sido cambiada con &eacute;xito.</p>
								<p align="JUSTIFY"><em>*Nota: Recuerde que para poder realizar una transacci&oacute;n que implique su <b>Clave ASB</b>, tiene que esperar a que cambie autom&aacute;ticamente su <b>Clave Din&aacute;mica</b> ya que &eacute;sta no puede ser utilizada m&aacute;s de una ocasi&oacute;n.</p>
							</td>
						</tr>
					</table>
					<?php
					break;
				case "02":
					?>
					<table align="CENTER" border="0" cellspacing="0" cellpadding="0" width="85%">
						<tr>
							<td>
								<p align="JUSTIFY">El N&uacute;mero de Serie no coincide con el que le asignaron en sucursal.</p>
							</td>
						</tr>
					</table>
					<?php
					break;
				case "03":
					?>
					<table align="CENTER" border="0" cellspacing="0" cellpadding="0" width="85%">
						<tr>
							<td>
								<p align="JUSTIFY">Su <b>Clave ASB</b> es Incorrecta.</p>
								<p align="JUSTIFY">Favor de volverlo a intentar nuevamente.</p>
							</td>
						</tr>
					</table>
					<?php
					break;
				case "08":
					?>
					<table align="CENTER" border="0" cellspacing="0" cellpadding="0" width="85%">
						<tr>
							<td>
								<p align="JUSTIFY">Su <b>Llave ASB</b> ya fue activada.</p>
							</td>
						</tr>
					</table>
					<?php
					break;
				default:
					?>
					<table align="CENTER" border="0" cellspacing="0" cellpadding="0" width="85%">
						<tr>
							<td>
								<p align="JUSTIFY">Su transacci&oacute;n no pudo ser completada.</p>
								<p align="JUSTIFY">Favor de volverlo a intentar m&aacute;s tarde.</p>
								<p align="JUSTIFY">C&oacute;digo: (<?php echo( $ret_code ) ?>).</p>
							</td>
						</tr>
					</table>
					<?php
			}
	}
	$tr->blog( $qki, "NIPASB", "", "", "", "", isset( $Serial ) ? $Serial : "" );
	$tr->PostToHost( $backend, "/IBnkIIS.dll", "Trxn=pas&Access=" . urlencode( $Access ) . "&CustID=".urlencode( $usr ) . "&Cadpriv=" . urlencode( $priv ) . "&Action=JustLog" . ( isset( $Serial ) ? "&desc1=" . urlencode( $Serial ) : "" ) . ( isset( $ret_code ) ? "&Error=" . urlencode( $ret_code ) : "" ) . ( isset( $transaction_buffer ) ? "&Message=" . urlencode( $transaction_buffer ) : "" ), $qki, $usr, $qki, $priv, $transaction_buffer );
	$transaction_buffer = "";
}
else
{
	$t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ), "eztransaccion/user/intl/", $Language, "userlogin.php" );
	$t->setAllStrings();
	$t->set_file( array( "user_login_tpl" => "userlogin.tpl" ) );
	$t->set_var( "redirect_url", "/transaccion/traspaso/" );
	$t->pparse( "output", "user_login_tpl" );
}
?>