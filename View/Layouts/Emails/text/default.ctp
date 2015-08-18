<?php echo $this->fetch('content'); ?> 
----
Thank you for using <?php echo Configure::read('Solution.name'); ?> 
Too many emails?  You can manage your subscriptions at <?php echo Router::url( array('controller' => 'user', 'action' => 'profile'), true); ?>