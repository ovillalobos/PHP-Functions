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

include_once( "eztransaccion/classes/encrypt.php" );

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
                         "eztransaccion/user/intl/", $Language, "clave.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "clave_tpl" => "clave.tpl"
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
    $Empresa = "1"; //-- EVG-EDS Bajionet CNBV 01042005 agregue campo Empresa
    if(empty($_POST['Access'])) {
        $_POST['Access'] = "FrAc";
    }


    switch($_POST['Access']) {
    case "FrAc":
        $particularFields = "&Empresa=".urlencode($Empresa); //-- EVG-EDS Bajionet CNBV 19072005 agregue campo Empresa
        $tr->blog($qki,"Password", $_POST['FrAccount'], $_POST['ToAccount'], $_POST['DiasPzo'], $_POST['Amount'], $_POST['Accion']);
        break;
    case "Process":
        $particularFields = "&Passwd=".encrypt( $_POST['Passwd'], strtolower( $usr ) )."&NewPass=".encrypt( $_POST['NewPass'], strtolower( $usr ) )."&Empresa=".urlencode($Empresa)."&PwdActual=".encrypt( $_POST['PwdActual'], strtolower( $usr ) );
        $tr->blog($qki,"Password", $_POST['FrAccount'], $_POST['ToAccount'], $_POST['DiasPzo'], $_POST['Amount'], $_POST['Accion']);
        break;
    }
    $transaccion_buffer = "";
    // DebugBreak();

    $ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=pwd&Access=".urlencode($_POST['Access'])."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // clave

    $t->set_var( "transaccion_buffer", $transaccion_buffer );
	$t->pparse( "output", "clave_tpl" );
	if ( $_POST['Access'] == "Process" && $ret_code == 0 )
	{
		eZHTTPTool::header( "Refresh: 5; url=https://" . $ini->read_var( "site", "SecureServer" ) . $ServerNumber . $DomainPostfix . "/userbb/login/logout/" );
	}
	/*
	if ( $ret_code == 0 && $_POST['Access']=="Process" ) {
		$ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=ver&CustID=".urlencode($usr)."&Passwd=".urlencode($NewPass)."&Cadpriv=".urlencode($priv), "", $usr, $qki, $priv, $transaccion_buffer); // clave
		if($ret_code == 0 ) {
			eZHTTPTool::header( "Location: http://www.bb.com.mx/" );
		}
	}
	*/
}
else
{
	$t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
						 "eztransaccion/user/intl/", $Language, "userlogin.php" );

	$t->setAllStrings();

	$t->set_file( array(
		"user_login_tpl" => "userlogin.tpl"
		) );

	$t->set_var( "redirect_url", "/transaccion/clave/" );

	$t->pparse( "output", "user_login_tpl" );
}
?>