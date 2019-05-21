<?php
//
//
// Created on: <1-Nov-2001 17:37:53 smb>
//
// This source file is part of IVA.
//
// Copyright (C) 1997-2004 Internet de Alta Calidad, S.A. de C.V.  Todos los derechos reservados. All rights reserved.
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, US
//
// DebugBreak();
global $matriz;
global $regPatronal; // dgm-yeho
include_once( "classes/INIFile.php" );
include_once( "classes/eztemplate.php" );
include_once( "classes/ezlog.php" );
include_once( "classes/ezfile.php" );
include_once( "classes/ezhttptool.php" );
include_once( "classes/sendmail.php" );

include_once( "ezuserbb/classes/ezuser.php" );
include_once( "ezfilemanager/classes/ezvirtualfile.php" );
include_once( "ezfilemanager/classes/ezvirtualfolder.php" );
include_once( "eztransaccion/classes/eztransaccion.php" );
include_once( "eztransaccion/classes/valida_adeudos.php" );
include_once( "eztransaccion/classes/valida_domiciliacion.php" );
include_once( "eztransaccion/classes/valida_pago_proveedores_mismo_banco.php" );
include_once( "eztransaccion/classes/valida_pago_proveedores_otros_bancos.php" );
include_once( "eztransaccion/classes/valida_pago_masivo_proveedores.php" );
include_once( "eztransaccion/classes/valida_depositos_nomina.php" );
include_once( "eztransaccion/classes/valida_depositos.php" );
include_once( "eztransaccion/classes/valida_altas_bajas_nomina.php" );
include_once( "eztransaccion/classes/valida_TajetasMarcaTel.php" ); // DGM 03Ene2006
include_once( "eztransaccion/classes/valida_estandarizado.php" ); // FAF - Pago de servicios
include_once( "eztransaccion/classes/valida_SUV.php" ); // DGM 26Abr2007
include_once( "ezsession/classes/ezsession.php" );
// VMV start

