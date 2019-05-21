<?php

	include("../../eztransaccion/user/include/reportemultipago.inc");
	include("../../eztransaccion/user/include/httplib.php");

	$HostSTMResp = $_GET['hsr'];
	$JBossHost = $_GET['jh'];
	$JBossPath = $_GET['jp'];
	$cookiesession = $_GET['cs'];
	$timeout_seconds = $_GET['ts'];
	$timeout_miliseconds = $_GET['tm'];
	$BackEndResp = $_GET['ber'];
	$BackEndResp2 = $_GET['ber2'];
	$JBossBackup = $_GET['jb'];
	$datos = explode("*", $_GET['d']);

	$particularFields = "&concepto=".urlencode($datos[3]).
		"&tipopago=".urlencode($datos[4]).
		"&status=".urlencode($datos[5]).
		"&plazo=".urlencode($datos[6]).
		"&folio=".urlencode($datos[7]).
		"&filetype=".urlencode($datos[8]).
		"&fechapagodesde=".urlencode($datos[9]).
		"&fechapagohasta=".urlencode($datos[10]).
		"&noserv=".urlencode($datos[11]).//;//050209
		"&top=".urlencode($datos[12]);//180309

/*
	$ret_code = PostToJBoss($HostSTMResp, $JBossHost, $JBossPath, $cookiesession, $timeout_seconds, $timeout_miliseconds,
				$BackEndResp, $BackEndResp2, $JBossBackup, "Trxn=".urlencode($datos[0])."&Access=".urlencode('Process')."&CustID="
				.urlencode($datos[1])."&Cadpriv=".urlencode($datos[2]).$particularFields, $transaccion_buffer, $content_type,
				$filename );

*/

//FAF

$particularFields .= "&Trxn=".urlencode($datos[0])."&Access=".urlencode('Process')."&CustID=".urlencode($datos[1])."&Cadpriv=".urlencode($datos[2]);

$header = array();

$salida = requestHTTPtoJBossMV($JBossHost, $JBossPath, $particularFields, $header);

$content_type = substr($header["content-type"],0,strpos($header["content-type"],';'));
$filename = str_replace("\"", "" ,strstr($header["content-disposition"],"\"") ); 

//$content_type = "text/plain"; 
//$filename = "prueba.txt"; 


//echo "Content type: " . $content_type . "<br>";
//echo "filename: " . $filename . "<br>";
//exit(0);
//FAF
	//if($ret_code===0)
	if(strlen($salida))
	{
		if (strstr($content_type, "text/html"))
		{
			$transaccion_buffer = $salida;

			$HostSTMResp = "";
			$JBossHost = "";
			$JBossPath = "";
			$cookiesession = "";
			$timeout_seconds = "";
			$timeout_miliseconds = "";
			$BackEndResp = "";
			$BackEndResp2 = "";
			$JBossBackup = "";
			$datos = "";
			$particularFields ="";

			 $transaccion_buffer=str_replace(chr(13),"",  $transaccion_buffer);
			 $transaccion_buffer=str_replace(chr(10),"",  $transaccion_buffer);
			echo "<script>
							parent.opener.window.document.getElementById('respuesta').setAttribute('size',".(strlen($transaccion_buffer)+30).");
							parent.opener.window.document.getElementById('respuesta').value='$transaccion_buffer'; window.close();</script>";

		}
		else
		{
/*
			header($content_type);
			header($filename);
			header('Pragma: no-cache');
			header('Expires:0');
			echo $transaccion_buffer;
*/
			//str_replace("\n","\r\n", $salida);
			sendFileToClient($salida, $content_type, $filename);
		}
	}
	else
	{

		switch($ret_code)
		{
			case 1:
				echo "<script languaje='javascript'> var mensaje='Codigo: 001 - Se ha producido un error. Intente m&aacute;s tarde.'</script>";
				break;
			case 2:
				echo "<script languaje='javascript'> var mensaje='Codigo: 002 - Se ha producido un error. Intente m&aacute;s tarde.'</script>";
				break;
			case 3:
				echo "<script languaje='javascript'> var mensaje='Codigo: 003 - Se ha producido un error. Intente m&aacute;s tarde.'</script>";
				break;
			case 4:
				echo "<script languaje='javascript'> var mensaje='Codigo: 004 - Se ha producido un error. Intente m&aacute;s tarde.'</script>";
				break;
			default:
				echo "<script languaje='javascript'> var mensaje='Codigo: 100 - Se ha producido un error. Intente m&aacute;s tarde.'</script>";	//Error desconodido

		}

		// echo "<script>
							// parent.opener.window.document.getElementById('respuesta').setAttribute('size',mensaje.length+30));
							// parent.opener.window.document.getElementById('respuesta').value=mensaje; window.close();</script>";



	}
?>
