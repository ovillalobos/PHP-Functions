<?php
//
//
// Created on: <ene/2014>
//MRG activación de servicio paperles para TDC por Bajionet
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
	$t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ), "eztransaccion/user/intl/", $Language, "ECTTarjeta.php" );
	$t->setAllStrings();


	$dateTime  = new eZDateTime( );
	$timeStamp = $dateTime->timeStamp();
	$time =& date('H:i:s', $timeStamp );
	$date =& date('jMY', $timeStamp );


	$t->set_file( array( "ECTTarjeta_tpl" => "ECTTarjeta.tpl" ) );
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
		
	if(!empty($_POST['Gene']))
		$Gene = $_POST['Gene'];
		
	if(!empty($_POST['Locali']))
		$Locali = $_POST['Locali'];
		
	if(!empty($_POST['NomBen3']))
		$NomBen3 = $_POST['NomBen3'];
		
	if(!empty($_POST['Estado']))
		$Estado = $_POST['Estado'];
		
	
		
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
			//echo("MRG ENTRO FRAC");
			$particularFields = "";			
			$Accion	= "Listaect";
			$particularFields = "&Cust=".urlencode($Cust)."&Accion=".urlencode($Accion)."&Pos=".urlencode($Pos);
			$tr->blog( $qki,"Paperless TDC", $Cust, $Accion );
			//echo($particularFields);
			break;
		case "Confirm":
	
		case "Process":
		if( $_POST['NewECT'] =="Alta" )
		{
			//echo("ACCION ANTES DE: ".$Accion); //MRG CONTROL
			$Accion = "AltaECT";
			//echo("ACCION DESPUES DE: ".$Accion); //MRG CONTROL
		}
		else if( $_POST['EditEC2'] == "Cambio Contraseña" )
		{
			$Accion	= "EditEC2";
		}
		else if( $_POST['EditECT'] == "Cambio Correo Electrónico" )
		{
			$Accion	= "EditECT";
		}
		else if( $_POST['BajaECT'] == "Baja" )
		{
			$Accion	= "MBajaect";
		}
		else if( $_POST['Listado'] == "Cancelar" )
		{
			$Accion	= "Listaect";
			$Access	= "FrAc";
		}

		$particularFields =	"&Passwd=".encrypt( $code, strtolower( $usr ) )."&Cust=".urlencode($Cust)."&FrAccount=".urlencode($FrAccount)."&Accion=".urlencode($Accion)."&DiasPzo=".utf8_encode($DiasPzo)."&RenCap=".urlencode($RenCap)."&Editar=".urlencode($Editar)."&Parent3=".urlencode($Parent3)."&Gene=".urlencode($Gene)."&Estado=".urlencode($Estado)."&Locali=".utf8_encode($Locali)."&NomBen3=".utf8_encode($NomBen3);
		
		//echo($particularFields);
		$tr->blog( $qki,"Paperless TDC", $FrAccount, $Cust, $Accion );
		break;
	}
	$transaccion_buffer	= "";
	$ret_code =	$tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=ect&Access=".urlencode($Access)."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv,	$transaccion_buffer);
    	$t->set_var( "transaccion_buffer", $transaccion_buffer.$Pagina );
    	//echo "*****particularfields accion[$Accion] access[$Access]";//psm
	$t->pparse( "output", "ECTTarjeta_tpl" );
}
else
{
	$t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ), "eztransaccion/user/intl/", $Language, "userlogin.php" );
	$t->setAllStrings();
	$t->set_file( array( "user_login_tpl" => "userlogin.tpl" ) );
	$t->set_var( "redirect_url", "/transaccion/ECTTarjeta/" );
	$t->pparse( "output", "user_login_tpl" );
}
?>
