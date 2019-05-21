<?php 
include_once( "classes/INIFile.php" );
include_once( "classes/eztemplate.php" );
include_once( "eztransaccion/classes/eztransaccion.php" );
include_once( "ezsession/classes/ezsession.php" );
include_once( "ezuserbb/classes/ezuser.php" );

$session =& eZSession::globalSession();

if( !$session->fetch() )
	$session->store();

$ini =& $GLOBALS[ "GlobalSiteIni" ];
$ini =& INIFile::globalINI();

$host = $ini->read_var( "site", "RSAHost" );
$port = $ini->read_var( "site", "RSAPort" );

$user =& eZUserBB::currentUser();
$backend = "gw" . $ini->read_var( "site", "ServerNumber" ) . $ini->read_var( "site", "DomainPostfix" );

if ( $user )
{
	?>
	<h1>Activaci&oacute;n de la Llave ASB (Acceso Seguro Baj&iacute;o)</h1>
	<hr noshade="noshade" size="4" />
	<br />
	<?php
	if ( !isset( $_POST['Access'] ) )
		$_POST['Access'] = "FrAc";
	$tr = new eZTransaccion( );
	$usr = $session->variable( "r_usr" );
	$qki = $session->variable( "r_qki" );
	$priv = $session->variable( "r_priv" );
	$transaction_buffer = "";
	switch ( $_POST['Access'] )
	{
		case "":
		case "FrAc":
		    if ( $session->variable( "r_tkact" ) == "False" )
		    {
			?>
			<form method="POST">
				<input type="HIDDEN" name="Access" value="Process">
				<table align="CENTER" border="0" cellspacing="0" cellpadding="0" width="85%">
					<tr>
						<td>
							<ul type="DISC">
								<li>Para activar su <b>Llave ASB</b>, ejecute los siguientes pasos:</li>
								<ol>
									<li>Registre el No. de Serie de su <b>Llave ASB</b>, el cual est&aacute; formado de 8 a 12 d&iacute;gitos y se encuentra en la parte posterior de la misma.</li>
									<li>Capture un NIP de 4 d&iacute;gitos a su elecci&oacute;n (&uacute;nicamente n&uacute;meros), para usarse en combinaci&oacute;n con su <b>Llave ASB</b>.</li>
									<li>Confirme su NIP.</li>
								</ol>
								</li>
								<li>El NIP no ser&aacute; desplegado en pantalla, solamente aparecer&aacute;n asteriscos.</li>
								<li>Presione el bot&oacute;n <em>Aceptar</em> para realizar la activaci&oacute;n.</li>
								<li>El bot&oacute;n <em>Aceptar</em> ser&aacute; habilitado cuando los datos est&eacute;n completos.</li>
							</ul>
							<hr size="1" noshade>
						</td>
					</tr>
					<tr>
						<td align="CENTER">
							<table align="CENTER" border="0" cellspacing="0" cellpadding="0">
								<tr>
									<th>No. de Serie</th>
									<th>&nbsp;</th>
									<th>NIP</th>
									<th>&nbsp;</th>
									<th>Confirma tu NIP</th>
								</tr>
								<tr>
									<td align="CENTER"><input align="LEFT" id="Serial" name="Serial" maxlength="12" onkeyup="javascript:{ if ( ( document.getElementById( 'Serial' ).value.length >= 8 ) && ( document.getElementById( 'NIP' ).value.length == 4 ) && ( document.getElementById( 'ConfirmNIP' ).value.length == 4 ) ) document.getElementById( 'OK' ).disabled = false; else document.getElementById( 'OK' ).disabled = true; }" size="12" type="TEXT"></td>
									<td>&nbsp;&nbsp;&nbsp;</td>
									<td align="CENTER"><input align="LEFT" id="NIP" maxlength="4" name="PIN" onkeyup="javascript:{ if ( ( document.getElementById( 'Serial' ).value.length >= 8 ) && ( document.getElementById( 'NIP' ).value.length == 4 ) && ( document.getElementById( 'ConfirmNIP' ).value.length == 4 ) ) document.getElementById( 'OK' ).disabled = false; else document.getElementById( 'OK' ).disabled = true; }" size="4" type="PASSWORD" ></td>
									<td>&nbsp;&nbsp;&nbsp;</td>
									<td align="CENTER"><input align="LEFT" id="ConfirmNIP" maxlength="4" name="ConfirmPIN" onkeyup="javascript:{ if ( ( document.getElementById( 'Serial' ).value.length >= 8 ) && ( document.getElementById( 'NIP' ).value.length == 4 ) && ( document.getElementById( 'ConfirmNIP' ).value.length == 4 ) ) document.getElementById( 'OK' ).disabled = false; else document.getElementById( 'OK' ).disabled = true; }" size="4" type="PASSWORD" ></td>
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
			}
			else
			{
			?>
			<form method="POST">
				<input type="HIDDEN" name="Access" value="Process">
				<table align="CENTER" border="0" cellspacing="0" cellpadding="0" width="85%">
					<tr>
						<td>
							Su llave ASB se encuentra registrada como Robada, Extraviada, Bloqueada o Cancelada.
							Por favor acuda con su Ejecutivo de Cuenta.
						</td>
					</tr>
				</table>
			</form>
			<?php
			}
			break;
		case "Process":
			if
				(
						!is_numeric( $_POST['PIN'] )
					||	!is_numeric( $_POST['ConfirmPIN'] )
				)
			{
				?>
				<br />
				<p>Su <b>NIP</b> y la confirmaci&oacute;n del mismo deben estar conformados &uacute;nicamente por n&uacute;meros.</p>
				<?php
				break;
			}
			if ( $_POST['PIN'] != $_POST['ConfirmPIN'] )
			{
				?>
				<br />
				<p>Su <b>NIP</b> y la confirmaci&oacute;n del mismo son diferentes.</p>
				<?php
				break;
			}

			$ret_code = $tr->PostToRSA( $host, $port, "02", $usr, $_POST['Serial'], $_POST['PIN'], "", "", "", $transaction_buffer );
			switch ( $ret_code )
			{
				case "00":
					$session->setVariable( "r_tkact", "True" );
					?>
					<table align="CENTER" border="0" cellspacing="0" cellpadding="0" width="85%">
						<tr>
							<td>
								<p align="JUSTIFY">Su <b>Llave ASB</b> ha sido activada con &eacute;xito.</p>
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
			?>
<!--15Jun2010   ACS  Llave ASB para Circular X Modificación I-->
<script>
	//Salida por redireccion.
	setTimeout(function(){window.location='/ingresaBB/';},5000);
</script>
<!--15Jun2010   ACS  Llave ASB para Circular X Modificación F-->
			<?php
			
	}
	$tr->blog( $qki, "ActivacionASB", "", "", "", "", isset( $_POST['Serial'] ) ? $_POST['Serial'] : "" );
	$tr->PostToHost( $backend, "/IBnkIIS.dll", "Trxn=aas&Access=" . urlencode( $_POST['Access'] ) . "&CustID=".urlencode( $usr ) . "&Cadpriv=" . urlencode( $priv ) . "&Action=JustLog" . ( isset( $_POST['Serial'] ) ? "&desc1=" . urlencode( $_POST['Serial'] ) : "" ) . ( isset( $ret_code ) ? "&Error=" . urlencode( $ret_code ) : "" ) . ( isset( $transaction_buffer ) ? "&Message=" . urlencode( $transaction_buffer ) : "" ), $qki, $usr, $qki, $priv, $transaction_buffer );
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