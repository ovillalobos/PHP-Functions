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
                         "eztransaccion/user/intl/", $Language, "CatalogoDeCuentas.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "CatalogoDeCuentas_tpl" => "CatalogoDeCuentas.tpl"
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
	
	if(!empty($_POST['DiasPzo']))
		$DiasPzo = $_POST['DiasPzo'];
		
	if(!empty($_POST['Accion']))
		$Accion = $_POST['Accion'];
		
	if(!empty($_POST['FrAccount']))
		$FrAccount = $_POST['FrAccount'];
		
	if(!empty($_POST['ToAccount']))
		$ToAccount = $_POST['ToAccount'];
		
	if(!empty($_POST['Cuenta']))
		$Cuenta = $_POST['Cuenta'];
		
	if(!empty($_POST['Cust']))
		$CustNo = $_POST['Cust'];
		
	if(!empty($_POST['Sub']))
		$Sub = $_POST['Sub'];
		
	if(!empty($_POST['Pos']))
		$Pos = $_POST['Pos'];
		
	if(!empty($_POST['chkbox0']))
		$chkbox0 = $_POST['chkbox0'];
		
	if(!empty($_POST['chkbox1']))
		$chkbox1 = $_POST['chkbox1'];
		
	if(!empty($_POST['chkbox2']))
		$chkbox2 = $_POST['chkbox2'];
		
	if(!empty($_POST['chkbox3']))
		$chkbox3 = $_POST['chkbox3'];
		
	if(!empty($_POST['chkbox4']))
		$chkbox4 = $_POST['chkbox4'];
		
	if(!empty($_POST['chkbox5']))
		$chkbox5 = $_POST['chkbox5'];
		
	if(!empty($_POST['chkbox6']))
		$chkbox6 = $_POST['chkbox6'];
		
	if(!empty($_POST['chkbox7']))
		$chkbox7 = $_POST['chkbox7'];
		
	if(!empty($_POST['chkbox8']))
		$chkbox8 = $_POST['chkbox8'];
		
	if(!empty($_POST['chkbox9']))
		$chkbox9 = $_POST['chkbox9'];
		
	if(!empty($_POST['Nomben2']))
		$Nomben2 = $_POST['Nomben2'];
		
	if(!empty($_POST['Nomben3']))
		$Nomben3 = $_POST['Nomben3'];
