<script>
var excluded = [];
var count = 15;
var no_more_skills = false;
function getSkills(ct, container)
{
	var $skill_container = $(container);
	$.ajax({
		'type' : 'GET',
		'url' : environment.site_root + 'api/skills/popular',
		'data' : {
			'count': ct,
			'order_by': 'event_count',
			'excluded_skill_ids': excluded.join(','),
			'exclude_user_skills': true
		},
		'success' : function(data)
		{
			$.each(data, function(skill_id, skill)
			{
				excluded.push(skill_id.toString() );
				
				var $situation = $('<div />')
					.addClass('situation')
					.text(skill);
					
				var $actions = $('<div />')
					.addClass('actions')
					.html( '<ul><li><a class="text-success skill-add" href="#">Add To Profile</a></li><li><a href="#" class="text-danger skill-exclude">Skip</a></li></ul>');
					
					
				$('<div />')
					.addClass('actionable')
					.addClass('skill')
					.attr('data-skill-id', skill_id)
					.append($situation)
					.append($actions)
					.appendTo($skill_container);
			});
			
			if( data.length < ct && !no_more_skills )
			{
				$('<p />')
				.text("There are no more skills to show you.  If you skipped a skill, it isn't gone forever.  Refresh the page to reconsider.")
				.appendTo($skill_container);
				
				no_more_skills = true;
			}
		},
		'error' : function(data)
		{
			alert("An error has occurred.  Try refreshing the page!");
		}
	});

}
$(document).ready(function(){
	
	var $skill_container = $('.skill-picker');
	
	getSkills(15, $skill_container);
	
	$('body')
	.on('click swiperight', '.user.skill-picker .skill-add', function(e){
		e.preventDefault();
		
		var $trigger = $(this);
		var $parent = $trigger.closest('.actionable.skill');
		var skill_id = $parent.data('skill-id');
		
		console.log(skill_id);
		
		$.ajax({
			url: environment.site_root + 'api/users/skill_attach',
			type: 'POST',
			data: {
				'skill_id' : skill_id
			},
			success: function(data)
			{
				if(data.attached)
					$parent.slideUp(500, function(){
						$(this).remove();
						getSkills(1, $skill_container);
					});
				else
					alert("There was a problem with your request.  Try reloading the page.");
			}
		});
		
		return false;
	})
	.on('click', '.skill-exclude', function(e){
		e.preventDefault();
		
		var $trigger = $(this);
		var $parent = $trigger.closest('.actionable.skill');
		var skill_id = $parent.data('skill-id');
		
		$parent.slideUp(200, function(){
			$(this).remove();
			getSkills(1, $skill_container);
		});
		
		return false;
	});
});
</script>

<div class="text-center">
<h2>Skill Picker</h2>
<p class="lead text-muted"><?php echo __("Click a skill to add it to your profile."); ?></p>
<div class="skill-picker user">
	
</div>