<?php if( !empty($times) ): ?>
<div class="_table">
	<div class="_thead">
		<div class="_tr">
			<span class="_th"><?php echo __('Volunteer'); ?></span>
			<span class="_th"><?php echo __('Time In'); ?></span>
			<span class="_th"><?php echo __('Time Out'); ?></span>
			<span class="_th"><?php echo __('Duration'); ?></span>
		</div>
	</div>
	<div class="_tbody">
		<?php
		foreach($times as $time):
			$stop_time = Hash::get($time, 'Time.stop_time');
			$duration = Hash::get($time, 'Time.duration');
			$row_class = $duration ? "_success" : "_warning";
		?>
			<a class="_tr <?php echo $row_class; ?>" href="<?php echo $this->Html->url( array(
				'controller' => 'times',
				'action' => 'view',
				$time['Time']['time_id']
			)) ?>">
				<span class="_td"><?php echo Hash::get($time, 'Time.User.full_name'); ?></span>
				<span class="_td"><?php echo CakeTime::nice(Hash::get($time, 'Time.start_time')); ?></span>
				<span class="_td"><?php echo $stop_time ? CakeTime::nice($stop_time) : "&hellip;"; ?></span>
				<span class="_td"><?php echo $duration ? number_format($duration, 2) : "&hellip;"; ?></span>
			</a>
		<?php endforeach; ?>
	</div>
</div>
<?php endif; ?>