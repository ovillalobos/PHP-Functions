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

if ( $user )
{
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "saldosTC.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "saldosTC_tpl" => "saldosTC.tpl"
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
	if(!empty($_POST['Access']))
        $Access = $_POST['Access'];
        
   if(!empty($_POST['Accion']))
        $Accion = $_POST['Accion'];
        
   if(!empty($_POST['DiasPzo']))
        $DiasPzo = $_POST['DiasPzo'];
        
   if(!empty($_POST['FrAccount']))
        $FrAccount = $_POST['FrAccount'];
            
   if(!empty($_POST['ToAccount']))
        $ToAccount = $_POST['ToAccount'];
        
	if(!empty($_POST['Amount']))
        $Amount = $_POST['Amount'];
        
   if(!empty($_POST['Button']))
        $Button = $_POST['Button'];
        
   if(!empty($_POST['PAN']))
        $PAN = $_POST['PAN'];
        
   if(!empty($_POST['RFC']))
        $RFC = $_POST['RFC'];
        
   if(!empty($_POST['Apocope']))
        $Apocope = $_POST['Apocope'];
        
   if(!empty($_POST['Empresa']))
        $Empresa = $_POST['Empresa'];
//HB
    if(empty($Access)) {
        $Access = "FrAc";
    }
    switch($Access) {
    case "FrAc":
        $Accion  = "MenuTar";
        $DiasPzo = "";   // Cadena con lista de tarjetas p actualizar en ovation char de 150
        $particularFields = "&Accion=".urlencode($Accion);
        $tr->blog($qki,"TarjetasCto", $FrAccount, $ToAccount, $DiasPzo, $Amount, $Accion);
        break;
    case "Confirm":
    case "Process":
        if( $Button == "Alta Tarjeta" ) {
            $Accion = "NewCard";
        }
        if( $Button == "Aceptar" ) {
            $Accion = $Operacion; // Para cachar el radio button.
         }
        break;
    }
    $transaccion_buffer = "";
    $sna_transaccion_buffer = "";
    // $Accion = "Movtos";
    // $Accion = "EdoCta";
    // $Month = "02";
    // $Accion = "NewCard";
    // $Accion = "NewCard";
    // $Accion = "Saldos";
    // $Access = "Process";
    // $Access = "EdoCta2Meses";
    // $Access = "EdoCta3Meses";
    // $Access = "MovtosFecha";
    // $Access = "UltimoEstadoCta";

    // echo "<em>Accion</em> "  . var_dump ( $Accion ) . "<br>\n";
    // echo "<em>RFC</em> "  . var_dump ( $RFC ) . "<br>\n";
    $rfc = $RFC;
    $pan = $PAN;
    $tarjeta = "";
    $tipo = "";
    $nombre = "";
    $hubo_error = 0;
    list( $tarjeta, $tipo, $nombre ) = explode( "-", $pan);

	switch($Accion) {
	case "MenuTar":     // Menu, nada que hacer.
		break;
	case "Saldos":     // Saldos
		$AccionSNA = "ConsultaSaldo";
		if ( $tipo != "ICA" ) {
			$tarjeta = trim($Apocope);
		}
		$data_to_send = $tarjeta;
		$sna_ret_code = $tr->PostToSNA($snabackend, $snabackendport, $snaappid, $AccionSNA, $snafs, $data_to_send, $sna_transaccion_buffer); // tarjeta de credito Consulta Saldo
		if ( $sna_ret_code == -1 ) {
			$html_de_salida = "Error de comunicaciones con host.";
			break;
		}
		$sna_ver_code = substr($sna_transaccion_buffer, 0, 2); // Los primeros dos bytes
		$sna_ret_code = substr($sna_transaccion_buffer, 2, 3); // Los siguientes tres bytes
		// echo "<em>sna_ret_code</em> "  . var_dump ( $sna_ret_code ) . "<br>\n";
		if ( $sna_ret_code == "999" ) {
			list ( $no_pelar, $error ) = explode ( chr($snafs), $sna_transaccion_buffer);
			$Access = "Process";
			$Day = "";
			$EspecCh = "";
			$PAN = "9999";
			$Nomben2 = "";
			$Empresa = "";
			$UbNo = "";
			$Parent1 = ucfirst( strtolower( $error )) . ".";
			$Parent2 = "";
			$Parent3 = "";
			$particularFields = "&Accion=".urlencode($Accion)."&PAN=".urlencode($PAN)."&Nomben2=".urlencode($Nomben2)."&EspecCh=".urlencode($EspecCh)."&UbNo=".urlencode($UbNo)."&Empresa=".urlencode($Empresa)."&Day=".urlencode($Day)."&Parent1=".urlencode($Parent1)."&Parent2=".urlencode($Parent2)."&Parent3=".urlencode($Parent3);
			// echo "<em>particularFields</em> " . var_dump ( $particularFields ) . "<br>\n";
			$ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=tct&Access=".urlencode($Access)."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // tarjetas de credito
			$hubo_error = 1;
			$html_de_salida = "";
			break;
		}
		//echo "sna_transaccion_buffer " . var_dump( $sna_transaccion_buffer ) . "<br>\n";;
		if ( $sna_ret_code == "000" ) {
			$html_de_salida = $tr->decode(substr($sna_transaccion_buffer, 5), $AccionSNA, $snafs, $tarjeta, $particularFields, $rfc, $tipo, $nombre );
			// echo "<br>\n<br>\n<br>html_de_salida " . var_dump ( $html_de_salida ) . "<br>\n";
			if ( substr( $html_de_salida, 0, 1 ) == "E" ) { // hay un transcode :)
				$Access = "Process";
				$particularFields .= "&Accion=".urlencode($Accion)."&Access=".urlencode($Access);
				// echo "<em>particularFields</em> " . var_dump ( $particularFields ) . "<br>\n";
				$ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=tct&Access=".urlencode($Access)."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // tarjetas de credito
				$tarjeta = substr($html_de_salida, 1, 16);
				// echo "<br>\n<br>\n<br>\ntarjeta " . var_dump ( $tarjeta ) . "<br>\n";
				$data_to_send = $tarjeta;
				$sna_ret_code = $tr->PostToSNA($snabackend, $snabackendport, $snaappid, $AccionSNA, $snafs, $data_to_send, $sna_transaccion_buffer); // tarjeta de credito Consulta Saldo
				if ( $sna_ret_code == -1 ) {
					$html_de_salida = "Error de comunicaciones con host.";
					break;
				}
				$sna_ver_code = substr($sna_transaccion_buffer, 0, 2); // Los primeros dos bytes
				$html_de_salida = $tr->decode(substr($sna_transaccion_buffer, 5), $AccionSNA, $snafs, $tarjeta, $particularFields, $rfc, $tipo, $nombre );
			}
		} else {
			// Aguas hay que separar errores de transcode.
			//$html_de_salida = "Error " . $sna_ret_code;
			$html_de_salida = "";
			$data_to_send = "420199" . $tarjeta; // este tarjeta es el transcode que me devolvio SNA
			$sna_ret_code = $tr->PostToSNA($snabackend, $snabackendport, $snaappid, $AccionSNA, $snafs, $data_to_send, $sna_transaccion_buffer); // tarjeta de credito Consulta Saldo
			if ( $sna_ret_code == -1 ) {
				$html_de_salida = "Error de comunicaciones con host.";
				break;
			}
			// Checar si jala.
			$Access = "Process";
			//$Accion = "Editar";
			$Parent1 = "SNA COMUNICATION ERROR";
			$particularFields = "&Accion=".urlencode($Accion)."&Parent1=".urlencode($Parent1)."&Transcode=".urlencode($data_to_send);
			// echo "<em>particularFields</em> " . var_dump ( $particularFields ) . "<br>\n";
			$ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=tct&Access=".urlencode($Access)."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // tarjeta
			$hubo_error = 1;
		}
		break;
	case "NewCard":     // Afiliacion
		$AccionSNA = "Afiliacion";
		$data_to_send = $tarjeta; // Solo mando el numero de tarjeta, yo, de regreso, tengo que comparar con el rfc.
		$sna_ret_code = $tr->PostToSNA($snabackend, $snabackendport, $snaappid, $AccionSNA, $snafs, $data_to_send, $sna_transaccion_buffer); // tarjeta de credito afiliacion
		if ( $sna_ret_code == -1 ) {
			$html_de_salida = "Error de comunicaciones con host.";
			break;
		}
		$sna_ver_code = substr($sna_transaccion_buffer, 0, 2); // Los primeros dos bytes
		//echo "<em>sna_ver_code</em> "  . var_dump ( $sna_ver_code ) . "<br>\n";
		$sna_ret_code = substr($sna_transaccion_buffer, 2, 3); // Los siguientes tres bytes
		// echo "<em>sna_ret_code</em> "  . var_dump ( $sna_ret_code ) . "<br>\n";
		// echo "<em>html_de_salida</em> "  . var_dump (substr( $html_de_salida , 0, 1 )) . "<br>\n";
		if ( $sna_ret_code == "999" ) {
			list ( $no_pelar, $error ) = explode ( chr($snafs), $sna_transaccion_buffer);
			$Accion = $OldAction;
			$Access = "Process";
			$Day = "";
			$EspecCh = "";
			$PAN = "9999";
			$Nomben2 = "";
			$Empresa = "";
			$UbNo = "";
			$Parent1 = ucfirst( strtolower( $error )) . ".";
			$Parent2 = "";
			$Parent3 = "";
			$particularFields = "&Accion=".urlencode($Accion)."&PAN=".urlencode($PAN)."&Nomben2=".urlencode($Nomben2)."&EspecCh=".urlencode($EspecCh)."&UbNo=".urlencode($UbNo)."&Empresa=".urlencode($Empresa)."&Day=".urlencode($Day)."&Parent1=".urlencode($Parent1)."&Parent2=".urlencode($Parent2)."&Parent3=".urlencode($Parent3);
			// Checar si jala con el SNA arriba tambien
			//$ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=tct&Access=".urlencode($Access)."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // tarjetas de credito
			$hubo_error = 1;
			$html_de_salida = "";
			break;
		}
		if ( $sna_ret_code == "000" ) {
			// echo "<em>sna_transaccion_buffer</em> "  . var_dump ( $sna_transaccion_buffer ) . "<br>\n";
			$html_de_salida = $tr->decode(substr($sna_transaccion_buffer, 5), $AccionSNA, $snafs, $tarjeta, $particularFields, $rfc, $tipo, $nombre );
			if ( substr( $html_de_salida, 0, 1 ) == "E" ) {
				$html_de_salida = "";
			}
		// echo "<em>html_de_salida</em> "  . var_dump ( $html_de_salida ) . "<br>\n";
		} else {
			//$html_de_salida = "Error " . $sna_ret_code;
			$html_de_salida = "";
		}
		break;
	//OJO particularFields debe ser igual tambien para Saldos
	case "Extravio":    // Extravio
	case "Robo":        // Robo
		if( $tipo == "CBA" ) {
			$html_to_send = "";
			$Access = "Process";
			$Parent1 = "Tarjeta CBA no operan esta transaccion.";
			$particularFields = "&PAN=".urlencode($PAN)."&Access=".urlencode($Access)."&Parent1=".urlencode($Parent1);
			break;
		}
		$AccionSNA = "RepRoboExtravio";
		$html_de_salida = "";
		$data_to_send = $tarjeta;
		$sna_ret_code = $tr->PostToSNA($snabackend, $snabackendport, $snaappid, $AccionSNA, $snafs, $data_to_send, $sna_transaccion_buffer); // tarjeta de credito Consulta Saldo
		if ( $sna_ret_code == -1 ) {
			$html_de_salida = "Error de comunicaciones con host.";
			break;
		}
		$sna_ver_code = substr($sna_transaccion_buffer, 0, 2); // Los primeros dos bytes
		// echo "<em>sna_ver_code</em> "  . var_dump ( $sna_ver_code ) . "<br>\n";
		$sna_ret_code = substr($sna_transaccion_buffer, 2, 3); // Los siguientes tres bytes
		// echo "<em>sna_ret_code</em> "  . var_dump ( $sna_ret_code ) . "<br>\n";
		if ( $sna_ret_code == "999" ) {
			list ( $no_pelar, $error ) = explode ( chr($snafs), $sna_transaccion_buffer);
			$Access = "Process";
			$Day = "";
			$EspecCh = "";
			$PAN = "9999";
			$Nomben2 = "";
			$Empresa = "";
			$UbNo = "";
			$Parent1 = ucfirst( strtolower( $error )) . ".";
			$Parent2 = "";
			$Parent3 = "";
			$particularFields = "&Accion=".urlencode($Accion)."&PAN=".urlencode($PAN)."&Nomben2=".urlencode($Nomben2)."&EspecCh=".urlencode($EspecCh)."&UbNo=".urlencode($UbNo)."&Empresa=".urlencode($Empresa)."&Day=".urlencode($Day)."&Parent1=".urlencode($Parent1)."&Parent2=".urlencode($Parent2)."&Parent3=".urlencode($Parent3);
			$ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=tct&Access=".urlencode($Access)."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // tarjetas de credito
			$html_de_salida = "";
			$hubo_error = 1;
			break;
		}
		if ( $sna_ret_code == "000" ) {
			$html_de_salida = $tr->decode(substr($sna_transaccion_buffer, 5), $AccionSNA, $snafs, $tarjeta, $particularFields, $rfc, $tipo, $nombre );
			if ( substr( $html_de_salida, 0, 1 ) == "E" ) {
				$Access = "Process";
				$particularFields .= "&Accion=".urlencode($Accion)."&Access=".urlencode($Access);
				// echo "<em>particularFields</em> " . var_dump ( $particularFields ) . "<br>\n";
				$ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=tct&Access=".urlencode($Access)."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // tarjetas de credito
				$tarjeta = substr($html_de_salida, 1, 16);
				$data_to_send = $tarjeta;
				$sna_ret_code = $tr->PostToSNA($snabackend, $snabackendport, $snaappid, $AccionSNA, $snafs, $data_to_send, $sna_transaccion_buffer); // tarjeta de credito Consulta Saldo
				$sna_ver_code = substr($sna_transaccion_buffer, 0, 2); // Los primeros dos bytes
				$html_de_salida = $tr->decode(substr($sna_transaccion_buffer, 5), $AccionSNA, $snafs, $tarjeta, $particularFields, $rfc, $tipo, $nombre );
			}
		} else {
			$html_de_salida = "";
			$data_to_send = "420199" . $tarjeta; // este tarjeta es el transcode que me devolvio SNA
			$sna_ret_code = $tr->PostToSNA($snabackend, $snabackendport, $snaappid, $AccionSNA, $snafs, $data_to_send, $sna_transaccion_buffer); // tarjeta de credito Consulta Saldo
			if ( $sna_ret_code == -1 ) {
				$html_de_salida = "Error de comunicaciones con host.";
				break;
			}
			// Checar si jala.
			$Access = "Process";
			$Accion = "Editar";
			$particularFields = "&Accion=".urlencode($Accion)."&Transcode=".urlencode($data_to_send);
			$ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=tct&Access=".urlencode($Access)."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // tarjeta
		}
		// echo "<em>html_de_salida</em> "  . var_dump ( $html_de_salida ) . "<br>\n";
		break;
	case "Movtos":      // Movimientos
		$AccionSNA = "MovtosFecha";
		$data_to_send = $tarjeta;
		$sna_ret_code = $tr->PostToSNA($snabackend, $snabackendport, $snaappid, $AccionSNA, $snafs, $data_to_send, $sna_transaccion_buffer); // tarjeta de credito Consulta Saldo
		if ( $sna_ret_code == -1 ) {
			$html_de_salida = "Error de comunicaciones con host.";
			break;
		}
		$sna_ver_code = substr($sna_transaccion_buffer, 0, 2); // Los primeros dos bytes
		// echo "<em>sna_ver_code</em> "  . var_dump ( $sna_ver_code ) . "<br>\n";
		$sna_ret_code = substr($sna_transaccion_buffer, 2, 3); // Los siguientes tres bytes
		// echo "<em>sna_ret_code</em> "  . var_dump ( $sna_ret_code ) . "<br>\n";
		if ( $sna_ret_code == "999" ) {
			list ( $no_pelar, $error ) = explode ( chr($snafs), $sna_transaccion_buffer);
			$Access = "Process";
			$Day = "";
			$EspecCh = "";
			$PAN = "9999";
			$Nomben2 = "";
			$Empresa = "";
			$UbNo = "";
			$Parent1 = ucfirst( strtolower( $error )) . ".";
			$Parent2 = "";
			$Parent3 = "";
			$particularFields = "&Accion=".urlencode($Accion)."&PAN=".urlencode($PAN)."&Nomben2=".urlencode($Nomben2)."&EspecCh=".urlencode($EspecCh)."&UbNo=".urlencode($UbNo)."&Empresa=".urlencode($Empresa)."&Day=".urlencode($Day)."&Parent1=".urlencode($Parent1)."&Parent2=".urlencode($Parent2)."&Parent3=".urlencode($Parent3);
			$ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=tct&Access=".urlencode($Access)."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // tarjetas de credito
			$html_de_salida = "";
			break;
		}
		if ( $sna_ret_code == "000" ) {
			$html_de_salida = $tr->decode(substr($sna_transaccion_buffer, 5), $AccionSNA, $snafs, $tarjeta, $particularFields, $rfc, $tipo, $nombre );
			if ( substr( $html_de_salida, 0, 1 ) == "E" ) {
				$Access = "Process";
				$particularFields .= "&Accion=".urlencode($Accion)."&Access=".urlencode($Access);
				// echo "<em>particularFields</em> " . var_dump ( $particularFields ) . "<br>\n";
				$ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=tct&Access=".urlencode($Access)."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // tarjetas de credito
				$tarjeta = substr($html_de_salida, 1, 16);
				$data_to_send = $tarjeta;
				$sna_ret_code = $tr->PostToSNA($snabackend, $snabackendport, $snaappid, $AccionSNA, $snafs, $data_to_send, $sna_transaccion_buffer); // tarjeta de credito Consulta Saldo
				if ( $sna_ret_code == -1 ) {
					$html_de_salida = "Error de comunicaciones con host.";
					break;
				}
				$sna_ver_code = substr($sna_transaccion_buffer, 0, 2); // Los primeros dos bytes
				$html_de_salida = $tr->decode(substr($sna_transaccion_buffer, 5), $AccionSNA, $snafs, $tarjeta, $particularFields, $rfc, $tipo, $nombre );
			}
		} else {
			// Aguas hay que separar errores de transcode.
			$html_de_salida = "";
			// Checar si jala.
			$Access = "Process";
			//$Accion = "Editar";
			$particularFields = "&Accion=".urlencode($Accion)."&Transcode=".urlencode($data_to_send);
			// echo "<em>particularFields</em> " . var_dump ( $particularFields ) . "<br>\n";
			$ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=tct&Access=".urlencode($Access)."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // tarjeta
		}
		break;
	case "EdoCta":      // Estado de cuenta
		if ( $tipo == "PCA" ) {
			$tarjeta = trim($Apocope);
		}
		$data_to_send = $tarjeta;
		switch($Month) {
		case "01":
			$AccionSNA = "UltimoEstadoCta";
			break;
		case "02":
			$AccionSNA = "EdoCta2Meses";
			break;
		case "03":
			$AccionSNA = "EdoCta3Meses";
			break;
		}
		$sna_ret_code = $tr->PostToSNA($snabackend, $snabackendport, $snaappid, $AccionSNA, $snafs, $data_to_send, $sna_transaccion_buffer); // tarjeta de credito Consulta Saldo
		if ( $sna_ret_code == -1 ) {
			$html_de_salida = "Error de comunicaciones con host.";
			break;
		}
		$sna_ver_code = substr($sna_transaccion_buffer, 0, 2); // Los primeros dos bytes
		// echo "<em>sna_ver_code</em> "  . var_dump ( $sna_ver_code ) . "<br>\n";
		$sna_ret_code = substr($sna_transaccion_buffer, 2, 3); // Los siguientes tres bytes
		// echo "<em>sna_ret_code</em> "  . var_dump ( $sna_ret_code ) . "<br>\n";
		if ( $sna_ret_code == "999" ) {
			list ( $no_pelar, $error ) = explode ( chr($snafs), $sna_transaccion_buffer);
			$Access = "Process";
			$Day = "";
			$EspecCh = "";
			$PAN = "9999";
			$Nomben2 = "";
			$Empresa = "";
			$UbNo = "";
			$Parent1 = ucfirst( strtolower( $error )) . ".";
			$Parent2 = "";
			$Parent3 = "";
			$particularFields = "&Accion=".urlencode($Accion)."&PAN=".urlencode($PAN)."&Nomben2=".urlencode($Nomben2)."&EspecCh=".urlencode($EspecCh)."&UbNo=".urlencode($UbNo)."&Empresa=".urlencode($Empresa)."&Day=".urlencode($Day)."&Parent1=".urlencode($Parent1)."&Parent2=".urlencode($Parent2)."&Parent3=".urlencode($Parent3);
			// echo "<em>particularFields</em> " . var_dump ( $particularFields ) . "<br>\n";
			$ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=tct&Access=".urlencode($Access)."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // tarjetas de credito
			$html_de_salida = "";
			$hubo_error = 1;
			break;
		}
		if ( $sna_ret_code == "000" ) {
			$html_de_salida = $tr->decode(substr($sna_transaccion_buffer, 5), $AccionSNA, $snafs, $tarjeta, $particularFields, $rfc, $tipo, $nombre );
			if ( substr( $html_de_salida, 0, 1 ) == "E" ) {
				$Access = "Process";
				$particularFields .= "&Accion=".urlencode($Accion)."&Access=".urlencode($Access);
				// echo "<em>particularFields</em> " . var_dump ( $particularFields ) . "<br>\n";
				$ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=tct&Access=".urlencode($Access)."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // tarjetas de credito
				$Access = "Process";
				$particularFields .= "&Accion=".urlencode($Accion)."&Access=".urlencode($Access);
				// echo "<em>particularFields</em> " . var_dump ( $particularFields ) . "<br>\n";
				$tarjeta = substr($html_de_salida, 1, 16);
				$data_to_send = $tarjeta;
				$sna_ret_code = $tr->PostToSNA($snabackend, $snabackendport, $snaappid, $AccionSNA, $snafs, $data_to_send, $sna_transaccion_buffer); // tarjeta de credito Consulta Saldo
				if ( $sna_ret_code == -1 ) {
					$html_de_salida = "Error de comunicaciones con host.";
					break;
				}
				$sna_ver_code = substr($sna_transaccion_buffer, 0, 2); // Los primeros dos bytes
				$html_de_salida = $tr->decode(substr($sna_transaccion_buffer, 5), $AccionSNA, $snafs, $tarjeta, $particularFields, $rfc, $tipo, $nombre );
			}
		} else {
			// Aguas hay que separar errores de transcode.
			//$html_de_salida = "Error " . $sna_ret_code;
			$html_de_salida = "";
			$data_to_send = "420199" . $tarjeta; // este tarjeta es el transcode que me devolvio SNA
			$sna_ret_code = $tr->PostToSNA($snabackend, $snabackendport, $snaappid, $AccionSNA, $snafs, $data_to_send, $sna_transaccion_buffer); // tarjeta de credito Consulta Saldo
			if ( $sna_ret_code == -1 ) {
				$html_de_salida = "Error de comunicaciones con host.";
				break;
			}
			// Checar si jala.
			$Access = "Process";
			$Accion = "Editar";
			$particularFields = "&Accion=".urlencode($Accion)."&Transcode=".urlencode($data_to_send);
			$ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=tct&Access=".urlencode($Access)."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // tarjeta
		}
		break;
	}

	if(! $hubo_error ) {
		$particularFields .= "&Accion=".urlencode($Accion);
		// echo "<em>particularFields</em> " . var_dump ( $particularFields ) . "<br>\n";
		$ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=tct&Access=".urlencode($Access)."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // tarjetas de credito
	}
    $transaccion_buffer = $transaccion_buffer . $html_de_salida;
    $t->set_var( "transaccion_buffer", $transaccion_buffer );
    $t->pparse( "output", "saldosTC_tpl" );
    // phpinfo();
} else {
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "userlogin.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "user_login_tpl" => "userlogin.tpl"
        ) );

    $t->set_var( "redirect_url", "/transaccion/saldosTC/" );
    $t->pparse( "output", "user_login_tpl" );
}

?>
