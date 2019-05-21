<link rel='stylesheet' type='text/css' media='screen' href='/middleware/css/slide/style.css' />
<!-- <script language="javascript" src="/middleware/js/common/slide/modernizr.custom.04022.js" /> -->

<!--[if lte IE 8]>
<style type="text/css">	
	.imgBannerOVVC{ width: 100%; }
</style>		
<![endif]-->

<?php
/***************************************************************
	CREATED ON: <26-Jun-2013 14:32:53 smb>
	CREATED BY: Omar Vicente Villalobos Castro
	   WEBPAGE: Desarrollo de NOVEDADES
****************************************************************/

/***************************************************************
	INCLUDES
****************************************************************/
include_once( "classes/INIFile.php" );
include_once( "classes/eztemplate.php" );
include_once( "classes/ezhttptool.php" );
include_once( "ezsession/classes/ezsession.php" );
include_once( "classes/ezlog.php" );
	
$session =& eZSession::globalSession();
if( !$session->fetch() ) $session->store();
$ini =& $GLOBALS["GlobalSiteIni"];

/***************************************************************
	SITE.INI
****************************************************************/
$Language = $ini->read_var( "eZTransaccionMain", "Language" );
$ServerNumber = $ini->read_var( "site", "ServerNumber" );
$DomainPostfix = $ini->read_var( "site", "DomainPostfix" );
$segmento = $ini->read_var("site", "Segmento");
$perfil = $session->variable("r_perfil");
$segmento 	 = $ini->read_var("site", "Segmento");

/***************************************************************
	VALIDACIÓN DE SEGMENTO Y HTTPS
****************************************************************/

if ( !isset( $_SERVER['HTTPS'] ) || strtolower( $_SERVER[ 'HTTPS' ] ) != 'on' ){ $la_s = ""; } else { $la_s = "s"; }
if ($segmento != "gobierno")
{	
	$secure_site = $ini->read_var( "site", "SecureServer" ) . $ini->read_var( "site", "ServerNumber" ) . $ini->read_var( "site", "DomainPostfix" );
	$www_site 	 = $ini->read_var("site","WWWServer");
}
else
{
	$secure_site = $ini->read_var( "site", "SecureServer" ) . $ini->read_var( "site", "ServerGobierno" ) . $ini->read_var( "site", "DomainPostfix" );
}
//<h1>{intl-transaccion_operacion}</h1>
?>
<script language="javascript">
	$(document).ready(function(){
		var i=0;
		var timeSlide = 200;
		for( i=1 ; i<5 ; i++ ){ $('#imgBann'+i).hide();$('#arrow'+i+'_a').hide();$('#arrow'+i+'_b').hide();}
		/*********************************************************************************
		 OPCIONES PARA MOSTRAR DERECHA
		**********************************************************************************/
		$('#arrow0_b').click( function () { $('#imgBann0').fadeOut(0); $('#imgBann1').fadeIn(timeSlide); $('#arrow0_a').hide(); $('#arrow0_b').hide(); $('#arrow1_a').show(); $('#arrow1_b').show(); })
		$('#arrow1_b').click( function () { $('#imgBann1').fadeOut(0); $('#imgBann2').fadeIn(timeSlide); $('#arrow1_a').hide(); $('#arrow1_b').hide(); $('#arrow2_a').show(); $('#arrow2_b').show(); })
		$('#arrow2_b').click( function () { $('#imgBann2').fadeOut(0); $('#imgBann3').fadeIn(timeSlide); $('#arrow2_a').hide(); $('#arrow2_b').hide(); $('#arrow3_a').show(); $('#arrow3_b').show(); })
		$('#arrow3_b').click( function () { $('#imgBann3').fadeOut(0); $('#imgBann4').fadeIn(timeSlide); $('#arrow3_a').hide(); $('#arrow3_b').hide(); $('#arrow4_a').show(); $('#arrow4_b').show(); })
		$('#arrow4_b').click( function () { $('#imgBann4').fadeOut(0); $('#imgBann0').fadeIn(timeSlide); $('#arrow4_a').hide(); $('#arrow4_b').hide(); $('#arrow0_a').show(); $('#arrow0_b').show(); })
		
		/*********************************************************************************
		 OPCIONES PARA MOSTRAR IZQUIERDA
		**********************************************************************************/
		$('#arrow0_a').click( function () { $('#imgBann0').fadeOut(0); $('#imgBann4').fadeIn(timeSlide); $('#arrow0_a').hide(); $('#arrow0_b').hide(); $('#arrow4_a').show(); $('#arrow4_b').show(); })
		$('#arrow4_a').click( function () { $('#imgBann4').fadeOut(0); $('#imgBann3').fadeIn(timeSlide); $('#arrow4_a').hide(); $('#arrow4_b').hide(); $('#arrow3_a').show(); $('#arrow3_b').show(); })
		$('#arrow3_a').click( function () { $('#imgBann3').fadeOut(0); $('#imgBann2').fadeIn(timeSlide); $('#arrow3_a').hide(); $('#arrow3_b').hide(); $('#arrow2_a').show(); $('#arrow2_b').show(); })
		$('#arrow2_a').click( function () { $('#imgBann2').fadeOut(0); $('#imgBann1').fadeIn(timeSlide); $('#arrow2_a').hide(); $('#arrow2_b').hide(); $('#arrow1_a').show(); $('#arrow1_b').show(); })
		$('#arrow1_a').click( function () { $('#imgBann1').fadeOut(0); $('#imgBann0').fadeIn(timeSlide); $('#arrow1_a').hide(); $('#arrow1_b').hide(); $('#arrow0_a').show(); $('#arrow0_b').show(); })
	});
