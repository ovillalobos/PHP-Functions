<link rel='stylesheet' type='text/css' media='screen' href='/middleware/css/errorValidate.css' />
<link rel='stylesheet' type='text/css' media='screen' href='/middleware/css/estilo_middleware.css' />
<link rel='stylesheet' type='text/css' media='screen' href='/middleware/css/tab-view.css' />
<link rel='stylesheet' type='text/css' media='screen' href='/middleware/css/ayuda.css' />

<script language="javascript" src="/middleware/js/common/tab-view.js" />
<script language="javascript" src="/middleware/js/common/jsValidador.js" />
<script language="javascript" src="/middleware/js/common/jsTooltips.js" />
<script language="javascript" src="/middleware/js/common/jsTkPopUp.js"/>
<script language="javascript" src="/middleware/js/common/jsPopUp.js" />
<script language="javascript" src="/middleware/js/common/jsControl.js" />

<link rel='stylesheet' type='text/css' media='screen' href='/middleware/css/jquery-ui-1.7.3.custom.css' />
<link rel='stylesheet' type='text/css' media='screen' href='/middleware/js/common/jquery.jqGrid-4.2.0/css/ui.jqgrid.css' />

<script language="javascript" src="/middleware/js/common/jquery.jqGrid-4.2.0/js/i18n/grid.locale-es.js" />
<script language="javascript" src="/middleware/js/common/jquery.jqGrid-4.2.0/js/jquery.jqGrid.min.js" />

<script type="text/javascript">
//**************CRA 03jul2013 Reingenieria Bajionet************* - Ini
$(document).ready(function() 
{
	$("#aceptaimpCF").click( function()
   	{		
		llamaPopUp();
	});
	
	 $("#Amount").keypress(function(event) 
	 {
		if ( event.which == 13 ) {
		llamaPopUp();
		}
	});
	
	$("#Desc").keypress(function(event) 
	 {
		if ( event.which == 13 ) {
		llamaPopUp();
		}
	});
});
	
function llamaPopUp()
	{
		$('#bitacora1').hide(0);
		popUpOper($(this),"impCF");
	}
//**************CRA 03jul2013 Reingenieria Bajionet************* - Fin
</script>

<?php
//CRA - 08jul2013 Reingenieria Bajionet - Ini
$JQueryTrans = $ini->read_var("site" , "JQueryTrans");

If($JQueryTrans == 1)
{?>
	<script>
		$(function() {
		  $('#btnAceptar').hide(0);
		  $('#aceptaimpCF').show(0);
		  $("input").removeAttr("onkeypress");
		});
	</script>
<?php	
}
else
{
?>
	<script>
		$(function() {
		   $('#btnAceptar').show(0);
		  $('#aceptaimpCF').hide(0);
		});
	</script>
<?php
}
//CRA - 08jul2013 Reingenieria Bajionet - Fin
($parametros['Access']!=""?$Access=$parametros['Access']:0);
($parametros['Datos1']!=""?$Datos1=$parametros['Datos1']:0);
($parametros['DatosImp']!=""?$DatosImp=$parametros['DatosImp']:0);
($parametros['UbNo']!=""?$UbNo=$parametros['UbNo']:0);
($parametros['CadOrig']!=""?$CadOrig=$parametros['CadOrig']:0);
($parametros['FrAccount']!=""?$FrAccount=$parametros['FrAccount']:0);
($parametros['CustID']!=""?$usr=$usr=ltrim(rtrim($parametros['CustID'])):0);
($parametros['Amount']!=""?$Amount=$parametros['Amount']:0);

$DatosImp=str_replace("^?",":",$DatosImp);
$DatosImp=str_replace("~?",",",$DatosImp);
include_once( "ezreimp/classes/ezreimp.php" ); //DBA Reimpresion de Comprobantes
$DatosImp=str_replace("^a",chr(225),$DatosImp);
$DatosImp=str_replace("^e",chr(233),$DatosImp);
$DatosImp=str_replace("^i",chr(237),$DatosImp);
$DatosImp=str_replace("^o",chr(243),$DatosImp);
$DatosImp=str_replace("^u",chr(250),$DatosImp);
$DatosImp=str_replace("^n",chr(241),$DatosImp);
$DatosImp=str_replace("^N",chr(209),$DatosImp);
$Datos1=str_replace("^n",chr(241),$Datos1);
$Datos1=str_replace("^N",chr(209),$Datos1);
$Datos1=str_replace("^?",":",$Datos1);
$Datos1=str_replace("~?",",",$Datos1);
$CadOrig=str_replace("^?",":",$CadOrig);
$CadOrig=str_replace("~?",",",$CadOrig);

