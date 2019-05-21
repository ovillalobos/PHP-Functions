	<?php
	header('Content-Type: application/force-download');
	$session->setVariable("calendario",$session->variable("calendario")+1);
	//$Calendario = $ini->read_var("site", "Calendario");
	require_once("nusoap-0.7.3/lib/nusoap.php");
	include_once( "classes/ezlog.php" );
	include_once( "classes/ezfile.php" );
	include_once( "ezfilemanager/classes/ezvirtualfile.php" );
	include_once( "ezfilemanager/classes/ezvirtualfolder.php" );
	include_once( "middleware/consmov/utilerias.inc" );//DBA consmov
//06May2010   ACS  Llave ASB para Circular X I
	include("eztransaccion/user/include/controles_javascript.inc");
//06May2010   ACS  Llave ASB para Circular X F
	include("eztransaccion/user/include/httplib.php");		// JAC 05NOV2010
	include("eztransaccion/user/include/tcpipnexions.php"); // JAC 05NOV2010
	include("eztransaccion/user/include/xmlparser.inc");		// JAC 05NOV2010
	include("eztransaccion/user/include/edosCtas.inc");
	$blackList=false;
	$error=false;
	$ImageDir = $ini->read_var( "eZFileManagerMain", "ImageDir" );
	$Limit = $ini->read_var( "eZFileManagerMain", "Limit" );
	$ShowUpFolder = $ini->read_var( "eZFileManagerMain", "ShowUpFolder" ) == "enabled";
	$secure_site = $ini->read_var( "site", "SecureServer" ) . $ini->read_var( "site", "ServerNumber" ) . $ini->read_var( "site", "DomainPostfix" );
//echo "USUARIO: ".$ImageDir;
	// ********************************************************************************************
	//ACDP INI Mayo 2014
	function completaBuffer($servicioOrigen, $filename, $mes, $anio, $secure_site, $file_description, $la_s, $i_Server)
	{
		$buffer_temporal  = "";
		$buffer_temporal .= "<tr>";
		$buffer_temporal .= "<td class='form_grid' width=\"1%\" >";
		$buffer_temporal .= "<img src=\"https://$secure_site/images/file.gif\" border=\"0\" alt=\"\" width=\"16\" height=\"16\" border=\"0\" />";
		$buffer_temporal .= "</td>";
		$buffer_temporal .= "<td class='form_grid' width=\"40%\">$filename</td>";
		$buffer_temporal .= "<td class='form_grid' width=\"56%\"><span class=\"small\">$file_description</span></td>";
		$buffer_temporal .= "<td class='form_grid' width=\"1%\">&nbsp;</td>";
		$buffer_temporal .= "<td class='form_grid' width=\"1%\">";
		$buffer_temporal .= "<a onClick=\"getArchivoEstadoCuentaVista('$servicioOrigen','$filename')\" onMouseOut=\"MM_swapImgRestore()\"";
		$buffer_temporal .= "onMouseOver=\"MM_swapImage('ezf$filename-dl','','https://$secure_site/images/downloadminimrk.gif',1)\">";
		$buffer_temporal .= "<img style='cursor:pointer' name=\"ezf$filename-dl\" border=\"0\" src=\"https://$secure_site/images/downloadmini.gif\" width=\"16\" height=\"16\" align=\"top\" alt=\"Descargar\" /></a>";
		$buffer_temporal .= "</td>";
		$buffer_temporal .= "</tr>";
		$buffer_temporal .= "<tr><td colspan=\"5\">&nbsp;</td></tr>";
		return $buffer_temporal;
	}
	//ACDP FIN Mayo 2014
	// DGM I
	function Month2mes($mes)
	{
		switch ($mes)
		{
			case "Jan":
				return "Ene";
			case "Apr":
				return "Abr";
			case "Aug":
				return "Ago";
			case "Dec":
				return "Dic";
			default:
				return $mes;
		}
		return $mes;
	}
	function ultimoDia( $mes, $anio )
	{
		//echo ("Recibi: ($mes)($anio) ");
		$ultimo_dia = 28;
		while (checkdate ($mes, $ultimo_dia, $anio) )
		{
			//echo ("en while.. $ultimo_dia");
			$ultimo_dia ++;
		}
		return ($ultimo_dia-1);
	}

	//ACS-28Ago2008 Inicio Fecha final de estado de cuenta
	// function parserHTMLtoXLS ( $HTML, $fechaEDO, $FrAccount )
	// {
	function parserHTMLtoXLS ( $HTML, $fechaEDO, $fechaEDOFin, $FrAccount )
	{
	//ACS-28Ago2008 Fin Fecha final de estado de cuenta
		//echo "estadosdecuenta.php parserHTMLtoXLS() FrAccount[" . $FrAccount . "]";

		$posFIN = strpos($HTML,"<TABLE BORDER=");
		//eZLog::writeNotice( ".ROBERT............ $posFIN");
		if ($posFIN > 0)
		{
			$HTML = substr($HTML, $posFIN);  //regresa todos los caraceres despues de la posicion indicada
			$posFIN				= strpos($HTML,"<BR/><INPUT NAME=\"Dias\"");

			if ($posFIN > 0)
			{
				$HTML = substr($HTML,0, $posFIN);
			}
			// Se le agrega el encabezado.
			//ACS-28Ago2008 Inicio Fecha final de estado de cuenta
			//$HTML = "Banco del Baj�o, S. A.\r\nEstado de Cuenta Informativo.\r\n \r\nCuenta: ".$FrAccount."\r\nMovimientos del ".$fechaEDO." al ".$fechaEDO." \r\n".$HTML;
			$HTML = "Banco del Baj�o, S. A.\r\nEstado de Cuenta Informativo.\r\n \r\nCuenta: ".$FrAccount."\r\nMovimientos del ".$fechaEDO." al ".$fechaEDOFin." \r\n".$HTML;
			//ACS-28Ago2008 Fin Fecha final de estado de cuenta
			$HTML = preg_replace("#<TABLE ([^>]+)#","",	$HTML );  //(buscar, remplazo, en_donde)    /*HB AGL*/
			$HTML = preg_replace("#<TR ([^>]+)#",	"",		$HTML );  //(buscar, remplazo, en_donde)    /*HB AGL*/
			$HTML = preg_replace("#<TD ([^>]+)#",	"",		$HTML );  //(buscar, remplazo, en_donde)    /*HB AGL*/
			$HTML = preg_replace("#<BR ([^>]+)#",	"",		$HTML );  //(buscar, remplazo, en_donde)    /*HB AGL*/
			$HTML = preg_replace("#<TH ([^>]+)#",	"",		$HTML );  //(buscar, remplazo, en_donde)    /*HB AGL*/
			$HTML = preg_replace("#<FONT ([^>]+)#",	"",	$HTML );  //(buscar, remplazo, en_donde)    /*HB AGL*/

			$HTML = preg_replace("#<TABLE>#",	"",	$HTML );  //(buscar, remplazo, en_donde)		    /*HB AGL*/
			$HTML = preg_replace("#<TR>#",	"",	$HTML );  //(buscar, remplazo, en_donde)		    /*HB AGL*/
			$HTML = preg_replace("#<TD>#",	"",	$HTML );  //(buscar, remplazo, en_donde)		    /*HB AGL*/
			$HTML = preg_replace("#<BR>#",	"",	$HTML );  //(buscar, remplazo, en_donde)		    /*HB AGL*/
			$HTML = preg_replace("#<TH>#",	"",	$HTML );  //(buscar, remplazo, en_donde)		    /*HB AGL*/
			$HTML = preg_replace("#<FONT>#",	"",	$HTML );  //(buscar, remplazo, en_donde)		    /*HB AGL*/
			$HTML = preg_replace("#<EM>#",	"",	$HTML );  //(buscar, remplazo, en_donde)		    /*HB AGL*/

			$HTML = preg_replace("#</TABLE>#",	"",		$HTML );  //(buscar, remplazo, en_donde)    /*HB AGL*/
			$HTML = preg_replace("#</TR>#",		"\r\n",	$HTML );  //(buscar, remplazo, en_donde)    /*HB AGL*/
			$HTML = preg_replace("#</TD>#",		"\t",	$HTML );  //(buscar, remplazo, en_donde)    /*HB AGL*/
			$HTML = preg_replace("#</FONT>#",		"",		$HTML );  //(buscar, remplazo, en_donde)    /*HB AGL*/
			$HTML = preg_replace("#</TH>#",		"\t",	$HTML );  //(buscar, remplazo, en_donde)    /*HB AGL*/
			$HTML = preg_replace("#</EM>#",		"",		$HTML );  //(buscar, remplazo, en_donde)    /*HB AGL*/
			$HTML = preg_replace("#<BR/#",		"",		$HTML );  //(buscar, remplazo, en_donde)    /*HB AGL*/
			$HTML = preg_replace("#>#",			"",		$HTML );  //(buscar, remplazo, en_donde)    /*HB AGL*/
			$HTML .= "*La informaci�n contenida en este archivo es exclusivamente de car�cter informativo.\r\n";
			$HTML .= "*El Estado de Cuenta Oficial lo puede obtener en su sucursal o se le har� llegar a su domicilio, dependiendo de su indicaci�n al momento de aperturar la cuenta.\r\n";

			// Se separa todo linea x linea buscando las transaciones o movimientos para sacar la palabra recibo(xx) de la columna "descripcion"

			$archivoXLS = "";
			$posINI		= 0;

	//REF WMA-05sep2008, Inicio
			//$HTML	= str_replace( "FECHA\t",			  "\r\nFECHA\tRECIBO\t",	$HTML );
			$HTML	= str_replace( "FECHA\t",			  "\r\nFECHA\tRECIBO/DOCTO\t",	$HTML );
	//REF WMA-05sep2008, Fin
			$HTML	= str_replace( "MONTO NETO\t",		  "",						$HTML );
			$HTML	= str_replace( "MONTO\tCOMISION\tIVA","COMISION\tIVA\tMONTO",	$HTML );
			$HTML	= str_replace( "SALDO\t",				  "SALDO \r\n|",			$HTML );
			$HTML	= str_replace( "No Aplica","0.0",	$HTML );	// MAT 26Dic2011 track - 209382 Comisiones e Iva por archivo debe mostrar 0.0 cuando es No Aplica


			$posFIN	= strpos($HTML,"\r");

			while ( $posFIN > 0 )
			{


				$renglon		= substr		($HTML, $posINI, $posFIN-$posINI-1	);
				$renglon		= str_replace	("NA*","0",$renglon					);
				$posTracer		= strpos		($renglon,"n�mero de cheque"		);
				$posBuscando	= strpos		($renglon,"Recibo #"				);
				$ingreso		= "no";

				if ($posTracer > 0) //encontre una cadena con el n�mero de cheque
				{
					//cuando la descripci�n viene con n�mero de cheque xxx, el excel debe mantener la descripci�n que lleg� pero sacar el n�mero
					//de cheque en la columna del recibo

					$linea  = substr($renglon,	$posTracer	    );	// Toda la linea empezando desde n�mero de cheque
					$posTab = strpos($linea,	"\t"			);	// Posicion del 1er tab despues de n�mero de cheque
					$posRec	= strpos ($linea,   "Recibo #"		);  // Posicion donde comienza la palabra Recibo #
					$recibo = substr($linea,	0, $posRec );	    //aqui traemos "n�mero de cheque 1321321\t"

					$recibo			= str_replace("n�mero de cheque"," ", $recibo); //elimino la palabra n�mero de cheque
					$posBuscando	= strpos($renglon,"\t"	);				// insertamos la palabra recibo despues de la fecha (despues del 1er tab)
					$renglon		= substr($renglon,0, $posBuscando+1).$recibo."\t".substr($renglon,$posBuscando+1);

					$ingreso = "si";

				}
				else
				{
					if ( $posBuscando > 0 ) //ya encontre una cadena "Recibo #" ahora buscamos la posicion del siguiente tabulador
					{
						$linea  = substr($renglon,	$posBuscando	);	// Toda la linea empezando desde Recibo
						$posTab = strpos($linea,	"\t"			);	// Posicion del 1er tab despues de Recibo
						$recibo = substr($linea,	0, $posTab		);	//aqui traemos "Recibo # 1321321\t"

						$renglon		= str_replace($recibo, " ", $renglon);	//eliminamos la palabra recibo xxxx del renglon y le ponemos un tab
						$recibo			= substr($recibo, 9		);				//le quitamos la palabra "Recibo #"
						$posBuscando	= strpos($renglon,"\t"	);				// insertamos la palabra recibo despues de la fecha (despues del 1er tab)
						$renglon		= substr($renglon,0, $posBuscando+1).$recibo."\t".substr($renglon,$posBuscando+1);

						$ingreso = "si";
					}
				}

				if ($ingreso == "si")
				{
					/* tenemos en el renglon
					  0     1        2         3      4      5   6    7
					fecha recibo descripcion monto comision iva neto saldo

					se pretende dejar asi
					  0     1        2           4      5     3   7
					fecha recibo descripcion  comision iva  monto saldo

					*/

					$renglon = substr($renglon,1);

					$campos	 = explode("\t", $renglon);


					if ( strlen( $campos[0] ) == 8  ) // estaba cambiado esto
					{
						$campos[0] = "0" . $campos[0];
					}

					$mesLetra = strtolower(substr($campos[0],2,3)); //feb, mar, abr.

					switch ( $mesLetra )
					{
						case "ene":
						case "jan":
									$mesLetra = "01";
									break;
						case "feb":
									$mesLetra = "02";
									break;
						case "mar":
									$mesLetra = "03";
									break;
						case "abr":
						case "apr":
									$mesLetra = "04";
									break;
						case "may":
									$mesLetra = "05";
									break;
						case "jun":
									$mesLetra = "06";
									break;
						case "jul":
									$mesLetra = "07";
									break;
						case "ago":
						case "aug":
									$mesLetra = "08";
									break;
						case "sep":
									$mesLetra = "09";
									break;
						case "oct":
									$mesLetra = "10";
									break;
						case "nov":
									$mesLetra = "11";
									break;
						case "dec":
						case "dic":
									$mesLetra = "12";
									break;
					}

					$campos[3]	= str_replace( ",",	"",	$campos[3] );
					$campos[7]	= str_replace( ",",	"",	$campos[7] );
					$campos[0] = substr($campos[0],5).$mesLetra.substr($campos[0],0,2); // formato de dd-mmm-yy aaaammdd
					$renglon = $campos[0]."\t".$campos[1]."\t".$campos[2]."\t".$campos[4]."\t".$campos[5]."\t".$campos[3]."\t".$campos[7]."\r\n";
				}

	//REF WMA-05sep2008, Fin.

				$archivoXLS .= $renglon;
				$posINI		 = $posFIN+1;
				$posFIN		 = strpos($HTML,"\r", $posINI);

			}

			$HTML = $archivoXLS;
		}
		//eZLog::writeNotice( ".ROBERT............ $HTML");
		return ( $HTML );
	}

	//JAC OCT2010 INI
	$perfil = $session->variable("r_perfil");
	//JAC OCT2010 FIN
	//ACS  - MenuContenidoAjax I
	($parametros['Reporte']!=""?$Reporte=$parametros['Reporte']:0);	// REF JAC OCT2010
	($parametros['Accion']!=""?$Accion=$parametros['Accion']:0);
	($parametros['Accion2']!=""?$Accion2=$parametros['Accion2']:0);
	($parametros['Access']!=""?$Access=$parametros['Access']:0);
	($parametros['Cadpriv']!=""?$Cadpriv=$parametros['Cadpriv']:0);
	($parametros['CustID']!=""?$usr=$parametros['CustID']:0);
	($parametros['Trxn']!=""?$Trxn=$parametros['Trxn']:0);
	($parametros['FrAccount']!=""? $FrAccount =$parametros['FrAccount']:0);


	($parametros['File']!=""?$File=$parametros['File']:0);
	($parametros['Day']!=""?$Day=$parametros['Day']:0);
	($parametros['Month']!=""?$Month=$parametros['Month']:0);
	($parametros['Year']!=""?$Year=$parametros['Year']:0);
	($parametros['DayF']!=""?$DayF=$parametros['DayF']:0);
	($parametros['MonthF']!=""?$MonthF=$parametros['MonthF']:0);
	($parametros['YearF']!=""?$YearF=$parametros['YearF']:0);

	//ACS 0 - MenuContenidoAjax F

	$transaccion_buffer = "";

	// REF JAC OTC2010
	if( !isset /*(HB AGL - Ajustes PHP5)*/( $Reporte ) ) {
		$Reporte = "";
	}
	// REF JAC OCT2010
	if( !isset /*(HB AGL - Ajustes PHP5)*/( $Access ) || empty( $Access ) ) {
		$Access = "FrAc";
	}
	if( !isset /*(HB AGL - Ajustes PHP5)*/( $Accion ) ) {
		$Accion = "";
	}
	// $Accion = "estadosdecuenta";
	if( !isset /*(HB AGL - Ajustes PHP5)*/( $i ) ) {
		$i = "";
	}
	if( !isset /*(HB AGL - Ajustes PHP5)*/( $FrAccount ) ) {
		$FrAccount = "";
	}
	if( !isset /*(HB AGL - Ajustes PHP5)*/( $Day ) ) {
		$Day = "";
	}
	if( !isset /*(HB AGL - Ajustes PHP5)*/( $Month ) ) {
		$Month = "";
	}
	if( !isset /*(HB AGL - Ajustes PHP5)*/( $Year ) ) {
		$Year = "";
	}
	if( !isset /*(HB AGL - Ajustes PHP5)*/( $DayF ) ) {
		$DayF = "";
	}
	if( !isset /*(HB AGL - Ajustes PHP5)*/( $MonthF ) ) {
		$MonthF = "";
	}
	if( !isset /*(HB AGL - Ajustes PHP5)*/( $YearF ) ) {
		$YearF = "";
	}
