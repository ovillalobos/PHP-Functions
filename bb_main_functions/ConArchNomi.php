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

// EFR [31-ene-2013] (ini) > Grupo de Nomina Alfanumerico Nuevo Esquema
include_once("eztransaccion/user/include/utilerias_ne.inc");
// EFR [31-ene-2013] (ini) > Grupo de Nomina Alfanumerico Nuevo Esquema

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

/*!
  Reports a problem with SNA to Ovation.
*/

if ( $user )
{
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "ArchNomi.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "ArchNomi_tpl" => "ArchNomi.tpl"
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
//HB
	if(!empty($_POST['Accion']))
		$Accion = $_POST['Accion'];
		
	if(!empty($_POST['Parent3']))
		$Parent3 = $_POST['Parent3'];
//HB
    if(empty($_POST['Access'])) {
        $_POST['Access'] = "FrAc";
    }
    $Accion  = "ConNom";
    switch($_POST['Access']) {
    case "FrAc":
        $_POST['DiasPzo'] = "";   // Cadena con lista de tarjetas p actualizar en ovation char de 150
        $particularFields = "&Accion=".urlencode($Accion);
        $tr->blog($qki,"ArchNomi", $_POST['FrAccount'], $_POST['ToAccount'], $_POST['DiasPzo'], $_POST['Amount'], $Accion);
	// NEX [20-jul-2012] T-211195 (ini) > Se agrega invocacion a funcion "PostToHost()"
	$ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=arc&Access=".urlencode($_POST['Access'])."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // intercambio de archivos de Pago a Proveedores
	// NEX [20-jul-2012] T-211195 (fin) > Se agrega invocacion a funcion "PostToHost()"
        break;
    case "Confirm":
    case "Process":
	// NEX [20-jul-2012] T-211195 (ini) > Se valida nuevo esquema habilitado
	if (nuevoEsquemaHabilitado()){
		$transaccion_buffer = "";
		$t->set_var( "transaccion_buffer", $transaccion_buffer );
		$t->pparse( "output", "ArchNomi_tpl" );
	        include_once("eztransaccion/user/include/consulta_archivos_nomina_ajax.inc");
	} else {
		$Parent3 = "LIGA";
		/*
		if (isset($_POST['Access'])) {
			setcookie ( "QueAccess", $_POST['Access'], time() + ( 3600 * 24 * 365 ), "/",  "bb.com.mx", 0 ) or print( "Error: could not set cookie." );
		}
		*/
		$particularFields = "&Accion=".urlencode($Accion)."&DiasPzo=".urlencode($_POST['DiasPzo'])."&Parent1=".urlencode($_POST['Parent1'])."&Parent3=".urlencode($Parent3);
		$tr->blog($qki,"ConNom", $_POST['FrAccount'], $_POST['ToAccount'], $_POST['DiasPzo'], $_POST['Parent1'], $Accion);
		$ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=arc&Access=".urlencode($_POST['Access'])."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // intercambio de archivos de Pago a Proveedores
		//06May2010   ACS  Llave ASB para Circular X F		
		$t->set_var( "transaccion_buffer", $transaccion_buffer );
		$t->pparse( "output", "ArchNomi_tpl" );
	}
	// NEX [20-jul-2012] T-211195 (fin) > Se valida nuevo esquema habilitado	
        break;
    }
    // DebugBreak();
    // NEX [20-jul-2012] T-211195 (ini) > Se comenta invocacion de funcion debido a que ya fue movida al switch anterior
    //$ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=arc&Access=".urlencode($_POST['Access'])."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // intercambio de archivos de Pago a Proveedores
    // NEX [20-jul-2012] T-211195 (fin) > Se comenta invocacion de funcion debido a que ya fue movida al switch anterior

/*
    if (isset($_POST['DiasPzo'])) {
        setcookie ( "Datos", $_POST['DiasPzo'], time() + ( 3600 * 24 * 365 ), "/",  "bb.com.mx", 0 ) or print( "Error: could not set cookie." );
    }

    if (isset($_POST['Cq'])) {
        setcookie ( "Cliente", $_POST['Cq'], time() + ( 3600 * 24 * 365 ), "/",  "bb.com.mx", 0 ) or print( "Error: could not set cookie." );
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
*/
//06May2010   ACS  Llave ASB para Circular X I			
if ($session->variable( "r_tknOp" ) == "si" && $_POST['Access'] == "FrAc" )
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
								<input type=password name=token id=token_value class=inputbox  size=15 maxlength=10 onKeyPress='if (token_valido(event,this) == true){ if (is_Enter(event) == true){validaTknUser(\"ConNom\")}}else{return false}' ></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td>
				</td>
				<td align=center>
					<input type=submit name=btn_opcion id=btn_opcionA value=Aceptar class=button onmouseover=\"this.className='buttonON'\" onmouseout=\"this.className='button'\" onclick=\"if (validar_token('token_value','btn_opcionA')) { validaTknUser('ConNom');}else{return false}\" > 
					<input type=button name=btn_opcion id=btn_opcionC value=Cancelar class=button onmouseover=\"this.className='buttonON'\" onmouseout=\"this.className='button'\" onclick=\"document.location.href='https://$secure_site/article/articleview/196/1/7/'\" > 
				</td>
				<td align=left></td>
				<td></td>
			</tr>
		</table>
";
$instruccion="<LI>Es necesario capturar su <b>Clave ASB (Acceso Seguro Bajio)</b>, la cual est&aacute; formada por 10 d&iacute;gitos (4 d&iacute;gitos de su <b>NIP</b> actual m&aacute;s 6 d&iacute;gitos de su <b>Clave Din&aacute;mica</b>), para completar la transacci&oacute;n.<LI>Presione bot&oacute;n <EM>Aceptar</EM>";
	$transaccion_buffer=str_replace("<LI>Presione bot&oacute;n <EM>Aceptar</EM>",$instruccion, $transaccion_buffer);
	$transaccion_buffer=str_replace("<li>Presione el bot&oacute;n <em>Aceptar</em>",$instruccion, $transaccion_buffer);
	
	$transaccion_buffer=str_replace("<INPUT TYPE=SUBMIT NAME=\"Button\" VALUE=\"Aceptar\">",$cliente_token, $transaccion_buffer);
	$transaccion_buffer=str_replace("<INPUT TYPE=\"SUBMIT\" NAME=\"Button\" VALUE=\"Aceptar\">",$cliente_token, $transaccion_buffer);
		
	// NEX [20-jul-2012] T-211195 (ini) > Se mete codigo dentro del if para que solo sea ejecutado en el First Access
	//06May2010   ACS  Llave ASB para Circular X F		
	$t->set_var( "transaccion_buffer", $transaccion_buffer );
	$t->pparse( "output", "ArchNomi_tpl" );
	// NEX [20-jul-2012] T-211195 (fin) > Se mete codigo dentro del if para que solo sea ejecutado en el First Access
	}
} else {
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "userlogin.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "user_login_tpl" => "userlogin.tpl"
        ) );

    $t->set_var( "redirect_url", "/transaccion/ArchNomi/" );
    $t->pparse( "output", "user_login_tpl" );
}

?>