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
include_once( "classes/INIFile.php" );
include_once( "classes/eztemplate.php" );
include_once( "classes/ezhttptool.php" );

include_once( "eztransaccion/classes/eztransaccion.php" );
include_once( "ezsession/classes/ezsession.php" );
include_once( "classes/ezdatetime.php" );
include_once( "ezuserbb/classes/ezuser.php" );
include_once( "pear/SOAP/Client.php" );
include_once( "classes/sendmail.php" );


$session =& eZSession::globalSession();

if( !$session->fetch() )
    $session->store();

$ini =& $GLOBALS["GlobalSiteIni"];

$Language = $ini->read_var( "eZTransaccionMain", "Language" );
// $backend = $ini->read_var( "eZTransaccionMain", "Backend" );
$ServerNumber = $ini->read_var( "site", "ServerNumber" );
$DomainPostfix = $ini->read_var( "site", "DomainPostfix" );
$procesarWebservicesServer   =  $ini->read_var( "site", "procesarWebservicesServer" );
$procesarWebservicesUser     =  $ini->read_var( "site", "procesarWebservicesUser" );
$procesarWebservicesPassword =  $ini->read_var( "site", "procesarWebservicesPassword" );
$procesarWebservicesBaseURL  =  $ini->read_var( "site", "procesarWebservicesBaseURL" );
$BridgeWebServiceURL         =  $ini->read_var( "site", "BridgeWebServiceURL" );
$ErrEMail1 = $ini->read_var( "site", "ErrEMail1" );
$ErrEMail2 = $ini->read_var( "site", "ErrEMail2" );

$backend = "gw" . $ServerNumber. $DomainPostfix;



$user =& eZUserBB::currentUser();

// DebugBreak();

//Funcion para quitar caracteres no permitidos y pueda coicidir lo mayor posible con los datos retornados de procesar
function quitarespeciales(&$cadena){
$cadena1 = "";
   //compruebo que los caracteres sean los permitidos //JMRG SE ELIMINA LA Ñ DE LOS PERMITIDOS
   $permitidos = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-_";
   for ($i=0; $i<strlen($cadena); $i++){
      if (strpos($permitidos, substr($cadena,$i,1))===false){
          $cadena{$i} = "";
      }
      else{
      $cadena1 .= substr($cadena,$i,1);
      }
   }
   $cadena = $cadena1;
   return;
}


