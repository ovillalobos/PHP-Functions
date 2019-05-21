	<?php
	include_once ( "classes/ezlog.php" );
	include_once ( "classes/ezfile.php" );
	include_once ( "ezfilemanager/classes/ezvirtualfile.php" );
	include_once ( "ezfilemanager/classes/ezvirtualfolder.php" );
	include ( "eztransaccion/user/include/httplib.php" );
	include ( "eztransaccion/user/include/tcpipnexions.php" );
//06May2010   ACS  Llave ASB para Circular X I
	include("eztransaccion/user/include/controles_javascript.inc");
//06May2010   ACS  Llave ASB para Circular X F
	include ( "xmlparser.inc" );
	include("eztransaccion/user/include/estilo.inc");
	include('eztransaccion/user/include/controles_javascript.inc');

	$ImageDir = $ini->read_var ( "eZFileManagerMain", "ImageDir" );
	$Limit = $ini->read_var ( "eZFileManagerMain", "Limit" );
	$ShowUpFolder = $ini->read_var ( "eZFileManagerMain", "ShowUpFolder" ) == "enabled";


	// ********************************************************************************************
	//ACS  - MenuContenidoAjax I
	( $parametros [ 'FrAccount' ] != "" ? $FrAccount = $parametros [ 'FrAccount' ] : 0 );
	( $parametros [ 'Accion2'   ] != "" ? $Accion2   = $parametros [ 'Accion2'   ] : 0 );
	( $parametros [ 'Cadpriv'   ] != "" ? $Cadpriv   = $parametros [ 'Cadpriv'   ] : 0 );
	( $parametros [ 'Reporte'   ] != "" ? $Reporte   = $parametros [ 'Reporte'   ] : 0 );
	( $parametros [ 'Accion'    ] != "" ? $Accion    = $parametros [ 'Accion'    ] : 0 );
	( $parametros [ 'Access'    ] != "" ? $Access    = $parametros [ 'Access'    ] : 0 );
	( $parametros [ 'CustID'    ] != "" ? $usr       = $parametros [ 'CustID'    ] : 0 );
	( $parametros [ 'MonthF'    ] != "" ? $MonthF    = $parametros [ 'MonthF'    ] : 0 );
	( $parametros [ 'Month'     ] != "" ? $Month     = $parametros [ 'Month'     ] : 0 );
	( $parametros [ 'YearF'     ] != "" ? $YearF     = $parametros [ 'YearF'     ] : 0 );
	( $parametros [ 'DayF'      ] != "" ? $DayF      = $parametros [ 'DayF'      ] : 0 );
	( $parametros [ 'File'      ] != "" ? $File      = $parametros [ 'File'      ] : 0 );
	( $parametros [ 'Trxn'      ] != "" ? $Trxn      = $parametros [ 'Trxn'      ] : 0 );
	( $parametros [ 'Year'      ] != "" ? $Year      = $parametros [ 'Year'      ] : 0 );
	( $parametros [ 'Day'       ] != "" ? $Day       = $parametros [ 'Day'       ] : 0 );
	//ACS 0 - MenuContenidoAjax F

	$transaccion_buffer = "";

	if ( !isset /*(HB AGL - Ajustes PHP5)*/ ( $Access ) || empty( $Access ) ) { $Access = "FrAc"; }

	if ( !isset /*(HB AGL - Ajustes PHP5)*/ ( $FrAccount ) ) { $FrAccount = ""; }
	if ( !isset /*(HB AGL - Ajustes PHP5)*/ ( $Reporte   ) ) { $Reporte   = ""; }
	if ( !isset /*(HB AGL - Ajustes PHP5)*/ ( $Accion    ) ) { $Accion    = ""; }
	if ( !isset /*(HB AGL - Ajustes PHP5)*/ ( $MonthF    ) ) { $MonthF    = ""; }
	if ( !isset /*(HB AGL - Ajustes PHP5)*/ ( $Month     ) ) { $Month     = ""; }
	if ( !isset /*(HB AGL - Ajustes PHP5)*/ ( $YearF     ) ) { $YearF     = ""; }
	if ( !isset /*(HB AGL - Ajustes PHP5)*/ ( $DayF      ) ) { $DayF      = ""; }
	if ( !isset /*(HB AGL - Ajustes PHP5)*/ ( $Year      ) ) { $Year      = ""; }
	if ( !isset /*(HB AGL - Ajustes PHP5)*/ ( $Day       ) ) { $Day       = ""; }
	if ( !isset /*(HB AGL - Ajustes PHP5)*/ ( $i         ) ) { $i         = ""; }

