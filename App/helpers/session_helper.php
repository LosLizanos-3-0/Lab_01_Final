<?php
	//open session
	session_start();

	//validates if variable of session is setted
	function isLoggedIn(){
		if(isset($_SESSION['usuario'])){
			return true;
		}else{
			return false;
		}
	}

	//escapa texto para mostrarlo de forma segura en HTML
	function e($value){
		return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
	}

	//guarda un mensaje temporal (se muestra una sola vez en la siguiente pagina)
	//$type puede ser: 'ok' o 'error'
	function setFlash($type, $message){
		$_SESSION['flash'] = ['type' => $type, 'message' => $message];
	}

	//lee y borra el mensaje temporal
	function getFlash(){
		if(isset($_SESSION['flash'])){
			$flash = $_SESSION['flash'];
			unset($_SESSION['flash']);
			return $flash;
		}
		return null;
	}
?>