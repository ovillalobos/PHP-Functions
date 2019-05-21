<?php
include_once( "classes/ezlog.php" );	// JAC NXN 09NOV2012
session_start(); 

function store_in_session($key,$value)
{
	if (isset($_SESSION))
	{
		$_SESSION[$key]=$value;
	}	
}
function unset_session($key)
{
	$_SESSION[$key]="";
	unset($_SESSION[$key]);
}

function get_from_session($key)
{
	if (isset($_SESSION))
	{//echo "getif [$key] ---".$_SESSION[$key]."---<br>";
		return $_SESSION[$key];
	}
	else {  
			return false; } //no session data, no CSRF risk
}



function csrfguard_generate_token($unique_form_name)
{
	if (function_exists("hash_algos") and in_array("sha512",hash_algos()))
	{
		$token=hash("sha512",mt_rand(0,mt_getrandmax()));
	}
	else
	{
		$token="";
		for ($i=0;$i<128;++$i)
		{
			$r=mt_rand(0,35);
			if ($r<26)
			{
				$c=chr(ord('a')+$r);
			}
			else
			{ 
				$c=chr(ord('0')+$r-26);
			} 
			$token.=$c;
		}
	}
	store_in_session($unique_form_name,$token);
	return $token;
}
function csrfguard_validate_token($unique_form_name,$token_value)
{
	$token=get_from_session($unique_form_name);
	//echo"<br>token[".$token."]==token_value[".$token_value."]<br>";
	if ($token===false)
	{
		return true;
	}
	elseif ($token==$token_value)
	{
		$result=true;
	}
	else
	{ 
		$result=false;
	} 
	
	unset_session($unique_form_name);
	return $result;
}

function csrfguard_replace_forms()
{

	$name="CSRFGuard_".mt_rand(0,mt_getrandmax());
	$token=csrfguard_generate_token($name);
	
	// $session->setvariable( "val_csrf", "CSRFGuard_".mt_rand(0,mt_getrandmax()));
	// $session->setvariable( $name, $token);
	
	// if (is_array($matches))
	// {
		// foreach ($matches as $m)
		// {
			// if (strpos($m[1],"nocsrf")!==false) { continue; }
			// $name="CSRFGuard_".mt_rand(0,mt_getrandmax());
			// $token=csrfguard_generate_token($name);
			// $form_data_html=str_replace($m[0],
				// "<form{$m[1]}>
// <input type='hidden' name='CSRFName' value='{$name}' />
// <input type='hidden' name='CSRFToken' value='{$token}' />{$m[2]}</form>",$form_data_html);
		// }
	// }
	//$form_data_html="<input type='hidden' id='CSRFName' name='CSRFName' value='".$session->variable( "val_csrf" )."' /><input type='hidden' id='CSRFToken' name='CSRFToken' value='".$session->variable( "val_csrfTkn" )."' />";
	//$form_data_html="<script>$('#CSRFName').val('$name'); $('#CSRFToken').val('$token'); </script>";//$session->variable( "val_csrf" )
	$form_data_html = "<input type='hidden' id='CSRFName' name='CSRFName' value='$name' ><input type='hidden' id='CSRFToken' name='CSRFToken' value='$token' >";
	return $form_data_html;
}


function csrfguard_inject()
{
	$data=ob_get_clean();
	$data=csrfguard_replace_forms($data);
	echo $data;
}
function csrfguard_start()
{
	$session =& eZSession::globalSession();
	if( !$session->fetch() )
		$session->store();
		
	if (count($_POST))
	{
		if (!isset($_POST['CSRFName']))
		{
			//eZLog::writeNotice( "...............csrfguard_start()... NO SE ENCUENTRA EL PARAMETRO CSRFName ...");  MAOS Oct2013 Quitar Notice
			echo "Error mensaje invalido fuera de tiempo. ";	// NXN 09NOV2012
			return false;
		} 
		$name =$_POST['CSRFName'];
		$token=$_POST['CSRFToken'];

		if (!csrfguard_validate_token($name, $token))
		{ 
			//eZLog::writeNotice( "...............csrfguard_start()... TOKEN INVALIDO PARA OPERAR [name:$name token:$token] ...");  MAOS Oct2013 Quitar Notice
			echo "Error mensaje fuera de tiempo. "; // NXN 09NOV2012
			return false;
		}
	}
	ob_start();
	//register_shutdown_function(csrfguard_inject);	
	register_shutdown_function(csrfguard_replace_forms);	
	return true;
}

$session =& eZSession::globalSession( );
if( !$session->fetch( ) )
{$session->store( );}
$ini =& $GLOBALS[ "GlobalSiteIni" ];
if ($session->variable( "val_csrfOn" )=="" && $ini->read_var( "site", "CSRFValidate" )=="si")
{	
	$session->setvariable( "val_csrfOn","CSRFGuard_".mt_rand(0,mt_getrandmax()));
	$_SESSION[$session->variable( "val_csrfOn" )] = csrfguard_generate_token($session->variable( "val_csrfOn" ));
	csrfguard_start();
}

?>