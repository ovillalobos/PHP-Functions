<?php
//
// Autorización de Archivos de Altas de Empleados y Depósitos de Nómina Electrónica
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

// EFR [31-ene-2013] (ini) > Grupo de Nomina Alfanumerico Nuevo Esquema
// NEX [20-jul-2012] T-211195 (ini) > Se agregan includes para nuevo esquema de nomina
//include("eztransaccion/user/include/tcpipnexions.php");
//include("eztransaccion/user/include/xmlparser.inc");
//include("eztransaccion/user/include/general_functions.inc");
// NEX [20-jul-2012] T-211195 (fin) > Se agregan includes para nuevo esquema de nomina

//include_once("eztransaccion/user/include/LibNom.inc");
include_once("eztransaccion/user/include/utilerias_ne.inc");
// EFR [31-ene-2013] (fin) > Grupo de Nomina Alfanumerico Nuevo Esquema

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
                         "eztransaccion/user/intl/", $Language, "LibNom.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "LibNom_tpl" => "LibNom.tpl"
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
		
	if(!empty($_POST['TipoPzo']))
		$TipoPzo = $_POST['TipoPzo'];
//HB
    if(empty($_POST['Access'])) {
        $_POST['Access'] = "FrAc";
    }
    $Accion  = "LibNom";
	
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
		$Accion  = "LibNom";
	        $_POST['DiasPzo'] = "";
	        $TipoPzo ="";  //LVPR 11jul2008
       	 $particularFields = "&Accion=".urlencode($Accion)."&FrAccount=".urlencode($_POST['FrAccount'])."&AgruPagIni=".urlencode($_POST['AgruPagIni']);
	        $tr->blog($qki,"LibNom", $_POST['FrAccount'], $_POST['ToAccount'], $_POST['DiasPzo'], $_POST['Parent1'], $Accion);
       	 $ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=arc&Access=".urlencode($_POST['Access'])."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // Archivos de Altas Empleados Nomina
	        $t->set_var( "transaccion_buffer", $transaccion_buffer );
       	 $t->pparse( "output", "LibNom_tpl" );
	 }
	 else
	 {
	        $_POST['DiasPzo'] = "";
	        $TipoPzo =""; //LVPR 11jul2008
       	 $particularFields = "&Accion=".urlencode($Accion)."&FrAccount=".urlencode($_POST['FrAccount'])."&AgruPagIni=".urlencode($_POST['AgruPagIni']);
	        $tr->blog($qki,"LibNom", $_POST['FrAccount'], $_POST['ToAccount'], $_POST['DiasPzo'], $_POST['Parent1'], $Accion);
       	 $ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=arc&Access=".urlencode($_POST['Access'])."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // Archivos de Altas Empleados Nomina
	        $t->set_var( "transaccion_buffer", $transaccion_buffer );
       	 $t->pparse( "output", "LibNom_tpl" );
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
        //include( "eztransaccion/user/myfileupload.php" );
//LVPR I 11jul2008
        list( $servicio, $Ciudad, $_POST['Empresa'], $rowno, $nombre_esperado,$MismoMonto ) = explode("-", $_POST['DiasPzo']);
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
				$t->pparse( "output", "LibNom_tpl" );
			return;
	        }
	    }
//LVPR F  11jul2008
		switch( $servicio ) {
			   case "nomina1" :
			   case "nomina2" :
			   case "nomina3" :	// IRG09052011 - Nómina IMSS
					$servicio = "nomina";
					$_POST['Parent1']  = "00";
					//$result = valida_pago_proveedores_mismo_banco( $uploadedFile, $nombre_esperado );
					//$uploadedFile->OriginalFileName = $nombre_esperado;
					$mt = "mramirez@bb.com.mx,ablanco@bb.com.mx";
					break;
				default:
					$mt = "helpdesk@bb.com.mx";
					break;
		}

        $particularFields = "&Passwd=".encrypt( $_POST['code'], strtolower( $usr ) )."&Accion=".urlencode($Accion)."&Parent1=".urlencode($_POST['Parent1'])."&Parent2=".urlencode($Parent2)."&DiasPzo=".urlencode($_POST['DiasPzo'])."&TipoPzo=".urlencode($TipoPzo);  //LVPR 11jul2008
        $tr->blog($qki,"ArchServ", $_POST['FrAccount'], $_POST['ToAccount'], $_POST['DiasPzo'], $_POST['Parent1'], $Accion);
        
        // NEX [20-jul-2012] T-211195 (ini) > Se valida nuevo esquema de nomina habilitado
        if (nuevoEsquemaHabilitado()){
		// EFR [31-ene-2013] (ini) > Grupo de Nomina Alfanumerico Nuevo Esquema
        	$tagRequest = array();
			$tagRequest['Trxn'] = 'arc';
			$tagRequest['Access'] = $_POST['Access'];
			$tagRequest['Accion'] = $Accion;
			$tagRequest['TipoMsj'] = 'rqs';
			$tagRequest['CustID'] = $usr;
			// EFR [11-mar-2013] (ini) > Correccion Token autenticacion  
			$tagRequest['HomeBankID'] = $qki;
			// EFR [11-mar-2013] (fin) > Correccion Token autenticacion 
			$tagRequest['SessionCookie'] = $session->hash();
			$tagRequest['ServiceString'] = $_POST['DiasPzo'];
			$tagRequest['Token'] = $_POST['code'];
			$tagRequest['Password'] = encrypt($_POST['code'], strtolower($usr));
			$tagRequest['Privilegio'] = $priv;

        	include_once("eztransaccion/user/include/estilo.inc");
        	include_once("eztransaccion/user/include/autoriza_archivo_ne.inc");
        	$xml_struct = autorizar_archivo($tagRequest);

        	include_once("eztransaccion/user/include/html_response_builder_ne.inc");
        	$transaccion_buffer = getHTMLResponseFromWSLibNom($xml_struct, $Accion);
        	// EFR [31-ene-2013] (fin) > Grupo de Nomina Alfanumerico Nuevo Esquema

        	muestraErrorDetallado($transaccion_buffer);
        } else {
        	$ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=arc&Access=".urlencode($_POST['Access'])."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // intercambio de archivos de nomina electronica
        	$t->set_var( "transaccion_buffer", $transaccion_buffer );
        	$t->pparse( "output", "LibNom_tpl" );
        }
        // NEX [20-jul-2012] T-211195 (fin) > Se valida nuevo esquema de nomina habilitado
        break;
    }
} else {
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "userlogin.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "user_login_tpl" => "userlogin.tpl"
        ) );

    $t->set_var( "redirect_url", "/transaccion/LibNom/" );
    $t->pparse( "output", "user_login_tpl" );
}

?>

