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

//LGAM Ini 22Jul2013 Cambio de Calendarios
include("eztransaccion/user/include/jscalendar/calendar-blue.css");
include("eztransaccion/user/include/jscalendar/calendar.js");

$session->setVariable("calendario",$session->variable("calendario")+1);

include("eztransaccion/user/include/js/FuncionesReimpresion.js");
$Calendario = $ini->read_var( "site", "Calendario" );
//LGAM Fin 22Jul2013 Cambio de Calendarios

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
                         "eztransaccion/user/intl/", $Language, "ProEmplNom.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "ProEmpNom_tpl" => "ProEmpNom.tpl"
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
    if(empty($_POST['Access'])) {
        $_POST['Access'] = "FrAc";
    }
    switch($_POST['Access']) {
    case "FrAc":
		$_POST['Accion'] = "Folios";
	    $particularFields = "&Accion=".urlencode($_POST['Accion']);
		$tr->blog($qki,"ProEmpNom", $_POST['Day'], $_POST['Month'], $_POST['ToAccount'], $_POST['Button'], $_POST['Accion']);
        break;

    case "Confirm":


		if($_POST['Button'] == "Repetir Nomina")
		{
			$_POST['Button'] = "OK";
			$_POST['Accion'] = "Modifica";
			$particularFields =	"&Day=".urlencode($_POST['Day'])."&Month=".urlencode($_POST['Month'])."&Year=".urlencode($_POST['Year'])."&ToAccount=".urlencode($_POST['ToAccount'])."&Button=".urlencode($_POST['Button'])."&Accion=".urlencode($_POST['Accion'])."&Amount=".$_POST['Amount']."&PAN=".urlencode($_POST['PAN']);
			$tr->blog($qki,"ProEmpNom", $_POST['Day'], $_POST['Month'], $_POST['ToAccount'], $_POST['Button'], $_POST['Accion']);
		}
		if($_POST['Button'] == "Cancelar Nomina")
		{
			$_POST['Button'] = "Cancelar";
			$_POST['Accion'] = "Cancelar";
			$particularFields =	"&FrAccount=".urlencode($_POST['FrAccount'])."&Button=".urlencode($_POST['Button'])."&Accion=".urlencode($_POST['Accion']);
			$tr->blog($qki,"ProEmpNom", $_POST['Day'], $_POST['Month'], $_POST['ToAccount'], $_POST['Button'], $_POST['Accion']);
		}

		break;
    case "Process":
	    if ($_POST['Accion'] == "Cancelar")
	    {
	        $particularFields = "&FrAccount=".urlencode($_POST['FrAccount'])."&Button=".urlencode($_POST['Button'])."&Accion=".urlencode($_POST['Accion']);
	    }
	    else
	    {
			$_POST['Button'] = "OK";
			$_POST['Accion'] = "Modifica";
	        $particularFields = "&Passwd=".encrypt( $_POST['code'], strtolower( $usr ) )."&Day=".urlencode($_POST['Day'])."&Month=".urlencode($_POST['Month'])."&Year=".urlencode($_POST['Year'])."&ToAccount=".urlencode($_POST['ToAccount'])."&Button=".urlencode($_POST['Button'])."&Accion=".urlencode($_POST['Accion'])."&Amount=".$_POST['Amount']."&PAN=".urlencode($_POST['PAN']);
	    }

		$tr->blog($qki,"ProEmpNom", $_POST['Day'], $_POST['Month'], $_POST['ToAccount'], $_POST['Button'], $_POST['Accion']);
	    break;
    }
    $transaccion_buffer = "";
    // DebugBreak();
    $ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=neo&Access=".urlencode($_POST['Access'])."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // transferencia
    // var_dump ( $transaccion_buffer );
    //var_dump ($particularFields);

    $t->set_var( "transaccion_buffer", $transaccion_buffer );
    $t->pparse( "output", "ProEmpNom_tpl" );

}
else
{
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "userlogin.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "user_login_tpl" => "userlogin.tpl"
        ) );

    $t->set_var( "redirect_url", "/transaccion/ProEmpNom/" );

    $t->pparse( "output", "user_login_tpl" );
}
if ($Calendario == 1) {
?>
<script type="text/javascript">
	$( document).ready(function(){
        $('.CaldOld').remove();
	});
</script>
<?php
} else {
?>
<script type="text/javascript">
	$( document).ready(function(){
        $('.CaldNew').remove();
	});
</script>
<?php
}
?>