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
	$("#aceptaimpR").click( function()
   	{		
		llamaPopUp();
	});
	
	 $("#Amount").keypress(function(event) 
	 {
		if ( event.which == 13 ) {
		llamaPopUp();
		}
	});
	
});
	
function llamaPopUp()
	{
		$('#bitacora1').hide(0);
		popUpOper($(this),"impR");
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
		  $('#aceptaimpR').show(0);
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
		  $('#aceptaimpR').hide(0);
		});
	</script>
<?php
}
//CRA - 08jul2013 Reingenieria Bajionet - Fin
($parametros['Access']!=""?$Access=$parametros['Access']:0);
($parametros['Trxn']!=""?$Trxn=$parametros['Trxn']:0);
($parametros['Amount']!=""?$Amount=$parametros['Amount']:0);
($parametros['RenCap']!=""?$RenCap=$parametros['RenCap']:0);
($parametros['PlazaB']!=""?$PlazaB=$parametros['PlazaB']:0);
($parametros['FrAccount']!=""?$FrAccount=$parametros['FrAccount']:0);
($parametros['code']!=""?$code=$parametros['code']:0);
($parametros['CustID']!=""?$usr=$usr=ltrim(rtrim($parametros['CustID'])):0);

include_once( "ezreimp/classes/ezreimp.php" ); //DBA Reimpresion de Comprobantes
$Amount=str_replace("^?",":",$Amount);
$Amount=str_replace("~?",",",$Amount);


$RenCap=str_replace("^?",":",$RenCap);
$RenCap=str_replace("~?",",",$RenCap);
$PlazaB=str_replace("^?",":",$PlazaB);
$PlazaB=str_replace("~?",",",$PlazaB);

$RenCap=str_replace("^a",chr(225),$RenCap);
$RenCap=str_replace("^e",chr(233),$RenCap);
$RenCap=str_replace("^i",chr(237),$RenCap);
$RenCap=str_replace("^o",chr(243),$RenCap);
$RenCap=str_replace("^u",chr(250),$RenCap);
$RenCap=str_replace("^n",chr(241),$RenCap);
$RenCap=str_replace("^N",chr(209),$RenCap);

$PlazaB=str_replace("^a",chr(225),$PlazaB);
$PlazaB=str_replace("^e",chr(233),$PlazaB);
$PlazaB=str_replace("^i",chr(237),$PlazaB);
$PlazaB=str_replace("^o",chr(243),$PlazaB);
$PlazaB=str_replace("^u",chr(250),$PlazaB);
$PlazaB=str_replace("^n",chr(241),$PlazaB);
$PlazaB=str_replace("^N",chr(209),$PlazaB);

