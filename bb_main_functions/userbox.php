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
include_once( "classes/ezlog.php" );
include_once( "classes/ezhttptool.php" );

$ini =& INIFile::globalINI();
$Language = $ini->read_var( "eZTransaccionMain", "Language" );
$NormalSite = $ini->read_var( "site", "WWWServer" );
$srv_num = $ini->read_var( "site", "ServerNumber" );
$DomainPostfix = $ini->read_var( "site", "DomainPostfix" );
$SecureSite = $ini->read_var( "site", "SecureServer" );
$ServersInFarm = $ini->read_var( "site", "ServersInFarm" );
$RedunSendMail = $ini->read_var( "site", "RedunSendMail" );
$ServerType = $ini->read_var( "site", "ServerType" );
$ErrEMail1 = $ini->read_var( "site", "ErrEMail1" );
$ErrEMail2 = $ini->read_var( "site", "ErrEMail2" );
$var_gobMenu = $ini->read_var("site","gobMenu");

include_once( "ezuserbb/classes/ezuser.php" );
include_once( "ezuserbb/classes/ezmodule.php" );
include_once( "ezuserbb/classes/ezpermission.php" );
include_once( "ezsession/classes/ezsession.php" );

$session =& eZSession::globalSession();

if( !$session->fetch() )
	$session->store();

if ( $ServerType != "QA" )
{
	$user =& eZUserBB::currentUser();
	
}
else
{
	$user = false;
}

if ( $GLOBALS["DEBUGA"] == true ) {
	eZLog::writeNotice( "En userbox (user) is ACK ->" . print_r( $user, true ) . "|" );
}

// DebugBreak();

