<style>
#msgDao{
	border:1px solid #979798;
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
.thBB TH{
	border-left: 1px solid #B498CB;
	font-size: 11px;
	text-align: center;
	border-bottom: 2px solid #979798; 
}
#domOrden td
{
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
.button{
	background-color: #AEAACB; border: 1px solid #000000; padding: 2px; color: #ffffff;
	font-weight: bold; font-size: 13px; text-shadow: 1px 1px 1px rgba(0,0,0,0.3); margin-right: 5px; cursor: pointer;		
}
.button:hover{
	background-color: #7C6CC5; border: 1px solid #000000;
}
.button:active{
	background-color: #615DA3; border: 1px solid #000000;
}
</style>
<script type="text/javascript">
	$(document).ready( function() {
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
                         "eztransaccion/user/intl/", $Language, "AutPagosProgramadosDAO.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "AutPagosProgramados_tpl" => "AutPagosProgramados.tpl"
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
		
	if(!empty($_POST['Parent1']))
		$Parent1 = $_POST['Parent1'];
		
	if(!empty($_POST['CustNo']))
		$CustNo = $_POST['CustNo'];
		
	if(!empty($_POST['Cq']))
		$Cq = $_POST['Cq'];
		
	if(!empty($_POST['Apocope']))
		$Apocope = $_POST['Apocope'];
		
	if(!empty($_POST['code']))
		$code = $_POST['code'];
		
	if(!empty($_POST['Cuenta']))
		$Cuenta = $_POST['Cuenta'];
		
	if(!empty($_POST['Cust']))
		$CustNo = $_POST['Cust'];
		
	if(!empty($_POST['Sub']))
		$Sub = $_POST['Sub'];
		
	if(!empty($_POST['Pos']))
		$Pos = $_POST['Pos'];
		
	if(!empty($_POST['Amount']))
		$Amount = $_POST['Amount'];
		
	if(!empty($_POST['Desc']))
		$Desc = $_POST['Desc'];
		
	if(!empty($_POST['Nomben1']))
		$Nomben1 = $_POST['Nomben1'];
//HB   
  
    if(empty($Access)) {
        $Access = "FrAc";
    }
    switch($Access) {
    case "FrAc":
        $particularFields = "";
        $Accion = "Listdao";
        $particularFields = "&Cust=".urlencode($Cust)."&Cuenta=".urlencode($Cuenta)."&Sub=".urlencode($Sub)."&Accion=".urlencode($Accion)."&Pos=".urlencode($Pos);
        $tr->blog($qki,"AutorizacionPP", $FrAccount, $Cust, $Cuenta, $Sub, $Accion);
        break;
    case "Confirm":
    case "Process":
        if($Listado=="Consultas") {
            $Accion = "Listdao";
        }
        else if($Aut=="Autorizar") {
            $Accion = "AutAPP";
        }
        else if($Button == "Autorizar"){
        	$Accion = "AutAPP";
        }
		//ALUNA  I 21AGO2007 agenda de pagos tokens
		else
		{
				$Accion = "AutAPP"; 
		}
		//ALUNA  F 21AGO2007 agenda de pagos tokens
		
        $particularFields = "";
        $particularFields = "&Passwd=".encrypt( $code, strtolower( $usr ) ).	//ALUNA 30Jul2007 agenda de pagos tokens
							"&Cust=".urlencode($Cust)."&Cuenta=".urlencode($Cuenta)
                            ."&Sub=".urlencode($Sub)."&Apocope=".urlencode($Apocope)
                            ."&Accion=".urlencode($Accion);
        $tr->blog($qki,"AutorizacionesPP", $FrAccount, $Nomben1, $Desc, $Amount, $Accion);
        break;
    }
    $transaccion_buffer = "";
    // DebugBreak();
    $ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=aup&Access=".urlencode($Access)."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // autorizacion de operaciones pendientes
	
	switch($Access) {
		case "FrAc":
			switch($Accion) {
				case "Listdao":
					$transaccion_buffer = str_replace("<TABLE WIDTH=\"80%\">","<DIV id=\"msgDao\" ><DIV id=\"title\">Domingo a la Orden<a  HREF=\"/transaccion/pagosprogramadosDAO/\" ><img border=\"0\" src=\"/sitedesign/bajionet/images/btn_close.gif\" class=\"boCerrar\" alt=\"Cerrar\"></a></DIV><TABLE WIDTH=\"80%\">", $transaccion_buffer);
					$transaccion_buffer = str_replace( "Usted a&uacute;n no tiene operaciones pendientes de autorizar en su Agenda de Pagos.","Usted a&uacute;n no tiene operaciones pendientes de autorizar en su Domingo a la Orden.", $transaccion_buffer);
					$transaccion_buffer = str_replace( "<TD><A HREF=\"/transaccion/pagosprogramados/\">Agregar nuevo Pago Programado:</A> Calendarizar un nuevo pagos con cargo a su cuenta.</TD>","<TD><A CLASS='boTxtCerrar' HREF=\"/transaccion/pagosprogramadosDAO/\">Agregar nuevo Pago Programado:</A> Calendarizar un nuevo pagos con cargo a su cuenta.</TD>", $transaccion_buffer);
					$transaccion_buffer = str_replace("</TABLE>","</TABLE><BR/></DIV>", $transaccion_buffer);
					$transaccion_buffer = str_replace("<TABLE WIDTH=100% CELLSPACING=2 BORDER=0>","<TABLE WIDTH=100% CELLSPACING=0 BORDER=0>", $transaccion_buffer);
					
					$transaccion_buffer = str_replace( "<td bgcolor=\"#DDDDDD\" >","<td bgcolor=\"#DDDDDD\">a", $transaccion_buffer);
					$transaccion_buffer = str_replace( "<TR BGCOLOR=\"#5A419C\">","<TR class=\"thBB\">", $transaccion_buffer);
					$transaccion_buffer = str_replace( "<INPUT TYPE=SUBMIT NAME=\"Aut\" VALUE=\"Autorizar\">","<INPUT CLASS=\"button\" TYPE=SUBMIT NAME=\"Aut\" VALUE=\"Autorizar\">", $transaccion_buffer);
					$transaccion_buffer = str_replace( "<INPUT TYPE=SUBMIT NAME=\"Listado\" VALUE=\"Consultas\">","<INPUT CLASS=\"button\" TYPE=SUBMIT NAME=\"Listado\" VALUE=\"Consultas\">", $transaccion_buffer);
					$transaccion_buffer = str_replace( "<P>Autorizar:   Seleccione el pago que desee liberar en la columna de Autorizar y oprima el Bot&oacute;n de <I>Autorizar</I></P>","", $transaccion_buffer);
				break;
			}
		break;
		case "Confirm":
			switch($Accion) {
				case "AutAPP":
					$transaccion_buffer = str_replace( "<INPUT TYPE=SUBMIT NAME=\"Button\" VALUE=\"Autorizar\">","<INPUT CLASS=\"button\" TYPE=SUBMIT NAME=\"Button\" VALUE=\"Autorizar\">", $transaccion_buffer);
					$transaccion_buffer = str_replace( "<INPUT TYPE=BUTTON VALUE=\"Cambiar\" LANGUAGE=\"JavaScript\" ONCLICK=\"parent.history.back()\">","<INPUT CLASS=\"button\" TYPE=BUTTON VALUE=\"Cambiar\" LANGUAGE=\"JavaScript\" ONCLICK=\"parent.history.back()\">", $transaccion_buffer);
				break;
			}
		break;
		case "Process":
			switch($Accion) {
				case "AutAPP":
					$transaccion_buffer = str_replace( "<FORM METHOD=post>","<DIV id=\"msgDao\" ><DIV id=\"title\">Domingo a la Orden<a  HREF=\"/transaccion/autorizacionPPDAO/\" ><img border=\"0\" src=\"/sitedesign/bajionet/images/btn_close.gif\" class=\"boCerrar\" alt=\"Cerrar\"></a></DIV><FORM METHOD=post>", $transaccion_buffer);
					$transaccion_buffer = str_replace( "</FORM>","</FORM></DIV>", $transaccion_buffer);
					$transaccion_buffer = str_replace( "<P>Para regresar a la Autorizaci&oacute;n de Pagos <A HREF=\"/transaccion/autorizacionPP/\">haga clic aqu&iacute</A>.</P>","<P>Para regresar a la Autorizaci&oacute;n de Pagos Domingo a la Orden <A HREF=\"/transaccion/autorizacionPPDAO/\">haga clic aqu&iacute</A>.</P>", $transaccion_buffer);
				break;
			}
			break;
		break;
    }
	$transaccion_buffer = str_replace("pekes", "Pekes", $transaccion_buffer);
	$transaccion_buffer = str_replace("unico", "&uacute;nico", $transaccion_buffer);
	$transaccion_buffer = str_replace("Unico", "&Uacute;nico", $transaccion_buffer);
	$transaccion_buffer = str_replace("chavos", "Chavos", $transaccion_buffer);
	//$transaccion_buffer = str_replace("</TABLE>","</TABLE>".$Access."|".$Accion, $transaccion_buffer); // Temporal [BORRAR]	
	$transaccion_buffer = str_replace("","", $transaccion_buffer);
    $t->set_var( "transaccion_buffer", $transaccion_buffer );
    $t->pparse( "output", "AutPagosProgramados_tpl" );
}
else
{
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "userlogin.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "user_login_tpl" => "userlogin.tpl"
        ) );

    $t->set_var( "redirect_url", "/transaccion/autorizacionPPDAO/" );

    $t->pparse( "output", "user_login_tpl" );
}
?>