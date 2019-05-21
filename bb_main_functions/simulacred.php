<?php
//
//
// Created on: <1-Nov-2001 17:37:53 smb>
//
// This source file is part of IVA.
//
// Copyright (C) 1997-2004 Internet de Alta Calidad, S.A. de C.V.  Todos los derechos reservados. All rights reserved.
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, US
//
// DebugBreak();

//RVE Inicio	 24Sep2010 Implementacion Webservice ----Track ID>169678 ------------------Inclusion parser -----
require_once("eztransaccion/user/include/XMLToArray.php");
require_once("nusoap-0.7.3/lib/nusoap.php");
//RVE Fin	 24Sep2010 Implementacion Webservice ----Track ID>169678 ------------------Inclusion parser -----
include_once( "classes/INIFile.php" );
include_once( "classes/eztemplate.php" );
include_once( "classes/ezhttptool.php" );

include_once( "eztransaccion/classes/eztransaccion.php" );
include_once( "ezsession/classes/ezsession.php" );

// JAC NXN 14ENE2013 INI
function get_include_contents($filename) {
    if (is_file($filename)) {
        ob_start();
        include $filename;
        return ob_get_clean();
    }
    return false;
}

function number2month( $number ) 
{
	$month = "";
	switch( $number ) 
	{
		case '01': $month = "Ene"; break;
		case '02': $month = "Feb"; break;
		case '03': $month = "Mar"; break;
		case '04': $month = "Abr"; break;
		case '05': $month = "May"; break;
		case '06': $month = "Jun"; break;
		case '07': $month = "Jul"; break;
		case '08': $month = "Ago"; break;
		case '09': $month = "Sep"; break;
		case '10': $month = "Oct"; break;
		case '11': $month = "Nov"; break;
		case '12': $month = "Dic"; break;
	}
	return $month;
}
// JAC NXN - 14ENE2013 FIN

$session =& eZSession::globalSession();

if( !$session->fetch() )
    $session->store();

$ini =& $GLOBALS["GlobalSiteIni"];

$Language = $ini->read_var( "eZTransaccionMain", "Language" );
// $backend = $ini->read_var( "eZTransaccionMain", "Backend" );
$ServerNumber = $ini->read_var( "site", "ServerNumber" );
$DomainPostfix = $ini->read_var( "site", "DomainPostfix" );
//RVE Inicio	 24Sep2010 Implementacion Webservice ----Track ID>169678 ------------------Variable Globales -----
$IsiUrlWSDL = $ini->read_var( "site", "IsiUrlWSDL" );
$UsrIsiWSDL = $ini->read_var( "site", "UsrIsiWSDL" );
$MensajeErrorISI = "";
$estadOperacion ="";
$creditos = array();
$colorRows =""; //Color de la fila
$htmlRowsCreditos= ""; //sólo contendrá trs de htmls precargados.
//RVE Fin	 24Sep2010 Implementacion Webservice ----Track ID>169678 ------------------Variable Globales-----

