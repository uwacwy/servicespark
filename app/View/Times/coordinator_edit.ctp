<div class="row frm coordinator edit time">
	<div class="col-md-3">
		<?php echo $this->Element('coordinator_time_actions', array('event_id' => $time['EventTime'][0]['Event']['event_id'])); ?>
	</div>
	<div class="col-md-9">
		<?php 
			//$form_defaults['class'] = 'form form-inline'; // this is a bootstrap override because the form will look like garbage otherwise
			echo $this->Form->Create('Time', $form_defaults);
		?>
			<h1>
				<small>You are editing a time entry for</small><br>
				<?php echo h($time['User']['full_name']); ?> <small>for <?php echo h($time['EventTime'][0]['Event']['title']); ?></small>
			</h1>
			<?php echo $this->Form->input('time_id'); ?>
				
				<div class="form form-inline">
					<?php 
						echo $this->Form->input('start_time', array('separator' => "") );
						echo $this->Form->input('stop_time', array('separator' => "") );
						echo "<hr>";
						echo $this->Form->input('Time.blank', array('type'=>'checkbox', 'label' => 'Check this box to blank the stop time.'));
					?>
				</div>
			<p class="text-warning">
			<?php echo __("Blanking the stop time will allow the user to clock out of this event again."); ?></p>
		<?php echo $this->Form->input('TimeComment.body', array('label' => __("Add a Comment"), 'rows' => 3 ) ); ?>
		<?php echo $this->Form->End(array('label' => 'Edit Time Entry', 'class' => 'btn btn-warning') ); ?>
	</div>
</div>