
    <nav class="nav">
      <ul class="nav__list container">

      <?php foreach ($categories as $category) : ?>
          <li class="nav__item">
            <a href="/all-lots.php?category_id=<?= $category['id']; ?>"><?= htmlspecialchars($category['name']); ?></a>
          </li>
        <?php endforeach; ?>

      </ul>
    </nav>
    <form class="form form--add-lot container <?= addErrorStyle($form_errors); ?>" action="add.php" method="post" enctype="multipart/form-data">
      <h2>Добавление лота</h2>
      <div class="form__container-two">
        <div class="form__item <?= addErrorStyle($form_errors['lot-name']); ?>">
          <label for="lot-name">Наименование <sup>*</sup></label>
          <input id="lot-name" type="text" name="lot-name" placeholder="Введите наименование лота" value="<?= htmlspecialchars($form_values['lot-name']); ?>">
          <span class="form__error"><?= $form_errors['lot-name'] ?></span>
        </div>
        <div class="form__item <?= addErrorStyle($form_errors['category']); ?>">
          <label for="category">Категория <sup>*</sup></label>
          <select id="category" name="category">
            <option value="Выберите категорию">Выберите категорию</option>

            <?php
            foreach ($categories as $category) :
              $selected = '';
              if ($category['id'] == $form_values['category']) {
                $selected = ' selected';
              }
            ?>
              <option <?= $selected; ?> value="<?= $category['id']; ?>"> <?= htmlspecialchars($category['name']); ?></option>
            <?php endforeach; ?>

          </select>
          <span class="form__error"><?= $form_errors['category'] ?></span>
        </div>
      </div>
      <div class="form__item form__item--wide <?= addErrorStyle($form_errors['message']); ?>">
        <label for="message">Описание <sup>*</sup></label>
        <textarea id="message" name="message" placeholder="Напишите описание лота"><?= htmlspecialchars($form_values['message']); ?></textarea>
        <span class="form__error"><?= $form_errors['message'] ?></span>
      </div>

      <!--  добавить файл-изображение -->
      <div class="form__item form__item--file <?= addErrorStyle($img_values['img_errors']); ?>">
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
        <div class="form__item form__item--small <?= addErrorStyle($form_errors['lot-rate']); ?>">
          <label for="lot-rate">Начальная цена <sup>*</sup></label>
          <input id="lot-rate" type="text" name="lot-rate" placeholder="0" value="<?= htmlspecialchars($form_values['lot-rate']); ?>">
          <span class="form__error"><?= $form_errors['lot-rate'] ?></span>
        </div>
        <div class="form__item form__item--small <?= addErrorStyle($form_errors['lot-step']); ?>">
          <label for="lot-step">Шаг ставки <sup>*</sup></label>
          <input id="lot-step" type="text" name="lot-step" placeholder="0" value="<?= htmlspecialchars($form_values['lot-step']); ?>">
          <span class="form__error"><?= $form_errors['lot-step'] ?></span>
        </div>
        <div class="form__item <?= addErrorStyle($form_errors['lot-date']); ?>">
          <label for="lot-date">Дата окончания торгов <sup>*</sup></label>
          <input class="form__input-date" id="lot-date" type="date" name="lot-date" placeholder="Введите дату в формате ГГГГ-ММ-ДД" value="<?= htmlspecialchars($form_values['lot-date']); ?>">
          <span class="form__error"><?= $form_errors['lot-date'] ?></span>
        </div>
      </div>
      <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
      <button type="submit" class="button" name="add-lot">Добавить лот</button>
    </form>