//HB    
    
    if(empty($_POST['Access'])) {
        $_POST['Access'] = "FrAc";
    }
    switch($_POST['Access']) {
    case "FrAc":
        $particularFields = "";
        $_POST['Accion'] = "Listacta";
        $particularFields = "&Cust=".urlencode($Cust)."&Cuenta=".urlencode($Cuenta)."&Sub=".urlencode($Sub)."&Accion=".urlencode($_POST['Accion'])."&UbNo=".urlencode($_POST['UbNo'])."&Pos=".urlencode($Pos);
        $tr->blog($qki,"Catalogo de Cuentas", $FrAccount, $Cust, $Cuenta, $Sub, $_POST['Accion']);
        break;
    case "Confirm":
    case "Process":
        if($_POST['Listado']=="Consultas") {
            $_POST['Accion'] = "Listacta";
        }
        else if($_POST['Newcta']=="Altas") {
            $_POST['Accion'] = "Newcta";
        }
        else if($_POST['Editcta'] == "Cambios"){
        	$_POST['Accion'] = "Editcta";
        }
        else if($_POST['MenuCan']=="Bajas") {
            $_POST['Accion'] = "MenuCan";
            $DiasPzo = "";
            $DiasPzo = "a".urlencode($chkbox0)."a".urlencode($chkbox1)."a".urlencode($chkbox2)."a".
            		   urlencode($chkbox3)."a".urlencode($chkbox4)."a".urlencode($chkbox5)."a".
            		   urlencode($chkbox6)."a".urlencode($chkbox7)."a".urlencode($chkbox8)."a".
            		   urlencode($chkbox9);
        }
        else if($_POST['Listado']=="Cancelar") {
            $_POST['Accion'] = "Listacta";
        }
        else if($_POST['Button'] == "Alta Beneficiario"){
        	$_POST['Accion'] = "MenuAlt";
        }
        else if($_POST['Button'] == "Actualizar Beneficiario"){
        	$_POST['Accion'] = "MenuEdit";
        }
        $particularFields = "&Passwd=".encrypt( $_POST['code'], strtolower( $usr ) )."&Cust=".urlencode($Cust)."&Apocope=".urlencode($_POST['Apocope']).
        "&UbNo=".urlencode($_POST['UbNo'])."&RFC=".urlencode($_POST['RFC'])."&PlazaB=".urlencode($_POST['PlazaB']).
        "&Accion=".urlencode($_POST['Accion'])."&DiasPzo=".urlencode($DiasPzo)."&NombreB=".urlencode($_POST['NombreB']).
        "&RenCap=".urlencode($_POST['RenCap'])."&Nomben2=".urlencode($Nomben2).
        "&RenInt=".urlencode($_POST['RenInt'])."&Nomben3=".urlencode($Nomben3).
        "&DireccB=".urlencode($_POST["DireccB"])."&LadaB=".urlencode($_POST["LadaB"]).
        "&ImpTipo=".urlencode($_POST['ImpTipo'])."&PAN=".urlencode($_POST['PAN']).
        "&Empresa=".urlencode($_POST['Empresa'])."&NumCheq=".urlencode($_POST['NumCheq']).
        "&EspecCh=".urlencode($_POST['EspecCh'])."&Talonar=".urlencode($_POST['Talonar']).
        "&Editar=".urlencode($_POST['Editar']).
        "&TeleB=".urlencode($_POST['TeleB'])."&Pos=".urlencode($Pos)."&Button=".urlencode($_POST['Button']);
        $tr->blog($qki,"Catalogo de Cuentas", $FrAccount, $Cust, $Cuenta, $Sub, $_POST['Accion']);
        break;
    }
	
    $transaccion_buffer = "";
    // DebugBreak();
    $ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=cta&Access=".urlencode($_POST['Access'])."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // ligas de cuentas de terceros
	//MAOS Nov2013 Quitar Scrolls I
	//Sin datos fiscales
	$transaccion_buffer = str_replace("</TR><TR><TD ><INPUT TYPE=\"TEXT\" NAME=\"DireccB\" SIZE=\"20\" MAXVALUE=\"60\"></TD></TR>", "SINDATFIS", $transaccion_buffer);
	$transaccion_buffer = str_replace("SINDATFIS", "<TH>&nbsp;</TH><TR><TD><INPUT TYPE=\"TEXT\" NAME=\"UbNo\" SIZE=\"8\" MAXLENGTH=\"8\" onKeypress=\"alfanumerico(this);\" ></TD><TD><INPUT TYPE=\"TEXT\" NAME=\"NombreB\" SIZE=\"20\" MAXLENGTH=\"40\" onKeypress=\"alfanumerico(this);\"></TD></TR>SIN1DATFIS", $transaccion_buffer);
	$transaccion_buffer = str_replace("SIN1DATFIS", "<TH>Direcci&oacute;n</TH><TH>&nbsp;</TH><TH>LADA &nbsp;&nbsp; Tel&eacute;fono </TH>", $transaccion_buffer);
	//Con datos fiscales
	$transaccion_buffer = str_replace("<TH>RFC del Beneficiario</TH></TR><TR><TD><INPUT TYPE=\"TEXT\" NAME=\"DireccB\" SIZE=\"20\" MAXVALUE=\"60\"></TD><TD><INPUT  TYPE=\"TEXT\" NAME=\"RFC\" SIZE=\"13\" MAXLENGTH=\"13\"></TD></TR>", "CONDATFIS", $transaccion_buffer);
	$transaccion_buffer = str_replace("CONDATFIS", "<TH>RFC del Beneficiario</TH></TR><TD><INPUT TYPE=\"TEXT\" NAME=\"UbNo\" SIZE=\"8\" MAXLENGTH=\"8\" onKeypress=\"alfanumerico(this);\" ></TD><TD><INPUT TYPE=\"TEXT\" NAME=\"NombreB\" SIZE=\"20\" MAXLENGTH=\"40\" onKeypress=\"alfanumerico(this);\"></TD><TD><INPUT  TYPE=\"TEXT\" NAME=\"RFC\" SIZE=\"13\" MAXLENGTH=\"13\"></TD></TR>CON1DATFIS", $transaccion_buffer);
	$transaccion_buffer = str_replace("CON1DATFIS", "<TH>Direcci&oacute;n</TH><TH>&nbsp;</TH><TH>LADA &nbsp;&nbsp; Tel&eacute;fono </TH>", $transaccion_buffer);
	
	$transaccion_buffer = str_replace("Tel&eacute;fono </TH>", "Tel&eacute;fono</TH></TR><TR><TD colspan=2><INPUT TYPE=\"TEXT\" NAME=\"DireccB\" SIZE=\"20\" MAXVALUE=\"60\" class=\"combos1\"></TD><TD colspand=2><INPUT TYPE=\"TEXT\"  NAME=\"LadaB\" SIZE=\"3\" MAXLENGTH=\"3\" onKeypress=\"alfanumerico(this);\"> <INPUT TYPE=\"TEXT\" NAME=\"TeleB\" SIZE=\"8\" MAXLENGTH=\"7\" onKeypress=\"alfanumerico(this);\"></TD></TR>", $transaccion_buffer);
	$transaccion_buffer = str_replace("</TR><TR><TH ALIGN=LEFT>Banco receptor</TH>", "</TR><TR><TH ALIGN=LEFT>Banco receptor </TH><TH ALIGN=LEFT>Cuenta CLABE</TH><TH ALIGN=LEFT>Tarjeta de Debito</TH>", $transaccion_buffer);
	$transaccion_buffer = str_replace("</TR><TR><TD ALIGN=LEFT><BR><b>Cuenta CLABE</b></BR></TD><TD ALIGN=LEFT><BR><b>Tarjeta de Débito</b></BR></TD></TR><TR>", " ", $transaccion_buffer);
	$transaccion_buffer = str_replace("<TR><TD ALIGN=LEFT><BR><b>Cuenta CLABE</b></BR></TD><TD ALIGN=LEFT><BR><b>Tarjeta de Débito</b></BR></TD></TR><TR>", " ", $transaccion_buffer);
	$transaccion_buffer = str_replace("<TR><TD COLSPAN=2 ALIGN=LEFT><BR><B><EM> Datos de la Cuenta No. 1", "<TABLE ALIGN=CENTER BORDER=0 WIDTH=\"100%\"><TR><TD COLSPAN=2 ALIGN=LEFT><BR><B><EM> Datos de la Cuenta No. 1", $transaccion_buffer);
	$transaccion_buffer = str_replace("NAME=\"UbNo\"", "NAME=\"UbNo\" ID=\"UbNo\" mensaje=\"Intruduzca  el dato con el que identificar&aacute; a su beneficiario.\" onMouseOver=\"mostrarAyuda('UbNo');\" onmouseout=\"rmvAyuda();\"", $transaccion_buffer);
	$transaccion_buffer = str_replace("NAME=\"NombreB\"", "NAME=\"NombreB\" ID=\"NombreB\" class=\"combos\" mensaje=\"Nombre de la persona o raz&oacute;n social que recibir&aacute; la transferencia.\" onMouseOver=\"mostrarAyuda('NombreB');\" onmouseout=\"rmvAyuda();\"", $transaccion_buffer);
	$transaccion_buffer = str_replace("NAME=\"RFC\"", "NAME=\"RFC\" ID=\"RFC\" mensaje=\"Dato Opcional\" onMouseOver=\"mostrarAyuda('RFC');\" onmouseout=\"rmvAyuda();\"", $transaccion_buffer);
	$transaccion_buffer = str_replace("NAME=\"ImpTipo\"", "NAME=\"ImpTipo\" ID=\"ImpTipo\" mensaje=\"Capturar 18 dígitos\" onMouseOver=\"mostrarAyuda('ImpTipo');\" onmouseout=\"rmvAyuda();\"", $transaccion_buffer);
	$transaccion_buffer = str_replace("NAME=\"PAN\"", "NAME=\"PAN\" ID=\"PAN\" mensaje=\"Capturar los 16 dígitos \" onMouseOver=\"mostrarAyuda('PAN');\" onmouseout=\"rmvAyuda();\"", $transaccion_buffer);
	$transaccion_buffer = str_replace("NAME=\"Empresa\"", "NAME=\"Empresa\" ID=\"Empresa\" mensaje=\"Capturar los 18 dígitos \" onMouseOver=\"mostrarAyuda('Empresa');\" onmouseout=\"rmvAyuda();\"", $transaccion_buffer);
	$transaccion_buffer = str_replace("NAME=\"NumCheq\"", "NAME=\"NumCheq\" ID=\"NumCheq\" mensaje=\"Capturar los 16 dígitos \" onMouseOver=\"mostrarAyuda('NumCheq');\" onmouseout=\"rmvAyuda();\"", $transaccion_buffer);
	$transaccion_buffer = str_replace("NAME=\"EspecCh\"", "NAME=\"EspecCh\" ID=\"EspecCh\" mensaje=\"Capturar los 18 dígitos \" onMouseOver=\"mostrarAyuda('EspecCh');\" onmouseout=\"rmvAyuda();\"", $transaccion_buffer);
	$transaccion_buffer = str_replace("NAME=\"Talonar\"", "NAME=\"Talonar\" ID=\"Talonar\" mensaje=\"Capturar los 16 dígitos \" onMouseOver=\"mostrarAyuda('Talonar');\" onmouseout=\"rmvAyuda();\"", $transaccion_buffer);
	
	
	//MAOS Nov2013 Quitar Scrolls F

    $t->set_var( "transaccion_buffer", $transaccion_buffer );
    $t->pparse( "output", "CatalogoDeCuentas_tpl" );
}
else
{
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "userlogin.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "user_login_tpl" => "userlogin.tpl"
        ) );

    $t->set_var( "redirect_url", "/transaccion/CatalogoDeCuentas/" );

    $t->pparse( "output", "user_login_tpl" );
}
?>
