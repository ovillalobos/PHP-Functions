<?php


($parametros['Access']!=""?$Access=$parametros['Access']:0);
($parametros['CustID']!=""?$CustID=$parametros['CustID']:0);
($parametros['AgruPagIni']!=""?$AgruPagIni=$parametros['AgruPagIni']:0);
($parametros['DiasPzo']!=""?$DiasPzo=$parametros['DiasPzo']:0);
($parametros['Empresa']!=""?$Empresa=$parametros['Empresa']:0);
($parametros['Cq']!=""?$Cq=$parametros['Cq']:0);
($parametros['FrAccount']!=""?$FrAccount=$parametros['FrAccount']:0);
($parametros['Button']!=""?$Button=$parametros['Button']:0);
($parametros['Accion']!=""?$Accion=$parametros['Accion']:0);

function modSubmit ($submit)
{
	$submit=str_replace("TYPE=SUBMIT","TYPE=\"BUTTON\" ONCLICK=\"javascript:procesarAjax();\"", $submit);
	$submit=str_replace("FORM METHOD=post","FORM METHOD=post action=\"/transaccion/ArcPP/\"   id=\"ArcPPForm\"", $submit);
	$submit=str_replace("TYPE=\"SUBMIT\"","TYPE=\"BUTTON\" ONCLICK=\"javascript:procesarAjax();\"", $submit);
	$submit=str_replace("select name=\"DiasPzo\"","select name=\"selectDiasPzo\"", $submit);
	$submit=str_replace("SELECT NAME=\"DiasPzo\"","select name=\"selectDiasPzo\"", $submit);
	$submit=str_replace("HREF=\"/transaccion/ConArcPP/\"","HREF=\"#\" onclick=\"javascript:iniProcesarMenu('ConArcPP', '');\"", $submit);
	return $submit;
}


$t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
					 "eztransaccion/user/intl/", $Language, "ArcPP.php" );

$t->setAllStrings();

$t->set_file( array(
	"ArcPP_tpl" => "ArcPP.tpl"
	) );

$particularFields = "";

