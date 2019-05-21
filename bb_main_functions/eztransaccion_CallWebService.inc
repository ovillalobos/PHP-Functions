<?php
//////////////////////////////
// Nexions > 20-jul-2012 [Track-211195] 
// NUEVO ESQUEMA DE NOMINA
//////////////////////////////

include_once( "classes/ezlog.php" );
require_once("nusoap-0.7.3/lib/nusoap.php");

function callWebServiceTDC($servicio, $paramsRequest, $paramsAdd, $paramsNodes, $wsdl) {
		
		//eZLog::writeNotice( "[middleware_CallWebServiceTDC.inc] =====> Inicio ");	MAOS Oct2013 Quitar Notice
		 //echo ("<br>........CallWebService.....WSDL:[" . $wsdl . "]....................<br>");
		 //echo ("<br>........dataCall.....WSDL:[" . $wsdl . "]....................<br>");
		 //echo ("<br>........dataCall.....servicio:[" . $servicio . "]....................<br>");
				
		$xmlTmpBody = "<?xml version='1.0'?><trxn>";
		//echo ("<br>........xmlBody1:::::::[" . $xmlTmpBody . "]....................<br>");
		//$xmlTmpBody = $xmlTmpBody.getXmlTag('', 'trxn', $paramsRequest);
		//echo ("<br>........xmlBody1:::::::[" . $xmlTmpBody . "]....................<br>");
		if (count($paramsNodes) >= 0) {			
			$result = "";
			foreach( $paramsNodes as $name=>$value )	
			{				
				$result = $result."<".$name." value=\"".$value."\"/>";
			}	
			//$result = $result." />";			
			
			$xmlTmpBody = $xmlTmpBody . $result;
		}
		$xmlTmpBody = $xmlTmpBody . "</trxn>";
		
		//echo ("<br>........xmlBody.:::::::[" . $xmlTmpBody . "]....................<br>");
		
		$param =array('request'=>$xmlTmpBody, 'securityAuthenticator'=>"<?xml version='1.0' ?><mensaje></mensaje>");
		$client= new nusoap_client($wsdl,'wsdl');
		$response = $client->call($servicio, $param);

		// echo "<br>------------------------------------------------------------------------------<br><br>";
		//echo '<h2>Request</h2><pre>' . htmlspecialchars($client->request, ENT_QUOTES) . '</pre>';
		// echo "<br>------------------------------------------------------------------------------<br><br>";
		//echo '<h2>Response</h2><pre>' . htmlspecialchars($client->response, ENT_QUOTES) . '</pre>';
		// echo "<br>------------------------------------------------------------------------------<br><br>";
		//echo '<h2>Debug</h2><pre>' . htmlspecialchars($client->getDebug(), ENT_QUOTES) . '</pre>';
		// echo "<br>------------------------------------------------------------------------------<br><br>";

		if ($client->fault)	{
			echo "No se pudo completar la operacion";
			return "Error al conectarse con el Web Service.";
		} else {
			$error = $client->getError();
			if ($error){
				echo "<br><br><b><font size =3  color =\"#646464\">En este momento no podemos atenderle.<br><br>";
				echo "Por favor intente más tarde<br><br><br></b></font>";
				//echo "==========> Error al llamar al webservice:" . $error . " wsdl[$wsdl]<BR><BR>";
				return "Error " . $error;
			}
		}

		return $response;
}
?>