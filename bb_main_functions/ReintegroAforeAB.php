<?php
//
//
// Created on: <26-Feb-2007 17:37:53 DGM>
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
include_once( "classes/INIFile.php"		);
include_once( "classes/eztemplate.php"	);
include_once( "classes/ezhttptool.php"	);

include_once( "eztransaccion/classes/eztransaccion.php" );
include_once( "ezsession/classes/ezsession.php"			);
include_once( "classes/ezdatetime.php"					);
include_once( "eztransaccion/classes/encrypt.php"		);

$session =& eZSession::globalSession();

if( !$session->fetch() )
    $session->store();

$ini =& $GLOBALS["GlobalSiteIni"];

$Language		= $ini->read_var( "eZTransaccionMain", "Language" );
$ServerNumber	= $ini->read_var( "site", "ServerNumber" );
$DomainPostfix	= $ini->read_var( "site", "DomainPostfix" );

$backend = "gw" . $ServerNumber. $DomainPostfix;

include_once( "ezuserbb/classes/ezuser.php" );
include_once( "ezreimp/classes/ezreimp.php" ); //DBA Reimpresion de Comprobantes

$user =& eZUserBB::currentUser();

if ( $user )
{
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "ReintegroAforeAB.php" );

    $t->setAllStrings();

	$dateTime	= new eZDateTime( );
	$timeStamp	= $dateTime->timeStamp();
	$time		=& date('H:i:s', $timeStamp );
	$date		=& date('jMY', $timeStamp );

    $t->set_file( array("ReintegroAforeAB_tpl" => "ReintegroAforeAB.tpl" ) );

    $session =& eZSession::globalSession();

    if ( !$session->fetch() )
    {
        $session->store();
    }

    $tr					= new eZTransaccion( );
    $usr				= $session->variable( "r_usr" );
    $qki				= $session->variable( "r_qki" );
    $priv				= $session->variable( "r_priv" );
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
		
	if(!empty($_POST['CadPriv']))
		$CadPriv = $_POST['CadPriv'];
		
	if(!empty($_POST['Empresa']))
		$Empresa = $_POST['Empresa'];
		
	if(!empty($_POST['Accion']))
		$Accion = $_POST['Accion'];
		
	if(!empty($_POST['Parent2']))
		$Parent2 = $_POST['Parent2'];
		
	if(!empty($_POST['cbTipoAf']))
		$cbTipoAf = $_POST['cbTipoAf'];
		
	if(!empty($_POST['cbTipoAp']))
		$cbTipoAp = $_POST['cbTipoAp'];
		
	if(!empty($_POST['cbDedu']))
		$cbDedu = $_POST['cbDedu'];

	if(!empty($_POST['code']))
		$code = $_POST['code'];
//HB
    if( empty ( $Access     ) ) {	$Access		= "FrAc";   }
    if( !isset /*(HB AGL - Ajustes PHP5)*/( $FrAccount	) ) { 	$FrAccount	= "";		}
	if( !isset /*(HB AGL - Ajustes PHP5)*/( $RenCap		) ) { 	$RenCap		= "";		}
	if( !isset /*(HB AGL - Ajustes PHP5)*/( $Amount		) ) {	$Amount		= "";		}
    if( !isset /*(HB AGL - Ajustes PHP5)*/( $CadPriv	) ) { 	$CadPriv	= "";		}

	if( !isset /*(HB AGL - Ajustes PHP5)*/( $Empresa	) ) {	$Empresa	= "Afore Afirme Bajio";	}
	if( !isset /*(HB AGL - Ajustes PHP5)*/( $Accion	) ) {	$Accion = "Rein";	}
    if( !isset /*(HB AGL - Ajustes PHP5)*/( $Parent2	) ) { 	$Parent2	= "";		}

    if( !isset /*(HB AGL - Ajustes PHP5)*/( $cbTipoAf	) ) { 	$cbTipoAf	= "";		}
    if( !isset /*(HB AGL - Ajustes PHP5)*/( $cbTipoAp	) ) { 	$cbTipoAp	= "";		}
    if( !isset /*(HB AGL - Ajustes PHP5)*/( $cbDedu	) ) { 	$cbDedu	= "";		}
