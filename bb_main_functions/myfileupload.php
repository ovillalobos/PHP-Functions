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
//include_once( "eztransaccion/classes/valida_estandarizado.php" ); // FAF - Pago de servicios
//NXN TarjetasPrepago 10Ago2010 Ini
include_once( "eztransaccion/classes/valida_altas_bajas_tarjprepab.php" );
include_once("eztransaccion/user/include/general_functions.inc");
include_once("eztransaccion/user/include/controles_javascript.inc");
include_once( "eztransaccion/classes/valida_depositos_tarjprepdep.php" );
//04Oct2012 HHR  206104-Proyecto tarjetas de credito Ini
include_once( "eztransaccion/classes/valida_altas_tarjhabiente.php" ); 
include_once( "eztransaccion/classes/valida_devolucion_recursos.php" );
//04Oct2012 HHR  206104-Proyecto tarjetas de credito Fin
//NXN TarjetasPrepago 10Ago2010 Fin
include_once( "ezsession/classes/ezsession.php" );
// VMV start
include_once( "eztransaccion/classes/mapping_layout_nomina.inc" ); // IRG25052011 - Nómina IMSS

include("eztransaccion/user/include/tcpipnexions.php");
include("eztransaccion/user/include/xmlparser.inc");

// EFR [31-ene-2013] (ini) > Grupo de Nomina Alfanumerico Nuevo Esquema
include_once("eztransaccion/user/include/utilerias_ne.inc");
// EFR [31-ene-2013] (fin) > Grupo de Nomina Alfanumerico Nuevo Esquema


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
//MAT -26Ene2012- [inicio] Desarrollo Add Select Tipo Deposito
//setcookie ( "eZMyFileUpload", $_POST['DiasPzo'], time() + ( 3600 * 24 * 365 ), "/",  "bb.com.mx", 0 )
//   or print( "Error: could not set cookie." );
setcookie ( "eZMyFileUpload", $_POST['DiasPzo']."-".$_POST['TipoDep'], time() + ( 3600 * 24 * 365 ), "/",  "bb.com.mx", 0 )
   or print( "Error: could not set cookie." ); //MAT -26Ene2012- [fin] Desarrollo Add Select Tipo Deposito
   //or print( "<script>	document.cookie='eZMyFileUpload=".$_POST['DiasPzo']."';
	//					if(document.cookie.indexOf('eZMyFileUpload')==-1)
	//					{
	//						alert('Error: could not set cookie.');
	//					}
	//		</script>" );

//ACS 15May2009

if(!empty($_COOKIE['QueAccess']))
	$QueAccess = $_COOKIE['QueAccess'];
if(!empty($_COOKIE['QueAccion']))
	$QueAccion = $_COOKIE['QueAccion'];

if ( isset /*(HB AGL - Ajustes PHP5)*/( $NewFile ) )
{
    $Action = "New";
}
if ( isset /*(HB AGL - Ajustes PHP5)*/( $NewFolder ) )
{
    eZHTTPTool::header( "Location: /filemanager/folder/new/".$_POST['FolderID']."" );
    exit();
}

if ( isset /*(HB AGL - Ajustes PHP5)*/( $DeleteFiles ) )
{
    $Action = "DeleteFiles";
}

if ( isset /*(HB AGL - Ajustes PHP5)*/( $Delete ) )
{
    $Action = "Delete";
}

if ( isset /*(HB AGL - Ajustes PHP5)*/( $DeleteFolders ) )
{
    $Action = "DeleteFolders";
}

if ( isset /*(HB AGL - Ajustes PHP5)*/( $Cancel ) )
{
    eZHTTPTool::header( "Location: /filemanager/list/" . $parentID );
    exit();
}

