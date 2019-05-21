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
                         "eztransaccion/user/intl/", $Language, "movtosTC.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "movtosTC_tpl" => "movtosTC.tpl"
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
			$Operacion = "Movtos";	//FAF Cambio Nuevo Menu
            $Accion = $Operacion; // Para cachar el radio button.
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
    // $_POST['Access'] = "Process";
    // $_POST['Access'] = "EdoCta2Meses";
    // $_POST['Access'] = "EdoCta3Meses";
    // $_POST['Access'] = "MovtosFecha";
    // $_POST['Access'] = "UltimoEstadoCta";

    // echo "<em>Accion</em> "  . var_dump ( $Accion ) . "<br>\n";
    // echo "<em>RFC</em> "  . var_dump ( $RFC ) . "<br>\n";
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
//	print $tarjeta;

//	print "aqui entro ".$Accion ;
   switch($Accion) {
    case "MenuTar":     // Menu, nada que hacer.
        break;
    case "Movtos":      // Movimientos
        // - Definir la transaccion a realizar
        $AccionSNA = "MovtosFecha";
        // - Definir los datos a enviar
        $data_to_send = $tarjeta;
        // - Realizar la transaccion con SNA
		
		
        //ALUNA 11Feb2008 TDCOvation I

		$blnpaso = 0;
		$tctemp = $tarjeta;
        if ( $tipo == "ICA" ) 
		{ 
			$blnpaso = 1;	
			$tarjeta = trim($_POST['Apocope']); 
		}
		
		
		
		if ($blnpaso == 1)
		{

			$particularFields = "&PAN=" . $tctemp;		
		}
		else
		{

			$particularFields = "&PAN=" . $tarjeta;
		}	
		break;
		//ALUNA 11Feb2008 TDCOvation F
		
		
        $sna_call_ret_code = $tr->PostToSNA($snabackend, $snabackendport, $snaappid, $AccionSNA, $snafs, $data_to_send, $sna_transaccion_buffer); // tarjeta de credito Consulta Saldo
        // - Si hubo errores de comunicacion
        switch ( $sna_call_ret_code ) {
        case -1:
            //  - Reportar a Ovation
            //  - Salir
            $transaccion_buffer = ReportSNAError( $backend, $usr, $priv, $qki, $tr, $Accion, $fs, $sna_transaccion_buffer );
            $html_de_salida = "";
            $hubo_error = 1;
            break;
        case -2:
            $transaccion_buffer = ReportSNAError( $backend, $usr, $priv, $qki, $tr, $Accion, $fs, $sna_transaccion_buffer );
            $html_de_salida = "";
            $hubo_error = 1;
            break;
        default:
            continue;
        }
        // - Si no hubo error
        $sna_ver_code = substr($sna_transaccion_buffer, 0, 2); // Los primeros dos bytes
        // echo "<em>sna_ver_code</em> "  . var_dump ( $sna_ver_code ) . "<br>\n";
        $sna_ret_code = substr($sna_transaccion_buffer, 2, 3); // Los siguientes tres bytes
        // echo "<em>sna_ret_code</em> "  . var_dump ( $sna_ret_code ) . "<br>\n";
        switch( $sna_ret_code ) {
            case "001":
            case "002":
            case "003":
            case "004":
            case "005":
            case "006":
            case "007":
            case "008":
            case "009":
            case "010":
            case "011":
                // echo "<em>Toy en los chorros<br>\n";
                //  - Si hubo errores de aplicacion
                //      - Reportar a Ovation
                //      - Salir
                $transaccion_buffer = ReportSNAError( $backend, $usr, $priv, $qki, $tr, $Accion, $fs, str_replace(chr($snafs), " ", substr( substr($sna_transaccion_buffer, 2), 0, -2) ) );
                $html_de_salida = "";
                $hubo_error = 1;
                break;
            case "999":
                //  - Si hubo errores de aplicacion
                //      - Reportar a Ovation
                //      - Salir
                $transaccion_buffer = ReportSNAError( $backend, $usr, $priv, $qki, $tr, $Accion, $fs, str_replace(chr($snafs), " ", substr( substr($sna_transaccion_buffer, 2), 0, -2) ) );
                $html_de_salida = "";
                $hubo_error = 1;
                break;
            case "000":
                // echo "<em>Toy en los ceros.</em><br>\n";
                $html_de_salida = $tr->decode(substr($sna_transaccion_buffer, 5), $AccionSNA, $snafs, $tarjeta, $particularFields, $rfc, $tipo, $nombre );
                // echo "<br>\n<br>\n<br>html_de_salida " . var_dump ( $html_de_salida ) . "<br>\n";
                //  - Si la respuesta indica transcode
                if ( substr( $html_de_salida, 0, 1 ) == "E" ) { // hay un transcode :)
                    //      - Reportar a Ovation
                    $_POST['Access'] = "Process";
                    $particularFields .= "&Accion=".urlencode($Accion)."&Access=".urlencode($_POST['Access']);
                    // echo "<em>particularFields</em> " . var_dump ( $particularFields ) . "<br>\n";
                    $ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=tct&Access=".urlencode($_POST['Access'])."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // tarjetas de credito
                    //      - Definir los nuevos datos a enviar
                    $tarjeta = substr($html_de_salida, 1, 16);
                    // echo "<br>\n<br>\n<br>\ntarjeta " . var_dump ( $tarjeta ) . "<br>\n";
                    $data_to_send = $tarjeta;
                    //      - Realizar la transaccion con SNA
                    $sna_call_ret_code = $tr->PostToSNA($snabackend, $snabackendport, $snaappid, $AccionSNA, $snafs, $data_to_send, $sna_transaccion_buffer); // tarjeta de credito Consulta Saldo
                    //      - Si hubo errores  de comunicacion
                    switch ( $sna_call_ret_code ) {
                    case -1:
                        //  - Reportar a Ovation
                        //  - Salir
                        $transaccion_buffer = ReportSNAError( $backend, $usr, $priv, $qki, $tr, $Accion, $fs, $sna_transaccion_buffer );
                        $html_de_salida = "";
                        $hubo_error = 1;
                        break;
                    case -2:
                        $transaccion_buffer = ReportSNAError( $backend, $usr, $priv, $qki, $tr, $Accion, $fs, $sna_transaccion_buffer );
                        $html_de_salida = "";
                        $hubo_error = 1;
                        break;
                    default:
                        continue;
                    }
                    //      - Si no hubo errores  de comunicacion
                    //          - Decodificar respuesta
                    $sna_ver_code = substr($sna_transaccion_buffer, 0, 2); // Los primeros dos bytes
                    $sna_ret_code = substr($sna_transaccion_buffer, 2, 3); // Los siguientes tres bytes
                    $html_de_salida = $tr->decode(substr($sna_transaccion_buffer, 5), $AccionSNA, $snafs, $tarjeta, $particularFields, $rfc, $tipo, $nombre );
                    //      - Si hubo errores de aplicacion
                    switch( $sna_ret_code ) {
                        case "001":
                        case "002":
                        case "003":
                        case "004":
                        case "005":
                        case "006":
                        case "007":
                        case "008":
                        case "009":
                        case "010":
                        case "011":
                            // echo "<em>Toy en los chorros<br>\n";
                            //  - Si hubo errores de aplicacion
                            //      - Reportar a Ovation
                            //      - Salir
                            $transaccion_buffer = ReportSNAError( $backend, $usr, $priv, $qki, $tr, $Accion, $fs, str_replace(chr($snafs), " ", substr( substr($sna_transaccion_buffer, 2), 0, -2) ) );
                            $html_de_salida = "";
                            $hubo_error = 1;
                            break;
                        case "999":
                            //      - Reportar a Ovation
                            //      - Salir
                            $transaccion_buffer = ReportSNAError( $backend, $usr, $priv, $qki, $tr, $Accion, $fs, str_replace(chr($snafs), " ", substr( substr($sna_transaccion_buffer, 2), 0, -2) ) );
                            $html_de_salida = "";
                            $hubo_error = 1;
                            break;
                    }
                }
            break;
        }
        break;
    }

    if( ! $hubo_error ) {
        $particularFields .= "&Accion=".urlencode($Accion);
        // echo "<em>particularFields</em> " . var_dump ( $particularFields ) . "<br>\n";
        $ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=tct&Access=".urlencode($_POST['Access'])."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // tarjetas de credito
    }
    $transaccion_buffer = $transaccion_buffer . $html_de_salida;
    $t->set_var( "transaccion_buffer", $transaccion_buffer );
    $t->pparse( "output", "movtosTC_tpl" );

}
else
{
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
