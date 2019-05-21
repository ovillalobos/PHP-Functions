<style>
.thBB{
	background: #665497;
	background: -moz-linear-gradient(top, #665497 0%, #452e81 100%); /* FF3.6+ */
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#665497), color-stop(100%,#452e81)); /* Chrome,Safari4+ */
	background: -webkit-linear-gradient(top, #665497 0%,#452e81 100%); /* Chrome10+,Safari5.1+ */
	background: -o-linear-gradient(top, #665497 0%,#452e81 100%); /* Opera 11.10+ */
	background: -ms-linear-gradient(top, #665497 0%,#452e81 100%); /* IE10+ */
	background: linear-gradient(to bottom, #665497 0%,#452e81 100%); /* W3C */
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#665497', endColorstr='#452e81',GradientType=0 ); /* IE6-9 */
}
.thBB th{
	border-bottom: 2px solid #979798; 
}
.tbBB{
	background: #f7f7f7; /* Old browsers */
	background: -moz-linear-gradient(top, #f7f7f7 0%, #dddddd 100%); /* FF3.6+ */
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#f7f7f7), color-stop(100%,#dddddd)); /* Chrome,Safari4+ */
	background: -webkit-linear-gradient(top, #f7f7f7 0%,#dddddd 100%); /* Chrome10+,Safari5.1+ */
	background: -o-linear-gradient(top, #f7f7f7 0%,#dddddd 100%); /* Opera 11.10+ */
	background: -ms-linear-gradient(top, #f7f7f7 0%,#dddddd 100%); /* IE10+ */
	background: linear-gradient(to bottom, #f7f7f7 0%,#dddddd 100%); /* W3C */
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f7f7f7', endColorstr='#dddddd',GradientType=0 ); /* IE6-9 */
}
.newTable{
	background: #f2f2f2; /* Old browsers */
	background: -moz-linear-gradient(top, #f2f2f2 0%, #dbdbdb 100%); /* FF3.6+ */
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#f2f2f2), color-stop(100%,#dbdbdb)); /* Chrome,Safari4+ */
	background: -webkit-linear-gradient(top, #f2f2f2 0%,#dbdbdb 100%); /* Chrome10+,Safari5.1+ */
	background: -o-linear-gradient(top, #f2f2f2 0%,#dbdbdb 100%); /* Opera 11.10+ */
	background: -ms-linear-gradient(top, #f2f2f2 0%,#dbdbdb 100%); /* IE10+ */
	background: linear-gradient(to bottom, #f2f2f2 0%,#dbdbdb 100%); /* W3C */
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f2f2f2', endColorstr='#dbdbdb',GradientType=0 ); /* IE6-9 */
	border-left: 1px solid #fff;
	border-bottom: 1px solid #fff;
}
#msgDao{
	border:1px solid #979798;
}
#msgDao p{
	padding-left: 5px;
}
#msgDao #title{
	background: #665497;
	background: -moz-linear-gradient(top, #665497 0%, #452e81 100%); /* FF3.6+ */
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#665497), color-stop(100%,#452e81)); /* Chrome,Safari4+ */
	background: -webkit-linear-gradient(top, #665497 0%,#452e81 100%); /* Chrome10+,Safari5.1+ */
	background: -o-linear-gradient(top, #665497 0%,#452e81 100%); /* Opera 11.10+ */
	background: -ms-linear-gradient(top, #665497 0%,#452e81 100%); /* IE10+ */
	background: linear-gradient(to bottom, #665497 0%,#452e81 100%); /* W3C */
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#665497', endColorstr='#452e81',GradientType=0 ); /* IE6-9 */
	text-align: center;

	padding-top: 3px;
	padding-bottom: 3px;
	color: #ffffff;
	font-weight: bold;
	text-shadow: 1px 1px 2px rgba(0,0,0,0.8);
}
#msgDao #text{
	padding-top: 5px;
	padding-bottom: 5px;
	padding-left: 5px;
	padding-right: 5px;	
	color: red;
}
.red{
	color: red;
}
.boCerrar{
	margin-top: -3px;
	float: right;
	cursor: pointer;
}
</style>
<script type="text/javascript">
$( document).ready(function(){
	//$(".nomDao").hide();
	/*
	var num, length;
	var val1Dao, fin1Dao;
	
	val1Dao = $("#val1").val();		
	num = val1Dao.indexOf(",");
	length = val1Dao.length;
	
	fin1Dao = val1Dao.substring(num+1,length);
	
	alert(fin1Dao);
	*/
	$(".boCerrar, .boTxtCerrar").click( function () {
		$("#msgDao").slideUp();
	});
	
});
</script>
<?php
//
//
// Created on: <1-Nov-2001 17:37:53 smb>
//
// This source file is part of IVA. 
//
//  Copyright (C) 1997-2004 Internet de Alta Calidad, S.A. de C.V.  Todos los derechos reservados. All rights reserved.
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
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),"eztransaccion/user/intl/", $Language, "CatalogoDeCuentasDAO.php" );
    $t -> setAllStrings();
    $t -> set_file( array( "CatalogoDeCuentasDAO_tpl" => "CatalogoDeCuentasDAO.tpl" ) );

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

	if(!empty($_POST['Access']))		$Access = $_POST['Access'];	
	if(!empty($_POST['DiasPzo']))		$DiasPzo = $_POST['DiasPzo'];	
	if(!empty($_POST['Accion']))		$Accion = $_POST['Accion'];		
	if(!empty($_POST['FrAccount']))		$FrAccount = $_POST['FrAccount'];		
	if(!empty($_POST['ToAccount']))		$ToAccount = $_POST['ToAccount'];
	if(!empty($_POST['Cuenta']))		$Cuenta = $_POST['Cuenta'];		
	if(!empty($_POST['Cust']))			$CustNo = $_POST['Cust'];		
	if(!empty($_POST['Sub']))			$Sub = $_POST['Sub'];		
	if(!empty($_POST['Pos']))			$Pos = $_POST['Pos'];		
	if(!empty($_POST['chkbox0']))		$chkbox0 = $_POST['chkbox0'];		
	if(!empty($_POST['chkbox1']))		$chkbox1 = $_POST['chkbox1'];		
	if(!empty($_POST['chkbox2']))		$chkbox2 = $_POST['chkbox2'];		
	if(!empty($_POST['chkbox3']))		$chkbox3 = $_POST['chkbox3'];		
	if(!empty($_POST['chkbox4']))		$chkbox4 = $_POST['chkbox4'];		
	if(!empty($_POST['chkbox5']))		$chkbox5 = $_POST['chkbox5'];		
	if(!empty($_POST['chkbox6']))		$chkbox6 = $_POST['chkbox6'];		
	if(!empty($_POST['chkbox7']))		$chkbox7 = $_POST['chkbox7'];		
	if(!empty($_POST['chkbox8']))		$chkbox8 = $_POST['chkbox8'];		
	if(!empty($_POST['chkbox9']))		$chkbox9 = $_POST['chkbox9'];		
	if(!empty($_POST['Nomben2']))		$Nomben2 = $_POST['Nomben2'];		
	if(!empty($_POST['Nomben3']))		$Nomben3 = $_POST['Nomben3'];    
    if(empty($_POST['Access']))			$_POST['Access'] = "FrAc";
    
    switch($_POST['Access']) 
	{	
		case "FrAc":
			$particularFields = "";
			$_POST['Accion'] = "Listdao";
			$particularFields = "&Cust=".urlencode($Cust)."&Cuenta=".urlencode($Cuenta)."&Sub=".urlencode($Sub)."&Accion=".urlencode($_POST['Accion'])."&UbNo=".urlencode($_POST['UbNo'])."&Pos=".urlencode($Pos);
			$tr->blog($qki,"Catalogo de Cuentas", $FrAccount, $Cust, $Cuenta, $Sub, $_POST['Accion']);
			break;
		case "Confirm":
		case "Process":
			if($_POST['Listado']=="Consultas") {
				//$_POST['Accion'] = "Listacta";
				$_POST['Accion'] = "Listdao";
			}
			else if($_POST['Newcta']=="Altas") {
				$t -> set_var( "redirect_url", "/transaccion/ligas/" );
				$_POST['Accion'] = "Newcta";
			}
			else if($_POST['Editcta'] == "Cambios"){
				$_POST['Accion'] = "Editcta";
			}
			else if($_POST['MenuCan']=="Bajas") {
				$_POST['Accion'] = "MenuCanD";
				$DiasPzo = "";
				$DiasPzo = "a".urlencode($chkbox0)."a".urlencode($chkbox1)."a".urlencode($chkbox2)."a".
						   urlencode($chkbox3)."a".urlencode($chkbox4)."a".urlencode($chkbox5)."a".
						   urlencode($chkbox6)."a".urlencode($chkbox7)."a".urlencode($chkbox8)."a".
						   urlencode($chkbox9);
			}
			else if($_POST['Listado']=="Cancelar") {
				$_POST['Accion'] = "Listdao";
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
	/******* MAOS Nov2013 Quitar Scrolls I *******/	
	/******* SIN DATOS FISCALES *******/
	$transaccion_buffer = str_replace("</TR><TR><TD><INPUT TYPE=\"TEXT\" NAME=\"DireccB\" SIZE=\"20\" MAXVALUE=\"60\"></TD></TR>", "SINDATFIS", $transaccion_buffer);
	$transaccion_buffer = str_replace("SINDATFIS", "<TH>&nbsp;</TH><TR><TD><INPUT TYPE=\"TEXT\" NAME=\"UbNo\" SIZE=\"8\" MAXLENGTH=\"8\" onKeypress=\"alfanumerico(this);\" ></TD><TD><INPUT TYPE=\"TEXT\" NAME=\"NombreB\" SIZE=\"20\" MAXLENGTH=\"40\" onKeypress=\"alfanumerico(this);\"></TD></TR>SIN1DATFIS", $transaccion_buffer);
	$transaccion_buffer = str_replace("SIN1DATFIS", "<TH>Direcci&oacute;n</TH><TH>&nbsp;</TH><TH>LADA &nbsp;&nbsp; Tel&eacute;fono </TH>", $transaccion_buffer);
	/******* CON DATOS FISCALES *******/
	$transaccion_buffer = str_replace("<TH>RFC del Beneficiario</TH></TR><TR><TD><INPUT TYPE=\"TEXT\" NAME=\"DireccB\" SIZE=\"20\" MAXVALUE=\"60\"></TD><TD><INPUT  TYPE=\"TEXT\" NAME=\"RFC\" SIZE=\"13\" MAXLENGTH=\"13\"></TD></TR>", "CONDATFIS", $transaccion_buffer);
	$transaccion_buffer = str_replace("CONDATFIS", "<TH>RFC del Beneficiario</TH></TR><TD><INPUT TYPE=\"TEXT\" NAME=\"UbNo\" SIZE=\"8\" MAXLENGTH=\"8\" onKeypress=\"alfanumerico(this);\" ></TD><TD><INPUT TYPE=\"TEXT\" NAME=\"NombreB\" SIZE=\"20\" MAXLENGTH=\"40\" onKeypress=\"alfanumerico(this);\"></TD><TD><INPUT  TYPE=\"TEXT\" NAME=\"RFC\" SIZE=\"13\" MAXLENGTH=\"13\"></TD></TR>CON1DATFIS", $transaccion_buffer);
	$transaccion_buffer = str_replace("CON1DATFIS", "<TH>Direcci&oacute;n</TH><TH>&nbsp;</TH><TH>LADA &nbsp;&nbsp; Tel&eacute;fono </TH>", $transaccion_buffer);	
	
	/******* OVVC JUNIO2014 DOMINGO A LA ORDEN *******/
	switch($_POST['Access']) {
		case "Confirm":
			switch($_POST['Accion']) {
				case "MenuCanD":
					/****** CANCELACIÓN ******/						
					$transaccion_buffer = str_replace("<UL>", "<DIV id=\"msgDao\" ><DIV id=\"title\">Domingo a la Orden<a  HREF=\"/transaccion/catalogocuentasDAO/\" ><img border=\"0\" src=\"/sitedesign/bajionet/images/btn_close.gif\" class=\"boCerrar\" alt=\"Cerrar\"></a></DIV><UL>", $transaccion_buffer);
					$transaccion_buffer = str_replace("</UL>", "</UL></DIV>", $transaccion_buffer);
					$transaccion_buffer = str_replace("Cuenta:", "<LI>Cuenta:", $transaccion_buffer);					
					$transaccion_buffer = str_replace("<INPUT TYPE=SUBMIT NAME=\"Button\" VALUE=\"Aceptar\">", "<INPUT CLASS=\"botonBajio\" TYPE=SUBMIT NAME=\"Button\" VALUE=\"Aceptar\">", $transaccion_buffer);
					$transaccion_buffer = str_replace("<INPUT TYPE=SUBMIT NAME=\"Listado\" VALUE=\"Cancelar\">", "<INPUT CLASS=\"botonBajio\"  TYPE=SUBMIT NAME=\"Listado\" VALUE=\"Cancelar\">", $transaccion_buffer);
					//$transaccion_buffer = str_replace("<P>El Beneficiario(s) no pudo ser dado de baja.</P>", "<DIV id=\"msgDao\" ><DIV id=\"title\">Domingo a la Orden<a  HREF=\"/transaccion/catalogocuentasDAO/\" ><img border=\"0\" src=\"/sitedesign/bajionet/images/btn_close.gif\" class=\"boCerrar\" alt=\"Cerrar\"></a></DIV><P CLASS=\"red\" >El Beneficiario(s) no pudo ser dado de baja.</P>", $transaccion_buffer);
					$transaccion_buffer = str_replace("<P>El Beneficiario(s) no pudo ser dado de baja.</P>", "<DIV id=\"msgDao\" ><DIV id=\"title\">Domingo a la Orden<a  HREF=\"/transaccion/catalogocuentasDAO/\" ><img border=\"0\" src=\"/sitedesign/bajionet/images/btn_close.gif\" class=\"boCerrar\" alt=\"Cerrar\"></a></DIV>", $transaccion_buffer);
					$transaccion_buffer = str_replace("<P>Es necesario que seleccione alguno.</P>", "<P>Es necesario seleccionar un beneficiario para poderlo dar de baja.<BR/>", $transaccion_buffer);
					$transaccion_buffer = str_replace("<p>Código: ( )</p>", "Para regresar al Cat&aacute;logo de cuentas de Domingo a la Orden <A HREF=\"/transaccion/catalogocuentasDAO/\">haga clic aqu&iacute</A>.</P></DIV>", $transaccion_buffer);
				break;
				default:
					$transaccion_buffer = str_replace("<P>El Beneficiario(s) no pudo ser dado de baja.</P>", "<DIV id=\"msgDao\" ><DIV id=\"title\">Domingo a la Orden<a  HREF=\"/transaccion/catalogocuentasDAO/\" ><img border=\"0\" src=\"/sitedesign/bajionet/images/btn_close.gif\" class=\"boCerrar\" alt=\"Cerrar\"></a></DIV><P CLASS=\"red\">El Beneficiario(s) no pudo ser dado de baja.</P>", $transaccion_buffer);
					$transaccion_buffer = str_replace("<p>Código: ( )</p>", "<P>Para regresar al Cat&aacute;logo de Cuentas de Domingo a la Orden <A HREF=\"/transaccion/catalogocuentasDAO/\">haga clic aqu&iacute</A>.</P></DIV>", $transaccion_buffer);
				break;
			}	
		break;
		case "Process":
			switch($_POST['Accion']) {
				case "MenuCanD":					
					$transaccion_buffer = str_replace("<TABLE BORDER=0 WIDTH=\"80%\" ALIGN=CENTER>", "<DIV id=\"msgDao\" ><DIV id=\"title\">Domingo a la Orden<a  HREF=\"/transaccion/catalogocuentasDAO/\" ><img border=\"0\" src=\"/sitedesign/bajionet/images/btn_close.gif\" class=\"boCerrar\" alt=\"Cerrar\"></a></DIV><TABLE BORDER=0 WIDTH=\"80%\" ALIGN=CENTER>", $transaccion_buffer);
					$transaccion_buffer = str_replace("</TABLE>", "</TABLE></DIV>", $transaccion_buffer);
					$transaccion_buffer = str_replace("<P>Para regresar al Cat&aacute;logo de Cuentas <A HREF=\"/transaccion/catalogocuentas/\">haga clic aqu&iacute</A>.</P>", "<P>Para regresar al Cat&aacute;logo de Cuentas <A HREF=\"/transaccion/catalogocuentasDAO/\">haga clic aqu&iacute</A>.</P>", $transaccion_buffer);
				break;
				default:

				break;
			}	
		break;
		case "FrAc":
			switch($_POST['Accion']) {
				case "Listdao":
					/****** CAMBIO DE COLUMNAS EN TABLA ******/
					$transaccion_buffer = str_replace("<TABLE WIDTH=95% CELLSPACING=2 BORDER=0>", "<TABLE WIDTH=95% CELLSPACING=0 BORDER=0>", $transaccion_buffer);
					$transaccion_buffer = str_replace("<TH><FONT COLOR=WHITE>Bajas</FONT></TH>", "<TH STYLE=\"border-right: 1px solid #BE9ED9;\"><FONT COLOR=WHITE><CENTER>Bajas</CENTER></FONT></TH>", $transaccion_buffer);
					$transaccion_buffer = str_replace("<TH><FONT COLOR=WHITE>Alias</FONT></TH>", "<TH STYLE=\"border-right: 1px solid #BE9ED9;\"><FONT COLOR=WHITE><CENTER>Datos del Beneficiario</CENTER></FONT></TH>", $transaccion_buffer);
					$transaccion_buffer = str_replace("<TH><FONT COLOR=WHITE>Datos del Beneficiario</FONT></TH>", "", $transaccion_buffer);
					$transaccion_buffer = str_replace("<TH><FONT COLOR=WHITE>Cambios</FONT></TH>", "", $transaccion_buffer);		
					
					/****** TABLAS ******/	
					$transaccion_buffer = str_replace("<TR BGCOLOR=\"#5A419C\">", "<TR CLASS=\"thBB\">", $transaccion_buffer);
					$transaccion_buffer = str_replace("<td bgcolor='#DDDDDD'>", "<td CLASS=\"tbBB\" >", $transaccion_buffer);	
					//$transaccion_buffer = str_replace("<P>Altas:   Oprima el Bot&oacute;n de <I>Altas</I></P>", "", $transaccion_buffer);
					//$transaccion_buffer = str_replace("<P>Bajas:   Seleccione el(los) beneficiario(s) que desea dar de baja en la columna de Bajas y oprima el Bot&oacute;n de <I>Bajas</I></P>	", "", $transaccion_buffer);
					$transaccion_buffer = str_replace("<P>Cambios: Seleccione el beneficiario del cual desee modificar datos en la columna de Cambios y oprima el Bot&oacute;n de <I>Cambios</I></P>", "", $transaccion_buffer);	
	
					//$transaccion_buffer = str_replace("<TD COLSPAN=4>Usted a&uacute;n no ha dado de alta ningún Beneficiario en su Catálogo de Cuentas para programar su agenda de pagos.</TD>", "<TD COLSPAN=4>Usted a&uacute;n no ha dado de alta ningún Beneficiario en su Catálogo de Cuentas para programar su Domingo a la Orden.</TD>", $transaccion_buffer);
					//$transaccion_buffer = str_replace("<TD>Este es el cat&aacute;logo de las cuentas que se encuentran ligadas con sus cuentas de BanBaj&iacute;o, hacia las cu&aacute;les Usted podr&aacute hacer transferencias a trav&eacute;s de la Agenda de Pagos o de los m&eacute;todos usuales de transferencia de fondos que le ofrece Bajionet.</TD>", "<TD>Este es el cat&aacute;logo de las cuentas que se encuentran ligadas con sus cuentas de BanBaj&iacute;o, hacia las cu&aacute;les Usted podr&aacute hacer transferencias a trav&eacute;s de Domingo a la Orden o de los m&eacute;todos usuales de transferencia de fondos que le ofrece Bajionet.</TD>", $transaccion_buffer);
					$transaccion_buffer = str_replace("<TD COLSPAN=4>Usted a&uacute;n no ha dado de alta ningún Beneficiario en su Catálogo de Cuentas para programar su agenda de pagos.</TD>", "<TD COLSPAN=4></TD>", $transaccion_buffer);
					$transaccion_buffer = str_replace("<TD>Este es el cat&aacute;logo de las cuentas que se encuentran ligadas con sus cuentas de BanBaj&iacute;o, hacia las cu&aacute;les Usted podr&aacute hacer transferencias a trav&eacute;s de la Agenda de Pagos o de los m&eacute;todos usuales de transferencia de fondos que le ofrece Bajionet.</TD>", "<TD></TD>", $transaccion_buffer);
				break;
				default:

				break;
			}	
		break;
	}		
	
	/* CREACIÓN DE BOTONES HTML DOMINGO A LA ORDEN */	
	$transaccion_buffer = str_replace("<INPUT CLASS=\"botonBajio\" TYPE=SUBMIT NAME=\"Newcta\" VALUE=\"Altas\">", "<A CLASS=\"botonBajio\" STYLE=\"padding: 2px 7px 2px 7px; text-decoration:none; color: #FFFFFF;\" HREF=\"#\" onclick=\"javascript:iniProcesarMenu('ligasDAO','')\" ID=\"boAltas\" NAME=\"boAltas\" mensaje=\"Proceso para dar de alta las cuentas Pekes y Chavos\" onMouseOver=\"mostrarAyuda('boAltas');\" onmouseout=\"rmvAyuda();\" >Altas</A>", $transaccion_buffer);		
	//$transaccion_buffer = str_replace("<INPUT CLASS=\"botonBajio\" TYPE=SUBMIT NAME=\"Newcta\" VALUE=\"Altas\">", "<A CLASS=\"botonBajio\" STYLE=\"padding: 2px 7px 2px 7px; text-decoration:none; color: #FFFFFF;\" HREF=\"/transaccion/ligasDAO/\" onclick="javascript:iniProcesarMenu('ligas','')" ID=\"boAltas\" NAME=\"boAltas\" mensaje=\"Proceso para dar de alta las cuentas Pekes y Chavos\" onMouseOver=\"mostrarAyuda('boAltas');\" onmouseout=\"rmvAyuda();\" >Altas</A>", $transaccion_buffer);		
	$transaccion_buffer = str_replace("<INPUT CLASS=\"botonBajio\" TYPE=SUBMIT NAME=\"MenuCan\" VALUE=\"Bajas\">", "<INPUT CLASS=\"botonBajio\" TYPE=SUBMIT NAME=\"MenuCan\" VALUE=\"Bajas\" >", $transaccion_buffer);	
	$transaccion_buffer = str_replace("<INPUT CLASS=\"botonBajio\" TYPE=SUBMIT NAME=\"Editcta\" VALUE=\"Cambios\">", "", $transaccion_buffer);
	$transaccion_buffer = str_replace("<INPUT CLASS=\"botonBajio\" TYPE=SUBMIT NAME=\"Listado\" VALUE=\"Consultas\">", "", $transaccion_buffer);
	
	$transaccion_buffer = str_replace("pekes", "Pekes", $transaccion_buffer);
	$transaccion_buffer = str_replace("chavos", "Chavos", $transaccion_buffer);
	
	//$transaccion_buffer = str_replace("<P>Es necesario que seleccione alguno.</P>","<P>Es necesario que seleccione alguno.</P>".$_POST['Access']."|".$_POST['Accion'], $transaccion_buffer); // Temporal [BORRAR]	
	//$transaccion_buffer = str_replace("</TABLE>","</TABLE>".$_POST['Access']."|".$_POST['Accion'], $transaccion_buffer); // Temporal [BORRAR]	
	//$transaccion_buffer = str_replace("","", $transaccion_buffer);
	
    $t->set_var( "transaccion_buffer", $transaccion_buffer );
    $t->pparse( "output", "CatalogoDeCuentasDAO_tpl" );	
}
else
{
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),"eztransaccion/user/intl/", $Language, "userlogin.php" );
    $t -> setAllStrings();
    $t -> set_file( array( "user_login_tpl" => "userlogin.tpl" ) );
    $t -> set_var( "redirect_url", "/transaccion/CatalogoDeCuentasDAO/" );
    $t -> pparse( "output", "user_login_tpl" );
}
?>
