<?php
		$url_array = explode( "/", $_SERVER['REQUEST_URI'] );
		ini_set( "include_path", "/var/www/html/bajio/" );	//QA la necesita, prod?

		include_once( "classes/INIFile.php" );
		$ini =& INIFile::globalINI();
		//DBA login2

		$ReImpSrv = $ini->read_var("site", "ReImpSrv");
		$ReImpDB  = $ini->read_var("site", "ReImpDB");
		$ReImpUsr = $ini->read_var("site", "ReImpUsr");
		$ReImpPwd = $ini->read_var("site", "ReImpPwd");
		//Esta linea solo para el ambiente de NAcho en produccion pasar la linea de arriba
		//echo "ICC $ReImpSrv $ReImpUsr $ReImpPwd ICC";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="es" lang="es">
	<!-- DBA Impresion de Comprobantes-->
<?php
		$url_array = explode( "/", $_SERVER['REQUEST_URI'] );

		function fechaVentana()
		{
			$mes["01"] = "enero";
			$mes["02"] = "febrero";
			$mes["03"] = "marzo";
			$mes["04"] = "abril";
			$mes["05"] = "mayo";
			$mes["06"] = "junio";
			$mes["07"] = "julio";
			$mes["08"] = "agosto";
			$mes["09"] = "septiembre";
			$mes["10"] = "octubre";
			$mes["11"] = "noviembre";
			$mes["12"] = "diciembre";
			return date( "j" ) . " de " . $mes[ date( "m" ) ] . " de " . date( "Y" );
		}

function Hex2String($hex){

    $string='';

    for ($i=0; $i < strlen($hex)-1; $i+=2){

        $string .= chr(hexdec($hex[$i].$hex[$i+1]));

    }

    return $string;

}

function hex2bin($hexdata) {
  $bindata = "";

  for ($i = 0; $i < strlen($hexdata); $i += 2) {
    $bindata .= chr(hexdec(substr($hexdata, $i, 2)));
  }

  return $bindata;
}


function decrypt( $String, $Password )
		{
			$Char		= 0;
			$Asc		= 0;
			$Encrypt	= "";
			for( $Char = 0; $Char < ( strlen( $String ) / 3 ); $Char++ )
			{
				$Asc		= substr( $String, ( $Char * 3 ), 3 );
				$Asc		= $Asc - ord( substr( $Password, ( ( $Char % strlen( $Password ) ) + 1 ), 1 ) ) - 255;
				$Encrypt	.= chr( $Asc );
			}
			return $Encrypt;
	}

    // Seqno Transaccion
    $seqno = trim($_GET['com']);
    $decrypted_text = decrypt($seqno,"bajiobajiobajiob");
	//echo "seqno....[$decrypted_text]";

	//Cust
	$cust = trim($_GET['cust']);
	$decrypted_text2 = decrypt ($cust,"bajiobajiobajiob");
	//echo "Valor del cust *$decrypted_text2*";
	//Type
	$type = trim($_GET['type']);
	$decrypted_text3 = decrypt ($type,"bajiobajiobajiob");
//echo "Valor del type *$decrypted_text3*";
	//Sub
	$sub = trim($_GET['sub']);
	$decrypted_text4 = decrypt ($sub,"bajiobajiobajiob");
//echo "Valor del sub *$decrypted_text4*";
	//Srv
	$srv = trim($_GET['s']); //srv
	$decrypted_text6 = decrypt ($srv,"bajiobajiobajiob");
//echo "Valor del srv *$decrypted_text6*";
	//Effective
	$effective = trim($_GET['effective']); //seqno
	$decrypted_text5 = decrypt ($effective,"bajiobajiobajiob");
	$fecha = explode("-",$decrypted_text5);
	//echo "Valor de la fecha---[$fecha[0]]";
	$fecha[0] = substr("$fecha[0]",2,2);
	//echo "Valor de la fecha---[$fecha[0]]";
	$fechaFinal = $fecha[0].$fecha[1].$fecha[2]; //formato yymmdd