//06May2010   ACS  Llave ASB para Circular X I
$secure_site = $ini->read_var( "site", "SecureServer" ) . $ini->read_var( "site", "ServerNumber" ) . $ini->read_var( "site", "DomainPostfix" );

$cliente_token="
					</tr>
				</table>
			</td>
		</tr>
		<tr><td>
		<form id=token_form method=post action='/procesarAjaxMenu.php' onsubmit= \"validaTknUser('estadosdecuenta_ajax'); return false;\">
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
								<input type=password name=token id=token_value class=inputbox  size=15 maxlength=10 onKeyPress=\"if (token_valido(event,this) == true){ if (is_Enter(event) == true){validaTknUser('estadosdecuenta_ajax');}}else{return false}\" ></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td>
				</td>
				<td align=center>
					<input type=button name=btn_opcion id=btn_opcionA value=Aceptar class=button onmouseover=\"this.className='buttonON'\" onmouseout=\"this.className='button'\" onclick=\"if (validar_token('token_value','btn_opcionA')) { validaTknUser('estadosdecuenta_ajax');}\" >
					<input type=button name=btn_opcion id=btn_opcionC value=Cancelar class=button onmouseover=\"this.className='buttonON'\" onmouseout=\"this.className='button'\" onclick=\"document.location.href='https://$secure_site/article/articleview/196/1/7/'\" >
				</td>
				<td align=left></td>
				<td></td>
			</tr>
		</table>
	</form>";
//06May2010   ACS  Llave ASB para Circular X F

//REGA 28May2014 Omisi�n de clave ASB si ya se captur� una vez.
	if($Access != "FrAc"){
		$session->setVariable("Access",1);
	}
	if($Access == "FrAc" && $session->variable("Access")>0){
		$Access="Confirm";
	}
	/*echo "ACCESS: ".$Access;
	echo "<br>SESSION ACCESS: ".$session->variable("Access");*/
//REGA 28May2014 Omisi�n de clave ASB si ya se captur� una vez.

	switch( $Access )
	{
		case "FrAc":
		{
			//$t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ), "eztransaccion/user/intl/", $Language, "estadosdecuenta.php" );
			//DBA I 182332 CFD
			if ( $Accion == "Corte" )
			{
				$t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ), "eztransaccion/user/intl/", $Language, "movimientos.php" );
			}
			else //Mensual
			{
				$t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ), "eztransaccion/user/intl/", $Language, "estadosdecuenta.php" );
			}
			//$t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ), "eztransaccion/user/intl/", $Language, "movimientos.php" );
			//DBA F 182332 CFD
			$t->setAllStrings();
			$t->set_file( array( "estadosdecuenta_tpl" => "estadosdecuenta.tpl" ) );
			$particularFields = "";
			$transaccion_buffer = "";
			$ret_code = $tr->PostToHost( $backend, "/IBnkIIS.dll", "Trxn=stm&Access=FrAc&TipoEdo=" . $Accion . "&CustID=" . urlencode( $usr ) . "&Cadpriv=" . urlencode( $priv ) . $particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer );
			$t->set_var( "transaccion_buffer", $transaccion_buffer );
			//ACS  - MenuContenidoAjax I
			//$t->pparse( "output", "estadosdecuenta_tpl" );//ACS
			$transaccion_buffer=$t->parse( "output", "estadosdecuenta_tpl" );//ACS
