<?php
$menu = Router::getMenu('menu_acl');
$currentPage = currentPage();
// dnd($currentPage);
?>

<nav class="navbar navbar-default">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#main_menu" aria-expanded="false">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="<?= PROOT . 'home' ?>"><?= MENU_BRAND ?></a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="main_menu">
      <ul class="nav navbar-nav">
        <?php foreach ($menu as $menuName => $link) :
          $active = ''; ?>
          <?php if (is_array($link)) : // check if the link is a dropdown menu
          ?>
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?= $menuName ?> <span class="caret"></span></a>
              <ul class="dropdown-menu">
                <?php foreach ($link as $subName => $subLink) :
                  $active = ($subLink == $currentPage) ? 'active' : ''
                ?>
                  <?php if ($subName == 'Separator') : ?>
                    <li role="separator" class="divider"></li>
                  <?php else : ?>
                    <li class="<?= $active ?>"><a href="<?= $subLink ?>"><?= $subName ?></a></li>
                  <?php endif; ?>
                <?php endforeach; ?>
              </ul>
            </li>
          <?php else :
            $active = ($link == $currentPage) ? 'active' : '' ?>
            <li class="<?= $active ?>"><a href="<?= $link ?>"><?= $menuName ?></a></li>
          <?php endif; ?>
        <?php endforeach; ?>
      </ul>

      <ul class="nav navbar-nav navbar-right">
        <?php if (currentUser()) : ?>
          <li><a href="#">Hello <?= currentUser()->fname ?></a></li>
        <?php endif; ?>
      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>