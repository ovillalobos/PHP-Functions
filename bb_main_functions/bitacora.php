<style>
	#crmpublicidad{
		width: 800px;
		height: 201px;
		margin: 0px auto;
	}
	#crmpublicidadTxt{
		width: 798px;
		height: 42px;
		margin: 0px auto;
		border: 2px solid #724598;
		background-image: url('https://<?php print ($_SERVER["HTTP_HOST"]); ?>/eztransaccion/user/menu/images_menu/imagenes-horizontales.gif');
		background-repeat: repeat-x;
		background-position: 0px -259px; 
		color: #ffffff;
		border-radius: 10px 0px 10px 0px;
		font-weight: bold;
		text-shadow: 2px 2px 3px rgba(0,0,0,0.5);
		padding-top: 18px;
	}	
	.size18{
		font-size: 18px;
	}
	.size12{
		font-size: 12px;
	}
</style>
<?php
//
//
// Created on: <1-Nov-2001 17:37:53 smb>
//
// This source file is part of IVA.
//
// Copyright (C) 1997-2004 Internet de Alta Calidad, S.A. de C.V.  Todos los derechos reservados. All rights reserved.
//
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

include_once( "classes/INIFile.php" );
include_once( "classes/eztemplate.php" );
include_once( "classes/ezhttptool.php" );
include_once( "eztransaccion/classes/eztransaccion.php" );
include_once( "ezsession/classes/ezsession.php" );

include_once( "eztransaccion/classes/ezlog.php" );		// CRM_PUBLICIDAD OVVC
require_once( "nusoap-0.7.3/lib/nusoap.php" );			// CRM_PUBLICIDAD OVVC

$session =& eZSession::globalSession();

if( !$session->fetch() )
{
    $session->store();
}

$ini =& $GLOBALS["GlobalSiteIni"];
$Language = $ini->read_var( "eZTransaccionMain", "Language" );
$ServerNumber = $ini->read_var( "site", "ServerNumber" );
$DomainPostfix = $ini->read_var( "site", "DomainPostfix" );
$backend = "gw" . $ServerNumber. $DomainPostfix;

$wsdl = trim($ini->read_var( "site", "MiddlewareCRMPublicidad"));	// CRM_PUBLICIDAD OVVC
$CRMPublicidad = trim($ini->read_var( "site", "CRMPublicidad"));	// CRM_PUBLICIDAD OVVC
$servicio = "pubnet";												// CRM_PUBLICIDAD OVVC

if ( $GLOBALS["DEBUGA"] == true )
{
		$log = "En ".$_SERVER['SCRIPT_FILENAME']." que es ".$_SERVER['PHP_SELF']." (backend) ->" . print_r( $backend, true ) . "|";
        eZLog::writeNotice( $log );
		define_syslog_variables();
		openlog( "IVA", LOG_PID | LOG_PERROR, LOG_DAEMON );
		syslog( LOG_WARNING, "$log" );
		closelog();
}
include_once( "ezuserbb/classes/ezuser.php" );
$user =& eZUserBB::currentUser();

$iusrID = $session->variable( "r_usr" );	// CRM_PUBLICIDAD OVVC

if ( $GLOBALS["DEBUGA"] == true )
{
        eZLog::writeNotice( "En bitacora (user) ->" . print_r( $user, true ) . "|" );
}
if ( $user )
{
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ), "eztransaccion/user/intl/", $Language, "bitacora.php" );
    $t->setAllStrings();
    $t->set_file( array( "bitacora_tpl" => "bitacora.tpl" ) );
    $session =& eZSession::globalSession();
    if ( !$session->fetch() )
    {
        $session->store();
    }
    $tr = new eZTransaccion( );
    $usr = $session->variable( "r_usr" );
    $qki = $session->variable( "r_qki" );
    $priv = $session->variable( "r_priv" );

