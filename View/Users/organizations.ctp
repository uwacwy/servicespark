<div class="row">
	<div class="col-md-12">
		<h2>My Organizations</h2>
		<p class="text-muted">You are connected to these organizations.  You may leave any organization in any capacity at any time if you please.</p>
	</div>
	<div class="col-md-4">
		<h3>Publishing to...</h3>
		<?php if( !empty($publishing) ) : ?>
		<p>Your volunteering activity may be viewed by supervisors of these organizations.</p>
		<table class="table">
			<thead>
				<tr>
					<th>Organization</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($publishing as $org) : ?>
				<tr>
					<td><?php echo h($org['Organization']['name']); ?></td>
					<td><?php 
						echo $this->Html->link(
							__('Leave'),
							array(
								'volunteer' => true, 
								'controller' => 'organizations', 
								'action' => 'membership',
								'publish',
								'off',
								$org['Organization']['organization_id'],
								'?' => array(
									'redirect_to' => $this->here
								)
							),
							array('class' => 'btn btn-danger btn-xs'),
							__('Are you sure you want to leave this organization?')
						);
					?></td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php else : ?>
		<p><em>You are not publishing activity to any organizations</em></p>
		<?php endif; ?>
	</div>
	<div class="col-md-4">
		<h3>Supervising</h3>
		<?php if( !empty($supervising) ) : ?>
		<p>You can view aggregate time data for volunteers that publish to the following organizations.</p>
		<table class="table">
			<thead>
				<tr>
					<th>Organization</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($supervising as $org) : ?>
				<tr>
					<td><?php echo h($org['Organization']['name']); ?></td>
					<td><?php
							echo $this->Html->link(
								__('Supervise'),
								array('supervisor' => true, 'controller' => 'organizations', 'action' => 'dashboard', $org['Organization']['organization_id']),
								array('class' => 'btn btn-primary btn-xs')
							);
							echo " ";
							echo $this->Html->link(
								__('Leave'),
								array('supervisor' => true, 'controller' => 'organizations', 'action' => 'leave',  $org['Organization']['organization_id']),
								array('class' => 'btn btn-danger btn-xs'),
								__('Are you sure you want to stop publishing your activity to this organization?')
							); 
					?></td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php else : ?>
		<p><em>You are not supervising any organizations</em></p>
		<?php endif; ?>
	</div>
	<div class="col-md-4">
		<h3>Coordinating</h3>
		<?php if( !empty($coordinating) ) : ?>
		<p>You can create, manage, and supervise events for these organizations..</p>
		<table class="table">
			<thead>
				<tr>
					<th>Organization</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($coordinating as $org) : ?>
				<tr>
					<td><?php echo h($org['Organization']['name']); ?></td>
					<td><?php 
						echo $this->Html->link(
							__('Coordinate'),
							array('coordinator' => true, 'controller' => 'organizations', 'action' => 'dashboard', $org['Organization']['organization_id']),
							array('class' => 'btn btn-primary btn-xs')
						);
						echo " ";echo $this->Html->link(
							__('Leave'),
							array('coordinator' => true, 'controller' => 'organizations', 'action' => 'leave', $org['Organization']['organization_id']),
							array('class' => 'btn btn-danger btn-xs'),
							__('Are you sure you want to leave this organization?')
						);
					?></td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php else : ?>
		<p><em>You are not coordinating any organizations</em></p>
		<?php endif; ?>
	</div>
</div>