if(empty($Access)) {
	$Access = "FrAc";
}
$Accion  = "ArcPP";
//ACS
$procesarAjax="<script>
					function procesarAjax()
					{
						var parametros=	'CustID:'+document.getElementsByName('CustID')[0].value;
									
							if(document.getElementsByName('Access')[0]!=null)
							{
								parametros+=',Access:'+document.getElementsByName('Access')[0].value;
							}
							
													
							if(document.getElementsByName('DiasPzo')[0]!=null)
							{
							
								if (document.getElementsByName('DiasPzo')[0].type!='HIDDEN' && document.getElementsByName('DiasPzo')[0].type!='hidden')
								{
									parametros+=',DiasPzo:'+document.getElementsByName('DiasPzo')[0].options[document.getElementsByName('DiasPzo')[0].selectedIndex].value;
								}
								else
								{
									parametros+=',DiasPzo:'+document.getElementsByName('DiasPzo')[0].value;
								}
							}
							
							if(document.getElementsByName('selectDiasPzo')[0]!=null)
							{
								document.getElementsByName('DiasPzo')[0].value=document.getElementsByName('selectDiasPzo')[0].options[document.getElementsByName('selectDiasPzo')[0].selectedIndex].value;
								//document.cookie='eZMyFileUpload='+document.getElementsByName('selectDiasPzo')[0].options[document.getElementsByName('selectDiasPzo')[0].selectedIndex].value;
								parametros+=',DiasPzo:'+document.getElementsByName('selectDiasPzo')[0].options[document.getElementsByName('selectDiasPzo')[0].selectedIndex].value;
							}
							
							if(document.getElementsByName('Empresa')[0]!=null)
							{
								parametros+=',Empresa:'+document.getElementsByName('Empresa')[0].value;
							}
							
							if(document.getElementsByName('AgruPagIni')[0]!=null)
							{
								parametros+=',AgruPagIni:'+document.getElementsByName('AgruPagIni')[0].value;
							}
							
							if(document.getElementsByName('Cq')[0]!=null)
							{
								parametros+=',Cq:'+document.getElementsByName('Cq')[0].value;
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
							
									
						//alert ('Presionamos el boton correctamente'+parametros);
						if (document.getElementsByName('Access')[0].value!='FrAc')
						{
							//NEX -21Ago2012- [iniciio] Falla encarga archivos de nomina
							//document.cookie='eZMyFileUpload='+document.getElementsByName('DiasPzo')[0].value;
							//document.cookie='QueAccess=Process';
							//document.cookie='QueAccion=".$Accion."';
							var today = new Date();
							var expire= new Date();
							expire.setTime( today.getTime()+ 3600*24*365 );
							//alert ('Presionamos el boton '+ expire.setTime( today.getTime()+ 3600*24*365 ) + ' correctamente'+parametros+ ' strring ::'+expire.toGMTString());
							document.cookie='eZMyFileUpload='+document.getElementsByName('DiasPzo')[0].value+';expires=expire.toGMTString();path=/;domain=bb.com.mx';
							document.cookie='QueAccess=Process;expires=expire.toGMTString();path=/;domain=bb.com.mx';
							document.cookie='QueAccion=".$Accion.";expires=expire.toGMTString();path=/;domain=bb.com.mx';
							//NEX -21Ago2012- [fin] Falla encarga archivos de nomina
							document.getElementById(\"ArcPPForm\").submit()							
						}
						else
						{
							iniProcesarMenu('ArcPP', parametros);
						}
					}
			   </script>";
//ACS
switch($Access) {
case "FrAc":
	// if (isset($Access)) {
		//setcookie ( "QueAccion", $Access, time() + ( 3600 * 24 * 365 ), "/",  "bb.com.mx", 0 ) or print( "Error: could not set cookie." );
		// echo "<script>document.cookie='QueAccess=".$Access."';</script>";
	// }
	
	// if (isset($Accion)) {
		//setcookie ( "QueAccion", $Accion, time() + ( 3600 * 24 * 365 ), "/",  "bb.com.mx", 0 ) or print( "Error: could not set cookie." );
		// echo "<script>document.cookie='QueAccion=".$Accion."';</script>";
	// }

if ( $Agrupa=="Aceptar" )
{	
	$Access = "FrAc";
	$Accion  = "ArcPP";
	$DiasPzo = "";
	$particularFields = "&Accion=".urlencode($Accion)."&FrAccount=".urlencode($FrAccount)."&Button=".urlencode($Button)."&AgruPagIni=".urlencode($AgruPagIni);
	$tr->blog($qki,"ArcPP", $FrAccount, $ToAccount, $DiasPzo, $Parent1, $Accion);
	$ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=arc&Access=".urlencode($Access)."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // intercambio de archivos de Pago a Proveedores
	$t->set_var( "transaccion_buffer", $transaccion_buffer );
	//$t->pparse( "output", "ArcPP_tpl" );
	$transaccion_buffer=$t->parse( "output", "ArcPP_tpl" );
	$transaccion_buffer=modSubmit($transaccion_buffer);
	$transaccion_buffer=$procesarAjax.$transaccion_buffer;
	
	//$transaccion_buffer+=$procesarAjax;
	
	
}
else
{
	$DiasPzo = "";
	$particularFields = "&Accion=".urlencode($Accion)."&FrAccount=".urlencode($FrAccount)."&Button=".urlencode($Button)."&AgruPagIni=".urlencode($AgruPagIni);
	$tr->blog($qki,"ArcPP", $FrAccount, $ToAccount, $DiasPzo, $Parent1, $Accion);
	$ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=arc&Access=".urlencode($Access)."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // intercambio de archivos de Pago a Proveedores
	$t->set_var( "transaccion_buffer", $transaccion_buffer );
	//$t->pparse( "output", "ArcPP_tpl" );
	$transaccion_buffer=$t->parse( "output", "ArcPP_tpl" );
	$transaccion_buffer=modSubmit($transaccion_buffer);
	$transaccion_buffer=$procesarAjax.$transaccion_buffer;
	//$transaccion_buffer+=$procesarAjax;
}
	break;
case "Confirm":
case "Process":

	if (isset($Access)) {
		//setcookie ( "QueAccess", $Access, time() + ( 3600 * 24 * 365 ), "/",  "bb.com.mx", 0 ) or print( "Error: could not set cookie." );
		echo "<script>document.cookie='QueAccess=".$Access."';</script>";
	}
	if (isset($Accion)) {
		//setcookie ( "QueAccion", $Accion, time() + ( 3600 * 24 * 365 ), "/",  "bb.com.mx", 0 ) or print( "Error: could not set cookie." );
		echo "<script>document.cookie='QueAccion=".$Accion."';</script>";
	}
	
	 //include( "eztransaccion/user/myfileupload.php" );
	 //$transaccion_buffer= "<script>window.location=\"/transaccion/myfileupload/\"</script>";
	 //$transaccion_buffer= "<script>iniProcesarMenu('myFileUpload', '');</script>";
	
	//echo "<script>alert('$DiasPzo');</script>";	
	
	$eZMyFileUpload=$DiasPzo;
	$t->set_var( "transaccion_buffer", $transaccion_buffer );
	$t->pparse( "output", include( "eztransaccion/user/myfileupload.php" ));
	 
	// $t->set_var( "transaccion_buffer", $transaccion_buffer);
	//echo "<script>iniProcesarMenu('myfileupload', '');</script>";
	
	break;
}
 

?>

