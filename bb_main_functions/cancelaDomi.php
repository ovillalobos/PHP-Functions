<?php
session_start();
/////////////////////////
//Creado por Nexions - HM
/////////////////////////

function ControlAcceso($menu,$usr)
{
        global $errors;

        //Mensaje - Nexions HM
        $trans = "<?xml version='1.0'?>";
        $trans .= "<mensaje><trxn value='cdm'/><accion value='ValidaOpMenu'/><tipomsj value='rqs'/><CustID value='$usr'/>";
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

if (!headers_sent())
{
        header("ETag: PUB" . time());
        header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
        header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
        header( 'Cache-Control: no-store, no-cache, must-revalidate' );
        header( 'Cache-Control: post-check=0, pre-check=0', false );
        header( 'Pragma: no-cache' );
}

include_once( "classes/INIFile.php" );
include_once( "classes/eztemplate.php" );
include_once( "classes/ezhttptool.php" );

include_once( "eztransaccion/classes/eztransaccion.php" );
include_once( "ezsession/classes/ezsession.php" );


include("eztransaccion/user/include/tcpipnexions.php");
include("eztransaccion/user/include/xmlparser.inc");
include_once("eztransaccion/user/include/general_functions.inc");
//
include_once("eztransaccion/classes/encrypt.php");


$session =& eZSession::globalSession();

if( !$session->fetch() )
    $session->store();

$ini =& $GLOBALS["GlobalSiteIni"];

$Language = $ini->read_var( "eZTransaccionMain", "Language" );
$backend = $ini->read_var( "eZTransaccionMain", "Backend" );
$ServerNumber = $ini->read_var( "site", "ServerNumber" );
$DomainPostfix = $ini->read_var( "site", "DomainPostfix" );

$backend = "gw" . $ServerNumber. $DomainPostfix;

include_once( "ezuserbb/classes/ezuser.php" );

$user =& eZUserBB::currentUser();

// DebugBreak();

if ( $user )
{

	$t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "CancelarDomiciliacion.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "CancelarDomiciliacion_tpl" => "CancelarDomiciliacion.tpl"
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

    $t->set_var( "transaccion_buffer", $transaccion_buffer );
	$usr = strtolower($usr);
    //Escritura del template
	if($_SESSION['cantreg']=="")
	        $_SESSION['cantreg'] = 10;
	print "<div><input readOnly=true size=1 style=\" border: 0px; background-color:transparent; height:2px;\" type=text id='foco'></div><div id='main'>";

//HB
	if(empty($parametros) && !empty($_POST))
		$parametros = $_POST;
//HB

	if($parametros["Access"] == "") {
		$Access = "FrAc";
	}
	else
	{$Access = $parametros["Access"];}

	$t->pparse( "output", "CancelarDomiciliacion_tpl" );

	switch ($Access)
	{
		case "FrAc":
			$result = ControlAcceso("canceldomi",$usr);

				if($result==="0000")
				{
					include("eztransaccion/user/include/canceladomi_main_form.inc");
				}
				else
					echo $result;
			break;
		case "Confirm":
			include("eztransaccion/user/include/canceladomi_confirm.inc");
			break;
		case "Process":
			include("eztransaccion/user/include/canceladomi_process.inc");
			break;
	}

}//Fin if
else
{
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "userlogin.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "user_login_tpl" => "userlogin.tpl"
        ) );

    $t->set_var( "redirect_url", "/transaccion/capturapagosarchivo/" );

    $t->pparse( "output", "user_login_tpl" );
}//Fin else



	if( !isset( $transaccion_buffer ) ) {
	    	$transaccion_buffer = "";
    }
?>