</script>
<h1>Novedades</h1>
<hr noshade="noshade" size="4" />
<div class="containerBanner">					

		<label id="arrow0_a" class="ovvc-arrow left"></label><label id="arrow0_b" class="ovvc-arrow right"></label>
		<label id="arrow1_a" class="ovvc-arrow left"></label><label id="arrow1_b" class="ovvc-arrow right"></label>
		<label id="arrow2_a" class="ovvc-arrow left"></label><label id="arrow2_b" class="ovvc-arrow right"></label>
		<label id="arrow3_a" class="ovvc-arrow left"></label><label id="arrow3_b" class="ovvc-arrow right"></label>
		<label id="arrow4_a" class="ovvc-arrow left"></label><label id="arrow4_b" class="ovvc-arrow right"></label>
		
<?php
$t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
					 "eztransaccion/user/intl/", $Language, "novedades.php" );
$t->setAllStrings();
$t->set_file( array("novedades_tpl" => "novedades.tpl" ));
$session =& eZSession::globalSession();

if ( !$session->fetch() ){$session->store();}
$tr = new eZTransaccion( );

if ( $segmento == "gobierno" ){
		$txt_file = "gobierno.ini";
		$uri_array = abrirFile($txt_file);
		$tamArray = $uri_array[0];
		$index = 1;
		if ( $tamArray > 5 ){ $tamArray = 5; }
		if ( $tamArray < 1 ){ $tamArray = 1; }
		for ( $i=0 ; $i<$tamArray ; $i++ ){
			//echo "<img id=\"imgBann".$i."\" class=\"imgBannerOVVC\" src=\"http".$la_s."://".$secure_site."/images/bajiosecure/banner_secure/slide/large/gobierno_".$index.".jpg\" alt=\"bb_img1\" data-thumb=\"http".$la_s."://".$secure_site."/images/bajiosecure/banner_secure/slide/thumbs/1.jpg\" title=\"". $uri_array[$index] ."\"/>";
			echo "<img id=\"imgBann".$i."\" class=\"imgBannerOVVC\" src=\"http".$la_s."://".$secure_site."/images/bajiosecure/banner_secure/slide/large/gobierno_".$index.".jpg\" data-thumb=\"http".$la_s."://".$secure_site."/images/bajiosecure/banner_secure/slide/thumbs/1.jpg\" />";
			$index++;
		}	
}
else{
	if ( $perfil == "persfis" || $perfil == "perfacem" ){	
		$txt_file = "persona.ini";
		$uri_array = abrirFile($txt_file);
		$tamArray = $uri_array[0];
		$index = 1;
		if ( $tamArray > 5 ){ $tamArray = 5; }
		if ( $tamArray < 1 ){ $tamArray = 1; }
		for ( $i=0 ; $i<$tamArray ; $i++ ){
			//echo "<img id=\"imgBann".$i."\" class=\"imgBannerOVVC\" src=\"http".$la_s."://".$secure_site."/images/bajiosecure/banner_secure/slide/large/personas_".$index.".jpg\" alt=\"bb_img1\" data-thumb=\"http".$la_s."://".$secure_site."/images/bajiosecure/banner_secure/slide/thumbs/1.jpg\" title=\"". $uri_array[$index] ."\"/>";
			echo "<img id=\"imgBann".$i."\" class=\"imgBannerOVVC\" src=\"http".$la_s."://".$secure_site."/images/bajiosecure/banner_secure/slide/large/personas_".$index.".jpg\" data-thumb=\"http".$la_s."://".$secure_site."/images/bajiosecure/banner_secure/slide/thumbs/1.jpg\" />";
			$index++;
		}
		
	}
	else{
		$txt_file = "empresa.ini";
		$uri_array = abrirFile($txt_file);
		$tamArray = $uri_array[0];
		$index = 1;
		if ( $tamArray > 5 ){ $tamArray = 5; }
		if ( $tamArray < 1 ){ $tamArray = 1; }
		for ( $i=0 ; $i<$tamArray ; $i++ ){			
			//echo "<img id=\"imgBann".$i."\" class=\"imgBannerOVVC\" src=\"http".$la_s."://".$secure_site."/images/bajiosecure/banner_secure/slide/large/empresa_".$index.".jpg\" alt=\"bb_img1\" data-thumb=\"http".$la_s."://".$secure_site."/images/bajiosecure/banner_secure/slide/thumbs/1.jpg\" title=\"". $uri_array[$index] ."\"/>";
			echo "<img id=\"imgBann".$i."\" class=\"imgBannerOVVC\" src=\"http".$la_s."://".$secure_site."/images/bajiosecure/banner_secure/slide/large/empresa_".$index.".jpg\" data-thumb=\"http".$la_s."://".$secure_site."/images/bajiosecure/banner_secure/slide/thumbs/1.jpg\" />";
			$index++;
		}		
	}
}

function abrirFile($pr_file){
	$archivoImg = "images/bajiosecure/banner_secure/slide/".$pr_file;
	if (file_exists($archivoImg))
	{
		$archivoDire = fopen( $archivoImg, "r" );
		$archivoLeido = fread( $archivoDire, filesize( $archivoImg ) );	
		fclose( $archivoImg );
		$uri_array = explode( "|", $archivoLeido );
	} else { $uri_array = "No se pudo abrir"; }
	return $uri_array;
}

?>		
</div>
