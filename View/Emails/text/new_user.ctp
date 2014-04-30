Hello, <?php echo __('@%s', $entry['User']['username']); ?>!

Welcome to <?php echo Configure::read('Solution.name'); ?>!  Your account has been created and you're ready to start finding service opportunities that challenge you.

You can always login to <?php echo Configure::read('Solution.name'); ?> at <?php echo $this->Html->url( array('controller' => 'users', 'action' => 'login' ), true ); ?>

Again, thank you for joining <?php echo Configure::read('Solution.name'); ?>.

Yours,
The United Way of Albany County