<?php
//
//
// Created on: <20/dic/2011>
//PSM activacion de servicio de notificaciones SMS para TDC por Bajionet
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
$ServerNumber =	$ini->read_var( "site", "ServerNumber" );
$DomainPostfix = $ini->read_var( "site", "DomainPostfix" );

$backend = "gw".$ServerNumber.$DomainPostfix;

include_once( "ezuserbb/classes/ezuser.php" );

$user =& eZUserBB::currentUser();



if ( $user )
{
	$t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ), "eztransaccion/user/intl/", $Language, "SMSTarjeta.php" );
	$t->setAllStrings();


	$dateTime  = new eZDateTime( );
	$timeStamp = $dateTime->timeStamp();
	$time =& date('H:i:s', $timeStamp );
	$date =& date('jMY', $timeStamp );


	$t->set_file( array( "SMSTarjeta_tpl" => "SMSTarjeta.tpl" ) );
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
	
//HB
	if(!empty($_POST['Cust']))
		$Cust = $_POST['Cust'];
		
	if(!empty($_POST['Accion']))
		$Accion = $_POST['Accion'];
		
	if(!empty($_POST['Pos']))
		$Pos = $_POST['Pos'];

	if(!empty($_POST['code']))
		$code = $_POST['code'];
		
	if(!empty($_POST['FrAccount']))
		$FrAccount = $_POST['FrAccount'];
		
	if(!empty($_POST['RenCap']))
		$RenCap = $_POST['RenCap'];
		
	if(!empty($_POST['Editar']))
		$Editar = $_POST['Editar'];
		
	if(!empty($_POST['Parent3']))
		$Parent3 = $_POST['Parent3'];
		
	if(!empty($_POST['DiasPzo']))
		$DiasPzo = $_POST['DiasPzo'];
		
	if(!empty($_POST['PlazaB']))
		$PlazaB = $_POST['PlazaB'];
		
	if(!empty($_POST['Pagina']))
		$Pagina = $_POST['Pagina'];
//HB

	if( empty( $_POST['Access'] ) )
	{
		$Access = "FrAc";
	}
	else {
		$Access = $_POST['Access'];
	}
	switch ( $Access )
	{
		case "FrAc":
			$particularFields = "";
			$Accion	= "Listasms";
			$particularFields = "&Cust=".urlencode($Cust)."&Accion=".urlencode($Accion)."&Pos=".urlencode($Pos);
			$tr->blog( $qki,"Notificaciones SMS", $Cust, $Accion );
			break;
		case "Confirm":
		case "Process":
		if( $_POST['NewSMS'] =="Alta" )
		{
			$Accion = "AltaSMS";
		}
		else if( $_POST['EditSMS'] == "Cambio" )
		{
			$Accion	= "EditSMS";
		}
		else if( $_POST['BajaSMS'] == "Baja" )
		{
			$Accion	= "MBajasms";
		}
		else if( $_POST['Listado'] == "Cancelar" )
		{
			$Accion	= "Listasms";
			$Access	= "FrAc";
		}

		$particularFields =	"&Passwd=".encrypt( $code, strtolower( $usr ) )."&Cust=".urlencode($Cust)."&FrAccount=".urlencode($FrAccount)."&Accion=".urlencode($Accion)."&DiasPzo=".urlencode($DiasPzo)."&RenCap=".urlencode($RenCap)."&Editar=".urlencode($Editar)."&Parent3=".urlencode($Parent3)."&PlazaB=".urlencode($PlazaB);
		$tr->blog( $qki,"Notificaciones SMS de Tarjeta", $FrAccount, $Cust, $Accion );
		break;
	}
	$transaccion_buffer	= "";
	$ret_code =	$tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=sms&Access=".urlencode($Access)."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv,	$transaccion_buffer);
    	$t->set_var( "transaccion_buffer", $transaccion_buffer.$Pagina );
    	//echo "*****particularfields accion[$Accion] access[$Access] Parent3[$Parent3] Editar[$Editar]";//psm
	$t->pparse( "output", "SMSTarjeta_tpl" );
}
else
{
	$t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ), "eztransaccion/user/intl/", $Language, "userlogin.php" );
	$t->setAllStrings();
	$t->set_file( array( "user_login_tpl" => "userlogin.tpl" ) );
	$t->set_var( "redirect_url", "/transaccion/SMSTarjeta/" );
	$t->pparse( "output", "user_login_tpl" );
}
?>