//RVE Inicio	24Sep2010 Implementacion Webservice ----Track ID>169678 ------------------Funciones -----
function generateHTMLRowsTable($nl_tipoCredito, $nl_nroCredito, $nl_fechaPago, $nl_capital, $nl_interes, $nl_ivaInteres, $nl_seguroVida, $nl_seguroDanios, $nl_prepagoCapital, $nl_montoDelPago, $nl_saldo)
{
	//echo "<BR><BR>===========>==> ROW HTML de la tabla <====".  $nl_tipoCredito . " - "  . $nl_nroCredito ."=============<BR><BR>";

	// JAC NXN - 14JAN2013 INI
	// $GLOBALS["htmlRowsCreditos"] .= "<TR ID=row_credito_" . $nl_fechaPago. " style=\"display:block\">"; //none or table-row
	$GLOBALS["htmlRowsCreditos"] .= "<TR ID='row_credito_" . $nl_fechaPago. "'>"; //none or table-row	
	$GLOBALS["htmlRowsCreditos"] .= "<TD  align=right BGCOLOR=".$GLOBALS["colorRows"]." class='form_grid' ID='cell_fechaPago_" . $nl_fechaPago. "'>" .  $nl_fechaPago . "</TD>";
	$GLOBALS["htmlRowsCreditos"] .= "<TD  align=right BGCOLOR=".$GLOBALS["colorRows"]." class='form_grid' ID='cell_capital_" . $nl_fechaPago. "'>" .  number_format($nl_capital, 2, ".", ",") . "</TD>";
	$GLOBALS["htmlRowsCreditos"] .= "<TD  align=right BGCOLOR=".$GLOBALS["colorRows"]." class='form_grid' ID='cell_interes_" . $nl_fechaPago. "'>" .  number_format($nl_interes, 2, ".", ",") . "</TD>";
	$GLOBALS["htmlRowsCreditos"] .= "<TD  align=right BGCOLOR=".$GLOBALS["colorRows"]." class='form_grid' ID='cell_ivaInteres_" . $nl_fechaPago. "'>" .  number_format($nl_ivaInteres, 2, ".", ",") . "</TD>";
	$GLOBALS["htmlRowsCreditos"] .= "<TD  align=right BGCOLOR=".$GLOBALS["colorRows"]." class='form_grid' ID='cell_seguroVida_" . $nl_fechaPago. "'>" .  number_format($nl_seguroVida, 2, ".", ",") . "</TD>";
	$GLOBALS["htmlRowsCreditos"] .= "<TD  align=right BGCOLOR=".$GLOBALS["colorRows"]." class='form_grid' ID='cell_seguroDanios_" . $nl_fechaPago. "'>" .  number_format($nl_seguroDanios, 2, ".", ",") . "</TD>";
	$GLOBALS["htmlRowsCreditos"] .= "<TD  align=right BGCOLOR=".$GLOBALS["colorRows"]." class='form_grid' ID='cell_prepagoCapital_" . $nl_fechaPago. "'>" .  number_format($nl_prepagoCapital, 2, ".", ",") . "</TD>";
	$GLOBALS["htmlRowsCreditos"] .= "<TD  align=right BGCOLOR=".$GLOBALS["colorRows"]." class='form_grid' ID='cell_montoDelPago_" . $nl_fechaPago. "'>" .  number_format($nl_montoDelPago, 2, ".", ",") . "</TD>";
	$GLOBALS["htmlRowsCreditos"] .= "<TD  align=right BGCOLOR=".$GLOBALS["colorRows"]." class='form_grid' ID='cell_saldo_" . $nl_fechaPago. "'>" .  number_format($nl_saldo, 2, ".", ",") . "</TD>";
	$GLOBALS["htmlRowsCreditos"] .= "</TR>";
	// JAC NXN - 14JAN2013 FIN
}

function loadXmlToSession($xmlOvation)
{
	$xml2a		= new XMLToArray();

	$root_node	= $xml2a->parse($xmlOvation);


	if($root_node["_NAME"] == "root")
	{
		//Datos del credito sobre el cual se va consultar a ISI
		$GLOBALS["creditos"] = $root_node["_ELEMENTS"][0]["_ELEMENTS"][0];
	}

	$GLOBALS["estadOperacion"] = "OK";

	return 	$GLOBALS["estadOperacion"];
}

