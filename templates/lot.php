
<?php if (isset($_GET['lot_success'])) : ?>
  <div class="alert alert-success">
    <p>Лот успешно добавлен!</p>
  </div>
<?php endif; ?>

<?php if (isset($_GET['bet_success'])) : ?>
  <div class="alert alert-success">
    <p>Ставка принята!</p>
  </div>
<?php endif; ?>

    <nav class="nav">
      <ul class="nav__list container">

        <?php foreach ($categories as $category) : ?>
          <li class="nav__item">
            <a href="/all-lots.php?categoryID=<?= $category['id']; ?>"><?= deffXSS($category['name']); ?></a>
          </li>
        <?php endforeach; ?>

      </ul>
    </nav>
    <section class="lot-item container">
      <h2><?= htmlspecialchars($item['name']) ?></h2>
      <div class="lot-item__content">
        <div class="lot-item__left">
          <div class="lot-item__image">
            <img src="<?= htmlspecialchars($item['img_url']); ?>" width="730" height="548" alt="<?= htmlspecialchars($item['name']) ?>">
          </div>
          <p class="lot-item__category">Категория: <span><?= htmlspecialchars($item['category']) ?></span></p>
          <p class="lot-item__description"><?= htmlspecialchars($item['description']) ?></p>
        </div>
        <div class="lot-item__right">

        <?php if (isset($_SESSION['user']) && strtotime($item['ts_end']) > time()) : ?>
          <div class="lot-item__state">
            <?php $Timer = makeTimer(htmlspecialchars($item['ts_end'])); /* функция таймер */ ?>
            <div class="lot-item__timer timer <?= $Timer['style']; ?>">
              <?= $Timer['DDHHMM']; ?>
            </div>
            <div class="lot-item__cost-state">
              <div class="lot-item__rate">
                <span class="lot-item__amount">Текущая цена</span>
                <span class="lot-item__cost"><?= makePriceFormat(htmlspecialchars($item['l_price'])); ?></span>
              </div>
              <div class="lot-item__min-cost">
                Мин. ставка <span><?= makePriceFormat($item['min_bet']); ?> р</span>
              </div>
            </div>

            <?php
            $classErr = isset($formErr) ? 'form__item--invalid' : '';
            $error = $formErr['cost'] ?? '';
            $value = $formVal['cost'] ?? '';
            ?>

            <form class="lot-item__form <?= $classErr ?>" action="/lot.php?itemID=<?= $item['id'] ?>" method="post" autocomplete="off">
              <p class="lot-item__form-item form__item <?= $classErr ?>">
                <label for="cost">Ваша ставка</label>
                <input id="cost" type="text" name="cost" placeholder="<?= makePriceFormat($item['min_bet']); ?>" value="<?= $value ?>">
                <span class="form__error"><?= $error ?></span>
              </p>
              <button type="submit" class="button">Сделать ставку</button>
            </form>
          </div>
          <div class="history">
            <h3>История ставок (<span><?= $item['number_bets']; ?></span>)</h3>
            <table class="history__list">
              <?php foreach ($itemBets as $bet) : ?>
              <tr class="history__item">
                <td class="history__name"><?= htmlspecialchars($bet['user_name']); ?></td>
                <td class="history__price"><?= htmlspecialchars($bet['bet_price']); ?> р</td>
                <td class="history__time"><?= makeBacktime(htmlspecialchars($bet['ts_betted'])); ?></td>
              </tr>
              <?php endforeach; ?>
            </table>
          </div>
        <?php endif ?>
        </div>
      </div>
    </section>

