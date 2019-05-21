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

$user =& eZUserBB::currentUser();

// DebugBreak();

if ( $user )
{
	$t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
						 "eztransaccion/user/intl/", $Language, "busquedacheques.php" );

	$t->setAllStrings();

	$t->set_file( array(
		"busquedacheques_tpl" => "busquedacheques.tpl"
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
	// $_POST['Accion'] = "busquedacheques";
	if( !isset( $_POST['Amount'] ) ) {
		$_POST['Amount'] = "";
	}
	if( !isset( $_POST['CqNo'] ) ) {
		$_POST['CqNo'] = "";
	}
	if( !isset( $_POST['Cq'] ) ) {
		$_POST['Cq'] = "";
	}
	if( !isset( $_POST['FrAccount'] ) ) {
		$_POST['FrAccount'] = "";
	}
	if( !isset( $_POST['Pos'] ) ) {
		$_POST['Pos'] = "";
	}
	switch( $_POST['Access'] )
	{
		case "FrAc":
			$particularFields = "";
			$tr->blog($qki,"BusquedaCheqs", $_POST['FrAccount'], $_POST['Pos'], $_POST['CqNo'], $_POST['Amount'], $_POST['Accion']);
			break;
		case "Confirm":
		case "Process":
			$particularFields = "&Pos=".urlencode($_POST['Pos'])."&FrAccount=".urlencode($_POST['FrAccount'])."&CqNo=".urlencode($_POST['CqNo'])."&Cq=".urlencode($_POST['Cq'])."&Amount=".urlencode($_POST['Amount']);
			$tr->blog($qki,"BusquedaCheqs", $_POST['FrAccount'], $_POST['Pos'], $_POST['CqNo'], $_POST['Amount'], $_POST['Accion']);
			break;
	}
	$transaccion_buffer = "";
	// DebugBreak();
	$ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=chq&Access=".urlencode($_POST['Access'])."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // busqueda de cheques

	$t->set_var( "transaccion_buffer", $transaccion_buffer );
	$t->pparse( "output", "busquedacheques_tpl" );

}
else
{
	$t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ), "eztransaccion/user/intl/", $Language, "userlogin.php" );
	$t->setAllStrings();
	$t->set_file( array( "user_login_tpl" => "userlogin.tpl" ) );
	$t->set_var( "redirect_url", "/transaccion/busquedacheques/" );
	$t->pparse( "output", "user_login_tpl" );
}
?>