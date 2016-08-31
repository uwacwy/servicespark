/*
	UWAC Javascript
*/
var $body = $('body'),
	$document= $(document);
	
(function($) {
    $.fn.countTo = function(options) {
        // merge the default plugin settings with the custom options
        options = $.extend({}, $.fn.countTo.defaults, options || {});

        // how many times to update the value, and how much to increment the value on each update
        var loops = Math.ceil(options.speed / options.refreshInterval),
            increment = (options.to - options.from) / loops;

        return $(this).each(function() {
            var _this = this,
                loopCount = 0,
                value = options.from,
                interval = setInterval(updateTimer, options.refreshInterval),
                formatter = options.format || value.toLocaleString;

            function updateTimer() {
                value += increment;
                loopCount++;
                $(_this).html(formatter(value, options.decimals));

                if (typeof(options.onUpdate) == 'function') {
                    options.onUpdate.call(_this, value);
                }

                if (loopCount >= loops) {
                    clearInterval(interval);
                    value = options.to;

                    if (typeof(options.onComplete) == 'function') {
                        options.onComplete.call(_this, value);
                    }
                }
            }
        });
    };

    $.fn.countTo.defaults = {
        from: 0,  // the number the element should start at
        to: 100,  // the number the element should end at
        speed: 1000,  // how long it should take to count between the target numbers
        refreshInterval: 100,  // how often the element should be updated
        decimals: 0,  // the number of decimal places to show
        onUpdate: null,  // callback method for every time the element is updated,
        onComplete: null,  // callback method for when the element finishes updating
        format: Number.toLocaleString
    };
})(jQuery);

var SkillPicker = function($skill_picker)
{
	this.validSortBy = ['created', 'modified', 'skill', 'event_count', 'user_count'];
	
	this.setView = function(newSortBy)
	{
		if( this.validSortBy.indexOf(newSortBy) === -1 )
			newSortBy = 'event_count';
			
		this.sortBy = newSortBy;
	}
};

var getSkills = function(opts, onSuccess, onError)
{
	var $this = this;
	$this.done = false;
	
	opts = $.extend({
		'order_by' : "user_count",
		'count' : 15,
		'exclude_user_skills' : false
	}, opts);
	
	var result = "hello";
	
	$.ajax({
		'type' : 'GET',
		'url' : environment.site_root + 'api/skills/popular',
		'data' : opts,
		'success' : onSuccess,
		'error' : onError
	});
	
};



