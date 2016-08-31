<div class="event skill-picker" data-for="event">
	<div class="categories">
		<ul>
			<li><a href="#" data-order-by="user_count" class="category-toggle"><?php echo __("Popular with %s Users", Configure::read('Solution.name')); ?></a></li>
			<li><a href="#" data-order-by="event_count" class="category-toggle"><?php echo __("Popular with other events"); ?></a></li>
			<li><a href="#" data-order-by="created" class="category-toggle"><?php echo __("Recently Added"); ?></a></li>
			<li><a href="#" data-order-by="skill" class="category-toggle"><?php echo __("A to Z"); ?></a></li>
		</ul>
	</div>
	<div class="available-skills">
		<h3>Available</h3>
		<ul></ul>
	</div>
	<div class="chosen-skills">
		<h3>Chosen</h3>
		<ul></ul>
	</div>
</div>