//	echo "Valor de la fecha...[$fechaFinal]";

    /*
	$SecureServer = $ini->read_var( "site", "SecureServer" );
	$SecureServerType = $ini->read_var( "site", "SecureServerType" );
	$DomainPostfix = $ini->read_var( "site", "DomainPostfix" );
	$ServerNumber = $ini->read_var( "site", "ServerNumber" );
	$ReImpDB		= $ini->read_var( "site", "ReImpDB" );
	$ReImpUsr		= $ini->read_var( "site", "ReImpUsr" );
	$ReimpPwd		= $ini->read_var( "site", "ReImpPwd" );
	*/

   $SecureServer 	 = "secure";
   $SecureServerType = "https";
   $DomainPostfix    = ".bb.com.mx";
   $ServerNumber 	 = trim($decrypted_text6);
   $secure_site		 = $SecureServer.$ServerNumber.$DomainPostfix
   ?>

   <!--<HEAD>
       <link rel="stylesheet" type="text/css" href="https://<?php print $secure_site; ?>/impuestos/recibos.css' TYPE='text/css'"/>

   		<SCRIPT LANGUAGE='JavaScript'>

		function window_onbeforeprint()
		{
		Imprimir.style.visibility = 'hidden';
		Salir.style.visibility = 'hidden';
		}
		function window_onafterprint()
		{
		Imprimir.style.visibility = 'visible'; Salir.style.visibility = 'visible';
		}
		</SCRIPT>

	</HEAD>
   	<BODY LANGUAGE='javascript' onbeforeprint='return window_onbeforeprint()' onafterprint='return window_onafterprint()'>
	<INPUT type='button' value='Imprimir' id=Imprimir name=Imprimir onclick='window.print();'>&nbsp;
	<INPUT type='button' value=' Salir  ' id=Salir name=Salir onclick='window.close();'>
	<P>
	<CENTER>
		<IMG SRC='https://<?php print $secure_site; ?>/impuestos/LOGO_BB.GIF' WIDTH=100 HEIGHT=50 ALIGN= 'TOP' Banco del Bajio>
	</P>
	</CENTER>-->
	 <!--<H1> Hacer esto PARAMETRIZABLE **************************************
		<span style='font-size:23px;font-family:Arial;'>SPEI
    </H1>-->

<!--	<title> -->
   <?php
	//echo "ReImpDB .$ReImpDB.ReImpUsr.$ReImpUsr.ReimpPwd[$ReimpPwd]....ServerNumber..[$ServerNumber]...SecureServerType[$SecureServerType]";

	//$enlace = mysqli_connect('sql1.bb.com.mx', $ReImpUsr, $ReimpPwd);
	/*	$enlace = mysqli_connect('sql1.bb.com.mx', "bajio", "bbajiob") //REvisar la manera de obtener lso datos de las variables
		or die ("No se puede realizar la consulta");
	*/
	//http://secure1.bb.com.mx/transaccion/Imprecibo?com=14&cust=2046902&type=Cheqsi&sub=1&effective=090513&s=1
	$enlace = mysqli_connect($ReImpSrv, $ReImpUsr, $ReImpPwd ) //REvisar la manera de obtener lso datos de las variables
	//$enlace = mysqli_connect("sqlcomp.bb.com.mx", "bajio", "bbajiob") //REvisar la manera de obtener lso datos de las variables
			or die ("<br>No se puede realizar la consulta");

	//
		if ( !$enlace)	//No hay conexion con el servidor mysql
		{
			$html_de_salida  = "<BR> Su transacci&oacute;n no puede ser procesada.<BR><BR>";
			$html_de_salida .= "<BR> Favor de intentar en unos momentos. (Error 5000) <BR><BR>";
			//$html_de_salida .= "<P ALIGN=CENTER><A HREF=/transaccion/saldosTC/>Regresar</A></P>";
		    $hubo_error = 1;
		    echo "1";
			exit();
		}

		//if (! mysqli::select_db($ReImpDB) )
		if (! mysqli_select_db($enlace,$ReImpDB) )
		{
			$html_de_salida  = "<BR> Su transacci&oacute;n no puede ser procesada.<BR><BR>";
			$html_de_salida .= "<BR> Favor de intentar en unos momentos. (Error 5010)<BR><BR>";
//			$html_de_salida .= "<P ALIGN=CENTER><A HREF=/transaccion/saldosTC/>Regresar</A></P>";
            $hubo_error = 1;
            echo "2";
			exit();
		}

		//Imptos Mancomunados 13May2011 Imprimir correctamente comprobantes
		if($decrypted_text3=="Cheqsi")
		{
				$decrypted_text3="Clasica"; //YEHO 193406 29Jun2011
		}		
		else if ($decrypted_text3=="ahnom")//ATAR cambio de nombre ahnom a Nomina
		{
				$decrypted_text3="Nomina"; 		
		}
		else if ($decrypted_text3=="Ahorro")//ATAR cambio de nombre Ahorro a Nomina Basica
		{
				$decrypted_text3="NominaBa"; 		
		}				
		//Imptos Mancomunados 13May2011 Imprimir correctamente comprobantes
	   $consulta  = "SELECT rowno,cust,type,sub,seqno,html1
					  FROM
							comprobante
					  WHERE
							seqno = $decrypted_text
					  AND
					  		cust  = '$decrypted_text2'
					  AND
					  		type  = '$decrypted_text3'
					  AND
					  		sub   = '$decrypted_text4'
					  AND
					  		effective = '$fechaFinal'
					 ";

		$resultado = mysqli_query($enlace,$consulta);


		//$num_rows = mysqli_num_rows($resultado);
		$transaccion_buffer2 = array( );
		$registro  = mysqli_fetch_array($resultado, MYSQL_BOTH);

		$transaccion_buffer = " ";


		//Quitamos acentos
		$registro["html1"] = str_replace("á","&aacute;",$registro["html1"]);
		$registro["html1"] = str_replace("é","&eacute;",$registro["html1"]);
		$registro["html1"] = str_replace("í","&iacute;",$registro["html1"]);
		$registro["html1"] = str_replace("ó","&oacute;",$registro["html1"]);
		$registro["html1"] = str_replace("ú","&uacute;",$registro["html1"]);

		$transaccion_buffer = 	$registro["html1"];

		 if ( $resultado )
		{
			mysqli_free_result($resultado);
			mysqli_close( $enlace );
		}
		else
		{
			$transaccion_buffer =  "<BR> Su transacci&oacute;n no puede ser procesada.<BR><BR><BR> Favor de intentar en unos momentos. (Error 5012)<BR><BR>";
		}

		//Revisamos que si tiene contenido el html

		if ( $registro["html1"] == "")
		{
			$transaccion_buffer = "<BR> No se ha generado comprobante para este movimiento.<BR><BR>";
		}




		echo $transaccion_buffer;