function callWebServiceISI($FrAccount, $FrDate, $FrAmount)	// JAC NXN - 14JAN2013 
{
		$GLOBALS["MensajeErrorISI"] = "";
		list( $disp, $type ) = explode( "-", $FrAccount );	// JAC NXN - 14JAN2013

		//echo "========callWebServiceISI=============>Armando array para webservice<======================<BR>";
		// JAC NXN - 14JAN2013 INI
		// $param=array('usuario'=>$GLOBALS["UsrIsiWSDL"], 'nro_credito'=> $GLOBALS["creditos"]["nro_credito"], 'monto_prepago'=>$GLOBALS["creditos"]["monto_prepago"], 'fecha_prepago'=>$GLOBALS["creditos"]["fecha_prepago"]);
		$param=array('usuario'=>$GLOBALS["UsrIsiWSDL"], 'nro_credito'=> $disp, 'monto_prepago'=>$FrAmount, 'fecha_prepago'=>$FrDate);
		// JAC NXN - 14JAN2013 FIN

		//echo "<BR><BR>==callWebServiceISI=> REQUEST A ISI ->array contiene el request a enviar<====<BR><BR>";
		//print_r($param);

		$wsdl= $GLOBALS["IsiUrlWSDL"];
		$client= new nusoap_client($wsdl,'wsdl');

		//echo "<BR><BR>==========> Llamo al webservice <===============<BR>";
		$pagosResponse = $client->call('ConsultaPagos', $param);

		if ($client->fault)
		{
			//echo "No se pudo completar la operación";
			$GLOBALS["MensajeErrorISI"] = $pagosResponse["detail"]["Error"]["ErrorMessage"];
			$GLOBALS["estadOperacion"] = "ERROR_WEBSERVICE_ISI";
			return $GLOBALS["estadOperacion"];
			//die();
		}
		else
		{
			$error = $client->getError();
			if( $error )
			{
				//echo "==========> Error al llamar al webservice:" . $error . "<BR><BR>";
				$GLOBALS["estadOperacion"] = "ERROR_WEBSERVICE_ISI";
				return $GLOBALS["estadOperacion"];
			}
		}
		//echo "<BR><BR>=========> Respuesta del webservice <=================<BR>";
		//print_r($pagosResponse);

		if($pagosResponse["ConsultaPagosResult"]["cantidad_registros"] > 0)
		{
			$pagosResponse_aux = $pagosResponse["ConsultaPagosResult"]["pagos"]["Pagos"];
		}
		else
		{
			$GLOBALS["estadOperacion"] = "SIN_PAGOS";
			return $GLOBALS["estadOperacion"];
		}

		//echo "<BR><BR>=========> Actualizo variable global <=================";
		$i= 0;
		$GLOBALS["colorRows"] = "#FFFFFF";

		for($i; $i<count($pagosResponse_aux);$i++)
		{
			//Creo todos los rows para despues hacer el pagina sin refresh
			// JAC NXN - 14JAN2013 INI
			// generateHTMLRowsTable($GLOBALS["creditos"]["tipo_credito"], $GLOBALS["creditos"]["nro_credito"], $pagosResponse_aux[$i]['fecha_pago'], $pagosResponse_aux[$i]['capital'], $pagosResponse_aux[$i]['interes'], $pagosResponse_aux[$i]['iva_interes'], $pagosResponse_aux[$i]['seguro_vida'], $pagosResponse_aux[$i]['seguro_danios'], $pagosResponse_aux[$i]['prepago_capital'], $pagosResponse_aux[$i]['monto_del_pago'], $pagosResponse_aux[$i]['saldo']);
			generateHTMLRowsTable($type, $disp, $pagosResponse_aux[$i]['fecha_pago'], $pagosResponse_aux[$i]['capital'], $pagosResponse_aux[$i]['interes'], $pagosResponse_aux[$i]['iva_interes'], $pagosResponse_aux[$i]['seguro_vida'], $pagosResponse_aux[$i]['seguro_danios'], $pagosResponse_aux[$i]['prepago_capital'], $pagosResponse_aux[$i]['monto_del_pago'], $pagosResponse_aux[$i]['saldo']);
			// JAC NXN - 14JAN2013 FIN

			if($i%2)
				$GLOBALS["colorRows"] = "#EFEFF0";
			else
				$GLOBALS["colorRows"] = "#FFFFFF";
		}
		//echo "<BR><BR>=========> Array global GLOBALS[htmlRowsCreditos] <=================<BR><BR>";
		//print_r($GLOBALS["htmlRowsCreditos"]);
		return $GLOBALS["estadOperacion"];
}

