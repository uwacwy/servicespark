<div class="row">
	<div class="col-md-12">
		<h1><small>Coordinator</small><br>Organizations </h1>
		<p>You can coordinate events for these organizations.</p>
		<?php if ( !empty($pag_organizations) ) : ?>
		
		<?php foreach( $pag_organizations as $organization ) : ?>
			<div class="actionable">
				<div class="situation">
					<h4><?php echo h( $organization['Organization']['name'] ); ?></h4>
					<?php if( !empty( $organization['Organization']['description']) )
						echo $this->Html->tag('p', $organization['Organization']['description']); ?>
					
					<div class="stat-bar">
					
						<div class="stat">
							<span class="key"><?php echo __("Upcoming Events"); ?></span>
							<span class="value"><?php echo number_format( count($organization['Event']) ); ?></span>
						</div>
						
						<div class="stat">
							<span class="key"><?php echo __("Total Members"); ?></span>
							<span class="value"><?php echo number_format( count($organization['Permission']) ); ?></span>
						</div>
						
					</div>
					
				</div>
				<div class="actions">
					<ul>
						<li><?php echo $this->Html->link(
								__('Dashboard'),
								array('coordinator' => true, 'controller' => 'organizations', 'action' => 'dashboard', $organization['Organization']['organization_id']),
								array('class' => ' text-success')
							); ?></li>
						<li><?php echo $this->Html->link(
								__('Edit Addresses and Members'), 
								array('action' => 'edit', $organization['Organization']['organization_id']), 
								array('class' => '')
							); ?></li>

					</ul>
				</div>
			</div>
		<?php endforeach; ?>

	<?php else: ?>
		<p><em>You are not a coordinator for any organizations.</em></p>
	<?php endif; ?>
	</div>
</div>