<?php if (isset($_GET['congratulation'])): ?>
  <div class="alert alert-success">
    <p>Поздравляем, регистрация пройдена!</p>
  </div>
<?php endif; ?>

<?php if ($user_name): ?>
<div class="content__main-col">
    <header class="content__header content__header--left-pad">
        <h2 class="content__header-text">Добро пожаловать, <?=$user_name;?></h2>
    </header>
</div>
<?php else: ?>

<nav class="nav">
      <ul class="nav__list container">
      <?php /* Главное меню - все страницы кроме главной */
        foreach ($categories as $category): ?>
          <li class="nav__item">
            <a href="all-lots.php?categoryID=<?= $category['id']; ?>"><?= htmlspecialchars($category['name']); ?></a>
          </li>
        <?php endforeach; ?>
      </ul>
    </nav>
    <?php 
    if (isset($errors)) {
      $classNameForm = count($errors) ? 'form--invalid' : '';
    }
    ?>
    <form class="form container <?= $classNameForm; ?>" action="/login.php" method="post">
      <h2>Вход</h2>
      <?php 
      $className = isset($errors['email']) ? 'form__item--invalid' : '';
      $value = $formVals['email'] ?? '';
      $error = $errors['email'] ?? '';
      ?>
      <div class="form__item <?= $className; ?>"> 
        <label for="email">E-mail <sup>*</sup></label>
        <input id="email" type="text" name="email" placeholder="Введите e-mail" value="<?= $value; ?>">
        <span class="form__error"><?= $error; ?></span>
      </div>
      <?php 
      $className = isset($errors['password']) ? 'form__item--invalid' : '';
      $value = $formVals['password'] ?? '';
      $error = $errors['password'] ?? '';
      ?>
      <div class="form__item form__item--last <?= $className; ?>">
        <label for="password">Пароль <sup>*</sup></label>
        <input id="password" type="password" name="password" placeholder="Введите пароль" value="<?= $value; ?>">
        <span class="form__error"><?= $error; ?></span>
      </div>
      <button type="submit" class="button">Войти</button>
    </form>

    <?php endif; ?>