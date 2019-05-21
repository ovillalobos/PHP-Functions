<?php
//
//
// Created on: <1-Nov-2001 17:37:53	smb>
//
// This	source file	is part	of IVA.
//
// Copyright (C) 1997-2001 Internet	de Alta	Calidad, S.A. de C.V.  All rights reserved.
//
// This	program	is free	software; you can redistribute it and/or
// modify it under the terms of	the	GNU	General	Public License
// as published	by the Free	Software Foundation; either	version	2
// of the License, or (at your option) any later version.
//
// This	program	is distributed in the hope that	it will	be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A	PARTICULAR PURPOSE.	 See the
// GNU General Public License for more details.
//
// You should have received	a copy of the GNU General Public License
// along with this program;	if not,	write to the Free Software
// Foundation, Inc., 59	Temple Place - Suite 330, Boston, MA  02111-1307, US
//
include_once( "classes/INIFile.php" );
include_once( "classes/eztemplate.php" );
include_once( "classes/ezhttptool.php" );

include_once( "eztransaccion/classes/eztransaccion.php" );
include_once( "ezsession/classes/ezsession.php" );
include_once( "classes/ezdatetime.php" );

$session = &eZSession::globalSession();

if ( !$session->fetch() )
	$session->store();

$ini =&	$GLOBALS["GlobalSiteIni"];

$Language =	$ini->read_var(	"eZTransaccionMain", "Language" );
// $backend	= $ini->read_var( "eZTransaccionMain", "Backend" );
$ServerNumber =	$ini->read_var( "site", "ServerNumber" );
$DomainPostfix = $ini->read_var( "site", "DomainPostfix" );

$backend = "gw".$ServerNumber.$DomainPostfix;

include_once( "ezuserbb/classes/ezuser.php" );

$user =& eZUserBB::currentUser();

// DebugBreak();

if ( $user )
{
	$t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ), "eztransaccion/user/intl/", $Language, "PagoAutTarjeta.php" );
	$t->setAllStrings();

	// AGG I 25Nov2005 Para poner fecha y hora en los comprobantes
	$dateTime  = new eZDateTime( );
	$timeStamp = $dateTime->timeStamp();
	$time =& date('H:i:s', $timeStamp );
	$date =& date('jMY', $timeStamp );
	// AGG F 25Nov2005 Para poner fecha y hora en los comprobantes

	$t->set_file( array( "PagoAutTarjeta_tpl" => "PagoAutTarjeta.tpl" ) );
	$session =& eZSession::globalSession();
	if ( !$session->fetch() )
	{
		$session->store();
	}
	$tr					= new eZTransaccion();
	$usr				= $session->variable( "r_usr" );
	$qki				= $session->variable( "r_qki" );
	$priv				= $session->variable( "r_priv" );
	$transaccion_buffer	= "";
	if( empty( $_POST['Access'] ) )
	{
		$_POST['Access'] = "FrAc";
	}
	switch ( $_POST['Access'] )
	{
		case "FrAc":
			$particularFields = "";
			$_POST['Accion']	= "Listapat";
			$particularFields = "&Cust=".urlencode($Cust)."&Accion=".urlencode($_POST['Accion'])."&Pos=".urlencode($Pos);
			$tr->blog( $qki,"Pago Automatico de Tarjeta", $Cust, $_POST['Accion'] );
			break;
		case "Confirm":
		case "Process":
		if( $_POST['NewPAT'] =="Alta" )
		{
			$_POST['Accion'] = "AltaPAT";
			//var_dump($_POST['Accion']);
		}
		else if( $_POST['EditPAT'] == "Cambio" )
		{
			$_POST['Accion']	= "EditPAT";
			//var_dump($_POST['Accion']);
		}
		else if( $_POST['BajaPAT'] == "Baja" )
		{
			//var_dump($_POST['Editar']);
			$_POST['Accion']	= "MBajapat";
		}
		else if( $_POST['Listado'] == "Cancelar" )
		{
			$_POST['Accion']	= "Listapat";
			$_POST['Access']	= "FrAc";
		}

//		echo "particularfields accion[$_POST['Accion']] access[$_POST['Access']]";
		$particularFields =	"&Passwd=".encrypt( $_POST['code'], strtolower( $usr ) )."&Cust=".urlencode($Cust)."&FrAccount=".urlencode($_POST['FrAccount'])."&Accion=".urlencode($_POST['Accion'])."&DiasPzo=".urlencode($_POST['DiasPzo'])."&RenCap=".urlencode($_POST['RenCap'])."&Editar=".urlencode($_POST['Editar'])."&PlazaB=".urlencode($_POST['PlazaB']);
//		echo "YEHO process particularfiel[$particularFields] FrAccount[$_POST['FrAccount']] Cust[$Cust] Accion[$_POST['Accion']]";

		$tr->blog( $qki,"Pago Automatico de Tarjeta", $_POST['FrAccount'], $Cust, $_POST['Accion'] );

		break;
	}
	//var_dump($particularFields);
	$transaccion_buffer	= "";
	// DebugBreak();

	$ret_code =	$tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=pat&Access=".urlencode($_POST['Access'])."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv,	$transaccion_buffer);
    	$t->set_var( "transaccion_buffer", $transaccion_buffer.$Pagina );
	$t->pparse( "output", "PagoAutTarjeta_tpl" );
}
else
{
	$t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ), "eztransaccion/user/intl/", $Language, "userlogin.php" );
	$t->setAllStrings();
	$t->set_file( array( "user_login_tpl" => "userlogin.tpl" ) );
	$t->set_var( "redirect_url", "/transaccion/PagoAutTarjeta/" );
	$t->pparse( "output", "user_login_tpl" );
}
?>
