    <nav class="nav">
      <ul class="nav__list container">

      <?php foreach ($categories as $category) : ?>
          <li class="nav__item">
            <a href="/all-lots.php?category_id=<?= $category['id']; ?>"><?= htmlspecialchars($category['name']); ?></a>
          </li>
        <?php endforeach; ?>

      </ul>
    </nav>
    <form class="form form--add-lot container <?= !empty(array_filter($form_errors)) ? 'form--invalid' : ''; ?>" action="add.php" method="post" enctype="multipart/form-data">
      <h2>Добавление лота</h2>
      <div class="form__container-two">
        <div class="form__item <?= !empty($form_errors['lot-name']) ? 'form__item--invalid' : ''; ?>">
          <label for="lot-name">Наименование <sup>*</sup></label>
          <input id="lot-name" type="text" name="lot-name" placeholder="Введите наименование лота" value="<?= htmlspecialchars($form_values['lot-name']); ?>">
          <span class="form__error"><?= $form_errors['lot-name'] ?></span>
        </div>
        <div class="form__item <?= !empty($form_errors['category']) ? 'form__item--invalid' : ''; ?>">
          <label for="category">Категория <sup>*</sup></label>
          <select id="category" name="category">
            <option value="Выберите категорию">Выберите категорию</option>

            <?php
            foreach ($categories as $category) :
              $selected = '';
              if ($category['id'] === $form_values['category']) {
                $selected = ' selected';
              }
            ?>
              <option <?= $selected; ?> value="<?= $category['id']; ?>"> <?= htmlspecialchars($category['name']); ?></option>
            <?php endforeach; ?>

          </select>
          <span class="form__error"><?= $form_errors['category'] ?></span>
        </div>
      </div>
      <div class="form__item form__item--wide <?= !empty($form_errors['message']) ? 'form__item--invalid' : ''; ?>">
        <label for="message">Описание <sup>*</sup></label>
        <textarea id="message" name="message" placeholder="Напишите описание лота"><?= htmlspecialchars($form_values['message']); ?></textarea>
        <span class="form__error"><?= $form_errors['message'] ?></span>
      </div>

      <!--  добавить файл-изображение -->
      <div class="form__item form__item--file <?= !empty($img_values['img_errors']) ? 'form__item--invalid' : ''; ?>">
        <label>Изображение <sup>*</sup></label>
        <div class="form__input-file">
          <input class="visually-hidden" type="file" id="lot-img" name="lot-img" value="">
          <label for="lot-img">
            Добавить
          </label>
        </div>
        <span class="form__error"><?= $img_values['img_errors']; ?></span>
        <span style="color: inherit; display: block; clear: left"> <?= $img_values['img_loaded']; ?></span>
      </div>

      <div class="form__container-three">
        <div class="form__item form__item--small <?= !empty($form_errors['lot-rate']) ? 'form__item--invalid' : ''; ?>">
          <label for="lot-rate">Начальная цена <sup>*</sup></label>
          <input id="lot-rate" type="text" name="lot-rate" placeholder="0" value="<?= htmlspecialchars($form_values['lot-rate']); ?>">
          <span class="form__error"><?= $form_errors['lot-rate'] ?></span>
        </div>
        <div class="form__item form__item--small <?= !empty($form_errors['lot-step']) ? 'form__item--invalid' : ''; ?>">
          <label for="lot-step">Шаг ставки <sup>*</sup></label>
          <input id="lot-step" type="text" name="lot-step" placeholder="0" value="<?= htmlspecialchars($form_values['lot-step']); ?>">
          <span class="form__error"><?= $form_errors['lot-step'] ?></span>
        </div>
        <div class="form__item <?= !empty($form_errors['lot-date']) ? 'form__item--invalid' : ''; ?>">
          <label for="lot-date">Дата окончания торгов <sup>*</sup></label>
          <input class="form__input-date" id="lot-date" type="date" name="lot-date" placeholder="Введите дату в формате ГГГГ-ММ-ДД" value="<?= htmlspecialchars($form_values['lot-date']); ?>">
          <span class="form__error"><?= $form_errors['lot-date'] ?></span>
        </div>
      </div>
      <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
      <button type="submit" class="button" name="add-lot">Добавить лот</button>
    </form>
