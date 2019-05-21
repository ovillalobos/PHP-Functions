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
                         "eztransaccion/user/intl/", $Language, "CatEmpNom.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "CatEmpNom_tpl" => "CatEmpNom.tpl"
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
		
	if(!empty($_POST['Cust']))
		$Cust = $_POST['Cust'];
		
	if(!empty($_POST['Cuenta']))
		$Cuenta = $_POST['Cuenta'];
		
	if(!empty($_POST['FrAccount']))
		$FrAccount = $_POST['FrAccount'];
		
	if(!empty($_POST['Sub']))
		$Sub = $_POST['Sub'];
		
	if(!empty($_POST['Listado']))
		$Listado = $_POST['Listado'];
		
	if(!empty($_POST['DiasPzo']))
		$DiasPzo = $_POST['DiasPzo'];
		
	if(!empty($_POST['MenuCan']))
		$MenuCan = $_POST['MenuCan'];
		
	if(!empty($_POST['Editcta']))
		$Editcta = $_POST['Editcta'];
		
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
		
	if(!empty($_POST['Editar']))
		$Editar = $_POST['Editar'];
		
	if(!empty($_POST['Pos']))
		$Pos = $_POST['Pos'];
//HB
    if(empty($_POST['Access'])) {
        $_POST['Access'] = "FrAc";
    }
    switch($_POST['Access']) {
    case "FrAc":
        $particularFields = "";
        $_POST['Accion'] = "Listacta";
        $particularFields = "&Cust=".urlencode($Cust)."&Cuenta=".urlencode($Cuenta)."&Sub=".urlencode($Sub)."&Accion=".urlencode($_POST['Accion'])."&UbNo=".urlencode($_POST['UbNo'])."&Pos=".urlencode($Pos);
        $tr->blog($qki,"CatalogoEmpleadosNomina", $FrAccount, $Cust, $Cuenta, $Sub, $_POST['Accion']);
		//var_dump ( $particularFields );
        break;
    case "Confirm":
    case "Process":
        if($Listado=="Ver mas Registros") {
			$Listado ="Consultas";
            $_POST['Accion']  = "Listacta";
        }
        else if($_POST['Newcta']=="Altas") {
            $_POST['Accion'] = "Newcta";
        }
        else if($Editcta == "Cambios"){
        	$_POST['Accion'] = "Editcta";
        }
        else if($MenuCan=="Bajas") {
            $_POST['Accion'] = "MenuCan";
            $DiasPzo = "";
            $DiasPzo = "a".urlencode($chkbox0)."a".urlencode($chkbox1)."a".urlencode($chkbox2)."a".
            		   urlencode($chkbox3)."a".urlencode($chkbox4)."a".urlencode($chkbox5)."a".
            		   urlencode($chkbox6)."a".urlencode($chkbox7)."a".urlencode($chkbox8)."a".
            		   urlencode($chkbox9);
        }
        else if($Listado=="Cancelar") {
            $_POST['Accion'] = "Listacta";
        }
        else if($_POST['Button'] == "Alta Empleado"){
        	$_POST['Accion'] = "MenuAlt";
        }
        else if($_POST['Button'] == "Actualizar Empleado"){
        	$_POST['Accion'] = "MenuEdit";
        }

		//DGM
        $particularFields = "&Passwd=".encrypt( $_POST['code'], strtolower( $usr ) )."&Cust=".urlencode($Cust)."&Apocope=".urlencode($_POST['Apocope']).
        "&UbNo=".urlencode($_POST['UbNo']).
        "&Accion=".urlencode($_POST['Accion'])."&DiasPzo=".urlencode($DiasPzo)."&NombreB=".urlencode($_POST['NombreB']).
//      "&DireccB=".urlencode($DireccB)."&LadaB=".urlencode($LadaB).
        "&ImpTipo=".urlencode($_POST['ImpTipo']).
        "&Editar=".urlencode($Editar).
//        "&TeleB=".urlencode($TeleB).
        "&Pos=".urlencode($Pos)."&Button=".urlencode($_POST['Button']);
        $tr->blog($qki,"CatalogoEmpleadosNomina", $FrAccount, $Cust, $Cuenta, $Sub, $_POST['Accion']);
	//	var_dump ( $particularFields );
        break;
    }
    $transaccion_buffer = "";
    // DebugBreak();
    $ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=cdc&Access=".urlencode($_POST['Access'])."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // ligas de cuentas de terceros

    $t->set_var( "transaccion_buffer", $transaccion_buffer );
    $t->pparse( "output", "CatEmpNom_tpl" );
}
else
{
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "userlogin.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "user_login_tpl" => "userlogin.tpl"
        ) );

    $t->set_var( "redirect_url", "/transaccion/CatEmpNom/" );

    $t->pparse( "output", "user_login_tpl" );
}
?>
