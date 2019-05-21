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
                         "eztransaccion/user/intl/", $Language, "ConEmplNom.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "ConEmpNom_tpl" => "ConEmpNom.tpl"
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
	//var_dump("Access",$Access, "Accion: ", $Accion, "Button: ", $Button, "Position: ", $Pos);

	if (($_POST['Pos']) > 0)
	{
		$band = 1;
	}
    if(empty($_POST['Access'])) {
        $_POST['Access'] = "FrAc";
    }
    switch($_POST['Access']) {
    case "FrAc":
		$_POST['Accion'] = "Consulta";
        $particularFields = "&Accion=".urlencode($_POST['Accion']);
        $tr->blog($qki,"ConEmpNom", $_POST['framount'], $_POST['ToAccount'], $_POST['RenInt'], $_POST['Button'], $_POST['Accion']);

        break;
    case "Confirm":
		$_POST['Accion'] = "Modifica";
		if($_POST['Button'] =="OK")
		{
			$particularFields =	"&PAN=".urlencode($_POST['PAN'])."&RenCap=".urlencode($_POST['RenCap'])."&RenInt=".urlencode($_POST['RenInt'])."&Desc=".urlencode($_POST['Desc'])."&Talonar=".urlencode($_POST['Talonar'])."&Day=".urlencode($_POST['Day'])."&Month=".urlencode($_POST['Month'])."&Year=".urlencode($_POST['Year'])."&rfc=".urlencode($_POST['rfc'])."&PlazaB=".urlencode($_POST['PlazaB'])."&Plaza2=".urlencode($_POST['Plaza2'])."&Plaza3=".urlencode($_POST['Plaza3'])."&cqno=".urlencode($_POST['cqno'])."&Porcen3=".urlencode($_POST['Porcen3'])."&Porcen2=".urlencode($_POST['Porcen2'])."&file=".urlencode($_POST['file'])."&NombreB=".urlencode($_POST['NombreB'])."&Parent1=".urlencode($_POST['Parent1'])."&Amount=".urlencode($_POST['Amount'])."&Nomben2=".urlencode($_POST['Nomben2'])."&Parent2=".urlencode($_POST['Parent2'])."&framount=".urlencode($_POST['framount'])."&Nomben3=".urlencode($_POST['Nomben3'])."&Parent3=".urlencode($_POST['Parent3'])."&toamount=".urlencode($_POST['toamount'])."&ToAccount=".urlencode($_POST['ToAccount'])."&Accion=".urlencode($_POST['Accion'])."&framount=".urlencode($_POST['framount'])."&Button=".urlencode($_POST['Button'])."&Empresa=".urlencode($_POST['Empresa'])."&desc1=".urlencode($_POST['desc1'])."&desc2=".urlencode($_POST['desc2'])."&pan1=".urlencode($_POST['pan1'])."&numcheq2=".urlencode($_POST['numcheq2'])."&Cuenta=".urlencode($_POST['Cuenta'])."&estado=".urlencode($_POST['estado']);
		}

    case "Process":
		$_POST['Accion'] = "Modifica";

		if($_POST['Button']=="Consultar Detalle")
		{
			$_POST['Button'] = "Modificar";
			$particularFields =	"&ToAccount=".urlencode($_POST['ToAccount'])."&Accion=".urlencode($_POST['Accion'])."&framount=".urlencode($_POST['framount'])."&Button=".urlencode($_POST['Button']);
		}
		if($_POST['Button']=="OK")
		{
                $particularFields =	"&PAN=".urlencode($_POST['PAN'])."&RenCap=".urlencode($_POST['RenCap'])."&RenInt=".urlencode($_POST['RenInt'])."&Desc=".urlencode($_POST['Desc'])."&Talonar=".urlencode($_POST['Talonar'])."&Day=".urlencode($_POST['Day'])."&Month=".urlencode($_POST['Month'])."&Year=".urlencode($_POST['Year'])."&rfc=".urlencode($_POST['rfc'])."&PlazaB=".urlencode($_POST['PlazaB'])."&Plaza2=".urlencode($_POST['Plaza2'])."&Plaza3=".urlencode($_POST['Plaza3'])."&cqno=".urlencode($_POST['cqno'])."&Porcen3=".urlencode($_POST['Porcen3'])."&Porcen2=".urlencode($_POST['Porcen2'])."&file=".urlencode($_POST['file'])."&NombreB=".urlencode($_POST['NombreB'])."&Parent1=".urlencode($_POST['Parent1'])."&Amount=".urlencode($_POST['Amount'])."&Nomben2=".urlencode($_POST['Nomben2'])."&Parent2=".urlencode($_POST['Parent2'])."&framount=".urlencode($_POST['framount'])."&Nomben3=".urlencode($_POST['Nomben3'])."&Parent3=".urlencode($_POST['Parent3'])."&toamount=".urlencode($_POST['toamount'])."&ToAccount=".urlencode($_POST['ToAccount'])."&Accion=".urlencode($_POST['Accion'])."&framount=".urlencode($_POST['framount'])."&Button=".urlencode($_POST['Button'])."&Empresa=".urlencode($_POST['Empresa'])."&desc1=".urlencode($_POST['desc1'])."&desc2=".urlencode($_POST['desc2'])."&pan1=".urlencode($_POST['pan1'])."&numcheq2=".urlencode($_POST['numcheq2'])."&Cuenta=".urlencode($_POST['Cuenta'])."&estado=".urlencode($_POST['estado']);
		}
		if($_POST['Button'] =="Consultar mas Empleados")
		{
			$_POST['Button'] ="Consultar";
			$_POST['Access'] = "FrAc";
			$_POST['Accion'] = "Consulta";
			if (($_POST['Pos']) > 0)
			{
				$particularFields ="&Accion=".urlencode($_POST['Accion'])."&Button=".urlencode($_POST['Button'])."&Pos=".urlencode($_POST['Pos']);
			}
			else
			{
				$particularFields ="&Accion=".urlencode($_POST['Accion'])."&Button=".urlencode($_POST['Button']);
			}
		}


        $tr->blog($qki,"ConEmpNom", $_POST['framount'], $_POST['ToAccount'], $_POST['RenInt'], $_POST['Button'], $_POST['Accion'] );

        break;
    }
    $transaccion_buffer = "";
    // DebugBreak();
    $ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=nee&Access=".urlencode($_POST['Access'])."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // transferencia
    //var_dump ( $transaccion_buffer );
	//var_dump($particularFields);
    $t->set_var( "transaccion_buffer", $transaccion_buffer );
    $t->pparse( "output", "ConEmpNom_tpl" );

}
else
{
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "userlogin.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "user_login_tpl" => "userlogin.tpl"
        ) );

    $t->set_var( "redirect_url", "/transaccion/ConEmpNom/" );

    $t->pparse( "output", "user_login_tpl" );
}
?>
