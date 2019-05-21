<?php
($parametros['Access']!=""?$Access=ltrim(rtrim($parametros['Access'])):0);
($parametros['CustID']!=""?$usr=$usr=ltrim(rtrim($parametros['CustID'])):0);
($parametros['Cadpriv']!=""?$priv=ltrim(rtrim($parametros['Cadpriv'])):0);
($parametros['Passwd']!=""?$Passwd=ltrim(rtrim($parametros['Passwd'])):0);



function modSubmit ($submit)
{
	$submit=str_replace("javascript:{ document.getElementById( 'OK' ).disabled = true; document.forms[ 0 ].submit(); }","procesarAjax()",$submit);
	$submit=str_replace("TYPE=SUBMIT", "type=\"button\"",$submit);
	$submit=str_replace("TYPE=\"SUBMIT\"","TYPE=\"BUTTON\"", $submit);
	$submit=str_replace("type=\"submit\"","type=\"button\"",$submit);
	$submit=str_replace("type=submit","type=\"button\"",$submit);
	$submit=str_replace("javascript:{ if ( ( document.getElementById( 'Passwd' ).value.length == 10 ) ) document.getElementById( 'OK' ).disabled = false; else document.getElementById( 'OK' ).disabled = true; }","javascript:{ if (window.event){ sueltaTecla(window.event); } else { sueltaTecla(event);}} \" onkeypress=\" if (isEnter(event) == true) { return false; }", $submit);
	return $submit;
}

$procesarAjax="<script>

						function sueltaTecla (e)
						{
							
							if( ( document.getElementsByName( 'Passwd' )[0].value.length == 10 ) ) 
							{
							
								document.getElementById( 'OK' ).disabled = false;
								
								if (isEnter(e) == true) 
								{ 
									procesarAjax(); 
								} 
							} 
							else 
							{
								document.getElementById( 'OK' ).disabled = true;
							}
							return true;
														
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
							
							if(document.getElementsByName('Passwd')[0]!=null)
							{
								parametros+=',Passwd:'+document.getElementsByName('Passwd')[0].value;
							}
							
							if(document.getElementsByName('Cadpriv')[0]!=null)
							{
								parametros+=',Cadpriv:'+document.getElementsByName('Cadpriv')[0].value;
							}
							
							
							iniProcesarMenu('testasb', parametros);
						}
						
											
						
						
						
						
						
				</script>";



	$t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ), "eztransaccion/user/intl/", $Language, "testasb.php" );

	$t->setAllStrings();

	$t->set_file( array( "testasb_tpl" => "testasb.tpl" ) );

	
	if ( ! isset( $Access ) )
	{
		$Access = "FrAc";
	}
	switch( $Access )
	{
		case "FrAc":
			$particularFields = "";
			$tr->blog( $qki,"TestASB", "", "", "", "", "" );
			break;
		case "Confirm":
		case "Process":
			$Passwd = encrypt( $Passwd, strtolower( trim( $usr ) ) );
			$particularFields = "&Passwd=".urlencode( $Passwd );
			$tr->blog( $qki,"TestASB", $Passwd );
			break;
	}
	
	$transaccion_buffer = "";
	$ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=ttk&Access=".urlencode($Access)."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // Testasb
	$tr->blog($qki,"Testasb", "", "", "", "", "");
	$t->set_var( "transaccion_buffer", $transaccion_buffer );
	$transaccion_buffer=modSubmit($t->parse( "output", "testasb_tpl" ));
	$transaccion_buffer=$procesarAjax.$transaccion_buffer;


?>