//06May2010   ACS  Llave ASB para Circular X I
$secure_site = $ini->read_var( "site", "SecureServer" ) . $ini->read_var( "site", "ServerNumber" ) . $ini->read_var( "site", "DomainPostfix" );
echo "site";
$cliente_token="
					</tr>
				</table>
			</td>
		</tr>
		<tr><td>
		<form id=token_form method=post action='/procesarAjaxMenu.php' onsubmit= \"validaTknUser('edoctacred_ajax'); return false;\">
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
								<input type=password name=token id=token_value class=inputbox  size=15 maxlength=10 onKeyPress=\"if (token_valido(event,this) == true){ if (is_Enter(event) == true){validaTknUser('edoctacred_ajax');}}else{return false}\" ></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td>
				</td>
				<td align=center>
					<input type=button name=btn_opcion id=btn_opcionA value=Aceptar class=button onmouseover=\"this.className='buttonON'\" onmouseout=\"this.className='button'\" onclick=\"if (validar_token('token_value','btn_opcionA')) { validaTknUser('edoctacred_ajax');}\" >
					<input type=button name=btn_opcion id=btn_opcionC value=Cancelar class=button onmouseover=\"this.className='buttonON'\" onmouseout=\"this.className='button'\" onclick=\"document.location.href='https://$secure_site/article/articleview/196/1/7/'\" >
				</td>
				<td align=left></td>
				<td></td>
			</tr>
		</table>
	</form>";
