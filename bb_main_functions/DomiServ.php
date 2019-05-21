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
include_once("include/controles_javascript.inc"); // RAI Validando token para domiciliación

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
                         "eztransaccion/user/intl/", $Language, "DomiServ.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "domiserv_tpl" => "DomiServ.tpl"
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

    // if ( $url_array[3] == "insert" ) {
    //     $_POST['Access'] = "Confirm";
    //    $Action = "insert";
    // }

	//NXN-118013, Incorporacion de agrupacion de cuentas I
    // if(empty($_POST['Access'])) {
        // $_POST['Access'] = "FrAc";
    // }
   
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
//HB   
    
	$ConsAgrp = false;
	
    if(empty($_POST['Access'])) {
        $_POST['Access'] = "FrAc";
		$ConsAgrp = true;
    }
	//NXN-118013, Incorporacion de agrupacion de cuentas F
    $Accion  = "DomiServ";
    switch($_POST['Access']) {
    case "FrAc":
		$session->setVariable( "tkn_valido", "" ); // RAI control de validación de token del lado del webserver
        if (isset($_POST['Access'])) {
            setcookie ( "QueAccess", $_POST['Access'], time() + ( 3600 * 24 * 365 ), "/",  "bb.com.mx", 0 ) or print( "Error: could not set cookie." );
        }
        if (isset($Accion)) {
            setcookie ( "QueAccion", $Accion, time() + ( 3600 * 24 * 365 ), "/",  "bb.com.mx", 0 ) or print( "Error: could not set cookie." );
        }
        $DiasPzo = "";
		//NXN-118013, Incorporacion de agrupacion de cuentas I
		if ($ConsAgrp)
		{
			$particularFields = "&Accion=".urlencode($Accion)."&Filtro=ConsAgr";;
			//$particularFields = "&Accion=".urlencode($Accion);
		}
		else
		{
			$particularFields = "&Accion=".urlencode($Accion)."&gene5=".urlencode($_POST['FrAccount']);
			
		}
		//NXN-118013, Incorporacion de agrupacion de cuentas F
        $tr->blog($qki,"DomiServ", $_POST['FrAccount'], $ToAccount, $DiasPzo, $Parent1, $Accion);
        $ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=arc&Access=".urlencode($_POST['Access'])."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // intercambio de archivos de Domiciliacion
		$find_fraccount = strpos($transaccion_buffer,"Seleccione el Cliente:");
		if ($find_fraccount === false)
		{
			$find_fraccount = "no";
		}
		else
		{
			$find_fraccount = "si";
		}
		
		// RAI 27Sep2010 Llave ASB para Domiciliación - Inicio	
		if ($session->variable( "r_tknOp" ) == "si" and $find_fraccount == "no")
		{?>
			<script>
				$(document).ready(function (){
					$('#token_value').focus()
					$('form').bind('submit', function() {
						try
						{
							if ($("#bandera").val() != "envia")
							{
								return false;
							}
						}
						catch (err)
						{
							return false;
						}

					}
					)
				});
			</script>
		<?php

			$secure_site = $ini->read_var( "site", "SecureServer" ) . $ini->read_var( "site", "ServerNumber" ) . $ini->read_var( "site", "DomainPostfix" );

			$cliente_token="
							
						<input type='hidden' id='nomFunc' name='nomFunc' value='clienteasb'>
						<input type='hidden' id='btn_opcion' name='btn_opcion'>
						<table width=98% align=center border=0 class=form_tbl>
							<tr>
								<td colspan=3 align=center>
									<table border=1 width=60% height=60px>
										<tr>
											<td colspan=2 border=1><b> Es necesario capturar su Clave ASB (Acceso Seguro Bajio)</b></td>
										</tr>
										<tr>
											<td colspan=2 align=center > 
												<b>Clave ASB (Acceso Seguro Bajio):</b> 
												<input type=password name=token id=token_value class=inputbox  size=15 maxlength=10 onKeyPress=\"if (token_valido(event,this) == true){ if (is_Enter(event) == true){validaTknUser('DomiServ')}}else{return false}\" ></td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td>
								</td>
								<td align=center>
									<input type=submit name=btn_opcion id=btn_opcionA value=Aceptar class=button onmouseover=\"this.className='buttonON'\" onmouseout=\"this.className='button'\" onclick=\"if (validar_token('token_value','btn_opcionA')) { validaTknUser('DomiServ');}else{return false}\" > 
									<input type=button name=btn_opcion id=btn_opcionC value=Cancelar class=button onmouseover=\"this.className='buttonON'\" onmouseout=\"this.className='button'\" onclick=\"document.location.href='https://$secure_site/article/articleview/196/1/7/'\" > 
								</td>
								<td align=left></td>
								<td></td>
							</tr>
						</table>
				";

			$transaccion_buffer=str_replace("<INPUT TYPE=SUBMIT NAME=\"Button\" VALUE=\"Aceptar\">",$cliente_token,$transaccion_buffer);

			//Se Agrega instricción para el token
			/*
			$instruccion="<LI>Es necesario capturar su <b>Clave ASB (Acceso Seguro Bajio)</b>, la cual est&aacute; formada por 10 d&iacute;gitos (4 d&iacute;gitos de su <b>NIP</b> actual m&aacute;s 6 d&iacute;gitos de su <b>Clave Din&aacute;mica</b>), para completar la transacci&oacute;n.<LI>Presione bot&oacute;n <EM>Aceptar</EM>";
			$transaccion_buffer=str_replace("<LI>Presione bot&oacute;n <EM>Aceptar</EM>",$instruccion, $transaccion_buffer);
			$transaccion_buffer=str_replace("<li>Presione el bot&oacute;n <em>Aceptar</em>",$instruccion, $transaccion_buffer);*/
		}
		// RAI 27Sep2010 Llave ASB para Domiciliación - Fin			

		
        $t->set_var( "transaccion_buffer", $transaccion_buffer );
        $t->pparse( "output", "domiserv_tpl" );
		$session->setVariable( "primer_FrAc", "no" ); // RAI control de validación de token, regulando FrAc 1 y 2

        break;
    case "Confirm":
    case "Process":
        if (isset($_POST['Access'])) {
            setcookie ( "QueAccess", $_POST['Access'], time() + ( 3600 * 24 * 365 ), "/",  "bb.com.mx", 0 ) or print( "Error: could not set cookie." );
        }
        if (isset($Accion)) {
            setcookie ( "QueAccion", $Accion, time() + ( 3600 * 24 * 365 ), "/",  "bb.com.mx", 0 ) or print( "Error: could not set cookie." );
        }
		// RAI control de validación de token del lado del webserver
		$tkn_valido = $session->variable( "tkn_valido" );
		if($tkn_valido == "si")
		{
			include( "eztransaccion/user/myfileupload.php" );
		}
		else
		{
			$transaccion_buffer = "<p>Token no v&aacute;lido: Favor de repetir la operaci&oacute;n</p>";
			$t->set_var( "transaccion_buffer", $transaccion_buffer );
	        $t->pparse( "output", "domiserv_tpl" );
		} 
        break;
    }
} else {
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "userlogin.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "user_login_tpl" => "userlogin.tpl"
        ) );

    $t->set_var( "redirect_url", "/transaccion/DomiServ/" );
    $t->pparse( "output", "user_login_tpl" );
}

?>

