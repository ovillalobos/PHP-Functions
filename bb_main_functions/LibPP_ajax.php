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
($parametros['code']!=""?$code=$parametros['code']:0);

function modSubmit ($submit)
{
	
	$submit=str_replace("TYPE=SUBMIT", "type=\"button\" onclick=\"procesarAjax();\"",$submit);
	$submit=str_replace("TYPE=\"SUBMIT\"","TYPE=\"BUTTON\" ONCLICK=\"javascript:procesarAjax();\"", $submit);
	$submit=str_replace("TYPE=\"SUBMIT\"","type=\"button\" onclick=\"procesarAjax();\"",$submit);
	$submit=str_replace("type=\"submit\"","type=\"button\" onclick=\"procesarAjax();\"",$submit);
	$submit=str_replace("/transaccion/ConArcPP/\"","#\" onclick=\"javascript:iniProcesarMenu('ConArcPP', '');\"", $submit);
	$submit=str_replace("TYPE=\"PASSWORD\""," TYPE=\"PASSWORD\" onKeyPress=\"if (isEnter(event) == true) { procesarAjax(); }\"", $submit);
	$submit=str_replace("TYPE=\"RADIO\"","TYPE=\"RADIO\" ID=\"DiasPzo\"  onclick=\"check(this.value)\"", $submit);
	$submit=str_replace("TYPE=RADIO","TYPE=\"RADIO\" ID=\"DiasPzo\" onclick=\"check(this.value)\"", $submit);
	$submit=str_replace("type=\"radio\"","TYPE=\"RADIO\" ID=\"DiasPzo\" onclick=\"check(this.value)\"", $submit);
	return $submit;
}

	$procesarAjax="
					<script>
						var radioVar;
						radioVar='';
						
						function check(browser)
					    {
						  radioVar=browser;
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
							var parametros=	'CustID:'+document.getElementsByName('CustID')[0].value;
							
							if(document.getElementsByName('Access')[0]!=null)
							{
								parametros+=',Access:'+document.getElementsByName('Access')[0].value;
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
							
							if(document.getElementsByName('AgruPagIni')[0]!=null)
							{
								parametros+=',AgruPagIni:'+document.getElementsByName('AgruPagIni')[0].value;
							}
							
							if(radioVar!='')
							{
								
								
								//parametros+=',DiasPzo:'+document.getElementById('DiasPzo').value;
								parametros+=',DiasPzo:'+radioVar;
							}
							
							if(document.getElementsByName('Empresa')[0]!=null)
							{
								parametros+=',Empresa:'+document.getElementsByName('Empresa')[0].value;
							}
							
							if(document.getElementsByName('Cq')[0]!=null)
							{
								parametros+=',Cq:'+document.getElementsByName('Cq')[0].value;
							}

							if(document.getElementsByName('code')[0]!=null)
							{
								parametros+=',code:'+document.getElementsByName('code')[0].value;
							}
							
							
							
							iniProcesarMenu('LibPP', parametros);
							
						}
					</script>";

    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "LibPP.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "LibPP_tpl" => "LibPP.tpl"
        ) );


    $particularFields = "";
	

    if(empty($Access)) {
        $Access = "FrAc";
    }
    $Accion  = "LibArcPP";

	switch($Access) {
    case "FrAc":
        if (isset($Access)) {
            //setcookie ( "QueAccess", $Access, time() + ( 3600 * 24 * 365 ), "/",  "bb.com.mx", 0 ) or print( "Error: could not set cookie." );
			//NEX -21Ago2012- [iniciio] Falla encarga archivos de nomina
			//echo "<script>document.cookie='QueAccess=".$Access."';</script>";
			echo "<script> var today = new Date(); var expire= new Date(); expire.setTime( today.getTime()+ 3600*24*365 ); document.cookie='QueAccess=".$Access."';expires=expire.toGMTString();path=/;domain=bb.com.mx';</script>";
        }
        if (isset($Accion)) {
            //setcookie ( "QueAccion", $Accion, time() + ( 3600 * 24 * 365 ), "/",  "bb.com.mx", 0 ) or print( "Error: could not set cookie." );
			//echo "<script>document.cookie='QueAccion=".$Accion."';</script>";
			echo "<script>document.cookie='QueAccion=".$Accion.";expires=expire.toGMTString();path=/;domain=bb.com.mx';</script>";
			//NEX -21Ago2012- [fin] Falla encarga archivos de nomina
        }

	 if ( $Agrupa=="Aceptar" )
	 {
			$Access = "FrAc";
			$Accion  = "LibArcPP";
      	  	$DiasPzo = "";
	  	    $particularFields = "&Accion=".urlencode($Accion)."&FrAccount=".urlencode($FrAccount)."&AgruPagIni=".urlencode($AgruPagIni);
      	 	$tr->blog($qki,"LibArcPP", $FrAccount, $ToAccount, $DiasPzo, $Parent1, $Accion);
	  	    $ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=arc&Access=".urlencode($Access)."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // intercambio de archivos de Pago a Proveedores
      	 	$t->set_var( "transaccion_buffer", $transaccion_buffer );
	  	    //$t->pparse( "output", "LibPP_tpl" );
			$transaccion_buffer=$t->parse( "output", "LibPP_tpl" );
			$transaccion_buffer=modSubmit($transaccion_buffer);
			$transaccion_buffer=$procesarAjax.$transaccion_buffer;
	 }
	 else
	 {
        	$DiasPzo = "";
	       	$particularFields = "&Accion=".urlencode($Accion)."&FrAccount=".urlencode($FrAccount)."&AgruPagIni=".urlencode($AgruPagIni);
       		$tr->blog($qki,"LibArcPP", $FrAccount, $ToAccount, $DiasPzo, $Parent1, $Accion);
	   	    $ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=arc&Access=".urlencode($Access)."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // intercambio de archivos de Pago a Proveedores
      	 	$t->set_var( "transaccion_buffer", $transaccion_buffer );
	  	    //$t->pparse( "output", "LibPP_tpl" );
			$transaccion_buffer=$t->parse( "output", "LibPP_tpl" );
			$transaccion_buffer=modSubmit($transaccion_buffer);
			$transaccion_buffer=$procesarAjax.$transaccion_buffer;
	 }
        break;
    case "Confirm":
    case "Process":
        if (isset($Access)) {
            //setcookie ( "QueAccess", $Access, time() + ( 3600 * 24 * 365 ), "/",  "bb.com.mx", 0 ) or print( "Error: could not set cookie." );
			//NEX -21Ago2012- [iniciio] Falla encarga archivos de nomina
			//echo "<script>document.cookie='QueAccess=".$Access."';</script>";
			echo "<script> var today = new Date(); var expire= new Date(); expire.setTime( today.getTime()+ 3600*24*365 ); document.cookie='QueAccess=".$Access.";expires=expire.toGMTString();path=/;domain=bb.com.mx';</script>";			
        }
        if (isset($Accion)) {
            //setcookie ( "QueAccion", $Accion, time() + ( 3600 * 24 * 365 ), "/",  "bb.com.mx", 0 ) or print( "Error: could not set cookie." );
			//echo "<script>document.cookie='QueAccion=".$Accion."';</script>";
			echo "<script>document.cookie='QueAccion=".$Accion.";expires=expire.toGMTString();path=/;domain=bb.com.mx';</script>";
			//NEX -21Ago2012- [fin] Falla encarga archivos de nomina
        }
        //include( "eztransaccion/user/myfileupload.php" );
        list( $servicio, $ciudad, $empresa, $rowno, $nombre_esperado ) = explode("-", $DiasPzo);
		switch( $servicio ) {
			   case "pgprov1" :
			   case "pgprov2" :
					$servicio = "pgprov";
					$Parent1  = "00";
					//$result = valida_pago_proveedores_mismo_banco( $uploadedFile, $nombre_esperado );
					//$uploadedFile->OriginalFileName = $nombre_esperado;
					$mt = "fortiz@bb.com.mx,ablanco@bb.com.mx";
					break;
			  case "pgprov3" :
					$servicio = "pgprov";
					$Parent1  = "00";
					//$result = valida_pago_proveedores_mismo_banco( $uploadedFile, $nombre_esperado );
					//$uploadedFile->OriginalFileName = $nombre_esperado;
					$mt = "fortiz@bb.com.mx,ablanco@bb.com.mx";
					break;
				default:
					$mt = "helpdesk@bb.com.mx";
					break;
		}

        $particularFields = "&Passwd=".encrypt( $code, strtolower( $usr ) )."&Accion=".urlencode($Accion)."&Parent1=".urlencode($Parent1)."&Parent2=".urlencode($Parent2)."&DiasPzo=".urlencode($DiasPzo);
        $tr->blog($qki,"ArchServ", $FrAccount, $ToAccount, $DiasPzo, $Parent1, $Accion);

        $ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=arc&Access=".urlencode($Access)."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // intercambio de archivos de nómina electrónica

        $t->set_var( "transaccion_buffer", $transaccion_buffer );
        //$t->pparse( "output", "LibPP_tpl" );
		$transaccion_buffer=$t->parse( "output", "LibPP_tpl" );
		$transaccion_buffer=modSubmit($transaccion_buffer);
		$transaccion_buffer=$procesarAjax.$transaccion_buffer;

        break;
    }


?>