//06May2010   ACS  Llave ASB para Circular X I
			if ($session->variable( "r_tknOp" ) == "no" )
			{
				//$transaccion_buffer=str_replace("<form method=\"post\">","<form method=\"post\" onsubmit='return false'>",$transaccion_buffer);
				$transaccion_buffer=str_replace("type=\"SUBMIT\"","type=\"BUTTON\"",$transaccion_buffer);
				$transaccion_buffer=str_replace("onclick=\"javascript:{ document.getElementById( 'OK' ).disabled = true; document.forms[ 0 ].submit(); }\"","onclick=\"javascript:procesarAjax();\"",$transaccion_buffer);
				//Se Agrega instricci�n para el token
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

			//ACS  - MenuContenidoAjax F
			$tr->blog( $qki, "EstadosDeCuenta", $particularFields, "", "", "", "" );

			break;
		}
		case "Confirm":
		{
			//DBA I 182332 CFD
			//echo "Imprimiendo en Confirm el aCCion...[$Accion]";
			if ( $Accion == "Corte" )
			{
				$t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ), "eztransaccion/user/intl/", $Language, "movimientos.php" );
			}
			else
			{
				$t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ), "eztransaccion/user/intl/", $Language, "estadosdecuenta.php" );
			}
			//DBA F 182332 CFD
			$t->setAllStrings();
			$t->set_file( array( "estadosdecuenta_tpl" => "estadosdecuenta.tpl" ) );
			$particularFields = "&Accion=" . $Accion;
			$transaccion_buffer = "";
			$ret_code = $tr->PostToHost( $backend, "/IBnkIIS.dll", "Trxn=stm&Access=Confirm&CustID=" . urlencode( $usr ) . "&Cadpriv=" . urlencode( $priv ) . $particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer );
			//JC
			if ( $Accion != "Mensual" )
			{
				//DBA consmov
				eZLog::writeNotice("xxxxCONSMOVxx3xx");
				if ( !habilitaConsmov() )
				{
				eZLog::writeNotice("CONSMOV....1");
					$postaci = strpos ($transaccion_buffer,"<!--rplc-->");
					$subc1 = substr($transaccion_buffer,0,$postaci);
					$postacf = strpos ($transaccion_buffer,"<!--rplc-->",$postaci+11);
					$subc2 = substr($transaccion_buffer,$postacf+11,strlen($transaccion_buffer));
					$transaccion_buffer = $subc1 . $subc2;
				}
					eZLog::writeNotice("CONSMOV....2");
				if ( $perfil == "permor" || $perfil == "persfis" || $perfil == "perfacem" )
				{
						//$transaccion_buffer = str_replace("<rpl>","",$transaccion_buffer);
				}
				else
				{


						$postagi = strpos ($transaccion_buffer,"<!--rpl-->");
						$sub1 = substr($transaccion_buffer,0,$postagi);
						$postagf = strpos ($transaccion_buffer,"<!--rpl-->",$postagi+10);
						$sub2 = substr($transaccion_buffer,$postagf+10,strlen($transaccion_buffer));
						$transaccion_buffer = $sub1 . $sub2;
						//Buscando 2da cadena

						$postagi = strpos ($transaccion_buffer,"<!--rpl2-->");
						$sub1 = substr($transaccion_buffer,0,$postagi);
						$postagf = strpos ($transaccion_buffer,"<!--rpl2-->",$postagi+11);
						$sub2 = substr($transaccion_buffer,$postagf+11,strlen($transaccion_buffer));
						$transaccion_buffer = $sub1 . $sub2;
						$transaccion_buffer = str_replace("Formato 1","",$transaccion_buffer);
				}

			}
			//JC
			$t->set_var( "transaccion_buffer", $transaccion_buffer );
			//ACS  - MenuContenidoAjax I
			//$t->pparse( "output", "estadosdecuenta_tpl" );
			$transaccion_buffer=$t->parse( "output", "estadosdecuenta_tpl" );
			$transaccion_buffer=str_replace("TYPE=\"SUBMIT\"","TYPE=\"BUTTON\"",$transaccion_buffer);
			$transaccion_buffer=str_replace("ONCLICK=\"DisabledButton()\"","ONCLICK=\"javascript:procesarAjax();\"",$transaccion_buffer);
			$transaccion_buffer=str_replace("type=\"submit\"","type=\"BUTTON\"",$transaccion_buffer);
			$transaccion_buffer=str_replace("onclick=\"javascript:{ document.getElementById( 'OK' ).disabled = true; document.forms[ 0 ].submit(); }\"","onclick=\"javascript:procesarAjax();\"",$transaccion_buffer);

			//ACS  - MenuContenidoAjax F
			//EMedrano NOV2013 I
			$clienteteso = $session->variable( "r_cno" );

			if($clienteteso!=2767531){
				$transaccion_buffer=str_replace("<input checked id=\"Reporte3\" name=\"Reporte\" type=\"radio\" value=\"txtTeso\" mensaje=\"Archivo tesofe.\" onMouseOver=\"mostrarAyuda('Reporte3');\" onmouseout=\"rmvAyuda();\">Archivo TESOFE"," ",$transaccion_buffer);
			}
			//EMedrano NOV2013 I
			$tr->blog( $qki, "EstadosDeCuenta", $particularFields, "", "", "", "" );
			//echo "EDDY <BR>".$transaccion_buffer;
			break;
		}
		case "Process":
		{
			//passthru( "/bin/bash /var/www/html/bajio/mountnfs.sh", $ret_code );
			// if ( $ret_code != 0 )
			// {
				// passthru( "/bin/bash /var/www/html/bajio/mountnfs.sh", $ret_code );
				// if ( $ret_code != 0 )
				// {
					// passthru( "/bin/bash /var/www/html/bajio/mountnfs.sh", $ret_code );
					// if ( $ret_code != 0 )
					// {
						// eZLog::writeNotice( "At estadosdecuenta: images.bb.com.mx:/var/www/images/ezfilemanager/files/ and/or services.bb.com.mx:/var/www/data/ are not mounted.|" );
						// sendmail( $ini->read_var( "site", "ErrEMail1" ), "Baj�oNET. Error Servidor " . $ini->read_var( "site", "ServerNumber" ), "Las Monturas en el Servidor www". $ini->read_var( "site", "ServerNumber" ) . ".bb.com.mx, No est�n Disponibles.\r\n\r\nFavor de volverlas a montar ejecutando \"umount -a\" y enseguida \"mount -a\"." );
						// sendmail( $ini->read_var( "site", "ErrEMail2" ), "Baj�oNET. Error Servidor " . $ini->read_var( "site", "ServerNumber" ), "Las Monturas en el Servidor www". $ini->read_var( "site", "ServerNumber" ) . ".bb.com.mx, No est�n Disponibles.\r\n\r\nFavor de volverlas a montar ejecutando \"umount -a\" y enseguida \"mount -a\"." );
						// $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ), "eztransaccion/user/intl/", $Language, "estadosdecuenta.php" );
						// $t->setAllStrings();
						// $t->set_file( array( "estadosdecuenta_tpl" => "estadosdecuenta.tpl" ) );
						// $transaccion_buffer = "<br />Su transacci�n no puede ser atendida por el momento.<br /><br />Favor de volver a intentar m�s tarde.<br /><br />";
						////ACS  - MenuContenidoAjax I
						////$transaccion_buffer .= "Si desea descargar otro Estado de Cuenta haga clic <a href=\"/transaccion/estadosdecuenta/\">aqu&iacute;</a>.";
						// $transaccion_buffer .= "Si desea descargar otro Estado de Cuenta haga clic <a href='#' onclick=\"iniProcesarMenu('estadosdecuenta', '');\">aqu&iacute;</a>.";
						////ACS  - MenuContenidoAjax F
						// $t->set_var( "transaccion_buffer", $transaccion_buffer );
						////ACS  - MenuContenidoAjax I
						////$t->pparse( "output", "estadosdecuenta_tpl" );
						// $transaccion_buffer=$t->parse( "output", "estadosdecuenta_tpl" );
						////ACS  - MenuContenidoAjax F
						// $tr->blog( $qki, "EstadosDeCuenta", $particularFields, "", "", "", "Mensual" );
						// unset( $ret_code );
						// break;
					// }
				// }
			// }
			unset( $ret_code );
			//eZLog::writeNotice(">>>>>>>>>>>ROBERT ACCION: ".$Accion);
			switch( $Accion )
			{
				case "Corte":
				{
					//DBA I 182332 CFD
					if ( $Accion == "Corte" )
					{
						$t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ), "eztransaccion/user/intl/", $Language, "movimientos.php" );
					}
					else
					{
						$t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ), "eztransaccion/user/intl/", $Language, "estadosdecuenta.php" );
					}
					//$t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ), "eztransaccion/user/intl/", $Language, "estadosdecuenta.php" );
					//DBA I 182332 CFD
					$t->setAllStrings();
					// JAC OCT2010 INI
					$canBreak = false;
					switch( $Reporte )
					{
						case "consmov":
						{
							$canBreak = true;
							$DayHoy		= date("d");	// 28 12 2008  (28 de diciembre del 2008)
							//$DayLetra	= strtolower(date("D"));
							$MonthHoy	= date("m");
							$YearHoy	= date("Y");

							$ctaValida	= substr($FrAccount,-1);
							if ( $ctaValida == "D")
								$cuenta   	= substr($FrAccount,0,-1);
							else
								$cuenta = $FrAccount;//cuentas agrupadas

						eZLog::writeNotice("Cuenta MAESTRA....[$cuenta]...FrAccount...[$FrAccount]");
							$FrAccount = str_replace(" ", "", $FrAccount);
							$FrAccount = str_replace("-", "", $FrAccount);

							$FrAccount = strtolower(substr($FrAccount,0, strlen($FrAccount)-1)).substr($FrAccount,-1);
							$cuenta = strtolower(substr($cuenta,0, strlen($cuenta)-1)).substr($cuenta,-1);

							$FrAccount = str_replace( "brillante", "brillan", $FrAccount );
							$FrAccount = str_replace( "clasica", "cheqsi", $FrAccount );
							$FrAccount = str_replace( "nominabasica", "ahnom", $FrAccount );
							$FrAccount = str_replace( "nomina", "ahorro", $FrAccount );

							$cuenta   = str_replace( "brillante", "brillan", $cuenta );
							$cuenta   = str_replace( "clasica", "cheqsi", $cuenta );
							$cuenta   = str_replace( "nominabasica", "ahnom", $cuenta );
							$cuenta   = str_replace( "nomina", "ahorro", $cuenta );

							eZLog::writeNotice("Cuenta MAESTRA....[$cuenta]");
							$FrAccount = urlencode( $FrAccount );
							if ( $Accion2 == "periododia" ) // Lo debe resuelve un sam exclusivo para estados de cuenta.
							{
							//echo "hola1";
								$Day	= $DayHoy;
								$Month	= $MonthHoy;
								$Year	= $YearHoy;

								$DayF   = $DayHoy;
								$MonthF = $MonthHoy;
								$YearF  = $YearHoy;
							}
							else
							{
								if ( $Month == "  " or $Year == "    ")	// error.. debe seleccionar al menos mes anio inicial
								{
									$transaccion_buffer = "Transacci&oacute;n no realizada.<br><br><b>Favor de capturar el mes y a�o iniciales</b>";
									$t->set_file( array("estadosdecuenta_tpl" => "estadosdecuenta.tpl") );
									$t->set_var( "transaccion_buffer", $transaccion_buffer );
									$transaccion_buffer=$t->parse( "output", "estadosdecuenta_tpl" );
									break;
								}
								else
								{
									if (
											( $DayF == "  " or $MonthF == "  " or $YearF == "    " ) and
											( $DayF.$MonthF.$YearF != "        " )
										)
									{
										$transaccion_buffer = "Transacci�n no realizada.<br><br><b>Favor de capturar la fecha final.</b>";
										$t->set_file( array("estadosdecuenta_tpl" => "estadosdecuenta.tpl") );
										$t->set_var( "transaccion_buffer", $transaccion_buffer );
										$transaccion_buffer=$t->parse( "output", "estadosdecuenta_tpl" );
										break;
									}

									if ($Day == "  ")
										$Day = "01";

									if ($DayF == "  ")
									{
										$DayF = ultimoDia($Month, $Year);
									}

									if ($MonthF == "  " )
										$MonthF = $Month;

									if ($YearF == "    ")
										$YearF = $Year;

									if ($YearF.$MonthF.$DayF < $Year.$Month.$Day )
									{
										$transaccion_buffer = "Transacci&oacute;n no realizada.<br><br><b>La fecha final debe ser mayor o igual que la inicial</b>";
										$t->set_file( array("estadosdecuenta_tpl" => "estadosdecuenta.tpl") );
										$t->set_var( "transaccion_buffer", $transaccion_buffer );
										$transaccion_buffer=$t->parse( "output", "estadosdecuenta_tpl" );
										break;
									}

									if (!(checkdate($Month,$Day,$Year)))
									{
										$transaccion_buffer = "Transacci&oacute;n no realizada.<br><br><b>La fecha inicial es inv&aacute;lida</b>";
										$t->set_file( array("estadosdecuenta_tpl" => "estadosdecuenta.tpl") );
										$t->set_var( "transaccion_buffer", $transaccion_buffer );
										$transaccion_buffer=$t->parse( "output", "estadosdecuenta_tpl" );
										break;
									}
									if (!(checkdate($MonthF,$DayF,$YearF)))
									{
										$transaccion_buffer = "Transacci&oacute;n no realizada.<br><br><b>La fecha final es inv&aacute;lida</b>";
										$t->set_file( array("estadosdecuenta_tpl" => "estadosdecuenta.tpl") );
										$t->set_var( "transaccion_buffer", $transaccion_buffer );
										$transaccion_buffer=$t->parse( "output", "estadosdecuenta_tpl" );
										break;
									}
								}
							}

							$ret_code = 0;
							/*$trans = $trans . "<?xml version='1.0'?>";
							$trans .= "<mensaje><trxn value='stmt'/><accion value='ConsMovsTeso'/><tipomsj value='rqs'/><CustID value='".
								$user ."'/><FrAccount value='".
								$FrAccount ."'/><Day value='".
								$Day ."'/><Month value='".
								$Month ."'/><Year value='".
								$Year ."'/><DayF value='".
								$DayF ."'/><MonthF value='".
								$MonthF ."'/><YearF value='".
								$YearF ."'/><format value='text'/></mensaje>";

							$JBoss = trim ( readParam ( "JBossHost", "site.ini" ) );
							$JBossPath = trim ( readParam( "JBossPath", "site.ini" ) );
							*/
							if ( $Day == "" )
								$Day = $_POST['Day'];
							if ( $Month == "" )
								$Month = $_POST['Month'];
							if ( $Year == "" )
								$Year = $_POST['Year'];

							eZLog::writeNotice("Dia...[".$Day."]...[$Month]....[$Year]");

							//$Day   = date ( "d" );
							//$Month = date ( "m" );
							//$Year  = date ( "Y" );

							$fechaInicial 	= $Year.$Month.$Day;
							$fechaFinal		= $YearF.$MonthF.$DayF;
							$fechaConsMov	= $fechaInicial . "-" . $fechaFinal;

							$FrAccount = str_replace ( " ", "", $FrAccount );

							//$cuentax = substr($cuenta,0,-1);

							$cuentac = preg_split('#(?<=\d)(?=[a-z])#i',$cuenta);//[0]=364307 [1]=cheqsi - 1
							$sub     = explode("-",$cuentac[1]);

							eZLog::writeNotice("CUENTA 1....[".trim($cuentac[0])."]....[".trim($sub[0])."]...[".trim($sub[1])."].");

							//eZLog::writeNotice("CUENTA.2..[".print_r($match,true)."]");

							$cuentaCM   = trim($cuentac[0])."-".trim($sub[0])."-".trim($sub[1]);
							$FrAccount2 = $FrAccount;
							$wsdl       = $ini->read_var( "site", "wsdlAltaCtas" );

						eZLog::writeNotice("fecha..>$fechaConsMov>>>>[".$Day.$Month.$Year."].....[".$DayF.$MonthF.$YearF."]");

							$transaccion_buffer = getDataConsmov("consmov",$wsdl,$cuentaCM ,$fechaConsMov);

							$trans = explode("&",$transaccion_buffer);

							$error = $trans[0];//00
							$transaccion_buffer = $trans[1];//ok
							//eZLog::writeNotice("Buff...[$transaccion_buffer]");
							if ( $error == "E001")
							{
								$transaccion_buffer = "<br><b>".$transaccion_buffer."<b>"; //no hay movimientos
								$ret_code = -1;
							}

							if ( $ret_code == 0) //si hay archivo generado
							{
								$t->set_file( array("estadosdecuenta_tpl" => "estadosdecuenta_process.tpl") );

								$d = new eZDate( );
								$mes = Month2mes(date("M"));
								//$fecha = $d->year( ) . $d->addZero( $d->month( ) ) . $d->addZero ( $d->day( ) );
								$fecha = $DayF.$mes.$YearF;
								$size = strlen( $transaccion_buffer );
								$FileExt = ".txt";
								//$FrAccount2 = str_replace( " ", "", $FrAccount );
								//$FrAccount2 = str_replace( "-", "", $FrAccount2 );
								$fileName = "movimientos".trim($cuentac[0])."-".trim($sub[0])."_".trim($sub[1])."-".$fecha.$FileExt;
								$fileName2 = "movimientos".trim($cuentac[0])."-".trim($sub[0])."_".trim($sub[1])."-".$fecha.$FileExt;
								if ( preg_match( "/^edocta([0-9]{1,9})/", $fileName, $regs ) ) /*HB-AGL*/
								{
										$CustNo = $regs[1];
								}

								//$tb = preg_replace("/\|/", "\r\n", $transaccion_buffer );	 /* HB AGL*/
								$tb = preg_replace("/\|/", "| ", $transaccion_buffer );	 /* HB AGL*/

								/*$tb = preg_replace("/\aacute/", "�", $transaccion_buffer );
								$tb = preg_replace("/\eacute/", "�", $transaccion_buffer );
								$tb = preg_replace("/\iacute/", "�", $transaccion_buffer );
								$tb = preg_replace("/\oacute/", "�", $transaccion_buffer );
								$tb = preg_replace("/\uacute/", "�", $transaccion_buffer );	*/

								$tb = preg_replace( "/\�/", "\r\n", $tb );				     /* HB AGL*/
								$tb = preg_replace( "/\�/", "\t", $tb );					 /* HB AGL*/
								$file = new eZFile();
								$file->dumpDataToFile( $tb, $fileName );

								$uploadedFile = new eZVirtualFile( );
								$uploadedFile->setName( $fileName );
								$uploadedFile->setDescription( $fileName2 );
								$uploadedFile->setFile( $file );
								$uploadedFile->storebb( $CustNo );

								$FileID = $uploadedFile->id();
								$FolderID = 1;
								$folder = new eZVirtualFolder( $FolderID );
								eZObjectPermission::setPermission( -1, $FileID, "filemanager_file", 'r' );
								$uploadedFile->setUser( $folder->user() );
								$uploadedFile->store();

								$folder->addFile( $uploadedFile );

								eZLog::writeNotice( "File added to file manager from transaccion using request from IP:" . $_SERVER['REMOTE_ADDR'] );

								$t->set_block( "errors_tpl", "error_write_permission", "write_permission" );
								$t->set_var( "write_permission", "&nbsp;" );

								$t->set_block( "errors_tpl", "error_upload_permission", "upload_permission" );
								$t->set_var( "upload_permission", "&nbsp;" );

								$t->set_block( "errors_tpl", "error_name_tpl", "error_name" );
								$t->set_var( "error_name", "&nbsp;" );

								$t->set_block( "errors_tpl", "error_file_upload_tpl", "error_file_upload" );
								$t->set_var( "error_file_upload", "&nbsp" );

								$t->set_block( "errors_tpl", "error_description_tpl", "error_description" );
								$t->set_var( "error_description", "&nbsp;" );

								$t->set_block( "errors_tpl", "error_read_everybody_permission_tpl", "error_read_everybody_permission" );
								$t->set_var( "error_read_everybody_permission", "&nbsp;" );

								$t->set_block( "errors_tpl", "error_write_everybody_permission_tpl", "error_write_everybody_permission" );
								$t->set_var( "error_write_everybody_permission", "&nbsp;" );


								$filename = $uploadedFile->name();
								$t->set_var( "file_id", $uploadedFile->id() );
								$t->set_var( "original_file_name_without_spaces", str_replace( " ", "%20", $filename ) );
								$t->set_var( "original_file_name", $filename );
								$t->set_var( "file_name", $filename );
								$t->set_var( "file_url", $filename );
								$t->set_var( "file_description", $uploadedFile->description() );

								$filePath = $uploadedFile->filePath( true );

								$size = $uploadedFile->siFileSize();
								$t->set_var( "file_size", $size["size-string"] );
								$t->set_var( "file_unit", $size["unit"] );

								$t->set_var( "file_read", "" );
								$t->set_var( "file_write", "" );
								$t->set_var( "td_class", ( $i % 2 ) ? "bgdark" : "bglight" );

								if ( eZObjectPermission::hasPermission( $uploadedFile->id(), "filemanager_file", "r", $user ) || eZVirtualFile::isOwner( $user ,$uploadedFile->id() ) )
								{
									$t->parse( "read", "read_tpl" );
									$i++;
								}
								else
								{
									$t->set_var( "read", "" );
								}

								$t->parse( "file", "file_tpl", true );
								$transaccion_buffer = "";
								$t->set_var( "transaccion_buffer", $transaccion_buffer );
								//ACS  - MenuContenidoAjax I
								//$t->pparse( "output", "estadosdecuenta_tpl" );
								$transaccion_buffer=$t->parse( "output", "estadosdecuenta_tpl" );
								//ACS  - MenuContenidoAjax F

							}
							else //no existe el archivo y se envia algun error
							{
								$t->set_file( array("estadosdecuenta_tpl" => "estadosdecuenta.tpl") );
								$t->set_var( "transaccion_buffer", $transaccion_buffer );
								//ACS  - MenuContenidoAjax I
								//$t->pparse( "output", "estadosdecuenta_tpl" );
								$transaccion_buffer=$t->parse( "output", "estadosdecuenta_tpl" );
								//ACS  - MenuContenidoAjax F
							}

							$transaccion_buffer=str_replace("type=\"submit\"","type=\"BUTTON\"",$transaccion_buffer);
							$transaccion_buffer=str_replace("onclick=\"javascript:{ document.getElementById( 'OK' ).disabled = true; document.forms[ 0 ].submit(); }\"","onclick=\"javascript:procesarAjax();\"",$transaccion_buffer);
							// REF JAC 05NOV2010 FIN
							break;
						}
						case "txt":
						{
							$canBreak = true;
							$DayHoy		= date("d");	// 28 12 2008  (28 de diciembre del 2008)
							$DayLetra	= strtolower(date("D"));
							$MonthHoy	= date("m");
							$YearHoy	= date("Y");
							$FrAccount = str_replace(" ", "", $FrAccount);
							$FrAccount = str_replace("-", "", $FrAccount);
							$FrAccount = strtolower(substr($FrAccount,0, strlen($FrAccount)-1)).substr($FrAccount,-1);

							$FrAccount = str_replace( "brillante", "brillan", $FrAccount );
							$FrAccount = urlencode( $FrAccount );
							if ( $Accion2 == "periododia" ) // Lo debe resuelve un sam exclusivo para estados de cuenta.
							{
							//echo "hola1";
								$Day	= $DayHoy;
								$Month	= $MonthHoy;
								$Year	= $YearHoy;

								$DayF   = $DayHoy;
								$MonthF = $MonthHoy;
								$YearF  = $YearHoy;
							}
							else
							{
								if ( $Month == "  " or $Year == "    ")	// error.. debe seleccionar al menos mes anio inicial
								{
									$transaccion_buffer = "Transacci&oacute;n no realizada.<br><br><b>Favor de capturar el mes y a�o iniciales</b>";
									$t->set_file( array("estadosdecuenta_tpl" => "estadosdecuenta.tpl") );
									$t->set_var( "transaccion_buffer", $transaccion_buffer );
									$transaccion_buffer=$t->parse( "output", "estadosdecuenta_tpl" );
									break;
								}
								else
								{
									if (
											( $DayF == "  " or $MonthF == "  " or $YearF == "    " ) and
											( $DayF.$MonthF.$YearF != "        " )
										)
									{
										$transaccion_buffer = "Transacci�n no realizada.<br><br><b>Favor de capturar la fecha final.</b>";
										$t->set_file( array("estadosdecuenta_tpl" => "estadosdecuenta.tpl") );
										$t->set_var( "transaccion_buffer", $transaccion_buffer );
										$transaccion_buffer=$t->parse( "output", "estadosdecuenta_tpl" );
										break;
									}

									if ($Day == "  ")
										$Day = "01";

									if ($DayF == "  ")
									{
										$DayF = ultimoDia($Month, $Year);
									}

									if ($MonthF == "  " )
										$MonthF = $Month;

									if ($YearF == "    ")
										$YearF = $Year;

									if ($YearF.$MonthF.$DayF < $Year.$Month.$Day )
									{
										$transaccion_buffer = "Transacci&oacute;n no realizada.<br><br><b>La fecha final debe ser mayor o igual que la inicial</b>";
										$t->set_file( array("estadosdecuenta_tpl" => "estadosdecuenta.tpl") );
										$t->set_var( "transaccion_buffer", $transaccion_buffer );
										$transaccion_buffer=$t->parse( "output", "estadosdecuenta_tpl" );
										break;
									}

									if (!(checkdate($Month,$Day,$Year)))
									{
										$transaccion_buffer = "Transacci&oacute;n no realizada.<br><br><b>La fecha inicial es inv&aacute;lida</b>";
										$t->set_file( array("estadosdecuenta_tpl" => "estadosdecuenta.tpl") );
										$t->set_var( "transaccion_buffer", $transaccion_buffer );
										$transaccion_buffer=$t->parse( "output", "estadosdecuenta_tpl" );
										break;
									}
									if (!(checkdate($MonthF,$DayF,$YearF)))
									{
										$transaccion_buffer = "Transacci&oacute;n no realizada.<br><br><b>La fecha final es inv&aacute;lida</b>";
										$t->set_file( array("estadosdecuenta_tpl" => "estadosdecuenta.tpl") );
										$t->set_var( "transaccion_buffer", $transaccion_buffer );
										$transaccion_buffer=$t->parse( "output", "estadosdecuenta_tpl" );
										break;
									}
								}
							}
							// JAC 05NOV2010 INI
							/*
							echo "
								<script language=\"JavaScript\">
								var page = '/reportespagos/';
								var w=25, h=25, bw, bh, bl, bt, topPos, leftPos;

								bw = document.documentElement.clientWidth;
								bh = document.documentElement.clientHeight;
								bl = window.screenLeft;
								bt = window.screenTop;

								leftPos = (Math.floor(bw/2)-2*w);
								topPos = (Math.floor(bh/2)-h);

								page = page + \"?type=edo_cta&user=\" + '$usr';
								page = page + \"&FrAccount=\" + '$FrAccount';
								page = page + \"&Day=\" + '$Day' + \"&Month=\" + '$Month' + \"&Year=\" + '$Year';
								page = page + \"&DayF=\" + '$DayF' + \"&MonthF=\" + '$MonthF' + \"&YearF=\" + '$YearF';

								attributes = \"width=\" + w + \",height=\" + h + \",top=\" + topPos + \",left=\" + leftPos;
								popUpWin = window.open (page, \"winName\", attributes);

								</script>";
							$transaccion_buffer = "Transacci&oacute;n realizada con &eacute;xito.<br><br>";
							$t->set_file( array("estadosdecuenta_tpl" => "estadosdecuenta.tpl") );
							$t->set_var( "transaccion_buffer", $transaccion_buffer );
							$transaccion_buffer=$t->parse( "output", "estadosdecuenta_tpl" );
							*/
							//Mensaje - Nexions HM
							$ret_code = 0;
							$trans = $trans . "<?xml version='1.0'?>";
							$trans .= "<mensaje><trxn value='stmt'/><accion value='ConsMovs'/><tipomsj value='rqs'/><CustID value='".
								$user ."'/><FrAccount value='".
								$FrAccount ."'/><Day value='".
								$Day ."'/><Month value='".
								$Month ."'/><Year value='".
								$Year ."'/><DayF value='".
								$DayF ."'/><MonthF value='".
								$MonthF ."'/><YearF value='".
								$YearF ."'/><format value='text'/></mensaje>";

							$JBoss = trim ( readParam ( "JBossHost", "site.ini" ) );
							$JBossPath = trim ( readParam( "JBossPath", "site.ini" ) );

							$Day   = date ( "d" );
							$Month = date ( "M" );
							$Year  = date ( "Y" );

							$FrAccount = str_replace ( " ", "", $FrAccount );

							$transaccion_buffer = requestHTTPtoJBoss ( $JBoss, $JBossPath, "xml", $trans );
							if ( trim ( $transaccion_buffer ) == "" )
								return "<script> alert(' 4503 - " .  $errors[4501]  . " ') </script>";

							/*
							$code_idx = strpos ( $transaccion_buffer, "020" );
							echo "code_idx: $code_idx<BR>";
							if ( $code_idx !== false )
							{
								$error_code = substr ( $transaccion_buffer, $code_idx, $code_idx + 4 );
								$error_desc = substr ( $transaccion_buffer, $code_idx + 6 );
								$transaccion_buffer = "<br><br><em>Hubo un error al consultar los movimientos. <BR>El c&oacute;digo de error fue <strong>";
								$transaccion_buffer .= $error_code .": </strong>" . $error_desc . "</em>";
								$ret_code = -1;
							}
							*/
							if (strpos($transaccion_buffer, "No se encontraron movimientos para los datos recibidos") > 0 || $transaccion_buffer=='')
							{
								$transaccion_buffer = "<br><b>La cuenta seleccionada no tiene movimientos.<b>"; //no hay movimientos
								$ret_code = -1;
							}
							if ( $ret_code == 0) //si hay archivo generado
							{
								$t->set_file( array("estadosdecuenta_tpl" => "estadosdecuenta_process.tpl") );

								$d = new eZDate( );
								$fecha = $d->year( ) . $d->addZero( $d->month( ) ) . $d->addZero ( $d->day( ) );
								$size = strlen( $transaccion_buffer );
								$FileExt = ".xls";
								$FrAccount2 = str_replace( " ", "", $FrAccount );
								$FrAccount2 = str_replace( "-", "", $FrAccount2 );
								$fileName = "edocta$FrAccount2$fecha$FileExt";
								$fileName2 = "edocta$FrAccount$fecha$FileExt";
								if ( preg_match( "/^edocta([0-9]{1,9})/", $fileName, $regs ) ) /*HB-AGL*/
								{
										$CustNo = $regs[1];
								}
								$tb = preg_replace("/\|/", "\r\n", $transaccion_buffer );	 /* HB AGL*/
								$tb = preg_replace( "/\�/", "\r\n", $tb );				     /* HB AGL*/
								$tb = preg_replace( "/\�/", "\t", $tb );					 /* HB AGL*/
								$file = new eZFile();
								$file->dumpDataToFile( $tb, $fileName );

								$uploadedFile = new eZVirtualFile( );
								$uploadedFile->setName( $fileName );
								$uploadedFile->setDescription( $fileName2 );
								$uploadedFile->setFile( $file );
								$uploadedFile->storebb( $CustNo );

								$FileID = $uploadedFile->id();
								$FolderID = 1;
								$folder = new eZVirtualFolder( $FolderID );
								eZObjectPermission::setPermission( -1, $FileID, "filemanager_file", 'r' );
								$uploadedFile->setUser( $folder->user() );
								$uploadedFile->store();

								$folder->addFile( $uploadedFile );

								eZLog::writeNotice( "File added to file manager from transaccion using request from IP:" . $_SERVER['REMOTE_ADDR'] );

								$t->set_block( "errors_tpl", "error_write_permission", "write_permission" );
								$t->set_var( "write_permission", "&nbsp;" );

								$t->set_block( "errors_tpl", "error_upload_permission", "upload_permission" );
								$t->set_var( "upload_permission", "&nbsp;" );

								$t->set_block( "errors_tpl", "error_name_tpl", "error_name" );
								$t->set_var( "error_name", "&nbsp;" );

								$t->set_block( "errors_tpl", "error_file_upload_tpl", "error_file_upload" );
								$t->set_var( "error_file_upload", "&nbsp" );

								$t->set_block( "errors_tpl", "error_description_tpl", "error_description" );
								$t->set_var( "error_description", "&nbsp;" );

								$t->set_block( "errors_tpl", "error_read_everybody_permission_tpl", "error_read_everybody_permission" );
								$t->set_var( "error_read_everybody_permission", "&nbsp;" );

								$t->set_block( "errors_tpl", "error_write_everybody_permission_tpl", "error_write_everybody_permission" );
								$t->set_var( "error_write_everybody_permission", "&nbsp;" );


								$filename = $uploadedFile->name();
								$t->set_var( "file_id", $uploadedFile->id() );
								$t->set_var( "original_file_name_without_spaces", str_replace( " ", "%20", $filename ) );
								$t->set_var( "original_file_name", $filename );
								$t->set_var( "file_name", $filename );
								$t->set_var( "file_url", $filename );
								$t->set_var( "file_description", $uploadedFile->description() );

								$filePath = $uploadedFile->filePath( true );

								$size = $uploadedFile->siFileSize();
								$t->set_var( "file_size", $size["size-string"] );
								$t->set_var( "file_unit", $size["unit"] );

								$t->set_var( "file_read", "" );
								$t->set_var( "file_write", "" );
								$t->set_var( "td_class", ( $i % 2 ) ? "bgdark" : "bglight" );

								if ( eZObjectPermission::hasPermission( $uploadedFile->id(), "filemanager_file", "r", $user ) || eZVirtualFile::isOwner( $user ,$uploadedFile->id() ) )
								{
									$t->parse( "read", "read_tpl" );
									$i++;
								}
								else
								{
									$t->set_var( "read", "" );
								}

								$t->parse( "file", "file_tpl", true );
								$transaccion_buffer = "";
								$t->set_var( "transaccion_buffer", $transaccion_buffer );
								//ACS  - MenuContenidoAjax I
								//$t->pparse( "output", "estadosdecuenta_tpl" );
								$transaccion_buffer=$t->parse( "output", "estadosdecuenta_tpl" );
								//ACS  - MenuContenidoAjax F

							}
							else //no existe el archivo y se envia algun error
							{
								$t->set_file( array("estadosdecuenta_tpl" => "estadosdecuenta.tpl") );
								$t->set_var( "transaccion_buffer", $transaccion_buffer );
								//ACS  - MenuContenidoAjax I
								//$t->pparse( "output", "estadosdecuenta_tpl" );
								$transaccion_buffer=$t->parse( "output", "estadosdecuenta_tpl" );
								//ACS  - MenuContenidoAjax F
							}

							$transaccion_buffer=str_replace("type=\"submit\"","type=\"BUTTON\"",$transaccion_buffer);
							$transaccion_buffer=str_replace("onclick=\"javascript:{ document.getElementById( 'OK' ).disabled = true; document.forms[ 0 ].submit(); }\"","onclick=\"javascript:procesarAjax();\"",$transaccion_buffer);
							// REF JAC 05NOV2010 FIN
							break;
						}
						case "txtTeso":
						{
							$canBreak = true;
							$DayHoy		= date("d");	// 28 12 2008  (28 de diciembre del 2008)
							$DayLetra	= strtolower(date("D"));
							$MonthHoy	= date("m");
							$YearHoy	= date("Y");
							$FrAccount = str_replace(" ", "", $FrAccount);
							$FrAccount = str_replace("-", "", $FrAccount);
							$FrAccount = strtolower(substr($FrAccount,0, strlen($FrAccount)-1)).substr($FrAccount,-1);

							$FrAccount = str_replace( "brillante", "brillan", $FrAccount );
							$FrAccount = urlencode( $FrAccount );
							if ( $Accion2 == "periododia" ) // Lo debe resuelve un sam exclusivo para estados de cuenta.
							{
							//echo "hola1";
								$Day	= $DayHoy;
								$Month	= $MonthHoy;
								$Year	= $YearHoy;

								$DayF   = $DayHoy;
								$MonthF = $MonthHoy;
								$YearF  = $YearHoy;
							}
							else
							{
								if ( $Month == "  " or $Year == "    ")	// error.. debe seleccionar al menos mes anio inicial
								{
									$transaccion_buffer = "Transacci&oacute;n no realizada.<br><br><b>Favor de capturar el mes y a�o iniciales</b>";
									$t->set_file( array("estadosdecuenta_tpl" => "estadosdecuenta.tpl") );
									$t->set_var( "transaccion_buffer", $transaccion_buffer );
									$transaccion_buffer=$t->parse( "output", "estadosdecuenta_tpl" );
									break;
								}
								else
								{
									if (
											( $DayF == "  " or $MonthF == "  " or $YearF == "    " ) and
											( $DayF.$MonthF.$YearF != "        " )
										)
									{
										$transaccion_buffer = "Transacci�n no realizada.<br><br><b>Favor de capturar la fecha final.</b>";
										$t->set_file( array("estadosdecuenta_tpl" => "estadosdecuenta.tpl") );
										$t->set_var( "transaccion_buffer", $transaccion_buffer );
										$transaccion_buffer=$t->parse( "output", "estadosdecuenta_tpl" );
										break;
									}

									if ($Day == "  ")
										$Day = "01";

									if ($DayF == "  ")
									{
										$DayF = ultimoDia($Month, $Year);
									}

									if ($MonthF == "  " )
										$MonthF = $Month;

									if ($YearF == "    ")
										$YearF = $Year;

									if ($YearF.$MonthF.$DayF < $Year.$Month.$Day )
									{
										$transaccion_buffer = "Transacci&oacute;n no realizada.<br><br><b>La fecha final debe ser mayor o igual que la inicial</b>";
										$t->set_file( array("estadosdecuenta_tpl" => "estadosdecuenta.tpl") );
										$t->set_var( "transaccion_buffer", $transaccion_buffer );
										$transaccion_buffer=$t->parse( "output", "estadosdecuenta_tpl" );
										break;
									}

									if (!(checkdate($Month,$Day,$Year)))
									{
										$transaccion_buffer = "Transacci&oacute;n no realizada.<br><br><b>La fecha inicial es inv&aacute;lida</b>";
										$t->set_file( array("estadosdecuenta_tpl" => "estadosdecuenta.tpl") );
										$t->set_var( "transaccion_buffer", $transaccion_buffer );
										$transaccion_buffer=$t->parse( "output", "estadosdecuenta_tpl" );
										break;
									}
									if (!(checkdate($MonthF,$DayF,$YearF)))
									{
										$transaccion_buffer = "Transacci&oacute;n no realizada.<br><br><b>La fecha final es inv&aacute;lida</b>";
										$t->set_file( array("estadosdecuenta_tpl" => "estadosdecuenta.tpl") );
										$t->set_var( "transaccion_buffer", $transaccion_buffer );
										$transaccion_buffer=$t->parse( "output", "estadosdecuenta_tpl" );
										break;
									}
								}
							}

							$ret_code = 0;
							$trans = $trans . "<?xml version='1.0'?>";
							$trans .= "<mensaje><trxn value='stmt'/><accion value='ConsMovsTeso'/><tipomsj value='rqs'/><CustID value='".
								$user ."'/><FrAccount value='".
								$FrAccount ."'/><Day value='".
								$Day ."'/><Month value='".
								$Month ."'/><Year value='".
								$Year ."'/><DayF value='".
								$DayF ."'/><MonthF value='".
								$MonthF ."'/><YearF value='".
								$YearF ."'/><format value='text'/></mensaje>";

							$JBoss = trim ( readParam ( "JBossHost", "site.ini" ) );
							$JBossPath = trim ( readParam( "JBossPath", "site.ini" ) );

							$Day   = date ( "d" );
							$Month = date ( "M" );
							$Year  = date ( "Y" );

							$FrAccount = str_replace ( " ", "", $FrAccount );

							$transaccion_buffer = requestHTTPtoJBoss ( $JBoss, $JBossPath, "xml", $trans );
							if ( trim ( $transaccion_buffer ) == "" )
								return "<script> alert(' 4503 - " .  $errors[4501]  . " ') </script>";

							/*
							$code_idx = strpos ( $transaccion_buffer, "020" );
							echo "code_idx: $code_idx<BR>";
							if ( $code_idx !== false )
							{
								$error_code = substr ( $transaccion_buffer, $code_idx, $code_idx + 4 );
								$error_desc = substr ( $transaccion_buffer, $code_idx + 6 );
								$transaccion_buffer = "<br><br><em>Hubo un error al consultar los movimientos. <BR>El c&oacute;digo de error fue <strong>";
								$transaccion_buffer .= $error_code .": </strong>" . $error_desc . "</em>";
								$ret_code = -1;
							}
							*/
							if (strpos($transaccion_buffer, "No se encontraron movimientos para los datos recibidos") > 0 || $transaccion_buffer=='')
							{
								$transaccion_buffer = "<br><b>La cuenta seleccionada no tiene movimientos.<b>"; //no hay movimientos
								$ret_code = -1;
							}
							if ( $ret_code == 0) //si hay archivo generado
							{
								$t->set_file( array("estadosdecuenta_tpl" => "estadosdecuenta_process.tpl") );

								$d = new eZDate( );
								$fecha = $d->year( ) . $d->addZero( $d->month( ) ) . $d->addZero ( $d->day( ) );
								$size = strlen( $transaccion_buffer );
								$FileExt = ".txt";
								$FrAccount2 = str_replace( " ", "", $FrAccount );
								$FrAccount2 = str_replace( "-", "", $FrAccount2 );
								$fileName = "edocta$FrAccount2$fecha$FileExt";
								$fileName2 = "edocta$FrAccount$fecha$FileExt";
								if ( preg_match( "/^edocta([0-9]{1,9})/", $fileName, $regs ) ) /*HB-AGL*/
								{
										$CustNo = $regs[1];
								}
								$tb = preg_replace("/\|/", "\r\n", $transaccion_buffer );	 /* HB AGL*/
								$tb = preg_replace( "/\�/", "\r\n", $tb );				     /* HB AGL*/
								$tb = preg_replace( "/\�/", "\t", $tb );					 /* HB AGL*/
								$file = new eZFile();
								$file->dumpDataToFile( $tb, $fileName );

								$uploadedFile = new eZVirtualFile( );
								$uploadedFile->setName( $fileName );
								$uploadedFile->setDescription( $fileName2 );
								$uploadedFile->setFile( $file );
								$uploadedFile->storebb( $CustNo );

								$FileID = $uploadedFile->id();
								$FolderID = 1;
								$folder = new eZVirtualFolder( $FolderID );
								eZObjectPermission::setPermission( -1, $FileID, "filemanager_file", 'r' );
								$uploadedFile->setUser( $folder->user() );
								$uploadedFile->store();

								$folder->addFile( $uploadedFile );

								eZLog::writeNotice( "File added to file manager from transaccion using request from IP:" . $_SERVER['REMOTE_ADDR'] );

								$t->set_block( "errors_tpl", "error_write_permission", "write_permission" );
								$t->set_var( "write_permission", "&nbsp;" );

								$t->set_block( "errors_tpl", "error_upload_permission", "upload_permission" );
								$t->set_var( "upload_permission", "&nbsp;" );

								$t->set_block( "errors_tpl", "error_name_tpl", "error_name" );
								$t->set_var( "error_name", "&nbsp;" );

								$t->set_block( "errors_tpl", "error_file_upload_tpl", "error_file_upload" );
								$t->set_var( "error_file_upload", "&nbsp" );

								$t->set_block( "errors_tpl", "error_description_tpl", "error_description" );
								$t->set_var( "error_description", "&nbsp;" );

								$t->set_block( "errors_tpl", "error_read_everybody_permission_tpl", "error_read_everybody_permission" );
								$t->set_var( "error_read_everybody_permission", "&nbsp;" );

								$t->set_block( "errors_tpl", "error_write_everybody_permission_tpl", "error_write_everybody_permission" );
								$t->set_var( "error_write_everybody_permission", "&nbsp;" );


								$filename = $uploadedFile->name();
								$t->set_var( "file_id", $uploadedFile->id() );
								$t->set_var( "original_file_name_without_spaces", str_replace( " ", "%20", $filename ) );
								$t->set_var( "original_file_name", $filename );
								$t->set_var( "file_name", $filename );
								$t->set_var( "file_url", $filename );
								$t->set_var( "file_description", $uploadedFile->description() );

								$filePath = $uploadedFile->filePath( true );

								$size = $uploadedFile->siFileSize();
								$t->set_var( "file_size", $size["size-string"] );
								$t->set_var( "file_unit", $size["unit"] );

								$t->set_var( "file_read", "" );
								$t->set_var( "file_write", "" );
								$t->set_var( "td_class", ( $i % 2 ) ? "bgdark" : "bglight" );

								if ( eZObjectPermission::hasPermission( $uploadedFile->id(), "filemanager_file", "r", $user ) || eZVirtualFile::isOwner( $user ,$uploadedFile->id() ) )
								{
									$t->parse( "read", "read_tpl" );
									$i++;
								}
								else
								{
									$t->set_var( "read", "" );
								}

								$t->parse( "file", "file_tpl", true );
								$transaccion_buffer = "";
								$t->set_var( "transaccion_buffer", $transaccion_buffer );
								//ACS  - MenuContenidoAjax I
								//$t->pparse( "output", "estadosdecuenta_tpl" );
								$transaccion_buffer=$t->parse( "output", "estadosdecuenta_tpl" );
								//ACS  - MenuContenidoAjax F

							}
							else //no existe el archivo y se envia algun error
							{
								$t->set_file( array("estadosdecuenta_tpl" => "estadosdecuenta.tpl") );
								$t->set_var( "transaccion_buffer", $transaccion_buffer );
								//ACS  - MenuContenidoAjax I
								//$t->pparse( "output", "estadosdecuenta_tpl" );
								$transaccion_buffer=$t->parse( "output", "estadosdecuenta_tpl" );
								//ACS  - MenuContenidoAjax F
							}

							$transaccion_buffer=str_replace("type=\"submit\"","type=\"BUTTON\"",$transaccion_buffer);
							$transaccion_buffer=str_replace("onclick=\"javascript:{ document.getElementById( 'OK' ).disabled = true; document.forms[ 0 ].submit(); }\"","onclick=\"javascript:procesarAjax();\"",$transaccion_buffer);
							// REF JAC 05NOV2010 FIN
							break;
						}
						case "excel":
							break;
						default:
							break;
					}
					if ( $canBreak )
					{
						break;
					}
					// JAC OCT2010 FIN

					$FileExt = ".xls";
					$File = "XLS";	// DGM

					// *********************************************************************************************************************************************************************************************************************************
					// DGM I 12Feb2008 Segmentar estado de cuenta (diario-anterior)
					// Esta consultando el dia de hoy

					$DayHoy		= date("d");	// 28 12 2008  (28 de diciembre del 2008)
					$DayLetra	= strtolower(date("D"));
					$MonthHoy	= date("m");
					$YearHoy	= date("Y");


					// ***********************************************************************
					// DGM 23Ene2006 Se agregan campos de rangos de fechas
					$particularFields = "&Accion=Corte&FrAccount=".urlencode( $FrAccount )."&Day=".urlencode( $Day )."&Month=".urlencode( $Month )."&Year=".urlencode( $Year )."&DayF=".urlencode( $DayF )."&MonthF=".urlencode( $MonthF )."&YearF=".urlencode( $YearF )."&File=".urlencode($File);
					$tr->blog( $qki, "EstadosDeCuenta", $particularFields, "", "", "", "Corte" );
					// ***********************************************************************

					$transaccion_buffer = "";

					if ( $Accion2 == "periododia" ) // Lo debe resuelve un sam exclusivo para estados de cuenta.
					{


						$Day	= $DayHoy;
						$Month	= $MonthHoy;
						$Year	= $YearHoy;

						$DayF   = $DayHoy;
						$MonthF = $MonthHoy;
						$YearF  = $YearHoy;

						$particularFields = "&Accion=Corte&FrAccount=".urlencode( $FrAccount )."&Day=".urlencode( $Day )."&Month=".urlencode( $Month )."&Year=".urlencode( $Year )."&DayF=".urlencode( $DayF )."&MonthF=".urlencode( $MonthF )."&YearF=".urlencode( $YearF )."&File=".urlencode($File);
						$tr->blog( $qki, "EstadosDeCuenta", $particularFields, "", "", "", "Corte" );

						// **********************************************************************************************************************************
						// DGM I 20Feb2008 Saldos al dia (se ejecuta un hst para ser resulto por JBOSS)

						//echo("<br>  Resolviendo [$backend] ");
						//Para JBoss
					//REF WMA-16Jun2008, Inicio. Se define nueva transaccion que luego se disfrazar� como un hst
						//if ( in_array( "stm", ( preg_split( "/[,||(,\s)]+/", $ini->read_var( "site", "JBossTransactions" ) ) ) ) )
						if ( in_array( "hststm", ( preg_split( "/[,||(,\s)]+/", $ini->read_var( "site", "JBossTransactions" ) ) ) ) )
					//REF WMA-16Jun2008, Fin
						{
							//echo("<br>Resuelve JBoss con trxn = hst");
							$top	= 9999999;	//10 millones de movimientos


							// Se agrega la T para las cuentas de Plazo
							if (
									substr($FrAccount, -1) != "D"    and 	substr($FrAccount, -1) != "T"
								)
							{
								if ( strpos($FrAccount, "Plazo") > 0   )
								{
									$FrAccount .= " T";
								}
								else
								{
									$FrAccount .= " D";
								}
							}

							$FrAccount = str_replace(" ", "", $FrAccount);
							$FrAccount = str_replace("-", "", $FrAccount);
							$FrAccount = strtolower(substr($FrAccount,0, strlen($FrAccount)-1)).substr($FrAccount,-1);


							$FrAccount = str_replace( "brillante", "brillan", $FrAccount );

							$particularFields = "&FrAccount=".urlencode( $FrAccount )."&Accion=Todos&Day=".urlencode( $Day )."&Month=".urlencode( $Month )."&Year=".urlencode( $Year )."&DayF=".urlencode( $DayF )."&MonthF=".urlencode( $MonthF )."&YearF=".urlencode( $YearF )."&Pos=".urlencode( $Pos )."&FrAmount=".urlencode( $FrAmount )."&Dias=".urlencode( $Dias )."&FrNo=".urlencode( $FrNo )."&top=".urlencode( $top );
							$tr->blog( $qki,"Movimientos", $FrAccount, $Day, $Month, $Year, $Accion );

					//REF WMA-16Jun2008, Inicio
							$backend = $ini->read_var( "site", "JBossHost" ); //Se va al servidor de produccion

							//echo ("Resuelve JBoss Backend: $backend");
							//$ret_code = $tr->PostToHost( $backend, "/IBnkIIS.dll", "Trxn=hst&Access=".urlencode( $Access )."&CustID=".urlencode( $usr )."&Cadpriv=".urlencode( $priv ).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer ); // movimientos
							$ret_code = $tr->PostToHost( $backend, "/IBnkIIS.dll", "Trxn=hststm&Access=".urlencode( $Access )."&CustID=".urlencode( $usr )."&Cadpriv=".urlencode( $priv ).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer ); // movimientos
							//return $transaccion_buffer;

					//REF WMA-16Jun2008, Fin

							if (strpos($transaccion_buffer, "La cuenta seleccionada no tiene movimientos") > 0 || $transaccion_buffer=='')
							{
								$transaccion_buffer = "<br><b>La cuenta seleccionada no tiene movimientos.<b>"; //no hay movimientos
								$ret_code = -1;
							}
							else
							{
								//echo "estadosdecuenta.php FrAccount[" . $FrAccount . "]";
								//ACS-28Ago2008 Inicio Fecha final de estado de cuenta
								//$transaccion_buffer = parserHTMLtoXLS( $transaccion_buffer, $Day."-".$Month."-".$Year, $FrAccount);
								$transaccion_buffer = parserHTMLtoXLS( $transaccion_buffer, $Day."-".$Month."-".$Year, $DayF."-".$MonthF."-".$YearF, $FrAccount);

								//ACS-28Ago2008 Inicio Fecha final de estado de cuenta
								$t->set_var( "transaccion_buffer", $transaccion_buffer );
								//ACS  - MenuContenidoAjax I
								//$t->pparse( "output", "movimientos_tpl" );
								//$transaccion_buffer=$t->parse( "output", "movimientos_tpl" );
								//ACS  - MenuContenidoAjax F
							}

						}
						else
						{

							// Si esta pidiendo un corte diario pero tiene fechas al dia de hoy se manda por respaldo
							// al servidor de respaldo entre semana de 9 a 9
							$hora = date("His");  //17
							if (
									(
										(
											$hora 		>= "090000" and
											$hora 		<= "210000"
										)
											and
										(
											 $DayLetra	!= "sat" and
											 $DayLetra	!= "sab" and
											 $DayLetra	!= "sun" and
											 $DayLetra	!= "dom"
										)
									)
									and
									(
										(
											$Day 	!= $DayHoy 		or ///Validacion extra
											$Month	!= $MonthHoy 	or
											$Year	!= $YearHoy		or
											$DayF   != $DayHoy		or
											$MonthF != $MonthHoy	or
											$YearF  != $YearHoy
										)

									)
								)

							{
								$backend = $ini->read_var( "site", "SAMStm" ); //Se va al servidor de respaldo (srvbases)
							}

							//echo ("Resuelve Backend: $backend");

							$ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=stm&Access=".urlencode($Access)."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // estados de cuenta
							//$transaccion_buffer="Entro antes parse pplepu".$transaccion_buffer;
						}


						// DGM F 20Feb2008 Saldos al dia (se ejcuta un hst para ser resulto por JBOSS
						// **********************************************************************************************************************************
					}
					else //corte historico
					{
					//REF WMA-16Jun2008, Inicio
						if ( in_array( "hststm", ( preg_split( "/[,||(,\s)]+/", $ini->read_var( "site", "JBossTransactions" ) ) ) ) )
						{
							$trn = "hststm";	//se disfraza como un hst de tipo stm para JBoss
						//REF WMA-01Aug2008, Inicio
							if (substr($FrAccount, -1) != "D"    and 	substr($FrAccount, -1) != "T")
							{
								if ( strpos($FrAccount, "Plazo") > 0   )
								{
									$FrAccount .= " T";
								}
								else
								{
									$FrAccount .= " D";
								}
							}
						//REF WMA-01Aug2008, Fin
						//REF WMA-29Jul2008, Inicio
							$FrAccount = str_replace(" ", "", $FrAccount);
							$FrAccount = str_replace("-", "", $FrAccount);
							$FrAccount = strtolower(substr($FrAccount,0, strlen($FrAccount)-1)).substr($FrAccount,-1);
							$FrAccount = str_replace( "brillante", "brillan", $FrAccount );
							$FrAccount = str_replace( "d�lar", "dolar", $FrAccount );
						//REF WMA-29Jul2008, Fin
						}
						else
						{
							$trn = "stm";
						}
					//REF WMA-16Jun2008, Fin
						// Si esta pidiendo un corte historico y tiene fechas al dia de hoy se manda por respaldo
						// al servidor de respaldo entre semana de 9 a 9
						$hora = date("His");  //17
						if (
									(
										$hora 		>= "090000" and
										$hora 		<= "210000"
									)
										and
									(
										 $DayLetra	!= "sat" and
										 $DayLetra	!= "sab" and
										 $DayLetra	!= "sun" and
										 $DayLetra	!= "dom"
									)
							)
						{
						//REF WMA 16JUN2008, Inicio
							//$backend = $ini->read_var( "site", "SAMStm" ); //Se va al servidor de respaldo (srvbases)
							if ($trn == "hststm")
							{
								/*echo "X Datos: [" .$Day."][".$Month."][".$Year."][".$DayF."][".$MonthF."][".$YearF."]";
								echo strlen($Month);*/

								// ************************************************************************************************************************
								// DGM I 03Jul2008 Validacion de captura de Rango de Fechas en corte anterior
								if ( $Month == "  " or $Year == "    ")	// error.. debe seleccionar al menos mes anio inicial
								{
									//echo "A Datos: [" .$Day."][".$Month."][".$Year."][".$DayF."][".$MonthF."][".$YearF."]";
									$transaccion_buffer = "Transacci&oacute;n no realizada.<br><br><b>Favor de capturar el mes y a�o iniciales</b>";
									$t->set_file( array("estadosdecuenta_tpl" => "estadosdecuenta.tpl") );
									$t->set_var( "transaccion_buffer", $transaccion_buffer );
									//ACS  - MenuContenidoAjax I
									//$t->pparse( "output", "estadosdecuenta_tpl" );
									$transaccion_buffer=$t->parse( "output", "estadosdecuenta_tpl" );
									//ACS  - MenuContenidoAjax F
									break;

								}
								else
								{
									// Si esta presente al menos un dato en la fecha final, deben de estar todos capturados
									if (
											( $DayF == "  " or $MonthF == "  " or $YearF == "    " ) and
											( $DayF.$MonthF.$YearF != "        " )
										)
									{
										$transaccion_buffer = "Transacci�n no realizada.<br><br><b>Favor de capturar la fecha final.</b>";
										$t->set_file( array("estadosdecuenta_tpl" => "estadosdecuenta.tpl") );
										$t->set_var( "transaccion_buffer", $transaccion_buffer );
										//ACS  - MenuContenidoAjax I
										//$t->pparse( "output", "estadosdecuenta_tpl" );
										$transaccion_buffer=$t->parse( "output", "estadosdecuenta_tpl" );
										//ACS  - MenuContenidoAjax F
										break;
									}


									if ($Day == "  ")
										$Day = "01";

									if ($DayF == "  ")
									{
										$DayF = ultimoDia($Month, $Year);
									}

									if ($MonthF == "  " )
										$MonthF = $Month;

									if ($YearF == "    ")
										$YearF = $Year;

									//echo ("|".$YearF."|".$MonthF."|".$DayF. " < ".$Year.$Month.$Day. "??" );

									if ($YearF.$MonthF.$DayF < $Year.$Month.$Day )
									{
										$transaccion_buffer = "Transacci&oacute;n no realizada.<br><br><b>La fecha final debe ser mayor o igual que la inicial</b>";
										$t->set_file( array("estadosdecuenta_tpl" => "estadosdecuenta.tpl") );
										$t->set_var( "transaccion_buffer", $transaccion_buffer );
										//ACS  - MenuContenidoAjax I
										//$t->pparse( "output", "estadosdecuenta_tpl" );
										$transaccion_buffer=$t->parse( "output", "estadosdecuenta_tpl" );
										//ACS  - MenuContenidoAjax F
										break;
									}

									//REF WMA-13aug2008, Inicio. Nueva validaci�n para d�a bien formado
									if (!(checkdate($Month,$Day,$Year)))
									{
										$transaccion_buffer = "Transacci&oacute;n no realizada.<br><br><b>La fecha inicial es inv&aacute;lida</b>";
										$t->set_file( array("estadosdecuenta_tpl" => "estadosdecuenta.tpl") );
										$t->set_var( "transaccion_buffer", $transaccion_buffer );
										//ACS  - MenuContenidoAjax I
										//$t->pparse( "output", "estadosdecuenta_tpl" );
										$transaccion_buffer=$t->parse( "output", "estadosdecuenta_tpl" );
										//ACS  - MenuContenidoAjax F
										break;
									}
									if (!(checkdate($MonthF,$DayF,$YearF)))
									{
										$transaccion_buffer = "Transacci&oacute;n no realizada.<br><br><b>La fecha final es inv&aacute;lida</b>";
										$t->set_file( array("estadosdecuenta_tpl" => "estadosdecuenta.tpl") );
										$t->set_var( "transaccion_buffer", $transaccion_buffer );
										//ACS  - MenuContenidoAjax  I
										//$t->pparse( "output", "estadosdecuenta_tpl" );
										$transaccion_buffer=$t->parse( "output", "estadosdecuenta_tpl" );
										//ACS  - MenuContenidoAjax F
										break;
									}
									//REF WMA-13aug2008, Fin
								}
								// DGM I 03Jul2008 Validacion de captura de Rango de Fechas en corte anterior
								// ************************************************************************************************************************


								$backend = $ini->read_var( "site", "JBossStm" ); //Se va al servidor de respaldo (srvbases)
								$top	= 9999999;	//10 millones de movimientos
								$particularFields = "&FrAccount=".urlencode( $FrAccount )."&Accion=Todos&Day=".urlencode( $Day )."&Month=".urlencode( $Month )."&Year=".urlencode( $Year )."&DayF=".urlencode( $DayF )."&MonthF=".urlencode( $MonthF )."&YearF=".urlencode( $YearF )."&Pos=".urlencode( $Pos )."&FrAmount=".urlencode( $FrAmount )."&Dias=".urlencode( $Dias )."&FrNo=".urlencode( $FrNo )."&top=".urlencode( $top );
								//return "Perrote".$particularFields;
							}
							else
							{
								$backend = $ini->read_var( "site", "SAMStm" ); //Se va al servidor de respaldo (srvbases)
							}
						}
						else
						{
							if ($trn == "hststm")
							{
								$backend = $ini->read_var( "site", "JBossHost" ); //Se va al servidor de respaldo (srvbases)
								$top	= 9999999;	//10 millones de movimientos
								$particularFields = "&FrAccount=".urlencode( $FrAccount )."&Accion=Todos&Day=".urlencode( $Day )."&Month=".urlencode( $Month )."&Year=".urlencode( $Year )."&DayF=".urlencode( $DayF )."&MonthF=".urlencode( $MonthF )."&YearF=".urlencode( $YearF )."&Pos=".urlencode( $Pos )."&FrAmount=".urlencode( $FrAmount )."&Dias=".urlencode( $Dias )."&FrNo=".urlencode( $FrNo )."&top=".urlencode( $top );
							}
						}
						//REF WMA 16JUN2008, Fin

						//echo ("Resuelve Backend: $backend");
					//REF WMA-16Jun2008, Inicio
							//$ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=stm&Access=".urlencode($Access)."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // estados de cuenta

							//echo "Datos: [" .$Day."][".$Month."][".$Year."][".$DayF."][".$MonthF."][".$YearF."]";

							$ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=".$trn."&Access=".urlencode($Access)."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // estados de cuenta


							if ($trn == "hststm") //solo transforma el HTML si fue a JBoss
							{
								if (strpos($transaccion_buffer, "La cuenta seleccionada no tiene movimientos") > 0 )
								{
									$transaccion_buffer = "<br><b>La cuenta seleccionada no tiene movimientos.<b>"; //no hay movimientos
									$ret_code = -1;
								}
								else
								{
									//echo "estadosdecuenta.php FrAccount[" . $FrAccount . "]";
									//ACS-28Ago2008 Inicio Fecha final de estado de cuenta
									//$transaccion_buffer = parserHTMLtoXLS( $transaccion_buffer, $Day."-".$Month."-".$Year,  $FrAccount);
									$transaccion_buffer = parserHTMLtoXLS( $transaccion_buffer, $Day."-".$Month."-".$Year, $DayF."-".$MonthF."-".$YearF, $FrAccount);
									//ACS-28Ago2008 Fin Fecha final de estado de cuenta
									$t->set_var( "transaccion_buffer", $transaccion_buffer );
									//ACS  - MenuContenidoAjax I
									//$t->pparse( "output", "movimientos_tpl" );
									//$transaccion_buffer=$t->parse( "output", "movimientos_tpl" );

									//ACS  - MenuContenidoAjaxF

								}
							}

					//REF WMA-16Jun2008, Fin
					}


					//echo("servidor buscando: $backend");
					// DGM F 12Feb2008 Segmentar estado de cuenta (diario-anterior)
					// *********************************************************************************************************************************************************************************************************************************


					//echo ("Particular Fields: [$particularFields] ret_code = [$ret_code]");


					// DGM 24Ene2005 Estado de cuenta con nuevas columnas
					//$tr->blog($qki,"estadosdecuenta", $FrAccount, $Day, $Month, $Year, $Accion);
					$tr->blog( $qki, "EstadosDeCuenta", $particularFields, "", "", "", "Corte" );

					// *********************************************************************************************
					// DGM I Estados de Cuenta con columnas nuevas y mostrando pagina de error
					if ( $ret_code == 0) //si hay archivo generado
					{
						$t->set_file( array("estadosdecuenta_tpl" => "estadosdecuenta_process.tpl") );

						$d = new eZDate( );
						$fecha = $d->year( ) . $d->addZero( $d->month( ) ) . $d->addZero ( $d->day( ) );
						$size = strlen( $transaccion_buffer );
						$FrAccount2 = str_replace( " ", "", $FrAccount );
						$FrAccount2 = str_replace( "-", "", $FrAccount2 );
						$fileName = "edocta$FrAccount2$fecha$FileExt";
						$fileName2 = "edocta$FrAccount$fecha$FileExt";
						if ( preg_match( "/^edocta([0-9]{1,9})/", $fileName, $regs ) ) /*HB-AGL*/
						{
								$CustNo = $regs[1];
						}
						//echo "[" . $CustNo . "]";
						$tb = preg_replace("/\|/", "\r\n", $transaccion_buffer );			 /* HB AGL*/
						$tb = preg_replace( "/\�/", "\r\n", $tb );						 /* HB AGL*/
						$tb = preg_replace( "/\�/", "\t", $tb );							 /* HB AGL*/

						$file = new eZFile();
						$file->dumpDataToFile( $tb, $fileName );

						$uploadedFile = new eZVirtualFile( );
						$uploadedFile->setName( $fileName );
						$uploadedFile->setDescription( $fileName2 );
						$uploadedFile->setFile( $file );
						$uploadedFile->storebb( $CustNo );


						$FileID = $uploadedFile->id();
						$FolderID = 1;
						$folder = new eZVirtualFolder( $FolderID );
						eZObjectPermission::setPermission( -1, $FileID, "filemanager_file", 'r' );
						$uploadedFile->setUser( $folder->user() );
						$uploadedFile->store();
						$folder->addFile( $uploadedFile );


						eZLog::writeNotice( "File added to file manager from transaccion using request from IP:" . $_SERVER['REMOTE_ADDR'] );

						$t->set_block( "errors_tpl", "error_write_permission", "write_permission" );
						$t->set_var( "write_permission", "&nbsp;" );

						$t->set_block( "errors_tpl", "error_upload_permission", "upload_permission" );
						$t->set_var( "upload_permission", "&nbsp;" );

						$t->set_block( "errors_tpl", "error_name_tpl", "error_name" );
						$t->set_var( "error_name", "&nbsp;" );

						$t->set_block( "errors_tpl", "error_file_upload_tpl", "error_file_upload" );
						$t->set_var( "error_file_upload", "&nbsp" );

						$t->set_block( "errors_tpl", "error_description_tpl", "error_description" );
						$t->set_var( "error_description", "&nbsp;" );

						$t->set_block( "errors_tpl", "error_read_everybody_permission_tpl", "error_read_everybody_permission" );
						$t->set_var( "error_read_everybody_permission", "&nbsp;" );

						$t->set_block( "errors_tpl", "error_write_everybody_permission_tpl", "error_write_everybody_permission" );
						$t->set_var( "error_write_everybody_permission", "&nbsp;" );


						$filename = $uploadedFile->name();
						$t->set_var( "file_id", $uploadedFile->id() );
						$t->set_var( "original_file_name_without_spaces", str_replace( " ", "%20", $filename ) );
						$t->set_var( "original_file_name", $filename );
						$t->set_var( "file_name", $filename );
						$t->set_var( "file_url", $filename );
						$t->set_var( "file_description", $uploadedFile->description() );

						$filePath = $uploadedFile->filePath( true );

						$size = $uploadedFile->siFileSize();
						$t->set_var( "file_size", $size["size-string"] );
						$t->set_var( "file_unit", $size["unit"] );

						$t->set_var( "file_read", "" );
						$t->set_var( "file_write", "" );
						$t->set_var( "td_class", ( $i % 2 ) ? "bgdark" : "bglight" );

						if ( eZObjectPermission::hasPermission( $uploadedFile->id(), "filemanager_file", "r", $user ) || eZVirtualFile::isOwner( $user ,$uploadedFile->id() ) )
						{
							$t->parse( "read", "read_tpl" );
							$i++;
						}
						else
						{
							$t->set_var( "read", "" );
						}

						$t->parse( "file", "file_tpl", true );
						$transaccion_buffer = "";
						$t->set_var( "transaccion_buffer", $transaccion_buffer );
						//ACS  - MenuContenidoAjax I
						//$t->pparse( "output", "estadosdecuenta_tpl" );
						$transaccion_buffer=$t->parse( "output", "estadosdecuenta_tpl" );
						//ACS  - MenuContenidoAjax F

					}
					else //no existe el archivo y se envia algun error
					{
						$t->set_file( array("estadosdecuenta_tpl" => "estadosdecuenta.tpl") );
						$t->set_var( "transaccion_buffer", $transaccion_buffer );
						//ACS  - MenuContenidoAjax I
						//$t->pparse( "output", "estadosdecuenta_tpl" );
						$transaccion_buffer=$t->parse( "output", "estadosdecuenta_tpl" );
						//ACS  - MenuContenidoAjax F
					}

					$transaccion_buffer=str_replace("type=\"submit\"","type=\"BUTTON\"",$transaccion_buffer);
					$transaccion_buffer=str_replace("onclick=\"javascript:{ document.getElementById( 'OK' ).disabled = true; document.forms[ 0 ].submit(); }\"","onclick=\"javascript:procesarAjax();\"",$transaccion_buffer);
					// DGM F Estados de Cuenta con columnas nuevas y mostrando pagina de error
					// *********************************************************************************************
					break;
				}
				case "Mensual":
				{
					//eZLog::writeNotice( ">>>>>>>>>>>>>>>>>>ROBERT3382 ".$ini->read_var( "eZTransaccionMain", "TemplateDir" ));
					$FrAccount = trim( $FrAccount );
					$Formato   = trim( $Formato   ); //DBA 182332 CFD

					$t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ), "eztransaccion/user/intl/", $Language, "estadosdecuenta.php" );
					$t->setAllStrings();
					//LGAM Inicio Validaci�n para Edo. Ctas. sin fecha. 09/09/2013
					if ($Month == " " and $Year == " ")
					{
						if (date("m") == 1) {
							$Month = 12;
							$Year = (date("Y")-1);
						}
						else {
							$Month = (date("m")-1);
							$Year = date("Y");
							if ( strlen($Month) == 1)
							{
								$Month = "0".$Month;
							}
						}
					}
					//LGAM Fin Validaci�n para Edo. Ctas. sin fecha. 09/09/2013
					//IRG 130912- Inicio - NoPaperless
					if(in_array($FrAccount, ( preg_split( "/[,||(,\s)]+/", $ini->read_var( "site", "NoPaperless" ) ) ) ) && $Formato == "pdf")
					{
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

						$particularFields = "&Accion=Mensual&FrAccount=" . urlencode( $FrAccount ) . "&Month=".urlencode( $Month )."&Year=" . urlencode( $Year );
						$month = array( "", "enero", "febrero", "marzo", "abril", "mayo", "junio", "julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre" );

						if(file_exists( "/var/www/repo_eec/" . $Year . "/" . $Month . "/" . $Year . $Month . "_" . $FrAccount . ".pdf" ) &&  is_readable( "/var/www/repo_eec/" . $Year . "/" . $Month . "/" . $Year . $Month . "_" . $FrAccount . ".pdf" ))
						{
							$t->set_file( array( "estadosdecuenta_tpl" => "estadosdecuenta_process.tpl" ) );

							$file = new eZFile();
							$file->TmpFileName = "/var/www/repo_eec/" . $Year . "/" . $Month . "/" . $Year . $Month . "_" . $FrAccount . "." . $Formato;
							$file->FileName = $Year . $Month . "_" . $FrAccount ."." . $Formato;

							$uploadedFile = new eZVirtualFile();
							$uploadedFile->setName( $file->FileName );
							$uploadedFile->setDescription( $file->FileName );
							$uploadedFile->setFile( $file );
							$uploadedFile->storebb( $FrAccount );
							$size = $uploadedFile->siFileSize();
							$t->set_var( "file_id", $uploadedFile->id() );
							$t->set_var( "file_description", "Estado de Cuenta de " . $month[ intval( $Month ) ] ." de " . $Year );
							//$t->set_var( "file_size", $size["size-string"] );
							$t->set_var( "file_size", "" );
							//$t->set_var( "file_unit", $size["unit"] );
							$t->set_var( "file_unit", "" );
							$t->set_var( "original_file_name", $file->FileName );
							$t->set_var( "original_file_name_without_spaces", $file->FileName );

							$folder = new eZVirtualFolder( 1 );
							eZObjectPermission::setPermission( -1, $uploadedFile->id(), "filemanager_file", "r" );
							$uploadedFile->setUser( $folder->user() );
							$uploadedFile->store();
							$folder->addFile( $uploadedFile );

							$t->parse( "file", "file_tpl", true );
							$transaccion_buffer = "";
							$t->set_var( "transaccion_buffer", $transaccion_buffer );
							$transaccion_buffer=$t->parse( "output", "estadosdecuenta_tpl" );
						}
						else
						{
							$t->set_file( array( "estadosdecuenta_tpl" => "estadosdecuenta.tpl" ) );
							if ( ( $Year . $Month ) < ( date( "Y", ( time() - ( 60 * 60 * 24 * 30.4 * 15 ) ) ) . date( "m", ( time() - ( 60 * 60 * 24 * 30.4 * 15 ) ) ) ) )
							{
								$transaccion_buffer = "<p>No existe el Estado de Cuenta correspondiente al mes de1 <b>" . $month[ intval( $Month ) ] ."</b> de <b>" . $Year . "</b>.</p><p>Recuerde que s&oacute;lo puede obtener Estados de Cuenta Mensuales de 15 meses atr&aacute;s.</p>";
							}
							elseif
								(
										( $Year . $Month ) == ( date( "Y" ) . date( "m" ) )
									or	( $Year . $Month ) <= ( date( "Y", ( time() - ( 60 * 60 * 24 * 5 ) ) ) . date( "m", ( time() - ( 60 * 60 * 24 * 5 ) ) ) )
								)
							{
								$transaccion_buffer = "<p>No existe el Estado de Cuenta correspondiente al mes de2 <b>" . $month[ intval( $Month ) ] ."</b> de <b>" . $Year . "</b> en formato ".strtoupper(trim($Formato)).".</p><p>Favor de volverlo a intentar m&aacute;s tarde.</p>";
								eZLog::writeNotice( "At estadosdecuenta: Statement services.bb.com.mx/var/www/data/eec/" . $Year . "/" . $Month . "/" . $Year . $Month . "_" . $FrAccount . ".pdf" . " Not Found.|" );
								sendmail( $ini->read_var( "site", "ErrEMail1" ), "Baj�oNET. Estados de Cuenta Mensuales.", "El Estado de Cuenta services.bb.com.mx/var/www/data/eec/" . $Year . "/" . $Month . "/" . $Year . $Month . "_" . $FrAccount . ".pdf" . " No Existe. Favor de verificarlo.\r\n\r\nSi es as�, favor de gestionar las acciones necesarias para dejarlo displonible." );
								sendmail( $ini->read_var( "site", "ErrEMail2" ), "Baj�oNET. Estados de Cuenta Mensuales.", "El Estado de Cuenta services.bb.com.mx/var/www/data/eec/" . $Year . "/" . $Month . "/" . $Year . $Month . "_" . $FrAccount . ".pdf" . " No Existe. Favor de verificarlo.\r\n\r\nSi es as�, favor de gestionar las acciones necesarias para dejarlo displonible." );
							}
							else
							{
								$transaccion_buffer = "<p>A&uacute;n no ha sido generado el Estado de Cuenta correspondiente al mes de <b>" . $month[ intval( $Month ) ] ."</b> de <b>" . $Year . "</b>.</p><p>Favor de volverlo a intentar 5 d&iacute;as despu&eacute;s del d&iacute;a de corte.</p>";
							}
							$transaccion_buffer .= "Si desea descargar otro Estado de Cuenta haga clic <a href=\"/transaccion/estadosdecuenta/\">aqu&iacute;</a>.";
							$t->set_var( "transaccion_buffer", $transaccion_buffer );
							$transaccion_buffer=$t->parse( "output", "estadosdecuenta_tpl" );
						}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
					}
					else
					{
					//IRG 130912- Fin - NoPaperless

						$particularFields = "&Accion=Mensual&FrAccount=" . urlencode( $FrAccount ) . "&Month=".urlencode( $Month )."&Year=" . urlencode( $Year );
						$month = array( "", "enero", "febrero", "marzo", "abril", "mayo", "junio", "julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre" );

						//IRG090512 - Inicio - Paperless
						//Se valida que para el formato xml se seleccione una fecha posterior a Dic de 2011
						//y se valida que la fecha seleccionada no sea superior a la fecha actual.
						/*if(($Formato == "xml" && $Year == "2011") ||
						(( $Year . $Month ) >= ( date( "Y", ( time() - ( 60 * 60 * 24 * 5 ) ) ) . date( "m", ( time() - ( 60 * 60 * 24 * 5 ) ) ) ) ))*/
						if(($Formato == "xml" && $Year == "2011") ||
						(( $Year . $Month ) > date("Y").date("m")))
						{
							$transaccion_buffer = "<p>No existe el Estado de Cuenta correspondiente al mes de <b>" . $month[ intval( $Month ) ] ."</b> de <b>" . $Year . "</b> en formato ".strtoupper(trim($Formato)).".</p>";
							$transaccion_buffer .= "Si desea descargar otro Estado de Cuenta haga clic <a href=\"/transaccion/estadosdecuenta/\">aqu&iacute;</a>.";
						}
						else
						/*if
							(
								// file_exists( "/var/www/data/eec/" . $Year . "/" . $Month . "/" . $Year . $Month . "_" . $FrAccount . ".pdf" )
								// &&	is_readable( "/var/www/data/eec/" . $Year . "/" . $Month . "/" . $Year . $Month . "_" . $FrAccount . ".pdf" )
									(file_exists( "/var/www/repo_eec/" . $Year . "/" . $Month . "/" . $Year . $Month . "_" . $FrAccount . ".pdf" )
								&&  is_readable( "/var/www/repo_eec/" . $Year . "/" . $Month . "/" . $Year . $Month . "_" . $FrAccount . ".pdf" )) ||
								(file_exists( "/tmp/" .  $Year . $Month . "_" . $FrAccount . ".".$Formato )
								&&  is_readable( "/tmp/" . $Year . $Month . "_" . $FrAccount .".". $Formato ))
							) */
						{
							$servicioOrigen = $Formato == "pdf" ? "SERVICIOS" : "SERVICIOSXML";
							//$servicioOrigen = ($Formato == "pdf") ? "CREDITOLEM" : "CREDITOLEMXML";

							//::::::::::::::::::::::::::::LLAMADO AL WS::::::::::::::::::::::::::::::::::::::
							$edosExistentes2 = getCortesVistaByClienteAndPeriodo($FrAccount, $Year, $Month, $Formato);
							$words = array("Ocurrio", "conexion");
							$wordsChange = array("Ocurri�", "conexi�n");
							//$x=1;
							// [AETG:20140926] Se agrega esta variable para controlar la muestra de mensajes de error
							$esFormatoPDFXML = "";
							// [AETG:20140926] Se agrega esta variable para mostrar el producto junto con el mensaje de error
							$nombreProducto = "";
							for($x=0; $x < count($edosExistentes2["vals"]); $x++){
								//eZLog::writeNotice("DETALLE:: ".$edosExistentes2["vals"][$x]["attributes"]["DETALLE"]);

								// [AETG:20140926] Se agrega esta condicion para controlar la muestra de mensajes de error
								if ($edosExistentes2["vals"][$x]["tag"] == "PDF"){
									$esFormatoPDFXML = "PDF";
								} else if ($edosExistentes2["vals"][$x]["tag"] == "XML"){
									$esFormatoPDFXML = "XML";
								}

								// [AETG:20140926] Se agrega esta variable para mostrar el producto junto con el mensaje de error
								if ($edosExistentes2["vals"][$x]["tag"] == "PRODUCTO" && $edosExistentes2['vals'][$x]['attributes'] != null
									&& $edosExistentes2['vals'][$x]['attributes'][NAME] != ""){
									$nombreProducto = $edosExistentes2['vals'][$x]['attributes'][NAME];
								}

								if($edosExistentes2["vals"][$x]["tag"] == "ERROR" && $edosExistentes2["vals"][$x]["attributes"]["DETALLE"] == "(301) El cliente no tiene estados de cuenta disponibles."
									// [AETG:20140926-1017] Se agrega esta condicion para diferenciar entre formato pdf o xml
									&& ($esFormatoPDFXML == strtoupper(trim($Formato)) || $esFormatoPDFXML == "")){
									eZLog::writeNotice("LISTA NEGRA!!".$edosExistentes2["vals"][$x]["attributes"]["DETALLE"]);
									$transaccion_buffer = "";
									$transaccion_buffer = "<p>".
															"C&oacute;digo (301): El cliente no tiene estados de cuenta disponibles.".
															"</p>";
									$blackList = true;
								}
								else if($edosExistentes2["vals"][$x]["tag"] == "ERROR" && $edosExistentes2["vals"][$x]["attributes"]["DETALLE"]=="(305) Ocurrio el siguiente error al intentar obtener los cortes vigentes:"
									// [AETG:20140926-1017] Se agrega esta condicion para diferenciar entre formato pdf o xml
									&& ($esFormatoPDFXML == strtoupper(trim($Formato)) || $esFormatoPDFXML == "")){
									$transaccion_buffer .= "<p style='padding:0px 5px 0px 5px;'>".$nombreProducto."<br> ".
															"C&oacute;digo (305). Por favor intente m&aacute;s tarde. En caso de que el problema contin&uacute;e, por favor reporte a Help Desk.".
															"</p>";
									$error = true;
									eZLog::writeNotice("305!!".$edosExistentes2["vals"][$x]["attributes"]["DETALLE"]);
								}
								else if($edosExistentes2["vals"][$x]["tag"] == "ERROR" && $edosExistentes2["vals"][$x]["attributes"]["DETALLE"]=="(306) No fue posible conectarse con el servidor de P@perless."
									// [AETG:20140926-1017] Se agrega esta condicion para diferenciar entre formato pdf o xml
									&& ($esFormatoPDFXML == strtoupper(trim($Formato)) || $esFormatoPDFXML == "")){
									$transaccion_buffer = "";
									$transaccion_buffer = "<p>".
															"C&oacute;digo (JEC0306). Error de comunicaci&oacute;n, favor de intentarlo m&aacute;s tarde.<br>En caso de que el problema persista, favor de reportarlo a nuestro Call Center al 01-800-47-10-400.".
														  "</p>";
									$error = true;
									eZLog::writeNotice("306!!".$edosExistentes2["vals"][$x]["attributes"]["DETALLE"]);
								}
								else if($edosExistentes2["vals"][$x]["tag"] == "ERROR" && $edosExistentes2["vals"][$x]["attributes"]["DETALLE"]=="(307) No fue posible establecer la conexion con la Base de Datos Solicitada."
									// [AETG:20140926-1017] Se agrega esta condicion para diferenciar entre formato pdf o xml
									&& ($esFormatoPDFXML == strtoupper(trim($Formato)) || $esFormatoPDFXML == "")){
									//$transaccion_buffer = "";
									$transaccion_buffer .= "<p style='padding:0px 5px 0px 5px;'>".$nombreProducto."<br> ".
															"C&oacute;digo (307). Por favor intente m&aacute;s tarde. En caso de que el problema contin&uacute;e, por favor reporte a Help Desk.".
															"</p>";
									$error = true;
									eZLog::writeNotice("307!!".$edosExistentes2["vals"][$x]["attributes"]["DETALLE"]);
								}
							}

								if($blackList==false && $error==false){
								//::::::::::::::::::::::::::::LLAMADO AL WS::::::::::::::::::::::::::::::::::::::
								$edosExistentes = countEstadosCta($servicioOrigen, $FrAccount, $Year, $Month, $Formato);
								$numEdos = 0;
								//Se valida del response si existen estados de cuenta para la fecha seleccionada
								for($x=0; $x < count($edosExistentes["vals"]); $x++){ // JPB HB 14082013 se cambio la funcion strlen por count
									if( $edosExistentes["vals"][$x]["tag"] == "CUENTA"
										&& $edosExistentes["vals"][$x]["attributes"]["TIPO"] == "servicios"
										&& $edosExistentes["vals"][$x]["attributes"]["NUMERO"] > 0){
										//$numEdos = 1;
										$numEdos = $edosExistentes["vals"][$x]["attributes"]["NUMERO"];
									}
								}
								if($numEdos != 0){
									$template = $_POST['template'];
									//$name_file = $Year . $Month . "_" . $FrAccount . ".".$Formato;
									//ACDP INI Mayo 2014
									$la_s = "";
									if ( !isset($_SERVER['HTTPS']) || strtolower($_SERVER['HTTPS']) != 'on' ) {
										$la_s = "";
									} else {
										$la_s = "s";
									}
									$i_Server = $ini->read_var( "site", "ImageServer" );
									$transaccion_buffer = "";
									$transaccion_buffer .= "<table width=85% border=0 cellspacing=0 cellpadding=4 >";
									$t->set_file( array( "estadosdecuenta_tpl" => "estadosdecuentapdf_process.tpl" ) );
									for($contador = 0; $contador < $numEdos ; $contador++){
										$file_description = "Estado de Cuenta de " . $month[ intval( $Month ) ] . " de " . $Year;
										$name_file = $Year . $Month . "_" . $FrAccount;
										$name_file .= $numEdos > 1 ? "_" . $contador . "." . $Formato : "." . $Formato;
										$t->parse( "file", "file_tpl", true );
										$transaccion_buffer .= completaBuffer( $servicioOrigen, $name_file, $Month, $Year, $secure_site, $file_description, $la_s, $i_Server);
									}
									if (  $template != "no")
									$transaccion_buffer .= "<tr><td colspan=\"5\">Si desea descargar otro Estado de Cuenta haga clic <a href=\"/transaccion/estadosdecuenta/\">aqu&iacute;</a>.</td></tr>";
									$transaccion_buffer .= "</table>";
									$t->set_var( "transaccion_buffer", $transaccion_buffer );
									eZLog::writeNotice("Template-->$template<----");
									if (  $template != "no")
										$transaccion_buffer=$t->parse( "output", "estadosdecuenta_tpl" );

									//$t->set_file( array( "estadosdecuenta_tpl" => "estadosdecuentaxml_process.tpl" ) );
									//ACDP FIN Mayo 2014
	/*
									$file = new eZFile();
									// $file->TmpFileName = "/var/www/data/eec/" . $Year . "/" . $Month . "/" . $Year . $Month . "_" . $FrAccount . ".pdf";
										//$file->TmpFileName = "/var/www/repo_eec/" . $Year . "/" . $Month . "/" . $Year . $Month . "_" . $FrAccount . ".pdf";
										//$file->TmpFileName = "/var/www/repo_eec/" . $Year . "/" . $Month . "/" . $Year . $Month . "_" . $FrAccount . "." . $Formato; //DBA 182332 CFD      //kitar comentario
										$file->TmpFileName = "/tmp/". $Year . $Month . "_" . $FrAccount . "." . $Formato;  //probando nueva ubicacion
										//$file->FileName = $Year . $Month . "_" . $FrAccount .".pdf";
										$file->FileName = $Year . $Month . "_" . $FrAccount ."." . $Formato; //DBA 182332 CFD

									$uploadedFile = new eZVirtualFile();
									$uploadedFile->setName( $file->FileName );
									$uploadedFile->setDescription( $file->FileName );
									$uploadedFile->setFile( $file );
									//LVPR I Mejora para entregar el estado de cuenta al cliente
									//$uploadedFile->store();
									$uploadedFile->storebb( $FrAccount );
									//LVPR I Mejora para entregar el estado de cuenta al cliente
	*/
									//$size = filesize($fullname_file);
									//ACDP INI Mayo 2014
									//$t->set_var( "file_id", "1" );
									//ACDP FIN Mayo 2014
									//$t->set_var( "file_description", "Estado de Cuenta de " . $month[ intval( $Month ) ] ." de " . $Year );
									//$t->set_var( "file_size", round($size/1024));
									//$t->set_var( "file_unit", "KB" );
									//ACDP INI Mayo 2014
									/*$t->set_var( "file_size", "");
									$t->set_var( "file_unit", "" );
									$t->set_var( "original_file_name", $name_file );
									$t->set_var( "original_file_name_without_spaces", $name_file );*/
									//ACDP FIN Mayo 2014
									//$folder = new eZVirtualFolder( 1 );
									//eZObjectPermission::setPermission( -1, "1", "filemanager_file", "r" );
									//$uploadedFile->setUser( $folder->user() );
									//$uploadedFile->store();
									//$folder->addFile( $uploadedFile );
									//ACDP INI Mayo 2014
									/*$t->parse( "file", "file_tpl", true );
									$transaccion_buffer = "";
									$t->set_var( "transaccion_buffer", $transaccion_buffer );*/
									//ACDP FIN Mayo 2014
									//ACS  - MenuContenidoAjax I
									//$t->pparse( "output", "estadosdecuenta_tpl" );
									//ACDP INI Mayo 2014
									//$transaccion_buffer=$t->parse( "output", "estadosdecuenta_tpl" );
									//ACDP FIN Mayo 2014
									//ACS  - MenuContenidoAjax F

								}
								else
								{
									$t->set_file( array( "estadosdecuenta_tpl" => "estadosdecuentaxml_process.tpl" ) );
								//IRG090512 - Fin - Paperless
									if ( ( $Year . $Month ) < ( date( "Y", ( time() - ( 60 * 60 * 24 * 30.4 * 15 ) ) ) . date( "m", ( time() - ( 60 * 60 * 24 * 30.4 * 15 ) ) ) ) )
									{
										$transaccion_buffer = "<p>No existe el Estado de Cuenta correspondiente al mes de4 <b>" . $month[ intval( $Month ) ] ."</b> de <b>" . $Year . "</b> en formato ".strtoupper(trim($Formato)).".</p><p>Recuerde que s&oacute;lo puede obtener Estados de Cuenta Mensuales de 15 meses atr&aacute;s.</p>";
									}
									elseif
										(
												( $Year . $Month ) == ( date( "Y" ) . date( "m" ) )
											or	( $Year . $Month ) <= ( date( "Y", ( time() - ( 60 * 60 * 24 * 5 ) ) ) . date( "m", ( time() - ( 60 * 60 * 24 * 5 ) ) ) )
										)
									{

										$transaccion_buffer = "<p>No existe el Estado de Cuenta correspondiente al mes de <b>" . $month[ intval( $Month ) ] ."</b> de <b>" . $Year . "</b> en formato ".strtoupper(trim($Formato)).".</p>";
										//eZLog::writeNotice( "At estadosdecuenta: Statement services.bb.com.mx/var/www/data/eec/" . $Year . "/" . $Month . "/" . $Year . $Month . "_" . $FrAccount . ".pdf" . " Not Found.|" );
										//sendmail( $ini->read_var( "site", "ErrEMail1" ), "Baj�oNET. Estados de Cuenta Mensuales.", "El Estado de Cuenta services.bb.com.mx/var/www/data/eec/" . $Year . "/" . $Month . "/" . $Year . $Month . "_" . $FrAccount . ".pdf" . " No Existe. Favor de verificarlo.\r\n\r\nSi es as�, favor de gestionar las acciones necesarias para dejarlo displonible." );
										//sendmail( $ini->read_var( "site", "ErrEMail2" ), "Baj�oNET. Estados de Cuenta Mensuales.", "El Estado de Cuenta services.bb.com.mx/var/www/data/eec/" . $Year . "/" . $Month . "/" . $Year . $Month . "_" . $FrAccount . ".pdf" . " No Existe. Favor de verificarlo.\r\n\r\nSi es as�, favor de gestionar las acciones necesarias para dejarlo displonible." );
									}
									else
									{
										$transaccion_buffer = "<p>A&uacute;n no ha sido generado el Estado de Cuenta correspondiente al mes de <b>" . $month[ intval( $Month ) ] ."</b> de <b>" . $Year . "</b>.</p><p>Favor de volverlo a intentar 5 d&iacute;as despu&eacute;s del d&iacute;a de corte.</p>";
									}
									//$transaccion_buffer .= "Si desea descargar otro Estado de Cuenta haga clic <a href=\"/transaccion/estadosdecuenta/\">aqu&iacute;</a>.";
									$t->set_var( "transaccion_buffer", $transaccion_buffer );
									//ACS  - MenuContenidoAjax I
									//$t->pparse( "output", "estadosdecuenta_tpl" );
									//$transaccion_buffer=$t->parse( "output", "estadosdecuenta_tpl" );
									//ACS  - MenuContenidoAjax F
								}
							}
						}

						//$transaccion_buffer = "";
						//$ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=stm&Access=" . urlencode( $Access ) . "&CustID=" . urlencode( $usr ) . "&Cadpriv=" . urlencode( $priv ) . "&Action=JustLog" . $particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer );
					} //IRG 130912- NoPaperless
					$tr->blog( $qki, "EstadosDeCuenta", $particularFields, "", "", "", "Mensual" );
					//eZLog::writeNotice( "ROBERT!!!!>>>>>>>>> ".$transaccion_buffer );
					break;
				}
			}
			break;
		}

	}

	if($_POST['Otra'] == 'Download'){
		$nombreArchivo = $_POST['Year'].$_POST['Month'].'_'.$_POST['FrAccount'].".".$_POST['Formato'];
		//ACDP Mayo2014
		if($_POST['TipoEdo'] == 'Vista'){
			getDocumento($_POST['Origen'], $_POST['FrAccount'], $_POST['Year'], $_POST['Month'], $_POST['Formato'], $_POST['File']);
		} else {
			getDocumento($_POST['Origen'], $_POST['FrAccount'], $_POST['Year'], $_POST['Month'], $_POST['Formato'], $nombreArchivo);
		}
	}

	$transaccion_buffer=str_replace("href=\"/transaccion/estadosdecuenta/\"","HREF=\"#\" onclick=\"javascript:iniProcesarMenu('estadosdecuenta','')\" ",$transaccion_buffer);

	//eZLog::writeNotice("ROBERT>>>>>>>>>>>".$transaccion_buffer."<<<<<<<<<<<<<<<<<");

	$transaccion_buffer="<script>
					function getArchivoEstadoCuenta(origen)
					{
						$.ajax({
							type: 'POST',
							url: '/procesarAjaxMenu.php',
							data: 'nomFunc=estadosdecuenta&Access=Download&Otra=Download&Origen=".$servicioOrigen."&FrAccount=".$FrAccount."&Year=".$Year."&Month=".$Month."&Formato=".$Formato."',
							dataTypedataType: 'html',
							success: function(datos)
									{
										window.location.href = '/filedownload/'+ $('input[name=filename]').val();
									}
							});
					}
					//ACDP INI Mayo 2014
					function getArchivoEstadoCuentaVista(origen, filename)
					{
						loading();
						$.ajax({
							type: 'POST',
							url: '/procesarAjaxMenu.php',
							data: 'nomFunc=estadosdecuenta&Access=Download&Otra=Download&Origen=$servicioOrigen&FrAccount=$FrAccount&Year=$Year&Month=$Month&TipoEdo=Vista&Formato=$Formato&File=' + filename,
							dataTypedataType: 'html',
							success: function(datos)
									{
										$.modal.close();
										window.location.href = '/filedownload/'+ filename;
									}
							});
					}
					//ACDP FIN Mayo 2014
									function procesarAjax()
									{
										var parametros=	'CustID:'+document.getElementsByName('CustID')[0].value+
														',Cadpriv:'+document.getElementsByName('Cadpriv')[0].value+
														',Trxn:'+document.getElementsByName('Trxn')[0].value+
														',Accion:'+document.getElementsByName('Accion')[0].value+
														',Access:'+document.getElementsByName('Access')[0].value;



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



										if (document.getElementById('Day')!=null)
										{
											parametros+=',Day:'+document.getElementById('Day').value;
										}

										if (document.getElementById('Month')!=null)
										{
											parametros+=',Month:'+document.getElementById('Month').value;
										}

										if (document.getElementById('Year')!=null)
										{
											parametros+=',Year:'+document.getElementById('Year').value;
										}

										if (document.getElementById('DayF')!=null)
										{
											parametros+=',DayF:'+document.getElementById('DayF').value;
										}

										if (document.getElementById('MonthF')!=null)
										{
											parametros+=',MonthF:'+document.getElementById('MonthF').value;
										}

										if (document.getElementById('YearF')!=null)
										{
											parametros+=',YearF:'+document.getElementById('YearF').value;
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
										//DBA 182332 CFD
										if (document.getElementsByName('Formato')[0]!=null)
										{
											if (document.getElementsByName('Formato')[0].type!='HIDDEN' && document.getElementsByName('Formato')[0].type!='hidden')
											{
												parametros+=',Formato:'+$('input:radio[name=Formato]:checked').val();
											}
											else
											{
												parametros+=',Formato:'+document.getElementsByName('Formato')[0].value;
											}
										}
										//DBA 182332 CFD

										iniProcesarMenu('estadosdecuenta', parametros);
									}
//06May2010   ACS  Llave ASB para Circular X I



									$(document).ready(function (){
											$('#token_value').focus();
											$('form').bind('submit', function() {
												return false;
											}
										);
									});


//06May2010   ACS  Llave ASB para Circular X F
								</script>".$transaccion_buffer;
								//$transaccion_buffer = "ROBERT!!!";
								//eZLog::writeNotice( "ROBERT!!!\m/ ".$transaccion_buffer);
	?>
