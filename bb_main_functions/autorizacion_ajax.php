<?php
($parametros['CustID']!=""?$CustID=$parametros['CustID']:0);
($parametros['Access']!=""?$Access=$parametros['Access']:0);
($parametros['Day']!=""?$Day=$parametros['Day']:0);
($parametros['Month']!=""?$Month=$parametros['Month']:0);
($parametros['FrAccount']!=""?$FrAccount=$parametros['FrAccount']:0);
($parametros['AgruPagIni']!=""?$AgruPagIni=$parametros['AgruPagIni']:0);
($parametros['code']!=""?$code=$parametros['code']:0);
($parametros['Nomben1']!=""?$Nomben1=$parametros['Nomben1']:0);
($parametros['Desc']!=""?$Desc=$parametros['Desc']:0);
($parametros['Comprobante']!=""?$Comprobante=$parametros['Comprobante']:0);
($parametros['Agrupa']!=""?$Agrupa=$parametros['Agrupa']:0);
include_once( "ezreimp/classes/ezreimp.php" ); //DBA Reimpresion de Comprobantes

	function modSubmit ($submit)
	{
		$submit=str_replace("TYPE=SUBMIT", "type=\"button\" onclick=\"procesarAjax();\"",$submit);
		$submit=str_replace("TYPE=\"SUBMIT\"", "type=\"button\" onclick=\"procesarAjax();\"",$submit);
		$submit=str_replace("type=\"submit\"","type=\"button\" onclick=\"procesarAjax();\"",$submit);
		$submit=str_replace("TYPE=\"PASSWORD\""," TYPE=\"PASSWORD\" onKeyPress=\"if (isEnter(event) == true) { procesarAjax(); }\"", $submit);
		$submit=str_replace("TYPE=\"RADIO\""," TYPE=\"RADIO\" onclick=\"javascript:pago(this.value);\" ", $submit);
		$submit=str_replace("parent.history.back()","iniProcesarMenu('autorizacion', '');",$submit);
		return $submit;
	}

	$procesarAjax="
					<script>


						var seleccionPago;
						seleccionPago='';
						function pago( seleccion )
						{
							seleccionPago=seleccion;
						}

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

							var parametros=	'CustID:'+document.getElementsByName('CustID')[0].value;

							if(document.getElementsByName('Access')[0]!=null)
							{
								parametros+=',Access:'+document.getElementsByName('Access')[0].value;
							}

							if(document.getElementsByName('Accion')[0]!=null) //YEHO 189716
							{
								parametros+=',Accion:'+document.getElementsByName('Accion')[0].value;
							}
							if(document.getElementsByName('Day')[0]!=null)
							{
								parametros+=',Day:'+document.getElementsByName('Day')[0].value;
							}

							if(document.getElementsByName('Month')[0]!=null)
							{
								parametros+=',Month:'+document.getElementsByName('Month')[0].value;
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

							if(document.getElementsByName('code')[0]!=null)
							{
								parametros+=',code:'+document.getElementsByName('code')[0].value;
							}

							if(document.getElementsByName('Nomben1')[0]!=null)
							{
								parametros+=',Nomben1:'+document.getElementsByName('Nomben1')[0].value;
							}

							if(document.getElementsByName('Desc')[0]!=null)
							{
								if (document.getElementsByName('Desc')[0].type!='HIDDEN' && document.getElementsByName('Desc')[0].type!='hidden')
								{
									parametros+=',Desc:'+seleccionPago;
								}
								else
								{
									parametros+=',Desc:'+document.getElementsByName('Desc')[0].value;
								}

							}

							if(document.getElementsByName('Comprobante')[0]!=null)
							{
								if (document.getElementsByName('Comprobante')[0].checked==true)
								{
									parametros+=',Comprobante:'+document.getElementsByName('Comprobante')[0].value;
								}
								else
								{
									parametros+=',Comprobante:'
								}
							}


							if(document.getElementsByName('Agrupa')[0]!=null)
							{
								parametros+=',Agrupa:'+document.getElementsByName('Agrupa')[0].value;
							}

							if (document.getElementsByName('Desc')[0]!=null)
							{
								if (document.getElementsByName('Desc')[0].type!='HIDDEN' && document.getElementsByName('Desc')[0].type!='hidden')
								{
									if (seleccionPago!='')
									{
										iniProcesarMenu('autorizacion', parametros);
									}
									else
									{
										alert ('Seleccione un pago para autorizar')
									}
								}
								else
								{
									iniProcesarMenu('autorizacion', parametros);
								}

							}
							else
							{
								iniProcesarMenu('autorizacion', parametros);
							}

						}
					</script>";

    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "autorizacion.php" );

    $t->setAllStrings();
	// AGG I 25Nov2005 Para poner fecha y hora en los comprobantes
	$dateTime  = new eZDateTime( );
	$timeStamp = $dateTime->timeStamp();
	$time =& date('H:i:s', $timeStamp );
	$date =& date('jMY', $timeStamp );
	// AGG F 25Nov2005 Para poner fecha y hora en los comprobantes

	$dateTime  = new eZDateTime( );
	$timeStamp = $dateTime->timeStamp();
	$time =& date('H:i:s', $timeStamp );
	$date =& date('jMY', $timeStamp );

    $t->set_file( array(
        "autorizacion_tpl" => "autorizacion.tpl"
        ) );


    $transaccion_buffer = "";
    if(empty($Access)) {
        $Access = "FrAc";
    }
    // $Accion = "autorizacion";
    if( !isset /*(HB AGL - Ajustes PHP5)*/( $Amount ) ) {
    	$Amount = "";
    }
    if( !isset /*(HB AGL - Ajustes PHP5)*/( $Desc ) ) {
    	$Desc = "";
    }
    if( !isset /*(HB AGL - Ajustes PHP5)*/( $FrAccount ) ) {
    	$FrAccount = "";
    }
    if( !isset /*(HB AGL - Ajustes PHP5)*/( $Nomben1 ) ) {
    	$Nomben1 = "";
    }
     if( !isset /*(HB AGL - Ajustes PHP5)*/( $Accion ) ) { //YEHO 189716 06Ago12
	    	$Accion = "";
    }
    switch($Access) {
    case "FrAc":

	 if ( $Agrupa=="Aceptar" )
	 {
	 	$Access = "FrAc";
	 	$Accion  = "autoriza";
        	$particularFields = "&Accion=".urlencode($Accion)."&FrAccount=".urlencode($FrAccount)."&AgruPagIni=".urlencode($AgruPagIni);
        	$tr->blog($qki,"Autorizacion", $FrAccount, $Nomben1, $Desc, $Amount, $Accion);
	 }
	 else
	 {
		$particularFields = "";
        	$tr->blog($qki,"Autorizaciones", $FrAccount, $Nomben1, $Desc, $Amount, $Accion);
	 }
        break;
    case "Confirm":
        $particularFields = "&Access=".urlencode($Access)."&Nomben1=".urlencode($Nomben1)."&Desc=".urlencode($Desc)."&FrAccount=".urlencode($FrAccount);;
        $tr->blog($qki,"Autorizaciones", $FrAccount, $Nomben1, $Desc, $Amount, $Accion);
        break;
    case "Process":
        $particularFields = "&Passwd=".encrypt( $code, strtolower( $usr ) )."&Access=".urlencode($Access)."&Nomben1=".urlencode($Nomben1)."&Desc=".urlencode($Desc)."&FrAccount=".urlencode($FrAccount);;
        $tr->blog($qki,"Autorizaciones", $FrAccount, $Nomben1, $Desc, $Amount, $Accion);
        break;
    }
    $transaccion_buffer = "";
    // DebugBreak();

    $ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=aut&Access=".urlencode($Access)."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // autorizacion de operaciones pendientes
	$transaccion_buffer = str_replace( "#_EURO_#", "&euro;", $transaccion_buffer );	// trxn euro 232716
