<div class="row">
	<div class="col-md-3">
		<?php echo $this->Html->link(__('Back to Events'), array('action' => 'index'), array('class' => 'btn btn-primary btn-lg btn-block')); ?>
		<?php echo $this->Html->link( __('Register for %s', Configure::read('Solution.name')), array('controller' => 'users', 'action' => 'register'), array('class' => 'btn btn-default btn-lg btn-block') ); ?>
	</div>
	<div class="col-md-9">
			<h1>
				<small><?php echo $event['Organization']['name']; ?></small><br><?php echo h($event['Event']['title']); ?>
				<small><?php echo $this->Duration->format($event['Event']['start_time'], $event['Event']['stop_time']); ?></small>
			</h1>
			<blockquote><?php echo h($event['Event']['description']); ?></blockquote>
			<?php if (!empty($event['Skill']) ) : ?>
				<h3>Skills</h3>
				<p class="lead">
					<?php foreach ($event['Skill'] as $skill) : ?>
						<?php
							echo $this->Html->tag('span', $skill['skill'], array('class' => 'label label-info', 'title' => __('If you enjoy %s, consider volunteering for this event.', $skill['skill']) ) );
							echo ' ';
						?>
					<?php endforeach; ?>
				</p>
			<?php endif; ?>

		<?php
			if( !empty($event['Address']) ) 
			{
				echo "<h3>Event Addresses</h3>";
				echo '<div class="row">';
				foreach( $event['Address'] as $address )
				{
					echo '<address class="col-md-3">';
					switch($address['type'])
					{
						case 'physical':
							echo '<strong>Physical Address</strong><br>';
							break;
						case 'mailing':
							echo '<strong>Mailing Address</strong><br>';
							break;
						case 'both':
							echo '<strong>Physical/Mailing Address</strong><br>';
							break;
					}
					echo $address['address1'] . ' <br>';
					if($address['address2'] != null)
					{ 
						echo $address['address2'] . ' <br>';
					}
					echo $address['city'] . ', ' . $address['state'] . '  ' . $address['zip'];
					if( $address['type'] != 'mailing' )
					{
						echo '<br>'. $this->Html->link(
							__('Map'),
							sprintf(
								'https://maps.google.com/?q=%s',
								urlencode(
									sprintf(
										'%s, %s, %s, %s, %s',
										$address['address1'],
										$address['address2'],
										$address['city'],
										$address['state'],
										$address['zip']
									)
								)
							),
							array('target' => '_blank')
						);
					}
					echo '</address>';
				}
				echo '</div>';
			}

		?>
	</div>
</div>