<script>

function recarga( func_var, btn_var, state_var)
{
	$('#main').html("<br><br><br><br><br><br><br><br><br><div align=center style='width:100%;'><img src=\"https://images.bb.com.mx/sitedesign/bajionet/images/loading.gif\"	style=\"align: center;cursor: pointer; border: 0px solid red;\" title=\"cargando\" alt=\"cargando . . .\" align=center /><br>Cargando...</div>");
	$('#foco').focus();
	$('#main').load('/transaccion/autorizacionpagosarchivo/',{func:func_var,btn_pagos:btn_var,state:state_var});
}
function env_pagosAut(  btn_var,state_var, seleccionados_var)
{
	$('#main').html("<br><br><br><br><br><br><br><br><br><div align=center style='width:100%;'><img src=\"https://images.bb.com.mx/sitedesign/bajionet/images/loading.gif\"	style=\"align: center;cursor: pointer; border: 0px solid red;\" title=\"cargando\" alt=\"cargando . . .\" align=center /><br>Cargando...</div>");
	$('#foco').focus();
	$('#main').load('/transaccion/autorizacionpagosarchivo/',{ btn_pagos:btn_var, state:state_var, seleccionados:seleccionados_var});
}
</script>
<?php
//echo "<script src=\"https://$HTTP_HOST/jquery.form.js\" type=\"text/javascript\"></script>";
session_start();
//echo "<script src=\"https://$HTTP_HOST/jquery.form.js\" type=\"text/javascript\"></script>";
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
                         "eztransaccion/user/intl/", $Language, "AutorizacionPagosArchivo.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "AutorizacionPagosArchivo_tpl" => "AutorizacionPagosArchivo.tpl"
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
	if($_SESSION['cantreg']=="")
	        $_SESSION['cantreg'] = 10;
	print "<div><input readOnly=true size=1 style=\" border: 0px; background-color:transparent; height:2px;\" type=text id='foco'></div><div id='main'>";

	if ($_POST['func']=="")
	{
		
		$t->pparse( "output", "AutorizacionPagosArchivo_tpl" );
		$result = ControlAcceso("autarc",$usr);
		
		if($result==="0000")
		{
			
			include("eztransaccion/user/include/autorizacionpagosarchivo_main_form.inc");
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
		
		include("eztransaccion/user/include/autpagsarcFunc.inc");
		
		switch($_POST['func'])
		{
			case "consulta_archivos":
				$_SESSION['cantreg']=$_POST["cant_rowno"];
				print consulta_archivos($_POST["usr"], $_POST["rowno"], $_POST["cant_rowno"],$_POST["direction"],$_POST["page"], "transferido");
				exit();
			break;
			
			case "autorizacion_archivoDetalle":
				$t->pparse( "output", "AutorizacionPagosArchivo_tpl" );
				print autorizacion_archivoDetalle($_POST["usr"], $_POST["rowno"]);
				exit();
			break;
			
			case "Confirm_Oper":
				//MAOS Ene2015 Autorizacion sin registros I
				$verificaPagos = $_POST['seleccionados'];
						
				if( $verificaPagos != "" ){
					$t->pparse( "output", "AutorizacionPagosArchivo_tpl" );
					include("eztransaccion/user/include/pagosporautorizararch_confirmaciones.inc");
					exit();
				}else{
					$action ="noregistros";
					$_SESSION['user'] = $usr;
					include("eztransaccion/user/include/autorizacionpagosarchivo_main_form.inc");
					exit();
				}
				//MAOS Ene2015 Autorizacion sin registros F
			break;
			
			case "Autorizar":
				$t->pparse( "output", "AutorizacionPagosArchivo_tpl" );
				autorizar_pagos($usr,$_POST['tk']);
				$action ="autorizar";
				$_SESSION['user'] = $usr;
				include("eztransaccion/user/include/autorizacionpagosarchivo_main_form.inc");
				exit();
			break;
			
			case 'Borrar':
				// $registros = split(",",$_POST['pagos']);
				
				// for( $i=0 ; $i < count($registros) ; $i++)
				// {
					// borrar_archivo($usr, $registros[$i],$_POST['tk']);
					
					// $_SESSION['user'] = $usr;
					////print_r( $_SESSION['xml_pagos_file_status']);
					////exit();
					
				// }
				
				borrar_archivo($usr, $_POST['tk']);
					
				$_SESSION['user'] = $usr;
				
				$action="borrar";
				$t->pparse( "output", "AutorizacionPagosArchivo_tpl" );
				include("eztransaccion/user/include/autorizacionpagosarchivo_main_form.inc");

				
				exit();
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

