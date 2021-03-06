<?php if (isset($_GET['congratulation'])) : ?>
  <div class="alert alert-success">
    <p>Поздравляем, регистрация пройдена!</p>
  </div>
<?php endif; ?>

<nav class="nav">
      <ul class="nav__list container">

        <?php foreach ($categories as $category) : ?>
          <li class="nav__item">
            <a href="/all-lots.php?category_id=<?= $category['id']; ?>"><?= htmlspecialchars($category['name']); ?></a>
          </li>
        <?php endforeach; ?>

      </ul>
    </nav>
    <?php $classNameForm = '';
    if (isset($form_errors)) {
        $classNameForm = count($form_errors) ? 'form--invalid' : '';
    } ?>
    <form class="form container <?= $classNameForm; ?>" action="/login.php" method="post">
      <h2>Вход</h2>
        <?php
        $className = isset($form_errors['email']) ? 'form__item--invalid' : '';
        $value = $form_values['email'] ?? '';
        $error = $form_errors['email'] ?? '';
        ?>
      <div class="form__item <?= $className; ?>"> 
        <label for="email">E-mail <sup>*</sup></label>
        <input id="email" type="text" name="email" placeholder="Введите e-mail" value="<?= $value; ?>">
        <span class="form__error"><?= $error; ?></span>
      </div>
        <?php
        $className = isset($form_errors['password']) ? 'form__item--invalid' : '';
        $value = $form_values['password'] ?? '';
        $error = $form_errors['password'] ?? '';
        ?>
      <div class="form__item form__item--last <?= $className; ?>">
        <label for="password">Пароль <sup>*</sup></label>
        <input id="password" type="password" name="password" placeholder="Введите пароль" value="<?= $value; ?>">
        <span class="form__error"><?= $error; ?></span>
      </div>
      <button type="submit" class="button">Войти</button>
    </form>
