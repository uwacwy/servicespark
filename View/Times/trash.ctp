<div class="row">
	<div class="col-md-12">
		<h2><?php echo __("Deleted Time"); ?></h2>
		<p><?php echo __("This shows logged time you removed from your volunteer profile.  You can recover it if you would like."); ?></p>
		<?php if( !empty($deleted_times) ) : ?>
			<?php foreach($deleted_times as $deleted_time) : ?>
				<?php echo $this->Element('time_card', array('time' => $deleted_time) ); ?>
			<?php endforeach; ?>
		<?php else: ?>
			<p><em><?php echo __("You have no deleted time at the moment."); ?></em></p>
		<?php endif; ?>
	</div>
</div>