//NEX julio2011 Inicio Bitacora Deshabilitada para RSA
	$session =& eZSession::globalSession();
	if( !$session->fetch() )
	$session->store();

	$usrSEC = $session->variable( "r_usr" );
	$opcion = $session->Variable( "rsa_start" );
	$promotdc = $session->Variable( "r_promotdc" );
	$perspup = $session->Variable( "r_perspup" );
	//eZLog::writeNotice(" :::::::::::::variable rsa_start::::::[$opcion]::::::::: ");

	if( $opcion == "On" )
	{
		$ret_code = "INGRESO PROCESO RSA";
		$session->setvariable( "rsa_start", "sucess" );
		$opcion = $session->Variable( "rsa_start" );
		//ezLog::writeNotice(":::::BITACORA DESHABILITADA::rsa_start==> ON:".$opcion."::");
	}

	else
	{
		 //ezLog::writeNotice(":::::BITACORA HABILITADA::rsa_start=========> ON ::::::::::::::::::::::");

//NEX julio2011 Fin Bitacora Deshabilitada para RSA
		//ALUNA 12MAy2008 I Anuncio SAPAL
			/* DESHABILITAR POR FAST_TRACK
			$AnuncioInicio = $ini->read_var( "site", "AnuncioInicio" );
			if ($promotdc =="no")
			{
				if ($perspup =="no")
				{
					//$AnuncioIniImg = $ini->read_var( "site", "AnuncioIniImg" );
					$AnuncioIniImg ="/images/bajiosecure/banner_secure/popup/popup_1.jpg";
				}
				else
				{
				 	//$AnuncioIniImg ="/".$session->Variable( "r_perspup" ).".jpg";
				 	$AnuncioIniImg ="/images/bajiosecure/banner_secure/popup/".$session->Variable( "r_perspup" ).".jpg";

				}
			}
			else
			{
			//$AnuncioIniImg ="/".$session->Variable( "r_promotdc" ).".jpg";
			$AnuncioIniImg ="/images/bajiosecure/banner_secure/popup/".$session->Variable( "r_promotdc" ).".jpg";
			}
			if ( $AnuncioInicio != "" ) // and $PlazaUser == "Leon" )
			{
				$SecureServer = $ini->read_var( "site", "SecureServer" );
				$SecureServerType = $ini->read_var( "site", "SecureServerType" );
				$DomainPostfix = $ini->read_var( "site", "DomainPostfix" );
				$ServerNumber = $ini->read_var( "site", "ServerNumber" );
				$LinkAnuncioIniImg = $ini->read_var( "site", "LinkAnuncioIniImg" );

				//LGAM [239976] 08112012 - MODIFICAR TAMANO DE PAGINA ANUNCIO
				//$Pagina = "<SCRIPT LANGUAGE='JavaScript'> function ventana() { var windowW = 550; var windowH = 340; var windowX = 10; var windowY = 10; var title = 'Aviso'; s = 'width='+windowW+',height='+windowH; cadena=\"<HTML><HEAD><link REL='STYLESHEET' HREF='" . $SecureServerType . "://" . $SecureServer . $ServerNumber . $DomainPostfix . "/impuestos/recibos.css' TYPE='text/css'><TITLE>$AnuncioInicio</TITLE>";
				//LGAM [239976] 08112012 - MODIFICAR TAMANO DE PAGINA ANUNCIO
				if ($promotdc =="no" and $perspup =="no")
				{
				$Pagina = "<SCRIPT LANGUAGE='JavaScript'> function ventana() { var windowW = 550; var windowH = 360; var windowX = 10; var windowY = 10; var title = 'Aviso'; s = 'width='+windowW+',height='+windowH; cadena=\"<HTML><HEAD><link REL='STYLESHEET' HREF='" . $SecureServerType . "://" . $SecureServer . $ServerNumber . $DomainPostfix . "/impuestos/recibos.css' TYPE='text/css'><TITLE>$AnuncioInicio</TITLE>";
				}
				else
				{
				 $Pagina = "<SCRIPT LANGUAGE='JavaScript'> function ventana() { var windowW = 750; var windowH = 650; var windowX = 10; var windowY = 10; var title = 'Aviso'; s = 'width='+windowW+',height='+windowH; cadena=\"<HTML><HEAD><link REL='STYLESHEET' HREF='" . $SecureServerType . "://" . $SecureServer . $ServerNumber . $DomainPostfix . "/impuestos/recibos.css' TYPE='text/css'><TITLE>$AnuncioInicio</TITLE>";
				}
				//$Pagina = "<SCRIPT LANGUAGE='JavaScript'> function ventana() { var windowW = 650; var windowH = 370; var windowX = 0; var windowY = 0; var title = 'Aviso'; s = 'width='+windowW+',height='+windowH; cadena=\"<HTML><HEAD><link REL='STYLESHEET' HREF='" . $SecureServerType . "://" . $SecureServer . $ServerNumber . $DomainPostfix . "/impuestos/recibos.css' TYPE='text/css'><TITLE>$AnuncioInicio</TITLE>";
				$Pagina = $Pagina."<SCRIPT LANGUAGE='JavaScript'> function window_onbeforeprint() { Imprimir.style.visibility = 'hidden'; Salir.style.visibility = 'hidden';} function window_onafterprint() { Imprimir.style.visibility = 'visible'; Salir.style.";
				$Pagina = $Pagina."visibility = 'visible'; }\"+\"<\"+\"/\"+\"SCRIPT></HEAD><BODY LANGUAGE='javascript' onbeforeprint='return window_onbeforeprint()' onafterprint='return window_onafterprint()'>";
				$Pagina = $Pagina."<BR>";
				$Pagina = $Pagina."<P><CENTER>";
				//$Pagina = $Pagina."<a href=" . $SecureServerType . "://" .$SecureServer . $ServerNumber . $DomainPostfix . $LinkAnuncioIniImg . ">";
				//LVPR I 9Mar09 Tomar imagen del secure
				//$Pagina = $Pagina."<IMG SRC='" . $SecureServerType . "://images" . $DomainPostfix . "$AnuncioIniImg' ALIGN= 'TOP' SAPAL>";
				$Pagina = $Pagina."<IMG SRC='" . $SecureServerType . "://".$SecureServer .$ServerNumber.$DomainPostfix . "$AnuncioIniImg' ALIGN= 'TOP' SAPAL>"; // OVVC REINGENIERÍA//psm se rehabilita porque el hardcode de abajo afecto funcionalidad de popup personalizado
				//$Pagina = $Pagina."<IMG SRC='" . $SecureServerType . "://".$SecureServer .$ServerNumber.$DomainPostfix . "/images/bajiosecure/banner_secure/popup/popup_1.jpg' ALIGN= 'TOP' SAPAL>";
				//LVPR F 9Mar09 Tomar imagen del secure
				//$Pagina = $Pagina."</a>";
				$Pagina = $Pagina."</P></CENTER>";
				//$Pagina = $Pagina."<BR><hr noshade='noshade' size='4' color='#5A419c' />";
				$Pagina = $Pagina."<hr noshade='noshade' size='4' color='#5A419c' />";
				//$Pagina = $Pagina."<P ALIGN='RIGHT'><B><FONT>" . $SecureServerType . "://" .$SecureServer . $ServerNumber . $DomainPostfix . $LinkAnuncioIniImg . "</FONT></B></P>";
				$Pagina = $Pagina."</BODY></HTML>\"; NFW =";
				$Pagina = $Pagina." window.open(\"\",'Recibo',\"toolbar=no,menubar=no,scrollbars=2,\"+s); NFW.blur(); NFW.resizeTo(windowW,windowH); NFW.moveTo(windowX,windowY); NFW.";
				$Pagina = $Pagina."document.open(); NFW.document.write(cadena); NFW.document.close(); NFW.window.focus(); } ventana(); </SCRIPT>";
			}
			*/
		$transaccion_buffer = "";

		$ret_code = $tr->PostToHost( $backend, "/IBnkIIS.dll", "Trxn=log&Access=Process&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv), $qki, $usr, $qki, $priv, $transaccion_buffer );
		
		// CRQ6688 CRM PUBLICIDAD OMAR VILLALOBOS 280386 INI
		if ( $CRMPublicidad == "enabled" )
		{
			$SecureServerType = $ini->read_var( "site", "SecureServerType" );
			$SecureServer = $ini->read_var( "site", "SecureServer" );
			$ServerNumber = $ini->read_var( "site", "ServerNumber" );
			$DomainPostfix = $ini->read_var( "site", "DomainPostfix" );		
			
			$rutaPubnet = $SecureServerType."://".$SecureServer.$ServerNumber.$DomainPostfix."/images/bajiosecure/pubnet/";			
						
			$result = callWebServicePUBNET($wsdl, $iusrID, $servicio);
			$res 	= $result['pubnetReturn'];
			$xml	= simplexml_load_string($res);
			$urlPub = $xml->respuesta->Usuario->url;
			$error 	= $xml->respuesta->error;
						
			if( $error == "0000" )
			{		
				echo "<center><div id='crmpublicidad'>";						
				echo "<img id=\"imgBann\" class=\"imgBannerOVVC\" src=\"".$rutaPubnet.$urlPub."\" />";
				echo "</div></center>";		
				echo "<br/>";
			}
			else
			{					
				echo "<center><div id='crmpublicidad'>";						
				echo "<img id=\"imgBann\" class=\"imgBannerOVVC\" src=\"".$rutaPubnet."banbajio.jpg\" />";
				echo "</div></center>";	
				echo "<br/>";
				/*	MENSAJE CON CAMPAÑA ESPECIFICA
				echo "<center><div id='crmpublicidadTxt'>";						
				echo "<span class='size18'>Felicidades</span><span class='size12'> Omar Villalobos tienes una Tarjeta de Crédito preautorizada por </span><span class='size18'>$2222</span><a href='http://www.bb.com.mx'>hola </a>";
				echo "</div></center>";
				*/			
			}
		}
		// CRQ6688 CRM PUBLICIDAD OMAR VILLALOBOS 280386 FIN
		//NEX julio2011 Inicio Bitacora Deshabilitada para RSA
	}

	//NEX julio2011 Fin Bitacora Deshabilitada para RSA

    $tr->blog( $qki,"Bitacora", "", "", "", "", "" );
    $t->set_var( "transaccion_buffer", $transaccion_buffer.$Pagina  );	//ALUNA 12MAy2008 Anuncio SAPAL
    $t->pparse( "output", "bitacora_tpl" );
}
else
{
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ), "eztransaccion/user/intl/", $Language, "userlogin.php" );
    $t->setAllStrings();
    $t->set_file( array( "user_login_tpl" => "userlogin.tpl" ) );
    $t->set_var( "redirect_url", "/transaccion/saldos/" );
    $t->pparse( "output", "user_login_tpl" );
}

function callWebServicePUBNET($wsdl, $iusrID, $servicio)
{		
	$param = array ('xmlRequest'=>'<consulta><tipo>conbajionet</tipo><userid>'.$iusrID.'</userid></consulta>');	
	$client= new nusoap_client($wsdl,'wsdl');			
	$response = $client->call($servicio, $param);	
	
	//eZLog::writeNotice ("<br>........DBA REQUEST..[".$client->request ."]");
	//eZLog::writeNotice ("<br>........DBA......RESPONSE..[".$client->response ."]");
	//eZLog::writeNotice ("<br>........DBA DEBUG...[".htmlspecialchars($client->debug_str, ENT_QUOTES) ."]");			
	
	if ($client->fault)	{
		return "Error al conectarse con el Web Service.";
	} else {
		$error = $client->getError();
		if ($error){
			return "Error " . $error;
		}
	}
	
	return $response;
}			
?>