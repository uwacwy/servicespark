<div class="_table">
	<div class="_thead">
		<div class="_tr">
			<div class="_th"><?php echo __("Volunteer"); ?></div>
			<div class="_th"><?php echo __("Username"); ?></div>
		</div>
	</div>
	<div class="_tbody">
		<?php foreach($rsvps as $rsvp) : ?>
			<?php
				switch($rsvp['Rsvp']['status'])
				{
					case 'going':
						$class = '_success';
						break;
						
					case 'maybe':
						$class = '_warning';
						break;
						
					case 'not_going':
						$class = '_danger';
						break;
				}
			?>
			<div class="_tr <?php echo $class; ?>">
				<div class="_td"><?php echo h($rsvp['User']['full_name']); ?></div>
				<div class="_td"><?php echo h($rsvp['User']['username']); ?></div>
			</div>
		<?php endforeach; ?>
	</div>

</div>