if ( $user )
{
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "activacion.php" );

    $t->setAllStrings();

	// AGG I 25Nov2005 Para poner fecha y hora en los comprobantes
	$dateTime  = new eZDateTime( );
	$timeStamp = $dateTime->timeStamp();
	$time =& date('H:i:s', $timeStamp );
	$date =& date('jMY', $timeStamp );
	// AGG F 25Nov2005 Para poner fecha y hora en los comprobantes


    $t->set_file( array(
        "activacion_tpl" => "activacion.tpl"
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
//HB
	if(!empty($_POST['Access']))
        $Access = $_POST['Access'];
        
   if(!empty($_POST['ImpTipo']))
        $ImpTipo = $_POST['ImpTipo'];

	if(!empty($_POST['Accion']))
        $Accion = $_POST['Accion'];
        
   if(!empty($_POST['Parent1']))
        $Parent1 = $_POST['Parent1'];
        
   if(!empty($_POST['RFC']))
        $RFC = $_POST['RFC'];
        
   if(!empty($_POST['RenInt']))
        $RenInt = $_POST['RenInt'];
        
   if(!empty($_POST['desc1']))
        $desc1 = $_POST['desc1'];
        
   if(!empty($_POST['Desc']))
        $Desc = $_POST['Desc'];
        
   if(!empty($_POST['Apellido']))
        $Apellido = $_POST['Apellido'];
//HB

    if( !isset /*(HB AGL - Ajustes PHP5)*/( $ImpTipo	) ) { 	$ImpTipo	= "";		}

    if(empty($Access)) {
        $Access = "FrAc";
    }
    switch($Access) {
    case "FrAc":
        $particularFields = "";
        $tr->blog($qki,"Activacion",$Access , $Accion);
        break;
    case "Confirm":
		$particularFields = "&Parent1=".($Parent1)."&RFC=".($RFC)."&RenInt=".($RenInt)."&desc1=".($desc1)."&Desc=".$Desc."&locali=".($Apellido)."&ImpTipo=".$ImpTipo;
//    print_r($particularFields);
		$tr->blog($qki,"Activacion", $Parent1, $RFC, $RenInt, $desc1, $Desc,$Accion,$ImpTipo);
        break;
    case "Process":
		$datos = new SOAP_Client( $BridgeWebServiceURL );
		$datos->setOpt( 'timeout', 240 );
		$method = 'consultar';

    $url   = $procesarWebservicesServer . $procesarWebservicesBaseURL . 'ServiceConsultaDatosEV';
    $folio = $desc1;

		if ( $GLOBALS["DEBUGA"] == true ) {
			eZLog::writeNotice( "En activacion de eztransaccion/user consulta datos (endpoint) ->" . print_r( $BridgeWebServiceURL, true ) . "|" );
			eZLog::writeNotice( "En activacion de eztransaccion/user consulta datos (method) ->" . print_r( $method, true ) . "|" );
		}
		$params = array( 'user' => $procesarWebservicesUser, 'passwd' => $procesarWebservicesPassword, 'url' => $url, 'folio' => $folio);

		//AGG 27JUL2006 FUNCIONALIDAD PARA IMPRIMIR EN BITACORA LA HORA INICIAL Y FINAL DE LAS LLAMADAS A LOS WEB SERVICES
		$timeini		    = date( "H:i:s" );
		list( $usec, $sec )	= explode( " ", microtime() );
		$usini				= substr( $usec, 2);
		//AGG 27JUL2006 FUNCIONALIDAD PARA IMPRIMIR EN BITACORA LA HORA INICIAL Y FINAL DE LAS LLAMADAS A LOS WEB SERVICES

		$ans = $datos->call( $method, $params );

    if ( is_object($ans) )
    {
      //eZLog::writeNotice( "Error: PROCESAR NO Regreso Respuesta en CONSULTA|" . $folio . "|" . date( "H:i:s" ) . "|" . $ans->message);	MAOS Oct2013 Quitar Notice 
    }

		//AGG 27JUL2006 FUNCIONALIDAD PARA IMPRIMIR EN BITACORA LA HORA INICIAL Y FINAL DE LAS LLAMADAS A LOS WEB SERVICES
		$timefin		    = date( "H:i:s" );
		list( $usec, $sec )	= explode( " ", microtime() );
		$usfin				= substr( $usec, 2);
		//AGG 27JUL2006 FUNCIONALIDAD PARA IMPRIMIR EN BITACORA LA HORA INICIAL Y FINAL DE LAS LLAMADAS A LOS WEB SERVICES
//    print_r($ans->message);

//$ans ='01&10816365828&MORTHIMER ROGELIO $GAMIÑITO$GARCIA&COZA640804G3A&COZA640804HDFLVR00';

		if ( strpos($ans, '01&')===false  && strpos($ans, '02&') ===false && strpos($ans, '68&') ===false )
		{
			 if (strpos($ans, 'TIMED') === false and strpos($ans, 'TIMEOUT') ===false )
			{
				$respuesta = "99"; //error de conexion
				sendmail( $ErrEMail1, "BajíoNET. Error Servidor ProceSAR de BajíoNET no está respondiendo.\r\n\r\nFavor de avisar a los responsables." );
				//eZLog::writeNotice( "En activacion de eztransaccion/user consulta datos no hay conexion con ProceSAR" );	MAOS Oct2013 Quitar Notice
			}
		   	else
			{
				$respuesta = "98"; //tiempo de respuesta excedido
			}
			$particularFields ="&DiasPzo="."00"."&Porcen3=".($respuesta)."&Parent1=".($Parent1)."&RFC=".($RFC)."&RenInt=".($RenInt)."&desc1=".($desc1)."&Desc=".$Desc."&Pos=".($Pos)."&PAN=".urlencode($nss)."&ImpTipo=".$ImpTipo;
			$tr->blog($qki,"Consulta",$respuesta,$timeini.$usini,$timefin.$usfin, $ans->message,$Parent1, $RFC, $RenInt, $desc1, $Desc,$Accion,$ImpTipo);
			break;
		}
		list( $respuesta, $nss, $nombre_y_apellidos, $rfc_procesar, $curp ) = explode( "&", $ans );
		list( $nombres, $apellido_paterno, $apellido_materno ) = explode( "$", $nombre_y_apellidos );
		$rfc_procesar = substr( $rfc_procesar,  0, 10 );

		if ( $GLOBALS["DEBUGA"] == true ) {
			eZLog::writeNotice( "En activacion Valores enviados (Parent-Nombrecompleto) ->" . print_r( $Parent1, true ) . "|" );
			eZLog::writeNotice( "En activacion Valores enviados ->" . print_r( $RFC, true ) . "|" );
			eZLog::writeNotice( "En activacion Valores enviados ->" . print_r( $RenInt, true ) . "|" );
			eZLog::writeNotice( "En activacion de eztransaccion/user consulta datos (Parent1) ->" . print_r( $Parent1, true ) . "|" );
			eZLog::writeNotice( "En activacion de eztransaccion/user consulta datos (ans) ->" . print_r( $ans, true ) . "|" );
			eZLog::writeNotice( "En activacion de eztransaccion/user (respuesta) ->" . print_r( $respuesta, true ) ."|");
			eZLog::writeNotice( "En activacion de eztransaccion/user (nss) ->" . print_r( $nss, true ) ."|");
			eZLog::writeNotice( "En activacion de eztransaccion/user (nombre_y_apellidos) ->" . print_r( $nombre_y_apellidos, true ) ."|");
			eZLog::writeNotice( "En activacion de eztransaccion/user (rfc) ->" . print_r( $rfc_procesar, true ) ."|");
			eZLog::writeNotice( "En activacion de eztransaccion/user (curp) ->" . print_r( $curp, true ) ."|");
			eZLog::writeNotice( "En activacion de eztransaccion/user (nombres) ->" . print_r( $nombres, true ) ."|");
			eZLog::writeNotice( "En activacion de eztransaccion/user (apellido_paterno) ->" . print_r( $apellido_paterno, true ) ."|");
			eZLog::writeNotice( "En activacion de eztransaccion/user (apellido_materno) ->" . print_r( $apellido_materno, true ) ."|");
		}

		$ApellidoBajio = $Apellido;
		$nombre_procesar = trim( strtoupper( $nombres ) ) . " " . trim( strtoupper( $apellido_paterno ) ) . " " . trim( strtoupper( $apellido_materno ) );
		$nombre_bajio = trim( strtoupper( $Parent1 ) );
		$ApellidoBajio = trim( strtoupper( $ApellidoBajio ) );

		//a las variables que se van a comparar quitar los caracteres especiales para coicidir lo mayor posible
		quitarespeciales(&$nombre_bajio);
		quitarespeciales(&$nombre_procesar);
		quitarespeciales(&$RFC);
		quitarespeciales(&$rfc_procesar);
		quitarespeciales(&$ApellidoBajio);
		quitarespeciales(&$apellido_paterno);


		if ( $GLOBALS["DEBUGA"] == true ) {
			//eZLog::writeNotice( "En activacion de eztransaccion/user comparando (nombre_procesar) ->" . print_r( $nombre_procesar, true ) . "|" );
			//eZLog::writeNotice( "En activacion de eztransaccion/user comparando (nombre_bajio___) ->" . print_r( $nombre_bajio, true ) . "|" );
			//eZLog::writeNotice( "En activacion de eztransaccion/user comparando (RFC____________) ->" . print_r( $RFC, true ) . "|" );
			//eZLog::writeNotice( "En activacion de eztransaccion/user comparando (rfc_procesar-__) ->" . print_r( $rfc_procesar, true ) . "|" );
			eZLog::writeNotice( "En activacion de eztransaccion/user comparando (ApellidoBajio-_____) ->" . print_r( $ApellidoBajio, true ) . "|" );
			eZLog::writeNotice( "En activacion de eztransaccion/user comparando (apellido_paterno-__) ->" . print_r( $apellido_paterno, true ) . "|" );
		}
		//esta validacion es nombre completo y rfc sin homoclave
		//if( ( $respuesta == "01" ) && ( $nombre_bajio == $nombre_procesar ) && ( $RFC == $rfc_procesar) ) {
		//esta validacion es solamente el apellido paterno
		if(( $respuesta == "01" ) && ( $ApellidoBajio == $apellido_paterno )) {

				$datos = new SOAP_Client( $BridgeWebServiceURL );
				$datos->setOpt( 'timeout', 240 );
				$method = 'activar';
				//rellena o trunca la variable con 16 caracteres
				if ( strlen( $Pos ) > 16 ) {
					$Pos = substr( $Pos,  0, 16 );
				} else {
					$Pos = str_pad( $Pos, 16, "0" );
				}

				//rellena o trunca la variable con 100 caracteres
				if ( strlen( $Desc ) >100 ) {
					$Desc = substr( $Desc,  0, 100 );
				} else {
					$Desc = str_pad($Desc, 100, " " );
				}
				//$peticion .= "&" . $Pos;
				$url = $procesarWebservicesServer . $procesarWebservicesBaseURL . 'ServiceActivacionCLIPEV';
        $folioAct = $Pos;
        $email = $Desc;
				$params = array( 'user' => $procesarWebservicesUser, 'passwd' => $procesarWebservicesPassword, 'url' => $url, 'folio' => $folio, 'folioAct' => $folioAct, 'email' => $email);

				if ( $GLOBALS["DEBUGA"] == true ) {
						eZLog::writeNotice( "En activacion de eztransaccion/user activacion (endpoint) ->" . print_r( $url, true ) . "|" );
						eZLog::writeNotice( "En activacion de eztransaccion/user activacion (peticion) ->" . print_r( $params, true ) . "|" );
						eZLog::writeNotice( "En activacion de eztransaccion/user activacion (method) ->" . print_r( $method, true ) . "|" );
				}
				//AGG 27JUL2006 FUNCIONALIDAD PARA IMPRIMIR EN BITACORA LA HORA INICIAL Y FINAL DE LAS LLAMADAS A LOS WEB SERVICES
				$timeini		    = date( "H:i:s" );
				list( $usec, $sec )	= explode( " ", microtime() );
				$usini				= substr( $usec, 2);
				//AGG 27JUL2006 FUNCIONALIDAD PARA IMPRIMIR EN BITACORA LA HORA INICIAL Y FINAL DE LAS LLAMADAS A LOS WEB SERVICES

				$ans = $datos->call( $method, $params );

        if ( is_object ($ans) )
          {
            $ans = $datos->call( $method, $params );
            //eZLog::writeNotice( "Error: PROCESAR NO Regreso Respuesta en ACTIVACION1|" . $folio . "|" . date( "H:i:s" ) . "|" . $ans->message);	MAOS Oct2013 Quitar Notice
          }
				//AGG 27JUL2006 FUNCIONALIDAD PARA IMPRIMIR EN BITACORA LA HORA INICIAL Y FINAL DE LAS LLAMADAS A LOS WEB SERVICES
				$timefin		    = date( "H:i:s" );
				list( $usec, $sec )	= explode( " ", microtime() );
				$usfin				= substr( $usec, 2);
				//AGG 27JUL2006 FUNCIONALIDAD PARA IMPRIMIR EN BITACORA LA HORA INICIAL Y FINAL DE LAS LLAMADAS A LOS WEB SERVICES
				//$ans ='01';
				list( $ans, $ImpTipo ) = explode( "&", $ans );
				if ( strpos($ans, '01')===false  && strpos($ans, '02') ===false )
				{
					 if (strpos($ans, 'TIMED') === false and strpos($ans, 'TIMEOUT') ===false )
					{
						$respuesta = "99"; //error de conexion
						sendmail( $ErrEMail1, "BajíoNET. Error Servidor ProceSAR de BajíoNET no está respondiendo.\r\n\r\nFavor de avisar a los responsables." );
						//eZLog::writeNotice( "En activacion de eztransaccion/user consulta datos no hay conexion con ProceSAR" );	MAOS Oct2013 Quitar Notice
					}
		   			else
					{
						$respuesta = "98"; //tiempo de respuesta excedido
					}
          //eZLog::writeNotice( "Error: PROCESAR NO Regreso Respuesta en ACTIVACION2|" . $ans->message . "|" . date( "H:i:s" ));	MAOS Oct2013 Quitar Notice
 				  $particularFields ="&DiasPzo=".($respuesta2)."&Porcen3=".($respuesta)."&Parent1=".($Parent1)."&RFC=".($RFC)."&RenInt=".($RenInt)."&desc1=".($desc1)."&Desc=".$Desc."&Pos=".urlencode($Pos)."&PAN=".urlencode($nss)."&ImpTipo=".$ImpTipo;
					$tr->blog($qki,"Activacion",$respuesta2, $timeini.$usini,$timefin.$usfin,$ans->message,$Parent1,$RFC, $RenInt, $desc1, $Desc,$Accion,$respuesta,$respuesta2,$ImpTipo );
					break;
				}
				if ( $GLOBALS["DEBUGA"] == true ) {
						eZLog::writeNotice( "En activacion de eztransaccion/user activacion (ans) ->" . print_r( $ans, true ) . "|" );
				}
				if ( $ans == "01" ) {
					if ( $GLOBALS["DEBUGA"] == true ) {
							eZLog::writeNotice( "En activacion de eztransaccion/user activacion la cosa salio bien, se activo|" );
					}
				} else {
					if ( $GLOBALS["DEBUGA"] == true ) {
							eZLog::writeNotice( "En activacion de eztransaccion/user activacion la cosa NO salio bien, se NO activo|" );
					}
				}
				$particularFields ="&DiasPzo=".($ans)."&Porcen3=".($respuesta)."&Parent1=".($Parent1)."&RFC=".($RFC)."&RenInt=".($RenInt)."&desc1=".($desc1)."&Desc=".$Desc."&Pos=".urlencode($Pos)."&PAN=".urlencode($nss)."&ImpTipo=".$ImpTipo;
				$tr->blog($qki,"Activacion", $Parent1, $RFC, $RenInt, $desc1, $Desc,$Accion,$respuesta,$ans,$ImpTipo);
				break;
		}
		else
		{
			if ($respuesta == '01')
			{
				$respuesta = '03'; //no coinciden los datos
			}
			$particularFields ="&DiasPzo=".urlencode($Resulact)."&Porcen3=".($respuesta)."&Parent1=".($Parent1)."&RFC=".($RFC)."&RenInt=".($RenInt)."&desc1=".($desc1)."&Desc=".$Desc."&Pos=".urlencode($Pos)."&PAN=".urlencode($nss)."&Moti=".$ImpTipo;
			$tr->blog($qki,"Activacion", $Parent1, $RFC, $RenInt, $desc1, $ApellidoBajio . "!=" . $apellido_paterno,$Desc,$Accion,$respuesta,$ImpTipo);
			break;
		}
    }
    $transaccion_buffer = "";
    // DebugBreak();
    $ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=act&Access=".urlencode($Access)."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // activacion de CLIP

    $t->set_var( "transaccion_buffer", $transaccion_buffer );

    $t->pparse( "output", "activacion_tpl" );
}
else
{
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "userlogin.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "user_login_tpl" => "userlogin.tpl"
        ) );

    $t->set_var( "redirect_url", "/transaccion/activacion/" );

    $t->pparse( "output", "user_login_tpl" );
}
?>
