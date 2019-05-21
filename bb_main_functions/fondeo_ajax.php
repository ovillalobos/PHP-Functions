<?php

function alta_fondeo($user, $token, $montofondeo, $ctaorigen, $ctadestino, $rownoibpw)
{
	global $errors;

	$TokenEnc = asb_test($token, strtolower($user));

    $trans = "<?xml version='1.0'?>";
    $trans .= "<mensaje><trxn value='fon'/><accion value='FondeoEF'/><tipomsj value='rqs'/><CustID value='$user'/><Token value='". $TokenEnc ."'/>";
	$trans .= "<CtaOrigen value='" . $ctaorigen . "'/>";
	$trans .= "<CtaDestino value='" . $ctadestino . "'/>";
	$trans .= "<monto value='" . $montofondeo . "'/>";
	$trans .= "<rowno value='" . $rownoibpw . "'/>";
	$trans .= "<IP value='" . $_SERVER['REMOTE_ADDR'] . "'/></mensaje>";

    $conn = new TCPIPNexions();

    if(!$conn->connect())
    {
		$_SESSION['conn_error'] = " N&uacute;mero de error 4501 - " .  $errors[4501];
		return;
    }
	if(!$conn->send($trans))
	{
		$_SESSION['conn_error'] = " N&uacute;mero de error 4501 - " .  $errors[4501];
		return;
	}
	
	$ans = $conn->receive();

    if(trim($ans)=="")
	{
		$_SESSION['conn_error'] = " N&uacute;mero de error 4501 - " .  $errors[4501];
		return;
    }
	$xml_struct = parse_xml(trim($ans));
	if(($value = check_user($user, $xml_struct["vals"][$xml_struct["index"]["CUSTID"][0]]["attributes"]["VALUE"]) ) != "1")
	{
		eZLog::writeNotice( "...............XML REQUEST  CANCELAR PAGOS.........requestJboss[".print_r($trans,true)."].."); 
		eZLog::writeNotice( "...............XML RESPONSE CANCELAR PAGOS.........responseJboss[".print_r($ans,true)."].."); 							
		eZLog::writeNotice( "::::::::::::::ERROR AL CHECKAR DATOS DE USUARIO $user xmlCustID: ".$xml_struct["vals"][$xml_struct["index"]["CUSTID"][0]]["attributes"]["VALUE"]." ::::::::::::::::::::"); 		
	}
	if(trim($xml_struct["vals"][$xml_struct["index"]["ERRORNO"][0]]["attributes"]["VALUE"]) != "0000")
	{
		return  $xml_struct["vals"][$xml_struct["index"]["ERRORNO"][0]]["attributes"]["VALUE"] ." - " .$xml_struct["vals"][$xml_struct["index"]["ERRORNO"][0]]["attributes"]["DESC"];
	}
	return "La transacci&oacute;n se realiz&oacute; de manera exitosa con el n&uacute;mero de referencia <b>".$xml_struct["vals"][$xml_struct["index"]["REFERENCIA"][0]]["attributes"]["VALUE"]."</b>.";
	$conn->close();
	
}

include("eztransaccion/user/include/PHPLiveX-2.2.php");
include_once("eztransaccion/classes/encrypt.php");
include("httplib.php");	
include_once("classes/ezlog.php"); 
include("include/estilo.inc");
include("eztransaccion/user/include/tcpipnexions.php");
include("eztransaccion/user/include/xmlparser.inc");

session_start();

$_SESSION['user'] = $usr;
switch($_POST['Access'])
{
	case 'AltaFondeo':
		$res = alta_fondeo($usr, $_POST['Token'], $_POST['MontoFondeo'], $_POST['CtaOrigen'], $_POST['CtaDestino'], $_POST['RownoIbpw']);
		//eZLog::writeNotice("ACDP>>fondeo_ajax.php>>Resultado: ".$res);
		echo $res;
		break;
	default:
		echo "Selecci&oacute;n inv&aacute;lida";
}

?>
