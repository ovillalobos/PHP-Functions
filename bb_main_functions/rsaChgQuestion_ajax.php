<?php
include("eztransaccion/user/include/estilo.inc");
include("eztransaccion/user/include/estilo_divisas.inc");
include_once("eztransaccion/user/include/controles_javascript.inc");
?>
	
<script type="text/javascript">
	function isEnter(e)
	{
		var characterCode;

		if(e && e.which)
		{
			e = e;
			characterCode = e.which;
		}
		else
		{
			characterCode = e.keyCode;
		}

		if(characterCode == 13)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
</script>

<?php
	if ( !isset( $Access ) && !isset( $_POST['Access'] ))
		$_POST['Access'] = "FrAc";

	$tr = new eZTransaccion( );

    	$server_name = $GLOBALS["SERVER_NAME"];
	//$HTTP_HOST = $GLOBALS["HTTP_HOST"];
	$imagesPath = $ini->read_var("site", "aaRSA_pathImg");
	
	$transaction_buffer = "";

		echo "<h1>Cambiar preguntas de seguridad</h1>";
		echo "<hr noshade='noshade' size='4' />";
		echo "<br />";

	switch ( $_POST['Access'] )
	{
		case "FrAc":
			include("eztransaccion/user/include/rsaChgQuestion_frac.inc");
			break;
		case "Confirm":
			include("eztransaccion/user/include/rsaChgQuestion_confirm.inc");
			break;
		case "Process":
			include("eztransaccion/user/include/rsaChgQuestion_process.inc");
			break;
	}
	
	$tr->blog( $qki, "AARSAIMG", "", "", "", "", "" );
	
?>