if ( isset /*(HB AGL - Ajustes PHP5)*/( $Download ) )
{
    $file = new eZVirtualFile( $_POST['FileID'] );

    if ( $ini->read_var( "eZTransaccionMain", "DownloadOriginalFilename" ) == "true" )
        $fileName = $file->originalFileName();
    else
        $fileName = $file->name();

    eZHTTPTool::header( "Location: /filemanager/download/".$_POST['FileID']."/$fileName" );
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

$t->set_file( "file_upload_tpl", "myfileupload.tpl" );

$t->setAllStrings();

$t->set_block( "file_upload_tpl", "value_tpl", "value" );
$t->set_block( "file_upload_tpl", "errors_tpl", "errors" );

$t->set_block( "file_upload_tpl", "write_group_item_tpl", "write_group_item" );
$t->set_block( "file_upload_tpl", "read_group_item_tpl", "read_group_item" );

$t->set_var( "errors", "&nbsp;" );

$t->set_var( "name_value", $_POST['Name'] );
$t->set_var( "description_value", $_POST['Description'] );

$error = false;
$nameCheck = true;
$descriptionCheck = false;
$folderPermissionCheck = true;
$readCheck = true;
$fileCheck = true;
$result = 0;

// NEX [20-jul-2012] T-211195 (ini) > Se agrega variable para almacenar response de WS
$xml_result = "";
// NEX [20-jul-2012] T-211195 (fin) > Se agrega variable para almacenar response de WS

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


	if(!empty($_POST['token']))
	{
		$_GET['token'] = $_POST['token'];
		$uploadedFile = $_SESSION["uploadFile"];
		$_POST['FolderID'] = $_SESSION["FolderID"];
		$file =  $_SESSION["File"];
		$fileCheck = false;

	}

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
        if ( empty( $_POST['Description'] ) )
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
    $uploadedFile->setDescription( $_POST['Description'] );
    $uploadedFile->setUser( $user );
    $uploadedFile->setFile( $file );
    // $uploadedFile->setFile( &$file );

	if ( $GLOBALS["DEBUGA"] == true ) {
		eZLog::writeNotice( "En myfileupload (uploadedFile) ->" . print_r( $uploadedFile, true ) . "|" );
	}

    if ( empty( $_POST['Name'] ) )
        $_POST['Name'] = $uploadedFile->originalFileName();

    if ( !$ini->read_var( "eZTransaccionMain", "DownloadOriginalFilename" ) == "true" )
    {
        $extension = strrchr( $uploadedFile->originalFileName(), "." );
        if ( strrchr( $_POST['Name'], "." ) != $extension )
            $_POST['Name'] .= $extension;
    }

    $uploadedFile->setName( $_POST['Name'] );
    $uploadedFile->store();
    $_POST['FileID'] = $uploadedFile->id();
    $folder = new eZVirtualFolder( $_POST['FolderID'] );

    if ( eZObjectPermission::hasPermission( $_POST['FolderID'], "filemanager_folder", 'w' ) ||
         eZVirtualFolder::isOwner( $user, $_POST['FolderID'] ) )
    {
        changePermissions( $_POST['FileID'], $_POST['ReadGroupArrayID'], 'r' );
        changePermissions( $_POST['FileID'], $_POST['WriteGroupArrayID'], 'w' );
    }
    else // user had upload permission only, change ownership, set special rights..
    {
//        eZObjectPermission::removePermissions( $FileID, "filemanager_file", "wr" ); // no write/read
//        eZObjectPermission::setPermission( -1, $FileID, "filemanager_file", 'r' );
//        $uploadedFile->setUser( $folder->user() );

        changePermissions( $_POST['FileID'], $_POST['ReadGroupArrayID'], 'r' );
        changePermissions( $_POST['FileID'], $_POST['WriteGroupArrayID'], 'w' );

        $uploadedFile->store();
    }

    $folder->addFile( $uploadedFile );

	//MAT -26Ene2012- Desarrollo Add Select Tipo Deposito
	//list( $servicio, $ciudad, $empresa, $rowno, $nombre_esperado) = explode("-", $eZMyFileUpload);
    list( $servicio, $ciudad, $empresa, $rowno, $nombre_esperado, $tipo_deposito ) = explode("-", $_COOKIE['eZMyFileUpload']); //MAT -26Ene2012- Desarrollo Add Select Tipo Deposito

// VMV start
	if (trim($nombre_esperado) == "si" ) {					// Valesp en bill
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
		//MAT -12Ene2012-
		eZLog::writeNotice( "Valida_depositos_nomina " .  $matriz  . " -|".$uploadedFile."-|-".$nombre_esperado."-|-".$tipo_deposito."-|" );
            	$result = valida_depositos_nomina( $uploadedFile, $nombre_esperado, $matriz );
		//ACS 27Dic2010 Archivos de Nomina Ini
		// NEX [20-jul-2012] T-211195 (ini) > Nuevo esquema de nomina
            	if ( trim($result) == "00" ) {
			if (nuevoEsquemaHabilitado()) {
						// EFR [31-ene-2013] (ini) > Grupo de Nomina Alfanumerico Nuevo Esquema
						include_once("eztransaccion/user/include/xml_request_builder_ne.inc"); 
						// EFR [31-ene-2013] (fin) > Grupo de Nomina Alfanumerico Nuevo Esquema
						         
						$Extension = $servicio . "-" . $ciudad . "-" . $empresa. "-" . $rowno. "-" . $nombre_esperado;
						
						// EFR [31-ene-2013] (ini) > Grupo de Nomina Alfanumerico Nuevo Esquema
						$xml_result = validaArchivoNuevoEsquema($QueAccess, $QueAccion, $uploadedFile, $Extension); // ($QueAccion=DepNom)
						include_once("eztransaccion/user/include/html_response_builder_ne.inc");
						$result = getHTMLResponseFromWSDepNom($xml_result, "DepNom");
						// EFR [31-ene-2013] (fin) > Grupo de Nomina Alfanumerico Nuevo Esquema
				muestraErrorDetallado($result);
				return;
			}
		}
		// NEX [20-jul-2012] T-211195 (fin) > Nuevo esquema de nomina
		//ACS 27Dic2010 Archivos de Nomina Fin
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
		//ACS 27Dic2010 Archivos de Nomina Ini
		// NEX [20-jul-2012] T-211195 (ini) > Nuevo esquema de nomina
            	if ( trim($result) == "00" ) {
			if (nuevoEsquemaHabilitado()) {   
						// EFR [31-ene-2013] (ini) > Grupo de Nomina Alfanumerico Nuevo Esquema
						include_once("eztransaccion/user/include/xml_request_builder_ne.inc");
						// EFR [31-ene-2013] (fin) > Grupo de Nomina Alfanumerico Nuevo Esquema
						         
						$Extension = $servicio . "-" . $ciudad . "-" . $empresa. "-" . $rowno. "-" . $nombre_esperado;

						// EFR [31-ene-2013] (ini) > Grupo de Nomina Alfanumerico Nuevo Esquema
						$xml_result = validaArchivoNuevoEsquema($QueAccess, $QueAccion, $uploadedFile, $Extension); // ($QueAccion=EmpNom)
						include_once("eztransaccion/user/include/html_response_builder_ne.inc");
						$result = getHTMLResponseFromWSEmpNom($xml_result, "EmpNom");
						// EFR [31-ene-2013] (fin) > Grupo de Nomina Alfanumerico Nuevo Esquema
				muestraErrorDetallado($result);
				return;
			}
            	}
		// NEX [20-jul-2012] T-211195 (fin) > Nuevo esquema de nomina
		//ACS 27Dic2010 Archivos de Nomina Fin
		$uploadedFile->OriginalFileName = $nombre_esperado;
		$mt = "mramirez@bb.com.mx";
                break;
	// IRG06052011 - INICIO - Nómina IMSS
	case "nomina3" :
            	$servicio = "nomina";

            	if ( $GLOBALS["DEBUGA"] == true ) {
			eZLog::writeNotice( "En myfileupload (servicio) ->" . print_r( $servicio, true ) . "|" );
			eZLog::writeNotice( "En myfileupload (uploadedFile) ->" . print_r( $uploadedFile, true ) . "|" );
			eZLog::writeNotice( "En myfileupload (nombre_esperado) ->" . print_r( $nombre_esperado, true ) . "|" );
		}

		//Inicio de la validación del formato de archivo de nómina del IMSS que se recibe.
		global $GlobalSiteIni;
		$file_layout = $GlobalSiteIni->SiteDir."/ezfilemanager/files/" .$uploadedFile->FileName;

		$exec_line = "./eztransaccion/classes/nomimssC ".$file_layout;
		$resultado_exec = myExec( $exec_line, $lines, $errco);
		if($resultado_exec != "0"){
			muestraErrorDetallado("El formato del archivo de nómina IMSS es incorrecto.");
			return;
		}
		//Fin de la validación del formato de archivo de nómina del IMSS que se recibe.

		//Inicio del mappeo del archivo de nómina IMSS al layout de nómina actual.
		$result_mapping = mapping_layout_nomina($uploadedFile->FileName, $nombre_esperado, $matriz, $empresa);
		if ($result_mapping != "00"){
			muestraErrorDetallado("Error en la creación del archivo de nómina.");
			return;
		}

		//Fin del mappeo del archivo de nómina IMSS al layout de nómina actual

		$uploadedFile->OriginalFileName = $nombre_esperado;
		$uploadedFile->FileName = $nombre_esperado;

            	$result = valida_depositos_nomina( $uploadedFile, $nombre_esperado, $matriz );

		// NEX [20-jul-2012] T-211195 (ini) > Nuevo esquema de nomina
            	if ( trim($result) == "00" ) {
			if (nuevoEsquemaHabilitado()) {
				$lines = array();
				$errco = 0;
					
						// EFR [31-ene-2013] (ini) > Grupo de Nomina Alfanumerico Nuevo Esquema
						include_once("eztransaccion/user/include/xml_request_builder_ne.inc");
						// EFR [31-ene-2013] (fin) > Grupo de Nomina Alfanumerico Nuevo Esquema
						       
						$Extension = $servicio . "-" . $ciudad . "-" . $empresa. "-" . $rowno. "-" . $nombre_esperado;

						// EFR [31-ene-2013] (ini) > Grupo de Nomina Alfanumerico Nuevo Esquema
						$xml_result = validaArchivoNuevoEsquema($QueAccess, $QueAccion, $uploadedFile, $Extension); // ($QueAccion=DepNom)
						include_once("eztransaccion/user/include/html_response_builder_ne.inc");
						$result = getHTMLResponseFromWSDepNom($xml_result, "DepNom");
						// EFR [31-ene-2013] (fin) > Grupo de Nomina Alfanumerico Nuevo Esquema
				muestraErrorDetallado($result);
				return;
			}
		}
		// NEX [20-jul-2012] T-211195 (fin) > Nuevo esquema de nomina

		$uploadedFile->OriginalFileName = $nombre_esperado;
		$mt = "mramirez@bb.com.mx";
                break;
	// IRG06052011 - FIN - Nómina IMSS
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
//NXN TarjetasPrepago 10Ago2010 Ini
				case "TPREPAGO": //Alta / Baja de tarjetas prepago

					//$servicio = "tarjprepab";

					if ( $GLOBALS["DEBUGA"] == true ) {
						eZLog::writeNotice( "En myfileupload (servicio) ->" . print_r( $servicio, true ) . "|" );
						eZLog::writeNotice( "En myfileupload (uploadedFile) ->" . print_r( $uploadedFile, true ) . "|" );
						eZLog::writeNotice( "En myfileupload (nombre_esperado) ->" . print_r( $nombre_esperado, true ) . "|" );
					}
					$TKN = $_GET['token'];
					if(empty($TKN) )
					{
						$TKN = "FrAc";
						$lst =  $ini->read_var( "site", "SecureServer" ) . $ini->read_var( "site", "ServerNumber" ) . $ini->read_var( "site", "DomainPostfix" );
						$_SESSION["uploadFile"] = $uploadedFile;
						$_SESSION["FolderID"] = $_POST['FolderID'];
						$_SESSION["File"] = $file;
					}

					switch ($rowno)
					{
						case "tarjprepab":
						case "tarjprepab2"://datos complementarios
								$tipo = $rowno;
								$result = valida_altas_bajas_tarjprepab( $uploadedFile, $nombre_esperado, $matriz, $TKN, $lst, $_SESSION["comisionalta"],$tipo );
							break;
						case "tarjprepdep":
								$result = valida_depositos_tarjprepdep( $uploadedFile, $nombre_esperado, $matriz, $TKN, $lst, $_SESSION["comision"]  );
							break;
					   //04Oct2012 HHR  206104-Proyecto tarjetas de credito Ini
					   case "tarjhabiente":
								$result = valida_altas_tarjhabiente( $uploadedFile, $nombre_esperado, $matriz, $TKN, $lst, $_SESSION["comisionalta"] );
							break;
						case "devrectarj":
								$result = valida_devolucion_recursos( $uploadedFile, $nombre_esperado, $matriz, $TKN, $lst, $_SESSION["comision"] );
						break;
					   //04Oct2012 HHR  206104-Proyecto tarjetas de credito Fin
					}
					
				//04Oct2012 HHR  206104-Proyecto tarjetas de credito Ini
					if ($result == 0)
					{
						$no_reg_archivo = $_SESSION["total_registros"];
						$monto_archivo = $_SESSION["monto_total"];
					}
				//04Oct2012 HHR  206104-Proyecto tarjetas de credito Fin
					
					if ($TKN == "FrAc" && $result == 0)
					{
						echo $lst;
						setcookie ( "eZMyFileUpload", $_COOKIE['eZMyFileUpload'], time() + ( 3600 * 24 * 365 ), "/",  "bb.com.mx", 0 )
							or print( "Error: could not set cookie." );
						return;
					}
					else if (!empty($_GET["token"]))
					{

						$trn = new eZTransaccion( );
						$usr = $session->variable( "r_usr" );
						$qki = $session->variable( "r_qki" );
						$priv = $session->variable( "r_priv" );
						//$token = $_GET["token"];
						$particularFields = "";
						$ret_code = 0;


						// $ret_code = $trn->PostToHost($backend, "/IBnkIIS.dll", "Trxn=ctk&CustTkn=".encrypt( $token, strtolower( $usr ) )."&Access=".urlencode("process")."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv) , $qki, $usr, $qki, $priv, $transaccion_buffer);

						// if (strpos($transaccion_buffer,"TKNRESPTRUE")===false)
						// {
							// echo formatCaracter($transaccion_buffer);
							// return;
						// }
						// else
						// {
							$uploadedFile->OriginalFileName = $nombre_esperado;
						// }


					}
					else
					{
						$uploadedFile->OriginalFileName = $nombre_esperado;
					}
					$mt = "mramirez@bb.com.mx";
				break;
//NXN TarjetasPrepago 10Ago2010 Fin
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
            $tr->reportaErrorDetallado( $result, $QueAccess, $QueAccion , $matriz);
            $uploadedFile->delete();
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

	// if ( !is_dir( "/var/www/data/" . $servicio . "/" . $ciudad . "/" . $empresa . "/paso/" ) )
	if ( !is_dir( $base_dir . $servicio . "/" . $ciudad . "/" . $empresa . "/paso/" )  && ( $servicio != "TPREPAGO" ) || !is_dir( $base_dir  . $servicio . "/" . $ciudad . "/" . $empresa ."/"  ) )
	{
		umask( 0000 );
		// if ( !is_dir( "/var/www/data/" . $servicio . "/" ) )
		if ( !is_dir( $base_dir . $servicio . "/" ) )
		{
			// $res = mkdir( "/var/www/data/" . $servicio . "/" );
			$res = mkdir( $base_dir . $servicio . "/" );
			if ( !$res )
			{
				eZLog::writeNotice( "En myfileupload (res) ->" . print_r( $res, true ) . "<- dir->" . "/var/www/data/" . $servicio . "/|" );
				// $result = "xxProblemas con services (mkdir). Carpeta ->" . "/var/www/data/" . $servicio . "/<-";
				$result = "xxProblemas con services (mkdir). Carpeta ->" . $base_dir . $servicio . "/<-";
				$tr->reportaError( $result, $QueAccess, $QueAccion );
				$uploadedFile->delete();
				return ;
			}
		}
		// if ( !is_dir( "/var/www/data/" . $servicio . "/" . $ciudad . "/" ) )
		if ( !is_dir( $base_dir . $servicio . "/" . $ciudad . "/" ) )
		{
			// $res = mkdir( "/var/www/data/" . $servicio . "/" . $ciudad . "/" );
			$res = mkdir( $base_dir . $servicio . "/" . $ciudad . "/" );
			if ( !$res )
			{
				eZLog::writeNotice( "En myfileupload (res) ->" . print_r( $res, true ) . "<- dir->" . "/var/www/data/" . $servicio . "/" . $ciudad . "/|" );
				// $result = "xxProblemas con services (mkdir). Carpeta ->". "/var/www/data/" . $servicio . "/" . $ciudad . "/<-";
				$result = "xxProblemas con services (mkdir). Carpeta ->". $base_dir . $servicio . "/" . $ciudad . "/<-";
				$tr->reportaError( $result, $QueAccess, $QueAccion );
				$uploadedFile->delete();
				return ;
			}
		}
		
		
		// if ( !is_dir( "/var/www/data/" . $servicio . "/" . $ciudad . "/" . $empresa . "/" ) )
		if ( !is_dir( $base_dir . $servicio . "/" . $ciudad . "/" . $empresa . "/" ) )
		{
			// $res = mkdir( "/var/www/data/" . $servicio . "/" . $ciudad . "/" . $empresa . "/" );
			$res = mkdir( $base_dir . $servicio . "/" . $ciudad . "/" . $empresa . "/" );
			if ( !$res )
			{
				eZLog::writeNotice( "En myfileupload (res) ->" . print_r( $res, true ) . "<- dir->" . "/var/www/data/" . $servicio . "/" . $ciudad . "/" . $empresa . "/|" );
				// $result = "xxProblemas con services (mkdir). Carpeta ->" . "/var/www/data/" . $servicio . "/" . $ciudad . "/" . $empresa . "/<-";
				$result = "xxProblemas con services (mkdir). Carpeta ->" . $base_dir . $servicio . "/" . $ciudad . "/" . $empresa . "/<-";
				$tr->reportaError( $result, $QueAccess, $QueAccion );
				$uploadedFile->delete();
				return ;
			}
		}
		if ( $GLOBALS["DEBUGA"] == true ) {
				eZLog::writeNotice( "En myfileupload (exec_line) ->" . print_r( $exec_line, true ) . "|" );
		}
//NXN TarjetasPrepago 10Ago2010 Ini
		// $res = mkdir( "/var/www/data/" . $servicio . "/" . $ciudad . "/" . $empresa. "/paso/" );
//$res = mkdir( $base_dir . $servicio . "/" . $ciudad . "/" . $empresa. "/paso/" );
		if ($servicio != "TPREPAGO" )
		{

		$res = mkdir( $base_dir . $servicio . "/" . $ciudad . "/" . $empresa. "/paso/" );
		}
//NXN TarjetasPrepago 10Ago2010 Fin
		if ( !$res )
		{
			if ( $debug ) {
				echo "myFailResult: " . $res . "<br>\n\n";
			}
//NXN TarjetasPrepago 10Ago2010 Ini
			if ($servicio == "TPREPAGO" )
			{
				eZLog::writeNotice( "En myfileupload (res) ->" . print_r( $res, true ) . "<- dir->" . "/var/www/data/" . $servicio . "/" . $ciudad . "/" . $empresa . "/|" );
				$result = "xxProblemas con services (mkdir). Carpeta ->"  . "/var/www/data/" . $servicio . "/" . $ciudad . "/" . $empresa. "/<-";
			}
			else
			{
//NXN TarjetasPrepago 10Ago2010 Fin
			eZLog::writeNotice( "En myfileupload (res) ->" . print_r( $res, true ) . "<- dir->" . "/var/www/data/" . $servicio . "/" . $ciudad . "/" . $empresa. "/paso/|" );
			// $result = "xxProblemas con services (mkdir). Carpeta ->"  . "/var/www/data/" . $servicio . "/" . $ciudad . "/" . $empresa. "/paso/<-";
			$result = "xxProblemas con services (mkdir). Carpeta ->"  . $base_dir . $servicio . "/" . $ciudad . "/" . $empresa. "/paso/<-";
			}//NXN TarjetasPrepago 10Ago2010
			$tr->reportaError( $result, $QueAccess, $QueAccion );
			$uploadedFile->delete();
			return ;
		}
		else
		{
			// chgrp( "/var/www/data/" . $servicio . "/" . $ciudad . "/" . $empresa. "/paso/", "nfsnobody" );
		}
	}

  eZLog::writeNotice( "[AQUI1]\n\n" );
		if ($servicio == "TPREPAGO")
			{
				eZLog::writeNotice( "[AQUI2]\n\n" );
            if ( !is_dir( $base_dir . $servicio . "/" . $ciudad . "/SALIDA" ) )
      		{
      			eZLog::writeNotice( "[AQUI3]\n\n" );
      		       $res = mkdir( $base_dir . $servicio . "/" . $ciudad . "/SALIDA" );
      		       if ( !$res )
          			{
          				eZLog::writeNotice( "[AQUI4]\n\n" );
          				eZLog::writeNotice( "En myfileupload (res) ->" . print_r( $res, true ) . "<- dir->" . "/var/www/data/" . $servicio . "/" . $ciudad . "/|" );
          				// $result = "xxProblemas con services (mkdir). Carpeta ->" . "/var/www/data/" . $servicio . "/" . $ciudad . "/" . $empresa . "/<-";
          				$result = "xxProblemas con services (mkdir). Carpeta ->" . $base_dir . $servicio . "/" . $ciudad . "/" . $empresa . "/<-";
          				$tr->reportaError( $result, $QueAccess, $QueAccion );
          				$uploadedFile->delete();
          				return ;
          			}
      		}   
          
          if ( !is_dir( $base_dir . $servicio . "/" . $ciudad . "/SALIDA/REPORTES" ) )
      		{
      			eZLog::writeNotice( "[AQUI5]\n\n" );
      		       $res = mkdir( $base_dir . $servicio . "/" . $ciudad . "/SALIDA/REPORTES" );
      		       if ( !$res )
          			{
          				eZLog::writeNotice( "[AQUI6]\n\n" );
          				eZLog::writeNotice( "En myfileupload (res) ->" . print_r( $res, true ) . "<- dir->" . "/var/www/data/" . $servicio . "/" . $ciudad . "/SALIDA/REPORTES|" );
          				// $result = "xxProblemas con services (mkdir). Carpeta ->" . "/var/www/data/" . $servicio . "/" . $ciudad . "/" . $empresa . "/<-";
          				$result = "xxProblemas con services (mkdir). Carpeta ->" . $base_dir . $servicio . "/" . $ciudad . "/" . $empresa . "/<-";
          				$tr->reportaError( $result, $QueAccess, $QueAccion );
          				$uploadedFile->delete();
          				return ;
          			}
      		}     
                   
      }

    $lines = array();
    $errco = 0;
    //$exec_line = "cp $filename /var/www/data/$servicio/$ciudad/$empresa/paso/$uploadedFile->OriginalFileName 2>&1";
    //echo "<em>$exec_line</em> "  . var_dump ( $exec_line ) . "<br>\n";
    //$exec_line = "scp $filename root@192.1.3.216:/var/www/data/$servicio/$ciudad/$empresa/paso/$uploadedFile->OriginalFileName 2>&1";
    //$res = myExec( $exec_line, $lines, $errco);
	// $res = copy( $filename, "/var/www/data/" . $servicio . "/" . $ciudad . "/" . $empresa . "/paso/" . $uploadedFile->OriginalFileName );
//NXN TarjetasPrepago 10Ago2010 Ini
	if ($servicio == "TPREPAGO" )
	{
	$res = copy( $filename, $base_dir . $servicio . "/" . $ciudad . "/" . $empresa . "/" . $uploadedFile->OriginalFileName );
	}
	else
	{
	$res = copy( $filename, $base_dir . $servicio . "/" . $ciudad . "/" . $empresa . "/paso/" . $uploadedFile->OriginalFileName );
	}
//NXN TarjetasPrepago 10Ago2010 Fin
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

		$res = copy ( $filename, $reply . $servicio. "/" . $cuidad . "/" . $empresa . "/paso/" . $uploadedFile->OriginalFileName );
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
		eZLog::writeNotice( "En myfileupload (res) ->" . print_r( $res, true ) . "<- file->" . $filename . "<- original file name ->/var/www/data/" . $servicio . "/" . $ciudad . "/" . $empresa . "/paso/" . $uploadedFile->OriginalFileName . "|" );
		//}
		// $result = "xxProblemas con services (copy). Archivo->/var/www/html/bajio/ezfilemanager/files/" . $filename . "<- Nombre Original ->/var/www/data/" . $servicio . "/" . $ciudad . "/" . $empresa . "/paso/" . $uploadedFile->OriginalFileName . "<-";
		$result = "xxProblemas con services (copy). Archivo->/var/www/html/bajio/ezfilemanager/files/" . $filename . "<- Nombre Original ->". $base_dir . $servicio . "/" . $ciudad . "/" . $empresa . "/paso/" . $uploadedFile->OriginalFileName . "<-";
	// JAC 03AGO2010 FIN
        $tr->reportaError( $result, $QueAccess, $QueAccion );
        //$uploadedFile->delete();
        return ;
    } else {
    	// DebugBreak();
        $uploadedFile->delete();
        $result = "00";
        //$Extension = $servicio . "-" . $ciudad . "-" . $empresa. "-" . $rowno. "-" . $nombre_esperado; //MAT -26Ene2012- [inicio] Desarrollo Add Select Tipo Deposito
	//04Oct2012 HHR  206104-Proyecto tarjetas de credito Ini
		$Extension = $servicio . "-" . $ciudad . "-" . $empresa. "-" . $rowno. "-" . $nombre_esperado. "-" . $tipo_deposito;//MAT -26Ene2012- [inicio] Desarrollo Add Select Tipo Deposito
		$Extension = $servicio . "-" . $ciudad . "-" . $empresa. "-" . $rowno. "-" . $nombre_esperado. "-" . $tipo_deposito. "-" .$no_reg_archivo. "-" . $monto_archivo;
	//04Oct2012 HHR  206104-Proyecto tarjetas de credito Fin
        $tr->reportaErrorExtendido( $result, $QueAccess, $QueAccion, $Extension );
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

    eZLog::writeNotice( "File added to file manager from IP: ".$_SERVER['REMOTE_ADDR']."" );
    eZLog::writeNotice( "File validated with a result of: $result" );
    return;
}

if ( $Action == "Update" && $error == false )
{
    $file = new eZFile();

    $uploadedFile = new eZVirtualFile( $_POST['FileID'] );

    $uploadedFile->setName( $_POST['Name'] );
    $uploadedFile->setDescription( $_POST['Description'] );

    if ( $file->getUploadedFile( "userfile" ) )
    {
        $uploadedFile->setFile( $file );
    }

    $uploadedFile->store();
    changePermissions( $_POST['FileID'], $_POST['ReadGroupArrayID'], 'r' );
    changePermissions( $_POST['FileID'], $_POST['WriteGroupArrayID'], 'w' );

    $folder = new eZVirtualFolder( $_POST['FolderID'] );

    $uploadedFile->removeFolders();

    $folder->addFile( $uploadedFile );

    eZLog::writeNotice( "File added to file manager from IP: ".$_SERVER['REMOTE_ADDR'] );
    eZHTTPTool::header( "Location: /filemanager/list/".$_POST['FolderID']."/" );
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
    $file = new eZVirtualFile( $_POST['FileID'] );
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
        foreach ( $FolderArrayID as $_POST['FolderID'] )
        {
            $folder = new eZVirtualFolder( $_POST['FolderID'] );
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
    if ( $_POST['FolderID'] )
    {
        $readGroupArrayID =& eZObjectPermission::getGroups( $_POST['FolderID'], "filemanager_folder", "r", false );
        $writeGroupArrayID =& eZObjectPermission::getGroups( $_POST['FolderID'], "filemanager_folder", "w", false );
    }
    else
    {
        $t->set_var( "write_everybody", "selected" );
        $t->set_var( "read_everybody", "selected" );
    }
}

if ( $Action == "Edit" )
{
    $file = new eZVirtualFile( $_POST['FileID'] );

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
        $_POST['FolderID'] = $folder->id();

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

        if ( $folder && !$_POST['FolderID'] )
        {
            $_POST['FolderID'] = $folder->id();
        }

        if ( $_POST['FolderID'] )
        {
            if ( $folderItem[0]->id() == $_POST['FolderID'] )
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