//////////////////// YEHO I 13Abr2011
if ($Access == "Process")
	{
		$buffer2 = str_replace(chr(34),chr(92).chr(34),$transaccion_buffer);
		$ini =& INIFile::globalINI();
		$SecureServer = $ini->read_var( "site", "SecureServer" );
		$SecureServerType = $ini->read_var( "site", "SecureServerType" );
		$DomainPostfix = $ini->read_var( "site", "DomainPostfix" );
		$ServerNumber = $ini->read_var( "site", "ServerNumber" );
		// *****************************************************************************************
		// DGM I 25May2006 Respetar acumulado diario en Pago Impuestos f
		if ( strpos($buffer2, "Su pago de impuestos ha sido efectuado con") )
		{
		// *****************************************************************************************
			$Pagina = "<SCRIPT LANGUAGE='JavaScript'> function ventana() { var windowW = 800; var windowH = 450; var windowX = 10; var windowY = 10; var title = 'Recibo'; s = 'width='+windowW+',height='+windowH; cadena=\"<HTML><HEAD><link REL='STYLESHEET' HREF='" . $SecureServerType . "://" . $SecureServer . $ServerNumber . $DomainPostfix . "/impuestos/estilos.css' TYPE='text/css'><TITLE>Recibo de Contribuciones Federales</TITLE>";
			$Pagina = $Pagina."<SCRIPT LANGUAGE='JavaScript'> function window_onbeforeprint() { Imprimir.style.visibility = 'hidden'; Salir.style.visibility = 'hidden';} function window_onafterprint() { Imprimir.style.visibility = 'visible'; Salir.style.";
			$Pagina = $Pagina."visibility = 'visible'; }\"+\"<\"+\"/\"+\"SCRIPT></HEAD><BODY LANGUAGE='javascript' onbeforeprint='return window_onbeforeprint()' onafterprint='return window_onafterprint()'>";
			$Pagina = $Pagina."<INPUT type='button' value='Imprimir' id=Imprimir name=Imprimir onclick='window.print();'>&nbsp;<INPUT type='button' value=' Salir  ' id=Salir name=Salir onclick='window.close();'>";
			$Pagina = $Pagina.$buffer2;
			$Pagina = $Pagina."</BODY></HTML>\"; NFW =";
			$Pagina = $Pagina." window.open(\"\",'Recibo',\"toolbar=no,menubar=no,scrollbars=2,\"+s); NFW.blur(); NFW.resizeTo(windowW,windowH); NFW.moveTo(windowX,windowY); NFW.";
			$Pagina = $Pagina."document.open(); NFW.document.write(cadena); NFW.document.close(); NFW.window.focus(); } ventana(); </SCRIPT>";
		//	//DBA Reimpresion de Comprobantes
		//	$FechaHora = "";
		//	$QryRIC = new eZReImp( );
		//	$QryRIC -> store($CustID,$FrAccount,$buffer2,"imp","",$FechaHora);
			//DBA Reimpresion de Comprobantes
		}
		
		//ATAR Imprimir correctamente el SAT REferenciado 01SEP2014
		if(strpos($transaccion_buffer,"Su Pago ha sido procesado con"))
		{			
			$PageTitle		  = "Recibo Bancario de Pago de Contribuciones Federales";
			$Pagina = "<SCRIPT LANGUAGE='JavaScript'> function ventana() { var windowW = 800; var windowH = 450; var windowX = 10; var windowY = 10; var title = 'Recibo'; s = 'width='+windowW+',height='+windowH; cadena=\"<HTML><HEAD><link REL='STYLESHEET' HREF='" . $SecureServerType . "://" . $SecureServer . $ServerNumber . $DomainPostfix . "/impuestos/recibos.css' TYPE='text/css'><TITLE>" . $PageTitle . "</TITLE>";		
			$Pagina = $Pagina."<SCRIPT LANGUAGE='JavaScript'> function window_onbeforeprint() { Imprimir.style.visibility = 'hidden'; Salir.style.visibility = 'hidden';} function window_onafterprint() { Imprimir.style.visibility = 'visible'; Salir.style.";
			$Pagina = $Pagina."visibility = 'visible'; }\"+\"<\"+\"/\"+\"SCRIPT></HEAD><BODY LANGUAGE='javascript' onbeforeprint='return window_onbeforeprint()' onafterprint='return window_onafterprint()'>";
			$Pagina = $Pagina."<INPUT type='button' value='Imprimir' id=Imprimir name=Imprimir onclick='window.print();'>&nbsp;<INPUT type='button' value=' Salir  ' id=Salir name=Salir onclick='window.close();'>";
			$Pagina = $Pagina."<BR>";
			$Pagina = $Pagina."<P><CENTER>";
			$Pagina = $Pagina."<IMG SRC='" . $SecureServerType . "://" . $SecureServer . $ServerNumber . $DomainPostfix . "/impuestos/LOGO_BB.GIF' ALIGN= 'TOP' Banco del Bajio>";
			$Pagina = $Pagina."</P></CENTER>";
			$Pagina = $Pagina."<H2>" . $PageTitle . "</H2>";
			$Pagina = $Pagina."<BR><hr noshade='noshade' size='4' color='#5A419c' />";
			$Pagina = $Pagina."<P ALIGN='RIGHT'><B><FONT>$date $time</FONT></B></P>";
			$Pagina = $Pagina.$buffer2;
			$Pagina = $Pagina."<BR><hr noshade='noshade' size='4' color='#5A419c' />";
			$Pagina = $Pagina."<p><FONT SIZE=1>Av. Manuel J. Clouthier No. 508 Col. Jardines del Campestre CP 37128 León, Gto.</FONT> </p>";
			$Pagina = $Pagina."<p><FONT SIZE=1>Consultas y aclaraciones 01 (477) 710- 4600 o al 01 (800) 471-0400</FONT></p>";
			$Pagina = $Pagina."<p><FONT SIZE=1>LA TRANSFERENCIA A LA QUE HACE REFERENCIA ESTE COMPROBANTE FUE REALIZADA CONFORME A LA INFORMACIÓN E INSTRUCCIONES QUE NUESTRO CLIENTE ALIMENTÓ EN EL SISTEMA DE BANCO DEL BAJÍO, POR LO TANTO EL CLIENTE ES EL ÚNICO RESPONSABLE PRESENTE Y FUTURO POR CUALQUIER ERROR U OMISIÓN EN ESTA OPERACIÓN.</FONT></P>";
			//$Pagina = $Pagina."<p><FONT SIZE=1>EL ÚNICO COMPROBANTE OFICIAL DE ESTA TRANSACCIÓN ES EL ESTADO DE CUENTA QUE EMITE BANCO DEL BAJIO.</FONT></p>"; ATAR se comenta por especificaciones del SAT
			$Pagina = $Pagina."</BODY></HTML>\"; NFW =";
			$Pagina = $Pagina." window.open(\"\",'Recibo',\"toolbar=no,menubar=no,scrollbars=2,\"+s); NFW.blur(); NFW.resizeTo(windowW,windowH); NFW.moveTo(windowX,windowY); NFW.";
			$Pagina = $Pagina."document.open(); NFW.document.write(cadena); NFW.document.close(); NFW.window.focus(); } ventana(); </SCRIPT>";
		
		}
		//ATAR Imprimir correctamente el SAT REferenciado 01SEP2014
	}
