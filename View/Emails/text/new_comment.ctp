Hello, *|full_name|*

A new comment was posted on <?php echo $comment['Event']['title']; ?>.

<?php echo $comment['User']['full_name']; ?> said:
<?php echo $comment['Comment']['body']; ?> 

You may view the comment at
< <?php echo Router::url( array(
	'volunteer' => true,
	'controller' => 'events',
	'action' => 'view',
	$comment['Event']['event_id'],
	'#' => 'comment-'.$comment['Comment']['comment_id']
), true); ?> >

You are receiving this email because...
*|reasons_block|*

Manage your notification preferences from your profile
< <?php echo Router::url( array('controller' => 'users', 'action' => 'profile'), true); ?> >