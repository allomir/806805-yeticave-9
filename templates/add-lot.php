
    <nav class="nav">
      <ul class="nav__list container">
      <?php 
        /* Вкладывание простое горизонтальное меню, кроме главной страницы */
        require(__DIR__ . '/../inc/mainMenuSimple.php'); 
      ?>
      </ul>
    </nav>
    <form class="form form--add-lot container <?= insErrStyle($formErrors); ?>" action="add.php" method="post" enctype="multipart/form-data"> <!-- form--invalid -->
      <h2>Добавление лота</h2>
      <div class="form__container-two">
        <div class="form__item <?= insErrStyle($formErrors['lot-name']); ?>">
          <label for="lot-name">Наименование <sup>*</sup></label>
          <input id="lot-name" type="text" name="lot-name" placeholder="Введите наименование лота" value="<?= deffXSS($formData['lot-name']); ?>">
          <span class="form__error"><?= $formErrors['lot-name'] ?></span>
        </div>
        <div class="form__item <?= insErrStyle($formErrors['category']); ?>">
          <label for="category">Категория <sup>*</sup></label>
          <select id="category" name="category">
            <option>Выберите категорию</option>

            <?php 
            /* Итерация Категории из БД */
            foreach ($categories as $category) {
              $selected = '';
              if ($category['name'] == $formData['category']) {
                $selected = ' selected';
              }
              print('<option' . $selected . '>' . deffXSS($category['name']) . '</option>');
            } 
            ?>

          </select>
          <span class="form__error"><?= $formErrors['category'] ?></span>
        </div>
      </div>
      <div class="form__item form__item--wide <?= insErrStyle($formErrors['message']); ?>">
        <label for="message">Описание <sup>*</sup></label>
        <textarea id="message" name="message" placeholder="Напишите описание лота"><?= deffXSS($formData['message']); ?></textarea>
        <span class="form__error"><?= $formErrors['message'] ?></span>
      </div>

      <!--  добавить файл-изображение -->
      <div class="form__item form__item--file">
        <label>Изображение <sup>*</sup></label>
        <div class="form__input-file">
          <input class="visually-hidden" type="file" id="lot-img" name="lot-img" value="">
          <label for="lot-img">
            Добавить
          </label>
        </div>
        <?= $imgData['mess_err']; ?>
      </div>

      <div class="form__container-three">
        <div class="form__item form__item--small <?= insErrStyle($formErrors['lot-rate']); ?>">
          <label for="lot-rate">Начальная цена <sup>*</sup></label>
          <input id="lot-rate" type="text" name="lot-rate" placeholder="0" value="<?= deffXSS($formData['lot-rate']); ?>">
          <span class="form__error"><?= $formErrors['lot-rate'] ?></span>
        </div>
        <div class="form__item form__item--small <?= insErrStyle($formErrors['lot-step']); ?>">
          <label for="lot-step">Шаг ставки <sup>*</sup></label>
          <input id="lot-step" type="text" name="lot-step" placeholder="0" value="<?= deffXSS($formData['lot-step']); ?>">
          <span class="form__error"><?= $formErrors['lot-step'] ?></span>
        </div>
        <div class="form__item <?= insErrStyle($formErrors['lot-date']); ?>">
          <label for="lot-date">Дата окончания торгов <sup>*</sup></label>
          <input class="form__input-date" id="lot-date" type="text" name="lot-date" placeholder="Введите дату в формате ГГГГ-ММ-ДД" value="<?= deffXSS($formData['lot-date']); ?>">
          <span class="form__error"><?= $formErrors['lot-date'] ?></span>
        </div>
      </div>
      <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
      <?php // печать ошибок в верстке
      /* $i = 0;
        foreach ($formErrors as $value) {
          if (!empty($value)) {
            $i++;
            print($i . '. ');
            print($value . '<br>');
          }
        }
      if(empty($imgData['img_url'])) {print(++$i . '. файл не загружен <br>');}
      print('<br>'); */
      ?>
      <button type="submit" class="button" name="add_lot">Добавить лот</button>
    </form>
