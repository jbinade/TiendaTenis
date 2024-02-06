function menuDesplegable(categoria, button) {
  var menu = document.getElementById(categoria + '-menu');
  menu.style.display = (menu.style.display === 'block') ? 'none' : 'block';
  button.textContent = (menu.style.display === 'block') ? categoria.charAt(0).toUpperCase() + categoria.slice(1) + ' -' : categoria.charAt(0).toUpperCase() + categoria.slice(1) + ' +';
    
}