include("eztransaccion/user/include/tcpipnexions.php");
include("eztransaccion/user/include/xmlparser.inc");


  function SUVClon( $archivo, & $matriz, & $datosSUA )
{
  $salida = 0;
	$filename		= $archivo->FileName;
    $nombre_real	= $archivo->OriginalFileName;	//para validacion del nombre

	global $GlobalSiteIni;
	$filename = $GlobalSiteIni->SiteDir . "ezfilemanager/files/" . $filename;

//	echo "el archivo que subio esta en : $filename";
	if ( ! is_readable($filename) )
	{
/*        $datosSUA  = "<TABLE BORDER=0 WIDTH=50%>";	//se le indica que salio algo mal.. con el 1
		$datosSUA .=  "<TR><TD BGCOLOR=#CCCCFF ALIGN=RIGHT><FONT SIZE=2 FACE=ARIAL><B>Fecha Validaci&oacute;n: 	</B></FONT></TD><TD ALIGN=LEFT><FONT SIZE=2 FACE=Arial><B> </B></FONT></TD></TR>";
		$datosSUA .=  "<TR><TD BGCOLOR=#CCCCFF ALIGN=RIGHT><FONT SIZE=2 FACE=ARIAL><B> Validaci&oacute;n:</B></FONT></TD><TD ALIGN=LEFT><FONT SIZE=2 FACE=Arial><B></B></FONT></TD></TR>"." ";
		$datosSUA .=  "<TR><TD BGCOLOR=#CCCCFF ALIGN=RIGHT><FONT SIZE=2 FACE=ARIAL><B>Registro Patronal IMSS:</B></FONT></TD><TD ALIGN=LEFT><FONT SIZE=2 FACE=Arial><B></B></FONT></TD></TR>". " ";
		$datosSUA .=  "<TR><TD BGCOLOR=#CCCCFF ALIGN=RIGHT><FONT SIZE = 2 FACE = ARIAL><B>Periodo de Pago:</B></FONT></TD><TD ALIGN=LEFT><FONT SIZE=2 FACE=Arial><B></B></FONT></TD></TR>"." ";
		$datosSUA .=  "<TR><TD BGCOLOR=#CCCCFF ALIGN=RIGHT><FONT SIZE=2 FACE=ARIAL><B>Folio SUA:		</B></FONT></TD><TD ALIGN=LEFT><FONT SIZE=2 FACE=Arial><B></B></FONT></TD></TR></TABLE><BR>". " ";
*/		 //YEHO errorSUA
		$datosSUA =  "<TABLE BORDER=0 CELLSPACING=2 WIDTH=100%><TR><TH ALIGN=CENTER BGCOLOR=#5A419C COLSPAN=2><FONT COLOR=White FACE=Arial size=2>Errores encontrados durante la validaci&oacute;n</FONT></TH></TR><TR><TH BGCOLOR=#CCCCFF><FONT FACE=Arial size=2>Motivos del Rechazo:</FONT></TH></TR></TABLE>";
		$datosSUA .=  "<TABLE BORDER=0 CELLSPACING=2 WIDTH=100%>";
		$datosSUA .=  "<TR><TD>Error en el disco al tratar de leer el archivo</TD></TR></TABLE>";
		$datosSUA .= "<br><br><br><br><B><CENTER><FONT SIZE=2 FACE=Arial>El archivo no podr&aacute; ser pagado hasta la correcci&oacute;n de los errores.</FONT></CENTER></B>"."<BR><P><CENTER><INPUT TYPE=BUTTON NAME='Button2' VALUE='Regresar' LANGUAGE='JavaScript' ONCLICK='parent.history.back()'> </CENTER></P>";
		echo $datosSUA;
		$salida =  3;
    }

	$datosSUA   = "";
	$retorno	= 0;	//error por default

	// *************************************************************************************************
	/* Validaciones del nombre del archivo	 TAAMMDDD.SUA
											 01234567
		donde
			T	 = D o W			(Dos o Windows)
			AAMM = periodo de pago  (9707 = Julio de 1997)
			DDD  = digitos 3,7,9 del registro patronal
			SUA  = fijo				(Se exije al cliente que usa BajioNet utilizar 1 solo archivo) 	*/

	//if ( strtoupper(substr($nombre_real,0,1)) != "D" and strtoupper(substr($nombre_real,0,1)) != "W" )
	if ( 
		strtoupper(substr($nombre_real,0,1)) != "A" and 
		strtoupper(substr($nombre_real,0,1)) != "B" and 
		strtoupper(substr($nombre_real,0,1)) != "C" and 
		strtoupper(substr($nombre_real,0,1)) != "D" and 
		strtoupper(substr($nombre_real,0,1)) != "E" and 
		strtoupper(substr($nombre_real,0,1)) != "F" and 
		strtoupper(substr($nombre_real,0,1)) != "G" and 
		strtoupper(substr($nombre_real,0,1)) != "H" and 
		strtoupper(substr($nombre_real,0,1)) != "I" and 
		strtoupper(substr($nombre_real,0,1)) != "J" and 
		strtoupper(substr($nombre_real,0,1)) != "K" and 
		strtoupper(substr($nombre_real,0,1)) != "L" and 
		strtoupper(substr($nombre_real,0,1)) != "M" and 
		strtoupper(substr($nombre_real,0,1)) != "N" and 
		strtoupper(substr($nombre_real,0,1)) != "O" and 
		strtoupper(substr($nombre_real,0,1)) != "P" and 
		strtoupper(substr($nombre_real,0,1)) != "Q" and 
		strtoupper(substr($nombre_real,0,1)) != "R" and 
		strtoupper(substr($nombre_real,0,1)) != "S" and 
		strtoupper(substr($nombre_real,0,1)) != "T" and 
		strtoupper(substr($nombre_real,0,1)) != "U" and 
		strtoupper(substr($nombre_real,0,1)) != "V" and 
		strtoupper(substr($nombre_real,0,1)) != "W" and 
		strtoupper(substr($nombre_real,0,1)) != "X" and 
		strtoupper(substr($nombre_real,0,1)) != "Y" and 
		strtoupper(substr($nombre_real,0,1)) != "Z"
		)
	{
		/*$datosSUA = "<BR> + (W/D)";
		$datosSUA = "<BR> Comprobante de Rechazo<BR>Nombre de archivo inválido.";*/
/*		$datosSUA  = "<TABLE BORDER=0 WIDTH=50%>";	//se le indica que salio algo mal.. con el 1
		$datosSUA .=  "<TR><TD BGCOLOR=#CCCCFF ALIGN=RIGHT><FONT SIZE=2 FACE=ARIAL><B>Fecha Validaci&oacute;n: 	</B></FONT></TD><TD ALIGN=LEFT><FONT SIZE=2 FACE=Arial><B> </B></FONT></TD></TR>";
		$datosSUA .=  "<TR><TD BGCOLOR=#CCCCFF ALIGN=RIGHT><FONT SIZE=2 FACE=ARIAL><B> Validaci&oacute;n:</B></FONT></TD><TD ALIGN=LEFT><FONT SIZE=2 FACE=Arial><B></B></FONT></TD></TR>"." ";
		$datosSUA .=  "<TR><TD BGCOLOR=#CCCCFF ALIGN=RIGHT><FONT SIZE=2 FACE=ARIAL><B>Registro Patronal IMSS:</B></FONT></TD><TD ALIGN=LEFT><FONT SIZE=2 FACE=Arial><B></B></FONT></TD></TR>". " ";
		$datosSUA .=  "<TR><TD BGCOLOR=#CCCCFF ALIGN=RIGHT><FONT SIZE = 2 FACE = ARIAL><B>Periodo de Pago:</B></FONT></TD><TD ALIGN=LEFT><FONT SIZE=2 FACE=Arial><B></B></FONT></TD></TR>"." ";
		$datosSUA .=  "<TR><TD BGCOLOR=#CCCCFF ALIGN=RIGHT><FONT SIZE=2 FACE=ARIAL><B>Folio SUA:		</B></FONT></TD><TD ALIGN=LEFT><FONT SIZE=2 FACE=Arial><B></B></FONT></TD></TR></TABLE><BR>". " ";
*/		 //YEHO errorSUA		
		$datosSUA =  "<TABLE BORDER=0 CELLSPACING=2 WIDTH=100%><TR><TH ALIGN=CENTER BGCOLOR=#5A419C COLSPAN=2><FONT COLOR=White FACE=Arial size=2>Errores encontrados durante la validaci&oacute;n</FONT></TH></TR><TR><TH BGCOLOR=#CCCCFF><FONT FACE=Arial size=2>Motivos del Rechazo:</FONT></TH></TR></TABLE>";
		$datosSUA .=  "<TABLE BORDER=0 CELLSPACING=2 WIDTH=100%>";
		$datosSUA .=  "<TR><TD>El nombre del archivo no corresponde a los generados por el SUA a</TD></TR></TABLE>";
		$datosSUA .= "<br><br><br><br><B><CENTER><FONT SIZE=2 FACE=Arial>El archivo no podr&aacute; ser pagado hasta la correcci&oacute;n de los errores.</FONT></CENTER></B>"."<BR><P><CENTER><INPUT TYPE=BUTTON NAME='Button2' VALUE='Regresar' LANGUAGE='JavaScript' ONCLICK='parent.history.back()'> </CENTER></P>";
		echo $datosSUA;
		$salida =  3;
	
	}
	else if ( substr($nombre_real,3,2) < "01" or substr($nombre_real,3,2) > "15" ) //aamm   vs  aa(aamm)
	{
		//$datosSUA = "<BR> Nombre de archivo inválido Periodo de pago (AAMM) no corresponde con el contenido de la información.";
		//$datosSUA = "<BR> Comprobante de Rechazo<BR>Nombre de archivo inválido.";
//		$datosSUA  = "<TABLE BORDER=0 WIDTH=50%>";	//se le indica que salio algo mal.. con el 1
//		$datosSUA .=  "<TR><TD BGCOLOR=#CCCCFF ALIGN=RIGHT><FONT SIZE=2 FACE=ARIAL><B>Fecha Validaci&oacute;n: 	</B></FONT></TD><TD ALIGN=LEFT><FONT SIZE=2 FACE=Arial><B> </B></FONT></TD></TR>";
//		$datosSUA .=  "<TR><TD BGCOLOR=#CCCCFF ALIGN=RIGHT><FONT SIZE=2 FACE=ARIAL><B> Validaci&oacute;n:</B></FONT></TD><TD ALIGN=LEFT><FONT SIZE=2 FACE=Arial><B></B></FONT></TD></TR>"." ";
//		$datosSUA .=  "<TR><TD BGCOLOR=#CCCCFF ALIGN=RIGHT><FONT SIZE=2 FACE=ARIAL><B>Registro Patronal IMSS:</B></FONT></TD><TD ALIGN=LEFT><FONT SIZE=2 FACE=Arial><B></B></FONT></TD></TR>". " ";
//		$datosSUA .=  "<TR><TD BGCOLOR=#CCCCFF ALIGN=RIGHT><FONT SIZE = 2 FACE = ARIAL><B>Periodo de Pago:</B></FONT></TD><TD ALIGN=LEFT><FONT SIZE=2 FACE=Arial><B></B></FONT></TD></TR>"." ";
//		$datosSUA .=  "<TR><TD BGCOLOR=#CCCCFF ALIGN=RIGHT><FONT SIZE=2 FACE=ARIAL><B>Folio SUA:		</B></FONT></TD><TD ALIGN=LEFT><FONT SIZE=2 FACE=Arial><B></B></FONT></TD></TR></TABLE><BR>". " ";
		$datosSUA =  "<TABLE BORDER=0 CELLSPACING=2 WIDTH=100%><TR><TH ALIGN=CENTER BGCOLOR=#5A419C COLSPAN=2><FONT COLOR=White FACE=Arial size=2>Errores encontrados durante la validaci&oacute;n</FONT></TH></TR><TR><TH BGCOLOR=#CCCCFF><FONT FACE=Arial size=2>Motivos del Rechazo:</FONT></TH></TR></TABLE>";
		$datosSUA .=  "<TABLE BORDER=0 CELLSPACING=2 WIDTH=100%>";
		$datosSUA .=  "<TR><TD>El nombre del archivo no corresponde a los generados por el SUA w</TD></TR></TABLE>";
		$datosSUA .= "<br><br><br><br><B><CENTER><FONT SIZE=2 FACE=Arial>El archivo no podr&aacute; ser pagado hasta la correcci&oacute;n de los errores.</FONT></CENTER></B>"."<BR><P><CENTER><INPUT TYPE=BUTTON NAME='Button2' VALUE='Regresar' LANGUAGE='JavaScript' ONCLICK='parent.history.back()'> </CENTER></P>";
		 //YEHO  cambio x error del suv el bueno (segun)
		//		$datosSUA = "<BR> Nombre de archivo inválido Periodo de pago (AAMM) no corresponde con el contenido de la información.";
		echo $datosSUA;
		$salida = 3;
	}
	else if ( strtoupper(substr($nombre_real,-4)) != ".SUA" )
	{
		//$datosSUA = "<BR> Nombre de archivo inválido se esperaba extensión (.SUA).";
		//$datosSUA = "<BR> Comprobante de Rechazo<BR>Nombre de archivo inválido.";
/*		$datosSUA  = "<TABLE BORDER=0 WIDTH=50%>";	//se le indica que salio algo mal.. con el 1
		$datosSUA .=  "<TR><TD BGCOLOR=#CCCCFF ALIGN=RIGHT><FONT SIZE=2 FACE=ARIAL><B>Fecha Validaci&oacute;n: 	</B></FONT></TD><TD ALIGN=LEFT><FONT SIZE=2 FACE=Arial><B> </B></FONT></TD></TR>";
		$datosSUA .=  "<TR><TD BGCOLOR=#CCCCFF ALIGN=RIGHT><FONT SIZE=2 FACE=ARIAL><B> Validaci&oacute;n:</B></FONT></TD><TD ALIGN=LEFT><FONT SIZE=2 FACE=Arial><B></B></FONT></TD></TR>"." ";
		$datosSUA .=  "<TR><TD BGCOLOR=#CCCCFF ALIGN=RIGHT><FONT SIZE=2 FACE=ARIAL><B>Registro Patronal IMSS:</B></FONT></TD><TD ALIGN=LEFT><FONT SIZE=2 FACE=Arial><B></B></FONT></TD></TR>". " ";
		$datosSUA .=  "<TR><TD BGCOLOR=#CCCCFF ALIGN=RIGHT><FONT SIZE = 2 FACE = ARIAL><B>Periodo de Pago:</B></FONT></TD><TD ALIGN=LEFT><FONT SIZE=2 FACE=Arial><B></B></FONT></TD></TR>"." ";
		$datosSUA .=  "<TR><TD BGCOLOR=#CCCCFF ALIGN=RIGHT><FONT SIZE=2 FACE=ARIAL><B>Folio SUA:		</B></FONT></TD><TD ALIGN=LEFT><FONT SIZE=2 FACE=Arial><B></B></FONT></TD></TR></TABLE><BR>". " ";
*/		 //YEHO errorSUA		
		$datosSUA =  "<TABLE BORDER=0 CELLSPACING=2 WIDTH=100%><TR><TH ALIGN=CENTER BGCOLOR=#5A419C COLSPAN=2><FONT COLOR=White FACE=Arial size=2>Errores encontrados durante la validaci&oacute;n</FONT></TH></TR><TR><TH BGCOLOR=#CCCCFF><FONT FACE=Arial size=2>Motivos del Rechazo:</FONT></TH></TR></TABLE>";
		$datosSUA .=  "<TABLE BORDER=0 CELLSPACING=2 WIDTH=100%>";
		$datosSUA .=  "<TR><TD>El nombre del archivo no corresponde a los generados por el SUA y</TD></TR></TABLE>";
		$datosSUA .= "<br><br><br><br><B><CENTER><FONT SIZE=2 FACE=Arial>El archivo no podr&aacute; ser pagado hasta la correcci&oacute;n de los errores.</FONT></CENTER></B>"."<BR><P><CENTER><INPUT TYPE=BUTTON NAME='Button2' VALUE='Regresar' LANGUAGE='JavaScript' ONCLICK='parent.history.back()'> </CENTER></P>";
		echo $datosSUA;
		$salida = 3;
	}
	else if ( strlen($nombre_real) != 12 )
	{
		//$datosSUA = "<BR> Nombre de archivo inválido se esperaba extensión (.SUA).";
		//$datosSUA = "<BR> Comprobante de Rechazo<BR>Nombre de archivo inválido.";
/*		$datosSUA  = "<TABLE BORDER=0 WIDTH=50%>";	//se le indica que salio algo mal.. con el 1
		$datosSUA .=  "<TR><TD BGCOLOR=#CCCCFF ALIGN=RIGHT><FONT SIZE=2 FACE=ARIAL><B>Fecha Validaci&oacute;n: 	</B></FONT></TD><TD ALIGN=LEFT><FONT SIZE=2 FACE=Arial><B> </B></FONT></TD></TR>";
		$datosSUA .=  "<TR><TD BGCOLOR=#CCCCFF ALIGN=RIGHT><FONT SIZE=2 FACE=ARIAL><B> Validaci&oacute;n:</B></FONT></TD><TD ALIGN=LEFT><FONT SIZE=2 FACE=Arial><B></B></FONT></TD></TR>"." ";
		$datosSUA .=  "<TR><TD BGCOLOR=#CCCCFF ALIGN=RIGHT><FONT SIZE=2 FACE=ARIAL><B>Registro Patronal IMSS:</B></FONT></TD><TD ALIGN=LEFT><FONT SIZE=2 FACE=Arial><B></B></FONT></TD></TR>". " ";
		$datosSUA .=  "<TR><TD BGCOLOR=#CCCCFF ALIGN=RIGHT><FONT SIZE = 2 FACE = ARIAL><B>Periodo de Pago:</B></FONT></TD><TD ALIGN=LEFT><FONT SIZE=2 FACE=Arial><B></B></FONT></TD></TR>"." ";
		$datosSUA .=  "<TR><TD BGCOLOR=#CCCCFF ALIGN=RIGHT><FONT SIZE=2 FACE=ARIAL><B>Folio SUA:		</B></FONT></TD><TD ALIGN=LEFT><FONT SIZE=2 FACE=Arial><B></B></FONT></TD></TR></TABLE><BR>". " ";
*/		 //YEHO errorSUA		
		$datosSUA =  "<TABLE BORDER=0 CELLSPACING=2 WIDTH=100%><TR><TH ALIGN=CENTER BGCOLOR=#5A419C COLSPAN=2><FONT COLOR=White FACE=Arial size=2>Errores encontrados durante la validaci&oacute;n</FONT></TH></TR><TR><TH BGCOLOR=#CCCCFF><FONT FACE=Arial size=2>Motivos del Rechazo:</FONT></TH></TR></TABLE>";
		$datosSUA .=  "<TABLE BORDER=0 CELLSPACING=2 WIDTH=100%>";
		$datosSUA .=  "<TR><TD>El nombre del archivo no corresponde a los generados por el SUA x</TD></TR></TABLE>";
    $datosSUA .= "<br><br><br><br><B><CENTER><FONT SIZE=2 FACE=Arial>El archivo no podr&aacute; ser pagado hasta la correcci&oacute;n de los errores.</FONT></CENTER></B>"."<BR><P><CENTER><INPUT TYPE=BUTTON NAME='Button2' VALUE='Regresar' LANGUAGE='JavaScript' ONCLICK='parent.history.back()'> </CENTER></P>";
    echo $datosSUA;
		$salida = 3;
	}
	// **************************************************************************************************
	$fechaSUV  = date("Ymd");  //yyyymmdd  date(Ymdh:i:s);
	//$fechaSUV  = "20070320";	//esto es por si nos piden cambiar la fecha del sistema  ATAR comento


	//$comandito = $GlobalSiteIni->SiteDir . "/eztransaccion/classes/./suvC $filename $fechaSUV";
	$comandito = $GlobalSiteIni->SiteDir . "/eztransaccion/classes/./suvC $filename $fechaSUV $nombre_real";
 //echo "<br>DGM ...$comandito";
	passthru($comandito,$retorno);

	// ya ejecuto el shell de linux.. ahora leemos el archivo de salida
	$fileSalida = $filename.".bak";

	// **************************************************************************************************


  

	 if ( strlen($datosSUA) == 0 ) 	// El nombre del archivo estuvo correcto
	 {
//	 	echo("<hr>YEHOAbriendo archivo...[$fileSalida]");
	 	$gestor   = fopen ($fileSalida, "r"); //echo("Tratando de abrir archivo generado por el suv...");

	 	if ($gestor)
	 	{
	 		$datosSUA  = fgets ($gestor, filesize($fileSalida)+1);
	 		$retorno   = substr($datosSUA, 0, 1);	//el 1er caracter es resultado del suv
	 		$datosSUA  = substr($datosSUA, 1, strlen($datosSUA)); // le quito el 1er caracter
	 		fclose ($gestor);

	 		//echo "<br><br>si pude abrir el archivo de salida [$datosSUA]";

			// Borrar el archivo .bak (donde se despliegan errores) creado por la validacion suv de C Unix
		/*	if ( !unlink( $fileSalida ) ) // si no se pudo borrar.. que intente 2 veces mas
				if ( !unlink( $fileSalida ) ) // si no se pudo borrar.. que intente 2 veces mas
					unlink( $fileSalida);		*/
			if ($retorno != 0)
			{
				echo $datosSUA;
				$salida = 3; //dgm
			}
	 	}
	 	else
	 	{
	 		echo "<br><br> no pude encontrar archivo de salida del suv: $datosSUA";
	 		return -2;
			// es un error en suvC que no genero correctamente el archivo
	 	}
	 }
	// -1 no se pudo abrir archivo
	//  1 tuvo error en validacion (reportado por el suv)
	//  0 resultado ok

	/*if ( $retorno == 0 ) // todo ok se agrega al inicio de la informacion para enviarsela a ovation
			$datosSUA = $nombre_real.$datosSUA;
	para que sirve esto???		*/

  $datosSUA .=  $nombre_real;
  


//	return $retorno; //YEHO ESTA TENIA
//	return $datosSUA;  //con esto lo pinto
    return $salida; 
	//exit();
}                    //YEHO SUV ERROR


