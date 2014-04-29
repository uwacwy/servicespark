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

	function injectFormControl ( parent, val, key )
	{
		if( key == null )
		{
			$input = $('<input type="hidden" name="data[Skill][New][]" />').attr('value', val);
		}
		else
		{
			$input = $('<input type="hidden" name="data[Skill][Skill][]" />').attr('value', key);
		}

		$cancel = $('<a href="#" class="autocomplete-cancel"> </a>').html('&times;');
		$wrapper = $('<span class="autocomplete-wrapper" />').text(val);
		$input.appendTo($wrapper);
		$cancel.prependTo($wrapper);
		$wrapper.appendTo(parent);

	}

	$('input.username').each(function(){
		var $this = $(this);

		$this.parent().append( $("<p><span class=\"help-block\"></span></p>") );

		$this.keyup(function(){

			var state = {
				message: "Type a username to see if it's available.",
				css_class: "form-group"
			};
			
			if($this.val().length == 0 )
			{
				$this.parent().attr('class', state.css_class);
				$this.parent().find('p .help-block').text(state.message);
				return; 

			}

			$.ajax(
				{
					url: "/users/check/",
					dataType: 'json',
					type: 'get',
					data: {
						username: $this.val()
					},
					success: function( data ) {
						if( data.valid )
						{
							state = {
								message: "This username is available!",
								css_class: "form-group has-success"
							};
						}
						else
						{
							state = {
								message: "This username is taken.  Please try another.",
								css_class: "form-group has-error"
							}
						}

						$this.parent().attr('class', state.css_class);
						$this.parent().find('p .help-block').text(state.message);
					}
				}
			);

		});
	});

	$('.add-address').each(function(){
		var $tgt = $(this).attr('data-target');
		$(this).on('click', function(e){
			e.preventDefault();

			var data = {
				idx : $('.address').length,
				tgt : $tgt
			};

			$( Mustache.render(mustaches.address, data) ).prependTo($tgt);

			return false;
		});
	});

	$('.address-container').on( 'click', '.remove-address', function(e)
		{
			e.preventDefault();

			// jQuery will delete the node before the slide toggle completes unless you use a callback
			$(this).parent().parent().parent().slideToggle( function(){ $(this).remove(); } );

			return false;
		}
	);

	$('.autocomplete.skills').each(function(){
		var $parent = $(this).parent().addClass('ui-widget');

		var $target = $( $(this).attr('data-target') );

		$target.delegate('.autocomplete-cancel', 'click', function(e) {
			e.preventDefault();
			$( this ).parent().fadeOut('fast', function(){$(this).remove();})
			return false;
		});

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
			delay: 125,
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