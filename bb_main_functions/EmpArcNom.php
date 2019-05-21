<?php
//
// Envío de Archivos de Altas de Empleados de Nómina Electrónica
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
                         "eztransaccion/user/intl/", $Language, "EmpArcNom.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "EmpArcNom_tpl" => "EmpArcNom.tpl"
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
    $particularFields = "";
//HB
	 if(!empty($_POST['Accion']))
        $Accion = $_POST['Accion'];
//HB
    if(empty($_POST['Access'])) {
        $_POST['Access'] = "FrAc";
    }
    $Accion  = "EmpNom";
	
    switch($_POST['Access']) {
    case "FrAc":
        if (isset($_POST['Access'])) {
            setcookie ( "QueAccess", $_POST['Access'], time() + ( 3600 * 24 * 365 ), "/",  "bb.com.mx", 0 ) or print( "Error: could not set cookie." );
        }
        if (isset($Accion)) {
            setcookie ( "QueAccion", $Accion, time() + ( 3600 * 24 * 365 ), "/",  "bb.com.mx", 0 ) or print( "Error: could not set cookie." );
        }

	 if ( $_POST['Agrupa']=="Aceptar" )
	 {
		$_POST['Access'] = "FrAc";
		$Accion  = "EmpNom";
        	$_POST['DiasPzo'] = "";
        	$particularFields = "&Accion=".urlencode($Accion)."&FrAccount=".urlencode($_POST['FrAccount'])."&AgruPagIni=".urlencode($_POST['AgruPagIni']);
        	$tr->blog($qki,"EmpNom", $_POST['FrAccount'], $_POST['ToAccount'], $_POST['DiasPzo'], $_POST['Parent1'], $Accion);
        	$ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=arc&Access=".urlencode($_POST['Access'])."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // Archivos de Altas Empleados Nomina
        	$t->set_var( "transaccion_buffer", $transaccion_buffer );
        	$t->pparse( "output", "EmpArcNom_tpl" );
	 }
	 else
	 {
        	$_POST['DiasPzo'] = "";
        	$particularFields = "&Accion=".urlencode($Accion)."&FrAccount=".urlencode($_POST['FrAccount'])."&AgruPagIni=".urlencode($_POST['AgruPagIni']);
        	$tr->blog($qki,"EmpNom", $_POST['FrAccount'], $_POST['ToAccount'], $_POST['DiasPzo'], $_POST['Parent1'], $Accion);
        	$ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=arc&Access=".urlencode($_POST['Access'])."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // Archivos de Altas Empleados Nomina
        	$t->set_var( "transaccion_buffer", $transaccion_buffer );
        	$t->pparse( "output", "EmpArcNom_tpl" );
	 }



        break;
    case "Confirm":
    case "Process":
        if (isset($_POST['Access'])) {
            setcookie ( "QueAccess", $_POST['Access'], time() + ( 3600 * 24 * 365 ), "/",  "bb.com.mx", 0 ) or print( "Error: could not set cookie." );
        }
        if (isset($Accion)) {
            setcookie ( "QueAccion", $Accion, time() + ( 3600 * 24 * 365 ), "/",  "bb.com.mx", 0 ) or print( "Error: could not set cookie." );
        }
        //DGM Tokens, pide token para archivos de altas de nomina

		$particularFields = "&Passwd=".encrypt( $_POST['code'], strtolower( $usr ) )."&Accion=".urlencode($Accion)."&DiasPzo=".urlencode("");
		$ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=arc&Access=".urlencode($_POST['Access'])."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // Archivos de Altas Empleados Nomina

		if ( preg_match( "/Incorrecta/i", $transaccion_buffer, $regs ) ||   /* HB AGL - eregi */
			 preg_match( "/7105/i", $transaccion_buffer, $regs )	) 	//Cve Operacional/Token incorrecto  (distinto a 10 digitos)   /* HB AGL - eregi */
		{
			$t->set_var( "transaccion_buffer", $transaccion_buffer );
			$t->pparse( "output", "EmpArcNom_tpl" );
		}
		else
		{
			include( "eztransaccion/user/myfileupload.php" );
		}

        //DGM Tokens, pide token para archivos de altas de nomina
        break;
    }
} else {
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "userlogin.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "user_login_tpl" => "userlogin.tpl"
        ) );

    $t->set_var( "redirect_url", "/transaccion/EmpArcNom/" );
    $t->pparse( "output", "user_login_tpl" );
}

?>

