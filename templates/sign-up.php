    <nav class="nav">
      <ul class="nav__list container">
      
        <?php foreach ($categories as $category) : ?>
          <li class="nav__item">
            <a href="/all-lots.php?category_id=<?= $category['id']; ?>"><?= htmlspecialchars($category['name']); ?></a>
          </li>
        <?php endforeach; ?>

      </ul>
    </nav>
    <form class="form container <?= addErrorStyle($form_errors); ?>" action="sign-up.php" method="post" autocomplete="off">
      <h2>Регистрация нового аккаунта</h2>
      <div class="form__item <?= addErrorStyle($form_errors['email']); ?>">
        <label for="email">E-mail <sup>*</sup></label>
        <input id="email" type="text" name="email" placeholder="Введите e-mail" value="<?= htmlspecialchars($form_values['email']) ?>">
        <span class="form__error "><?= $form_errors['email']; ?></span>
      </div>
      <div class="form__item <?= addErrorStyle($form_errors['password']); ?>">
        <label for="password">Пароль <sup>*</sup></label>
        <input id="password" type="password" name="password" placeholder="Введите пароль" value="<?= htmlspecialchars($form_values['password']) ?>">
        <span class="form__error"><?= $form_errors['password']; ?></span>
      </div>
      <div class="form__item <?= addErrorStyle($form_errors['name']); ?>">
        <label for="name">Имя <sup>*</sup></label>
        <input id="name" type="text" name="name" placeholder="Введите имя" value="<?= htmlspecialchars($form_values['name']) ?>">
        <span class="form__error"><?= $form_errors['name']; ?></span>
      </div>
      <div class="form__item <?= addErrorStyle($form_errors['message']); ?>">
        <label for="message">Контактные данные <sup>*</sup></label>
        <textarea id="message" name="message" placeholder="Напишите как с вами связаться"><?= htmlspecialchars($form_values['message']) ?></textarea>
        <span class="form__error"><?= $form_errors['message']; ?></span>
      </div>
      <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
      <button type="submit" class="button" name="sign-up">Зарегистрироваться</button>
      <a class="text-link" href="login.php">Уже есть аккаунт</a>
    </form>