$document.ready(function()
{
	var $notifications = $('.ss-notifications').trigger('refresh');
	
	if( environment.pubnub )
	{
		var pn = PUBNUB.init({
			subscribe_key: environment.pubnub.subscribe_key
		});
		
		pn.subscribe({
			channel: environment.pubnub.channel,
			message: function(m){
				$.playSound(environment.site_root + "js/sound/blop");
				$notifications.trigger('refresh');
			}
		});

	}

	$('.cake-sql-log').addClass('table table-striped');

	$('.comment .comment-reply').hide();
	
	$('[data-toggle="tooltip"]').tooltip();

	$('.comments').on('click', '.comment-reply-trigger', function(e){
		e.preventDefault();
		$(this).toggleClass('on off').parent().find(".comment-reply").slideToggle();
		console.log('displaying comment form');
		return false;
	});
	$('body').on('change cut paste drop keydown', '.reply-body', function(e){
		$(this).height(0).height( $(this).get(0).scrollHeight + 20);
	});
	
	$('.trianglify').each(function(idx, item){
		var $item = $(item);
		var seed = $item.data('seed') || null;
		var pattern = Trianglify({
			height: $item.outerHeight(),
			width: $item.outerWidth(),
			cell_size: 40,
			seed: seed
		});
		$item.css('background-image', 'url('+ pattern.png() + ')' );
	})
	
	if( window.location.hash )
	{
		var re = /^#!([\w-]*)\/([\w-]*)$/i; // valid-selector-here/valid-subselector-here
		var info = window.location.hash.match(re);
		
		if( info.length == 3 )
		{
			$('a[data-toggle="tab"]').one('shown.bs.tab', function (e)
			{
				$('html, body').animate({
					scrollTop: $("#" + info[2]).offset().top
				}, 1000);
			});
		
			$('a[href=#'+info[1]+']').tab('show');
		}
	}
	
	
	
	$('body').on('click', '.skill-picker .category-toggle', function(e)
	{
		e.preventDefault();
		var $toggle = $(this),
			order_by = $toggle.data('order-by'),
			skills = {};
			
		getSkills(
			{
				'order_by' : order_by
			},
			function(data)
			{
				$available_skills = $toggle.closest('.skill-picker').find('.available-skills ul');
				$available_skills.empty();
				$.each(data, function(skill_id, skill){
					
					$('<a />')
						.attr('href', '#')
						.addClass('skill-add')
						.text(skill)
						.attr('id', 'skill-'+skill_id)
						.attr('data-skill-id', skill_id)
						.appendTo($available_skills)
						.wrap('<li />');
				})
			},
			function(data)
			{
				console.log("error!");
			}
		);
	})
	.on('click', '.event.skill-picker .skill-add', function(e){
		var $this = $(this);
		var $chosenSkills = $('.chosen-skills ul');
		e.preventDefault();
		console.log('skill adder clicked');
		var $input = $("<input />")
			.attr('type', 'hidden')
			.attr('name', 'data[EventSkill][' + $chosenSkills.find('li').length + "][skill_id]")
			.attr('value', $this.data('skill-id') );
			
		$("<li />")
			.append( $input )
			.append( $this.toggleClass('skill-add skill-remove').detach() )
			.appendTo($chosenSkills);
	})
	.on('click', 'a.notification', function(e){
			
		$.ajax({
			type: "POST",
			url: $(this).attr('data-api'),
			dataType: 'json'
		});
		
		return true; // the link should still follow
	});
	
	$('.skill-picker').each(function(index, picker)
	{
		var $skill_picker = $(picker);
		
		$skill_picker.attr('id', 'skill-picker-' + index);
		
		
		
	});
	
	$('.animate-count').each(function(index, value)
	{
		$item = $(this);
		
		$item.countTo({
			from: $item.data('count-from'),
			to: $item.data('count-to'),
			speed: $item.data('count-speed'),
			refreshInterval: 50,
			format: function(value)
			{
				return numeral(value).format('0,0');
			}
		});
	});
	
	window.setTimeout(function() {
		$('.toast').fadeOut('fast');
	}, 2500);
	
	$('.append-username').each(function(idx){
		console.log('username found');
		var $appender = $(this);
		
		$appender.on('click', function(e){
			e.preventDefault();
			
			$('.reply-body').each(function(j){
				var $cmtBlock = $(this);
				var newComment = $cmtBlock.val() + " @" + $appender.text();
				$cmtBlock.val( newComment.trim() + " " );
				$cmtBlock.focus();
			});
			
			return false;
		});
	});
	
	$('.cloth').on('Container.child_removing', function(e)
	{
		var
			$container = $(this),
			$children = $container.children();
		
		if($children.length === 1)
		{
			// attempt to load more
			// $.ajax({
			// 	type: "GET",
			// 	url: $container.attr('data-api'),
			// 	dataType: 'JSON',
			// 	success: function(r)
			// 	{
			// 		console.log( "Removed" );
			// 		console.log(r);
			// 	}
			// });
			
			//if nothing from load,
			$('<em />').text( $container.attr('data-api-empty') ).appendTo($container);
			
		}
	});
	
	
	$('.api-trigger-time-reject').each(function(){
		var $this = $(this),
			action = $this.attr('data-api'),
			prompt = $this.attr('data-prompt'),
			on_success = $this.attr('data-on-success'),
			target = $this.attr('data-target');
			
		$this.on('click', function(e){
			e.preventDefault();
			
			bootbox.prompt( prompt, function(reason){
				$.ajax({
					type: "POST",
					url: action,
					dataType: 'JSON',
					data: {
						'data[TimeComment][body]' : reason
					},
					success: function(r)
					{
						if( on_success === 'collapse' )
						{
							$(target).slideUp().trigger('Container.child_removing').remove();
						}
						
						if( on_success === 'toggle_parent_class' )
						{
							$this.parent().toggleClass( $this.attr('data-toggle-class') );
						}
						
						$('<div />')
							.addClass('toast')
							.text(r.response.message)
							.appendTo($body)
							.delay(2500)
							.fadeOut('fast', function(){
								$(this).remove()
							});
					}
				});
			});
			return false;
		});
	});
	
	
	$('.api-trigger').each(function(){
		var $this = $(this),
			action = $this.attr('data-api'),
			on_success = $this.attr('data-on-success'),
			target = $this.attr('data-target');
			
		$this.on('click', function(e){
			e.preventDefault();
			$.ajax({
				type: "POST",
				url: action,
				dataType: 'JSON',
				success: function(r)
				{
					
					if( on_success === 'collapse' )
					{
						$(target).slideUp().trigger('Container.child_removing').remove();
					}
					
					if( on_success === 'toggle_parent_class' )
					{
						$this.parent().toggleClass( $this.attr('data-toggle-class') );
					}
					
					console.log(r);
					
					$('<div />')
						.addClass('toast')
						.text(r.response.message)
						.appendTo($body)
						.delay(2500)
						.fadeOut('fast', function(){
							$(this).remove()
						});
				}
			});
			return false;
		});
	});
		

	
	$('.rsvp-button').on('click', function(e)
	{
		if( typeof(ga) == "function")
		{
			e.preventDefault();
			
			$button = $(this);
			
			ga('send', {
					'hitType': 'event',          // Required.
					'eventCategory': 'RSVP',   // Required.
					'eventAction': $button.text(),      // Required
					'eventValue': $button.attr('href'),
					'hitCallback': function () {
						document.location = $button.attr('href');
					}
			});
			
			return false;
		}
		
		return true;
	});

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

		$this.on("keyup blur", function(){

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
					url: environment.site_root + "/users/check/",
					dataType: 'json',
					type: 'get',
					data: {
						username: $this.val()
					},
					success: function( data ) {
						if( data.valid == true )
						{
							state = {
								message: "This username is available!",
								css_class: "form-group has-success"
							};
						}
						else
						{
							state = {
								message: data.valid[0],
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
						url: environment.site_root + "/skills/search.json",
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