<?php
$var = $_POST['parametro'];

$buscando = "images/bajiosecure/pagoserv/".$var . ".jpg";

		
		  if (  file_exists($buscando) == true )
		{
			
			echo "si";
			
		}else
		{	
			echo "no";
		}

?>