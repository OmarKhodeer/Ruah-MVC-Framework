<?php $this->start('head'); ?>

<?php $this->end(); ?>

<?php $this->start('body'); ?>

<div class="col-md-6 col-md-offset-3 well">
  <h3 class="text-center">Register Here!</h3>
  <hr>
  <form action="" class="form" method="POST">
    <div class="bg-danger">
      <?= $this->displayErrors ?>
    </div>
    <div class="form-group">
      <label for="fname">First Name</label>
      <input type="text" name="fname" class="form-control" id="fname" value="<?= $this->post['fname'] ?>">
    </div>

    <div class="form-group">
      <label for="lname">Last Name</label>
      <input type="text" name="lname" class="form-control" id="lname" value="<?= $this->post['lname'] ?>">
    </div>

    <div class="form-group">
      <label for="email">Email</label>
      <input type="email" name="email" class="form-control" id="email" value="<?= $this->post['email'] ?>">
    </div>

    <div class="form-group">
      <label for="username">Username</label>
      <input type="text" name="username" class="form-control" id="username" value="<?= $this->post['username'] ?>">
    </div>

    <div class="form-group">
      <label for="password">Password</label>
      <input type="password" name="password" class="form-control" id="password" value="<?= $this->post['password'] ?>">
    </div>

    <div class="form-group">
      <label for="confirm">Confirm Password</label>
      <input type="password" name="confirm" class="form-control" id="confirm" value="<?= $this->post['confirm'] ?>">
    </div>

    <div class="pull-right">
      <input type="submit" class="btn btn-primary" value="Register">
    </div>
  </form>
</div>

<?php $this->end(); ?>