function UploadColeg($nroservicio, $usr)
{
        global $errors;

        //Mensaje - Nexions HM
        $trans = "<?xml version='1.0'?>";
        $trans .= "<mensaje><trxn value='pse'/><accion value='UploadColeg'/><tipomsj value='rqs'/><NroServ value='$nroservicio'/>";
        $trans .= "</mensaje>";
        //Mensaje

        //Conexion - Nexions HM
        $conn = new TCPIPNexions();

        if(!$conn->connect())
        {
              echo "  4501 - " .  $errors[4501];
              return "4501";
        }

        if(!$conn->send($trans))
        {
              echo "  4501 - " .  $errors[4501];
              return "4501";
        }

        $ans = $conn->receive();

        if(trim($ans)=="")
        {
              echo "  4501 - " .  $errors[4501];
              return "4501";
        }

        $xml_struct = parse_xml(trim($ans));

        if (  trim($xml_struct["vals"][$xml_struct["index"]["ERRORNO"][0]]["attributes"]["VALUE"]) != "0000" )
                return  $xml_struct["vals"][$xml_struct["index"]["ERRORNO"][0]]["attributes"]["VALUE"] ." - " .$xml_struct["vals"][$xml_struct["index"]["ERRORNO"][0]]["attributes"]["DESC"];

        $conn->close();
        //Conexion

        return "0000";
}

// VMV end

$session =& eZSession::globalSession();

if( !$session->fetch() )
    $session->store();

$user =& eZUserBB::currentUser();
//ACS 15May2009
setcookie ( "eZMyFileUpload", $DiasPzo, time() + ( 3600 * 24 * 365 ), "/",  "bb.com.mx", 0 )
   or print( "Error: could not set cookie." );
   //or print( "<script>	document.cookie='eZMyFileUpload=".$DiasPzo."';
	//					if(document.cookie.indexOf('eZMyFileUpload')==-1)
	//					{
	//						alert('Error: could not set cookie.');
	//					}
	//		</script>" );

//ACS 15May2009

if ( isSet( $NewFile ) )
{
    $Action = "New";
}
if ( isSet( $NewFolder ) )
{
    eZHTTPTool::header( "Location: /filemanager/folder/new/$FolderID" );
    exit();
}

if ( isSet( $DeleteFiles ) )
{
    $Action = "DeleteFiles";
}

if ( isSet( $Delete ) )
{
    $Action = "Delete";
}

if ( isSet( $DeleteFolders ) )
{
    $Action = "DeleteFolders";
}

if ( isSet( $Cancel ) )
{
    eZHTTPTool::header( "Location: /filemanager/list/" . $parentID );
    exit();
}

if ( isSet( $Download ) )
{
    $file = new eZVirtualFile( $FileID );

    if ( $ini->read_var( "eZTransaccionMain", "DownloadOriginalFilename" ) == "true" )
        $fileName = $file->originalFileName();
    else
        $fileName = $file->name();

    eZHTTPTool::header( "Location: /filemanager/download/$FileID/$fileName" );
    exit();
}

if ( !$user )
{
    eZHTTPTool::header( "Location: /error/403/" );
    exit();
}

$ini =& INIFile::globalINI();

$Language = $ini->read_var( "eZTransaccionMain", "Language" );
// $backend = $ini->read_var( "eZTransaccionMain", "Backend" );
$ServerNumber = $ini->read_var( "site", "ServerNumber" );
$DomainPostfix = $ini->read_var( "site", "DomainPostfix" );

$backend = "gw" . $ServerNumber. $DomainPostfix;

if ( $GLOBALS["DEBUGA"] == true ) {
	eZLog::writeNotice( "En myfileupload (Action) ->" . print_r( $Action, true ) . "|" );
	eZLog::writeNotice( "En myfileupload (backend) ->" . print_r( $backend, true ) . "|" );
}

$t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                     "eztransaccion/user/intl/", $Language, "myfileupload.php" );

//$t->set_file( "file_upload_tpl", "myfileupload.tpl" );
/*
print("<p>DGM <b>myfileupload.php</b>  entrando a Access = $Access, $servicio = $servicio</p>");
print("<p>DGM entrando a Accion = $Accion,  </p>");
*/

// ************************************************************************
// DGM I 16Nov2007 Entidad Receptora del IMSS
if ( $Accion == "ArchSUA" )
{
	$t->set_file( "file_upload_tpl", "myfileuploadSUA.tpl" );
}
else
{
	$t->set_file( "file_upload_tpl", "myfileupload.tpl" );	// EL ORIGINAL
}
// DGM F 16Nov2007 Entidad Receptora del IMSS
// ************************************************************************


$t->setAllStrings();

$t->set_block( "file_upload_tpl", "value_tpl", "value" );
$t->set_block( "file_upload_tpl", "errors_tpl", "errors" );

$t->set_block( "file_upload_tpl", "write_group_item_tpl", "write_group_item" );
$t->set_block( "file_upload_tpl", "read_group_item_tpl", "read_group_item" );

$t->set_var( "errors", "&nbsp;" );

$t->set_var( "name_value", $Name );
$t->set_var( "description_value", $Description );

$error = false;
$nameCheck = true;
$descriptionCheck = false;
$folderPermissionCheck = true;
$readCheck = true;
$fileCheck = true;
$result = 0;

