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
                         "eztransaccion/user/intl/", $Language, "chequera.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "chequera_tpl" => "chequera.tpl"
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
    // $Accion = "chequera";
    if( !isset( $_POST['EspecCh'] ) ) {
    	$_POST['EspecCh'] = "";
    }
    if( !isset( $_POST['FrAccount'] ) ) {
    	$_POST['FrAccount'] = "";
    }
    if( !isset( $_POST['NumCheq'] ) ) {
    	$_POST['NumCheq'] = "";
    }
    if( !isset( $_POST['Talonar'] ) ) {
    	$_POST['Talonar'] = "";
    }
    if( !isset( $_POST['TipoChq'] ) ) {
    	$_POST['TipoChq'] = "";
    }
    if( !isset( $_POST['DireccB'] ) ) {
    	$_POST['DireccB'] = "";
    }
    switch($_POST['Access']) {
    case "FrAc":
        $particularFields = "";
        $tr->blog($qki,"SolChequera", $_POST['FrAccount'], $_POST['NumCheq'], $_POST['EspecCh'], $_POST['Talonar'], $_POST['TipoChq']);
        break;
    case "Confirm":
    case "Process":
        $particularFields = "&FrAccount=".urlencode($_POST['FrAccount'])."&NumCheq=".urlencode($_POST['NumCheq'])."&EspecCh=".urlencode($_POST['EspecCh'])."&Talonar=".urlencode($_POST['Talonar'])."&TipoChq=".urlencode($_POST['TipoChq'])."&DireccB=".urlencode($_POST['DireccB']);
        $tr->blog($qki,"SolChequera", $_POST['FrAccount'], $_POST['NumCheq'], $_POST['EspecCh'], $_POST['Talonar'], $_POST['TipoChq']);
        break;
    }
    $transaccion_buffer = "";
    // DebugBreak();
    $ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=sch&Access=".urlencode($_POST['Access'])."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // chequera

    $t->set_var( "transaccion_buffer", $transaccion_buffer );
    $t->pparse( "output", "chequera_tpl" );
}
else
{
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "userlogin.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "user_login_tpl" => "userlogin.tpl"
        ) );

    $t->set_var( "redirect_url", "/transaccion/chequera/" );

    $t->pparse( "output", "user_login_tpl" );
}
?>