///////////////////////////// YEHO F 13Abr2011

	// AGG I 25Nov2005 Generacion de comprobantes
//ACS 17Ene2011 Impresión de comprobantes Inicio
	//if ($Access == "Process" and strpos($transaccion_buffer,"No. de Autoriza") != false)
	if ($Access == "Process" and (strpos($transaccion_buffer,"mero de Autoriza") != false or strpos($transaccion_buffer,"No. de Autoriza")))
//ACS 17Ene2011 Impresión de comprobantes Fin
	{
		$buffer2 = str_replace(chr(34),chr(92).chr(34),$transaccion_buffer);
		if (strpos($transaccion_buffer,"El saldo disponible") === false ) //si no tiene saldos
		{
			$buffer2 = str_replace(chr(34),chr(92).chr(34),$transaccion_buffer);
		}
		else //si tiene saldos hay que quitarlos
		{
//JAG 02may2011 inicio
			$buffer5 = substr($transaccion_buffer,strpos($transaccion_buffer,"Comi"));

			$buffer3 = substr($transaccion_buffer,0,strpos($transaccion_buffer,"<B>El saldo disponible"));

			$buffer2 = str_replace(chr(34),chr(92).chr(34),$buffer3.$buffer5);
/*
			$buffer2 = str_replace(chr(34),chr(92).chr(34),$transaccion_buffer);


			if (strpos($transaccion_buffer,"Para clientes que requieren comprobante") === false ) // si no tiene DFA
			{
				$buffer3 = substr($transaccion_buffer,0,strpos($transaccion_buffer,"El saldo disponible"));
				if (strpos($$transaccion_buffer, "No. de Folio") === false )
				{
//ACS 17Ene2011 Impresión de comprobantes Inicio
					//$buffer4 = substr($transaccion_buffer,strpos($transaccion_buffer,"No. de Autoriza"));
					if (strpos($transaccion_buffer, "No. de Autoriza") !== false )
					{
					$buffer4 = substr($transaccion_buffer,strpos($transaccion_buffer,"No. de Autoriza"));
					}
					else if (strpos($transaccion_buffer, "mero de Autoriza") !== false )
					{
						$buffer4 = substr($transaccion_buffer,strpos($transaccion_buffer,"mero de Autoriza")-9);
					}

//ACS 17Ene2011 Fin
				}
				else
				{
					$buffer4 = substr($transaccion_buffer,strpos($transaccion_buffer,"No. de Folio"));
				}
				$buffer2 = str_replace(chr(34),chr(92).chr(34),$buffer3.$buffer4);
			}
			else
			{
				$buffer3 = substr($transaccion_buffer,0,strpos($transaccion_buffer,"El saldo disponible"));
				$buffer4 = substr($transaccion_buffer,strpos($transaccion_buffer,"Para clientes que requieren comprobante"));
				$buffer2 = str_replace(chr(34),chr(92).chr(34),$buffer3.$buffer4);
			}
			*/
//JAG 02may2011 fin
		}
//ACS 17Ene2011 Impresión de comprobantes Inicio
		//$buffer2 = substr($buffer2,0,strpos($buffer2,"(Por favor anote el "));
		$buffer2 = substr($buffer2,0,strpos($buffer2,"(Por favor anote"));
//ACS 17Ene2011 Impresión de comprobantes Fin

		$ini =& INIFile::globalINI();
		$SecureServer = $ini->read_var( "site", "SecureServer" );
		$SecureServerType = $ini->read_var( "site", "SecureServerType" );
		$DomainPostfix = $ini->read_var( "site", "DomainPostfix" );
		$ServerNumber = $ini->read_var( "site", "ServerNumber" );

		if ( $Comprobante == "Activo" )
		{
		$Pagina = "<SCRIPT LANGUAGE='JavaScript'> function ventana() { var windowW = 800; var windowH = 450; var windowX = 10; var windowY = 10; var title = 'Recibo'; s = 'width='+windowW+',height='+windowH; cadena=\"<HTML><HEAD><link REL='STYLESHEET' HREF='" . $SecureServerType . "://" . $SecureServer . $ServerNumber . $DomainPostfix . "/impuestos/recibos.css' TYPE='text/css'><TITLE>Autorización</TITLE>";
		$Pagina = $Pagina."<SCRIPT LANGUAGE='JavaScript'> function window_onbeforeprint() { Imprimir.style.visibility = 'hidden'; Salir.style.visibility = 'hidden';} function window_onafterprint() { Imprimir.style.visibility = 'visible'; Salir.style.";
		$Pagina = $Pagina."visibility = 'visible'; }\"+\"<\"+\"/\"+\"SCRIPT></HEAD><BODY LANGUAGE='javascript' onbeforeprint='return window_onbeforeprint()' onafterprint='return window_onafterprint()'>";
		$Pagina = $Pagina."<INPUT type='button' value='Imprimir' id=Imprimir name=Imprimir onclick='window.print();'>&nbsp;<INPUT type='button' value=' Salir  ' id=Salir name=Salir onclick='window.close();'>";
		$Pagina = $Pagina."<BR>";
		$Pagina = $Pagina."<P><CENTER>";
		$Pagina = $Pagina."<IMG SRC='" . $SecureServerType . "://" . $SecureServer . $ServerNumber . $DomainPostfix . "/impuestos/LOGO_BB.GIF' ALIGN= 'TOP' Banco del Bajio>";
		$Pagina = $Pagina."</P></CENTER>";
		$Pagina = $Pagina."<H2>Autorización</H2>";
		$Pagina = $Pagina."<BR><hr noshade='noshade' size='4' color='#5A419c' />";
		$Pagina = $Pagina."<P ALIGN='RIGHT'><B><FONT>$date $time</FONT></B></P>";
		$Pagina = $Pagina.$buffer2;
		$Pagina = $Pagina."<BR><hr noshade='noshade' size='4' color='#5A419c' />";
		$Pagina = $Pagina."<p><FONT SIZE=1>Av. Manuel J. Clouthier No. 508 Col. Jardines del Campestre CP 37128 León, Gto.</FONT> </p>";
		$Pagina = $Pagina."<p><FONT SIZE=1>Consultas y aclaraciones 01 (477) 710- 4600 o al 01 (800) 471-0400</FONT></p>";
		$Pagina = $Pagina."<p><FONT SIZE=1>LA TRANSFERENCIA A LA QUE HACE REFERENCIA ESTE COMPROBANTE FUE REALIZADA CONFORME A LA INFORMACIÓN E INSTRUCCIONES QUE NUESTRO CLIENTE ALIMENTÓ EN EL SISTEMA DE BANCO DEL BAJÍO, POR LO TANTO EL CLIENTE ES EL ÚNICO RESPONSABLE PRESENTE Y FUTURO POR CUALQUIER ERROR U OMISIÓN EN ESTA OPERACIÓN.</FONT></P>";
		$Pagina = $Pagina."<p><FONT SIZE=1>EL ÚNICO COMPROBANTE OFICIAL DE ESTA TRANSACCIÓN ES EL ESTADO DE CUENTA QUE EMITEN BANCO DEL BAJIO Y EL BANCO RECEPTOR DE ESTA TRANSACCIÓN.</FONT></p>";
		$Pagina = $Pagina."</BODY></HTML>\"; NFW =";
		$Pagina = $Pagina." window.open(\"\",'Recibo',\"toolbar=no,menubar=no,scrollbars=2,\"+s); NFW.blur(); NFW.resizeTo(windowW,windowH); NFW.moveTo(windowX,windowY); NFW.";
		$Pagina = $Pagina."document.open(); NFW.document.write(cadena); NFW.document.close(); NFW.window.focus(); } ventana(); </SCRIPT>";
		}
		//DBA Reimpresion de Comprobantes
		/*
		$FechaHora = $date . " " . $time;
		$QryRIC = new eZReImp( );
		$QryRIC -> store($CustID,$FrAccount,$buffer2,"aut","Autorizaci&oacute;n",$FechaHora);*/
		//DBA Reimpresion de Comprobantes
	}
	if ($Access == "Process" and (strpos($transaccion_buffer,"Autorizaci&oacute;n") ))
	//ACS 17Ene2011 Impresión de comprobantes Fin
	{
		//DBA Reimpresion de Comprobantes
	/*	$FechaHora = $date . " " . $time;
		$QryRIC = new eZReImp( );
		$QryRIC -> store($CustID,$FrAccount,$buffer2,"aut","Autorizaci&oacute;n",$FechaHora);*/
		//DBA Reimpresion de Comprobantes
		//DBA Reimpresion de Comprobantes
		$FechaHora = "";
		$QryRIC = new eZReImp( );
		$QryRIC -> store($CustID,$FrAccount,$buffer2,"imp","",$FechaHora);
		//echo "YEHo mando a guardar en reimpresion [$CustID] [$FrAccount] [$FechaHora] [$buffer2]";
		//DBA Reimpresion de Comprobantes
	}
    $t->set_var( "transaccion_buffer", $transaccion_buffer.$Pagina );
	// AGG F 25Nov2005 Generacion de comprobantes

    //$t->set_var( "transaccion_buffer", $transaccion_buffer );
	//$t->pparse( "output", "autorizacion_tpl" );
    $transaccion_buffer=$t->parse( "output", "autorizacion_tpl" );
	$transaccion_buffer=modSubmit($transaccion_buffer);
	$transaccion_buffer=$procesarAjax.$transaccion_buffer;

?>
