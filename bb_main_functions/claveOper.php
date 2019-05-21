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
$NormalSite = $ini->read_var( "site", "WWWServer" ); //----- EVG-EDS 14092005 Cerrar la sesion
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
                         "eztransaccion/user/intl/", $Language, "claveOper.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "claveOper_tpl" => "claveOper.tpl"
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

	if(!empty($_POST['Empresa']))
		$Empresa = $_POST['Empresa'];
		
	if(!empty($_POST['FrAccount']))
		$FrAccount = $_POST['FrAccount'];
		
	if(!empty($_POST['ToAccount']))
		$ToAccount = $_POST['ToAccount'];
		
	if(!empty($_POST['DiasPzo']))
		$DiasPzo = $_POST['DiasPzo'];
		
	if(!empty($_POST['Amount']))
		$Amount = $_POST['Amount'];
		
	if(!empty($_POST['Accion']))
		$Accion = $_POST['Accion'];
		
	if(!empty($_POST['NewPass']))
		$NewPass = $_POST['NewPass'];
		
	if(!empty($_POST['code']))
		$code = $_POST['code'];
//HB    
    
    if(empty($Access)) {
        $Access = "FrAc";
    }
    $Empresa = "2";
    switch($Access) {
    case "FrAc":
        $particularFields = "&Empresa=".urlencode($Empresa); //-- EVG-EDS Bajionet CNBV 19072005 agregue campo Empresa
        $tr->blog($qki,"Password", $FrAccount, $ToAccount, $DiasPzo, $Amount, $Accion);
        break;
    case "Process":
        $particularFields = "&Passwd=".encrypt( $Passwd, strtolower( $usr ) )."&NewPass=".encrypt( $NewPass, strtolower( $usr ) )."&code=".encrypt( $code, strtolower( $usr ) )."&Empresa=".urlencode($Empresa);
        $tr->blog($qki,"Password", $FrAccount, $ToAccount, $DiasPzo, $Amount, $Accion);
        break;
    }
//	var_dump($particularFields);

    $transaccion_buffer = "";
    // DebugBreak();
    $ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=pwd&Access=".urlencode($Access)."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // clave

    $t->set_var( "transaccion_buffer", $transaccion_buffer );
    $t->pparse( "output", "claveOper_tpl" );
	if ( $Access == "Process" && $ret_code == 0 )
	{
		eZHTTPTool::header( "Refresh: 5; url=https://" . $ini->read_var( "site", "SecureServer" ) . $ServerNumber . $DomainPostfix . "/userbb/login/logout/" );
	}
}
else
{
	$t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
						 "eztransaccion/user/intl/", $Language, "userlogin.php" );

	$t->setAllStrings();

	$t->set_file( array(
		"user_login_tpl" => "userlogin.tpl"
		) );

	$t->set_var( "redirect_url", "/transaccion/claveOper/" );

	$t->pparse( "output", "user_login_tpl" );
}
?>