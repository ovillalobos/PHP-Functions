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

//SYEVG 18062012 212046-Aviso de Viaje
require_once( "eztransaccion/user/include/XMLToArray.php");
require_once( "nusoap-0.7.3/lib/nusoap.php");
//SYEVG 18062012 212046-Aviso de Viaje

include_once( "eztransaccion/classes/eztransaccion.php" );
include_once( "ezsession/classes/ezsession.php" );

//SYEVG 18062012 212046-Aviso de Viaje
include("eztransaccion/user/include/jscalendar/calendar-blue.css");
include("eztransaccion/user/include/jscalendar/calendar.js");
include("eztransaccion/user/include/js/FuncionesReimpresion.js");
//include_once( "pear/SOAP/Client.php" );
//SYEVG 18062012 212046-Aviso de Viaje
$session =& eZSession::globalSession();

if( !$session->fetch() )
    $session->store();
//SYEVG 18062012 212046-Aviso de Viaje
$session->setVariable("calendario",$session->variable("caledario")+1);
//SYEVG 18062012 212046-Aviso de Viaje

$ini =& $GLOBALS["GlobalSiteIni"];

$Language = $ini->read_var( "eZTransaccionMain", "Language" );
// $backend = $ini->read_var( "eZTransaccionMain", "Backend" );
$ServerNumber = $ini->read_var( "site", "ServerNumber" );
$DomainPostfix = $ini->read_var( "site", "DomainPostfix" );

$backend = "gw" . $ServerNumber. $DomainPostfix;

include_once( "ezuserbb/classes/ezuser.php" );

$user =& eZUserBB::currentUser();

// DebugBreak();
//SYEVG 18062012 212046-Aviso de Viaje
function callWebServiceNotificaSGCA($strTarjeta,$strTipo,$strDestino,$strComercio,$strMonto,$strFecIni,$strFecFin)
{
 //   echo "=========> Ingreso a  callWebServiceNotificaSGCA <=[".$strTarjeta."]========<BR>";
 //   echo "======Obtengo           WSDL===========<BR>";
    $ini =& $GLOBALS["GlobalSiteIni"];
    $tcUrlWSDL = $ini->read_var( "site", "SGCAUrlWSDL" );
 // echo "======WSDL===>".$tcUrlWSDL ."<=========<BR>";
 // echo "=========> Armando array para webservice<=========<BR>";
    $dataTC = array('strTarjeta'=>$strTarjeta,'strTipo'=>$strTipo,'strDestino'=>$strDestino,'strComercio'=>$strComercio,'strMonto'=>$strMonto,'strFecIni'=>$strFecIni,'strFecFin'=>$strFecFin);
    $param=array('PAN'=> $dataTC);
    $param = $dataTC;
 // echo "===> REQUEST A WS ->array contiene el request a enviar<====<BR>";
 // print_r($param);
 // echo "=====Creo cliente SOAP con nusoap ======================<BR>";
    $client= new nusoap_client($tcUrlWSDL,'wsdl');
 // echo "==========> Llamo al webservice <===============<BR>";
    $wsResponse = $client->call('GuardaAviso', $param);
 //  echo "<BR><BR>=========> Respuesta del webservice <=================<BR>";
 //  print_r($wsResponse);

    if ($client->fault)
    {
  //    echo "No se pudo completar la operacion";
        $wsResponse = "ERROR,No se pudo completar la operacion";
        return $wsResponse;
        //die();
    }
    else
    {
        $error = $client->getError();
        if( $error )
        {
   //       echo "<br>Ocurrio un error al enviar la informacion: " . $error . "<BR>";
            $wsResponse = "ERROR,".$error;
            return $wsResponse;
        }

    }

    //echo "<BR><BR>=========> FIN <=================<BR><BR>";
    //Para simular los regresos
    //$wsResponse = "OK,NV00000001";
    //$wsResponse = "ERROR,No se efectuo el registro en la Base de Datos";
    //wsResponse['GuardaAvisoResult'];
    return $wsResponse['GuardaAvisoResult'];
}
//SYEVG 18062012 212046-Aviso de Viaje