//06May2010   ACS  Llave ASB para Circular X F

	switch ( $Access )
	{
		case "FrAc":
			$t = new eZTemplate ( "eztransaccion/user/" . $ini->read_var ( "eZTransaccionMain", "TemplateDir" ),
								  "eztransaccion/user/intl/", $Language, "edoctacred.php" );
			$t->setAllStrings();
			$t->set_file ( array ( "edoctacred_tpl" => "edoctacred.tpl" ) );
			$particularFields = "";
			$transaccion_buffer = "";
			$ret_code = $tr->PostToHost ( $backend, "/IBnkIIS.dll", "Trxn=stm&Access=FrAc&TipoEdo=CorteC&CustID=" . // JAC MAY2011 - ADAPTACION ESTADOS DE CUENTA DE CREDITOS.
				urlencode ( $usr ) . "&Cadpriv=" .
				urlencode ( $priv ) . $particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer );
			$t->set_var ( "transaccion_buffer", $transaccion_buffer );
			$transaccion_buffer = $t->parse   ( "output", "edoctacred_tpl" );
			// $transaccion_buffer = str_replace ( "type=\"SUBMIT\"", "type=\"BUTTON\"", $transaccion_buffer );
			// $transaccion_buffer = str_replace ( "onclick=\"javascript:{ document.getElementById( 'OK' ).disabled = true; document.forms[ 0 ].submit(); }\"",
			//	"onclick=\"javascript:procesarAjax();\"", $transaccion_buffer );
				// JAC - FEB2011 - INI Cambios solicitados
				// TEMPORAL HASTA QUE SE IMPLEMENTE EL MODULO DE ESTADO DE CUENTA DE CREDITO AL CORTE.
				// $html .= "<td align=center><input id=Accion name=Accion type=radio value=Corte disabled />Al corte</td>";
				// $html .= "</tr><tr>";
				// echo "<BR/>Comienza reemplazo";
				$html = "<li>Mensual, Estado de Cuenta Oficial Mensual a partir de Enero de 2011.</li>";
				// echo "<BR/>Calcula posINI";
				$posrplINI = strpos ( $transaccion_buffer, "<!--rpl-->" );
				// echo "<BR/>Obtiene substr $posrplINI";
				$substr = substr ( $transaccion_buffer, 0, $posrplINI );
				// echo "<BR/>Calcula posFIN";
				$posrplFIN = strpos ( $transaccion_buffer, "<!--rpl-->", $posrplINI + 10 );
				// echo "<BR/>Obtiene substrCONT $posrplFIN";
				$substrCONT = substr ( $transaccion_buffer, $posrplFIN + 10, strlen ( $transaccion_buffer ) );

				$transaccion_buffer = $substr . $html . $substrCONT;

				$posASB = strpos ( $transaccion_buffer, "<!--ASB-->" );
				if ( $posASB != 0 )
				{
					$subASB_INI = substr ( $transaccion_buffer, 0, $posASB );
					$posASB_FIN = strpos ( $transaccion_buffer, "<!--ASB-->", $posASB + 10 );
					$subASB_FIN = substr ( $transaccion_buffer, $posASB_FIN + 10, strlen ( $transaccion_buffer ) );

					$transaccion_buffer = $subASB_INI . $subASB_FIN;
				}

				$html = "";
				$html .= "<td align=center><input id=Accion name=Accion type=radio value=Mensual checked />Mensual</td>";
				// $postagi = strpos ( $transaccion_buffer, "<!--rpl-->" );
				$postagi = strpos ( $transaccion_buffer, "<!--rpl-->", $posrplFIN + 10 );
				$substri = substr ( $transaccion_buffer, 0, $postagi );
				$postagf = strpos ( $transaccion_buffer, "<!--rpl-->", $postagi + 10 );
				$substrf = substr ( $transaccion_buffer, $postagf + 10, strlen( $transaccion_buffer ) );

				$transaccion_buffer = $substri . $html . $substrf;
				// JAC - FEB2011 FIN
//06May2010   ACS  Llave ASB para Circular X I
			if ($session->variable( "r_tknOp" ) == "no" )
			{
				//$transaccion_buffer=str_replace("<form method=\"post\">","<form method=\"post\" onsubmit='return false'>",$transaccion_buffer);
				$transaccion_buffer=str_replace("type=\"SUBMIT\"","type=\"BUTTON\"",$transaccion_buffer);
				$transaccion_buffer=str_replace("onclick=\"javascript:{ document.getElementById( 'OK' ).disabled = true; document.forms[ 0 ].submit(); }\"","onclick=\"javascript:procesarAjax();\"",$transaccion_buffer);
				//Se Agrega instricción para el token
				/*
				$instruccion="<LI>Es necesario capturar su <b>Clave ASB (Acceso Seguro Bajio)</b>, la cual est&aacute; formada por 10 d&iacute;gitos (4 d&iacute;gitos de su <b>NIP</b> actual m&aacute;s 6 d&iacute;gitos de su <b>Clave Din&aacute;mica</b>), para completar la transacci&oacute;n.<LI>Presione bot&oacute;n <EM>Aceptar</EM>";
				$transaccion_buffer=str_replace("<LI>Presione bot&oacute;n <EM>Aceptar</EM>",$instruccion, $transaccion_buffer);
				$transaccion_buffer=str_replace("<li>Presione el bot&oacute;n <em>Aceptar</em>",$instruccion, $transaccion_buffer);*/
			}
			else
			{
				$transaccion_buffer=str_replace("<input id=\"OK\" name=\"OK\" onclick=\"javascript:{ document.getElementById( 'OK' ).disabled = true; document.forms[ 0 ].submit(); }\" type=\"SUBMIT\" value=\"Aceptar\">",$cliente_token,$transaccion_buffer);
			}
//06May2010   ACS  Llave ASB para Circular X F
			$tr->blog ( $qki, "EstadosDeCuenta", $particularFields, "", "", "", "" );

			break;
		case "Confirm":
			echo "case confirm";
			exit(0);
			$t = new eZTemplate ( "eztransaccion/user/" . $ini->read_var ( "eZTransaccionMain", "TemplateDir" ),
								  "eztransaccion/user/intl/", $Language, "edoctacred.php" );
			$t->setAllStrings();
			$t->set_file ( array( "edoctacred_tpl" => "edoctacred.tpl" ) );
			$particularFields = "&Accion=" . $Accion;
			$transaccion_buffer = "";
			$ret_code = $tr->PostToHost ( $backend, "/IBnkIIS.dll", "Trxn=stm&Access=Confirm&CustID=" .
				urlencode ( $usr ) . "&Cadpriv=" .
				urlencode ( $priv ) . $particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer );
			$t->set_var ( "transaccion_buffer", $transaccion_buffer );
			$transaccion_buffer = $t->parse   ( "output", "edoctacred_tpl" );
			$transaccion_buffer = str_replace ( "TYPE=\"SUBMIT\"", "TYPE=\"BUTTON\"", $transaccion_buffer );
			$transaccion_buffer = str_replace ( "ONCLICK=\"DisabledButton()\"", "ONCLICK=\"javascript:procesarAjax();\"", $transaccion_buffer );
			$transaccion_buffer = str_replace ( "type=\"submit\"", "type=\"BUTTON\"", $transaccion_buffer );
			$transaccion_buffer = str_replace ( "onclick=\"javascript:{ document.getElementById( 'OK' ).disabled = true; document.forms[ 0 ].submit(); }\"",
				"onclick=\"javascript:procesarAjax();\"", $transaccion_buffer );
				// JAC - FEB2011 - INI Cambios solicitados.
				// TEMPORAL HASTA QUE SE IMPLEMENTE EL MODULO DE ESTADO DE CUENTA DE CREDITO AL CORTE.
				$html = "";
				$html .= "<li>Seleccione el mes y a&ntilde;o que requiera, s&oacute;lo puede obtener Estados de Cuenta Mensuales a partir de Enero de 2011</li>";
				$posINI = strpos ( $transaccion_buffer, "<!--rpl-->" );
				$rplINI = substr ( $transaccion_buffer, 0, $posINI );
				$posFIN = strpos ( $transaccion_buffer, "<!--rpl-->", $posINI + 10 );
				$rplFIN = substr ( $transaccion_buffer, $posFIN + 10, strlen ( $transaccion_buffer ) );

				$transaccion_buffer = $rplINI . $html . $rplFIN;

			// SE REEMPLAZA EL SELECT
			$posINI = strpos ( $transaccion_buffer, "<SELECT NAME=\"Year\">" );
			$strINI = substr ( $transaccion_buffer, 0, $posINI + 20 );
			$posFIN = strpos ( $transaccion_buffer, "</SELECT>", $posINI + 20 );
			$strFIN = substr ( $transaccion_buffer, $posFIN, strlen ( $transaccion_buffer ) );

			$startYear = 2011;
			$opt = "<OPTION VALUE=$startYear>$startYear</OPTION>";
			$year = date ( "Y" );
			while ( $startYear < $year )
			{
				$startYear = $startYear + 1;
				$opt .= "<OPTION VALUE=$startYear>$startYear</OPTION>";
			}

			$transaccion_buffer = $strINI . $opt . $strFIN;
			// JAC - FEB2011 - FIN
			// JAC MAY2011 INI - ADAPTACION ESTADOS DE CUENTA DE CREDITOS
				$posINI = strpos ( $transaccion_buffer, "<!--rpl-->" );
				$rplINI = substr ( $transaccion_buffer, 0, $posINI );
				$posFIN = strpos ( $transaccion_buffer, "<!--rpl-->", $posINI + 10 );
				$rplFIN = substr ( $transaccion_buffer, $posFIN + 10, strlen ( $transaccion_buffer ) );

				$transaccion_buffer = $rplINI . $rplFIN;
			// JAC MAY2011 FIN - ADAPTACION ESTADOS DE CUENTA DE CREDITOS
			$tr->blog ( $qki, "EstadosDeCuenta", $particularFields, "", "", "", "" );

			break;
		case "Process":
			switch ( $Accion )
			{
				case 'Corte': break;	// NO IMPLEMENTADO.
				case 'Mensual':
					$t = new eZTemplate ( "eztransaccion/user/" . $ini->read_var ( "eZTransaccionMain", "TemplateDir" ),
						                 "eztransaccion/user/intl/", $Language, "edoctacred.php" );
					$t->setAllStrings();
					$t->set_file ( array ( "edoctacred_tpl" => "edoctacred_process.tpl" ) );
					$session =& eZSession::globalSession();

					if ( !$session->fetch() )
					{
						$session->store();
					}

					if( !isset /*(HB AGL - Ajustes PHP5)*/( $FrAccount ) )
					{
						$FrAccount = "0";
					}

					$tr = new eZTransaccion();
					$usr  = $session->variable ( "r_usr" );
					$qki  = $session->variable ( "r_qki" );
					$priv = $session->variable ( "r_priv" );

					$result = 0;

					/* RAI - Inicio cambio ruta dependiendo esquema */
					$elimina_serv = $ini->read_var ( "site", "EliminaServices" ); // Si o No.
					$FrAccount = trim( $FrAccount );

					if ( strtoupper( trim ( $elimina_serv ) ) == "SI" )
					{
						$base_dir = '/var/www/repo_eec/';
					}
					else
					{
						$base_dir = '/var/www/data/';
					}
					/* RAI - Fin cambio ruta dependiendo esquema */
					$base_dir .= "edocta/paso/";	// FORMA PARTE DE LA ESTRUCTURA DEL DIRECTORIO?
					$base_dir .= $Year . "/" . $Month . "/";
					$ruta = explode ( "/",  $_SERVER['PHP_SELF'] );
					$n = count ( $ruta );
					for ( $i = 0; $i < $n - 3; $i++ )
					{
						if ( $ruta [ 3 + $i ] != "" )
						{
							$p .= $ruta [ 3 + $i ] . "/";
						}
					}
					$path = $base_dir . $p;

					$transaccion_buffer = "";
					$error_desc = "";
					$secure_site = $ini->read_var ( "site", "SecureServer"  ) .
								   $ini->read_var ( "site", "ServerNumber"  ) .
								   $ini->read_var ( "site", "DomainPostfix" );
					if ( ! $dir_handle = @opendir( $path ) )
					{
						$transaccion_buffer = "";
						$error_desc = 'access_deny';
					}
					else
					{
						$transaccion_buffer .=  "<PRE>";
						$cant_files = 0;
						if ( $path != "." )
						{
							$files = array();
							while ( $file = @readdir ( $dir_handle ) )
							{
								$pos = strpos ( $file, "-" ); // JAC 13APR2011
								if ( $file != "." && $file != ".." && $file != "ThisIsEec" && ( substr ( $file, 0, $pos ) == $FrAccount ) ) // JAC 13APR2011
								{
									$count = count( $files );
									$files [ $count ][ "key"         ] = date ( "YmdHis", filemtime ( $path . $file ) );
									$files [ $count ][ "name"        ] = $file;
									$files [ $count ][ "size"        ] = ( filesize ( $path . $file ) / 1024 );
									$files [ $count ][ "date"        ] = date ( "d/M/Y H:i:s", filemtime ( $path . $file ) );
									$files [ $count ][ "description" ] = $file;
									$files [ $count ][ "file_id"     ] = "";

									$cant_files++;
								}
							}
							if ( isset( $files ) )
							{
								if ( $cant_files == 0 )
								{
									$error_desc = 'empty';
								}
								rsort ( $files );
								$transaccion_buffer .= "<table width=85% border=0 cellspacing=0 cellpading=4 >";
								$i = 0;
								foreach ( $files as $file )
								{
									$color = ( $i % 2 ) ? "#FFFFFF" : "#EFEFF0";

									$transaccion_buffer .= "<tr>";
									// IMAGE INI
									$transaccion_buffer .= "<td class='form_grid' BGCOLOR=$color width=1% >";
									$transaccion_buffer .= "<a href=\"/access/getEdoCta/edocta/paso/$Year/$Month/" . $p .$file[ "name" ] . "\" ";
									$transaccion_buffer .= "onMouseOut=\"MM_swapImgRestore()\" ";
									$transaccion_buffer .= "onMouseOver=\"MM_swapImage('ezf" . $file [ "name" ] ."-dl','','https://$secure_site/images/downloadminimrk.gif',1)\" >";
									$transaccion_buffer .= "<img name=\"ezf" . $file [ "name" ];
									$transaccion_buffer .= "-dl\" src=\"https://$secure_site/images/downloadmini.gif\" border=0 alt=Bajar width=16 height=16 />";
									$transaccion_buffer .= "</a>";
									$transaccion_buffer .= "</td>";
									// IMAGE FIN

									// ORIGINAL FILE NAME INI
									$transaccion_buffer .= "<td class='form_grid' BGCOLOR=$color width=40%>";
									// $transaccion_buffer .= "<a href=\"/access/getEdoCta/edocta/paso/$Year/$Month/". $p . $file[ "name" ];
									$transaccion_buffer .= "<a href=\"/access/getEdoCta/edocta/paso/$Year/$Month/". $p . $file[ "name" ] . "\" ";
									$transaccion_buffer .= "onMouseOut=\"MM_swapImgRestore()\" ";
									$transaccion_buffer .= "onMouseOver=\"MM_swapImage('ezf" . $file [ "name" ] ."-dl','','https://$secure_site/images/downloadminimrk.gif',1)\" >";
									// $transaccion_buffer .= "\">";
									$transaccion_buffer .= $file[ "name" ] . "</a>";
									$transaccion_buffer .= "</td>";
									// ORIGINAL FILE NAME FIN

									// DESCRIPTION INI
									$transaccion_buffer .= "<td class='form_grid' BGCOLOR=$color width=32%>";
									$transaccion_buffer .= "<span class='small'>[" . $file[ "description" ] ."]</span>";
									$transaccion_buffer .= "</td>";
									// DESCRIPTION FIN

									// FILE SIZE INI
									$transaccion_buffer .= "<td class='form_grid' BGCOLOR=$color width=10%>";
									$transaccion_buffer .= sprintf( "%01.2f", $file[ "size" ] ) . " KBytes";
									$transaccion_buffer .= "</td>";
									// FILE SIZE FIN

									// FILE DATE INI
									$transaccion_buffer .= "<td class='form_grid' BGCOLOR=$color width=15%>";
									$transaccion_buffer .= $file [ "date" ];
									$transaccion_buffer .= "</td>";
									// FILE DATE FIN
									$transaccion_buffer .= "</tr>";
									$i++;
								}
								$transaccion_buffer .=  "</table>";
							}
							else
							{
								$error_desc = 'empty';
								$transaccion_buffer = "";
							}
						}
						elseif ( $path == ".")
						{
							while ( $file = readdir ( $dir_handle))
							{
									// Protect hidden files from being listed
									if ( $file [ 0 ] == '.' )
									{
										continue;
									}

									// filename - output left-justified
									$t = "<a href=\"/access/get/$file\">$file</a>";
									echo $t.substr ( $space, 0, 40 - strlen ( $file ) );

									// filesize - output right-justified
									$t = ( filesize ( $file ) / 1024 );
									$t = sprintf ( "%01.2f", $t ) . "kb ";
									echo substr ( $space, 0, 10 - strlen ( $t ) ) . $t;

									// filedate - output left-justified
									$t = date ( "d.M Y H:i:s", filemtime ( $file ) );
									echo $t.substr ( $space, 0, 20 - strlen ( $file ) );
									echo "\n";
							}
						}
						//closing the directory
						closedir ( $dir_handle );
						$transaccion_buffer .=  "</PRE>";
					}

					$t->set_var ( "transaccion_buffer", $transaccion_buffer );
					switch ( $error_desc )
					{
						case 'access_deny':
							$transaccion_buffer  = "<PRE><STRONG><FONT color=red>No fue posible obtener la lista de archivos disponibles. ";
							$transaccion_buffer .= "Por favor, intente nuevamente m&aacute;s tarde.</FONT></STRONG></PRE>";
							break;
						case 'empty':
							$transaccion_buffer = "<PRE><STRONG><FONT color=blue>No existen archivos disponibles. ";
							$transaccion_buffer .= "Por favor, intente nuevamente m&aacute;s tarde.</FONT></STRONG></PRE>"; // JAC 13ARP2011
							break;
						default:
							$transaccion_buffer = "";
							break;
					}
					$t->pparse( "output", "edoctacred_tpl" );
					break;
			}
			break;
	}
	$transaccion_buffer = str_replace ( "href=\"/transaccion/estadosdecuenta/\"", "HREF=\"#\" onclick=\"javascript:iniProcesarMenu('estadosdecuenta','')\" ", $transaccion_buffer );

	$transaccion_buffer = "<script>
							function procesarAjax()
							{
								var parametros=	'CustID:'+document.getElementsByName('CustID')[0].value+
												',Cadpriv:'+document.getElementsByName('Cadpriv')[0].value+
												',Trxn:'+document.getElementsByName('Trxn')[0].value+
												',Access:'+document.getElementsByName('Access')[0].value;

								if (document.getElementsByName('Accion')[0]!=null)
								{
									if (document.getElementsByName('Accion')[0].type!='HIDDEN' && document.getElementsByName('Accion')[0].type!='hidden')
									{
										parametros+=',Accion:'+$('input[@name=Accion]:checked').val();
									}
									else
									{
										parametros+=',Accion:'+document.getElementsByName('Accion')[0].value;
									}
								}
								if (document.getElementsByName('FrAccount')[0]!=null)
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

								if (document.getElementsByName('Day')[0]!=null)
								{
									parametros+=',Day:'+document.getElementsByName('Day')[0].options[document.getElementsByName('Day')[0].selectedIndex].value;
								}

								if (document.getElementsByName('Month')[0]!=null)
								{
									parametros+=',Month:'+document.getElementsByName('Month')[0].options[document.getElementsByName('Month')[0].selectedIndex].value;
								}

								if (document.getElementsByName('Year')[0]!=null)
								{
									parametros+=',Year:'+document.getElementsByName('Year')[0].options[document.getElementsByName('Year')[0].selectedIndex].value;
								}

								if (document.getElementsByName('DayF')[0]!=null)
								{
									parametros+=',DayF:'+document.getElementsByName('DayF')[0].options[document.getElementsByName('DayF')[0].selectedIndex].value;
								}

								if (document.getElementsByName('MonthF')[0]!=null)
								{
									parametros+=',MonthF:'+document.getElementsByName('MonthF')[0].options[document.getElementsByName('MonthF')[0].selectedIndex].value;
								}

								if (document.getElementsByName('YearF')[0]!=null)
								{
									parametros+=',YearF:'+document.getElementsByName('YearF')[0].options[document.getElementsByName('YearF')[0].selectedIndex].value;
								}

								if (document.getElementsByName('File')[0]!=null)
								{
									parametros+=',File:'+document.getElementsByName('File')[0].options[document.getElementsByName('File')[0].selectedIndex].value;
								}

								if (document.getElementsByName('Accion2')[0]!=null)
								{
									if (document.getElementsByName('Accion2')[0].type!='HIDDEN' && document.getElementsByName('Accion2')[0].type!='hidden')
									{
										parametros+=',Accion2:'+$('input:radio[name=Accion2]:checked').val();
									}
									else
									{
										parametros+=',Accion:'+document.getElementsByName('Accion2')[0].value;
									}

								}
								if (document.getElementsByName('Reporte')[0]!=null)
								{
									if (document.getElementsByName('Reporte')[0].type!='HIDDEN' && document.getElementsByName('Reporte')[0].type!='hidden')
									{
										parametros+=',Reporte:'+$('input:radio[name=Reporte]:checked').val();
									}
									else
									{
										parametros+=',Reporte:'+document.getElementsByName('Reporte')[0].value;
									}
								}

								iniProcesarMenu('edoctacred', parametros);
							}
//06May2010   ACS  Llave ASB para Circular X I



									$(document).ready(function (){
											$('#token_value').focus()
											$('form').bind('submit', function() {
												return false;
											}
										)
									});




//06May2010   ACS  Llave ASB para Circular X F
								</script>". $transaccion_buffer;
	?>