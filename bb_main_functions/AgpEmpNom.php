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
                         "eztransaccion/user/intl/", $Language, "AgpEmpNom.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "AgpEmpNom_tpl" => "AgpEmpNom.tpl"
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
    if(empty($_POST['Accion'])) {
        $_POST['Accion'] = "Listaagp";
    }
	if($_POST['Listado']=="Cancelar")
	{
		$_POST['Accion'] = "Listaagp";
	}
	if($_POST['Editcta'] == "Cambios")
	{
       	$_POST['Accion'] = "Editcta";
		$_POST['Access'] = "FrAc";
    }

    switch($_POST['Access']) {
    case "FrAc":
		if($_POST['Accion'] == "Editcta")
			{
				$Edicion = "";
                    if ($_POST['Editar'] <> "") { $Edicion= $_POST['Editar']."-"; }
					$particularFields = "&Accion=".urlencode($_POST['Accion']).
							"&Button=".urlencode($_POST['Button'])."&Pos=".urlencode($Pos).
							"&Desc=".urlencode($Edicion);
			}
		else
		{
			$particularFields = "&Accion=".urlencode($_POST['Accion'])."&Pos=".urlencode($Pos)."&Desc=".urlencode($_POST['Desc']);
			$tr->blog($qki,"AgendaEmpleadosNomina", $_POST['FrAccount'], $Cust, $Cuenta, $Sub, $_POST['Accion']);
		}
		//var_dump ( $particularFields);
        break;
    case "Confirm":
                if($_POST['Newagp']=="Altas")
                {
                        $_POST['Accion'] = "Newagp";
                        $_POST['Access'] = "FrAc";
                }
                if($_POST['Canccta']=="Bajas")
                {
                        $_POST['Accion'] = "Cancagp";
                }
	        switch($_POST['Access'])
			{
              case "FrAc":
                                $particularFields = "&Accion=".urlencode($_POST['Accion']).
                                "&Pos=".urlencode($Pos).
                                "&Rownos=".$Borrar0.$Borrar1.$Borrar2.$Borrar3;
                                $tr->blog($qki,"AgendaEmpleadosNomina", $_POST['FrAccount'], $Cust, $Cuenta, $Sub, $_POST['Accion']);
                                break;
								//var_dump ( $particularFields, $_POST['Access'] );
               case "Confirm":
                                if ($_POST['Accion'] == "Newagp")
                                {
                                        $particularFields = "&Accion=".urlencode($_POST['Accion']).
                                        "&Button=".urlencode($_POST['Button']).
                                        "&FrAccount=".urlencode($_POST['FrAccount']).
                                        "&ToAccount=".urlencode($_POST['ToAccount']).
                                        "&ImpEnvio=".urlencode($_POST['ImpEnvio']).
                                        "&Amount=".urlencode($_POST['Amount']).
                                        "&RenInt=".urlencode($_POST['RenInt']).
                                        "&Desc=".urlencode($_POST['Desc']).
                                        "&Editar=".urlencode($_POST['Editar']).
                                        "&Day=".urlencode($_POST['Day']).
                                        "&Month=".urlencode($_POST['Month']).
                                        "&Year=".urlencode($_POST['Year']).
                                        "&Cq=".urlencode($_POST['Cq']).
                                        "&Porcen1=".urlencode($_POST['Porcen1']).
                                        "&DiasPzo=".urlencode($_POST['DiasPzo']).
                                        "&DayF=".urlencode($_POST['DayF']).
                                        "&MonthF=".urlencode($_POST['MonthF']);

                                }
								else if  ( $_POST['Accion'] ==  "Editcta")
								{
									//$Edicion = "";
									//if ($_POST['Editar'] <> "") { $Edicion= $_POST['Editar']."-"; }
                                        $particularFields = "&Accion=".urlencode($_POST['Accion']).
                                        "&Button=".urlencode($_POST['Button']).
                                        "&FrAccount=".urlencode($_POST['FrAccount']).
                                        "&ToAccount=".urlencode($_POST['ToAccount']).
                                        "&ImpEnvio=".urlencode($_POST['ImpEnvio']).
                                        "&Amount=".urlencode($_POST['Amount']).
                                        "&RenInt=".urlencode($_POST['RenInt']).
                                        "&Desc=".urlencode($_POST['Desc']).
                                        "&Editar=".urlencode($_POST['Editar']).
                                        "&Day=".urlencode($_POST['Day']).
                                        "&Month=".urlencode($_POST['Month']).
                                        "&Year=".urlencode($_POST['Year']).
                                        "&Cq=".urlencode($_POST['Cq']).
                                        "&Porcen1=".urlencode($_POST['Porcen1']).
                                        "&DiasPzo=".urlencode($_POST['DiasPzo']).
                                        "&DayF=".urlencode($_POST['DayF']).
										"&trace_no=".urlencode($_POST['trace_no']).
                                        "&MonthF=".urlencode($_POST['MonthF']);

								}
                                else
                                {
                                        $Rownos = "";
                                        if ($Borrar0 <> "") { $Rownos = $Rownos.$Borrar0."-"; }
                                        if ($Borrar1 <> "") { $Rownos = $Rownos.$Borrar1."-"; }
                                        if ($Borrar2 <> "") { $Rownos = $Rownos.$Borrar2."-"; }
                                        if ($Borrar3 <> "") { $Rownos = $Rownos.$Borrar3."-"; }
                                        if ($Borrar4 <> "") { $Rownos = $Rownos.$Borrar4."-"; }
                                        if ($Borrar5 <> "") { $Rownos = $Rownos.$Borrar5."-"; }
                                        if ($Borrar6 <> "") { $Rownos = $Rownos.$Borrar6."-"; }
                                        if ($Borrar7 <> "") { $Rownos = $Rownos.$Borrar7."-"; }
                                        if ($Borrar8 <> "") { $Rownos = $Rownos.$Borrar8."-"; }
                                        if ($Borrar9 <> "") { $Rownos = $Rownos.$Borrar9."-"; }
                                        $particularFields = "&Accion=".urlencode($_POST['Accion']).
                                        "&Button=".urlencode($_POST['Button'])."&Pos=".urlencode($Pos).
                                        "&Desc=".urlencode($Rownos);
                                }
							$tr->blog($qki,"Pagos Programados", $_POST['FrAccount'], $_POST['ToAccount'], $_POST['Amount'], $_POST['ImpEnvio'], $_POST['Accion']);
							//var_dump ( $particularFields);
                            break;
                }
                break;
    case "Process":
        if($_POST['Listado']=="Ver mas Registros")
        {
			$_POST['Listado']="Consultas";
            $_POST['Accion'] = "Listaagp";
        }
        switch($_POST['Accion'])
        {
                        case "Newagp":
                                $particularFields = "&Accion=".urlencode($_POST['Accion']).
                                "&Button=".urlencode($_POST['Button']).
                                "&FrAccount=".urlencode($_POST['FrAccount']).
                                "&ToAccount=".urlencode($_POST['ToAccount']).
                                "&ImpEnvio=".urlencode($_POST['ImpEnvio']).
                                "&Amount=".urlencode($_POST['Amount']).
                                "&RenInt=".urlencode($_POST['RenInt']).
                                "&Desc=".urlencode($_POST['Desc']).
                                "&Editar=".urlencode($_POST['Editar']).
                                "&Day=".urlencode($_POST['Day']).
                                "&Month=".urlencode($_POST['Month']).
                                "&Year=".urlencode($_POST['Year']).
                                "&Cq=".urlencode($_POST['Cq']).
                                "&Porcen1=".urlencode($_POST['Porcen1']).
                                "&DiasPzo=".urlencode($_POST['DiasPzo']).
                                "&DayF=".urlencode($_POST['DayF']).
                                "&MonthF=".urlencode($_POST['MonthF']);
								if (isset($_POST['code']) )
                                {
                                	$particularFields = $particularFields."&Passwd=".encrypt( $_POST['code'], strtolower( $usr ) );
                                }
                                $tr->blog($qki,"Pagos Programados", $_POST['FrAccount'], $_POST['ToAccount'], $_POST['Amount'], $_POST['ImpEnvio'], $_POST['Accion']);
                                break;
						case "Editcta":
                                $particularFields = "&Accion=".urlencode($_POST['Accion']).
                                "&Button=".urlencode($_POST['Button']).
                                "&FrAccount=".urlencode($_POST['FrAccount']).
                                "&ToAccount=".urlencode($_POST['ToAccount']).
                                "&ImpEnvio=".urlencode($_POST['ImpEnvio']).
                                "&Amount=".urlencode($_POST['Amount']).
                                "&RenInt=".urlencode($_POST['RenInt']).
                                "&Desc=".urlencode($_POST['Desc']).
                                "&Editar=".urlencode($_POST['Editar']).
                                "&Day=".urlencode($_POST['Day']).
                                "&Month=".urlencode($_POST['Month']).
                                "&Year=".urlencode($_POST['Year']).
                                "&Cq=".urlencode($_POST['Cq']).
                                "&Porcen1=".urlencode($_POST['Porcen1']).
                                "&DiasPzo=".urlencode($_POST['DiasPzo']).
                                "&DayF=".urlencode($_POST['DayF']).
								"&trace_no=".urlencode($_POST['trace_no']).
                                "&MonthF=".urlencode($_POST['MonthF']);
                                if (isset($_POST['code']) )
                                {
                                	$particularFields = $particularFields."&Passwd=".encrypt( $_POST['code'], strtolower( $usr ) );
                                }
                                $tr->blog($qki,"Pagos Programados", $_POST['FrAccount'], $_POST['ToAccount'], $_POST['Amount'], $_POST['ImpEnvio'], $_POST['Accion']);
                                break;
                        case "Cancagp":
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
    //var_dump ( $particularFields);
    $transaccion_buffer = "";
    // DebugBreak();
    $ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=agp&Access=".urlencode($_POST['Access'])."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer);
	$transaccion_buffer = str_replace("SIZE=\"20\" MAXVALUE=\"60\" onFocus=\"this.value=WithOutFormatAmount(this.value);focus();select();\"", "SIZE=\"17\" MAXLENGTH=\"17\" id=\"Amount\" onKeyUp=\"currencya( this,event );\"", $transaccion_buffer); //MAOS OCT2013 Formato de moneda en linea
	$transaccion_buffer = str_replace(", MAXVALUE=\"60\" onFocus=\"this.value=WithOutFormatAmount(this.value);focus();select();\"", " MAXLENGTH=\"17\" id=\"Amount\" onKeyUp=\"currencya( this,event );\"", $transaccion_buffer); //MAOS OCT2013 Formato de moneda en linea para area de Cambio

    $t->set_var( "transaccion_buffer", $transaccion_buffer );
    $t->pparse( "output", "AgpEmpNom_tpl" );
}
else
{
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "userlogin.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "user_login_tpl" => "userlogin.tpl"
        ) );

    $t->set_var( "redirect_url", "/transaccion/AgpEmpNom/" );

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