//RVE Fin	 24Sep2010 Implementacion Webservice ----Track ID>169678 ------------------Funciones -----

$backend = "gw" . $ServerNumber. $DomainPostfix;
if ( $GLOBALS["DEBUGA"] == true ) {
		$log = "En ".$_SERVER['SCRIPT_FILENAME']." que es ".$_SERVER['PHP_SELF']." (backend) ->" . print_r( $backend, true ) . "|";
        eZLog::writeNotice( $log );
		define_syslog_variables();
		openlog( "IVA", LOG_PID | LOG_PERROR, LOG_DAEMON );
		syslog( LOG_WARNING, "$log" );
		closelog();
}

include_once( "ezuserbb/classes/ezuser.php" );

$user =& eZUserBB::currentUser();

if ( $GLOBALS["DEBUGA"] == true ) {
        eZLog::writeNotice( "En simulacred (user) ->" . print_r( $user, true ) . "|" );
}

// DebugBreak();

if ( $user )
{
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "simulacred.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "simulacred_tpl" => "simulacred.tpl"
        ) );

    $session =& eZSession::globalSession();

    if ( !$session->fetch() )
    {
        $session->store();
    }

    $tr = new eZTransaccion( );
    $usr = $session->variable( "r_usr" );
    $qki = $session->variable( "r_qki" );
    $priv = $session->variable( "r_priv" );
	$cust_id = $session->variable( "r_cno" ); //Este es el nro de cliente JAC NXN 14ENE2012
    if( empty( $Access ) )
    {
            $Access = "FrAc";
    }
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
	// JAC NXN - 14JAN2013 INI
	if( !isset( $lastCreditNo ) ) 
	{
			$lastCreditNo = "0";
	}
	if( !isset( $regs ) ) 
	{
			$regs = 0;
	}
	if( !isset( $page ) ) 
	{
			$page = 1;
	}
	if( !isset( $limit ) ) 
	{
			$limit = 20; 	// 20 registros max.
	} 
	if( !isset( $codLinea ) ) 
	{
			$codLinea = '0'; 	// todos los creditos disponibles.
	}
	// JAC NXN - 14JAN2013 FIN
    switch( $Access )
    {
            case "FrAc":
					// JAC NXN - 14JAN2013 INI
                    // $particularFields = "&Accion=Simula";
					include( "eztransaccion/user/include/cred_utils.inc" );
					
					$wsdl = $ini->read_var( "site", "Cred_ISI" );
					$saved .= cleanInnerSession( "saldoscre" ) . '\n';
					$userId = $usr;
					$custId = $cust_id;
					$regs = 0;
					eZLog::writeNotice( "::::::Page        [$page]::::::" );
					eZLog::writeNotice( "::::::Regs        [$regs]::::::" );
					eZLog::writeNotice( "::::::WSDL        [$wsdl]::::::" );
					eZLog::writeNotice( "::::::Limit       [$limit]::::::" );
					eZLog::writeNotice( "::::::UserId      [$userId]::::::" );
					eZLog::writeNotice( "::::::CustId      [$custId]::::::" );
					eZLog::writeNotice( "::::::CodLinea    [$codLinea]::::::" );
					eZLog::writeNotice( "::::::LastCreditNo[$lastCreditNo]::::::" );
					$ctas = getOnlyAccounts( $userId, $custId );
					if ( $ctas == "SIN_CLIENTES_AGRUPADOS" ) 
					{
						// Solo consulto saldos de credito para el cliente logueado.
						$accounts = Array( 0 => $custId );
					}
					else 
					{
						$accounts = explode( "|", $ctas );
					}
					
					//////////////////////////////////////////////////////////////////////////////////////
					////////// SERVICIO: obtenerSaldosCreditos ///////////////////////////////////////////
					//////////////////////////////////////////////////////////////////////////////////////
					$opt = "";
					$transaccion_buffer = "";
					eZLog::writeNotice( ":::::: Accounts[". count( $accounts ) ."] ::::::" );
					eZLog::writeNotice( ":::::: Accounts[". print_r( $accounts, true ) ."] ::::::" );
					// for( $i = 0; $i <= count( $accounts ); $i++ ) 
					foreach( $accounts as $k => $value )
					{
						eZLog::writeNotice( ":::::: VALUE[$value] ::::::" );
						if ( strlen( $value ) != 0 ) 
						{
							$cust = $value;
							if ( !isset( $_SESSION['saldoscre_page_'.$page] ) ) 
							{
								// call web service and save results in session.
								$log = "";
								$_SESSION['saldoscre_page_'.$page] = getSaldosCreditos( $wsdl, $userId, $cust, $limit, $codLinea, $lastCreditNo, $log  );
								$_SESSION['saldoscre_page_'.$page.'_rows'] = $regs + count( $_SESSION['saldoscre_page_'.$page] );
								$saved .= $log . "\n";
							} 
							eZLog::writeNotice( "::::::LOG[".print_r($log, true)."]::::::" );
							$dummy = $_SESSION['saldoscre_page_'.$page];
							/** TODO: VERIFICAR LA NECESIDAD DE NAVEGABILIDAD.........
							if ( $dummy[ count( $dummy ) - 1 ]['more'] === "TRUE" ) 
								$total_pages++;
							*/
							
							// last credit
							$nroCredito = $dummy[ $count - 1 ]['disp'];
							
							foreach( $dummy as $key => $array ) 
							{ 
								$cell = "";
								$cell = $cust . $array['tip_cred'] . "-" . $array['disp'];
								eZLog::writeNotice( "::::::CREDITO[". $array['disp'] . "] ::::::" );
								$opt .= "<OPTION VALUE='". $array['disp'] ."-". $array['tip_cred'] ."'>$cell</OPTION>";
							}
							$page++;
						}
					}
					if ( strlen( $opt ) !== 0 ) 
					{
						$select = "<SELECT NAME='FrAccount'>" . $opt . "</SELECT>";
						$header = get_include_contents( "statics/sim.hdr" );
						$footer = get_include_contents( "statics/sim.ftr" );
						$header = str_replace( "~c", $userId, $header );
						$transaccion_buffer .= $header . $select . $footer;
					}
					else 
					{
						// NO EXISTEN CREDITOS DESPONIBLES
						$transaccion_buffer = get_include_contents( "statics/rc1409.htm" );
					}
					// JAC NXN - 14JAN2013 FIN
                    break;
            case "Confirm":
            case "Process":
					// JAC NXN - 14JAN2013 INI
                        //$particularFields = "&FrAccount=".urlencode( $FrAccount )."&Accion=Simula&Day=".urlencode( $Day )."&Month=".urlencode( $Month )."&Year=".urlencode( $Year )."&Pos=".urlencode( $Pos )."&FrAmount=".urlencode( $FrAmount )."&Dias=".urlencode( $Dias )."&FrNo=".urlencode( $FrNo );
					// VALIDACIONES
					if( !is_numeric( $FrAmount ) || !is_numeric( $Day ) || !is_numeric( $Month ) || !is_numeric( $Year ) ) 
					{
						$transaccion_buffer = get_include_contents( "statics/rc3018.htm" );
					}
					else 
					{
						list( $disp, $type ) = explode( "-", $FrAccount );
						$fecha = $Day . number2month( $Month ) . $Year;
						$htmlAnterior = get_include_contents( "statics/simloan.hdr" );
						$htmlPosterior = get_include_contents( "statics/simloan.ftr" );
						$htmlAnterior = str_replace( "~c", $usr, $htmlAnterior );
						$htmlAnterior = str_replace( "~m", $disp . " " . $type, $htmlAnterior );
						$htmlAnterior = str_replace( "~6", $FrAmount, $htmlAnterior );
						$htmlAnterior = str_replace( "~z", $fecha, $htmlAnterior );
						$result = callWebServiceISI($FrAccount, $Day . $Month . $Year, $FrAmount);

						if($result == "SIN_PAGOS")
						{
							$GLOBALS["htmlRowsCreditos"] .= "<TR style=\"display:block\">";
							$GLOBALS["htmlRowsCreditos"] .= " <TD colspan=8 align=center BGCOLOR=#EFEFF0 class='form_grid' ><BR>No se han encontrado pagos actuales en el sistema ISI asociados a este crédito.<BR></TD></TR></TABLE>";
							$transaccion_buffer = $htmlAnterior . $GLOBALS["htmlRowsCreditos"] . $htmlPosterior;
						}
						else if($result == "ERROR_WEBSERVICE_ISI")
						{
							$MensajeError .= "<TABLE ALIGN=CENTER BORDER=0 WIDTH='100%'>";
							$MensajeError .= "<TR style=\"display:block\">";

							if($GLOBALS["MensajeErrorISI"] != "")
							{

								$MensajeError .= " <TD colspan=8 align=center BGCOLOR=#EFEFF0 class='form_grid' ><BR>Ha ocurrido un error de conexi&oacute;n. Se ha superado el tiempo de espera para la operaci&oacute;n, favor de intentar mas tarde. <BR><BR> Si el problema persiste comun&iacute;quese a nuestro Servicio Atiende 01 800 47 10 400.<BR><BR>".$GLOBALS["TiempoWSPeticion"]."<BR><BR>". $GLOBALS["MensajeErrorISI"] ."<BR></TD></TR>";
							}else
							{

								$MensajeError .= " <TD colspan=8 align=center BGCOLOR=#EFEFF0 class='form_grid' ><BR>Ha ocurrido un error de conexi&oacute;n. Se ha superado el tiempo de espera para la operaci&oacute;n, favor de intentar mas tarde. <BR> Si el problema persiste comun&iacute;quese a nuestro Servicio Atiende 01 800 47 10 400.<BR><BR>".$GLOBALS["TiempoWSPeticion"]."<BR></TD></TR>";
							}
							$MensajeError .= "</TR></TABLE>";

							$transaccion_buffer = $htmlAnterior . $MensajeError . $htmlPosterior;
						}
						else
						{
							$transaccion_buffer = $htmlAnterior . $GLOBALS["htmlRowsCreditos"] . $htmlPosterior;
						}					
					}
					// eZLog::writeNotice( "::::::TRANSACCION BUFFER...[". print_r( $transaccion_buffer, true ) ."]:::" );
					// JAC NXN - 14JAN2013 FIN
                    break;
    }
