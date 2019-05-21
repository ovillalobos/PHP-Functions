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
include_once( "ezreimp/classes/ezreimp.php" ); //DBA Reimpresion de Comprobantes

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

$user =& eZUserBB::currentUser();

// DebugBreak();

if ( $user )
{
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "pagoreferenciado.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "pagoreferenciado_tpl" => "pagoreferenciado.tpl"
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
		
	if(!empty($_POST['RenCap']))
		$RenCap = $_POST['RenCap'];
		
	if(!empty($_POST['Amount']))
		$Amount = $_POST['Amount'];
		
	if(!empty($_POST['PlazaB']))
		$PlazaB = $_POST['PlazaB'];
		
	if(!empty($_POST['code']))
		$code = $_POST['code'];
//HB    
    
    if(empty($Access)) {
        $Access = "FrAc";
    }

    switch($Access) {
    case "FrAc":
        $particularFields = "";
        //$tr->blog($qki,"Impuestos", $FrAccount, $ImpTipo, $Month, $Year, $Amount);
		$tr->blog($qki,"PagoReferenciado", $FrAccount, $RenCap, $Amount, $PlazaB);
        break;
    case "Confirm":
    case "Process":
        //$particularFields = "&RFC=".urlencode($RFC)."&FrAccount=".urlencode($FrAccount)."&ImpTipo=".urlencode($ImpTipo)."&ImpEnvio=".urlencode($ImpEnvio)."&Day=".urlencode($Day)."&Month=".urlencode($Month)."&Year=".urlencode($Year)."&DayF=".urlencode($DayF)."&MonthF=".urlencode($MonthF)."&YearF=".urlencode($YearF)."&Amount=".urlencode($Amount);
        //$tr->blog($qki,"Impuestos", $FrAccount, $ImpTipo, $Month, $Year, $Amount);
		$particularFields = "&Passwd=".encrypt( $code, strtolower( $usr ) )."&FrAccount=".urlencode($FrAccount)."&RenCap=".urlencode($RenCap)."&Amount=".urlencode($Amount)."&PlazaB=".urlencode($PlazaB);
		$tr->blog($qki,"PagoReferenciado", $FrAccount, $RenCap, $Amount, $PlazaB);
        break;
    }
    $transaccion_buffer = "";
    // DebugBreak();
    $ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=ire&Access=".urlencode($Access)."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer);

	// ********************************************************************
	if ($Access == "Process" and strpos($transaccion_buffer,"Su Pago ha sido procesado con") != false)
	//if ($Access == "Process" )
	{
		$buffer2 = str_replace(chr(34),chr(92).chr(34),$transaccion_buffer);

		$ini =& INIFile::globalINI();

		$SecureServer	  = $ini->read_var( "site", "SecureServer" );
		$SecureServerType = $ini->read_var( "site", "SecureServerType" );
		$DomainPostfix	  = $ini->read_var( "site", "DomainPostfix" );
		$ServerNumber	  = $ini->read_var( "site", "ServerNumber" );
		
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
		$Pagina = $Pagina."<p><FONT SIZE=1>Av. Manuel J. Clouthier No. 508 Col. Jardines del Campestre CP 37128 Le�n, Gto.</FONT> </p>";
		$Pagina = $Pagina."<p><FONT SIZE=1>Consultas y aclaraciones 01 (477) 710- 4600 o al 01 (800) 471-0400</FONT></p>";
		$Pagina = $Pagina."<p><FONT SIZE=1>LA TRANSFERENCIA A LA QUE HACE REFERENCIA ESTE COMPROBANTE FUE REALIZADA CONFORME A LA INFORMACI�N E INSTRUCCIONES QUE NUESTRO CLIENTE ALIMENT� EN EL SISTEMA DE BANCO DEL BAJ�O, POR LO TANTO EL CLIENTE ES EL �NICO RESPONSABLE PRESENTE Y FUTURO POR CUALQUIER ERROR U OMISI�N EN ESTA OPERACI�N.</FONT></P>";
		//$Pagina = $Pagina."<p><FONT SIZE=1>EL �NICO COMPROBANTE OFICIAL DE ESTA TRANSACCI�N ES EL ESTADO DE CUENTA QUE EMITE BANCO DEL BAJIO.</FONT></p>";  ATAR se comenta por especificaciones del SAT
		$Pagina = $Pagina."</BODY></HTML>\"; NFW =";
		$Pagina = $Pagina." window.open(\"\",'Recibo',\"toolbar=no,menubar=no,scrollbars=2,\"+s); NFW.blur(); NFW.resizeTo(windowW,windowH); NFW.moveTo(windowX,windowY); NFW.";
		$Pagina = $Pagina."document.open(); NFW.document.write(cadena); NFW.document.close(); NFW.window.focus(); } ventana(); </SCRIPT>";
		//DBA Reimpresion de Comprobantes
		$FechaHora = "";
		$QryRIC = new eZReImp( );
		$QryRIC -> store($CustID,$FrAccount,$buffer2,"ire",$PageTitle,$FechaHora);
		//DBA Reimpresion de Comprobantes
	}
	// ********************************************************************

    $t->set_var( "transaccion_buffer", $transaccion_buffer.$Pagina );
    $t->pparse( "output", "pagoreferenciado_tpl" );
}
else
{
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "userlogin.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "user_login_tpl" => "userlogin.tpl"
        ) );

    $t->set_var( "redirect_url", "/transaccion/pagoreferenciado/" );

    $t->pparse( "output", "user_login_tpl" );
}
?>