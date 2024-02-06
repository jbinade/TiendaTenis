<?php
//Inicio la sesión 
session_start();
//COMPRUEBA QUE EL USUARIO ESTA AUTENTIFICADO
if ($_SESSION["autenticado"] != true) {
    //si no existe, envío a la página de autentificación 
    header("Location: index.php?zona=1");
//ademas salgo de este script
    exit();
}

?>