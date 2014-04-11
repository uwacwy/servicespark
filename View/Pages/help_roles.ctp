<div class="row">
	<div class="col-md-3">
		<?php echo $this->Element('help_toc'); ?>
	</div>
	<div class="col-md-9">
		<h1>
			<small>Help and How-To</small><br>
			Roles and Permissions
		</h1>
		<p>Users make take on a number of roles within <?php echo Configure::read('Solution.name'); ?>.  This page enumerates the abilities of a user.  Users may have various roles with various organizations inside of <?php echo Configure::read('Solution.name'); ?>.</p>
		<dl>
			<dt>Best</dt>
				<dd>Some views will allow you to select Best as an alternative view option.  "Best" will automatically select the most powerful view for the logged-in user.</dd>
			<dt>Coordinator</dt>
				<dd>The coordinator user role is allowed to view and make changes to data submitted to an organization.  This includes the ability to manage events and associated time entries.</dd>
			<dt>Supervisor</dt>
				<dd>The supervisor user role is allowed to view an organization's data.  This includes the ability to view time punch information published to the organization.</dd>
			<dt>Volunteer</dt>
				<dd>The volunteer user role is allowed to view and search for events and update their volunteer profile and any associated data.  This includes all volunteer time entries belonging to the user.</dd>
		</dl>
	

		<h2>Role Matrix</h2>
		<p>The following table enumerates capabilities granted by user role.</p>
		<table class="table table-striped">
			<thead>
				<tr>
					<th>&nbsp;</th>
					<th class="col-md-2">Coordinator</th>
					<th class="col-md-2">Supervisor</th>
					<th class="col-md-2">Volunteer</th>
					<th class="col-md-2">Guest</th>
				</tr>
			</thead>
			<tbody>
<tr><th colspan="5">Organizations</th>					</tr>
<tr><td>Edit Organization</td>	<td><span class="glyphicon glyphicon-ok"></span></td>	<td></td>	<td></td>	<td></td>	</tr>
<tr><td>Create Organization</td>	<td></td>	<td></td>	<td>supervisor only</td>	<td></td>	</tr>
<tr><th colspan="5">Events</th>					</tr>
<tr><td>Create Events</td>	<td><span class="glyphicon glyphicon-ok"></span></td>	<td></td>	<td></td>	<td></td>	</tr>
<tr><td>Edit Events</td>	<td><span class="glyphicon glyphicon-ok"></span></td>	<td></td>	<td></td>	<td></td>	</tr>
<tr><td>Cancel Events</td>	<td><span class="glyphicon glyphicon-ok"></span></td>	<td></td>	<td></td>	<td></td>	</tr>
<tr><td>View Time Tokens</td>	<td><span class="glyphicon glyphicon-ok"></span></td>	<td><span class="glyphicon glyphicon-ok"></span></td>	<td></td>	<td></td>	</tr>
<tr><td>View Events</td>	<td><span class="glyphicon glyphicon-ok"></span></td>	<td><span class="glyphicon glyphicon-ok"></span></td>	<td>ongoing and future only</td>	<td>ongoing and future</td>	</tr>
<tr><th colspan="5">Time Entries</th>					</tr>
<tr><td>Adjust Entries</td>	<td><span class="glyphicon glyphicon-ok"></span></td>	<td></td>	<td></td>	<td></td>	</tr>
<tr><td>View Entries</td>	<td><span class="glyphicon glyphicon-ok"></span></td>	<td><span class="glyphicon glyphicon-ok"></span></td>	<td>can view own activity only</td>	<td></td>	</tr>
<tr><td>Clock In</td>	<td><span class="glyphicon glyphicon-ok"></span></td>	<td><span class="glyphicon glyphicon-ok"></span></td>	<td><span class="glyphicon glyphicon-ok"></span></td>	<td></td>	</tr>
<tr><td>Clock Out</td>	<td><span class="glyphicon glyphicon-ok"></span></td>	<td><span class="glyphicon glyphicon-ok"></span></td>	<td><span class="glyphicon glyphicon-ok"></span></td>	<td></td>	</tr>
<tr><th colspan="5">User Accounts</th>					</tr>
<tr><td>Create Account</td>	<td></td>	<td></td>	<td></td>	<td><span class="glyphicon glyphicon-ok"></span></td>	</tr>
<tr><td>Recover Password</td>	<td></td>	<td></td>	<td></td>	<td><span class="glyphicon glyphicon-ok"></span></td>	</tr>
<tr><td>Promote Within Organization</td>	<td><span class="glyphicon glyphicon-ok"></span></td>	<td></td>	<td></td>	<td></td>	</tr>
<tr><td>Remove From Organization</td>	<td><span class="glyphicon glyphicon-ok"></span></td>	<td></td>	<td></td>	<td></td>	</tr>


			</tbody>
		</table>
	</div>
</div>