if ( $user )
{
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "tarjeta.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "tarjeta_tpl" => "tarjeta.tpl"
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
    if(empty($_POST['Access'])) {
        $_POST['Access'] = "FrAc";
    }
    // $_POST['Accion'] = "tarjeta";
    if( !isset( $_POST['Amount'] ) ) {
    	$_POST['Amount'] = "";
    }
    if( !isset( $_POST['DiasPzo'] ) ) {
    	$_POST['DiasPzo'] = "";
    }
    if( !isset( $_POST['FrAccount'] ) ) {
    	$_POST['FrAccount'] = "";
    }
    if( !isset( $_POST['PAN'] ) ) {
    	$_POST['PAN'] = "";
    }
    switch($_POST['Access']) {
    case "FrAc":
        $particularFields = "";
        $tr->blog($qki,"Tarjetas", $_POST['FrAccount'], $_POST['PAN'], $_POST['DiasPzo'], $_POST['Amount'], $_POST['Accion']);
        break;
    case "Confirm":
    case "Process":
//SYEVG 18062012 212046-Aviso de Viaje

//   echo "===Access:[".$_POST['Access']."]=========<BR>";
//   echo "===Accion:[".$_POST['Accion']."]=========<BR>";
//   echo "===Codigo de respuesta:Amount [".$_POST['Amount']."]=========<BR>";
//   echo "===Codigo de respuesta:emailej[".$_POST['emailej']."]=========<BR>";
//   echo "===Codigo de respuesta:Empresa[".$_POST['Empresa']."]=========<BR>";
//   echo "===Codigo de respuesta:Day    [".$_POST['Day']."]=========<BR>";
//   echo "===Codigo de respuesta:Month  [".$_POST['Month']."]=========<BR>";
//   echo "===Codigo de respuesta:Year   [".$_POST['Year']."]=========<BR>";
//   echo "===Codigo de respuesta:DayF   [".$_POST['DayF']."]=========<BR>";
//   echo "===Codigo de respuesta:MonthF [".$_POST['MonthF']."]=========<BR>";
//   echo "===Codigo de respuesta:YearF  [".$_POST['YearF']."]=========<BR>";
//   echo "===Codigo de respuesta:Accion [".$_POST['Accion']."]=========<BR>";
//   echo "===Codigo de respuesta:PAN    [".$_POST['PAN']."]=========<BR>";

   	if ($_POST['Access'] == "Process" and ($_POST['Accion'] == "Aviaje" or $_POST['Accion'] == "Caresp"))
        {

	    	$bande = 0;
	    	if ($_POST['Accion'] == "Aviaje")
	    	{
	    		$tipo = "AV";
	    		$bande = 1;
	    		$strDestino = $_POST['emailej'];
	       	$strComercio= "";
	    		$strMonto   = 0;

	       	$datestringIni= $_POST['Year']."/".$_POST['Month']."/".$_POST['Day'];
	    		$timeIni = strtotime( $datestringIni );
	    		$myDateIni = date( 'd/M/Y', $timeIni );

	    		$datestringFin= $_POST['YearF']."/".$_POST['MonthF']."/".$_POST['DayF'];
	    		$timeFin = strtotime( $datestringFin );
	    		$myDateFin = date( 'd/M/Y', $timeFin );

	    // echo "===Aviaje $myDateIni       [".$myDateIni."]=========<BR>";
	    //	echo "===Aviaje $myDateFin       [".$myDateFin."]=========<BR>";


	    	}
	    	if ($_POST['Accion'] == "Caresp")
	    	{
	      		$datestringIni= $_POST['Year']."/".$_POST['Month']."/".$_POST['Day'];
	    		$timeIni = strtotime( $datestringIni );
	    		$myDateIni = date( 'd/M/Y', $timeIni );

	    		$tipo = "CE";
	    		$bande = 1;
			$strComercio= $_POST['emailej'];
	    		$strMonto   = $_POST['Amount'];

	     		//echo "===Caresp $myDateIni       [".$myDateIni."]=========<BR>";

	    	}
	    	if ($bande == 1)
	    	{
			$strTarjeta = $_POST['PAN'];
			$strTipo    = $tipo;
			$strFecIni  = $myDateIni;
			$strFecFin  = $myDateFin;

		//	echo "===Parametros:datestringIni       [".$datestringIni."]=========<BR>";
		//     echo "===Parametros:datestringFin       [".$datestringFin."]=========<BR>";
		//	echo "===Codigo de respuesta:myDateIni [".$myDateIni."]=========<BR>";
		//	echo "===Codigo de respuesta:myDateFin [".$myDateFin."]=========<BR>";
		//     echo "===Codigo de respuesta:timeIni   [".$timeIni."]=========<BR>";
		//     echo "===Codigo de respuesta:timeFin   [".$timeFin."]=========<BR>";
		//	echo "===Codigo de respuesta:strTarjeta[".$strTarjeta."]=========<BR>";
		//	echo "===Codigo de respuesta:strTipo   [".$strTipo."]=========<BR>";
		//     echo "===Codigo de respuesta:strFecFin [".$strFecFin."]=========<BR>";
		//     echo "===Codigo de respuesta:DayF      [".$_POST['DayF']."]=========<BR>";
		//	echo "===Codigo de respuesta:MonthF    [".$_POST['MonthF']."]=========<BR>";
		//	echo "===Codigo de respuesta:YearF     [".$_POST['YearF']."]=========<BR>";
		//	echo "===Codigo de respuesta:Accion    [".$_POST['Accion']."]=========<BR>";
		//	echo "===Codigo de respuesta:PAN       [".$_POST['PAN']."]=========<BR>";
		//    	echo "===Consulto a webservice<BR>";

	        	$Mensaje   = callWebServiceNotificaSGCA($strTarjeta,$strTipo,$strDestino,$strComercio,$strMonto,$strFecIni,$strFecFin);
	       // 	echo "===Mensaje :Mensaje      [".$Mensaje."]=========<BR>";
	        	$Respuesta = substr($Mensaje,0, 2);
	       // 	echo "===Respuesta:Respuesta      [".$Respuesta."]=========<BR>";



	        	if ($Respuesta == "OK")
	        	{
				$Detalle   = substr($Mensaje,3,strlen($Mensaje));
		//		echo "===Detalle:[".$Detalle."]=========<BR>";
	        		$folio = $Detalle;
	        	}
	        	else
	        	{
				$Detalle   = substr($Mensaje,6,strlen($Mensaje));
		//		echo "===Detalle:[".$Detalle."]=========<BR>";
	        		$folio = 0;
	        	}
	        	$gene4 = $folio;
	        	$Nomben2 = $Mensaje;
	        }
	        //echo "===folio:[".$folio."]=========<BR>";
	        //echo "===gene4:[".$gene4."]=========<BR>";
	        //echo "===Detalle:[".$Detalle."]=========<BR>";
	        //echo "===Nomben2:[".$Nomben2."]=========<BR>";
	        //echo "===Accion:[".$_POST['Accion']."]=========<BR>";
	}
    	$particularFields = "&emailej=".urlencode( $_POST['emailej'] )."&Empresa=".urlencode( $_POST['Empresa'] )."&Day=".urlencode( $_POST['Day'] )."&Month=".urlencode( $_POST['Month'] )."&Year=".urlencode( $_POST['Year'] )."&DayF=".urlencode( $_POST['DayF'] )."&MonthF=".urlencode( $_POST['MonthF'] )."&YearF=".urlencode( $_POST['YearF'] )."&Accion=".urlencode($_POST['Accion'])."&Amount=".$_POST['Amount']."&PAN=".urlencode($_POST['PAN'])."&gene4=".urlencode($gene4)."&Nomben2=".$Nomben2."&Button=OK";
        //$particularFields = "&Accion=".urlencode($_POST['Accion'])."&PAN=".urlencode($_POST['PAN'])."&Button=OK";
        $tr->blog($qki,"Tarjetas", $_POST['FrAccount'], $_POST['PAN'], $_POST['DiasPzo'], $_POST['Amount'], $_POST['Accion']);
//SYEVG 18062012 212046-Aviso de Viaje
        break;
    }
    $transaccion_buffer = "";
    // DebugBreak();
    $ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=crd&Access=".urlencode($_POST['Access'])."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // tarjeta

    $t->set_var( "transaccion_buffer", $transaccion_buffer );
    $t->pparse( "output", "tarjeta_tpl" );

}
else
{
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "userlogin.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "user_login_tpl" => "userlogin.tpl"
        ) );

    $t->set_var( "redirect_url", "/transaccion/tarjeta/" );

    $t->pparse( "output", "user_login_tpl" );
}
?>