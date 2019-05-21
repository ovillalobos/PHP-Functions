<style>
	.cabecera-tabla {
		height: 17px;
		background-image: url(https://images.bb.com.mx/sitedesign/bajionet/images/barra_mor.png); 
		font-size:10px;
		font-weight: bold;
		color: white;
		font-family: Tahoma, Verdana, Arial, Helvetica, sans-serif;
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
	.toolBarCSS{
		background: #665497;
		background: -moz-linear-gradient(top, #665497 0%, #452e81 100%); /* FF3.6+ */
		background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#665497), color-stop(100%,#452e81)); /* Chrome,Safari4+ */
		background: -webkit-linear-gradient(top, #665497 0%,#452e81 100%); /* Chrome10+,Safari5.1+ */
		background: -o-linear-gradient(top, #665497 0%,#452e81 100%); /* Opera 11.10+ */
		background: -ms-linear-gradient(top, #665497 0%,#452e81 100%); /* IE10+ */
		background: linear-gradient(to bottom, #665497 0%,#452e81 100%); /* W3C */
		filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#665497', endColorstr='#452e81',GradientType=0 ); /* IE6-9 */
		width: 350px;
		padding-top: 5px;
		padding-bottom: 5px;
		color: #ffffff;
		font-weight: bold;
		text-shadow: 1px 1px 2px rgba(0,0,0,0.8);
	}
	.borderDAO{
		margin: 10px auto;
		width: 350px;
		border:1px solid #979798;
		padding-bottom: 5px;
	}
	input[type="text"]{
		border:1px solid #979798;
		color: #452e81;
	}
	select{
		border:1px solid #979798;
		color: #452e81;
	}
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
	.boCerrar{
		margin-top: -3px;
		float: right;
		cursor: pointer;
	}
</style>
<script type="text/javascript">
	$( document).ready(function(){
		$(".agregarCuenta").hide();
		$("select option[value='Nomina']").remove();
		$("select option[value='NominaBasica']").remove();
		$("select option[value='basica']").remove();
		$("select option[value='brillan']").remove();
		$("select option[value='cheqsc']").remove();
		$("select option[value='Clasica']").remove();
		$("select option[value='dolar']").remove();
		$("select option[value='euro']").remove();
		$("select option[value='maestra']").remove();
		$("select option[value='precisa']").remove();
		$("select option[value='term']").remove();
		
		$(".boCerrar").click( function () {
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
$ServerNumber = $ini->read_var( "site", "ServerNumber" );
$DomainPostfix = $ini->read_var( "site", "DomainPostfix" );

$backend = "gw" . $ServerNumber. $DomainPostfix;

include_once( "ezuserbb/classes/ezuser.php" );

$user =& eZUserBB::currentUser();

// DebugBreak();

if ( $user )
{
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "ligasDAO.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "ligas_tpl" => "ligas.tpl"
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
		
	if(!empty($_POST['FrAccount']))
		$FrAccount = $_POST['FrAccount'];
		
	if(!empty($_POST['Cust']))
		$Cust = $_POST['Cust'];
		
	if(!empty($_POST['Cuenta']))
		$Cuenta = $_POST['Cuenta'];
		
	if(!empty($_POST['Sub']))
		$Sub = $_POST['Sub'];
		
	if(!empty($_POST['Accion']))
		$Accion = $_POST['Accion'];
		
	if(!empty($_POST['code']))
		$code = $_POST['code'];
//HB  
  
    if(empty($Access)) {
        $Access = "FrAc";
    }
    switch($Access) {
    case "FrAc":
        $particularFields = "";
        $tr->blog($qki,"Ligas", $FrAccount, $Cust, $Cuenta, $Sub, $Accion);
        break;
    case "Confirm":
    case "Process":
        if($Listado=="Consulta de Ligas") {
            $Accion = "Listajnt";
        }
        $particularFields = "&Passwd=".encrypt( $code, strtolower( $usr ) )."&Cust=".urlencode($Cust)."&Cuenta=".urlencode($Cuenta)."&Sub=".urlencode($Sub)."&Accion=".urlencode($Accion)."&Pos=".urlencode($Pos);
        $tr->blog($qki,"Ligas", $FrAccount, $Cust, $Cuenta, $Sub, $Accion);
        break;
    }
    $transaccion_buffer = "";
    // DebugBreak();
    $ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=jnt&Access=".urlencode($Access)."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // ligas de cuentas de terceros
		
	switch($Access) {
		case "FrAc":
			/****** ALTA DE CUENTAS ******/
			$transaccion_buffer = str_replace("<INPUT TYPE=\"RADIO\" NAME=\"Accion\" VALUE=\"Newjnt\" CHECKED>Agregar liga<BR>", "<INPUT CLASS=\"agregarCuenta\" TYPE=\"RADIO\" NAME=\"Accion\" VALUE=\"Newjnt\" CHECKED><SPAN CLASS=\"agregarCuenta\" >Agregar liga</SPAN><BR>", $transaccion_buffer);
			$transaccion_buffer = str_replace("<INPUT TYPE=\"RADIO\" NAME=\"Accion\" VALUE=\"Cancjnt\">Cancelar liga<BR>", " ", $transaccion_buffer);
			$transaccion_buffer = str_replace("<option value=\"Nomina\">Nomina</option>", "", $transaccion_buffer);    
			$transaccion_buffer = str_replace("<TD COLSPAN=3 ID=\"toolBar\" >&nbsp;</TD>", "<CENTER><DIV CLASS=\"toolBarCSS\">Alta de cuentas</DIV></CENTER>", $transaccion_buffer);
			
			$transaccion_buffer = str_replace("<INPUT TYPE=SUBMIT NAME=\"Listado\" VALUE=\"Consulta de Ligas\">", "", $transaccion_buffer);
			$transaccion_buffer = str_replace("<INPUT TYPE=SUBMIT NAME=\"Button\" VALUE=\"Aceptar\">", "<INPUT CLASS=\"button\" TYPE=SUBMIT NAME=\"Button\" VALUE=\"Aceptar\">", $transaccion_buffer);
			$transaccion_buffer = str_replace("<INPUT TYPE=RESET VALUE=\"Cambiar\">", "<A CLASS=\"button\" STYLE=\"padding: 2px 7px 2px 7px; text-decoration:none; color: #FFFFFF;\" HREF=\"/transaccion/catalogocuentasDAO/\" NAME=\"boAltas\" >Cancelar</A>", $transaccion_buffer);
			$transaccion_buffer = str_replace("<INPUT TYPE=BUTTON VALUE=\"Cambiar\" LANGUAGE=\"JavaScript\" ONCLICK=\"parent.history.back()\">", "<INPUT TYPE=BUTTON CLASS=\"button\" VALUE=\"Cambiar\" LANGUAGE=\"JavaScript\" ONCLICK=\"parent.history.back()\">", $transaccion_buffer);
        break;
		case "Confirm":
			switch($Accion) {
			case "Newjnt":
				/****** ERROR RC3096.HTM ******/
				$transaccion_buffer = str_replace("Verifique el tipo y sub de la cuenta.</P>", "", $transaccion_buffer);
				$transaccion_buffer = str_replace("<P>El n&uacute;mero de cliente seleccionado es inv&aacute;lido.<BR>", "<DIV id=\"msgDao\" ><DIV id=\"title\">Domingo a la Orden<a  HREF=\"/transaccion/ligasDAO/\" ><img border=\"0\" src=\"/sitedesign/bajionet/images/btn_close.gif\" class=\"boCerrar\" alt=\"Cerrar\"></a></DIV><P id=\"text\">El n&uacute;mero de cliente seleccionado es inv&aacute;lido. Verifique el tipo y sub de la cuenta.</P></DIV>", $transaccion_buffer);				
				$transaccion_buffer = str_replace("<INPUT TYPE=SUBMIT NAME=\"Button\" VALUE=\"Aceptar\">", "<INPUT CLASS=\"button\" TYPE=SUBMIT NAME=\"Button\" VALUE=\"Aceptar\">", $transaccion_buffer);
				$transaccion_buffer = str_replace("<INPUT TYPE=BUTTON VALUE=\"Cambiar\" LANGUAGE=\"JavaScript\" ONCLICK=\"parent.history.back()\">", "<INPUT CLASS=\"button\" TYPE=BUTTON VALUE=\"Cambiar\" LANGUAGE=\"JavaScript\" ONCLICK=\"parent.history.back()\">", $transaccion_buffer);
			break;
			}
		case "Process":
			switch($Accion) {
			case "Newjnt":
				/****** ERROR RC1206.HTM ******/
				$transaccion_buffer = str_replace("<P>Verifique el tipo y sub de la cuenta.</P>", "", $transaccion_buffer);				
				$transaccion_buffer = str_replace("<P>La cuenta especificada no existe.</P>", "<DIV id=\"msgDao\" ><DIV id=\"title\">Domingo a la Orden<a  HREF=\"/transaccion/ligasDAO/\" ><img border=\"0\" src=\"/sitedesign/bajionet/images/btn_close.gif\" class=\"boCerrar\" alt=\"Cerrar\"></a></DIV><P id=\"text\">La cuenta especificada no existe, verifique el tipo y sub de la cuenta. </P></DIV>", $transaccion_buffer);
				$transaccion_buffer = str_replace("<TABLE BORDER=0 WIDTH=\"80%\" ALIGN=CENTER>", "<DIV id=\"msgDao\" ><DIV id=\"title\">Domingo a la Orden<a  HREF=\"/transaccion/ligasDAO/\" ><img border=\"0\" src=\"/sitedesign/bajionet/images/btn_close.gif\" class=\"boCerrar\" alt=\"Cerrar\"></a></DIV><TABLE BORDER=0 WIDTH=\"80%\" ALIGN=CENTER>", $transaccion_buffer);	
				$transaccion_buffer = str_replace("</TABLE>", "</TABLE></DIV>", $transaccion_buffer);	
				$transaccion_buffer = str_replace("<P ALIGN=LEFT>Realizar <A HREF=\"/transaccion/transferencia\">transferencias</A> a sus cuentas relacionadas.</P>", "", $transaccion_buffer);	
			break;
			}
        break;
    }	

	//$transaccion_buffer = str_replace("</TABLE>","</TABLE>".$Access."|".$Accion, $transaccion_buffer); // Temporal [BORRAR]	
	
	$transaccion_buffer = str_replace("", "", $transaccion_buffer);	
	
    $t->set_var( "transaccion_buffer", $transaccion_buffer );
    $t->pparse( "output", "ligas_tpl" );
}
else
{
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "userlogin.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "user_login_tpl" => "userlogin.tpl"
        ) );

    $t->set_var( "redirect_url", "/transaccion/ligas/" );

    $t->pparse( "output", "user_login_tpl" );
}
?>