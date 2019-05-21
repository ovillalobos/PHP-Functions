
<?php
include_once( "eztransaccion/classes/ezlog.php" );
// Cambio de menu - FAF
	if ( $NuevoMenu == 1 )
	{
		if ( $la_s == "s" )
		{
?>

		<script type="text/javascript">var dmWorkPath="http<?php print $la_s; ?>://<?php print $images_site; ?>/sitedesign/<?php print ($GlobalSiteDesign); ?>/js/menu/deluxe-tree.files/";</script>
		<!-- Deluxe Tree -->
		<noscript>
			<a href="http://deluxe-tree.com [http://deluxe-tree.com]">JavaScript Tree Menu by Deluxe-Tree.com</a>
		</noscript>
	<!--ACS  Inicio Cambio Menu -->
		<!-- <script type="text/JavaScript" language="JavaScript1.2" src="http<?php print $la_s; ?>://<?php print $images_site; ?>/sitedesign/<?php print ($GlobalSiteDesign); ?>/js/menu/deluxe-tree.files/dtree.js"></script>-->
		<script type="text/JavaScript" language="JavaScript1.2" src="http<?php print $la_s; ?>://<?php print ($_SERVER['HTTP_HOST']); ?>/dtree.js"></script>
	<!--ACS  Fin Cambio Menu-->
		<!-- Copyright (c) 2006, Deluxe Tree, deluxe-tree.com [http://deluxe-tree.com] -->

<?php
			echo "<script type=\"text/javascript\">";

			include("menu/deluxe-tree.js");

			echo "</script>";
		}
	}// Cambio de menu - FAF
	else
	{
// Cambio de menu - FAF
	$t->set_var( "header_menu_bloque", "Servicios" );
	$t->parse( "header", "header_tpl" );

	if ($var_gobMenu != "enabled")
	{
		$t->set_var( "menu_liga", "/userbb/login/logout/" );
		$t->set_var( "menu_nombre_liga", "<font color='#FF0000'><b>S a l i r</b></font>" );
		$t->parse( "row", "row_tpl", true );
	}

	if ( strpos( $priv, "1" ) !== false )
	{
		//ACS  - MenuContenidoAjax I
		//$t->set_var( "menu_liga", "/transaccion/clave/" );
		if ($AjaxActivo===1 && strpos($ModuloAjax,'pwd')===false)
		{
			$t->set_var( "menu_liga", "javascript:iniProcesarMenu('clave', '')" );
		}
		else
		{
			$t->set_var( "menu_liga", "/transaccion/clave/" );
		}
		//ACS  - MenuContenidoAjax F
		$t->set_var( "menu_nombre_liga", "Cambio Clave de Acceso" );
		$t->parse( "row", "row_tpl", true );
	}

	if ($var_gobMenu != "enabled")
	{
		if ( strpos( $priv, "1" ) !== false )
		{
			$t->set_var( "menu_liga", "/article/articleview/196/1/7" );
			$t->set_var( "menu_nombre_liga", "Inicio" );
			$t->parse( "row", "row_tpl", true );
		}
	}

	if ( $session->variable( "r_cvop" ) == "Clave Operacional" )
	{
		$t->set_var( "menu_liga", "/transaccion/claveOper/" );		//ICC Tokens, 15Jan2007
		$t->set_var( "menu_nombre_liga", "Cambio Clave de Operación" );
		$t->parse( "row", "row_tpl", true );
	}
	// DBA Reimpresion de Comprobantes
	    $t->set_var( "menu_liga", "/transaccion/reimpresion/" );
		$t->set_var( "menu_nombre_liga", "Reimpresi&oacute;n de Comprobantes" );
		$t->parse( "row", "row_tpl", true );
    	// DBA Reimpresion de Comprobantes
	// RAI Reportes Comprobantes Multipago
	    $t->set_var( "menu_liga", "/transaccion/comprobantesmultipago/" );
		$t->set_var( "menu_nombre_liga", "Reportes Comprobantes Multipago " );
		$t->parse( "row", "row_tpl", true );
    //RAI Reportes Comprobantes Multipago
	// DBA Cuenta CLABE
		    $t->set_var( "menu_liga", "/transaccion/clabe/" );
			$t->set_var( "menu_nombre_liga", "Consulta de CLABE y Tarjeta de Débito" );
			$t->parse( "row", "row_tpl", true );
	// DBA Cuenta CLABE

	
		//emedrano Ini SPEIMovil
		//if( substr( $priv, 56, 1 ) == "1" )
		if( substr( $priv, 57, 1 ) == "1" )
		{			
			$t->set_var( "menu_liga", "javascript:iniProcesarMenu('speimovil', '')" );
			$t->set_var( "menu_nombre_liga", "Asociar Tel. Móvil a Cuenta" );
			$t->parse( "row", "row_tpl", true );
		}
		//emedrano Fin SPEIMovil
	$t->parse( "menu", "menu_tpl", true );
	$t->set_var( "row", "" );
	$t->set_var( "header_menu_bloque", "" );

	if ( $session->variable( "r_cvop" ) == "Clave ASB (Acceso Seguro Bajio)" )
	{
		$t->set_var( "header_menu_bloque", "ASB" );
		$t->parse( "header", "header_tpl" );

		if ( $session->variable( "r_tkact" ) == "True" )
		{
			//ACS  - MenuContenidoAjax I
			if ($AjaxActivo===1 && strpos($ModuloAjax,'sync')===false)
			{
				$t->set_var( "menu_liga", "javascript:iniProcesarMenu('synchronizeasb', '')" );
			}
			else
			{
				$t->set_var( "menu_liga", "/transaccion/synchronizeasb/" );
			}
			//ACS  - MenuContenidoAjax I
			$t->set_var( "menu_nombre_liga", "Sincronizaci&oacute;n Clave ASB" );
			$t->parse( "row", "row_tpl", true );
			//ACS  - MenuContenidoAjax I
			if ($AjaxActivo===1 && strpos($ModuloAjax,'pinasb')===false)
			{
				$t->set_var( "menu_liga", "javascript:iniProcesarMenu('pinasb', '')" );
			}
			else
			{
				$t->set_var( "menu_liga", "/transaccion/pinasb/" );
			}
			//ACS  - MenuContenidoAjax I
			$t->set_var( "menu_nombre_liga", "Cambio NIP ASB" );
			$t->parse( "row", "row_tpl", true );

			//ACS  - MenuContenidoAjax I
			if ($AjaxActivo===1 && strpos($ModuloAjax,'test')===false)
			{
				$t->set_var( "menu_liga", "javascript:iniProcesarMenu('testasb', '')" );
			}
			else
			{
				$t->set_var( "menu_liga", "/transaccion/testasb/" );
			}
			//ACS  - MenuContenidoAjax I
			$t->set_var( "menu_nombre_liga", "Prueba Clave ASB" );
			$t->parse( "row", "row_tpl", true );
		}
		else
		{
			// JFL [8-Ene-2012] T-233421 (ini) > Login Amigable Bajionet
			if($friendlyLoginActivo == 0) //Cuando no esta activo el login amigable
			{
				$t->set_var( "menu_liga", "/transaccion/activateasb/" );
				$t->set_var( "menu_nombre_liga", "Activaci&oacute;n Llave ASB" );
				$t->parse( "row", "row_tpl", true );
			}
			// JFL [8-Ene-2012] T-233421 (fin) > Login Amigable Bajionet
		}

// LVPR I Mzo2007 Manual ASB
    $t->set_var( "menu_liga", "javascript:function PopUp(){window.open( 'http://images.bb.com.mx/ezmediacatalogue/catalogue/GuiaASB.pdf', 'newwindow', 'toolbar = 0, location = 0, directories = 0, status = 0, menubar = 0, scrollbars = 0, resizeable = 0, width = 600, height = 400' );} PopUp();" );
    $t->set_var( "menu_nombre_liga", "Gu&iacute;a de Usuario Llave ASB" );
    $t->parse( "row", "row_tpl", true );
// LVPR F Mzo2007 Manual ASB

		$t->parse( "menu", "menu_tpl", true );
		$t->set_var( "row", "" );
		$t->set_var( "header_menu_bloque", "" );
	}

	if ( strpos( $priv, "1" ) !== false )
	{
		if
			(
					substr( $priv,  0, 1 ) == "1"
				||	substr( $priv, 22, 1 ) == "1"
				||	substr( $priv, 23, 1 ) == "1"
				||	substr( $priv,  2, 1 ) == "1"
				||	substr( $priv,  6, 1 ) == "1"
				||	substr( $priv,  9, 1 ) == "1"
				||	substr( $priv, 55, 1 ) == "1"	//emedrano 09Abr2014 nuevo servicio
				||	substr( $priv,  7, 1 ) == "1"
				||	substr( $priv,  5, 1 ) == "1"
				||	substr( $priv, 10, 1 ) == "1"
				||	substr( $priv,  8, 1 ) == "1"
				||	substr( $priv, 21, 1 ) == "1"
				||	substr( $priv, 13, 1 ) == "1"
				||	substr( $priv, 49, 1 ) == "1"	// DGM 25Oct2006 Protección de Cheques
//				||	substr( $priv, 50, 1 ) == "1"	// DGM 25Mar2007 Aportaciones Voluntarias por BajionNet
				||	substr( $priv, 57, 1 ) == "1"	//SY-YEHO 27Oct2008 Entero Recaudacion IDE de Instituciones No Auxiliares
			)
		{

			$t->set_var( "header_menu_bloque", "Operaciones" );
			$t->parse( "header", "header_tpl" ); // Atencion, no lleva TRUE porque es elemento unico de su tipo en el bloque

			if(substr($priv, 0, 1)=="1") { // pro
			//REF WMA-27ene2009, Inicio
				//$t->set_var( "menu_liga", "/transaccion/saldos/" );
				$t->set_var( "menu_liga", "javascript:iniProcesarMenu('saldos','')" );
			//REF WMA-27ene2009, Fin
				$t->set_var( "menu_nombre_liga", "Saldos" );
				$t->parse( "row", "row_tpl", true );

				// JAC NOV2011 - SE AGRUPAN FUNCIONALIDADES DE CREDITOS INI
				//aluna 13may2008 I Saldos Credito
				//$t->set_var( "menu_liga", "/transaccion/saldoscre/" );
				//$t->set_var( "menu_nombre_liga", "Saldos Crédito" );
				//$t->parse( "row", "row_tpl", true );
				//aluna 13may2008 F Saldos Credito
				// JAC NOV2011 - SE AGRUPAN FUNCIONALIDADES DE CREDITOS FIN

				//// JAC NOV2011 - SE AGRUPAN FUNCIONALIDADES DE CREDITOS INI
				//// Resumen de linea
				//$t->set_var( "menu_liga", "javascript:iniProcesarMenu('pre_page','redirect:res_linea')" );
				//$t->set_var( "menu_nombre_liga", "Resumen de l&iacute;nea" );
				//$t->parse( "row", "row_tpl", true );
                //
				//// Saldos de creditos
				//$t->set_var( "menu_liga", "javascript:iniProcesarMenu('pre_page', 'redirect:saldoscre')" );
				//$t->set_var( "menu_nombre_liga", "Saldos de cr&eacute;dito" );
				//$t->parse( "row", "row_tpl", true );
                //
				//// Consulta depositos en efectivo
				//$t->set_var( "menu_liga", "javascript:iniProcesarMenu('cons_depo','')" );
				//$t->set_var( "menu_nombre_liga", "Consulta dep&oacute;sitos en efectivo" );
				//$t->parse( "row", "row_tpl", true );
                //
				//// Estados de cuenta de creditos
				//$t->set_var( "menu_liga", "javascript:iniProcesarMenu('edoctacre','')" );
				//$t->set_var( "menu_nombre_liga", "Estado de cuenta de cr&eacute;ditos" );
				//$t->parse( "row", "row_tpl", true );
                //
				//// Calculo de proximos pagos
				//$t->set_var( "menu_liga", "javascript:iniProcesarMenu('pre_page','redirect:calc_next')" );
				//$t->set_var( "menu_nombre_liga", "C&aacute;lculo de pr&oacute;ximos pagos" );
				//$t->parse( "row", "row_tpl", true );
                //
				//// Calculo de pagos anticipados
				//$t->set_var( "menu_liga", "javascript:iniProcesarMenu('calc_pago','')" );
				//$t->set_var( "menu_nombre_liga", "C&aacute;lculo de pagos anticipados" );
				//$t->parse( "row", "row_tpl", true );
                //
				//// Preguntas Frecuentes
				//$t->set_var( "menu_liga", "javascript:iniProcesarMenu('cred_faqs','')" );
				//$t->set_var( "menu_nombre_liga", "Preguntas Frecuentes" );
				//$t->parse( "row", "row_tpl", true );
				//// JAC NOV2011 - SE AGRUPAN FUNCIONALIDADES DE CREDITOS FIN
			}

			if(substr($priv, 22, 1)=="1") { // mef
				$t->set_var( "menu_liga", "/transaccion/saldosMesa/" );
				$t->set_var( "menu_nombre_liga", "Mesa de Dinero y/o Operaciones Especiales" );
				$t->parse( "row", "row_tpl", true );
			}

			if(substr($priv, 23, 1)=="1") {
				$t->set_var( "menu_liga", "/transaccion/saldosFondos/" );
				$t->set_var( "menu_nombre_liga", "Fondos de Inversión" );
				$t->parse( "row", "row_tpl", true );
			}

			if( substr( $priv, 2, 1 ) == "1" ) // mib
			{
			//REF WMA-27ene2009, Inicio
				//$t->set_var( "menu_liga", "/transaccion/bitacora/" );
				$t->set_var( "menu_liga", "javascript:iniProcesarMenu('bitacoraIBNK', '')" );
			//REF WMA-27ene2009, Fin
				$t->set_var( "menu_nombre_liga", "Bitacora de Movimientos en Internet" );
				$t->parse( "row", "row_tpl", true );

			//REF WMA-27ene2009, Inicio
				//$t->set_var( "menu_liga", "/transaccion/movimientos/" ); //hst
				$t->set_var( "menu_liga", "javascript:iniProcesarMenu('movimientos', '')" ); //hst
			//REF WMA-27ene2009, Fin
				$t->set_var( "menu_nombre_liga", "Movimientos en Línea" ); //DBA CFD 182332
				$t->parse( "row", "row_tpl", true );
				//DBA I CFD 182332
				$t->set_var( "menu_liga", "javascript:iniProcesarMenu('estadosdecuenta', 'EdoLinea')" ); //hst
				$t->set_var( "menu_nombre_liga", "Movimientos por Archivo" ); //DBA CFD 182332
				$t->parse( "row", "row_tpl", true );
				//DBA F CFD 182332
			}

			if(substr($priv, 6, 1)=="1") { // stm
				//ACS  - MenuContenidoAjax I
				if ($AjaxActivo===1 && strpos($ModuloAjax,'stm')===false)
				{
					$t->set_var( "menu_liga", "javascript:iniProcesarMenu('estadosdecuenta', '')" );
				}
				else
				{
					$t->set_var( "menu_liga", "/transaccion/estadosdecuenta/" );
				}
				//ACS  - MenuContenidoAjax I
				$t->set_var( "menu_nombre_liga", "Estados de cuenta" );
				$t->parse( "row", "row_tpl", true );
			}

			if(substr($priv, 9, 1)=="1") { // pzo
				//EMC I - Incluir variable para habilitar el nuevo modulo de inversiones
				if ( trim($ini->read_var( "site", "hbltInversion")) == "SI" ){
					$t->set_var( "menu_liga", "javascript:iniProcesarMenuMiddleware('maininver', '')" );
				}
				else{
					$t->set_var( "menu_liga", "/transaccion/inversion/" );
				}
				//$t->set_var( "menu_liga", "/transaccion/inversion/" );
				//EMC F - Rediseño inversiones
				$t->set_var( "menu_nombre_liga", "Inversiones" );
				$t->parse( "row", "row_tpl", true );
			}
			//aluna I 27nov08 liga a pago de servicios nuevo
			if(substr($priv, 55, 1)=="1") { // pzo
				$t->set_var( "menu_liga", "/transaccion/pagodeserviciosnuevo/" );
				$t->set_var( "menu_nombre_liga", "Pago de servicios" ); //OVVC [21/10/2013] Eliminar texto "nuevo" en pago de servicios.
				$t->parse( "row", "row_tpl", true );
			}
			//aluna  F 27nov08 liga a pago de servicios nuevo

			/*ACDP Junio 2014
			if(substr($priv, 3, 1)=="1") { // bpy
				//ACS  - MenuContenidoAjax I
				//$t->set_var( "menu_liga", "/transaccion/pagoservicios/" );
				if ($AjaxActivo===1 && strpos($ModuloAjax,'bpy')===false)
				{
					$t->set_var( "menu_liga", "javascript:iniProcesarMenu('pagoservicios', '')" );
				}
				else
				{
					$t->set_var( "menu_liga", "/transaccion/pagoservicios/" );
				}
				//ACS  - MenuContenidoAjax F
				$t->set_var( "menu_nombre_liga", "Pago de servicios" );
				$t->parse( "row", "row_tpl", true );

				$t->set_var( "menu_liga", "/transaccion/CatalogoReferencia/" ); //cre
				$t->set_var( "menu_nombre_liga", "Pago Express" );
				$t->parse( "row", "row_tpl", true );

			// *******************************************************
			// DVC Pago Automatico de Tarjeta de Credito
				// $t->set_var( "menu_liga", "/transaccion/PagoAutTarjeta/" ); //pat
				// $t->set_var( "menu_nombre_liga", "Pago Automático Tarjeta de Crédito BB" );
				// $t->parse( "row", "row_tpl", true );
			// DVC Pago Automatico de Tarjeta de Credito
			// *******************************************************
			}
			*/
			//ACDP
			if(substr($priv, 3, 1)=="1") { // fondeo
				$t->set_var( "menu_liga", "/transaccion/fondeo/" );
				$t->set_var( "menu_nombre_liga", "Fondeo Enlace Financiero" );
				$t->parse( "row", "row_tpl", true );
			}
			// *******************************************************
			// SY-YEHO I 27Oct2008 Entero IDE Instituciones No Auxiliares
			/*if(substr($priv, 57, 1) == "1")
			{
				$t->set_var( "menu_liga", "/transaccion/ide/" ); //IDENoAux
				$t->set_var( "menu_nombre_liga", "Entero Recaudacion IDE" );
				$t->parse( "row", "row_tpl", true );
			}*/
			// SY-YEHO F 27Oct2008 Entero IDE Instituciones No Auxiliares
			// *******************************************************

				//ACS Inicia - Cambio menu
				// if(substr($priv, 7, 1)=="1") { // crd
					// $t->set_var( "menu_liga", "/transaccion/tarjeta/" );
					// $t->set_var( "menu_nombre_liga", "Tarjeta" );
					// $t->parse( "row", "row_tpl", true );
				// }
				//ACS Fin - Cambio menu

			if(substr($priv, 5, 1)=="1") { // chq
				$t->set_var( "menu_liga", "/transaccion/busquedacheques/" );
				$t->set_var( "menu_nombre_liga", "B&uacute;squeda de Cheques" );
				$t->parse( "row", "row_tpl", true );
			}


			if(substr($priv, 10, 1)=="1") { // rch
				$t->set_var( "menu_liga", "/transaccion/robo/" );
				$t->set_var( "menu_nombre_liga", "Reporte de Extravío o Robo de Cheques" );
				$t->parse( "row", "row_tpl", true );
			}

			if(substr($priv, 8, 1)=="1") { // sch
				$t->set_var( "menu_liga", "/transaccion/chequera/" );
				$t->set_var( "menu_nombre_liga", "Solicitud de Chequera" );
				$t->parse( "row", "row_tpl", true );
			}

			// *****************************************************************************
			//	DGM 07Ago2005 Proteccion de Cheques lch
			if(substr($priv, 49, 1)=="1")
			{
				$t->set_var( "menu_liga", "/transaccion/liberacioncheques/" );
				$t->set_var( "menu_nombre_liga", "Liberación de Cheques" );
				$t->parse( "row", "row_tpl", true );
			}
			// *****************************************************************************


				// if(substr($priv, 21, 1)=="1") { // tct
					// $t->set_var( "menu_liga", "/transaccion/saldosTC/" );
					// $t->set_var( "menu_nombre_liga", "Tarjetas de Crédito" );
					// $t->parse( "row", "row_tpl", true );
				// }

			if(substr($priv, 13, 1)=="1") { // aut
			//ACS  - MenuContenidoAjax I
			//$t->set_var( "menu_liga", "/transaccion/autorizacion/" );
			if ($AjaxActivo===1 && strpos($ModuloAjax,'aut')===false)
			{
				$t->set_var( "menu_liga", "javascript:iniProcesarMenu('autorizacion', '')" );
			}
			else
			{
				$t->set_var( "menu_liga", "/transaccion/autorizacion/" );
			}
			//ACS  - MenuContenidoAjax F
				$t->set_var( "menu_nombre_liga", "Autorizaciones pendientes" );
				$t->parse( "row", "row_tpl", true );
			}

			// JAC NOV2011 MODULO DE CREDITOS INI
			//$t->set_var( "menu_liga", "/transaccion/simulacreo/" );
			//$t->set_var( "menu_nombre_liga", "Tabla Actual de Pagos de Credito" );
			//$t->parse( "row", "row_tpl", true );
			// JAC NOV2011 MODULO DE CREDITOS FIN

			//$t->set_var( "menu_liga", "/transaccion/simulacred/" );
			//$t->set_var( "menu_nombre_liga", "Calcule con Pagos Anticipados" );
			//$t->parse( "row", "row_tpl", true );

			$t->parse( "menu", "menu_tpl", true );
			$t->set_var( "row", "" );
			$t->set_var( "header_menu_bloque", "" );
				//ACS -Inicia  Nuevo Manu
				//Tarjetas de credito

				//$t->set_var( "header_menu_bloque", "Tarjetas de Cr&eacute;dito" );
				//$t->parse( "header", "header_tpl" );

				$t->set_var( "menu_liga", "/transaccion/PagoAutTarjeta/" ); //pat
				$t->set_var( "menu_nombre_liga", "Pago Automático Tarjeta de Crédito BB" );
				$t->parse( "row", "row_tpl", true );

				if ( $session->variable( "r_tdc" ) == "" or $session->variable( "r_tdc" ) == "0" )
				{
					 //$t->set_var( "menu_liga", "/transaccion/saldosTC/" );
					//$t->set_var( "menu_nombre_liga", "Afiliación" );//PSM SE ELIMINA LA AFILIACION DE TARJETAS
					//$t->parse( "row", "row_tpl", true );
				}
				else
				{
					if(substr($priv, 21, 1)=="1")
					{ // tct
						$t->set_var( "menu_liga", "/transaccion/saldosTC/" );
						$t->set_var( "menu_nombre_liga", "Consulta de Saldos" );
						$t->parse( "row", "row_tpl", true );
					}

					if(substr($priv, 21, 1)=="1")
					{ //mov tct
						$t->set_var( "menu_liga", "/transaccion/movtosTC/" );
						$t->set_var( "menu_nombre_liga", "Consulta de Movimientos" );
						$t->parse( "row", "row_tpl", true );
					}

					$t->set_var( "menu_liga", "/transaccion/edosctaActualTC/" );
					$t->set_var( "menu_nombre_liga", "Estado de Cuenta - Mes actual" );
					$t->parse( "row", "row_tpl", true );

					$t->set_var( "menu_liga", "/transaccion/edoscta1AtrasTC/" );
					$t->set_var( "menu_nombre_liga", "Estado de Cuenta - 1 Mes Atras" );
					$t->parse( "row", "row_tpl", true );

					$t->set_var( "menu_liga", "/transaccion/edoscta2AtrasTC/" );
					$t->set_var( "menu_nombre_liga", "Estado de Cuenta - 2 Meses Atras" );
					$t->parse( "row", "row_tpl", true );

					$t->set_var( "menu_liga", "/transaccion/salptosTC/" );
					$t->set_var( "menu_nombre_liga", "Saldo puntos" );
					$t->parse( "row", "row_tpl", true );

					$t->set_var( "menu_liga", "/transaccion/repextravioTC/" );
					$t->set_var( "menu_nombre_liga", "Reporte extravio" );
					$t->parse( "row", "row_tpl", true );

					$t->set_var( "menu_liga", "/transaccion/reproboTC/" );
					$t->set_var( "menu_nombre_liga", "Reporte robo" );
					$t->parse( "row", "row_tpl", true );
//-- NEX inicio EstCta

					$t->set_var( "menu_liga", "/transaccion/estadoCtaElectr/" );
					$t->set_var( "menu_nombre_liga", "Suscripci&oacute;n Estado de Cuenta Oficial" );
					$t->parse( "row", "row_tpl", true );
//-- NEX Fin EstCta

				}
				$t->parse( "menu", "menu_tpl", true );
				$t->set_var( "row", "" );
				$t->set_var( "header_menu_bloque", "" );
				//ACS - Fin Nuevo Menu
		}

		// JAC 12MAR2012 REORDENAMIENTO MENU MODULO DE CREDITOS INI
		if ( substr( $priv, 0, 1 ) == "1" )
		{
			$t->set_var( "header_menu_bloque", "Cr&eacuteditos" );
			$t->parse( "header", "header_tpl" );
			if ( substr( $priv, 0, 1) == "1" ) //
			{
				// JAC NOV2011 - SE AGRUPAN FUNCIONALIDADES DE CREDITOS INI
				// Resumen de linea
				$t->set_var( "menu_liga", "javascript:iniProcesarMenu('pre_page','redirect:res_linea')" );
				$t->set_var( "menu_nombre_liga", "Resumen de l&iacute;nea" );
				$t->parse( "row", "row_tpl", true );

				// Saldos de creditos
				$t->set_var( "menu_liga", "javascript:iniProcesarMenu('pre_page', 'redirect:saldoscre')" );
				$t->set_var( "menu_nombre_liga", "Saldos de cr&eacute;dito" );
				$t->parse( "row", "row_tpl", true );

				// Consulta depositos en efectivo
				$t->set_var( "menu_liga", "javascript:iniProcesarMenu('cons_depo','')" );
				$t->set_var( "menu_nombre_liga", "Consulta dep&oacute;sitos en efectivo" );
				$t->parse( "row", "row_tpl", true );

				// Estados de cuenta de creditos
				$t->set_var( "menu_liga", "javascript:iniProcesarMenu('edoctacre','')" );
				$t->set_var( "menu_nombre_liga", "Estado de cuenta de cr&eacute;ditos" );
				$t->parse( "row", "row_tpl", true );

				// Calculo de proximos pagos
				$t->set_var( "menu_liga", "javascript:iniProcesarMenu('pre_page','redirect:calc_next')" );
				$t->set_var( "menu_nombre_liga", "C&aacute;lculo de pr&oacute;ximos pagos" );
				$t->parse( "row", "row_tpl", true );

				// Calculo de pagos anticipados
				$t->set_var( "menu_liga", "/transaccion/simulacreo/" );
				$t->set_var( "menu_nombre_liga", "C&aacute;lculo de pagos anticipados" );
				$t->parse( "row", "row_tpl", true );

				// Preguntas Frecuentes
				$t->set_var( "menu_liga", "javascript:iniProcesarMenu('cred_faqs','')" );
				$t->set_var( "menu_nombre_liga", "Preguntas Frecuentes" );
				$t->parse( "row", "row_tpl", true );
				// JAC NOV2011 - SE AGRUPAN FUNCIONALIDADES DE CREDITOS FIN
			}
			$t->parse( "menu", "menu_tpl", true );
			$t->set_var( "row", "" );
			$t->set_var( "header_menu_bloque", "" );
		}
		// JAC 12MAR2012 REORDENAMIENTO MENU MODULO DE CREDITOS FIN

		//REF DHE-TESIN-31jul2014.[BEGIN]
		// TESORERIA EMPRESARIAL
		if(substr($priv, 109, 1) == "1")
		{
			//MENU TITLE
			$t->set_var( "header_menu_bloque", "Tesoreria" );
			$t->parse( "header", "header_tpl" );

			if(substr($priv, 110, 1) == "1"
			|| substr($priv, 111, 1) == "1"
			|| substr($priv, 112, 1) == "1"
			|| substr($priv, 113, 1) == "1"
			)
			{	

				//OPTIONS
				if(substr( $priv, 110, 1 ) == "1")
				{
					$t->set_var( "menu_liga", "javascript:iniProcesarMenuTESO('teso','admoncuentas',0)" );
					$t->set_var( "menu_nombre_liga", "Administracion de Cuentas" );
					$t->parse( "row", "row_tpl", true );
				}			
				if(substr( $priv, 111, 1 ) == "1")
				{				
					$t->set_var( "menu_liga", "javascript:iniProcesarMenuTESO('teso','dispersionesprog',0)" );
					$t->set_var( "menu_nombre_liga", "Dispersiones Programadas" );
					$t->parse( "row", "row_tpl", true );
				}
			
				if(substr( $priv, 112, 1 ) == "1")
				{				
					$t->set_var( "menu_liga", "javascript:iniProcesarMenuTESO('teso','mapacuentas',0)" );
					$t->set_var( "menu_nombre_liga", "Mapa de Cuentas" );
					$t->parse( "row", "row_tpl", true );
				}
				if(substr( $priv, 113, 1 ) == "1")
				{
					//Reportes en Línea
					$t->set_var( "menu_liga", "javascript:iniProcesarMenu('movimientos', '')" );
					$t->set_var( "menu_nombre_liga", "Reportes en Línea" );
					$t->parse( "row", "row_tpl", true );

					//Reportes por Archivo
					$t->set_var( "menu_liga", "javascript:iniProcesarMenuTESO('teso', 'reporteArchivo', 0)" );
					$t->set_var( "menu_nombre_liga", "Reportes por Archivo" );
					$t->parse( "row", "row_tpl", true );
				}
				$t->parse( "menu", "menu_tpl", true );
				$t->set_var( "row", "" );
				$t->set_var( "header_menu_bloque", "" );
							
			}
		}
		//REF DHE-TESIN-31jul2014.[END]
		if
			(
					substr( $priv,  1, 1 ) == "1"
				||	substr( $priv, 46, 1 ) == "1"
				||	substr( $priv, 14, 1 ) == "1"
			)
		{
			$t->set_var( "header_menu_bloque", "Transferencias" );
			$t->parse( "header", "header_tpl" );
			if ( substr( $priv, 1, 1) == "1" ) // trn
			{
				//ACS  - MenuContenidoAjax I
				if ($AjaxActivo===1 && strpos($ModuloAjax,'trn')===false)
				{
					$t->set_var( "menu_liga", "javascript:iniProcesarMenu('transferencia', '')" );
				}
				else
				{
					$t->set_var( "menu_liga", "/transaccion/transferencia/" );
				}
				//ACS  - MenuContenidoAjax F
				$t->set_var( "menu_nombre_liga", "Transferencias de fondos entre cuentas" );
				$t->parse( "row", "row_tpl", true );
			}
			if ( substr( $priv, 46, 1 ) == "1" ) //spei
			{
			//REF WMA-27ene2009, Inicio
				//$t->set_var( "menu_liga", "/transaccion/speuaTT/" );
				$t->set_var( "menu_liga", "javascript:iniProcesarMenu('speuaTT', '')" );
			//REF WMA-27ene2009, Fin
				$t->set_var( "menu_nombre_liga", "SPEI" );
				$t->parse( "row", "row_tpl", true );
			}
			if ( substr( $priv, 14, 1 ) == "1" ) //pin
			{
			//REF WMA-27ene2009, Inicio
				//$t->set_var( "menu_liga", "/transaccion/pagointerbancario/" );
				$t->set_var( "menu_liga", "javascript:iniProcesarMenu('pagointerbancario', '')" );
			//REF WMA-27ene2009, Fin
				$t->set_var( "menu_nombre_liga", "Transferencia electrónica de fondos (T.E.F.)" );
				$t->parse( "row", "row_tpl", true );
			}
			$t->parse( "menu", "menu_tpl", true );
			$t->set_var( "row", "" );
			$t->set_var( "header_menu_bloque", "" );
		}
//ATAR SE COMENTA PARA QUE SE VAYA DESHABILITADO 01OCT2010
 ////YEHO 189716 I 04sep12
   //if (( substr( $priv, 48, 1 ) == "1" ))    //YEHO 161406 27sep10 se crea su propio menu
 /*  if (( substr( $priv, 81, 1 ) == "1" ))    //YEHO 161406 27sep10 se crea su propio menu
    {
    	$t->set_var( "header_menu_bloque", "Pagos IMSS-INFONAVIT" );
			$t->parse( "header", "header_tpl" );

			$t->set_var( "menu_liga", "/transaccion/pagoimssSUA/" );
      $t->set_var( "menu_nombre_liga", "Pago  IMSS - SUA" );
    	$t->parse( "row", "row_tpl", true );

			$t->parse( "menu", "menu_tpl", true );
			$t->set_var( "row", "" );
			$t->set_var( "header_menu_bloque", "" );

    }*/
	if
		(
				substr( $priv, 81, 1 ) == "1"
			||	substr( $priv, 48, 1 ) == "1"
		)
		{
			$t->set_var( "header_menu_bloque", "Pagos IMSS-INFONAVIT" );
			$t->parse( "header", "header_tpl" );

			if (( substr( $priv, 81, 1 ) == "1" ))
			{
				$t->set_var( "menu_liga", "/transaccion/pagoimssSUA/" );
				$t->set_var( "menu_nombre_liga", "Pago  IMSS - SUA" );
				$t->parse( "row", "row_tpl", true );
				if( $ini->read_var( "site", "FechaSIPARE") != "" && (date("Ymd") >= $ini->read_var( "site", "FechaSIPARE")) )
				{
					$t->set_var( "header_menu_bloque", "SIPARE" );
					$t->parse( "header", "header_tpl" );
					$t->set_var( "menu_liga", "/transaccion/sipare/" );
					$t->set_var( "menu_nombre_liga", "SIPARE/IMSS-INFONAVIT" );
					$t->parse( "row", "row_tpl", true );
				}
			}

			$t->set_var( "menu_liga", "/transaccion/autorizacionsua/" );
			$t->set_var( "menu_nombre_liga", "Autorizaciones archivos SUA" );
			$t->parse( "row", "row_tpl", true );

			//ATAR SUA y SIPARE comparten privilegio 12feb2014
			$t->set_var( "menu_liga", "/transaccion/autorizacionsipare/" );
			$t->set_var( "menu_nombre_liga", "Autorizaciones Lineas SIPARE" );
			$t->parse( "row", "row_tpl", true );
			//ATAR SUA y SIPARE comparten privilegio 12feb2014
			$t->parse( "menu", "menu_tpl", true );
			$t->set_var( "row", "" );
			$t->set_var( "header_menu_bloque", "" );
		}


	///////YEHO 189716 04sep12 F
	/*********  ATAR  se agrega el SIPARE**********/
	//if (( substr( $priv, 82, 1 ) == "1" ))
	// {
	// //ACS11DIC2012 Reimpresi&oacute;n de comprobantes SIPARE Ini
		// if( $ini->read_var( "site", "FechaSIPARE") != "" && (date("Ymd") >= $ini->read_var( "site", "FechaSIPARE")) )
		// {
		// $t->set_var( "header_menu_bloque", "SIPARE" );
		// $t->parse( "header", "header_tpl" );
		// $t->set_var( "menu_liga", "/transaccion/sipare/" );
		// $t->set_var( "menu_nombre_liga", "SIPARE/IMSS-INFONAVIT" );
		// $t->parse( "row", "row_tpl", true );

		// $t->parse( "menu", "menu_tpl", true );
		// $t->set_var( "row", "" );
		// $t->set_var( "header_menu_bloque", "" );
		// }//ACS11DIC2012 Reimpresi&oacute;n de comprobantes SIPARE FIN
	// }
    /*********  ATAR  se agrega el SIPARE**********/


	// *******************************************************************************
	// NSS Dic2014 - Ordenes de Pago
	//if (1 == 1 )
	if ( substr( $priv, 116, 4 ) != "0000" )
	{ 
		$t->set_var( "header_menu_bloque", "&Oacute;rdenes Electr&oacute;nicas" );
		$t->parse( "header", "header_tpl" );

		if ( substr( $priv, 116, 1 ) == "1" ) 
		{
			$t->set_var( "menu_liga", "javascript:iniProcesarMenuMiddleware('ordenelec', 'generar')" );  
			$t->set_var( "menu_nombre_liga", "Generar &Oacute;rdenes" ); 
			$t->parse( "row", "row_tpl", true );
		}

		if (  substr( $priv, 117, 1 ) == "1" )
		{
			$t->set_var( "menu_liga", "javascript:iniProcesarMenuMiddleware('ordenelec', 'autorizar')" );  
			$t->set_var( "menu_nombre_liga", "Autorizaci&oacute;n de &Oacute;rdenes" ); 
			$t->parse( "row", "row_tpl", true );
		}

		if (substr( $priv, 118, 1 ) == "1" )
			{
			$t->set_var( "menu_liga", "javascript:iniProcesarMenuMiddleware('ordenelec', 'cancelar')" );  
			$t->set_var( "menu_nombre_liga", "Cancelaci&oacute;n de &Oacute;rdenes" ); 
			$t->parse( "row", "row_tpl", true );
		}

		if (substr( $priv, 119, 1 ) == "1" )
		{
			$t->set_var( "menu_liga", "javascript:iniProcesarMenuMiddleware('ordenelec', 'consultar')" );  
			$t->set_var( "menu_nombre_liga", "Consultas y Reportes" ); 
			$t->parse( "row", "row_tpl", true );
		}

		$t->parse( "menu", "menu_tpl", true );
		$t->set_var( "row", "" );
		$t->set_var( "header_menu_bloque", "" );

	}
	
	//OVVC Pagos al Extranjero 1TEX [I]
	if( substr($priv, 121, 1) == "1" || substr($priv, 122, 1) == "1" || substr($priv, 123, 1) == "1" || substr($priv, 124, 1) == "1" ){		
		$t->set_var( "header_menu_bloque", "Transferencias al Ext" );
		$t->parse( "header", "header_tpl" );
		$t->set_var( "menu_liga", "javascript:iniProcesarMenuMiddleware('transext', 'aut')" );
		$t->set_var( "menu_nombre_liga", "Transferencias al Extranjero" );
		$t->parse( "row", "row_tpl", true );
		$t->parse( "menu", "menu_tpl", true );
		$t->set_var( "row", "" );
		$t->set_var( "header_menu_bloque", "" );
	}	
	//OVVC Pagos al Extranjero 1TEX [F]
	// *********************************

		if (
					( substr( $priv, 24, 1 ) == "1" )
				||	( substr( $priv, 25, 1 ) == "1" )
				||	( substr( $priv, 27, 1 ) == "1" )
				||	( substr( $priv, 30, 1 ) == "1" )
				||	( substr( $priv, 31, 1 ) == "1" )
				||	( substr( $priv, 47, 1 ) == "1" )
				//||	( substr( $priv, 64, 1 ) == "1" ) //NXN Agosto2012 234135
				||	( substr( $priv, 66, 1 ) == "1" ) //NXN Agosto2012 234135
			) //arc
		{ // Intercambio de Archivos
			$t->set_var( "header_menu_bloque", "Intercambio de Archivos" );
			$t->parse( "header", "header_tpl" );

			// ************************************************************************************
			// DGM I 26Abr2007 ER-IMSS
			//if(substr($priv, 24, 1)=="1") { // Archivos de Pagos de Servicios
//SE oculta la liga de SUA 161406 23sep2010				$t->set_var( "menu_liga", "/transaccion/pagoimssSUA/" );
//SE oculta la liga de SUA 161406 23sep2010				$t->set_var( "menu_nombre_liga", "Pagos Imss SUA" );
//SE oculta la liga de SUA 161406 23sep2010				$t->parse( "row", "row_tpl", true );
			//}
			// DGM F 26Abr2007 ER-IMSS
			// ************************************************************************************
			if(substr($priv, 24, 1)=="1") { // Archivos de Pagos de Servicios
				$t->set_var( "menu_liga", "/transaccion/ArchServ/" );
				//$t->set_var( "menu_nombre_liga", "Enviar Archivos de: Servicios / Internacional" );//emedrano 15May2015
				$t->set_var( "menu_nombre_liga", "Enviar Archivos de Servicios" );
				$t->parse( "row", "row_tpl", true );
			}

			if(substr($priv,66, 1)=="1") { //YEHO 08Mar2010 consultar Archivos PEMEX-gas
				$t->set_var( "menu_liga", "/transaccion/ConArchPmx/" );
				$t->set_var( "menu_nombre_liga", "Consultar Archivos Subgasera" );
				$t->parse( "row", "row_tpl", true );
			}

			if(substr($priv,25, 1)=="1") {
				$t->set_var( "menu_liga", "/transaccion/ConArchServ/" );
				$t->set_var( "menu_nombre_liga", "Consultar Archivos de Servicios" );
				$t->parse( "row", "row_tpl", true );
			}

			if(substr($priv, 27, 1)=="1") { // Archivos de Servicios de Domiciliacion
				$t->set_var( "menu_liga", "/transaccion/DomiServ/" );
				$t->set_var( "menu_nombre_liga", "Enviar Archivos de Domiciliación" );
				$t->parse( "row", "row_tpl", true );

				$t->set_var( "menu_liga", "/transaccion/ConDomiServ/" );
				$t->set_var( "menu_nombre_liga", "Consultar Archivos de Domiciliación" );
				$t->parse( "row", "row_tpl", true );
			}

			if(substr($priv, 30, 1)=="1") { // Archivos de Pago a Proveedores
				//ACS 11May2009 - MenuContenidoAjax I
				if ($AjaxActivo===1 && strpos($ModuloAjax,'arc')===false)
				{
					$t->set_var( "menu_liga", "javascript:iniProcesarMenu('ArcPP', '')" );
				}
				else
				{
					$t->set_var( "menu_liga", "/transaccion/ArcPP/" );
				}
				//ACS 11May2009  - MenuContenidoAjax I
				$t->set_var( "menu_nombre_liga", "Enviar Archivos Pago Proveedores" );
				$t->parse( "row", "row_tpl", true );
			}

			if(substr($priv,31, 1)=="1") {
				//ACS 11May2009 - MenuContenidoAjax I
				if ($AjaxActivo===1 && strpos($ModuloAjax,'arcLib')===false)
				{
					$t->set_var( "menu_liga", "javascript:iniProcesarMenu('LibPP', '')" );
				}
				else
				{
					$t->set_var( "menu_liga", "/transaccion/LibPP/" );
				}
				//$t->set_var( "menu_liga", "/transaccion/LibPP/" );
				//ACS 11May2009 - MenuContenidoAjax I
				$t->set_var( "menu_nombre_liga", "Liberar Archivos Pago Proveedores" );
				$t->parse( "row", "row_tpl", true );
			}

			if(substr($priv,47, 1)=="1") {
				//ACS 11May2009 - MenuContenidoAjax I
				if ($AjaxActivo===1 && strpos($ModuloAjax,'arcCon')===false)
				{
					$t->set_var( "menu_liga", "javascript:iniProcesarMenu('ConArcPP', '')" );
				}
				else
				{
					$t->set_var( "menu_liga", "/transaccion/ConArcPP/" );
				}
				//$t->set_var( "menu_liga", "/transaccion/ConArcPP/" );
				$t->set_var( "menu_nombre_liga", "Consultar Archivos Pago Proveedores" );
				$t->parse( "row", "row_tpl", true );
			}

			$t->parse( "menu", "menu_tpl", true );
			$t->set_var( "row", "" );
			$t->set_var( "header_menu_bloque", "" );
		}
//NXN TarjetasPrepago 10Ago2010 Ini
		if
			(
					substr( $priv, 70, 1 ) == "1"
				||	substr( $priv, 71, 1 ) == "1"
				||	substr( $priv, 72, 1 ) == "1"
				||	substr( $priv, 73, 1 ) == "1"
			//SMP CORRECCION MENU I
				||  substr( $priv, 85, 1 ) == "1"
				||  substr( $priv, 86, 1 ) == "1"
				||  substr( $priv, 87, 1 ) == "1"
				||  substr( $priv, 88, 1 ) == "1"
			//SMP CORRECCION MENU F
			)
		{
			//$t->set_var( "header_menu_bloque", "Tarjeta Prepago" ); //SMP CORRECCION MENU
			//$t->set_var( "header_menu_bloque", "Tarjeta Débito Gdes. Corpo." );
			$t->set_var( "header_menu_bloque", "Tarjeta de Débito Niveles" );
			$t->parse( "header", "header_tpl" );

			if ( substr( $priv, 70, 1) == "1" ) //
			{

				$t->set_var( "menu_liga", "/transaccion/tarjprepab/" );

				$t->set_var( "menu_nombre_liga", "Alta sin datos, Baja o trascodificaci&oacute;n" );
				$t->parse( "row", "row_tpl", true );

			}
			if ( substr( $priv, 70, 1) == "1" ) //
			{

				$t->set_var( "menu_liga", "/transaccion/tarjprepab2/" );

				$t->set_var( "menu_nombre_liga", "Alta con datos o complemento de datos" );
				$t->parse( "row", "row_tpl", true );
			}
			if ( substr( $priv, 71, 1) == "1" ) // trn
			{

				$t->set_var( "menu_liga", "/transaccion/tarjprepdep/" );

				$t->set_var( "menu_nombre_liga", "Dispersi&oacute;n o devoluci&oacute;n de recursos" );
				$t->parse( "row", "row_tpl", true );
			}
			if ( substr( $priv, 72, 1) == "1" ) // trn
			{

				$t->set_var( "menu_liga", "javascript:iniProcesarMenu('tarjprepconalt', '')" );

				$t->set_var( "menu_nombre_liga", "Consulta de archivos" );
				$t->parse( "row", "row_tpl", true );

				// $t->set_var( "menu_liga", "javascript:iniProcesarMenu('tarjprepcondep', '')" );

				// $t->set_var( "menu_nombre_liga", "Consulta de archivos Deposito de tarjetas" );
				//$t->parse( "row", "row_tpl", true ); //SMP CORRECCION MENU
			}
			if ( substr( $priv, 73, 1) == "1" ) // trn
			{

				$t->set_var( "menu_liga", "javascript:iniProcesarMenu('tarjprepconsalmov', '')" );

				$t->set_var( "menu_nombre_liga", "Consulta de movimientos" );
				$t->parse( "row", "row_tpl", true );

				$t->set_var( "menu_liga", "javascript:iniProcesarMenu('tarjprepconsal', '')" );

				$t->set_var( "menu_nombre_liga", "Consulta de saldos" );
				$t->parse( "row", "row_tpl", true );
			}
			//SMP CORRECCION MENU - SE AGREGA LIBERACION I
			if ( substr( $priv, 85, 1) == "1" || substr( $priv, 88, 1) == "1" ) // trn
			{

				//$t->set_var( "menu_liga", "/transaccion/tarjpenlib/" );
				$t->set_var( "menu_liga", "javascript:iniProcesarMenuMiddleware('tarjpenlib','')" );
				$t->set_var( "menu_nombre_liga", "Archivos pendientes por liberar" );
				$t->parse( "row", "row_tpl", true );
			}
			if ( substr( $priv, 86, 1) == "1" ) // trn
			{
				$t->set_var( "menu_liga", "/transaccion/altatarjhabiente/" );
				$t->set_var( "menu_nombre_liga", "Env&iacute;o de datos del tarjetahabiente" );
				$t->parse( "row", "row_tpl", true );
			}
			if ( substr( $priv, 87, 1) == "1") // trn
			{
				$t->set_var( "menu_liga", "/transaccion/devrectarjeta/" );
				$t->set_var( "menu_nombre_liga", "Devoluci&oacute;n de Recursos" );
				$t->parse( "row", "row_tpl", true );
			}
			//SMP CORRECCION MENU - SE AGREGA LIBERACION F
			$t->parse( "menu", "menu_tpl", true );
			$t->set_var( "row", "" );
			$t->set_var( "header_menu_bloque", "" );
		}
//NXN TarjetasPrepago 10Ago2010 Fin
		if ( substr( $priv, 32, 1 ) == "1" )
		{
			$t->set_var( "header_menu_bloque", "PEMEX-Gas" );
			$t->parse( "header", "header_tpl" );

			$t->set_var( "menu_liga", "/transaccion/PmxDocPag/" );
			$t->set_var( "menu_nombre_liga", "Documentos Pagados" );
			$t->parse( "row", "row_tpl", true );

			$t->set_var( "menu_liga", "/transaccion/PmxDocPPag/" );
			$t->set_var( "menu_nombre_liga", "Documentos por Pagar" );
			$t->parse( "row", "row_tpl", true );

			$t->set_var( "menu_liga", "/transaccion/PmxIntMor/" );
			$t->set_var( "menu_nombre_liga", "Tasa de Interes Moratorio" );
			$t->parse( "row", "row_tpl", true );

			$t->parse( "menu", "menu_tpl", true );
			$t->set_var( "row", "" );
			$t->set_var( "header_menu_bloque", "" );
		}
		$nvoesquema=0;
		if (
					( substr( $priv, 33, 1 ) == "1" )
				||	( substr( $priv, 34, 1 ) == "1" )
				||	( substr( $priv, 35, 1 ) == "1" )
				||	( substr( $priv, 45, 1 ) == "1" )
			//	||	( substr( $priv, 76, 1 ) == "1" ) // IRG04052011 - Nómina IMSS
			//MAOS JUN2014 I
				||	( substr( $priv, 36, 1 ) == "1" )
				||	( substr( $priv, 37, 1 ) == "1" )
				||	( substr( $priv, 41, 1 ) == "1" )
				||	( substr( $priv, 62, 1 ) == "1" )
			//MAOS JUN2014 F
			)
		{ // Intercambio de Archivos de Nómina Electrónica
			if ( nuevoEsquemaHabilitado() )
			{
				eZlog::writeNotice("Entrando al intercambio....1");
				$nvoesquema=1;
				$t->set_var( "header_menu_bloque", "Nómina" );
				$t->parse( "header", "header_tpl" );

				if(substr($priv,35, 1)=="1")//Autoriza?
				{
					eZlog::writeNotice("Entrando al intercambio....2");
					$t->set_var( "menu_liga", "javascript:iniProcesarMenuMiddleware('nomina', 'aut')" );
					$t->set_var( "menu_nombre_liga", "Nómina Electrónica" );
					$t->parse( "row", "row_tpl", true );
					//MAOS JUN2014 I
					
					if(substr($priv,41, 1)=="1") {
						if(trim($ini->read_var( "site", "hbltTDNSP")) == "SI")
						{
							$t->set_var( "menu_liga", "javascript:iniProcesarMenuMiddleware('TDNSP','')" );
							$t->set_var( "menu_nombre_liga", "Tarjetas de Nómina" );
							$t->parse( "row", "row_tpl", true );
						} else {
							$t->set_var( "menu_liga", "/transaccion/TjdNom/" );
							$t->set_var( "menu_nombre_liga", "Tarjetas de Nómina" );
							$t->parse( "row", "row_tpl", true );
						}//MAOS STOCK DE NOMINA
					}
					//MAOS JUN2014 I
				}
				else
				{
					eZlog::writeNotice("Entrando al intercambio....3");
					$t->set_var( "menu_liga", "javascript:iniProcesarMenuMiddleware('nomina', '')" );
					$t->set_var( "menu_nombre_liga", "Nómina Electrónica" );
					$t->parse( "row", "row_tpl", true );
					//MAOS JUN2014 I
					/*if(substr($priv,41, 1)=="1") {
						$t->set_var( "menu_liga", "/transaccion/TjdNom/" );
						$t->set_var( "menu_nombre_liga", "Tarjetas de Nómina" );
						$t->parse( "row", "row_tpl", true );
					}*/
					//MAOS JUN2014 I
					//MAOS JUN2014 I
					if(substr($priv,41, 1)=="1") {
						if(trim($ini->read_var( "site", "hbltTDNSP")) == "SI")
						{
							$t->set_var( "menu_liga", "javascript:iniProcesarMenuMiddleware('TDNSP','')" );
							$t->set_var( "menu_nombre_liga", "Tarjetas de Nómina" );
							$t->parse( "row", "row_tpl", true );
						} else {
							$t->set_var( "menu_liga", "/transaccion/TjdNom/" );
							$t->set_var( "menu_nombre_liga", "Tarjetas de Nómina" );
							$t->parse( "row", "row_tpl", true );
						}//MAOS STOCK DE NOMINA
					}
					//MAOS JUN2014 I
				}
			}
			else
			{
				if (
						( substr( $priv, 33, 1 ) == "1" )
					||	( substr( $priv, 34, 1 ) == "1" )
					||	( substr( $priv, 35, 1 ) == "1" )
					||	( substr( $priv, 45, 1 ) == "1" )
				)
				{
					eZlog::writeNotice("Entrando al intercambio....4");
					$t->set_var( "header_menu_bloque", "Intercambio de Archivos Nómina" );
					$t->parse( "header", "header_tpl" );

					if(substr($priv, 45, 1)=="1") {
						$t->set_var( "menu_liga", "/transaccion/DepArcNom/" );
						$t->set_var( "menu_nombre_liga", "Enviar Archivos Depósitos" );
						$t->parse( "row", "row_tpl", true );
					}

					if(substr($priv,33, 1)=="1") {
						$t->set_var( "menu_liga", "/transaccion/EmpArcNom/" );
						$t->set_var( "menu_nombre_liga", "Enviar Archivos Altas Empleados " );
						$t->parse( "row", "row_tpl", true );
					}

					if(substr($priv,35, 1)=="1"
						|| (substr( $priv, 76, 1 ) == "1" )) { // IRG04052011 - Nómina IMSS
						$t->set_var( "menu_liga", "/transaccion/LibNom/" );
						$t->set_var( "menu_nombre_liga", "Autorizar Archivos Nómina" );
						$t->parse( "row", "row_tpl", true );
					}

					if(substr($priv,34, 1)=="1") {
						$t->set_var( "menu_liga", "/transaccion/ConNom/" );
						$t->set_var( "menu_nombre_liga", "Consultar Archivos" );
						$t->parse( "row", "row_tpl", true );
					}
				}
			
			}

			$t->parse( "menu", "menu_tpl", true );
			$t->set_var( "row", "" );
			$t->set_var( "header_menu_bloque", "" );

		}
		//IRG03052011 - INICIO - NOMINA IMSS
		if ( substr( $priv, 75, 1 ) == "1" )
		{ // Archivos de nómina del imss
			$t->set_var( "header_menu_bloque", "Intercambio de Archivos Nómina IMSS" );
			$t->parse( "header", "header_tpl" );
			if(substr($priv, 75, 1)=="1") {
				$t->set_var( "menu_liga", "/transaccion/DepArcNomImss/" );
				$t->set_var( "menu_nombre_liga", "Enviar Archivos Depósitos IMSS" );
				$t->parse( "row", "row_tpl", true );
			}

			$t->parse( "menu", "menu_tpl", true );
			$t->set_var( "row", "" );
			$t->set_var( "header_menu_bloque", "" );

		}
		//IRG03052011 - FIN - NOMINA IMSS

	//---------- Nomina Electronica por Bajionet --------------------- I

		if (
					( substr( $priv, 36, 1 ) == "1" )
				||	( substr( $priv, 37, 1 ) == "1" )
				//||	( substr( $priv, 38, 1 ) == "1" )
				||	( substr( $priv, 41, 1 ) == "1" )
			)
		{ // Nómina Electrónica por Bajionet

		if ( nuevoEsquemaHabilitado() )
			{
				eZlog::writeNotice("Entrando al intercambio....5");
			}
			else
			{
				eZlog::writeNotice("Entrando al intercambio....6");
				$t->set_var( "header_menu_bloque", "Administración de Nómina en Línea" );
				$t->parse( "header", "header_tpl" );

				if(substr($priv, 36, 1)=="1") {
					$t->set_var( "menu_liga", "/transaccion/AltEmpNom/" );
					$t->set_var( "menu_nombre_liga", "Alta de Empleados" );
				$t->parse( "row", "row_tpl", true );
				}

				if(substr($priv,37, 1)=="1") {
					$t->set_var( "menu_liga", "/transaccion/ConEmpNom/" );
					$t->set_var( "menu_nombre_liga", "Consulta de Empleados" );
					$t->parse( "row", "row_tpl", true );
				}

					if(substr($priv,38, 1)=="1" && !nuevoEsquemaHabilitado()) {
					$t->set_var( "menu_liga", "/transaccion/ProEmpNom/" );
					$t->set_var( "menu_nombre_liga", "Repetir Nómina Dispersada" );
					$t->parse( "row", "row_tpl", true );
				}

				/*if(substr($priv,41, 1)=="1") {
					$t->set_var( "menu_liga", "/transaccion/TjdNom/" );
					$t->set_var( "menu_nombre_liga", "Tarjetas de Nómina" );
					$t->parse( "row", "row_tpl", true );
				}
				*/
				//MAOS JUN2014 I
				if(substr($priv,41, 1)=="1") {
					if(trim($ini->read_var( "site", "hbltTDNSP")) == "SI")
					{
						$t->set_var( "menu_liga", "javascript:iniProcesarMenuMiddleware('TDNSP','')" );
						$t->set_var( "menu_nombre_liga", "Tarjetas de Nómina" );
						$t->parse( "row", "row_tpl", true );
					} else {
						$t->set_var( "menu_liga", "/transaccion/TjdNom/" );
						$t->set_var( "menu_nombre_liga", "Ttas de Nómina" );
						$t->parse( "row", "row_tpl", true );
					}//MAOS STOCK DE NOMINA
				}
				//MAOS JUN2014 I

				$t->parse( "menu", "menu_tpl", true );
				$t->set_var( "row", "" );
				$t->set_var( "header_menu_bloque", "" );
			}
		}

	//---------- Programacion de depositos de nomina -------------------------

		/*if (
					( substr( $priv, 39, 1 ) == "1" )
				||	( substr( $priv, 40, 1 ) == "1" )
				||	( substr( $priv, 42, 1 ) == "1" )
				||	( substr( $priv, 43, 1 ) == "1" )
				||	( substr( $priv, 44, 1 ) == "1" )
			)
		{ // Agenda de Pagos de Nómina Electrónica
			$t->set_var( "header_menu_bloque", "Programacion de Depositos de Nómina" );
			$t->parse( "header", "header_tpl" );


			if((substr($priv, 42, 1)=="1") || (substr($priv, 43, 1)=="1")){
			$t->set_var( "menu_liga", "/transaccion/CatEmpNom/" );
				$t->set_var( "menu_nombre_liga", "Catálogo de Cuentas de Empleados" );
				$t->parse( "row", "row_tpl", true );
			}

			if((substr($priv, 39, 1)=="1") || (substr($priv, 40, 1)=="1")){
				$t->set_var( "menu_liga", "/transaccion/AgpEmpNom/" );
				$t->set_var( "menu_nombre_liga", "Programación de Depósitos" );
				$t->parse( "row", "row_tpl", true );
			}

			if(substr($priv,44, 1)=="1") {
				$t->set_var( "menu_liga", "/transaccion/NapEmpNom/" );
				$t->set_var( "menu_nombre_liga", "Autorización de Pagos" );
				$t->parse( "row", "row_tpl", true );
			}

			$t->parse( "menu", "menu_tpl", true );
			$t->set_var( "row", "" );
			$t->set_var( "header_menu_bloque", "" );
		}*/
		//DBA Nomina Express
		if 	( substr( $priv, 62, 1 ) == "1" )
		{
			if ( nuevoEsquemaHabilitado() )//Tiene solo nomina express y no se cargó su privilegio para archivos o linea
			{
				eZlog::writeNotice("Entrando al intercambio....9...$nvoesquema");
			}
			else
			{
				eZlog::writeNotice("Entrando al intercambio....1");
				$t->set_var( "header_menu_bloque", "Nómina Express" );
				$t->parse( "header", "header_tpl" );

				//$t->set_var( "menu_liga", "/transaccion/nomaltexp/" );
				$t->set_var( "menu_liga", "javascript:iniProcesarMenu('nomaltexp','')" );
				$t->set_var( "menu_nombre_liga", "Altas Express" );
				$t->parse( "row", "row_tpl", true );
				
				$t->parse( "menu", "menu_tpl", true );
				$t->set_var( "row", "" );
				$t->set_var( "header_menu_bloque", "" );
			}

		}
		//DBA Nomina Express
	//---------- Nomina Electronica por Bajionet ---------------------	 F

	//NXN-FOCA 21May2013 track 251114
	//---------- FoCA  ---------------------------------------------------
		if(
			substr( $priv, 89, 1 ) == "1"
		||	substr( $priv, 90, 1 ) == "1"
		||	substr( $priv, 91, 1 ) == "1"
		||	substr( $priv, 92, 1 ) == "1"
			)
		{
			//HEADER SECCION
			$t->set_var( "header_menu_bloque", "Fondos de Ahorro" );
			$t->parse( "header", "header_tpl" );
			//HEADER OPCIONES
			if(substr( $priv, 89, 1 ) == "1")
			{
				$t->set_var( "menu_liga", "javascript:iniProcesarMenuJSF('foca','saldosproducto',0)" );
				$t->set_var( "menu_nombre_liga", "Saldo Fondo de Ahorro" );
				$t->parse( "row", "row_tpl", true );
			}
			if(substr( $priv, 90, 1 ) == "1")
			{
				$t->set_var( "menu_liga", "javascript:iniProcesarMenuJSF('foca','cargaarchivos',0)" );
				$t->set_var( "menu_nombre_liga", "Carga de Archivos" );
				$t->parse( "row", "row_tpl", true );
			}

			if(substr( $priv, 91, 1 ) == "1")
			{
				$t->set_var( "menu_liga", "javascript:iniProcesarMenuJSF('foca','solindi',0)" );
				$t->set_var( "menu_nombre_liga", "Solicitudes Individuales" );
				$t->parse( "row", "row_tpl", true );
			}
			if(substr( $priv, 92, 1 ) == "1")
			{
				$t->set_var( "menu_liga", "javascript:iniProcesarMenuJSF('foca','libarc',0)" );
				$t->set_var( "menu_nombre_liga", "Liberacion de archivos y solicitudes" );
				$t->parse( "row", "row_tpl", true );
			}
			if(substr( $priv, 89, 1 ) == "1")
			{
				$t->set_var( "menu_liga", "javascript:iniProcesarMenuJSF('foca','consarc',0)" );
				$t->set_var( "menu_nombre_liga", "Consulta de archivos y solicitudes" );
				$t->parse( "row", "row_tpl", true );
			}
			if(substr( $priv, 89, 1 ) == "1")
			{
				$t->set_var( "menu_liga", "javascript:iniProcesarMenuJSF('foca','resumenmov',0)" );
				$t->set_var( "menu_nombre_liga", "Resumen de movimientos" );
				$t->parse( "row", "row_tpl", true );
			}
			if(substr( $priv, 89, 1 ) == "1")
			{
				$t->set_var( "menu_liga", "javascript:iniProcesarMenuJSF('foca','saldosempl',0)" );
				$t->set_var( "menu_nombre_liga", "Saldos por empleado" );
				$t->parse( "row", "row_tpl", true );
			}
			if(substr( $priv, 89, 1 ) == "1")
			{
				$t->set_var( "menu_liga", "javascript:iniProcesarMenuJSF('foca','detallemovempl',0)" );
				$t->set_var( "menu_nombre_liga", "Detalle de movimientos por empleado" );
				$t->parse( "row", "row_tpl", true );
			}
			if(substr( $priv, 89, 1 ) == "1")
			{
				$t->set_var( "menu_liga", "javascript:iniProcesarMenuJSF('foca','estadomov',0)" );
				$t->set_var( "menu_nombre_liga", "Estado de movimientos mensual" );
				$t->parse( "row", "row_tpl", true );
			}
			if(substr( $priv, 89, 1 ) == "1")
			{
				$t->set_var( "menu_liga", "javascript:iniProcesarMenuJSF('foca','resmenempl',0)" );
				$t->set_var( "menu_nombre_liga", "Resumen mensual empleados" );
				$t->parse( "row", "row_tpl", true );
			}
			if(substr( $priv, 89, 1 ) == "1")
			{
				$t->set_var( "menu_liga", "javascript:iniProcesarMenuJSF('foca','consapor',0)" );
				$t->set_var( "menu_nombre_liga", "Constancias individuales de aportacion" );
				$t->parse( "row", "row_tpl", true );
			}
			if(substr( $priv, 89, 1 ) == "1")
			{
				$t->set_var( "menu_liga", "javascript:iniProcesarMenuJSF('foca','tasasrend',0)" );
				$t->set_var( "menu_nombre_liga", "Tasas de rendimiento" );
				$t->parse( "row", "row_tpl", true );
			}
			//02feb2014 NXN-FOCA-ContratosCerrados Inicio
			if(substr( $priv, 89, 1 ) == "1")
			{			
				$t->set_var( "menu_liga", "javascript:iniProcesarMenuJSF('foca','saldosvenc',0)" );
				$t->set_var( "menu_nombre_liga", "Reporte de Cierre de Fondo" );
				$t->parse( "row", "row_tpl", true );
			}
			//02feb2014 NXN-FOCA-ContratosCerrados Fin
			//FIN SECCION
			$t->parse( "menu", "menu_tpl", true );
			$t->set_var( "row", "" );
			$t->set_var( "header_menu_bloque", "" );
		}
	//NXN-FOCA 21May2013 track 251114

		if(substr($priv, 4, 1)=="1") { // imp
			$t->set_var( "header_menu_bloque", "Impuestos" );
			$t->parse( "header", "header_tpl" );

	// *****************************************************************************
	//	DGM 11Nov2005 se deshabilita menú de pago de impuestos esquema anterior
	//        $t->set_var( "menu_liga", "/transaccion/pagoimpuestos/" );
	//        $t->set_var( "menu_nombre_liga", "Esquema Anterior" );
	//        $t->parse( "row", "row_tpl", true );
	// *****************************************************************************

	// *****************************************************************************
	//	DGM 30Nov2005 se habilita pago Referenciado
			//$t->set_var( "menu_liga", "/transaccion/pagoimpuestos/" );
			//ACS  - MenuContenidoAjax I
			//$t->set_var( "menu_liga", "/transaccion/pagoreferenciado/" );
			if ($AjaxActivo===1 && strpos($ModuloAjax,'ire')===false)
			{
				$t->set_var( "menu_liga", "javascript:iniProcesarMenu('pagoreferenciado', '')" );
			}
			else
			{
				$t->set_var( "menu_liga", "/transaccion/pagoreferenciado/" );
			}
			//ACS  - MenuContenidoAjax F
	        //$t->set_var( "menu_nombre_liga", "SAT Pago Referenciado" ); ATAR cambio de nombre
	        $t->set_var( "menu_nombre_liga", "Pago Referenciado SAT" ); //ATAR cambio de nombre 21sep2015
	        $t->parse( "row", "row_tpl", true );
	// *****************************************************************************
			//ACS  - MenuContenidoAjax I
			//$t->set_var( "menu_liga", "/transaccion/pagosprovisionales/" );
			/* ATAR eliminar imptos que no se usan
			if ($AjaxActivo===1 && strpos($ModuloAjax,'ipp')===false)
			{
				$t->set_var( "menu_liga", "javascript:iniProcesarMenu('pagosprovisionales', '')" );
			}
			else
			{
				$t->set_var( "menu_liga", "/transaccion/pagosprovisionales/" );
			}
			//ACS  - MenuContenidoAjax F
			$t->set_var( "menu_nombre_liga", "Pagos Provisionales" );
			$t->parse( "row", "row_tpl", true );
			*/

			//ACS  - MenuContenidoAjax I
			//$t->set_var( "menu_liga", "/transaccion/pagosanuales/" );
			
			//ATAR se elimina por completo la recepcion de pagos Anuales 03Sep2015
			// if ($AjaxActivo===1 && strpos($ModuloAjax,'ipj')===false)
			// {
				// $t->set_var( "menu_liga", "javascript:iniProcesarMenu('pagosanuales', '')" );
			// }
			// else
			// {
				// $t->set_var( "menu_liga", "/transaccion/pagosanuales/" );
			// }
			
			
			//ACS  - MenuContenidoAjax F
			// $t->set_var( "menu_nombre_liga", "Pagos Anuales" );
			// $t->parse( "row", "row_tpl", true );
		//ATAR se elimina por completo la recepcion de pagos Anuales 03Sep2015

			//ACS  - MenuContenidoAjax I
			//$t->set_var( "menu_liga", "/transaccion/pagoscoordinados/" );
			/* ATAR eliminar imptos que no se usan
			if ($AjaxActivo===1 && strpos($ModuloAjax,'pic')===false)
			{
				$t->set_var( "menu_liga", "javascript:iniProcesarMenu('pagoscoordinados', '')" );
			}
			else
			{
				$t->set_var( "menu_liga", "/transaccion/pagoscoordinados/" );
			}
			//ACS  - MenuContenidoAjax F
			$t->set_var( "menu_nombre_liga", "Pagos Coordinados E. Federativas" );
			$t->parse( "row", "row_tpl", true );
			*/
			//ACS  - MenuContenidoAjax I
			//$t->set_var( "menu_liga", "/transaccion/PCFimpuestos/" );
			/* ATAR eliminar imptos que no se usan
			if ($AjaxActivo===1 && strpos($ModuloAjax,'pcf')===false)
			{
				$t->set_var( "menu_liga", "javascript:iniProcesarMenu('PCFimpuestos', '')" );
			}
			else
			{
				$t->set_var( "menu_liga", "/transaccion/PCFimpuestos/" );
			}*/
			//ACS  - MenuContenidoAjax F
			/* ATAR eliminar imptos que no se usan
			$t->set_var( "menu_nombre_liga", "Pagos Creditos Fiscales" );
			$t->parse( "row", "row_tpl", true );
			*/
			//ACS  - MenuContenidoAjax I
			//$t->set_var( "menu_liga", "/transaccion/PDAimpuestos/" );
			if ($AjaxActivo===1 && strpos($ModuloAjax,'pda')===false)
			{
				$t->set_var( "menu_liga", "javascript:iniProcesarMenu('PDAimpuestos', '')" );
			}
			else
			{
				$t->set_var( "menu_liga", "/transaccion/PDAimpuestos/" );
			}
			//ACS  - MenuContenidoAjax I
			$t->set_var( "menu_nombre_liga", "Derechos, Productos y Aprovechamientos" );
			$t->parse( "row", "row_tpl", true );

			if(substr($priv, 13, 1)=="1") { // aut
			//ACS  - MenuContenidoAjax I
			//$t->set_var( "menu_liga", "/transaccion/autorizacion/" );
			if ($AjaxActivo===1 && strpos($ModuloAjax,'aut')===false)
			{
				$t->set_var( "menu_liga", "javascript:iniProcesarMenu('autorizacion', '')" );
			}
			else
			{
				$t->set_var( "menu_liga", "/transaccion/autorizacion/" );
			}
			//ACS  - MenuContenidoAjax F
				$t->set_var( "menu_nombre_liga", "Autorizaciones pendientes" );
				$t->parse( "row", "row_tpl", true );
			}
			$t->parse( "menu", "menu_tpl", true );
			$t->set_var( "row", "" );
			$t->set_var( "header_menu_bloque", "" );

		}

		if (
					( substr( $priv, 16, 1 ) == "1" )
				||	( substr( $priv, 17, 1 ) == "1" )
				||	( substr( $priv, 18, 1 ) == "1" )
				||	( substr( $priv, 19, 1 ) == "1" )
				||	( substr( $priv, 20, 1 ) == "1" )
			)
		{ // Agenda de Pagos
			$t->set_var( "header_menu_bloque", "Agenda de Pagos" );
			$t->parse( "header", "header_tpl" );

			if((substr($priv, 16, 1)=="1") || (substr($priv,17, 1)=="1")){ // cta
				$t->set_var( "menu_liga", "/transaccion/catalogocuentas/" );
				$t->set_var( "menu_nombre_liga", "Catálogo de Cuentas" );
				$t->parse( "row", "row_tpl", true );
			}

			if((substr($priv,18, 1)=="1")|| (substr($priv, 19, 1)=="1")) { // app
				$t->set_var( "menu_liga", "/transaccion/pagosprogramados/" );
				$t->set_var( "menu_nombre_liga", "Agenda de Pagos" );
				$t->parse( "row", "row_tpl", true );
			}

			if(substr($priv,20 , 1)=="1") { // autpp
				$t->set_var( "menu_liga", "/transaccion/autorizacionPP/" );
				$t->set_var( "menu_nombre_liga", "Autorización" );
				$t->parse( "row", "row_tpl", true );
			}


			$t->parse( "menu", "menu_tpl", true );
			$t->set_var( "row", "" );
			$t->set_var( "header_menu_bloque", "" );

		}

		//Modificado por Nexions - HM
		if
			(
					substr( $priv, 51, 1 ) == "1"
				||	substr( $priv, 52, 1 ) == "1"
				||	substr( $priv, 53, 1 ) == "1"
				||	substr( $priv, 54, 1 ) == "1"
			)
		{
			$t->set_var( "header_menu_bloque", "Pago a Proveedores en L&iacute;nea" );
			$t->parse( "header", "header_tpl" );

			if(substr($priv,51 , 1)=="1") {
				$t->set_var( "menu_liga", "/transaccion/proveedores/" );
				$t->set_var( "menu_nombre_liga", "Cat&aacute;logo de Proveedores y Cuentas" );
				$t->parse( "row", "row_tpl", true );
			}

			if(substr($priv,52, 1)=="1") {
				$t->set_var( "menu_liga", "/transaccion/capturapagos/" );
				$t->set_var( "menu_nombre_liga", "Captura de Pagos" );
				$t->parse( "row", "row_tpl", true );
			}
	        if(substr($priv,53, 1)=="1") {
				$t->set_var( "menu_liga", "/transaccion/autorizacionpagos/" );
				$t->set_var( "menu_nombre_liga", "Autorización de Pagos" );
				$t->parse( "row", "row_tpl", true );
            }

	        if(substr($priv,53, 1)=="1") {
					$t->set_var( "menu_liga", "/transaccion/autorizacionpagosprog/" );
					$t->set_var( "menu_nombre_liga", "Autorizaci&oacute;n de Pagos Programados" );
					$t->parse( "row", "row_tpl", true );
			}

	        if(substr($priv,53, 1)=="1") {
					$t->set_var( "menu_liga", "/transaccion/pagosporaplicar/" );
					$t->set_var( "menu_nombre_liga", "Cancelaci&oacute;n de Pagos" );
					$t->parse( "row", "row_tpl", true );
			}

	        if(substr($priv,54, 1)=="1") {
					$t->set_var( "menu_liga", "/transaccion/consultasreportespagos/" );
					$t->set_var( "menu_nombre_liga", "Consultas y Reportes" );
					$t->parse( "row", "row_tpl", true );
			}

			$t->parse( "menu", "menu_tpl", true );
			$t->set_var( "row", "" );
			$t->set_var( "header_menu_bloque", "" );


		}
		//ACS 22Jul2009 Privilegio para Captura pagos por archivo cambiar posicion de pin a leer segun nuevo privilegio
		if
			(
					substr( $priv, 59, 1 ) == "1"
				||	substr( $priv, 60, 1 ) == "1"
				||	substr( $priv, 61, 1 ) == "1"
			)
		{
			$t->set_var( "header_menu_bloque", "En L&iacute;nea por Archivo" );
			$t->parse( "header", "header_tpl" );

			if(substr($priv,59, 1)=="1") {
					$t->set_var( "menu_liga", "/transaccion/capturapagosarchivo/" );
					$t->set_var( "menu_nombre_liga", "Captura de Pagos por Archivo" );
				$t->parse( "row", "row_tpl", true );
			}

			if(substr($priv,60, 1)=="1") {
				$t->set_var( "menu_liga", "/transaccion/autorizacionpagosarchivo/" );
				$t->set_var( "menu_nombre_liga", "Autorización de Pagos por Archivo" );
				$t->parse( "row", "row_tpl", true );
			}

			if(substr($priv,61, 1)=="1") {
				$t->set_var( "menu_liga", "/transaccion/cancelacionpagosarchivo/" );
				$t->set_var( "menu_nombre_liga", "Cancelaci&oacute;n de Pagos por Archivo" );
				$t->parse( "row", "row_tpl", true );
			}


			$t->parse( "menu", "menu_tpl", true );
			$t->set_var( "row", "" );
			$t->set_var( "header_menu_bloque", "" );


		}
		//ACS 22Jul2009 Privilegio para Captura pagos por archivo cambiar posicion de pin a leer segun nuevo privilegio

		// *******************************************************************************
		// NSS Tarjeta Debito Empresarial
		if ( substr( $priv, 104, 4 ) != "0000" &&  trim(substr( $priv, 104, 4 )) != "")
		{ 
			$t->set_var( "header_menu_bloque", "Tarjeta Débito Negocios" );
			$t->parse( "header", "header_tpl" );

			if ( substr( $priv, 104, 1 ) == "1" ) 
			{
				$t->set_var( "menu_liga", "javascript:iniProcesarMenuMiddleware('tdebitoempresarial', 'solicitar')" );  //iniProcesarMenuMiddleware('tdebitoempresarial', 'solicitar')
				$t->set_var( "menu_nombre_liga", "Solicitar" ); 
				$t->parse( "row", "row_tpl", true );
			}

			if (  substr( $priv, 105, 1 ) == "1" )
			{
				$t->set_var( "menu_liga", "javascript:iniProcesarMenuMiddleware('tdebitoempresarial', 'asignar')" );  //iniProcesarMenuMiddleware('tdebitoempresarial', 'solicitar')
				$t->set_var( "menu_nombre_liga", "Asignar" ); 
				$t->parse( "row", "row_tpl", true );
			}

			if (substr( $priv, 106, 1 ) == "1" )
				{
				$t->set_var( "menu_liga", "javascript:iniProcesarMenuMiddleware('tdebitoempresarial', 'liberar')" );  //iniProcesarMenuMiddleware('tdebitoempresarial', 'solicitar')
				$t->set_var( "menu_nombre_liga", "Liberar" ); 
				$t->parse( "row", "row_tpl", true );
			}

			if (substr( $priv, 107, 1 ) == "1" )
			{
				$t->set_var( "menu_liga", "javascript:iniProcesarMenuMiddleware('tdebitoempresarial', 'consultatarjeta')" );  //iniProcesarMenuMiddleware('tdebitoempresarial', 'solicitar')
				$t->set_var( "menu_nombre_liga", "Consulta Tarjetas" ); 
				$t->parse( "row", "row_tpl", true );

				$t->set_var( "menu_liga", "javascript:iniProcesarMenuMiddleware('tdebitoempresarial', 'consultamovs')" );  //iniProcesarMenuMiddleware('tdebitoempresarial', 'solicitar')
				$t->set_var( "menu_nombre_liga", "Consulta Movimientos" ); 
				$t->parse( "row", "row_tpl", true );

				// $t->set_var( "menu_liga", "javascript:iniProcesarMenu('tarjprepconalt', '')" );
				// NSS 25Abr2015 nuevo menu consulta de archivos
				// $t->set_var( "menu_liga", "javascript:iniProcesarMenu('tdebitoempresarial', '')" );
				$t->set_var( "menu_liga", "javascript:iniProcesarMenuMiddleware('tdebitoempresarial', 'consultaarch')" );  //iniProcesarMenuMiddleware('tdebitoempresarial', 'solicitar')
				$t->set_var( "menu_nombre_liga", "Consulta de archivos" );
				$t->parse( "row", "row_tpl", true );
			}

			$t->parse( "menu", "menu_tpl", true );
			$t->set_var( "row", "" );
			$t->set_var( "header_menu_bloque", "" );

		}
		// *********************************

		//Nexions
		if ((substr($priv, 28, 1)=="1") || (substr($priv, 29, 1)=="1")) { // tesoreria empresarial
			$t->set_var( "header_menu_bloque", "Tesoreria Empresarial" );
			$t->parse( "header", "header_tpl" );

			if(substr($priv, 28, 1)=="1") { // con
				$t->set_var( "menu_liga", "/transaccion/concentracion/" );
				$t->set_var( "menu_nombre_liga", "Concentración de Fondos" );
				$t->parse( "row", "row_tpl", true );
			}

			if(substr($priv, 29, 1)=="1") { // dis
				$t->set_var( "menu_liga", "/transaccion/dispersion/" );
				$t->set_var( "menu_nombre_liga", "Dispersión de Fondos" );
				$t->parse( "row", "row_tpl", true );
			}

			$t->parse( "menu", "menu_tpl", true );
			$t->set_var( "row", "" );
			$t->set_var( "header_menu_bloque", "" );

		}
		//REGA se comenta bloque para no mostrar menú
		/*if ($var_gobMenu != "enabled")
		{
			$t->set_var( "header_menu_bloque", "Servicios AFORE" );
			$t->parse( "header", "header_tpl" );
			//EVG-EDS 20092007 ENTIDAD VERIFICADORA REGISTRO
			// regi
			$t->set_var( "menu_liga", "/transaccion/registro/" );
			$t->set_var( "menu_nombre_liga", "Registro Afore" );
			$t->parse( "row", "row_tpl", true );
			//EVG-EDS 20092007 ENTIDAD VERIFICADORA REGISTRO
			// acli
			$t->set_var( "menu_liga", "/transaccion/activacion/" );
			$t->set_var( "menu_nombre_liga", "Activación de CLIP" );
			$t->parse( "row", "row_tpl", true );
			// tras
			$t->set_var( "menu_liga", "/transaccion/traspaso/" );
			$t->set_var( "menu_nombre_liga", "Traspaso AFORE" );
			$t->parse( "row", "row_tpl", true );

			// ******************************************************************
			// DGM 25Mar2007 Aportaciones Voluntarias por BajionNet
			//if(substr($priv, 50, 1) == "1")
			//{
			$t->set_var( "menu_liga", "/transaccion/aportacionAforeAB/" );
			$t->set_var( "menu_nombre_liga", "Aportaciones Voluntarias" );
			$t->parse( "row", "row_tpl", true );

			$t->parse( "menu", "menu_tpl", true );
			$t->set_var( "row", "" );
			$t->set_var( "header_menu_bloque", "" );
		}*/
		//REGA se comenta bloque para no mostrar menú

		//Promociones > Novedades OVVC
		if( $ini->read_var( "site", "bannerNov") == "enabled" )
		{
			$t->set_var( "header_menu_bloque", "Conoce m&aacute;s" );
			$t->parse( "header", "header_tpl" );
			$t->set_var( "menu_liga", "javascript:iniProcesarMenu('novedades', '')" );
			//$t->set_var( "menu_liga", "/transaccion/novedades/" );
			$t->set_var( "menu_nombre_liga", "Novedades" );
			$t->parse( "row", "row_tpl", true );

			$t->parse( "menu", "menu_tpl", true );
			$t->set_var( "row", "" );
			$t->set_var( "header_menu_bloque", "" );
		}
		//Promociones > Novedades OVVC
		//}
	}
	//echo ("Valor del Segmento....[$Segmento]");
	//DBA Login Seguro
	if ($Segmento != "gobierno")
	{
	$t->set_var( "header_menu_bloque", "Información financiera" );
	$t->parse( "header", "header_tpl" );

	$t->set_var( "menu_liga", "/transaccion/divisas/" );
	$t->set_var( "menu_nombre_liga", "Tipo de Cambio" );
	$t->parse( "row", "row_tpl", true );

	$t->set_var( "menu_liga", "/transaccion/tasas/" );
	$t->set_var( "menu_nombre_liga", "Tasas de Interes" );
	$t->parse( "row", "row_tpl", true );

	$t->parse( "menu", "menu_tpl", true );
	$t->set_var( "row", "" );
	$t->set_var( "header_menu_bloque", "" );
	}


// NEX Inicio DatCli
	if ( substr($dcl, 0, 2) != "na")
	{
		$t->set_var( "header_menu_bloque", "Informaci&oacute;n del Usuario" );
		$t->parse( "header", "header_tpl" );

		if ( strpos( $priv, "1" ) !== false )
		{
			if ($AjaxActivo===1 && strpos($ModuloAjax,'pwd')===false)
			{
				$t->set_var( "menu_liga", "javascript:iniProcesarMenu('datoscliente', '')" );
			}
			else
			{
				$t->set_var( "menu_liga", "/transaccion/datoscliente/" );
			}
			$t->set_var( "menu_nombre_liga", "Informaci&oacute;n Actual" );
			$t->parse( "row", "row_tpl", true );

	$dcl = $session->variable( "r_dcl" );

			if ( strlen($dcl) > 2 )
			{
				if ( substr($dcl, 2, 2) == "si" && strpos($dcl,"cd") === false)
				{
					if ($AjaxActivo===1 && strpos($ModuloAjax,'pwd')===false)
					{
						$t->set_var( "menu_liga", "javascript:iniProcesarMenu('confdatcli', '')" );
					}
					else
					{
						$t->set_var( "menu_liga", "/transaccion/confdatcli/" );
					}
					$t->set_var( "menu_nombre_liga", "Claves de Validaci&oacute;n" );
					$t->parse( "row", "row_tpl", true );
				}
			}

		}
	$t->parse( "menu", "menu_tpl", true );
	$t->set_var( "row", "" );
	$t->set_var( "header_menu_bloque", "" );
	}
// NEX Fin DatCli

	if ($var_gobMenu === "enabled")
	{
		$t->pparse( "output", "userboxNew_tpl" );
	}
	else
	{
		$t->pparse( "output", "userbox_tpl" );
	}
	//$t->pparse( "output", "userbox_tpl" );
// Cambio de menu - FAF

// Cambio de menu - FAF
	}
//}

?>