$t->set_block( "errors_tpl", "error_write_permission", "write_permission" );
$t->set_var( "write_permission", "" );

$t->set_block( "errors_tpl", "error_upload_permission", "upload_permission" );
$t->set_var( "upload_permission", "" );

$t->set_block( "errors_tpl", "error_name_tpl", "error_name" );
$t->set_var( "error_name", "" );

$t->set_block( "errors_tpl", "error_file_upload_tpl", "error_file_upload" );
$t->set_var( "error_file_upload", "" );

$t->set_block( "errors_tpl", "error_description_tpl", "error_description" );
$t->set_var( "error_description", "" );

$t->set_block( "errors_tpl", "error_read_everybody_permission_tpl", "error_read_everybody_permission" );
$t->set_var( "error_read_everybody_permission", "" );

$t->set_block( "errors_tpl", "error_write_everybody_permission_tpl", "error_write_everybody_permission" );
$t->set_var( "error_write_everybody_permission", "" );

session_start();	//NXN-118013, Incorporacion de agrupacion de cuentas
//echo("0 DGM myfileupload.php Por aqui... $result, [$servicio-$Access-$Action]");

// ************************************************************************************************
// DGM I 11Jul2007 Entidad receptora del IMSS

//YEHO 181415
if ( $Access == "" and $Accion == "")	//Aqui llega cuando ya se valido el archivo y selecciono la cuenta y dio click en aceptar 
{
	$folioPOST = $_POST['folioVal'];
	$_SESSION['folioVal'] = $_POST['folioVal'];
	$folioVal = $_SESSION['folioVal'];
}
//YEHO 181415
//DBA
if ($folioPOST != "")
{
$Access = $_POST['Access'];
$Accion = $_POST['Accion'];
}
//DBA

if ( $Access == "Process" and $Accion == "ArcSUA")	//Aqui llega cuando ya se valido el archivo y selecciono la cuenta y dio click en aceptar 
{

	//echo "<br><b>ATAR</b> subo el Archivo registro patronal que viajo: $regPatronal";   

  $Access = "Confirm";
  
  $Access    = "Confirm";
	$QueAccess = "Confirm";   
	//hazPostSUA( $Access, $deAccion, $Extension);
	
if(!isset($_SESSION['num_folio']))
{
	$_SESSION['num_folio'] = $_POST['num_folio'];
}	
//  echo "<hr>DGM en myfileupload resutaldo de la 2da invocacion regPatronal = [$regPatronal]";
//  echo "<br><b>LLAMANDO AL PAGOIMSSSUA desde MYFILEUPDLOAD</b>";
  include_once( "eztransaccion/user/pagoimssSUA.php" );


	return;
}
 







// DGM F 11Jul2007 Entidad receptora del IMSS
// **********************************************************************************************
if ( $Action == "Insert" || $Action == "Update" )
{
	if ( $GLOBALS["DEBUGA"] == true ) {
		eZLog::writeNotice( "En myfileupload (Action) ->" . print_r( $Action, true ) . "|" );
		eZLog::writeNotice( "En myfileupload (recien entramos cuando el action es Insert o update).|" );
	}
    // DebugBreak();

// JAC 03AGO2010 INI
//	passthru( "/bin/bash /var/www/html/bajio/mountnfs.sh", $ret_code );
//	if ( $ret_code != 0 )
//	{
//		passthru( "/bin/bash /var/www/html/bajio/mountnfs.sh", $ret_code );
//		if ( $ret_code != 0 )
//		{
//			passthru( "/bin/bash /var/www/html/bajio/mountnfs.sh", $ret_code );
//			if ( $ret_code != 0 )
//			{
//				eZLog::writeNotice( "At myfileupload: images.bb.com.mx:/var/www/images/ezfilemanager/files/ and/or services.bb.com.mx:/var/www/data/ are not mounted.|" );
//				sendmail( $ini->read_var( "site", "ErrEMail1" ), "BajíoNET. Error Servidor " . $ini->read_var( "site", "ServerNumber" ), "Las Monturas en el Servidor www". $ini->read_var( "site", "ServerNumber" ) . ".bb.com.mx, No están Disponibles.\r\n\r\nFavor de volverlas a montar ejecutando \"umount -a\" y enseguida \"mount -a\"." );
//				sendmail( $ini->read_var( "site", "ErrEMail2" ), "BajíoNET. Error Servidor " . $ini->read_var( "site", "ServerNumber" ), "Las Monturas en el Servidor www". $ini->read_var( "site", "ServerNumber" ) . ".bb.com.mx, No están Disponibles.\r\n\r\nFavor de volverlas a montar ejecutando \"umount -a\" y enseguida \"mount -a\"." );
//			}
//		}
//	}
//	unset( $ret_code );
// JAC 03AGO2010 FIN

    if ( $folderPermissionCheck )
    {
        $folder = new eZVirtualFolder( $_POST['FolderID'] );
        // must upload to a folder
        if ( !isset( $_POST['FolderID'] ) || $_POST['FolderID'] == 0 )
        {
            $t->parse( "write_permission", "error_write_permission" );
            $error = true;
        }
        // if not write or upload to folder...
        if ( ( !eZObjectPermission::hasPermission( $folder->id(), "filemanager_folder", "w", $user ) &&
               !eZObjectPermission::hasPermission( $folder->id(), "filemanager_folder", "u", $user ) ) &&
             !eZVirtualFolder::isOwner( $user, $_POST['FolderID'] ) )
        {
            $t->parse( "write_permission", "error_write_permission" );
            $error = true;
        }
        // if update but not owner or write.
        if ( $Action == "Update" &&
            !eZObjectPermission::hasPermission( $folder->id(), "filemanager_folder", "w", $user ) &&
            !eZVirtualFolder::isOwner( $user, $_POST['FolderID'] ) )
        {
            $t->parse( "upload_permission", "error_upload_permission" );
            $error = true;
        }
		if ( $GLOBALS["DEBUGA"] == true ) {
			eZLog::writeNotice( "En myfileupload (if folderPermissionCheck).|" );
			eZLog::writeNotice( "En myfileupload (error) ->" . print_r( $error, true ) . "|" );
		}
    }

    if ( $descriptionCheck )
    {
        if ( empty( $Description ) )
        {
            $t->parse( "error_description", "error_description_tpl" );
            $error = true;
        }
		if ( $GLOBALS["DEBUGA"] == true ) {
			eZLog::writeNotice( "En myfileupload (if descriptionCheck).|" );
			eZLog::writeNotice( "En myfileupload (error) ->" . print_r( $error, true ) . "|" );
		}
    }

    if ( $fileCheck )
    {
        $file = new eZFile();
        if ( $file->getUploadedFile( "userfile" ) == false )
        {
            if ( $Action == "Insert" )
            {
                $error = true;
                $t->parse( "error_file_upload", "error_file_upload_tpl" );
				$matriz[] = "-2$Debe proporcionar un nombre de archivo";
				$result = "-2";
            }
        }
		if ( $GLOBALS["DEBUGA"] == true ) {
			eZLog::writeNotice( "En myfileupload (if fileCheck).|" );
			eZLog::writeNotice( "En myfileupload (file) ->" . print_r( $file, true ) . "|" );
			eZLog::writeNotice( "En myfileupload (error) ->" . print_r( $error, true ) . "|" );
		}
    }

    if ( $error )
    {
        $t->parse( "errors", "errors_tpl" );
        $tr = new eZTransaccion( );
        $tr->reportaErrorDetallado( $result, $QueAccess, $QueAccion , $matriz);
		return ;

    }
}

