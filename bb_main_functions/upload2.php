<?php
session_start();
include_once( "classes/ezlog.php" );
include_once( "middleware/nomina2/controller/valida_depositos_nomina.php" );
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


eZLog::writeNotice("Resultado.STATUS.<<<<<$respError>>>>>.");
eZLog::writeNotice("Resultado.ERROR.<<<<<$error>>>>>.");



	eZLog::writeNotice("Resultado.OMNI.<<<<<$respError-$respOK>>>>>.");

	if ( $error != "" )
		return $error . "-E001";
	else
		return trim( trim($respError) . "-" . trim($respOK));

}

$QueAccion 	 = "DepNom";
$QueAccess	 = "Process";
$grupo 		 = $_SESSION['grupoNom'];//"5674";
$userid		 = $_SESSION['useridnom'];
$file = $_FILES['file'];
$arc  = $_FILES["file"]["tmp_name"];
$arcname  = $_FILES["file"]["name"];
eZLog::writeNotice( "En UPLOAD...2" . print_r( $arc, true ) . "|<<<<<<" );
move_uploaded_file($_FILES["file"]["tmp_name"], '/upload_dir/' . $_FILES["file"]["name"]);

eZLog::writeNotice("Voy a validar el archivo...<<<$file>>>");
$resultado = valida_depositos_nomina( $file, $arcname, $matriz );

if ( trim($resultado) == "00" ) {
			if (nuevoEsquemaHabilitado()) {
						// EFR [31-ene-2013] (ini) > Grupo de Nomina Alfanumerico Nuevo Esquema
						include_once("middleware/nomina2/controller/xml_request_builder_ne.inc");
						// EFR [31-ene-2013] (fin) > Grupo de Nomina Alfanumerico Nuevo Esquema

						$Extension = $servicio . "-" . $ciudad . "-" . $empresa. "-" . $rowno. "-" . $nombre_esperado;

						// EFR [31-ene-2013] (ini) > Grupo de Nomina Alfanumerico Nuevo Esquema
						$xml_result = validaArchivoNuevoEsquema($QueAccess, $QueAccion, $file, $Extension,$grupo, $userid); // ($QueAccion=EmpNom) response en forma xml //MAOS JUL2014 Nomina
			//	return;
			}
  }


$detalle = split("-",muestraErrorDetalladoOMNI($xml_result)); //Se muestra un detalle del error de la forma 10-5
if ( $detalle[1] == "E001")
	//$res = "<h3>Por favor finalice el proceso existente de Altas.<h3>";
		$res = "<h3>".$detalle[0]."<h3>";
else
{
	if ( $resultado != "00" ) //Mostrar error de validacion de formato
	{
		$res = reg_desc($QueAccion,substr($resultado,0,2));
		}
	else
	{
	$total = intval($detalle[0])+intval($detalle[1]);
		if ( $detalle[1] != "0" ) //Errores
			$res = "Se procesaron: ". $total . " registros, <font color='red'>" . $detalle[0] . "</font> con error. <a href='javascript:revIncidenciasD()'>Favor de revisar</a>";
			//$res = "El archivo se cargo de manera correcta con <font color='red'>" . $detalle[1] . "</font> movimientos. <a href='javascript:revIncidenciasD()'>Detalle</a>";
		else
			$res = "<h3 style='color:#B7495D'>El archivo se cargo de manera correcta con <font color='red'>" . $detalle[0] . "</font> incidencias.<h3>";
	}
}

//eZLog::writeNotice("El desc es....<<<$res>>>>");
echo "<!--respuesta--><h3>$res<h3>";



?>