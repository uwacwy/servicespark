Hello, <?php echo $user['User']['full_name']; ?>!

Welcome to <?php echo Configure::read('Solution.name'); ?>!  Your account has been created and you're ready to start finding service opportunities that challenge you.

You can always login to <?php echo Configure::read('Solution.name'); ?> at <?php echo Router::url( array('controller' => 'users', 'action' => 'login' ), true ); ?> 

Don't forget that your new username is <?php echo $user['User']['username']; ?> 

You are receiving this email because someone just used this email address to create an account for <?php echo Configure::read('Solution.name'); ?> at <?php echo Router::url('/', true); ?>.