if ( $Action == "Insert" && !$error )
{
	if ( $GLOBALS["DEBUGA"] == true ) {
		eZLog::writeNotice( "En myfileupload (Action) ->" . print_r( $Action, true ) . "|" );
		eZLog::writeNotice( "En myfileupload (recien entramos cuando el action es Insert  y no hay errores).|" );
	}

    // DebugBreak();
    $uploadedFile = new eZVirtualFile();
    $uploadedFile->setDescription( $Description );
    $uploadedFile->setUser( $user );
    $uploadedFile->setFile( $file );
    // $uploadedFile->setFile( &$file );

	if ( $GLOBALS["DEBUGA"] == true ) {
		eZLog::writeNotice( "En myfileupload (uploadedFile) ->" . print_r( $uploadedFile, true ) . "|" );
	}

    if ( empty( $Name ) )
        $Name = $uploadedFile->originalFileName();

    if ( !$ini->read_var( "eZTransaccionMain", "DownloadOriginalFilename" ) == "true" )
    {
        $extension = strrchr( $uploadedFile->originalFileName(), "." );
        if ( strrchr( $Name, "." ) != $extension )
            $Name .= $extension;
    }

    $uploadedFile->setName( $Name );
    $uploadedFile->store();
    $FileID = $uploadedFile->id();
    $folder = new eZVirtualFolder( $_POST['FolderID'] );

    if ( eZObjectPermission::hasPermission( $FolderID, "filemanager_folder", 'w' ) ||
         eZVirtualFolder::isOwner( $user, $FolderID ) )
    {
        changePermissions( $FileID, $ReadGroupArrayID, 'r' );
        changePermissions( $FileID, $WriteGroupArrayID, 'w' );
    }
    else // user had upload permission only, change ownership, set special rights..
    {
//        eZObjectPermission::removePermissions( $FileID, "filemanager_file", "wr" ); // no write/read
//        eZObjectPermission::setPermission( -1, $FileID, "filemanager_file", 'r' );
//        $uploadedFile->setUser( $folder->user() );

        changePermissions( $FileID, $ReadGroupArrayID, 'r' );
        changePermissions( $FileID, $WriteGroupArrayID, 'w' );

        $uploadedFile->store();
    }

    $folder->addFile( $uploadedFile );

    list( $servicio, $ciudad, $empresa, $rowno, $nombre_esperado ) = explode("-", $_COOKIE['eZMyFileUpload']);

// VMV start
	if (trim($nombre_esperado) == si ) {					// Valesp en bill
		global $GlobalSiteIni;
  		$filename = $GlobalSiteIni->SiteDir . "ezfilemanager/files/" . $uploadedFile->FileName;
		$salida = system( "/var/www/html/bajio/eztransaccion/classes/cargaest $filename", $result );
	}
// VMV end
else
	{
	$ContenidoIntl = "";
    switch( $servicio ) {
            case "colegiaturas" :
            	if ( $GLOBALS["DEBUGA"] == true ) {
					eZLog::writeNotice( "En myfileupload (servicio) ->" . print_r( $servicio, true ) . "|" );
					eZLog::writeNotice( "En myfileupload (uploadedFile) ->" . print_r( $uploadedFile, true ) . "|" );
					eZLog::writeNotice( "En myfileupload (nombre_esperado) ->" . print_r( $nombre_esperado, true ) . "|" );
				}
				$result = valida_adeudos( $uploadedFile, $nombre_esperado , $matriz);
				$uploadedFile->OriginalFileName = $nombre_esperado;
				$ucret = UploadColeg($numserv, $result);				// VMV
				$mt = "mramirez@bb.com.mx";
                break;
            case "domi" :
            	if ( $GLOBALS["DEBUGA"] == true ) {
					eZLog::writeNotice( "En myfileupload (servicio) ->" . print_r( $servicio, true ) . "|" );
					eZLog::writeNotice( "En myfileupload (uploadedFile) ->" . print_r( $uploadedFile, true ) . "|" );
					eZLog::writeNotice( "En myfileupload (nombre_esperado) ->" . print_r( $nombre_esperado, true ) . "|" );
				}
				$result = valida_domiciliacion( $uploadedFile, $nombre_esperado, $matriz );
				$uploadedFile->OriginalFileName = $nombre_esperado;
				$mt = "mramirez@bb.com.mx";
                break;
           case "pgprov1" :
            	$servicio = "pgprov";
            	if ( $GLOBALS["DEBUGA"] == true ) {
					eZLog::writeNotice( "En myfileupload (servicio) ->" . print_r( $servicio, true ) . "|" );
					eZLog::writeNotice( "En myfileupload (uploadedFile) ->" . print_r( $uploadedFile, true ) . "|" );
					eZLog::writeNotice( "En myfileupload (nombre_esperado) ->" . print_r( $nombre_esperado, true ) . "|" );
				}
            	$result = valida_pago_proveedores_mismo_banco( $uploadedFile, $nombre_esperado, $matriz );
				//$result = valida_depositos_nomina( $uploadedFile, $nombre_esperado );
				//$result = valida_altas_bajas_nomina( $uploadedFile, $nombre_esperado );
				$uploadedFile->OriginalFileName = $nombre_esperado;
				$mt = "mramirez@bb.com.mx";
                break;
            case "pgprov2" :
            	$servicio = "pgprov";
            	if ( $GLOBALS["DEBUGA"] == true ) {
					eZLog::writeNotice( "En myfileupload (servicio) ->" . print_r( $servicio, true ) . "|" );
					eZLog::writeNotice( "En myfileupload (uploadedFile) ->" . print_r( $uploadedFile, true ) . "|" );
					eZLog::writeNotice( "En myfileupload (nombre_esperado) ->" . print_r( $nombre_esperado, true ) . "|" );
				}
            	$result = valida_pago_proveedores_otros_bancos( $uploadedFile, $nombre_esperado, $matriz );
				$uploadedFile->OriginalFileName = $nombre_esperado;
				$mt = "mramirez@bb.com.mx";
                break;

           case "pgprov3" :
            	$servicio = "pgprov";
            	if ( $GLOBALS["DEBUGA"] == true ) {
					eZLog::writeNotice( "En myfileupload (servicio) ->" . print_r( $servicio, true ) . "|" );
					eZLog::writeNotice( "En myfileupload (uploadedFile) ->" . print_r( $uploadedFile, true ) . "|" );
					eZLog::writeNotice( "En myfileupload (nombre_esperado) ->" . print_r( $nombre_esperado, true ) . "|" );
				}
            	$result = valida_pago_masivo_proveedores( $uploadedFile, $nombre_esperado, $matriz );
				$uploadedFile->OriginalFileName = $nombre_esperado;
				$mt = "mramirez@bb.com.mx";
                break;
           case "nomina1" :
            	$servicio = "nomina";
            	//echo "<em>$servicio</em> "  . var_dump ( $uploadedFile ) . "<br>\n";
            	if ( $GLOBALS["DEBUGA"] == true ) {
					eZLog::writeNotice( "En myfileupload (servicio) ->" . print_r( $servicio, true ) . "|" );
					eZLog::writeNotice( "En myfileupload (uploadedFile) ->" . print_r( $uploadedFile, true ) . "|" );
					eZLog::writeNotice( "En myfileupload (nombre_esperado) ->" . print_r( $nombre_esperado, true ) . "|" );
				}
            	$result = valida_depositos_nomina( $uploadedFile, $nombre_esperado, $matriz );
				$uploadedFile->OriginalFileName = $nombre_esperado;
				$mt = "mramirez@bb.com.mx";
                break;
            case "nomina2" :
            	$servicio = "nomina";
            	//echo "<em>$servicio</em> "  . var_dump ( $uploadedFile ) . "<br>\n";
            	if ( $GLOBALS["DEBUGA"] == true ) {
					eZLog::writeNotice( "En myfileupload (servicio) ->" . print_r( $servicio, true ) . "|" );
					eZLog::writeNotice( "En myfileupload (uploadedFile) ->" . print_r( $uploadedFile, true ) . "|" );
					eZLog::writeNotice( "En myfileupload (nombre_esperado) ->" . print_r( $nombre_esperado, true ) . "|" );
				}
            	$result = valida_altas_bajas_nomina( $uploadedFile, $nombre_esperado,$matriz );
				$uploadedFile->OriginalFileName = $nombre_esperado;
				$mt = "mramirez@bb.com.mx";
                break;
			case "suscripciones":
				if ( $GLOBALS["DEBUGA"] == true ) {
					eZLog::writeNotice( "ICC Suscripciones, posible arch Internacional ini empresa->" . print_r( substr( $empresa, 0, 6 ), true ) . "|" );
                }

				// ************************************************************************
				// DGM 03Ene2006 Validacion de formato de Tarjetas MarcaTel
				if ($empresa == "tarjetasmarcatel")
				{
					if ( $GLOBALS["DEBUGA"] == true ) {
						eZLog::writeNotice( "En myfileupload (servicio) ->" . print_r( $servicio, true ) . "|" );
						eZLog::writeNotice( "En myfileupload (uploadedFile) ->" . print_r( $uploadedFile, true ) . "|" );
						eZLog::writeNotice( "En myfileupload (nombre_esperado) ->" . print_r( $nombre_esperado, true ) . "|" );
					}

					$result = valida_adeudos2( $uploadedFile, $matriz );
				}
				// ************************************************************************

				if ( substr( $empresa, 0, 6 ) == "sollc_" )
				{
					$empresa_short = substr( $empresa, 6, strlen( $empresa )-6 );
					$mt = "produccion@bb.com.mx";
//LVPR I 8Feb08  envío parametrizado de correos por solicitud de cartas  de crédito
                    $LC_EMails = $ini->read_var( "site", "LCEMails" );
                    $ContenidoIntl = "\n    Favor de enviar al area de Internacional ( " . $LC_EMails . " )\n"
//LVPR F 8Feb08  envío parametrizado de correos por solicitud de cartas  de crédito
//					$ContenidoIntl = "\n    Favor de enviar al area de Internacional ( RJIMENEZ@bb.com.mx;fgonzalez@bb.com.mx;mhernandez@bb.com.mx )\n"
						                ."el archivo que se encuentra en:\n\n"
										."   \"\\\\Services\\var/www/data/$servicio/$ciudad/$empresa/paso/$uploadedFile->OriginalFileName\"\n\n"
					                    ."es un archivo de $empresa_short, en $ciudad para \"Solicitud Electronica \n"
										."de Carta de Credito Internacional\"\n\n\n"
										."Gracias.\n";
				}
                else
				{
				    $mt = "helpdesk@bb.com.mx";
				}
				break;

			// ************************************************************************
			// DGM I 17May2007 Entidad Receptora del IMSS
			case "pagoimssSUA":

				if ( $GLOBALS["DEBUGA"] == true ) {
					eZLog::writeNotice( "En myfileupload (servicio) ->pagoimssSUA|" );
					eZLog::writeNotice( "En myfileupload (uploadedFile) ->" . print_r( $uploadedFile, true ) . "|" );
					eZLog::writeNotice( "En myfileupload (nombre_esperado) ->" . print_r( $nombre_esperado, true ) . "|" );
				}

				$QueAccion = "ArchSUA";
				$QueAccess = "FrAc";

				// Se realiza la validación del archivo y si es exitosa, me regresa los datos
				// del archivo en la variable $datosSUA
				$datosSUA = "";

			//	echo "Inicia validacion suv... QueAccion = $QueAccion, QueAccess = $QueAccess";
			//	$result = SUV( $uploadedFile, &$matriz, $datosSUA );		//valida_suv.php
			 //   include_once( "eztransaccion/user/errorSUA.php" ); //ERROR SUA
$result =  SUVClon( $uploadedFile, $matriz, $datosSUA );		//esta funcion esta arriba
//				echo("<BR>2 DGM myfileupload.php linea 630 Por aqui... $result, [$servicio-$QueAccess-$QueAccion] DATOSSUA $datosSUA");
	   //    ECHO "YEHO $result"; YA SE PINTA ADENTRO DE LA FUNCION suvclon
	      // $datosSUA = ""; //PARA q nno muestre leyenda de erroress YEHO PROBAR
          if ( $result == 3 )
          {
//          $datosSUA .= $nombre_real;
             return $datosSUA;   //YEHO ERROR SUA       si lo quito muestra el correcto y si lo pongo muestra el incorrecto
          }
          else
          {
          }
//    	return $datosSUA;   //YEHO ERROR SUA       si lo quito muestra el correcto y si lo pongo muestra el incorrecto
				break;
			// DGM F 17May2007 Entidad Receptora del IMSS
			// ************************************************************************

			default:
				$mt = "helpdesk@bb.com.mx";
				break;
	}
}//fin
	if ( $GLOBALS["DEBUGA"] == true ) {
	        eZLog::writeNotice( "En myfileupload (result) ->" . print_r( $result, true ) . "|" );
	}

    $tr = new eZTransaccion( );

    if ( $result != 0 ) {
      	// ************************************************************************
  		// DGM I 29May2007 Entidad Receptora IMSS
  
  		if ( $servicio == "pagoimssSUA" )
  		{
    //		include_once( "eztransaccion/user/errorSUA.php" );
   // $result=4;
  			$tr->reportaErrorSUA( $result, $QueAccess, $QueAccion , $datosSUA );
     // $tr->reportaErrorDetallado( $result, $QueAccess, $QueAccion , &$matriz);
      //   $result = 0;
  		}
  		else
  		{
  		// DGM F 29May2007 Entidad Receptora IMSS
  		// ************************************************************************
  			$tr->reportaErrorDetallado( $result, $QueAccess, $QueAccion , $matriz);
              $uploadedFile->delete();
  		} // dgm
		// $uploadedFile->delete(); //dgm esto borra el archivo subido con error

		//exit ();
       // return; conn este funciona en ambiente CRA YEHO 
/*            $tr->reportaErrorDetallado( $result, $QueAccess, $QueAccion , &$matriz);
            $uploadedFile->delete();*/
            // exit ();
            return;
    }

	// DebugBreak();
	$lines = array();
	$errco = 0;
	global $GlobalSiteIni;
	$filename = $GlobalSiteIni->SiteDir . "ezfilemanager/files/" . $uploadedFile->FileName;
	// JAC 03AGO2010 INI
	$base_dir = "";
	$eliminaServices = $ini->read_var( "site", "EliminaServices" );	// Si o No.

	
	if (  strtoupper(trim($eliminaServices)) == "SI" ) {	// La variable se encuentra definida en site.ini
		$base_dir .= "/var/www/";
		if ( $servicio == "pgprov" || $servicio == "nomina" ) {
			$base_dir .= "repo_" . $servicio;
			// passthru( "/bin/bash /var/www/html/bajio/mountnfs_". $servicio .".sh", $ret_code );
			passthru( "/bin/bash /usr/bin/mountnfs_" . $servicio . ".sh", $ret_code );
				
			
			if ( $ret_code != 0 )
			{
				eZLog::writeNotice( "At myfileupload: images.bb.com.mx:/var/www/images/ezfilemanager/files/ and/or ". $backend .":/var/www/". $servicio ."/ are not mounted.|" );
				sendmail( $ini->read_var( "site", "ErrEMail1" ), "BajíoNET. Error Servidor " . $ini->read_var( "site", "ServerNumber" ), "Las Monturas en el Servidor www". $ini->read_var( "site", "ServerNumber" ) . ".bb.com.mx, No están Disponibles.\r\n\r\nFavor de volverlas a montar ejecutando \"umount -a\" y enseguida \"mount -a\"." );
				sendmail( $ini->read_var( "site", "ErrEMail2" ), "BajíoNET. Error Servidor " . $ini->read_var( "site", "ServerNumber" ), "Las Monturas en el Servidor www". $ini->read_var( "site", "ServerNumber" ) . ".bb.com.mx, No están Disponibles.\r\n\r\nFavor de volverlas a montar ejecutando \"umount -a\" y enseguida \"mount -a\"." );
				// NO PUDO MONTAR EL DIRECTORIO COMPARTIDO DE NOMINA Y/O PGPROV
				eZLog::writeNotice( "En myfileupload (ret_code) ->" . print_r( $ret_code, true ) . "<- dir->" . $base_dir );
				$result = "xxProblemas con services (mount). Carpeta ->"  . $base_dir . "/<-";
				$tr->reportaError( $result, $QueAccess, $QueAccion );
				$uploadedFile->delete();
				return ;
			}
			unset( $ret_code );
		} else {
			$base_dir .= "data";
			// passthru( "/bin/bash /var/www/html/bajio/mountnfs_data.sh", $ret_code );
			passthru( "/bin/bash /usr/bin/mountnfs_data.sh", $ret_code );
		
			if ( $ret_code != 0 )
			{
				eZLog::writeNotice( "At myfileupload: images.bb.com.mx:/var/www/images/ezfilemanager/files/ and/or ". $backend .":/var/www/data/ are not mounted.|" );
				sendmail( $ini->read_var( "site", "ErrEMail1" ), "BajíoNET. Error Servidor " . $ini->read_var( "site", "ServerNumber" ), "Las Monturas en el Servidor www". $ini->read_var( "site", "ServerNumber" ) . ".bb.com.mx, No están Disponibles.\r\n\r\nFavor de volverlas a montar ejecutando \"umount -a\" y enseguida \"mount -a\"." );
				sendmail( $ini->read_var( "site", "ErrEMail2" ), "BajíoNET. Error Servidor " . $ini->read_var( "site", "ServerNumber" ), "Las Monturas en el Servidor www". $ini->read_var( "site", "ServerNumber" ) . ".bb.com.mx, No están Disponibles.\r\n\r\nFavor de volverlas a montar ejecutando \"umount -a\" y enseguida \"mount -a\"." );
				eZLog::writeNotice( "En myfileupload (ret_code) ->" . print_r( $ret_code, true ) . "<- dir->" . $base_dir );
				$result = "xxProblemas con services (mount). Carpeta ->"  . $base_dir . "/<-";
				$tr->reportaError( $result, $QueAccess, $QueAccion );
				$uploadedFile->delete();
				return ;
			}
			unset( $ret_code );
		}
		$base_dir .= "/";
	} else {
		$base_dir = "/var/www/data/";
		// passthru( "/bin/bash /var/www/html/bajio/mountnfs_data.sh", $ret_code );
		passthru( "/bin/bash /usr/bin/mountnfs.sh", $ret_code );
	
		if ( $ret_code != 0 )
		{
			eZLog::writeNotice( "At myfileupload: images.bb.com.mx:/var/www/images/ezfilemanager/files/ and/or ". $backend .":/var/www/data/ are not mounted.|" );
			sendmail( $ini->read_var( "site", "ErrEMail1" ), "BajíoNET. Error Servidor " . $ini->read_var( "site", "ServerNumber" ), "Las Monturas en el Servidor www". $ini->read_var( "site", "ServerNumber" ) . ".bb.com.mx, No están Disponibles.\r\n\r\nFavor de volverlas a montar ejecutando \"umount -a\" y enseguida \"mount -a\"." );
			sendmail( $ini->read_var( "site", "ErrEMail2" ), "BajíoNET. Error Servidor " . $ini->read_var( "site", "ServerNumber" ), "Las Monturas en el Servidor www". $ini->read_var( "site", "ServerNumber" ) . ".bb.com.mx, No están Disponibles.\r\n\r\nFavor de volverlas a montar ejecutando \"umount -a\" y enseguida \"mount -a\"." );

			eZLog::writeNotice( "En myfileupload (ret_code) ->" . print_r( $ret_code, true ) . "<- dir->" . $base_dir );
			$result = "xxProblemas con services (mount). Carpeta ->"  . $base_dir . "/<-";
			$tr->reportaError( $result, $QueAccess, $QueAccion );
			$uploadedFile->delete();
			return ;
		}
		unset( $ret_code );
		
	}
//  echo "VARIABLES [$datosSUA]";
  $regpat =  substr($datosSUA,0,11);
  $folsua =  substr($datosSUA,30,6);
  $folsua=(string)(int)$folsua;

  $perpag =  substr($datosSUA,24,6);
//	if ( !is_dir( "/var/www/data/" . $servicio . "/" . $ciudad . "/" . $empresa . "/paso/" ) )
	if ( !is_dir( "/var/www/data/SUA/" . date("Ymd") . "/" .  $regpat . "_" . $folsua . "_" . $perpag .  "/" ) )
	{
		umask( 0000 );
		if ( !is_dir( "/var/www/data/SUA/" . date("Ymd") . "/" ) )
		{
				  
			//$res = mkdir( "/var/www/data/" . $servicio . "/" );
			$res = mkdir( "/var/www/data/SUA/" .  date("Ymd") . "/" );
			if ( !$res )
			{
				eZLog::writeNotice( "En myfileupload (res) ->" . print_r( $res, true ) . "<- dir->" . "/var/www/data/SUA" . date("Ymd") . "/|" );
				$result = "xxProblemas con services (mkdir). Carpeta ->" . "/var/www/data/" . date("Ymd") . "/<-";
				$tr->reportaError( $result, $QueAccess, $QueAccion );
				$uploadedFile->delete();
				return ;
			}
		}
		//YEHO  NECESito  el regpatronal folio y eso
		//if ( !is_dir( "/var/www/data/" . $servicio . "/" . $ciudad . "/" ) )
		if ( !is_dir( "/var/www/data/SUA/" . date("Ymd") . "/" .  $regpat . "_" . $folsua . "_" . $perpag .  "/" ) )
		{
			//$res = mkdir( "/var/www/data/" . $servicio . "/" . $ciudad . "/" );
  	   $res = mkdir( "/var/www/data/SUA/" .  date("Ymd") . "/" .  $regpat . "_" . $folsua . "_" . $perpag .  "/");
			if ( !$res )
			{
				eZLog::writeNotice( "En myfileupload (res) ->" . print_r( $res, true ) . "<- dir->" . "/var/www/data/" . date("Ymd") . "/" .  $regpat . "_" . $folsua . "_" . $perpag. "/|" );
				$result = "xxProblemas con services (mkdir). Carpeta ->". "/var/www/data/" . date("Ymd") . "/" .  $regpat . "_" . $folsua . "_" . $perpag . "/<-";
				$tr->reportaError( $result, $QueAccess, $QueAccion );
				$uploadedFile->delete();
				return ;
			}
		}
	/*	if ( !is_dir( "/var/www/data/" . $servicio . "/" . $ciudad . "/" . $empresa . "/" ) )
		{
			//$res = mkdir( "/var/www/data/" . $servicio . "/" . $ciudad . "/" . $empresa . "/" );
			$res = mkdir( "/var/www/data/SUA" .  date("Ymd") . "/paso/" );
		  echo "YEHO creando carpeta2";
			if ( !$res )
			{
				eZLog::writeNotice( "En myfileupload (res) ->" . print_r( $res, true ) . "<- dir->" . "/var/www/data/" . $servicio . "/" . $ciudad . "/" . $empresa . "/|" );
				$result = "xxProblemas con services (mkdir). Carpeta ->" . "/var/www/data/" . $servicio . "/" . $ciudad . "/" . $empresa . "/<-";
				$tr->reportaError( $result, $QueAccess, $QueAccion );
				$uploadedFile->delete();
				return ;
			}
		}  */ //YEHO no se usa ya otro nivel de carpeta a menos q se meta lo de la carpeta paso
		if ( $GLOBALS["DEBUGA"] == true ) {
				eZLog::writeNotice( "En myfileupload (exec_line) ->" . print_r( $exec_line, true ) . "|" );
		}
//		$res = mkdir( "/var/www/data/" . $servicio . "/" . $ciudad . "/" . $empresa. "/paso/" );
	//	$res = mkdir( "/var/www/data/SUA" .  date("Ymd") . "/paso/" );    creo q va descomentado
//		echo "YEHO creando carpeta";
//		$res = copy( $filename, "/var/www/data/SUA/" . date("Ymd")  . "/paso/" . $uploadedFile->OriginalFileName );
		if ( !$res )
		{
			if ( $debug ) {
				echo "myFailResult: " . $res . "<br>\n\n";
			}
			eZLog::writeNotice( "En myfileupload (res) ->" . print_r( $res, true ) . "<- dir->" . "/var/www/data/" . $servicio . "/" . $ciudad . "/" . $empresa. "/paso/|" );
			$result = "xxProblemas con services (mkdir). Carpeta ->"  . "/var/www/data/" . $servicio . "/" . $ciudad . "/" . $empresa. "/paso/<-";
			$tr->reportaError( $result, $QueAccess, $QueAccion );
			$uploadedFile->delete();
			return ;
		}
		else
		{
			// chgrp( "/var/www/data/" . $servicio . "/" . $ciudad . "/" . $empresa. "/paso/", "nfsnobody" );
		}
	}

    $lines = array();
    $errco = 0;
    //$exec_line = "cp $filename /var/www/data/$servicio/$ciudad/$empresa/paso/$uploadedFile->OriginalFileName 2>&1";
    //echo "<em>$exec_line</em> "  . var_dump ( $exec_line ) . "<br>\n";
    //$exec_line = "scp $filename root@192.1.3.216:/var/www/data/$servicio/$ciudad/$empresa/paso/$uploadedFile->OriginalFileName 2>&1";
    //$res = myExec( $exec_line, $lines, $errco);
	// $res = copy( $filename, "/var/www/data/" . $servicio . "/" . $ciudad . "/" . $empresa . "/paso/" . $uploadedFile->OriginalFileName );
	//$res = copy( $filename, $base_dir . $servicio . "/" . $ciudad . "/" . $empresa . "/paso/" . $uploadedFile->OriginalFileName );
  $res = copy( $filename, "/var/www/data/SUA/" . date("Ymd") . "/" .  $regpat . "_" . $folsua . "_" . $perpag .  "/" .$uploadedFile->OriginalFileName );

	$reply = "";	// Defino la variable con longitud 0.
	$reply = $ini->read_var( "site", "Data2" );
	
	if ( strlen( trim( $reply ) ) != 0 ) {	// La variable se encuentra definida en site.ini
		// passthru( "/bin/bash /var/www/html/bajio/mountnfs_data.sh", $ret_code );
		passthru( "/bin/bash /usr/bin/mountnfs.sh", $ret_code );
		if ( $ret_code != 0 )
		{
			eZLog::writeNotice( "At myfileupload: images.bb.com.mx:/var/www/images/ezfilemanager/files/ and/or ". $backend .":/var/www/data/ are not mounted.|" );
			sendmail( $ini->read_var( "site", "ErrEMail1" ), "BajíoNET. Error Servidor " . $ini->read_var( "site", "ServerNumber" ), "Las Monturas en el Servidor www". $ini->read_var( "site", "ServerNumber" ) . ".bb.com.mx, No están Disponibles.\r\n\r\nFavor de volverlas a montar ejecutando \"umount -a\" y enseguida \"mount -a\"." );
			sendmail( $ini->read_var( "site", "ErrEMail2" ), "BajíoNET. Error Servidor " . $ini->read_var( "site", "ServerNumber" ), "Las Monturas en el Servidor www". $ini->read_var( "site", "ServerNumber" ) . ".bb.com.mx, No están Disponibles.\r\n\r\nFavor de volverlas a montar ejecutando \"umount -a\" y enseguida \"mount -a\"." );
		}
		unset( $ret_code );

	//	$res = copy ( $filename, $reply . $servicio. "/" . $cuidad . "/" . $empresa . "/paso/" . $uploadedFile->OriginalFileName );
	   $res = copy( $filename, "/var/www/data/SUA/" . date("Ymd") . "/" .  $regpat . "_" . $folsua . "_" . $perpag .  "/" .$uploadedFile->OriginalFileName );

	}
    //echo "<em>$res</em> "  . var_dump ( $res ) . "<br>\n";
	if ( $GLOBALS["DEBUGA"] == true ) {
	        eZLog::writeNotice( "En myfileupload (exec_line) ->" . print_r( $exec_line, true ) . "|" );
	}
    //if ( $errco != 0 ) {
	if ( !$res ) {
        if ( $debug ) {
            echo "myFailErrco: " . $errco . "<br>\n";
            echo "myFailResult: " . $res . "<br>\n\n";
        }
		//if ( $GLOBALS["DEBUGA"] == true ) {
			//eZLog::writeNotice( "En myfileupload (errco) ->" . print_r( $errco, true ) . "|" );
		eZLog::writeNotice( "En myfileupload (res) ->" . print_r( $res, true ) . "<- file->" . $filename . "<- original file name ->/var/www/data/SUA/" .  date("Ymd") . "/" .  $regpat . "_" . $folsua . "_" . $perpag .  "/" . $uploadedFile->OriginalFileName . "|" );
		//}
		$result = "xxProblemas con services (copy). Archivo->/var/www/html/bajio/ezfilemanager/files/" . $filename . "<- Nombre Original ->/var/www/data/SUA/" .  date("Ymd") . "/" .  $regpat . "_" . $folsua . "_" . $perpag .  "/" . $uploadedFile->OriginalFileName . "<-";
        $tr->reportaError( $result, $QueAccess, $QueAccion );
        //$uploadedFile->delete();
        return ;
    } else {
    	// DebugBreak();
        $uploadedFile->delete();
        $result = "00";

		// *****************************************************
		// DGM I 30May2007 Entidad Receptora IMSS
		if ( $servicio == "pagoimssSUA" )
		{
		
			$Extension = $servicio . "-" . $datosSUA;
		}
		else
		{
		// DGM F 30May2007 Entidad Receptora IMSS
		// *****************************************************

			$Extension = $servicio . "-" . $ciudad . "-" . $empresa. "-" . $rowno. "-" . $nombre_esperado;

		} //DGM
		$tr->reportaErrorExtendido( $result, $QueAccess, $QueAccion, $Extension);
		if ( $ContenidoIntl == "" )
		{
	        $message = "Se recibió un archivo de la empresa: $empresa, radicada en la ciudad de $ciudad, para ser procesado por el servicio: $servicio.\n";
	        /*
			mail($mt, "Recepción de archivo de $servicio", $message,
				 "From: webmaster@bb.com.mx\r\n"
				."Reply-To: webmaster@bb.com.mx\r\n"
				."Attach-File: /var/www/data/$servicio/$ciudad/$empresa/paso/$uploadedFile->OriginalFileName\r\n"  //No funciona
				."X-Mailer: PHP/" . phpversion());
			*/
			//sendmail( $mt, "Recepción de archivo de $servicio", $message );
		}
		else
		{
	        $message = $ContenidoIntl;
	        /*
			mail("produccion@bb.com.mx", "Solicitud Electrónica de LC/BajioNET", $message,
				 "From: webmaster@bb.com.mx\r\n"
				."Reply-To: webmaster@bb.com.mx\r\n"
				."X-Mailer: PHP/" . phpversion());
			mail("RJIMENEZ@bb.com.mx", "Solicitud Electrónica de LC/BajioNET", $message,
				 "From: webmaster@bb.com.mx\r\n"
				."Reply-To: webmaster@bb.com.mx\r\n"
				."X-Mailer: PHP/" . phpversion());
			mail("fgonzalez@bb.com.mx", "Solicitud Electrónica de LC/BajioNET", $message,
				 "From: webmaster@bb.com.mx\r\n"
				."Reply-To: webmaster@bb.com.mx\r\n"
				."X-Mailer: PHP/" . phpversion());
			mail("mhernandez@bb.com.mx", "Solicitud Electrónica de LC/BajioNET", $message,
				 "From: webmaster@bb.com.mx\r\n"
				."Reply-To: webmaster@bb.com.mx\r\n"
				."X-Mailer: PHP/" . phpversion());
			*/
//LVPR   Envío parametrizado de correo por solicitud de carta de crédito
/*
			sendmail( "produccion@bb.com.mx", "Solicitud Electrónica de LC/BajioNET", $message );
			sendmail( "rjimenezbb.com.mx", "Solicitud Electrónica de LC/BajioNET", $message );
			sendmail( "fgonzalez@bb.com.mx", "Solicitud Electrónica de LC/BajioNET", $message );
			sendmail( "mhernandez@bb.com.mx", "Solicitud Electrónica de LC/BajioNET", $message );*/
			$LC_EMails = $ini->read_var( "site", "LCEMails" );
			$Arr_LCEMails = array( );
			$Arr_LCEMails = explode( ";", $LC_EMails );
			foreach( $Arr_LCEMails as $Key_LCEMails => $LCEMail )
			{
				sendmail( $LCEMail, "Solicitud Electrónica de L/BajioNET", $message );
			}
//LVPR   Envío parametrizado de correo por solicitud de carta de crédito
		}
    }

    eZLog::writeNotice( "File added to file manager from IP: $REMOTE_ADDR" );
    eZLog::writeNotice( "File validated with a result of: $result" );

	// DGM
	//YEHO 161406 I
	if ( $servicio  == "pagoimssSUA" and $QueAccess = "FrAc" and $QueAccion == "ArchSUA" and $result == "00" )
	{
  //		$Access    = "Confirm";
  	//	$QueAccess = "Confirm";                                                          
 //  echo "<br><b>LLAMANDO AL PAGOIMSSSUA desde MYFILEUPDLOAD</b>";
	//	include_once( "eztransaccion/user/pagoimssSUA.php" );
	//	$ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=arc&Access=".urlencode($Access)."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // intercambio de archivos de nómina electrónica
	 $_SESSION['folioVal'] = $_POST['folioVal']; //YEHO 181415
		$folioVal = $_SESSION['folioVal'];
//		ECHO "en el firstAccess folioVal[$folioVal]";
		$RenCap = $folioVal;
		  $Extension = $Extension. $RenCap;
//		  echo "FRACCESMY FILE extension[$Extension]";
//YEHO 181415
		return;
	}
	//YEHO 161406 F
/*	
  echo("3 DGM fieupload 790... $result, [$servicio-$QueAccess-$QueAccion] ACCES  $Access");
	if ( $servicio  == "pagoimssSUA" and $result == "00" )
	{
		$Access    = "Process";
		$QueAccess = "Process";
		ECHO "YEHO le puse PROCESS";
		return;
	}   */ 

    return;
}

