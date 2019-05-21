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

include_once( "classes/INIFile.php" );
include_once( "classes/eztemplate.php" );
include_once( "classes/ezhttptool.php" );
//NXN Inicio 29sep2011 Implementacion Webservice ----Track ID> ------------------Inclusion parser -----
require_once( "eztransaccion/user/include/XMLToArray.php");
require_once( "nusoap-0.7.3/lib/nusoap.php");
//NXN Fin 29Sep2011 Implementacion Webservice ----Track ID> ------------------Inclusion parser -----

include_once( "eztransaccion/classes/eztransaccion.php" );
include_once( "ezsession/classes/ezsession.php" );

$session =& eZSession::globalSession();

if( !$session->fetch() )
    $session->store();

$ini =& $GLOBALS["GlobalSiteIni"];

$Language = $ini->read_var( "eZTransaccionMain", "Language" );
// $backend = $ini->read_var( "eZTransaccionMain", "Backend" );
$ServerNumber = $ini->read_var( "site", "ServerNumber" );
$DomainPostfix = $ini->read_var( "site", "DomainPostfix" );

$backend = "gw" . $ServerNumber. $DomainPostfix;
$snabackend = $ini->read_var( "eZTransaccionMain", "SNABackend" );
$snabackendport = $ini->read_var( "eZTransaccionMain", "SNABackendPort" );
$snafs = $ini->read_var( "eZTransaccionMain", "SNAFS" );
$snaappid = $ini->read_var( "eZTransaccionMain", "SNAAppID" );


include_once( "ezuserbb/classes/ezuser.php" );

$user =& eZUserBB::currentUser();

// DebugBreak();

//NXN Inicio     29sep2011 Implementacion Webservice ----Track ID>
function callWebServiceLockCredit($creditNumber)
{
    //echo "<BR><BR>=========> Ingreso a callWebServiceLockCredit <=[".$creditNumber."]========<BR>";

    //echo "<BR><BR>======Obtengo           WSDL===========<BR>";
    $ini =& $GLOBALS["GlobalSiteIni"];
    $tcUrlWSDL = $ini->read_var( "site", "TcUrlWSDL" );
    //echo "<BR><BR>======WSDL===>".$tcUrlWSDL ."<=========<BR>";

    //echo "<BR><BR>=========> Armando array para webservice<=========<BR>";
    $dataTC = array('creditCardNumber'=>$creditNumber);
    //$param=array('LockCreditCard'=> $dataTC);

    $param = $dataTC;

    //echo "<BR><BR>===> REQUEST A WS ->array contiene el request a enviar<====<BR><BR>";
    //print_r($param);

    //echo "<BR><BR>=====Creo cliente SOAP con nusoap ======================<BR>";
    //$wsdl = 'http://192.168.128.146/cclocker/WSCClocker.asmx?wsdl';
    $client= new nusoap_client($tcUrlWSDL,'wsdl');

    //echo "<BR><BR>==========> Llamo al webservice <===============<BR>";
    $wsResponse = $client->call('LockCreditCard', $param);

    //echo "<BR><BR>=========> Respuesta del webservice 1 <=================<BR>";
    //print_r($wsResponse);

    if ($client->fault)
    {
        echo "No se pudo completar la operacion";
        return "01";
        //die();
    }
    else
    {
        $error = $client->getError();
        if( $error )
        {
            echo "<br>Ocurrio un error al procesar el pedido - " . $error . "<BR><BR>";
            return "02";
        }

    }

    //echo "<BR><BR>=========> FIN <=================<BR><BR>";
    return $wsResponse['LockCreditCardResult'] ;
}
//NXN Fin     29sep2011 Implementacion Webservice ----Track ID>

/*!
  Reports a problem with SNA to Ovation.
*/
function ReportSNAError( $backend, $usr, $priv, $qki, $tr, $Accion, $fs, $sna_transaccion_buffer ) {
        list ( $no_pelar, $error ) = explode ( chr($fs), $sna_transaccion_buffer);
        $_POST['Access'] = "Process";
        $Day = "";
        $EspecCh = "";
        $_POST['PAN'] = "9999";
        $Nomben2 = "";
        $Empresa = "";
        $UbNo = "";
        $Parent1 = ucfirst( strtolower( $sna_transaccion_buffer )) . ".";
        $Parent2 = "";
        $Parent3 = "";
        $transaccion_buffer = "";
        $particularFields = "&Accion=".urlencode($Accion)."&PAN=".urlencode($_POST['PAN'])."&Nomben2=".urlencode($Nomben2)."&EspecCh=".urlencode($EspecCh)."&UbNo=".urlencode($UbNo)."&Empresa=".urlencode($Empresa)."&Day=".urlencode($Day)."&Parent1=".urlencode($Parent1)."&Parent2=".urlencode($Parent2)."&Parent3=".urlencode($Parent3);
        // echo "<em>particularFields</em> " . var_dump ( $particularFields ) . "<br>\n";
        $ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=tct&Access=".urlencode($_POST['Access'])."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // tarjetas de credito

        return $transaccion_buffer;
}


