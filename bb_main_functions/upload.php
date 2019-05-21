<?php
session_start();
include_once( "classes/ezlog.php" );
include_once( "middleware/nomina2/controller/valida_altas_bajas_nomina.php" );
include_once( "middleware/nomina2/controller/reg_desc.php" );

function muestraErrorDetalladoOMNI($resultado)
{
	$res = $resultado['nominaReturn'];//['respuesta'];

	/*********************************************************/
	//eZLog::writeNotice("Resultado..<<<<<$res>>>>>.");
	//eZLog::writeNotice( "[middleware_CallWebService.inc] =====> Response2: " .  print_r( $res, true ) . "<<<<<<");

	$xml=simplexml_load_string($res); //Cargamos obj XML

	$respError = $xml->respuesta[0]->registrosProcesadosERR; //Registros procesados con ERROR
	$respOK    = $xml->respuesta[0]->registrosProcesadosOK; //Registros procesados OK

	$error	   = $xml->respuesta->error;


//eZLog::writeNotice("Resultado.STATUS.<<<<<$respError>>>>>.");
eZLog::writeNotice("Resultado.ERROR.<<<<<$error>>>>>.");
//	eZLog::writeNotice("Resultado.OMNI.<<<<<$respError-$respOK>>>>>.");

	if ( $error != "" )
		return $error . "-E001";
	else
		return trim( $respError . "-" . $respOK);

}

$QueAccion 	 = "EmpNom";
$QueAccess	 = "Process";
$grupo 		 = $_SESSION['grupoNom'];//"5674";
$userid		 = $_SESSION['useridnom'];	//MAOS Ene2015 usuario
eZLog::writeNotice( "SESSION: " .  print_r( $_SESSION, true ) . "<<<<<<");
eZlog::writeNotice("XXXXXX...[$grupo]...XXXXXX");
$file = $_FILES['file'];
$arc  = $_FILES["file"]["tmp_name"];
$arcname  = $_FILES["file"]["name"];
eZLog::writeNotice( "En UPLOAD...2-----------------" . print_r( $arc, true ) . "|<<<<<<GPO<<<<$grupo>>>>>><" );
move_uploaded_file($_FILES["file"]["tmp_name"], '/upload_dir/' . $_FILES["file"]["name"]);

//eZLog::writeNotice("Voy a validar el archivo...");
$resultado = valida_altas_bajas_nomina( $file, $arcname, $matriz ); //Validacion de formato

if ( trim($resultado) == "00" ) {
			if (nuevoEsquemaHabilitado()) {

						include_once("middleware/nomina2/controller/xml_request_builder_ne.inc");
						$Extension = $servicio . "-" . $ciudad . "-" . $empresa. "-" . $rowno. "-" . $nombre_esperado;
						$xml_result = validaArchivoNuevoEsquema($QueAccess, $QueAccion, $file, $Extension,$grupo, $userid); // ($QueAccion=EmpNom) response en forma xml //MAOS Ene2015 usuario
			//	return;
			}
  }

	eZLog::writeNotice( "Por mandar " .  print_r( $xml_result, true ) . "<<<<<<");

$detalle = split("-",muestraErrorDetalladoOMNI($xml_result)); //Se muestra un detalle del error de la forma 10-5

 eZLog::writeNotice("detalle[x]....<<".print_r($detalle,true).">>");
 eZLog::writeNotice("detalle[0]....<<".$detalle[0].">>");
 eZLog::writeNotice("detalle[1]....<<".$detalle[1].">>");
if ( $detalle[1] == "E001"){
	$res = "<h3>".$detalle[0]."<h3>";}
else
{
	if ( $resultado != "00" ) //Mostrar error de validacion de formato
	{
		$res = reg_desc($QueAccion,substr($resultado,0,2));
		}
	else
	{
	$total = intval($detalle[0])+intval($detalle[1]);
		if ( $detalle[0] != "0" && $total == 0) //Errores
			$res = "<font color='#B7495D'>Se procesaron " . $total . " registros.";
		else if ( $detalle[0] != "0" && $total != 0) //Errores
			$res = "Se procesaron: ". $total . " registros, <font color='red'>" . $detalle[0] . "</font> con error. <a href='javascript:revIncidencias()'>Favor de revisar</a>";
		else
			$res = "<font color='#B7495D'>Se procesaron " . $total . " registros.&nbsp;</font><a href='javascript:revIncidencias()'>Detalle</a>";
	}
}

eZLog::writeNotice("El desc es....<<<$res>>><<<$total>>>");
if ( $detalle[0] != "0")
	echo "<!--respuesta--><h3>$res</h3>";//SE agrega SCP para ejecutar un settimeoyut
else
	echo "<!--respuesta--><h3>$res</h3><!--SCP-->";


?>