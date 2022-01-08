<?php $this->start('body'); ?>
<h1 class="text-center red">Welcome to Ruah MVC Framework!</h1>
<?php
$user = new Users(3);
dnd($user);

?>
<?php $this->end(); ?>