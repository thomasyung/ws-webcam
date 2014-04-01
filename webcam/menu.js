function menu_init() {	
	var menu = document.querySelector('#menu');
	var menu_toggle = document.querySelector('#menu_toggle');
	var menu_items = document.querySelectorAll('.menu_item');
	menu_toggle.addEventListener('click', function(e){
		if (menu.className == 'menu_toggle_close'){
			menu.className = 'menu_toggle_open';
			menu_toggle.innerHTML = 'Menu <div class="arrow-up"></div>';
		} else {
			menu.className = 'menu_toggle_close';
			menu_toggle.innerHTML = 'Menu <div class="arrow-down"></div>';
		}
	}, true);
	for(var i=0; i<menu_items.length; i++) {
		menu_items[i].addEventListener('click', function(e){
			location.href = e.target.firstChild.href;
		}, true);
	}
	
}