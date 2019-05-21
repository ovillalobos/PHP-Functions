<?php
include("eztransaccion/user/include/estilo.inc");
include("eztransaccion/user/include/estilo_divisas.inc");
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

	$transaction_buffer = "";

		echo "<h1>Cambiar imagen y frase</h1>";
		echo "<hr noshade='noshade' size='4' />";
		echo "<br />";

    	$server_name = $GLOBALS["SERVER_NAME"];
	//$HTTP_HOST = $GLOBALS["HTTP_HOST"];
	$imagesPath = $ini->read_var("site", "aaRSA_pathImg");

	switch ( $_POST['Access'] )
	{
		case "FrAc":
			include("eztransaccion/user/include/rsaChgImg_frac.inc");
			break;
		case "Confirm":
			include("eztransaccion/user/include/rsaChgImg_confirm.inc");
			break;
		case "Process":
			include("eztransaccion/user/include/rsaChgImg_process.inc");
			break;
	}
	$tr->blog( $qki, "AARSAQST", "", "", "", "", "" );
?>
