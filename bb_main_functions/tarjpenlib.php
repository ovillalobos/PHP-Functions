<?php
//
// Autorización de Archivos de Altas de tarjetahabientes, liberación de recursos  y Depósitos de  tarjetas de débito Grandes Corporativos
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

// NEX [20-jul-2012] T-211195 (ini) > Se agregan includes para nuevo esquema de nomina
include("eztransaccion/user/include/tcpipnexions.php");
include("eztransaccion/user/include/xmlparser.inc");
include("eztransaccion/user/include/general_functions.inc");
include("eztransaccion/user/include/controles_javascript.inc");
//include_once("eztransaccion/user/include/tarjpenlib.inc");
// NEX [20-jul-2012] T-211195 (fin) > Se agregan includes para nuevo esquema de nomina

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
                         "eztransaccion/user/intl/", $Language, "tarjpenlib.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "tarjpenlib_tpl" => "tarjpenlib.tpl"
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
		
	if(!empty($_POST['Accion']))
		$Accion = $_POST['Accion'];
		
	if(!empty($_POST['Agrupa']))
		$Agrupa = $_POST['Agrupa'];
		
	if(!empty($_POST['DiasPzo']))
		$DiasPzo = $_POST['DiasPzo'];
		
	if(!empty($_POST['TipoPzo']))
		$TipoPzo = $_POST['TipoPzo'];
		
	if(!empty($_POST['FrAccount']))
		$FrAccount = $_POST['FrAccount'];
		
	if(!empty($_POST['AgruPagIni']))
		$AgruPagIni = $_POST['AgruPagIni'];
		
	if(!empty($_POST['Parent1']))
		$Parent1 = $_POST['Parent1'];
		
	if(!empty($_POST['code']))
		$code = $_POST['code'];
//HB
    if(empty($Access)) {
        $Access = "FrAc";
    }
    $Accion  = "LibTjp";
	//echo "Valor de Access: ".$Access;
    switch($Access) {
    case "FrAc":
        if (isset($Access)) {
            setcookie ( "QueAccess", $Access, time() + ( 3600 * 24 * 365 ), "/",  "bb.com.mx", 0 ) or print( "Error: could not set cookie." );
        }
        if (isset($Accion)) {
            setcookie ( "QueAccion", $Accion, time() + ( 3600 * 24 * 365 ), "/",  "bb.com.mx", 0 ) or print( "Error: could not set cookie." );
        }
	//echo "<br>Valor de Agrupa: ".$Agrupa;
	 if ( $Agrupa=="Aceptar" )
	 {
		$Access = "FrAc";
		$Accion  = "LibTjp";
	        $DiasPzo = "";
	        $TipoPzo ="";  //LVPR 11jul2008
       	 $particularFields = "&Accion=".urlencode($Accion)."&FrAccount=".urlencode($FrAccount)."&AgruPagIni=".urlencode($AgruPagIni);
	        $tr->blog($qki,"LibTjp", $FrAccount, $ToAccount, $DiasPzo, $Parent1, $Accion);
       	 $ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=arc&Access=".urlencode($Access)."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // Archivos de Altas Empleados Nomina
	        $t->set_var( "transaccion_buffer", $transaccion_buffer );
       	 $t->pparse( "output", "tarjpenlib_tpl" );
	 }
	 else
	 {
	        $DiasPzo = "";
	        $TipoPzo =""; //LVPR 11jul2008
       	 $particularFields = "&Accion=".urlencode($Accion)."&FrAccount=".urlencode($FrAccount)."&AgruPagIni=".urlencode($AgruPagIni);
		 //echo "<br>ParticularFields (Agrupa == ' '): ".$particularFields;
	        $tr->blog($qki,"LibTjp", $FrAccount, $ToAccount, $DiasPzo, $Parent1, $Accion);
		 //echo "<br>PostToHost (Agrupa == ' '): "."( ".$backend.", "."/IBnkIIS.dll".", "."Trxn=arc&Access=".urlencode($Access)."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields.", ".$qki.", ".$usr.", ".$qki.", ".$priv.", ".$transaccion_buffer.")";
       	 $ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=arc&Access=".urlencode($Access)."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // Archivos de Altas Empleados Nomina
		 //echo "<br>Valor ret_code: ".$ret_code;
	        $t->set_var( "transaccion_buffer", $transaccion_buffer );
       	 $t->pparse( "output", "tarjpenlib_tpl" );
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
//LVPR I 11jul2008
		//echo "<br>Valor DiazPzo: ".$DiasPzo;
        list( $servicio, $Ciudad, $Empresa, $rowno, $nombre_esperado,$MismoMonto ) = explode("-", $DiasPzo);
        list( $TServicio, $TCiudad, $TEmpresa, $Trowno, $Tnombre_esperado,$TMismoMonto  ) = explode("-", $TipoPzo);
        if ( $MismoMonto > 0 )
        {
	        if ( $nombre_esperado != $Tnombre_esperado )
	        {
	        	$transaccion_buffer = "
				<form method='POST'>
					<input type='HIDDEN' name='Access' value='Process'>
						<TABLE WIDTH='80%'>
							<TR>
								<TD>
									<P><B>El importe del archivo seleccionado coindice con otro autorizado el día de hoy.<P>
									<P><b>Favor de seleccionar el archivo y la opción de autorización</B></P>
								</TD>
							</TR>
						</TABLE>

				</form>";
				$t->set_var( "transaccion_buffer", $transaccion_buffer );
				$t->pparse( "output", "tarjpenlib_tpl" );
			return;
	        }
	    }
//LVPR F  11jul2008
		//echo "<br>servicio: ".$servicio;
		switch( $servicio ) {
			   case "deposito" :
			   case "altaTarjeta" :
			   case "datosTarjetahabiente" :	
			   case "devolucionRecursos" :
					$servicio = "LibTjp";
					$Parent1  = "00";
					$mt = "mramirez@bb.com.mx,ablanco@bb.com.mx";
					break;
				default:
					$mt = "helpdesk@bb.com.mx";
					break;
		}

        $particularFields = "&Passwd=".encrypt( $code, strtolower( $usr ) )."&Accion=".urlencode($Accion)."&Parent1=".urlencode($Parent1)."&Parent2=".urlencode($Parent2)."&DiasPzo=".urlencode($DiasPzo)."&TipoPzo=".urlencode($TipoPzo);  //LVPR 11jul2008
        $tr->blog($qki,"ArchServ", $FrAccount, $ToAccount, $DiasPzo, $Parent1, $Accion);
		
		//echo "<br>ParticularFields (Agrupa == ' '): ".$particularFields;
		//echo "<br>PostToHost (Agrupa == ' '): "."( ".$backend.", "."/IBnkIIS.dll".", "."Trxn=arc&Access=".urlencode($Access)."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields.", ".$qki.", ".$usr.", ".$qki.", ".$priv.", ".$transaccion_buffer.")";
		$ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=arc&Access=".urlencode($Access)."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // intercambio de archivos de tarjetas prepago grandes corporativos
        $t->set_var( "transaccion_buffer", $transaccion_buffer );
        $t->pparse( "output", "LibNom_tpl" );
		//echo "<br>Valor ret_code: ".$ret_code;
		
		$t->set_var( "transaccion_buffer", $transaccion_buffer );
		$t->pparse( "output", "tarjpenlib_tpl" );
		
        break;
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

    $t->set_var( "redirect_url", "/transaccion/tarjpenlib/" );
    $t->pparse( "output", "user_login_tpl" );
 }

?>
