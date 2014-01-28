<?php
	/*
		This is an annotated template file.  You are currently reading a PHP comment.  PHP and HTML can be mixed!
		If you would like to make a webpage a little smarter, you can start coding.  Just wrap your PHP code in PHP brackets!

		<?php echo "this is some code"; ?>

		PHP comments DO NOT appear in rendered HTML.  If you would like to leave HTML comments, you can use the <!-- here is a comment --> syntax

		// You can also use the slash comment in addition to the block comment.		
	*/
?>

<div class="users index">

<?php
	/*
		the __() function is used to internationalize your cake apps.
		This is a common PHP convention.  WordPress uses it.  Drupal uses it. CakePHP uses it.  It's kinda cool.
		Translations for your application can be placed in *.po files.  The PO file is a key=>value store.
		For now, our application is on en-us, but if we changed that to something like en-uk, it would look for BRITISH ENGLISH translations.
		Since we aren't using translations, and since a translation doesn't actually exist, the key will be echoed.

		Elegant. Handy.  Use this convention.  It's a tiny bit of work for tons of flexibility in the future.
	*/
?>

	<h2><?php echo __('Users'); ?></h2>


	<table cellpadding="0" cellspacing="0">

<?php
	/*
		This is the table header.  These cells are html TH elements and they have some PHP inside each cell.
		Take a look at the $this->Paginator->sort() function.  This is a handy component provided by CakePHP that allows you to quickly sort and paginate data.

		You'll notice that there are more echoed elements using the Paginator component lower on the page.

		the Paginator->sort() method understands our data.  as a result, you only need to tell it what field it is rendering a sort toggle for.  CakePHP takes care of the rest.
	*/
?>
	<tr>
			<th><?php echo $this->Paginator->sort('user_id'); ?></th>
			<th><?php echo $this->Paginator->sort('created'); ?></th>
			<th><?php echo $this->Paginator->sort('modified'); ?></th>
			<th><?php echo $this->Paginator->sort('username'); ?></th>
			<th><?php echo $this->Paginator->sort('password'); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>



<?php
	/*
		Data was sent to this view in the controller.  Open the app/Controller/UsersController.php and flip to the index() function.
		The UsersController::index() function handles all of the logic that generates this page, including pulling data from the database.

		Here is the foreach loop where we display the data that was sent from the controller.
		You'll notice the $user data being stored in an "associative array." This is php-speak for an array that uses strings as keys.

		Our controller set the $user variable with stuff it pulled from the database.
		the $users variable can be iterated using numeric keys.
		For example, $users[0] and $users[1] will return a user object.  This is a CakePHP convention--not a rule that has to be followed in PHP.
		foreach initiates an iterator and PHP handles this set of data quite handily.

		the echo() function just prints whatever is handed to it as text
		the h() function is a handy alias for the htmlspecialchars() PHP function.  This appropriately escapes data by turning it into valid HTML.


	*/
?>

<?php foreach ($users as $user): ?>
	<tr>
		<td><?php echo h($user['User']['user_id']); ?>&nbsp;</td>
		<td><?php echo h($user['User']['created']); ?>&nbsp;</td>
		<td><?php echo h($user['User']['modified']); ?>&nbsp;</td>
		<td><?php echo h($user['User']['username']); ?>&nbsp;</td>
		<td><?php echo h($user['User']['password']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $user['User']['user_id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $user['User']['user_id'])); ?>
			<?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $user['User']['user_id']), null, __('Are you sure you want to delete # %s?', $user['User']['user_id'])); ?>
		</td>
	</tr>
<?php endforeach; ?>

	</table>

<?php
	/*
		Here we see our paginator at work again.  This does a lot of work so we don't have to.
	*/
?>
	<p>
	<?php
	echo $this->Paginator->counter(
		array(
			'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
		)
	);
	?>	</p>
	<div class="paging">
	<?php
		echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => ''));
		echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
	?>
	</div>
</div>

<?php
	/*
		These actions are linking to other VIEWS inside the Users controller.

		Cake's powerful router will take care of linking for you.  If you tell it where you want to go, it will generate the correct link and print it on the page for you.

		you can specify a lot of parameters in the array()
	*/
?>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('New User'), array('action' => 'add')); ?></li>
		<li><?php echo $this->Html->link(__('Logout'), array('action' => 'logout')); ?></li>
	</ul>
</div>
