<div class="row">
	<div class="col-md-12">

		<h2>Organizations</h2>
		
		<?php if( !empty($organizations) ): ?>
			
			<?php foreach($organizations as $organization): ?>
			
				<?php $joined = !empty($organization['Permission']); ?>
			
				<div class="actionable">
					<div class="situation">
						<h4><?php echo h( $organization['Organization']['name'] ); ?></h4>
						<div class="stat-bar">
							<div class="stat">
								<div class="key"><?php echo __("Upcoming Events"); ?></div>
								<div class="value"><?php echo number_format(count($organization['Event'])); ?></div>
							</div>
						</div>
					</div>
					<div class="actions">
						<ul>
							<li class="<?php echo ( $joined ) ? 'on' : 'off'; ?>">
							<?php
				echo $this->Html->link(
					__('Stop publishing my volunteer activity with %s', $organization['Organization']['name']),
					array(
						'volunteer' => true,
						'controller' => 'organizations',
						'action' => 'leave',
						$organization['Organization']['organization_id'],
						'going'
					),
					array(
						'class' => 'api-trigger text-danger when-on',
						'data-api' => Router::url( array(
							'api' => true, 
							'controller' => 'organizations', 
							'action' => 'leave', 
							$organization['Organization']['organization_id'],
							'going'
						)),
						'data-on-success' => 'toggle_parent_class',
						'data-toggle-class' => 'on off'
					)
				)
			?>
								
								<?php
				echo $this->Html->link(
					__('Share my volunteer activity with %s', $organization['Organization']['name']),
					array(
						'volunteer' => true,
						'controller' => 'organizations',
						'action' => 'join',
						$organization['Organization']['organization_id'],
						'going'
					),
					array(
						'class' => 'api-trigger text-primary when-off',
						'data-api' => Router::url( array(
							'api' => true, 
							'controller' => 'organizations', 
							'action' => 'join', 
							$organization['Organization']['organization_id'],
							'going'
						)),
						'data-on-success' => 'toggle_parent_class',
						'data-toggle-class' => 'on off'
					)
				)
			?>
							</li>
						</ul>
					</div>
				</div>
			<?php endforeach; ?>
			
		<?php endif; ?>
		
		<ul class="pagination collapse-top">
			<?php
				echo $this->Paginator->prev(__('prev'), array('tag' => 'li'), null, array('tag' => 'li','class' => 'disabled','disabledTag' => 'a'));
				echo $this->Paginator->numbers(array('separator' => '','currentTag' => 'a', 'currentClass' => 'active','tag' => 'li','first' => 1));
				echo $this->Paginator->next(__('next'), array('tag' => 'li','currentClass' => 'disabled'), null, array('tag' => 'li','class' => 'disabled','disabledTag' => 'a'));
			?>
		</ul>
		
	</div>	
</div>