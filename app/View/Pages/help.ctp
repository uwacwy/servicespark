<div class="row">
	<div class="col-md-3">
		<?php echo $this->Element('help_toc'); ?>
	</div>
	<div class="col-md-9">
		<h1>
			<small>Help and How-To</small><br>
			Roles and Permissions
		</h1>
		<p>Users make take on a number of roles within <?php echo Configure::read('Solution.name'); ?>.  This page enumerates the abilities of a user.  Users may have various roles inside of <?php echo Configure::read('Solution.name'); ?>.</p>
		<dl>
			<dt>Coordinator</dt>
				<dd>The coordinator user role is allowed to view and make changes to data submitted to an organization.  This includes the ability to manage events and associated time entries.</dd>
			<dt>Supervisor</dt>
				<dd>The supervisor user role is allowed to view an organization's data.  This includes the ability to view time punch information published to the organization.</dd>
			<dt>Volunteer</dt>
				<dd>The volunteer user role is allowed to view and search for events and update their volunteer profile and any associated data.  This includes all volunteer time entries belonging to the user.</dd>
		</dl>
	</div>
</div>