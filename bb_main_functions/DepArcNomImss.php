<?php
//
// Envío de Archivos de Depósitos de Nómina Electrónica
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
$ServerNumber = $ini->read_var( "site", "ServerNumber" );
$DomainPostfix = $ini->read_var( "site", "DomainPostfix" );

$backend = "gw" . $ServerNumber. $DomainPostfix;


include_once( "ezuserbb/classes/ezuser.php" );

$user =& eZUserBB::currentUser();

// DebugBreak();

if ( $user )
{
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "DepArcNomImss.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "DepArcNomImss_tpl" => "DepArcNomImss.tpl"
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
    $Accion  = "DepNomIm";
    switch($_POST['Access']) {
    case "FrAc":
        if (isset($_POST['Access'])) {
            setcookie ( "QueAccess", $_POST['Access'], time() + ( 3600 * 24 * 365 ), "/",  "bb.com.mx", 0 ) or print( "Error: could not set cookie." );
        }
        if (isset($Accion)) {
            setcookie ( "QueAccion", $Accion, time() + ( 3600 * 24 * 365 ), "/",  "bb.com.mx", 0 ) or print( "Error: could not set cookie." );
        }
	 //echo "Agrupa: ".$_POST['Agrupa']; // kitar
	 if ( $_POST['Agrupa']=="Aceptar" )
	 {
		$_POST['Access'] = "FrAc";
		$Accion  = "DepNomIm";
        	$_POST['DiasPzo'] = "";
        	$particularFields = "&Accion=".urlencode($Accion)."&FrAccount=".urlencode($_POST['FrAccount'])."&Button=".urlencode($_POST['Button'])."&AgruPagIni=".urlencode($_POST['AgruPagIni']);
        	$tr->blog($qki,"DepNomIm", $_POST['FrAccount'], $_POST['ToAccount'], $_POST['DiasPzo'], $_POST['Parent1'], $Accion);
        	$ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=arc&Access=".urlencode($_POST['Access'])."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // Archivos de Depósitos Nomina
	 }
	 else
	 {
        	$_POST['DiasPzo'] = "";
        	$particularFields = "&Accion=".urlencode($Accion)."&FrAccount=".urlencode($_POST['FrAccount'])."&Button=".urlencode($_POST['Button'])."&AgruPagIni=".urlencode($_POST['AgruPagIni']);
        	$tr->blog($qki,"DepNomIm", $_POST['FrAccount'], $_POST['ToAccount'], $_POST['DiasPzo'], $_POST['Parent1'], $Accion);
        	$ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=arc&Access=".urlencode($_POST['Access'])."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // Archivos de Depósitos Nomina
	 }



        // ********************************************************
        // aqui reemplazar que no muestre la solicitud de clave

        // Quitar para alta de depósito de nominas la lista   /*HB AGL  Inicio*/
        $transaccion_buffer = preg_replace("#<LI>Es necesario capturar su#", "<!--<LI>Es necesario capturar su", $transaccion_buffer);
        $transaccion_buffer = preg_replace("#para completar la transacci&oacute;n.</LI>#", "para completar la transacci&oacute;n.</LI> -->", $transaccion_buffer);

        $transaccion_buffer = preg_replace("#<TABLE BORDER=1 ALIGN = CENTER WIDTH=500 HEIGHT=33><TR><TD ALIGN=\"CENTER\"><B>Autorice#","<!-- <TABLE BORDER=1 ALIGN = CENTER WIDTH=500 HEIGHT=33><TR><TD ALIGN=\"CENTER\"><B>Autorice",$transaccion_buffer);
        $transaccion_buffer = preg_replace("#TYPE=\"PASSWORD\" NAME=\"code\" SIZE=\"12\" MAXLENGTH=\"10\"></TD></TR></TABLE>#","TYPE=\"PASSWORD\" NAME=\"code\" SIZE=\"12\" MAXLENGTH=\"10\"></TD></TR></TABLE>-->",$transaccion_buffer);
        //Quitar para alta de depósito de nominas la tabla de solicitud de password

        // aqui reemplazar que no muestre la solicitud de clave
        // ********************************************************

        $t->set_var( "transaccion_buffer", $transaccion_buffer );
        $t->pparse( "output", "DepArcNomImss_tpl" );
        break;
    case "Confirm":
    case "Process":
        if (isset($_POST['Access'])) {
            setcookie ( "QueAccess", $_POST['Access'], time() + ( 3600 * 24 * 365 ), "/",  "bb.com.mx", 0 ) or print( "Error: could not set cookie." );
        }
        if (isset($Accion)) {
            setcookie ( "QueAccion", $Accion, time() + ( 3600 * 24 * 365 ), "/",  "bb.com.mx", 0 ) or print( "Error: could not set cookie." );
        }
        include( "eztransaccion/user/myfileupload.php" );
        break;
    }
} else {
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "userlogin.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "user_login_tpl" => "userlogin.tpl"
        ) );

    $t->set_var( "redirect_url", "/transaccion/DepArcNomImss/" );
    $t->pparse( "output", "user_login_tpl" );
}

?>