if ( $Action == "Update" && $error == false )
{
    $file = new eZFile();

    $uploadedFile = new eZVirtualFile( $FileID );

    $uploadedFile->setName( $Name );
    $uploadedFile->setDescription( $Description );

    if ( $file->getUploadedFile( "userfile" ) )
    {
        $uploadedFile->setFile( $file );
    }

    $uploadedFile->store();
    changePermissions( $FileID, $ReadGroupArrayID, 'r' );
    changePermissions( $FileID, $WriteGroupArrayID, 'w' );

    $folder = new eZVirtualFolder( $FolderID );

    $uploadedFile->removeFolders();

    $folder->addFile( $uploadedFile );

    eZLog::writeNotice( "File added to file manager from IP: $REMOTE_ADDR" );
    eZHTTPTool::header( "Location: /filemanager/list/$FolderID/" );
}

if ( $Action == "DeleteFiles" )
{
    $oldFolder = 0;
    if ( count( $FileArrayID ) != 0 )
    {
        foreach ( $FileArrayID as $ID )
        {
            $file = new eZVirtualFile( $ID );
            $oldParent = $file->folder();

            if ( $oldParent )
                $oldFolder = $oldParent->id();

            $file->delete();
        }
    }

    eZHTTPTool::header( "Location: /filemanager/list/$oldFolder/" );
    exit();
}

