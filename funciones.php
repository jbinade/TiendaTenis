<?php

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
  // Redirigir a index.php
  header("Location: index.php");
  exit; // Asegura que el script se detenga después de la redirección
}

?>


<script>
  
    function menuDesplegable(categoria, button) {
      var menu = document.getElementById(categoria + '-menu');
      menu.style.display = (menu.style.display === 'block') ? 'none' : 'block';
      button.textContent = (menu.style.display === 'block') ? categoria.charAt(0).toUpperCase() + categoria.slice(1) + ' -' : categoria.charAt(0).toUpperCase() + categoria.slice(1) + ' +';
        
    }

</script>


