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

$session =& eZSession::globalSession();

if( !$session->fetch() )
    $session->store();

$ini =& $GLOBALS["GlobalSiteIni"];

$Language = $ini->read_var( "eZTransaccionMain", "Language" );
// $backend = $ini->read_var( "eZTransaccionMain", "Backend" );
$ServerNumber = $ini->read_var( "site", "ServerNumber" );
$DomainPostfix = $ini->read_var( "site", "DomainPostfix" );

$backend = "gw" . $ServerNumber. $DomainPostfix;

include_once( "ezuserbb/classes/ezuser.php" );
include_once( "ezreimp/classes/ezreimp.php" ); //DBA Reimpresion de Comprobantes

$user =& eZUserBB::currentUser();

// DebugBreak();

if ( $user )
{
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "pagosanuales.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "pagosanuales_tpl" => "pagosanuales.tpl"
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
		
	if(!empty($_POST['FrAccount']))
		$FrAccount = $_POST['FrAccount'];
		
	if(!empty($_POST['DatosImp']))
		$DatosImp = $_POST['DatosImp'];
		
	if(!empty($_POST['Year']))
		$Year = $_POST['Year'];
		
	if(!empty($_POST['Amount']))
		$Amount = $_POST['Amount'];
		
	if(!empty($_POST['CadOrig']))
		$CadOrig = $_POST['CadOrig'];
		
	if(!empty($_POST['code']))
		$code = $_POST['code'];
		
	if(!empty($_POST['UbNo']))
		$UbNo = $_POST['UbNo'];
		
	if(!empty($_POST['CustID']))
		$CustID = $_POST['CustID'];
//HB    
//ATAR Bloqueo de Pagos Anuales
/*    $mensaje_sat = "";   
    $mensaje_sat=	 "<table width='80%' height='62' border='1' align='center' cellpadding='5' cellspacing='0'><tr><td colspan='0' align='center'>Sr. Contribuyente, el SAT le informa:<br>A partir de Agosto del 2014 est&aacute; obligado a utilizar el Servicio de Pago Referenciado para presentar sus pagos anuales o definitivos de impuestos federales, no debe de utilizar el esquema anterior de pagos electr&oacutenicos.</td></tr></table>";	
    $noPagProv=1;
   if($noPagProv==0){*/
    if(empty($Access)) {
        $Access = "FrAc";
    }
    switch($Access) {
    case "FrAc":
        $particularFields = "";
        $tr->blog($qki,"Impuestos Pagos Anuales", $FrAccount, $FrAccount, $DatosImp, $Year, $Amount);
        break;
    case "Confirm":
        $Datos1 = strtr($Datos1,"&Ρ‘","{^<");
	 $Datos1 = strtr($Datos1,"%","~");//EVG-EDS 02112004 EVG-EDS 28112005 Actualizacion
    	//$DatosImp = strtr($DatosImp,"αινσϊό°","aeiouuo");
    	$DatosImp = strtr($DatosImp,"αινσϊό°","Ώ?;*[]o");
    	$DatosImp = strtr($DatosImp,"&Ρ","{^");
        $particularFields = "&FrAccount=".urlencode($FrAccount)."&Datos=".$Datos1."&DatosImp=".$DatosImp."&CadOrig=".$CadOrig;
        $tr->blog($qki,"Impuestos Pagos Anuales", $FrAccount, $Datos, $DatosImp, $Year, $Amount);
        break;
    case "Process":
        $particularFields = "&Passwd=".encrypt( $code, strtolower( $usr ) )."&FrAccount=".urlencode($FrAccount)."&UbNo=".$UbNo;
        $tr->blog($qki,"Impuestos Pagos Anuales", $FrAccount, $UbNo, $DatosImp, $Year, $Amount);
        break;
    }
    $transaccion_buffer = "";
    // DebugBreak();
    $ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=ipj&Access=".urlencode($Access)."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // pago de impuestos

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
			//DBA Reimpresion de Comprobantes
			$FechaHora = "";
			$QryRIC = new eZReImp( );
			$QryRIC -> store($CustID,$FrAccount,$buffer2,"imp","",$FechaHora);
			//DBA Reimpresion de Comprobantes
		}
	}

//} //ATAR Bloqueo de pagos anuales
    //$t->set_var( "transaccion_buffer",$mensaje_sat.$transaccion_buffer.$Pagina );
    $t->set_var( "transaccion_buffer",$transaccion_buffer.$Pagina );
    $t->pparse( "output", "pagosanuales_tpl" );
}
else
{
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "userlogin.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "user_login_tpl" => "userlogin.tpl"
        ) );

    $t->set_var( "redirect_url", "/transaccion/pagosanuales/" );

    $t->pparse( "output", "user_login_tpl" );
}
?>