/*
    switch( $Access )
	{
		case "FrAc":
			$particularFields = "&Accion=".$Accion."&Empresa=".($Empresa);
			$tr->blog($qki,"PagoServicios", $FrAccount, $Empresa);
			break;
		case "Confirm":
			$particularFields = "&Parent2=".$Paren2."&Accion=".$Accion."&CadPriv=".urlencode($CadPriv)."&FrAccount=".urlencode($FrAccount)."&Empresa=".($Empresa)."&Amount=1"."&RenCap=".urlencode($RenCap)."&cbTipoAf=1-IMSS&cbTipoAp=6-REINTEGRO DE RECURSOS&cbDedu=".$cbDedu;
			$tr->blog($qki,"PagoServicios", $FrAccount, $Empresa, $Amount, $RenCap, $cbTipoAf, $cbTipoAp, $cbDedu);
			break;
		case "Process":
			$particularFields = "&Parent2=".$Paren2."&Accion=".$Accion."&CadPriv=".urlencode($CadPriv)."&FrAccount=".urlencode($FrAccount)."&Empresa=".($Empresa)."&Amount=".urlencode($Amount)."&RenCap=".urlencode($RenCap)."&Passwd=".encrypt( $code, strtolower( $usr ) )."&cbTipoAf=1-IMSS&cbTipoAp=6-REINTEGRO DE RECURSOS&cbDedu=2-NO DEDUCIBLE DE IMPUESTOS";
			$tr->blog($qki,"PagoServicios", $FrAccount, $Empresa, $Amount, $RenCap, $cbTipoAf, $cbTipoAp, $cbDedu);
			break;
    }

    $transaccion_buffer = "";
    //var_dump($particularFields);
    $ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=afr&Access=".urlencode($Access)."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer);

	// AGG I 25Nov2005 Generacion de comprobantes
	if ($Access == "Process" and strpos($transaccion_buffer,"No. de Autoriza") != false)
	{
		if (strpos($transaccion_buffer,"El saldo disponible") === false ) //si no tiene saldos
		{
			$buffer2 = str_replace(chr(34),chr(92).chr(34),$transaccion_buffer);
		}
		else //si tiene saldos hay que quitarlos
		{
			if (strpos($transaccion_buffer,"Para clientes que requieren comprobante") === false ) // si no tiene DFA
			{
				$buffer3 = substr($transaccion_buffer,0,strpos($transaccion_buffer,"El saldo disponible"));
				$buffer4 = substr($transaccion_buffer,strpos($transaccion_buffer,"No. de Autoriza"));
				$buffer2 = str_replace(chr(34),chr(92).chr(34),$buffer3.$buffer4);
			}
			else
			{
				$buffer3 = substr($transaccion_buffer,0,strpos($transaccion_buffer,"El saldo disponible"));
				$buffer4 = substr($transaccion_buffer,strpos($transaccion_buffer,"Para clientes que requieren comprobante"));
				$buffer2 = str_replace(chr(34),chr(92).chr(34),$buffer3.$buffer4);
			}
		}
		$buffer2 = substr($buffer2,0,strpos($buffer2,"(Por favor anote el "));


		$ini =& INIFile::globalINI();
		$SecureServer		= $ini->read_var( "site", "SecureServer"	 );
		$SecureServerType	= $ini->read_var( "site", "SecureServerType" );
		$DomainPostfix		= $ini->read_var( "site", "DomainPostfix"	 );
		$ServerNumber		= $ini->read_var( "site", "ServerNumber"	 );

		if ( $Comprobante == "Activo" )
		{
		$Pagina = "<SCRIPT LANGUAGE='JavaScript'> function ventana() { var windowW = 800; var windowH = 450; var windowX = 10; var windowY = 10; var title = 'Recibo'; s = 'width='+windowW+',height='+windowH; cadena=\"<HTML><HEAD><link REL='STYLESHEET' HREF='" . $SecureServerType . "://" . $SecureServer . $ServerNumber . $DomainPostfix . "/impuestos/recibos.css' TYPE='text/css'><TITLE>Reintegro de recursos</TITLE>";
		$Pagina = $Pagina."<SCRIPT LANGUAGE='JavaScript'> function window_onbeforeprint() { Imprimir.style.visibility = 'hidden'; Salir.style.visibility = 'hidden';} function window_onafterprint() { Imprimir.style.visibility = 'visible'; Salir.style.";
		$Pagina = $Pagina."visibility = 'visible'; }\"+\"<\"+\"/\"+\"SCRIPT></HEAD><BODY LANGUAGE='javascript' onbeforeprint='return window_onbeforeprint()' onafterprint='return window_onafterprint()'>";
		$Pagina = $Pagina."<INPUT type='button' value='Imprimir' id=Imprimir name=Imprimir onclick='window.print();'>&nbsp;<INPUT type='button' value=' Salir  ' id=Salir name=Salir onclick='window.close();'>";
		$Pagina = $Pagina."<BR>";
		$Pagina = $Pagina."<P><CENTER>";
		$Pagina = $Pagina."<IMG SRC='" . $SecureServerType . "://" . $SecureServer . $ServerNumber . $DomainPostfix . "/impuestos/LOGO_BB.GIF' ALIGN= 'TOP' Banco del Bajio>";
		$Pagina = $Pagina."</P></CENTER>";
		$Pagina = $Pagina."<H3>Reintegro de recursos</H3>";
		$Pagina = $Pagina."<hr noshade='noshade' size='4' color='#5A419c' />";
		$Pagina = $Pagina."<P ALIGN='RIGHT'><B><FONT>$date $time</FONT></B></P>";
		$Pagina = $Pagina.$buffer2;
		$Pagina = $Pagina."<hr noshade='noshade' size='4' color='#5A419c' />";
		$Pagina = $Pagina."<p><FONT SIZE=1>Av. Manuel J. Clouthier No. 508 Col. Jardines del Campestre CP 37128 León, Gto.</FONT> </p>";
		$Pagina = $Pagina."<p><FONT SIZE=1>Consultas y aclaraciones 01 (477) 710- 4600 o al 01 (800) 471-0400</FONT></p>";
		$Pagina = $Pagina."<p><FONT SIZE=1>LA TRANSFERENCIA A LA QUE HACE REFERENCIA ESTE COMPROBANTE FUE REALIZADA CONFORME A LA INFORMACIÓN E INSTRUCCIONES QUE NUESTRO CLIENTE ALIMENTÓ EN EL SISTEMA DE BANCO DEL BAJÍO, POR LO TANTO EL CLIENTE ES EL ÚNICO RESPONSABLE PRESENTE Y FUTURO POR CUALQUIER ERROR U OMISIÓN EN ESTA OPERACIÓN.</FONT></P>";
		$Pagina = $Pagina."<p><FONT SIZE=1>EL ÚNICO COMPROBANTE OFICIAL DE ESTA TRANSACCIÓN ES EL ESTADO DE CUENTA QUE EMITE BANCO DEL BAJIO.</FONT></p>";
		$Pagina = $Pagina."</BODY></HTML>\"; NFW =";
		$Pagina = $Pagina." window.open(\"\",'Recibo',\"toolbar=no,menubar=no,scrollbars=2,\"+s); NFW.blur(); NFW.resizeTo(windowW,windowH); NFW.moveTo(windowX,windowY); NFW.";
		$Pagina = $Pagina."document.open(); NFW.document.write(cadena); NFW.document.close(); NFW.window.focus(); } ventana(); </SCRIPT>";
		}
		//DBA Reimpresion de Comprobantes
		$FechaHora = $date . " " . $time;
		$QryRIC = new eZReImp( );
		$QryRIC -> store($CustID,$FrAccount,$buffer2,"afo","Aportaci&oacute;n Voluntaria",$FechaHora);
		//DBA Reimpresion de Comprobantes

	}
    $t->set_var( "transaccion_buffer", $transaccion_buffer.$Pagina );

    $t->pparse( "output", "ReintegroAforeAB_tpl" );
*/
}
else
{
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "userlogin.php" );
    $t->setAllStrings();

    $t->set_file( array("user_login_tpl" => "userlogin.tpl") );
    $t->set_var	( "redirect_url", "/transaccion/ReintegroAforeAB/" );
    $t->pparse	( "output", "user_login_tpl" );
}
?>