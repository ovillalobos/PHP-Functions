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
                         "eztransaccion/user/intl/", $Language, "speua.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "speua_tpl" => "speua.tpl"
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
		
	if(!empty($_POST['Amount']))
		$Amount = $_POST['Amount'];
		
	if(!empty($_POST['Apocope']))
		$Apocope = $_POST['Apocope'];
		
	if(!empty($_POST['NombreB']))
		$NombreB = $_POST['NombreB'];
		
	if(!empty($_POST['PlazaB']))
		$PlazaB = $_POST['PlazaB'];
		
	if(!empty($_POST['RFC']))
		$RFC = $_POST['RFC'];
		
	if(!empty($_POST['RenInt']))
		$RenInt = $_POST['RenInt'];
		
	if(!empty($_POST['FrAccount']))
		$FrAccount = $_POST['FrAccount'];
		
	if(!empty($_POST['Button']))
		$Button = $_POST['Button'];
		
	if(!empty($_POST['Desc']))
		$Desc = $_POST['Desc'];
		
	if(!empty($_POST['RenInt']))
		$RenInt = $_POST['RenInt'];
//HB    
    if(empty($Access)) {
        $Access = "FrAc";
    }
    // $Accion = "";
    if( !isset( $Amount ) ) {
    	$Amount = "";
    }
    if( !isset( $Apocope ) ) {
    	$Apocope = "";
    }
    if( !isset( $NombreB ) ) {
    	$NombreB = "";
    }
    if( !isset( $PlazaB ) ) {
    	$PlazaB = "";
    }
    if( !isset( $RFC ) ) {
    	$RFC = "";
    }
    if( !isset( $RenInt ) ) {
    	$RenInt = "";
    }
    switch($Access) {
    case "FrAc":
        $particularFields = "";
        $tr->blog($qki,"Speua", $FrAccount, $Apocope, $PlazaB, $NombreB, $Amount);
        break;
    case "Confirm":
    case "Process":
        $particularFields = "&FrAccount=".urlencode($FrAccount)."&Apocope=".urlencode($Apocope)."&PlazaB=".urlencode($PlazaB)."&Amount=".urlencode($Amount)."&ToAccount=".urlencode($ToAccount)."&NombreB=".urlencode($NombreB)."&DireccB=".urlencode($DireccB)."&LadaB=".urlencode($LadaB)."&Day=".urlencode($Day)."&TeleB=".urlencode($TeleB)."&Desc=".urlencode(preg_replace("/&/", "-", $Desc))."&RFC=".urlencode($RFC)."&RenInt=".urlencode($RenInt)."&Button=".urlencode($Button);  //HB AGL
        $tr->blog($qki,"Speua", $FrAccount, $Apocope, $PlazaB, $NombreB, $Amount);
        break;
    }
    $transaccion_buffer = "";
    // DebugBreak();
    $ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=spe&Access=".urlencode($Access)."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // SPEUA

    $t->set_var( "transaccion_buffer", $transaccion_buffer );
    $t->pparse( "output", "speua_tpl" );

}
else
{
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "userlogin.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "user_login_tpl" => "userlogin.tpl"
        ) );

    $t->set_var( "redirect_url", "/transaccion/speua/" );

    $t->pparse( "output", "user_login_tpl" );
}
?>