function modSubmit ($submit)
{
	$submit=str_replace("ONCLICK=\"DisabledButton()\"","",$submit);
	$submit=str_replace("ONCLICK=\"parent.history.back()\"","ONCLICK=\"iniProcesarMenu('PCFimpuestos', '')\"",$submit);
	$submit=str_replace("TYPE=SUBMIT", "type=\"button\" onclick=\"procesarAjax();\"",$submit);
	$submit=str_replace("TYPE=\"SUBMIT\"","TYPE=\"BUTTON\" ONCLICK=\"javascript:procesarAjax();\"", $submit);
	$submit=str_replace("type=\"submit\"","type=\"button\" onclick=\"procesarAjax();\"",$submit);
	$submit=str_replace("type=submit","type=\"button\" onclick=\"procesarAjax();\"",$submit);
	$submit=str_replace("TYPE=\"PASSWORD\""," TYPE=\"PASSWORD\" onKeyPress=\"if (isEnter(event) == true) { procesarAjax(); }\"", $submit);

	return $submit;
}

$procesarAjax="<script>

						function Acentos(Text)
						{
									for (var j = 0; j < Text.length; j++)
										{
											var Char=Text.charCodeAt(j);
											switch(Char)
											{
												case 225:
													Text=Text.substring(0,j)+'^a'+Text.substring(j+1,Text.length);
												break;
												
												case 233:
													Text=Text.substring(0,j)+'^e'+Text.substring(j+1,Text.length);
												break;
												
												case 237:
													Text=Text.substring(0,j)+'^i'+Text.substring(j+1,Text.length);
												break;
												
												case 243:
													Text=Text.substring(0,j)+'^o'+Text.substring(j+1,Text.length);
												break;
												
												case 250:
													Text=Text.substring(0,j)+'^u'+Text.substring(j+1,Text.length);
												break;
												
												case 241:
													Text=Text.substring(0,j)+'^n'+Text.substring(j+1,Text.length);
												break;
												
												case 209:
													Text=Text.substring(0,j)+'^N'+Text.substring(j+1,Text.length);
												break;
												
											}
											
										}
							return Text;
						}
						
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


						function procesarAjax()
						{

							if (document.getElementsByName('Access')[0].value=='Confirm')
							{
								document.frmApplet.Datos1.value    = document.AppCreditos.appEnviaDatos();
								document.frmApplet.DatosImp.value 	    = document.AppCreditos.appEnviaImpresion();
								document.frmApplet.CadOrig.value  	    = document.AppCreditos.appEnviaCadOriginal();
								var dImp=document.frmApplet.DatosImp.value

								while(dImp.indexOf(\":\")!=-1)
								{
									dImp=dImp.replace(\":\",\"^?\");
								}
								while(dImp.indexOf(\",\")!=-1)
								{
									dImp=dImp.replace(\",\",\"~?\");
								}
								dImp=Acentos(dImp);
								if (document.frmApplet.Datos1.value == '*')
								{
									window.alert('Debe de capturar al menos un concepto.');
									return false;
								}
							}


							var parametros=	'CustID:'+document.getElementsByName('CustID')[0].value;

							if(document.getElementsByName('Datos1')[0]!=null)
							{
								var datosAux=document.getElementsByName('Datos1')[0].value;
								
								while(datosAux.indexOf(\":\")!=-1)
								{
									datosAux=datosAux.replace(\":\",\"^?\");
								}
								
								while(datosAux.indexOf(\",\")!=-1)
								{
									datosAux=datosAux.replace(\",\",\"~?\");
								}
								parametros+=',Datos1:'+Acentos(datosAux);
							}

							if(document.getElementsByName('DatosImp')[0]!=null)
							{
								//parametros+=',DatosImp:'+document.getElementsByName('DatosImp')[0].value;
								parametros+=',DatosImp:'+dImp;
							}

							if(document.getElementsByName('Access')[0]!=null)
							{
								parametros+=',Access:'+document.getElementsByName('Access')[0].value;
							}

							if(document.getElementsByName('UbNo')[0]!=null)
							{
								parametros+=',UbNo:'+document.getElementsByName('UbNo')[0].value;
							}

							if(document.getElementsByName('Amount')[0]!=null)
							{
								parametros+=',Amount:'+document.getElementsByName('Amount')[0].value;
							}

							if(document.getElementsByName('CadOrig')[0]!=null)
							{
								var cadOrig=document.getElementsByName('CadOrig')[0].value;
								
								while(cadOrig.indexOf(\":\")!=-1)
								{
									cadOrig=cadOrig.replace(\":\",\"^?\");
								}
								
								while(cadOrig.indexOf(\",\")!=-1)
								{
									datosAux=cadOrig.replace(\",\",\"~?\");
								}
								
								parametros+=',CadOrig:'+cadOrig;
							}

							if(document.getElementsByName('FrAccount')[0]!=null)
							{

								if (document.getElementsByName('FrAccount')[0].type!='HIDDEN' && document.getElementsByName('FrAccount')[0].type!='hidden')
								{
									parametros+=',FrAccount:'+document.getElementsByName('FrAccount')[0].options[document.getElementsByName('FrAccount')[0].selectedIndex].value;
								}
								else
								{
									parametros+=',FrAccount:'+document.getElementsByName('FrAccount')[0].value;
								}
							}

							iniProcesarMenu('PCFimpuestos', parametros);
						}
				</script>";


$Language = $ini->read_var( "eZTransaccionMain", "Language" );
// $backend = $ini->read_var( "eZTransaccionMain", "Backend" );
$ServerNumber = $ini->read_var( "site", "ServerNumber" );
$DomainPostfix = $ini->read_var( "site", "DomainPostfix" );


    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "PCFimpuestos.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "PCFimpuestos_tpl" => "PCFimpuestos.tpl"
        ) );

    $transaccion_buffer = "";
//ATAR se bloquea recepcion de pago
    $mensaje_sat = "";
	////ATAR SE BLOQUEA EN DEFINITIVA RECEPCION DE PAGOS PROVISIONALES
	$mensaje_sat="<table width='80%' height='62' border='1' align='center' cellpadding='5' cellspacing='0'><tr><td colspan='0' align='center'>Sr. Contribuyente, el SAT le informa:<br>A partir de Agosto del 2014 est&aacute; obligado a utilizar el Servicio de Pago Referenciado para presentar sus pagos de impuestos federales, no debe de utilizar el esquema anterior de pagos electr&oacutenicos.</td></tr></table>";		
	$noPagProv=1;
    if($noPagProv==0){
    if(empty($Access)) {
        $Access = "FrAc";
    }
    switch($Access) {
    case "FrAc":
        $particularFields = "";
        $tr->blog($qki,"Impuestos Creditos Fiscales", $FrAccount, $FrAccount, $DatosImp, $Year, $Amount);
        break;
    case "Confirm":
        $Datos1 = strtr($Datos1,"&Ρ‘","{^<");
	 $Datos1 = strtr($Datos1,"%","~");	//EVG-EDS 02112004	//-- EVG-EDS Impuestos V4.0 28112005
    	//$DatosImp = strtr($DatosImp,"αινσϊό°","aeiouuo");
   	 $DatosImp = strtr($DatosImp,"αινσϊό°","Ώ?;*[]o");//EVG-EDS 02112004
    	 $DatosImp = strtr($DatosImp,"&Ρ","{^");//EVG-EDS 02112004
	 $DatosImp = strtr($DatosImp,"&","{"); //EVG-EDS 02112004
        $particularFields = "&FrAccount=".urlencode($FrAccount)."&Datos=".$Datos1."&DatosImp=".$DatosImp."&CadOrig=".$CadOrig;
        $tr->blog($qki,"Impuestos Creditos Fiscales", $FrAccount, $Datos, $DatosImp, $Year, $Amount);
        break;
    case "Process":
        $particularFields = "&FrAccount=".urlencode($FrAccount)."&UbNo=".$UbNo;
        $tr->blog($qki,"Impuestos Creditos Fiscales", $FrAccount, $UbNo, $DatosImp, $Year, $Amount);
        break;
    }
	//var_dump($particularFields);
    $transaccion_buffer = "";
    // DebugBreak();
    $ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=pcf&Access=".urlencode($Access)."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // pago de impuestos
		
	//***** CRA 03jul2013 Reingenieria Bajionet - Ini *****
	//***** CRA Se inserta html para darle tratamiento con 	Jquery Evita que cicle la pagina al usar Ajax en Transferencia
	if ( $Access == "Confirm" or $Access=="Process") // dgm - cra
	{
		$transaccion_buffer = "<!--BUSQUEDAI-->".$transaccion_buffer."<!--BUSQUEDAF-->"; // dgm - cra
	}
	//***** CRA 03jul2013 Reingenieria Bajionet - Fin *****	
		
	if ($Access == "Process")
	{
		$buffer2 = str_replace(chr(34),chr(92).chr(34),$transaccion_buffer);
		$ini =& INIFile::globalINI();

		$SecureServer = $ini->read_var( "site", "SecureServer" );
		$SecureServerType = $ini->read_var( "site", "SecureServerType" );
		$DomainPostfix = $ini->read_var( "site", "DomainPostfix" );
		$ServerNumber = $ini->read_var( "site", "ServerNumber" );

		// *****************************************************************************************
		// DGM I 25May2006 Respetar acumulado diario en Pago Impuestos f
		if ( strpos($buffer2, "Su pago de impuestos ha sido efectuado con") )
		{
		// *****************************************************************************************
			$Pagina = "<SCRIPT LANGUAGE='JavaScript'> function ventana() { var windowW = 800; var windowH = 450; var windowX = 10; var windowY = 10; var title = 'Recibo'; s = 'width='+windowW+',height='+windowH; cadena=\"<HTML><HEAD><link REL='STYLESHEET' HREF='" . $SecureServerType . "://" . $SecureServer . $ServerNumber . $DomainPostfix . "/impuestos/estilos.css' TYPE='text/css'><TITLE>Recibo de Contribuciones Federales</TITLE>";
			$Pagina = $Pagina."<SCRIPT LANGUAGE='JavaScript'> function window_onbeforeprint() { Imprimir.style.visibility = 'hidden'; Salir.style.visibility = 'hidden';} function window_onafterprint() { Imprimir.style.visibility = 'visible'; Salir.style.";
			$Pagina = $Pagina."visibility = 'visible'; }\"+\"<\"+\"/\"+\"SCRIPT></HEAD><BODY LANGUAGE='javascript' onbeforeprint='return window_onbeforeprint()' onafterprint='return window_onafterprint()'>";
			$Pagina = $Pagina."<INPUT type='button' value='Imprimir' id=Imprimir name=Imprimir onclick='window.print();'>&nbsp;<INPUT type='button' value=' Salir  ' id=Salir name=Salir onclick='window.close();'>";
			$Pagina = $Pagina.$buffer2;
			$Pagina = $Pagina."</BODY></HTML>\"; NFW =";
			$Pagina = $Pagina." window.open(\"\",'Recibo',\"toolbar=no,menubar=no,scrollbars=2,\"+s); NFW.blur(); NFW.resizeTo(windowW,windowH); NFW.moveTo(windowX,windowY); NFW.";
			$Pagina = $Pagina."document.open(); NFW.document.write(cadena); NFW.document.close(); NFW.window.focus(); } ventana(); </SCRIPT>";
			//DBA Reimpresion de Comprobantes
			$FechaHora = "";
			$QryRIC = new eZReImp( );
			$QryRIC -> store($CustID,$FrAccount,$buffer2,"imp","",$FechaHora);
			//DBA Reimpresion de Comprobantes
		}
	}
    }

    //$t->set_var( "transaccion_buffer", $transaccion_buffer.$Pagina );
    $t->set_var( "transaccion_buffer", $mensaje_sat.$transaccion_buffer.$Pagina );
    //$t->pparse( "output", "PCFimpuestos_tpl" );
    $transaccion_buffer=$t->parse( "output", "PCFimpuestos_tpl" );
	$transaccion_buffer=modSubmit($transaccion_buffer);
	$transaccion_buffer=$procesarAjax.$transaccion_buffer;

?>