// JAC NXN - 14JAN2013 INI
//    $transaccion_buffer = "";
//    $ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=sim&Access=".$Access."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // Saldos
//    $tr->blog($qki,"Saldos", $FrAccount, $Day, $Month, $Year, $Accion);
//	//eZLog::writeNotice( "::::::TRANSACCION BUFFER...[". print_r( $transaccion_buffer, true ) ."]:::" );
//
//    //RVE Inicio 24Sep2010 Implementacion Webservice ----Track ID>169678 ------------------Variable Globales-----
//    //--------------------------------------Nuevo codigo obteniendo datos del webservice------------------------------------
//    $inicioXML = 0;
//    $finXML = 0;
//    $htmlAnterior = "";
//    $htmlPosterior = "";
//
//    $inicioXML =  strpos($transaccion_buffer,"<mensaje_php>");
//    $finXML = strpos($transaccion_buffer,"</mensaje_php>");
//
//    if ($inicioXML > 0)
//    {
//		//Separo el XML que llega de Ovation
//		$htmlAnterior  = substr($transaccion_buffer, 0, $inicioXML);
//		$htmlPosterior = substr($transaccion_buffer, $finXML);
//		$xmlContent    = substr($transaccion_buffer, $inicioXML, $finXML);
//
//		//echo "<BR><BR>===XML enviado por Ovation<BR>";
//		//print_r($xmlContent);
//
//		//echo "<BR><BR>===Creo array global cargando el xml enviado por Ovation<BR>";
//		$result = loadXmlToSession($xmlContent);
//
//		//echo "<BR><BR>===Resultado de loadXmlToSession " . $result . "<BR>";
//		if( $result == "OK")
//		{
//			//echo "<BR><BR>===Consulto a webservice<BR>";
//			$result = callWebServiceISI();
//
//			//echo "<BR><BR>===resultado de webservice ",$result,"===<BR>";
//			if($result == "SIN_PAGOS")
//			{
//				$GLOBALS["htmlRowsCreditos"] .= "<TR style=\"display:block\">";
//				$GLOBALS["htmlRowsCreditos"] .= " <TD colspan=8 align=center BGCOLOR=#EFEFF0 class='form_grid' ><BR>No se han encontrado pagos anticipados en el sistema ISI asociados a este crédito credito.<BR></TD></TR></TABLE>";
//				$transaccion_buffer = $htmlAnterior . $GLOBALS["htmlRowsCreditos"] . $htmlPosterior;
//			}
//			else if($result == "ERROR_WEBSERVICE_ISI")
//			{
//				$MensajeError .= "<TABLE ALIGN=CENTER BORDER=0 WIDTH='100%'>";
//				$MensajeError .= "<TR style=\"display:block\">";
//
//				if($GLOBALS["MensajeErrorISI"] != "")
//				{
//
//					$MensajeError .= " <TD colspan=8 align=center BGCOLOR=#EFEFF0 class='form_grid' ><BR>Ha ocurrido un error de conexi&oacute;n. Se ha superado el tiempo de espera para la operaci&oacute;n. <BR><BR> Si el problema persiste comun&iacute;quese a nuestro Servicio Atiende 01 800 47 10 400.<BR><BR>".$GLOBALS["TiempoWSPeticion"]."<BR><BR>". $GLOBALS["MensajeErrorISI"] ."<BR></TD></TR>";
//				}else
//				{
//
//					$MensajeError .= " <TD colspan=8 align=center BGCOLOR=#EFEFF0 class='form_grid' ><BR>Ha ocurrido un error de conexi&oacute;n. Se ha superado el tiempo de espera para la operaci&oacute;n. <BR> Si el problema persiste comun&iacute;quese a nuestro Servicio Atiende 01 800 47 10 400.<BR><BR>".$GLOBALS["TiempoWSPeticion"]."<BR></TD></TR>";
//				}
//				$MensajeError .= "</TR></TABLE>";
//				$transaccion_buffer = $htmlAnterior . $MensajeError . $htmlPosterior;
//			}
//			else
//			{
//				$transaccion_buffer = $htmlAnterior . $GLOBALS["htmlRowsCreditos"] . $htmlPosterior;
//			}
//		}
//		else
//		{
//			$MensajeError .= "<TABLE ALIGN=CENTER BORDER=0 CELLSPACING=2 CELLPADDING=2 WIDTH='100%'>";
//			$MensajeError .= "<TR style=\"display:block\">";
//			$MensajeError .= " <TD colspan=8 align=center BGCOLOR=#EFEFF0 class='form_grid' ><BR>Ha ocurrido un error al consultar los numeros de creditos. <BR> Si el problema persiste consulte con el soporte de la aplicación.<BR></TD></TR></TABLE>";
//			$transaccion_buffer = $htmlAnterior . $MensajeError . $htmlPosterior;
//		}
//    }
//    //--------------------------------------Nuevo codigo obteniendo datos del webservice------------------------------------
//    //RVE Fin 24Sep2010 Implementacion Webservice ----Track ID>169678 ------------------Variable Globales-----
// JAC NXN - 14JAN2013 FIN

    $t->set_var( "transaccion_buffer", $transaccion_buffer );
    $t->pparse( "output", "simulacred_tpl" );
}
else
{
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "userlogin.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "user_login_tpl" => "userlogin.tpl"
        ) );

    $t->set_var( "redirect_url", "/transaccion/simulacred/" );

    $t->pparse( "output", "user_login_tpl" );
}

?>
