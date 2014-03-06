/*
	UWAC Javascript
*/



$(document).ready(function(){

	$('.cake-sql-log').addClass('table table-striped');

	var $body = $('body');
	function split( val )
	{
		return val.split( /,\s*/ );
	}
	function extractLast( term )
	{
		return split( term ).pop();
	}

	function injectFormControl ( parent, val, key = null )
	{

		console.log('injecting form control to parent ' + parent.attr('id'))
		if(key == null)
		{
			$input = $('<input type="hidden" name="data[Skill][New][]" />').attr('value', val);
		}
		else
		{
			$input = $('<input type="hidden" name="data[Skill][Skill][]" />').attr('value', key);
		}

		$cancel = $('<a href="#" class="autocomplete-cancel"> </a>').html('&times;').on('click', function(e){ $(this).parent().remove(); return false; });
		$wrapper = $('<span class="autocomplete-wrapper" />').text(val);
		$input.appendTo($wrapper);
		$cancel.prependTo($wrapper);
		$wrapper.appendTo(parent);

	}

	$('.add-address').each(function(){
		var $tgt = $(this).attr('data-target');
		console.log($tgt);
		$(this).on('click', function(e){
			e.preventDefault();

			var data = {
				idx : $('.address').length,
				tgt : $tgt
			};
			console.log( Mustache.render('Adding address {{idx}} to {{tgt}}', data));

			$( Mustache.render(mustaches.address, data) ).appendTo($tgt);

			return false;
		});
	});

	$('.autocomplete.skills').each(function(){
		var $parent = $(this).parent().addClass('ui-widget');

		var $target = $( $(this).attr('data-target') );

		console.log($(this).attr('id') + " has been bound to a skills autocomplete and is appending new Skills to " + $(this).attr('data-target') );

		$(this).bind( "keydown", function( event ){

			if( event.keyCode === $.ui.keyCode.ENTER )
			{
				event.preventDefault();

				if( !$body.hasClass('autocomplete-open') )
				{
					injectFormControl( $target, this.value, null);
					this.value = "";
				}

				return false;
			}
		}).bind('autocompleteopen', function( event ){
			$body.addClass('autocomplete-open').removeClass('autocomplete-closed');
		}).bind('autocompleteclose', function( event ){
			$body.addClass('autocomplete-closed').removeClass('autocomplete-open');
		}).autocomplete({
			delay: 250,
			source: function ( request, response )
			{
				$.ajax(
					{
						url: "/skills/search.json",
						dataType: 'json',
						data: {
							q: extractLast(request.term)
						},
						success: function( data ) {
							response($.map(data.skills, function(value, key){
								return {
									label: value,
									value: value,
									skill_id: key
								};
							}));
						}
					}
				)
			},
			minLength: 2,
			focus: function(){return false;},
			select: function( event, ui ) {

				injectFormControl($target, ui.item.label, ui.item.skill_id);
				this.value = "";
				return false;
			}
		});
	})
});