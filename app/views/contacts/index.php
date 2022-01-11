<?php $this->start('body'); ?>
<h1 class="text-center">My Contacts</h1>
<table class="table table-striped table-condensed table-bordered">
  <thead>
    <th>Name</th>
    <th>Email</th>
    <th>Cell Phone</th>
    <th>Home Phone</th>
    <th>Work Phone</th>
    <th></th>
  </thead>
  <tbody>
    <?php foreach ($this->contacts as $contact) : ?>
      <tr>
        <td><?= $contact->displayName() ?></td>
        <td><?= $contact->email ?></td>
        <td><?= $contact->cell_phone ?></td>
        <td><?= $contact->home_phone ?></td>
        <td><?= $contact->work_phone ?></td>
        <td></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php $this->end(); ?>