function modSubmit ($submit)
	{
		$submit=str_replace("onclick=\"Amount.value=WithOutFormatAmount(Amount.value)\"","",$submit);
		$submit=str_replace("ONCLICK=\"DisabledButton()\"","",$submit);
		$submit=str_replace("ONCLICK=\"parent.history.back()\"","ONCLICK=\"iniProcesarMenu('pagoreferenciado', '')\"",$submit);
		$submit=str_replace("TYPE=SUBMIT", "type=\"button\" onclick=\"procesarAjax();\"",$submit);
		$submit=str_replace("type=\"submit\"","type=\"button\" onclick=\"procesarAjax();\"",$submit);
		$submit=str_replace("TYPE=\"PASSWORD\""," TYPE=\"PASSWORD\" onKeyPress=\"if (isEnter(event) == true) { procesarAjax(); }\"", $submit);

		return $submit;
	}


	$procesarAjax="	<script>
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

						function quitaAcento(acento)
						{
							for (var j = 0; j < acento.length; j++)
								{
									var Char=acento.charCodeAt(j);
									switch(Char)
									{
										case 225:
											acento=acento.substring(0,j)+'A'+acento.substring(j+1,acento.length);
										break;

										case 233:
											acento=acento.substring(0,j)+'E'+acento.substring(j+1,acento.length);
										break;

										case 237:
											acento=acento.substring(0,j)+'I'+acento.substring(j+1,acento.length);
										break;

										case 243:
											acento=acento.substring(0,j)+'O'+acento.substring(j+1,acento.length);
										break;

										case 250:
											acento=acento.substring(0,j)+'U'+acento.substring(j+1,acento.length);
										break;

										case 241:
											acento=acento.substring(0,j)+'^N'+acento.substring(j+1,acento.length);
										break;

										case 209:
											acento=acento.substring(0,j)+'^N'+acento.substring(j+1,acento.length);
										break;
									}

								}
							return acento;
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
							var parametros='';

							if(document.getElementsByName('Trxn')[0]!=null)
							{
								parametros+=',Trxn:'+document.getElementsByName('Trxn')[0].value;
							}

							if(document.getElementsByName('Access')[0]!=null)
							{
								parametros+=',Access:'+document.getElementsByName('Access')[0].value;
							}

							if(document.getElementsByName('Amount')[0]!=null)
							{
								var AmountAux=document.getElementsByName('Amount')[0].value;

								while(AmountAux.indexOf(\":\")!=-1)
								{
									AmountAux=AmountAux.replace(\":\",\"^?\");
								}

								while(AmountAux.indexOf(\",\")!=-1)
								{
									AmountAux=AmountAux.replace(\",\",\"~?\");
								}

								parametros+=',Amount:'+Acentos(AmountAux);
							}

							if(document.getElementsByName('RenCap')[0]!=null)
							{
								var RenCapAux=document.getElementsByName('RenCap')[0].value;

								while(RenCapAux.indexOf(\":\")!=-1)
								{
									RenCapAux=RenCapAux.replace(\":\",\"^?\");
								}

								while(RenCapAux.indexOf(\",\")!=-1)
								{
									RenCapAux=RenCapAux.replace(\",\",\"~?\");
								}
								parametros+=',RenCap:'+Acentos(RenCapAux);
							}

							if(document.getElementsByName('PlazaB')[0]!=null)
							{
								var PlazaBAux=document.getElementsByName('PlazaB')[0].value;


								while(PlazaBAux.indexOf(\":\")!=-1)
								{
									PlazaBAux=PlazaBAux.replace(\":\",\"^?\");
								}

								while(PlazaBAux.indexOf(\",\")!=-1)
								{
									PlazaBAux=PlazaBAux.replace(\",\",\"~?\");
								}

								parametros+=',PlazaB:'+ quitaAcento(PlazaBAux);
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

							if(document.getElementsByName('code')[0]!=null)
							{
								parametros+=',code:'+document.getElementsByName('code')[0].value;
							}
							if(document.getElementsByName('CustID')[0]!=null)
							{
								parametros+=',CustID:'+document.getElementsByName('CustID')[0].value;
							}

							iniProcesarMenu('pagoreferenciado', parametros);

						}
					</script>";


    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "pagoreferenciado.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "pagoreferenciado_tpl" => "pagoreferenciado.tpl"
        ) );


    $transaccion_buffer = "";
    if(empty($Access)) {
        $Access = "FrAc";
    }

    switch($Access) {
    case "FrAc":
        $particularFields = "";
        //$tr->blog($qki,"Impuestos", $FrAccount, $ImpTipo, $Month, $Year, $Amount);
		$tr->blog($qki,"PagoReferenciado", $FrAccount, $RenCap, $Amount, $PlazaB);
        break;
    case "Confirm":
    case "Process":
        //$particularFields = "&RFC=".urlencode($RFC)."&FrAccount=".urlencode($FrAccount)."&ImpTipo=".urlencode($ImpTipo)."&ImpEnvio=".urlencode($ImpEnvio)."&Day=".urlencode($Day)."&Month=".urlencode($Month)."&Year=".urlencode($Year)."&DayF=".urlencode($DayF)."&MonthF=".urlencode($MonthF)."&YearF=".urlencode($YearF)."&Amount=".urlencode($Amount);
        //$tr->blog($qki,"Impuestos", $FrAccount, $ImpTipo, $Month, $Year, $Amount);
		$particularFields = "&Passwd=".encrypt( $code, strtolower( $usr ) )."&FrAccount=".urlencode($FrAccount)."&RenCap=".urlencode($RenCap)."&Amount=".urlencode($Amount)."&PlazaB=".urlencode($PlazaB);
		$tr->blog($qki,"PagoReferenciado", $FrAccount, $RenCap, $Amount, $PlazaB);
        break;
    }
    $transaccion_buffer = "";
    // DebugBreak();
    $ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=ire&Access=".urlencode($Access)."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer);
	
	//***** CRA 03jul2013 Reingenieria Bajionet - Ini *****
	//***** CRA Se inserta html para darle tratamiento con 	Jquery Evita que cicle la pagina al usar Ajax en Transferencia
	if ( $Access == "Confirm" or $Access=="Process") // dgm - cra
	{
		$transaccion_buffer = "<!--BUSQUEDAI-->".$transaccion_buffer."<!--BUSQUEDAF-->"; // dgm - cra
	}
	//***** CRA 03jul2013 Reingenieria Bajionet - Fin *****
	
	// ********************************************************************
	if ($Access == "Process" and strpos($transaccion_buffer,"Su Pago ha sido procesado con") != false)
	//if ($Access == "Process" )
	{
		$buffer2 = str_replace(chr(34),chr(92).chr(34),$transaccion_buffer);

		// $ini =& INIFile::globalINI();

		// $SecureServer	  = $ini->read_var( "site", "SecureServer" );
		// $SecureServerType = $ini->read_var( "site", "SecureServerType" );
		// $DomainPostfix	  = $ini->read_var( "site", "DomainPostfix" );
		// $ServerNumber	  = $ini->read_var( "site", "ServerNumber" );

		$PageTitle		  = "Recibo Bancario de Pago de Contribuciones Federales";
		$Pagina = "<SCRIPT LANGUAGE='JavaScript'> function ventana() { var windowW = 800; var windowH = 450; var windowX = 10; var windowY = 10; var title = 'Recibo'; s = 'width='+windowW+',height='+windowH; cadena=\"<HTML><HEAD><link REL='STYLESHEET' HREF='" . $SecureServerType . "://" . $SecureServer . $ServerNumber . $DomainPostfix . "/impuestos/recibos.css' TYPE='text/css'><TITLE>" . $PageTitle . "</TITLE>";
		$Pagina = $Pagina."<SCRIPT LANGUAGE='JavaScript'> function window_onbeforeprint() { Imprimir.style.visibility = 'hidden'; Salir.style.visibility = 'hidden';} function window_onafterprint() { Imprimir.style.visibility = 'visible'; Salir.style.";
		$Pagina = $Pagina."visibility = 'visible'; }\"+\"<\"+\"/\"+\"SCRIPT></HEAD><BODY LANGUAGE='javascript' onbeforeprint='return window_onbeforeprint()' onafterprint='return window_onafterprint()'>";
		$Pagina = $Pagina."<INPUT type='button' value='Imprimir' id=Imprimir name=Imprimir onclick='window.print();'>&nbsp;<INPUT type='button' value=' Salir  ' id=Salir name=Salir onclick='window.close();'>";
		$Pagina = $Pagina."<BR>";
		$Pagina = $Pagina."<P><CENTER>";
		$Pagina = $Pagina."<IMG SRC='" . $SecureServerType . "://" . $SecureServer . $ServerNumber . $DomainPostfix . "/impuestos/LOGO_BB.GIF' ALIGN= 'TOP' Banco del Bajio>";
		$Pagina = $Pagina."</P></CENTER>";
		$Pagina = $Pagina."<H2>" . $PageTitle . "</H2>";
		$Pagina = $Pagina."<BR><hr noshade='noshade' size='4' color='#5A419c' />";
		$Pagina = $Pagina."<P ALIGN='RIGHT'><B><FONT>$date $time</FONT></B></P>";
		$Pagina = $Pagina.$buffer2;
		$Pagina = $Pagina."<BR><hr noshade='noshade' size='4' color='#5A419c' />";
		$Pagina = $Pagina."<p><FONT SIZE=1>Av. Manuel J. Clouthier No. 508 Col. Jardines del Campestre CP 37128 León, Gto.</FONT> </p>";
		$Pagina = $Pagina."<p><FONT SIZE=1>Consultas y aclaraciones 01 (477) 710- 4600 o al 01 (800) 471-0400</FONT></p>";
		$Pagina = $Pagina."<p><FONT SIZE=1>LA TRANSFERENCIA A LA QUE HACE REFERENCIA ESTE COMPROBANTE FUE REALIZADA CONFORME A LA INFORMACIÓN E INSTRUCCIONES QUE NUESTRO CLIENTE ALIMENTÓ EN EL SISTEMA DE BANCO DEL BAJÍO, POR LO TANTO EL CLIENTE ES EL ÚNICO RESPONSABLE PRESENTE Y FUTURO POR CUALQUIER ERROR U OMISIÓN EN ESTA OPERACIÓN.</FONT></P>";
		//$Pagina = $Pagina."<p><FONT SIZE=1>EL ÚNICO COMPROBANTE OFICIAL DE ESTA TRANSACCIÓN ES EL ESTADO DE CUENTA QUE EMITE BANCO DEL BAJIO.</FONT></p>"; ATAR se comenta por especificaciones del SAT
		$Pagina = $Pagina."</BODY></HTML>\"; NFW =";
		$Pagina = $Pagina." window.open(\"\",'Recibo',\"toolbar=no,menubar=no,scrollbars=2,\"+s); NFW.blur(); NFW.resizeTo(windowW,windowH); NFW.moveTo(windowX,windowY); NFW.";
		$Pagina = $Pagina."document.open(); NFW.document.write(cadena); NFW.document.close(); NFW.window.focus(); } ventana(); </SCRIPT>";
		//DBA Reimpresion de Comprobantes
		$FechaHora = "";
		$QryRIC = new eZReImp( );
		$QryRIC -> store($CustID,$FrAccount,$buffer2,"ire",$PageTitle,$FechaHora);
		//DBA Reimpresion de Comprobantes
	}
	// ********************************************************************

    $t->set_var( "transaccion_buffer", $transaccion_buffer.$Pagina );
    //$t->pparse( "output", "pagoreferenciado_tpl" ); //ACS
    $transaccion_buffer=$t->parse( "output", "pagoreferenciado_tpl" );
	$transaccion_buffer=modSubmit($transaccion_buffer);
	$transaccion_buffer=$procesarAjax.$transaccion_buffer;

?>