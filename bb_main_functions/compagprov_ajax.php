<?php
include_once("eztransaccion/user/include/tcpipnexions.php");
include_once("eztransaccion/user/include/xmlparser.inc");
/* 
include_once( "eztransaccion/classes/eztransaccion.php" );
include("eztransaccion/user/include/tcpipnexions.php");
include("eztransaccion/user/include/xmlparser.inc");
include_once("eztransaccion/user/include/controles_javascript.inc");
 */
 //include_once("eztransaccion/user/include/estilo.inc");
//include("estilo.inc");

$Access = $_POST['Access'];
if(empty($Access) )
{
	$Access = "FrAc";	
}



if ($Access == "FrAc" )
{
	include_once("eztransaccion/user/include/jscalendar/calendar-blue.css");
	if ($session->variable("calendario")<1 )
	{	
		include_once("eztransaccion/user/include/jscalendar/calendar.js");	
		$session->setVariable("calendario", 1);
		$session->setVariable("acumulador", 1);
	}
	
}

	include_once("eztransaccion/user/include/compagprov_ajax.inc");


	
?>