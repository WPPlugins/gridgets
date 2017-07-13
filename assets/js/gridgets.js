(function($) {
	function sizeGridgets(){
		// Find each gridget with full width specified
		//console.log('asd');
		$('.gridgets-full-width').each(function(index){
			// Left offset
			viewportOffset = $(this)[0].getBoundingClientRect().left;
			if(viewportOffset>0){
				$(this).css('left',-viewportOffset);
				$(this).css('width',$(window).width());
			}
		});
	}
	sizeGridgets();
	$(window).resize(function(){
		sizeGridgets();
	});
})(jQuery);