?>
<?php
	$pos = strpos ($registro["html1"],"Recibo Bancario de Pago de Contribuciones");
	$posSPEI = strpos ($registro["html1"],"SPEI");
	$posTEF  = strpos ($registro["html1"],"Pago T.E.F.");
	$posAut  = strpos ($registro["html1"],"Autorizaci");

	if  ( $registro["html1"] == "") //No se pone el footer cuando no existe comprobante o es un impuesto
	{
	}
	else
	{
	?>
	<BR><hr noshade='noshade' size='4' color='#5A419c' /><p>
	<FONT SIZE=1>Av. Manuel J. Clouthier No. 508 Col. Jardines del Campestre CP 37128 Le&oacute;n, Gto.</FONT> </p>
	<p><FONT SIZE=1>Consultas y aclaraciones 01 (477) 710- 4600 o al 01 (800) 471-0400</FONT></p>
	<p><font size="1">LA TRANSFERENCIA A LA QUE HACE REFERENCIA ESTE COMPROBANTE FUE REALIZADA CONFORME A LA INFORMACI&Oacute;N E INSTRUCCIONES QUE NUESTRO CLIENTE ALIMENT&Oacute; EN EL SISTEMA DE BANCO DEL BAJ&Iacute;O, POR LO TANTO EL CLIENTE ES EL &Uacute;NICO RESPONSABLE PRESENTE Y FUTURO POR CUALQUIER ERROR U OMISI&Oacute;N EN ESTA OPERACI&Oacute;N.</font></p>
		<?php
		if ( ($posSPEI > 0 ) || ( $posTEF > 0 ) || ( $posAut > 0 && $pos === 0 ) )
			{
		?>
			<p><FONT SIZE=1>EL &Uacute;NICO COMPROBANTE OFICIAL DE ESTA TRANSACCI&Oacute;N ES EL ESTADO DE CUENTA QUE EMITE BANCO DEL</FONT>
			<FONT SIZE=1>BAJIO Y EL BANCO RECEPTOR DE ESTA TRANSACCI&Oacute;N.</FONT></p>
		<?php
		}
		else
		{
		?>
			<p><FONT SIZE=1>EL &Uacute;NICO COMPROBANTE OFICIAL DE ESTA TRANSACCI&Oacute;N ES EL ESTADO DE CUENTA QUE EMITE BANCO DEL BAJIO.</FONT></p>
		<?php
		}
		?>
	</body>
</html>
<?php } ?>