if ( $Action == "Delete" )
{
    $file = new eZVirtualFile( $FileID );
    $oldParent = $file->folder();

    if ( $oldParent )
        $oldFolder = $oldParent->id();

    $file->delete();

    eZHTTPTool::header( "Location: /filemanager/list/$oldFolder/" );
    exit();
}

if ( $Action == "DeleteFolders" )
{
    $oldFolder = 0;
    if ( count( $FolderArrayID ) > 0 )
    {
        foreach ( $FolderArrayID as $FolderID )
        {
            $folder = new eZVirtualFolder( $FolderID );
            $oldParent = $folder->parent();

            if ( $oldParent )
                $oldFolder = $oldParent->id();

            $folder->delete();
        }
    }

    eZHTTPTool::header( "Location: /filemanager/list/$oldFolder/" );
    exit();
}


$t->set_var( "write_everybody", "" );
$t->set_var( "read_everybody", "" );
if ( $Action == "New" || $error )
{
    $t->set_var( "action_value", "insert" );
    $t->set_var( "file_id", "" );
    if ( $FolderID )
    {
        $readGroupArrayID =& eZObjectPermission::getGroups( $FolderID, "filemanager_folder", "r", false );
        $writeGroupArrayID =& eZObjectPermission::getGroups( $FolderID, "filemanager_folder", "w", false );
    }
    else
    {
        $t->set_var( "write_everybody", "selected" );
        $t->set_var( "read_everybody", "selected" );
    }
}

