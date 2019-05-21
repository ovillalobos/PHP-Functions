<?php
//
//
// Created on: <10/05/2013>
//PSM promociones TDC Bajionet
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
	$t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ), "eztransaccion/user/intl/", $Language, "PromoTarjeta.php" );
	$t->setAllStrings();

	$dateTime  = new eZDateTime( );
	$timeStamp = $dateTime->timeStamp();
	$time =& date('H:i:s', $timeStamp );
	$date =& date('jMY', $timeStamp );


	$t->set_file( array( "PromoTarjeta_tpl" => "PromoTarjeta.tpl" ) );
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
	
	if(!empty($_POST['FrAccount']))
        $FrAccount = $_POST['FrAccount'];
        
   if(!empty($_POST['DiasPzo']))
        $DiasPzo = $_POST['DiasPzo'];
        
   if(!empty($_POST['RenCap']))
        $RenCap = $_POST['RenCap'];
        
   if(!empty($_POST['Parent3']))
        $Parent3 = $_POST['Parent3'];
        
   if(!empty($_POST['PlazaB']))
        $PlazaB = $_POST['PlazaB'];
//HB
	if( empty( $_POST['Access'] ) )
	{
		$_POST['Access'] = "FrAc";
	}
	switch ( $_POST['Access'] )
	{
		case "FrAc":
			$particularFields = "";
			$_POST['Accion']	= "Listaptc";
			$particularFields = "&Cust=".urlencode($Cust)."&Accion=".urlencode($_POST['Accion'])."&Pos=".urlencode($_POST['Pos']);
			$tr->blog( $qki,"Promociones TDC", $Cust, $_POST['Accion'] );
			break;
		case "Confirm":
		case "Process":
		if( $_POST['NewPTC'] =="Alta" )
		{
			$_POST['Accion'] = "AltaPTC";
		}
		else if( $_POST['EditPTC'] == "Aceptar Modificando" )
		{
			$_POST['Accion']	= "EditPTC";
		}
		else if( $_POST['BajaPTC'] == "Rechazar" )
		{
			$_POST['Accion']	= "MBajaptc";
		}
		else if( $_POST['Listado'] == "Cancelar" )
		{
			$_POST['Accion']	= "Listaptc";
			$_POST['Access']	= "FrAc";
		}
		else if( $_POST['AceptPTC'] == "Aceptar" )
		{
				$_POST['Accion']	= "MAceptc";

		}

		$particularFields =	"&Passwd=".encrypt( $_POST['code'], strtolower( $usr ) )."&Cust=".urlencode($Cust)."&FrAccount=".urlencode($FrAccount)."&Accion=".urlencode($_POST['Accion'])."&DiasPzo=".urlencode($DiasPzo)."&RenCap=".urlencode($RenCap)."&Editar=".urlencode($_POST['Editar'])."&Parent3=".urlencode($Parent3)."&PlazaB=".urlencode($PlazaB);
		$tr->blog( $qki,"Promociones de Tarjeta", $FrAccount, $Cust, $_POST['Accion'] );
		break;
	}
	$transaccion_buffer	= "";
	$ret_code =	$tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=ptc&Access=".urlencode($_POST['Access'])."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv,	$transaccion_buffer);
    	$t->set_var( "transaccion_buffer", $transaccion_buffer.$Pagina );
    	//echo "*****particularfields accion[$_POST['Accion']] access[$_POST['Access']] Parent3[$Parent3] Editar[$_POST['Editar']]";//psm
    	//$promocion= $session->Variable( "r_promotdc" );
    	//echo "*****particularfields promocion[$promocion]";//psm
	$t->pparse( "output", "PromoTarjeta_tpl" );
}
else
{
	$t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ), "eztransaccion/user/intl/", $Language, "userlogin.php" );
	$t->setAllStrings();
	$t->set_file( array( "user_login_tpl" => "userlogin.tpl" ) );
	$t->set_var( "redirect_url", "/transaccion/PromoTarjeta/" );
	$t->pparse( "output", "user_login_tpl" );
}
?>
