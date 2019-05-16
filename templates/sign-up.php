    <nav class="nav">
      <ul class="nav__list container">
      
        <?php foreach ($categories as $category): ?>
          <li class="nav__item">
            <a href="/all-lots.php?categoryID=<?= $category['id']; ?>"><?= deffXSS($category['name']); ?></a>
          </li>
        <?php endforeach; ?>

      </ul>
    </nav>
    <form class="form container <?= addErrorStyle($formErrors); ?>" action="sign-up.php" method="post" autocomplete="off">
      <h2>Регистрация нового аккаунта</h2>
      <div class="form__item <?= addErrorStyle($formErrors['email']); ?>"> <!-- form__item--invalid -->
        <label for="email">E-mail <sup>*</sup></label>
        <input id="email" type="text" name="email" placeholder="Введите e-mail" value="<?= deffXSS($formData['email']) ?>">
        <span class="form__error "><?= $formErrors['email']; ?></span>
      </div>
      <div class="form__item <?= addErrorStyle($formErrors['password']); ?>">
        <label for="password">Пароль <sup>*</sup></label>
        <input id="password" type="password" name="password" placeholder="Введите пароль" value="<?= deffXSS($formData['password']) ?>">
        <span class="form__error"><?= $formErrors['password']; ?></span>
      </div>
      <div class="form__item <?= addErrorStyle($formErrors['name']); ?>">
        <label for="name">Имя <sup>*</sup></label>
        <input id="name" type="text" name="name" placeholder="Введите имя" value="<?= deffXSS($formData['name']) ?>">
        <span class="form__error"><?= $formErrors['name']; ?></span>
      </div>
      <div class="form__item <?= addErrorStyle($formErrors['message']); ?>">
        <label for="message">Контактные данные <sup>*</sup></label>
        <textarea id="message" name="message" placeholder="Напишите как с вами связаться"><?= deffXSS($formData['message']) ?></textarea>
        <span class="form__error"><?= $formErrors['message']; ?></span>
      </div>
      <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
      <button type="submit" class="button" name="sign-up">Зарегистрироваться</button>
      <a class="text-link" href="#">Уже есть аккаунт</a>
    </form>