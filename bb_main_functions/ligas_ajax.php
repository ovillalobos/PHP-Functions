<?php

($parametros['Listado']!=""?$Listado=$parametros['Listado']:0);
($parametros['Access']!=""?$Access=$parametros['Access']:0);
($parametros['CustID']!=""?$CustID=$parametros['CustID']:0);
($parametros['Sub']!=""?$Sub=$parametros['Sub']:0);
($parametros['Accion']!=""?$Accion=$parametros['Accion']:0);
($parametros['Cust']!=""?$Cust=$parametros['Cust']:0);
($parametros['Cuenta']!=""?$Cuenta=$parametros['Cuenta']:0);
($parametros['Pos']!=""?$Pos=$parametros['Pos']:0);
($parametros['code']!=""?$code=$parametros['code']:0);      //Abr09 I Corrección validación token, por uso de ajax

//return "<script>alert('$Listado');</script>";
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "ligas.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "ligas_tpl" => "ligas.tpl"
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
    if(empty($Access)) {
        $Access = "FrAc";
    }
    switch($Access) {
    case "FrAc":
        $particularFields = "";
        $tr->blog($qki,"Ligas", $FrAccount, $Cust, $Cuenta, $Sub, $Accion);
        break;
    case "Confirm":
    case "Process":
	//JAG 26abr2011 inicio
		$_SESSION["cambioCheqsi"] = 1;
	//JAG 26abr2011 fin
        if($Listado=="Consulta de Ligas") {
            $Accion = "Listajnt";
        }
        $particularFields = "&Passwd=".encrypt( $code, strtolower( $usr ) )."&Cust=".urlencode($Cust)."&Cuenta=".urlencode($Cuenta)."&Sub=".urlencode($Sub)."&Accion=".urlencode($Accion)."&Pos=".urlencode($Pos);
        $tr->blog($qki,"Ligas", $FrAccount, $Cust, $Cuenta, $Sub, $Accion);
        break;
    }
    $transaccion_buffer = "";
    // DebugBreak();
    $ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=jnt&Access=".urlencode($Access)."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // ligas de cuentas de terceros
	$t->set_var( "transaccion_buffer", $transaccion_buffer );


    $transaccion_buffer=$t->parse( "output", "ligas_tpl" );

	$transaccion_buffer=str_replace("TYPE=SUBMIT","type='button' onclick=\"javascript:procesarAjax(this);\"",$transaccion_buffer);
	$transaccion_buffer=str_replace("type=\"submit\"","type='button' onclick=\"javascript:procesarAjax(this);\"",$transaccion_buffer);
	$transaccion_buffer=str_replace("parent.history.back()","procesarAjax(this);",$transaccion_buffer);
	$transaccion_buffer=str_replace("TYPE=\"PASSWORD\""," TYPE=\"PASSWORD\" onKeyPress=\"if (isEnter(event) == true) { procesarAjax(this); }\"", $transaccion_buffer);
	$transaccion_buffer=str_replace("/transaccion/transferencia\"","#\" onclick=\"javascript:iniProcesarMenu('transferencia', '');\"", $transaccion_buffer);
	//echo $transaccion_buffer;
	$transaccion_buffer="<script>

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

							function procesarAjax(boton)
							{
								if (boton.value=='Cambiar')
								{
									var parametros=	'';
								}
								else
								{
									var parametros=	'Access:'+document.getElementsByName('Access')[0].value+
//Abr09 I Corrección validación token, por uso de ajax
													',CustID:'+document.getElementsByName('CustID')[0].value;
									if(document.getElementsByName('Accion')[0]!=null)
									{
										if (document.getElementsByName('Accion')[0].type!='HIDDEN' && document.getElementsByName('Accion')[0].type!='hidden')
										{
											if (document.getElementsByName('Accion')[0].checked)
											{
												parametros+=',Accion:'+document.getElementsByName('Accion')[0].value;
											}
											else
											{
												parametros+=',Accion:'+document.getElementsByName('Accion')[1].value;
											}
										}
										else
										{
											parametros+=',Accion:'+document.getElementsByName('Accion')[0].value;
										}
									}
//Abr09 F Corrección validación token, por uso de ajax

									if(document.getElementsByName('Sub')[0]!=null)
									{
										parametros+=',Sub:'+document.getElementsByName('Sub')[0].value;
									}
									if(document.getElementsByName('Cust')[0]!=null)
									{
										parametros+=',Cust:'+document.getElementsByName('Cust')[0].value;
									}
									if(document.getElementsByName('Listado')[0]!=null)
									{
										if(boton.name=='Listado')
										{
											parametros+=',Listado:'+document.getElementsByName('Listado')[0].value;
										}
									}
									if(document.getElementsByName('Cuenta')[0]!=null)
									{
										if (document.getElementsByName('Cuenta')[0].type!='HIDDEN' && document.getElementsByName('Cuenta')[0].type!='hidden')
										{
											parametros+=',Cuenta:'+document.getElementsByName('Cuenta')[0].options[document.getElementsByName('Cuenta')[0].selectedIndex].value;
										}
										else
										{
											parametros+=',Cuenta:'+document.getElementsByName('Cuenta')[0].value;
										}
									}
									if(document.getElementsByName('Pos')[0]!=null)
									{
										parametros+=',Pos:'+document.getElementsByName('Pos')[0].value;
									}
//Abr09 I Corrección validación token, por uso de ajax

									if(document.getElementsByName('code')[0]!=null)
									{
										parametros+=',code:'+document.getElementsByName('code')[0].value;
									}
//Abr09 F Corrección validación token, por uso de ajax

								}

								//alert(parametros);

								iniProcesarMenu('ligas', parametros);

							}
						</script>".$transaccion_buffer;

	//$t->pparse( "output", "ligas_tpl" );

?>