if ( !$user )
{
	//include_once( "eztransaccion/classes/eztransaccion.php" );
	//$tr = new eZTransaccion( );
	include_once( "classes/sendmail.php" );
	eZLog::writeNotice( "At userbox (user) NACK ->" . print_r( $user, true ) . "|" );
	if ( $RedunSendMail == "yes" )
	{
		//eZLog::writeNotice( "At userbox (user) sending mail to ErrEMail1 ->" . $ErrEMail1 . "|" );	MAOS Oct2013 Quitar Notice
		//$tr->Send_Mail( $ErrEMail1, "BajíoNET. Error Servidor Seguro " . $srv_num . ".", "El Servidor Seguro ". $srv_num . " de BajíoNET no está respondiendo.\r\n\r\nFavor de avisar a los responsables." );
		sendmail( $ErrEMail1, "BajíoNET. Error Servidor Seguro " . $srv_num . ".", "El Servidor Seguro ". $srv_num . " de BajíoNET no está respondiendo.\r\n\r\nFavor de avisar a los responsables." );
		//eZLog::writeNotice( "At userbox (user) sending mail to ErrEMail2 ->" . $ErrEMail2 . "|" );	MAOS Oct2013 Quitar Notice
		//$tr->Send_Mail( $ErrEMail2, "BajíoNET. Error Servidor Seguro " . $srv_num . ".", "El Servidor Seguro ". $srv_num . " de BajíoNET no está respondiendo.\r\n\r\nFavor de avisar a los responsables." );
		sendmail( $ErrEMail2, "BajíoNET. Error Servidor Seguro " . $srv_num . ".", "El Servidor Seguro ". $srv_num . " de BajíoNET no está respondiendo.\r\n\r\nFavor de avisar a los responsables." );
	}
	/*
	$srv_num++;
	if ( $srv_num > $ServersInFarm ) {
		$srv_num = 1;
	}
	eZLog::writeNotice( "At userbox (user) redundancy redirect to next server ->" . print_r( $SecureSite . $srv_num . $DomainPostfix, true ) . "|" );
	eZHTTPTool::header( "Location: https://" . $SecureSite . $srv_num . $DomainPostfix . "/article/articleview/196/1/7" );
	*/
	eZUserBB::clearAutoCookieLogin();
	eZUserBB::logout();
	eZHTTPTool::header( "Location: https://" . $SecureSite . $srv_num . $DomainPostfix . "/article/articleview/86/1/7" );
	exit();
}
else
{
	$priv = " 00000000000000000000000000000000000000000000000000";

	$priv = $session->variable( "r_priv" );
	$usr = $session->variable( "r_usr" );
	$perfil = $session->variable("r_perfil");	// DVC-SYEO 181275 I
	$cust = $session->variable("r_cno");	//NXN-FOCA 21May2013 track 251114
	$qkif = $session->variable("r_qki" );   //NXN-FOCA 04Oct2013 track 251114

	//DBA Login Seguro
	$Segmento = $ini->read_var( "site", "Segmento" );

	if ($Segmento == "gobierno")	//Si es gobierno no deberan mostrarse: 8)SolChequera, Inform. Fin, 7)Tarjeta de Debito,
	{								//	32)PemexGas, 50)Aportación voluntaria AB, 5)Entero Recaudación IDE ni 16-20)Agenda de Pagos
		//echo "<br>Privs [" . $priv . "]<br>";
		//$priv = substr( $priv, 0, 7 ) . "00" . substr( $priv, 9, 16-9 ) . "00000" . substr( $priv, 21, 32-21 ) . "0" . substr( $priv, 33, 50-33 ) . "0" . substr( $priv, 51, 57-51 ) . "0" . substr( $priv, 58, 999 );
		$priv = substr( $priv, 0, 7 ) . "00" . substr( $priv, 9, 16-9 ) . "00000" . substr( $priv, 21, 32-21 ) . "0" . substr( $priv, 33, 50-33 ) . "0" . substr( $priv, 51, 999 ); //emedrano Se reutiliza priv 57 SPEIMovil
		//echo "<br>Privs [" . $priv . "]<br>";
	}
	//DBA Login Seguro


	$t = new eZTemplate( "eztransaccion/user/" .  $ini->read_var( "eZTransaccionMain", "TemplateDir" ), "eztransaccion/user/intl", $Language, "userbox.php" );
	$t->setAllStrings();

	if ($var_gobMenu === "enabled")
	{
		$t->set_file( "userboxNew_tpl", "userboxNew.tpl" );
		$t->set_block( "userboxNew_tpl", "menu_tpl", "menu" );
	}
	else
	{
		$t->set_file( "userbox_tpl", "userbox.tpl" );
		$t->set_block( "userbox_tpl", "menu_tpl", "menu" );
	}
	
	$t->set_block( "menu_tpl", "header_tpl", "header" );
	$t->set_block( "menu_tpl", "row_tpl", "row" );

	//ACS  Inicio Cambio Menu
	$NuevoMenu = 0;
	//ACS  - MenuContenidoAjax I
	$ProcesarAjax = $ini->read_var( "site", "ProcesarAjax" );
	$ModuloAjax = $ini->read_var( "site", "ModuloAjax" );
	//ACS  - MenuContenidoAjax F
	$UsarUpdDatCliente = $ini->read_var( "site", "UsarUpdDatCliente" );  // NEX DatCli
	$MenuDeluxeTree = $ini->read_var( "site", "MenuDeluxeTree" );
	$MenuDeluxeTree = trim( $MenuDeluxeTree );
	//REF WMA-24nov2008, Inicio
	$MenuDeluxeTreeDesactivado = $ini->read_var( "site", "MenuDeluxeTreeDesactivado" );
	$MenuDeluxeTreeDesactivado = trim( $MenuDeluxeTreeDesactivado );
	//REF WMA-24nov2008, Fin
	// JFL [8-Ene-2012] T-233421 (ini) > Login Amigable Bajionet
	$friendlyLoginActivo = 0;
	$flHabilitadoSesion = $session->variable( "isFriendlyLogin" );
	$flHabilitadoSesion = empty($flHabilitadoSesion) ? "False" : $flHabilitadoSesion; 
	
	if($flHabilitadoSesion == "True")
	{
		$friendlyLoginActivo = 1;
	}
	
	//eZLog::writeNotice( "En userbox FriendLyLogin Habilitado en Sesion => [$friendlyLoginActivo]" );
	// JFL [8-Ene-2012] T-233421 (fin) > Login Amigable Bajionet

	//echo "ICC - [" . $MenuDeluxeTree . "]<br>";
	if( $MenuDeluxeTree == "NuevoMenuBajioNET")	//A los usuarios que esten en el site ini o que tengan el privilegio les pinta el menu
	{ // Cambio de menu - FAF
		$NuevoMenu = 1;
	}
	else if( $MenuDeluxeTree == "ViejoMenuBajioNET" )	//A todos los usuarios les deja el menu anterior
	{
		$NuevoMenu = 0;
	}
	else
	{							//Busca por usuario, si esta en la lista le pinta el menu nuevo
	//REF WMA-24nov2008, Inicio. Se comenta el código anterior
		//$Users = array( );
		//$Users = explode( ',', $MenuDeluxeTree );
		////echo "ICC - Por usuario [" . $MenuDeluxeTree . "] usr[" . $usr . "]<br>";

		//foreach ($Users as $key => $value)
		//{
		//	$value = trim( $value );
		//	$value = strtoupper( $value );
		//	//echo "ICC - El usuario [" . $value . "]<br>";
		//	if ( $value == $usr )
		//	{
		//		$NuevoMenu = 1;
		//		break;
		//	}
		//}

		//si tiene el privilegio y no está en la variable de deshabilitados, muestro el nuevo menu
		if (substr( $priv,  56, 1 ) == "1")
		{
			$NuevoMenu = 1;

			$Users = array( );
			$Users = explode( ',', $MenuDeluxeTreeDesactivado );
			//echo "ICC - Por usuario [" . $MenuDeluxeTreeDesactivado . "] usr[" . $usr . "]<br>";

			foreach ($Users as $key => $value)
			{
				$value = trim( $value );
				$value = strtoupper( $value );
				//echo "ICC - El usuario [" . $value . "]<br>";
				if ( $value == $usr )
				{
					$NuevoMenu = 0;
					break;
				}
			}
		}
		else	//se mantiene la variable anterior por compatibilidad
		{
			$Users = array( );
			$Users = explode( ',', $MenuDeluxeTree );
			//echo "ICC - Por usuario [" . $MenuDeluxeTree . "] usr[" . $usr . "]<br>";

			foreach ($Users as $key => $value)
			{
				$value = trim( $value );
				$value = strtoupper( $value );
				//echo "ICC - El usuario [" . $value . "]<br>";
				if ( $value == $usr )
				{
					$NuevoMenu = 1;
					break;
				}
			}
		}

	//REF WMA-24nov2008, Fin

     }

?>

<!--ACS 09Ene2008 - MenuContenidoAjax-->
<script>
//JQUERY
function iniProcesarMenu( nombre, param)
{
	// JAC MAR2012 AUDITORIA INI
	// JAC JUN2012 INI
	// alert("nombre: " + nombre + " TknObligatorio: <?php echo $ini->read_var( "site", "TknObligatorio" ); ?>");
	<?php
//ACS 17Jul2012  Modificación Integración Auditoria Ini	
		//if ( $session->variable( "r_tkchk" ) == "True" || $ini->read_var( "site", "TknObligatorio" ) != "si" ) 
		$aux_dcl = $session->variable( "r_dcl" );
		 if (substr($aux_dcl, 2, 2) == "si" && substr($aux_dcl, 0, 2) != "sn" && substr($aux_dcl, 0, 2) != "cd" )
		 {
			$aux_dcl = "True";
		 }
		
		if ( $aux_dcl == "True" || $session->variable( "r_tkchk" ) == "True" || $ini->read_var( "site", "TknObligatorio" ) != "si" ) 
//ACS 17Jul2012  Modificación Integración Auditoria Fin		
		{
	?>
	// JAC MAR2012 AUDITORIA FIN
	// JAC JUN2012 INI
	if ((param == '' && (nombre.toString() == "speuaTT" || nombre.toString() == "pagointerbancario" || nombre.toString() == "transFiltro")) || (nombre.toString() != "speuaTT" && nombre.toString() != "pagointerbancario" && nombre.toString() != "transFiltro"))
	{
		document.getElementById('mainContents').innerHTML="<br><br><br><br><br><br><br><br><br><div align=center style='width:85%;'><img src=\"https://images.bb.com.mx/sitedesign/bajionet/images/loading.gif\"	style=\"align: center;cursor: pointer; border: 0px solid red;\" title=\"cargando\" alt=\"cargando . . .\" align=center /><br>Cargando...</div>";
	}
	// vmv - 129451
	if (nombre.toString() == "transFiltro") {
		$('#cmbBeneDIV').load("/procesarAjaxMenu.php",{nomFunc:nombre.toString(),parametro:param.toString() });
	}
	else {
		$('#mainContents').load("/procesarAjaxMenu.php",{nomFunc:nombre.toString(),parametro:param.toString() });
	}
	// vmv -129451
	// JAC MAR2012 AUDITORIA INI
	<?php
		}
		else 
		{
	?>
	// JAC JUN2012 INI
	if( nombre == 'clave' || nombre == 'pinasb' || nombre == 'synchronizeasb' || nombre == 'testasb' || nombre == 'pinsofttoken' ) 
	{
		if ((param == '' && (nombre.toString() == "speuaTT" || nombre.toString() == "pagointerbancario" || nombre.toString() == "transFiltro")) || (nombre.toString() != "speuaTT" && nombre.toString() != "pagointerbancario" && nombre.toString() != "transFiltro"))
		{
			document.getElementById('mainContents').innerHTML="<br><br><br><br><br><br><br><br><br><div align=center style='width:85%;'><img src=\"https://images.bb.com.mx/sitedesign/bajionet/images/loading.gif\"	style=\"align: center;cursor: pointer; border: 0px solid red;\" title=\"cargando\" alt=\"cargando . . .\" align=center /><br>Cargando...</div>";
		}
		// vmv - 129451
		if (nombre.toString() == "transFiltro") {
			$('#cmbBeneDIV').load("/procesarAjaxMenu.php",{nomFunc:nombre.toString(),parametro:param.toString() });
		}
		else {
			$('#mainContents').load("/procesarAjaxMenu.php",{nomFunc:nombre.toString(),parametro:param.toString() });
		}
	}
	else
	{
		window.location="/ingresaBB/logout/";
	}
	// JAC JUN2012 INI
	<?php
		}
	?>
	// JAC MAR2012 AUDITORIA INI
}
//18sep2009 ACS   	    Altas en Línea.  Nomina
function procesarConsulta (nombrePag, paramPag, paramCase, paramloading) 
{
	$("#"+paramCase).load("/procesarAjaxMenu.php",{nomFunc:nombrePag.toString(),parametro:paramPag.toString() },function(){document.getElementById(paramloading).style.visibility="hidden"; document.getElementById(paramCase).style.visibility="visible";});
} 
//18sep2009 ACS   	    Altas en Línea.  Nomina 

//Nex Middleware - Oct 2011 - INI

function iniProcesarMenuMiddleware( nombre, param)
{

	$('#mainContents').html("<br><br><br><br><br><br><br><br><br><div align=center style='width:85%;'><img src=\"https://secure<?php echo $srv_num ?>.bb.com.mx/sitedesign/bajionet/images/loading.gif\"        style=\"align: center;cursor: pointer; border: 0px solid red;\" title=\"cargando\" alt=\"cargando . . .\" align=center /><br>Cargando...</div>");

	//$('#mainContents').load("/procesarAjaxMiddleware.php",{modulo: nombre, accion: param });
	$('#mainContents').load("/procesarAjaxMiddleware.php",{modulo: nombre, action: param });
}


//Nex Middleware - Oct 2011 - INI

//NXN-FOCA 21May2013 track 251114
function iniProcesarMenuJSF( nombre, param, rqs)
{
	actualizarTiempo();
	Init();
   	$('#mainContents').html("<br><br><br><br><br><br><br><br><br><div align=center style='width:85%;'><img src=\"https://secure<?php echo $srv_num ?>.bb.com.mx/sitedesign/bajionet/images/loading.gif\"        style=\"align: center;cursor: pointer; border: 0px solid red;\" title=\"cargando\" alt=\"cargando . . .\" align=center /><br>Cargando...</div>");

	if (nombre == 'foca')
	{
		if (param == 'saldosproducto')
		{
			$.ajax({
				 type: "POST",
				 url: "/web-foca/saldosFondoAhorro.html",
				 data: "param1=<?php echo $usr ?>&param2=<?php echo $cust ?>&param3=<?php echo $qkif ?>",
				 success: function(msg){
				 $("#mainContents").html(msg);
				},
				error: function(XMLHttpRequest, textStatus, errorThrown) {
					
				}
			});
		}
		if (param == 'cargaarchivos')
		{

			$.ajax({
				 type: "POST",
				 url: "/web-foca/cargaArchivo.html",
				 data: "param1=<?php echo $usr ?>&param2=<?php echo $cust ?>&param3=<?php echo $qkif ?>",
				 success: function(msg){
				 $("#mainContents").html(msg);
				},
				error: function(XMLHttpRequest, textStatus, errorThrown) {
					
				}
			});
			
		}
		if (param == 'solindi')
		{
			$.ajax({
				 type: "POST",
				 url: "/web-foca/solicitudesIndividuales.html",
				 data: "param1=<?php echo $usr ?>&param2=<?php echo $cust ?>&param3=<?php echo $qkif ?>",
				 success: function(msg){
				 $("#mainContents").html(msg);
				},
				error: function(XMLHttpRequest, textStatus, errorThrown) {
					
				}
			});
		}
		if (param == 'libarc')
		{
			$.ajax({
				 type: "POST",
				 url: "/web-foca/archivosLiberacion.html",
				 data: "param1=<?php echo $usr ?>&param2=<?php echo $cust ?>&param3=<?php echo $qkif ?>",
				 success: function(msg){
				 $("#mainContents").html(msg);
				},
				error: function(XMLHttpRequest, textStatus, errorThrown) {
			
				}
			});
		}
		if (param == 'consarc')
		{
			$.ajax({
				 type: "POST",
				 url: "/web-foca/archivosSolicitudes.html",
				 data: "param1=<?php echo $usr ?>&param2=<?php echo $cust ?>&param3=<?php echo $qkif ?>",
				 success: function(msg){
				 $("#mainContents").html(msg);
				},
				error: function(XMLHttpRequest, textStatus, errorThrown) {
				
				}
			});
		}
		if (param == 'resumenmov')
		{
			$.ajax({
				 type: "POST",
				 url: "/web-foca/saldoResumenMovimientos.html",
				 data: "param1=<?php echo $usr ?>&param2=<?php echo $cust ?>&param3=<?php echo $qkif ?>",
				 success: function(msg){
				 $("#mainContents").html(msg);
				},
				error: function(XMLHttpRequest, textStatus, errorThrown) {
			
				}
			});
		}
		if (param == 'saldosempl')
		{
			$.ajax({
				 type: "POST",
				 url: "/web-foca/saldosEmpleados.html",
				 data: "param1=<?php echo $usr ?>&param2=<?php echo $cust ?>&param3=<?php echo $qkif ?>",
				 success: function(msg){
				 $("#mainContents").html(msg);
				},
				error: function(XMLHttpRequest, textStatus, errorThrown) {
				
				}
			});
		}
		if (param == 'detallemovempl')
		{
			$.ajax({
				 type: "POST",
				 url: "/web-foca/saldoDetalleEmpleado.html",
				 data: "param1=<?php echo $usr ?>&param2=<?php echo $cust ?>&param3=<?php echo $qkif ?>",
				 success: function(msg){
				 $("#mainContents").html(msg);
				},
				error: function(XMLHttpRequest, textStatus, errorThrown) {
			
				}
			});
		}
		if (param == 'estadomov')
		{
			$.ajax({
				 type: "POST",
				 url: "/web-foca/estadoCuentaCliente.html",
				 data: "param1=<?php echo $usr ?>&param2=<?php echo $cust ?>&param3=<?php echo $qkif ?>",
				 success: function(msg){
				 $("#mainContents").html(msg);
				},
				error: function(XMLHttpRequest, textStatus, errorThrown) {
			
				}
			});
		}
		if (param == 'resmenempl')
		{
			$.ajax({
				 type: "POST",
				 url: "/web-foca/estadoCuentaEmpleado.html",
				 data: "param1=<?php echo $usr ?>&param2=<?php echo $cust ?>&param3=<?php echo $qkif ?>",
				 success: function(msg){
				 $("#mainContents").html(msg);
				},
				error: function(XMLHttpRequest, textStatus, errorThrown) {
			
				}
			});
		}
		if (param == 'consapor')
		{
			$.ajax({
				 type: "POST",
				 url: "/web-foca/constanciasAporIndividuales.html",
				 data: "param1=<?php echo $usr ?>&param2=<?php echo $cust ?>&param3=<?php echo $qkif ?>",
				 success: function(msg){
				 $("#mainContents").html(msg);
				},
				error: function(XMLHttpRequest, textStatus, errorThrown) {
			
				}
			});
		}
		if (param == 'tasasrend')
		{
			$.ajax({
				 type: "POST",
				 url: "/web-foca/consultaTasas.html",
				 data: "param1=<?php echo $usr ?>&param2=<?php echo $cust ?>&param3=<?php echo $qkif ?>",
				 success: function(msg){
				 $("#mainContents").html(msg);
				},
				error: function(XMLHttpRequest, textStatus, errorThrown) {
		
				}
			});
		}
		//02feb2014 NXN-FOCA-ContratosCerrados Inicio
		if (param == 'saldosvenc')
		{
			$.ajax({
				 type: "POST",
				 url: "/web-foca/saldosEmpleadosCierre.html",
				 data: "param1=<?php echo $usr ?>&param2=<?php echo $cust ?>&param3=<?php echo $qkif ?>",
				 success: function(msg){
				 $("#mainContents").html(msg);
				},
				error: function(XMLHttpRequest, textStatus, errorThrown) {
		
				}
			});
		}
		//02feb2014 NXN-FOCA-ContratosCerrados Fin
	}
}
//NXN-FOCA 21May2013 track 251114


//REF DHE-TESIN-31jul2014.[BEGIN]
function iniProcesarMenuTESO( menuItem, submenuItem, rqs)
{
	actualizarTiempo();
	Init();
   	$('#mainContents').html("<br><br><br><br><br><br><br><br><br><div align=center style='width:85%;'><img src=\"https://secure<?php echo $srv_num ?>.bb.com.mx/sitedesign/bajionet/images/loading.gif\"        style=\"align: center;cursor: pointer; border: 0px solid red;\" title=\"cargando\" alt=\"cargando . . .\" align=center /><br>Cargando...</div>");

	var urlRequest = '';
	if (menuItem == 'teso')
	{
		if (submenuItem == 'admoncuentas')
		{
			urlRequest = "/web-teso/admonCtas.html";
		}
		else if(submenuItem == 'dispersionesprog')
		{
			urlRequest = "/web-teso/dispersionesProg.html";
		}
		else if(submenuItem == 'mapacuentas')
		{
			urlRequest = "/web-teso/mapaCtas.html";
		}
		// El direccionamiento a los reportes en línea se hacen de la forma ya implementada en BajíoNet
		//else if(submenuItem == 'reporteLinea')
		//{
		//	urlRequest = "/web-teso/Reportes.html";
		//}
		else if(submenuItem == 'reporteArchivo')
		{
			urlRequest = "/web-teso/reporteArchivo.html";
		}

		if(urlRequest !== '')
		{
			$.ajax({
				 type: "POST",
				 url: urlRequest,
				 data: "param1=<?php echo $usr ?>&param2=<?php echo $cust ?>&param3=<?php echo $qkif ?>",
				 success: function(msg){  $("#mainContents").html(msg);	},
				 error: function(XMLHttpRequest, textStatus, errorThrown) {$("#mainContents").html("<h2>Ocurrió un error al procesar la solicitud</h2>");}
			});
		}
	}
}
//REF DHE-TESIN-31jul2014.[END]
</script>






<?php
//21Mar2012 ACS   Exclisividad de servicio BajionetMovil  Inicio
	if ($session->variable( "r_bnetmov") == "si")
	{
		eZUserBB::clearAutoCookieLogin();
		eZUserBB::logout();
		header( "Location: /ingresaBB/ExclusiveBm" );
		exit();
	}
//21Mar2012 ACS   Exclisividad de servicio BajionetMovil  Fin
	//ACS  - MenuContenidoAjax I
	$AjaxActivo=1;


	if($ProcesarAjax!='Ninguno')
	{
		$UsersAjax = array( );
		$UsersAjax = explode( ',', $ProcesarAjax );

		foreach ($UsersAjax as $keyA => $valueAjax)
		{
			$valueAjax = trim( $valueAjax );
			$valueAjax = strtoupper( $valueAjax );

			if ( $valueAjax == $usr )
			{
				$AjaxActivo = 0;
				break;
			}

		}
	}
	else //if ($ProcesarAjax=='Ninguno')
	{
		$AjaxActivo = 0;
	}


	//ACS  - MenuContenidoAjax F





// NEX Inicio DatCli

$dcl = $session->variable( "r_dcl" );
//si->Despliega menu pequeño con clave validacion 
//sn-> Despliega menu pequeño sin clave validacion

//06May2010   ACS  Llave ASB para Circular X I
$session->setVariable( "r_tknOp", $ini->read_var( "site", "TknObligatorio" ) );

//echo "Entra a esta parte -------> r_tkact[".$session->variable( "r_tkact" )."] el tk obligatorio[".$session->variable( "r_tknOp" )."]";
//echo "<br><br><br>".$session->variable( "r_cliTkn" );
//Si 
if ($session->variable( "r_tknOp" ) == "no" )
{//echo "La variable es identica a no [".$session->variable( "r_tknOp" )."]";
//06May2010   ACS  Llave ASB para Circular X F
// NEX Inicio DatCli 17Jun2010
//	if ( ( substr($dcl, 0, 2) == "si" || substr($dcl, 0, 2) == "sn" ) && $UsarUpdDatCliente == 1 )
//	if ( ( substr($dcl, 0, 2) == "si" || substr($dcl, 0, 2) == "sn" || substr($dcl, 2, 2) == "si"  ) && $UsarUpdDatCliente == 1 )
	if ( ( substr($dcl, 0, 2) == "si" || substr($dcl, 0, 2) == "sn" || substr($dcl, 2, 2) == "si"   ) && $UsarUpdDatCliente == 1 )
// NEX Fin DatCli 17Jun2010
	{
		if ((substr($dcl, 2, 2) == "si" || substr($dcl, 0, 2) == "sn") && substr($dcl, 0, 2) != "cd" )
		{

			include_once( "userboxdcl.php" );
		}
		else
		{
			include_once( "userboxgral.php" );
		}
	}
	else
	{
		include_once( "userboxgral.php" );
	}
// NEX Fin DatCli
//06May2010   ACS  Llave ASB para Circular X I	
}
else
{
	if ( $session->variable( "r_tkact" ) == "True" )
	{
// NEX Inicio DatCli 17Jun2010
//		if ( substr($dcl, 0, 2) == "si" || substr($dcl, 0, 2) == "sn"  )
		if ( (substr($dcl, 0, 2) == "si" || substr($dcl, 0, 2) == "sn" || substr($dcl, 2, 2) == "si" ) && $UsarUpdDatCliente == 1 && substr($dcl, 0, 2) != "na")
//		if (( substr($dcl, 0, 2) != "cd" && substr($dcl, 2, 2) == "si" ) && $UsarUpdDatCliente == 1 )
// NEX Fin DatCli 17Jun2010
		{
			if ((substr($dcl, 2, 2) == "si" || substr($dcl, 0, 2) == "sn") && substr($dcl, 0, 2) != "cd" )
			{
				include_once( "userboxdcl.php" );
			}
			else
			{
				if ($session->variable( "r_cliTkn" ) == "si" )
				{
					include_once( "userboxgral.php" );
				}
				else
				{
					include_once( "userboxtkn.php" );
				}
			}
		}
		else
		{
		
			if ($session->variable( "r_cliTkn" ) == "si" )
			{
				include_once( "userboxgral.php" );
			}
			else
			{
				include_once( "userboxtkn.php" );
			}
		}
	}
	else
	{
		include_once( "userboxtkn.php" );
	}
}
//06May2010   ACS  Llave ASB para Circular X F
}
?>