if ( $Action == "Edit" )
{
    $file = new eZVirtualFile( $FileID );

    if ( !( eZObjectPermission::hasPermission( $file->id(), "filemanager_file", "r", $user ) &&
            ( eZObjectPermission::hasPermission( $file->folder( false ), "filemanager_folder", "r", $user ) ||
              eZVirtualFolder::isOwner( $user, $file->folder( false ) ) ) ) )
    {
        eZHTTPTool::header( "Location: /error/403/" );
        exit();
    }

    $t->set_var( "name_value", $file->name() );
    $t->set_var( "description_value", $file->description() );
    $t->set_var( "file_id", $file->id() );

    $folder = $file->folder();

    if ( $folder )
        $FolderID = $folder->id();

    $readGroupArrayID =& eZObjectPermission::getGroups( $file->id(), "filemanager_file", "r", false );
    $writeGroupArrayID =& eZObjectPermission::getGroups( $file->id(), "filemanager_file", "w", false );

    $t->set_var( "action_value", "update" );
}

// Print out all the groups.
$group = new eZUserGroupBB();
$groups = $group->getAll();

foreach ( $groups as $group )
{
    $t->set_var( "group_id", $group->id() );
    $t->set_var( "group_name", $group->name() );

    $t->set_var( "is_read_selected1", "" );
    $t->set_var( "is_write_selected1", "" );

    if ( $readGroupArrayID )
    {
        foreach ( $readGroupArrayID as $readGroup )
        {
            if ( $readGroup == $group->id() )
            {
                $t->set_var( "is_read_selected1", "selected" );
            }
            elseif ( $readGroup == -1 )
            {
                $t->set_var( "read_everybody", "selected" );
            }
            else
            {
                $t->set_var( "is_read_selected", "" );
            }
        }

    }

    if ( $writeGroupArrayID )
    {
        foreach ( $writeGroupArrayID as $writeGroup )
        {
            if ( $writeGroup == $group->id() )
            {
                $t->set_var( "is_write_selected1", "selected" );
            }
            elseif ( $writeGroup == -1 )
            {
                $t->set_var( "write_everybody", "selected" );
            }
            else
            {
                $t->set_var( "is_write_selected", "" );
            }
        }
    }

    $t->parse( "write_group_item", "write_group_item_tpl", true );
    $t->parse( "read_group_item", "read_group_item_tpl", true );
}

$folder = new eZVirtualFolder() ;

$folderList = $folder->getTree();

foreach ( $folderList as $folderItem )
{
    if ( eZObjectPermission::hasPermission( $folderItem[0]->id(), "filemanager_folder", 'w' ) ||
         eZVirtualFolder::isOwner( eZUser::currentUser(), $folderItem[0]->id() ) ||
         eZObjectPermission::hasPermission( $folderItem[0]->id(), "filemanager_folder", 'u' ))
    {
        $t->set_var( "option_name", $folderItem[0]->name() );
        $t->set_var( "option_value", $folderItem[0]->id() );

        if ( $folderItem[1] > 0 )
            $t->set_var( "option_level", str_repeat( "&nbsp;", $folderItem[1] ) );
        else
            $t->set_var( "option_level", "" );

        $t->set_var( "selected", "" );

        if ( $folder && !$FolderID )
        {
            $FolderID = $folder->id();
        }

        if ( $FolderID )
        {
            if ( $folderItem[0]->id() == $FolderID )
            {
                $t->set_var( "selected", "selected" );
            }
        }

        $t->parse( "value", "value_tpl", true );
    }
}

if ( $GLOBALS["DEBUGA"] == true ) {
	eZLog::writeNotice( "En myfileupload (Action) ->" . print_r( $Action, true ) . "|" );
	eZLog::writeNotice( "En myfileupload (ya me voy de aqui).|" );
}


$t->pparse( "output", "file_upload_tpl" );

/******* FUNCTIONS ****************************/
function changePermissions( $objectID, $groups, $permission )
{
    eZObjectPermission::removePermissions( $objectID, "filemanager_file", $permission );
    if ( count( $groups ) > 0 )
    {
        foreach ( $groups as $groupItem )
        {
            if ( $groupItem == 0 )
                $group = -1;
            else
                $group = new eZUserGroupBB( $groupItem );

            eZObjectPermission::setPermission( $group, $objectID, "filemanager_file", $permission );
        }
    }
}

function myExec($_cmd, &$lines, &$errco) {
    $cmd = "$_cmd ; echo $?";
    exec( $cmd, $lines );
    // Get rid of the last errco line...
    $errco = (integer) array_pop( $lines );
    if ( count( $lines ) == 0) {
        return "";
    } else {
        return $lines[count($lines) - 1];
    }
}
?>