if ( $user )
{
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "repextravioTC.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "repextravioTC_tpl" => "repextravioTC.tpl"
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
    $transaccion_buffer = "";
    $ret_code = 0;
    $sna_ret_code = 0;
//HB
	if(!empty($_POST['FrAccount']))
		$FrAccount = $_POST['FrAccount'];

	if(!empty($_POST['ToAccount']))
		$ToAccount = $_POST['ToAccount'];

	if(!empty($_POST['Amount']))
		$Amount = $_POST['Amount'];

	if(!empty($_POST['Accion']))
		$Accion = $_POST['Accion'];

	if(!empty($_POST['RFC']))
		$RFC = $_POST['RFC'];
//HB

    if(empty($_POST['Access'])) {
        $_POST['Access'] = "FrAc";
    }
    switch($_POST['Access']) {
    case "FrAc":
        $Accion  = "MenuTar";
        $_POST['DiasPzo'] = "";   // Cadena con lista de tarjetas p actualizar en ovation char de 150
        $particularFields = "&Accion=".urlencode($Accion);
        $tr->blog($qki,"TarjetasCto", $FrAccount, $ToAccount, $_POST['DiasPzo'], $Amount, $Accion);
        break;
    case "Confirm":
    case "Process":
            $Operacion = "Extravio";	//FAF Cambio Nuevo Menu
            $Accion = $Operacion; // Para cachar el radio button.
        break;
    }
    $transaccion_buffer = "";
    $sna_transaccion_buffer = "";

    $rfc = $RFC;
    $pan = $_POST['PAN'];
    $tarjeta = "";
    $tipo = "";
    $nombre = "";
    $html_de_salida = "";
    $hubo_error = 0;
    $sna_call_ret_code = 0;
    $sna_ret_code = 0;
    $sna_ver_code = 0;
    list( $tarjeta, $tipo, $nombre ) = explode( "-", $pan);

	switch($Accion) {
		case "MenuTar":     // Menu, nada que hacer.
		break;

	    case "Extravio":    // Extravio

		// - Definir la transaccion a realizar
		$AccionSNA = "RepRoboExtravio";

		//NXN Inicio  29sep2011 Implementacion Webservice ----Track ID>
       	    if( $_POST['Access'] == "Process") {
             //echo "<BR><BR>===Consulto a webservice<BR>";
            $resultWS  = callWebServiceLockCredit($tarjeta);
            //echo "<BR><BR>===Codigo de respuesta:[".$result."]=========<BR>";

            if($resultWS != "00")
            {
                $html_de_salida = "";
            }

        }
        //NXN Fin     29sep2011 Implementacion Webservice ----Track ID>

		// - Definir los datos a enviar
		//NXN Inicio  29sep2011 Implementacion Webservice ----Track ID>
		// if( $tipo == "CBA" ) {
			// $html_to_send = "";
			// $_POST['Access'] = "Process";
			// $Parent1 = "Tarjeta CBA no operan esta transaccion.";
			// $particularFields = "&PAN=".urlencode($_POST['PAN'])."&Access=".urlencode($_POST['Access'])."&Parent1=".urlencode($Parent1);
			// $ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=tct&Access=".urlencode($_POST['Access'])."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // tarjetas de credito
			// $hubo_error = 1;
			// break;
		// }
		//$data_to_send = $tarjeta;
		//NXN Fin  29sep2011 Implementacion Webservice ----Track ID>

		//NXN Inicio  29sep2011 Implementacion Webservice ----Track ID>
		$data_to_send = "&PAN=".urlencode($tarjeta);
		// - Realizar la transaccion con SNA
		// $sna_call_ret_code = $tr->PostToSNA($snabackend, $snabackendport, $snaappid, $AccionSNA, $snafs, $data_to_send, $sna_transaccion_buffer); // tarjeta de credito Consulta Saldo
		//// Si hubo errores de comunicacion
		// switch ( $sna_call_ret_code ) {
		// case -1:
			 //// Reportar a Ovation
			 //// Salir
			// $transaccion_buffer = ReportSNAError( $backend, $usr, $priv, $qki, $tr, $Accion, $fs, $sna_transaccion_buffer );
			// $html_de_salida = "";
			// $hubo_error = 1;
			// break;
		// case -2:
			// $transaccion_buffer = ReportSNAError( $backend, $usr, $priv, $qki, $tr, $Accion, $fs, $sna_transaccion_buffer );
			// $html_de_salida = "";
			// $hubo_error = 1;
			// break;
		// default:
			// continue;
		// }
		// - Si no hubo error
		//  - Decodificar respuesta
		//$sna_ver_code = substr($sna_transaccion_buffer, 0, 2); // Los primeros dos bytes
		// echo "<em>sna_ver_code</em> "  . var_dump ( $sna_ver_code ) . "<br>\n";
		//$sna_ret_code = substr($sna_transaccion_buffer, 2, 3); // Los siguientes tres bytes
		// echo "<em>sna_ret_code</em> "  . var_dump ( $sna_ret_code ) . "<br>\n";
		// switch( $sna_ret_code ) {
			// case "001":
			// case "002":
			// case "003":
			// case "004":
			// case "005":
			// case "006":
			// case "007":
			// case "008":
			// case "009":
			// case "010":
			// case "011":
				// echo "<em>Toy en los chorros<br>\n";
				//  - Si hubo errores de aplicacion
				//      - Reportar a Ovation
				//      - Salir
				// $transaccion_buffer = ReportSNAError( $backend, $usr, $priv, $qki, $tr, $Accion, $fs, str_replace(chr($snafs), " ", substr( substr($sna_transaccion_buffer, 2), 0, -2) ) );
				// $html_de_salida = "";
				// $hubo_error = 1;
				// break;
			// case "999":
				//  - Si hubo errores de aplicacion
				//      - Reportar a Ovation
				//      - Salir
				// $transaccion_buffer = ReportSNAError( $backend, $usr, $priv, $qki, $tr, $Accion, $fs, str_replace(chr($snafs), " ", substr( substr($sna_transaccion_buffer, 2), 0, -2) ) );
				// $html_de_salida = "";
				// $hubo_error = 1;
				// break;
			// case "000":
				// - Si no hubo error de aplicacion
				//  - Decodificar respuesta
				//  - Preparar resultado
				//  - En esta transaccion nosotros no presentamos nada al usuario, lo hace Ovation. Lo que
				//    en realidad nos interesa es el particular fields que regresa decode.
				//$sna_ver_code2 = substr($sna_transaccion_buffer, 20, 45);
				//echo "<em>sna_ver_code2</em> "  . var_dump ( $sna_ver_code2 ) . "<br>\n";

				// switch( $sna_ver_code2 ) {
							// case "MASTER RECORD UPDATED. CRV STATUS ADDED.     ":
							// case "NO CHANGE DETECTED. MAINTENANCE IGNORED. ":
							// case "NO CHANGE DETECTED. MAINTENANCE IGNORED.     ":
								// $html_de_salida = $tr->decode(substr($sna_transaccion_buffer, 5), $AccionSNA, $snafs, $tarjeta, $particularFields, $rfc, $tipo, $nombre );
								// break;
							// default:
								// $transaccion_buffer = ReportSNAError( $backend, $usr, $priv, $qki, $tr, $Accion, $fs, str_replace(chr($snafs), " ", substr( substr($sna_transaccion_buffer, 2), 0, -2) ) );
								// $html_de_salida = "";
								// $hubo_error = 1;
								// break;
				// }
				//$html_de_salida = $tr->decode(substr($sna_transaccion_buffer, 5), $AccionSNA, $snafs, $tarjeta, $particularFields, $rfc, $tipo, $nombre );
		//}
		//NXN Fin     29sep2011 Implementacion Webservice ----Track ID>
		break;
	}

    if( ! $hubo_error ) {
    	//NXN Inicio  29sep2011 Implementacion Webservice ----Track ID>
        $particularFields .= "&Accion=".urlencode($Accion);
	$particularFields .= "&Accion=".urlencode($Accion).$data_to_send;
	//NXN Fin     29sep2011 Implementacion Webservice ----Track ID>
        // echo "<em>particularFields</em> " . var_dump ( $particularFields ) . "<br>\n";
        $ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=tct&Access=".urlencode($_POST['Access'])."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // tarjetas de credito
    }
    $transaccion_buffer = $transaccion_buffer . $html_de_salida;
    //NXN Inicio  29sep2011 Implementacion Webservice ----Track ID>
	if( $_POST['Access'] == "Process" and $resultWS == "00") {
		$transaccion_buffer=str_replace("</B>.</P>","</B>.</P><P>La recepción de la solicitud ha sido Exitosa.</P>",$transaccion_buffer );
	}
	else
	{
		if( $resultWS != "00") {
			$transaccion_buffer=str_replace("<P>Su petici&oacute;n ha sido registrada.</P>","<P>Ha ocurrido un error al procesar el bloqueo de la tarjeta. Codigo [".$resultWS."]</P>",$transaccion_buffer );
			$transaccion_buffer=str_replace("<P>Por favor anote el siguiente número de reporte: #<B>","<P>Por Favor comuniquese al 01-800-47-10-400 (servicio a Clientes) donde un Ejecutivo le Atendera. </P><P><FONT color=#ffffff size=0>.",$transaccion_buffer );
		}
	}
    //NXN Inicio  29sep2011 Implementacion Webservice ----Track ID>
    $t->set_var( "transaccion_buffer", $transaccion_buffer );
    $t->pparse( "output", "repextravioTC_tpl" );
    // phpinfo();
} else {
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "userlogin.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "user_login_tpl" => "userlogin.tpl"
        ) );

    $t->set_var( "redirect_url", "/transaccion/repextravioTC/" );
    $t->pparse( "output", "user_login_tpl" );
}

?>
