jQuery(function($) {

	$(document).on('click', '#collapse-button', function() {
		$('body').toggleClass('folded');
	});

	$(document).on('mouseenter mouseover', '#adminmenuwrap li.wp-has-submenu', function() {
		var windowTop = $(window).scrollTop();
		var windowBottom = windowTop + $(window).height();
		var submenu = $(this).find('.wp-submenu');
		submenu.css('margin-top','');
		var offset = submenu.offset();
		var submenuBottom = offset.top + submenu.height();
		if( submenuBottom>windowBottom ) {
			submenu.css('margin-top', (-1*(submenuBottom-windowBottom))+'px');
		}
	});
	
});