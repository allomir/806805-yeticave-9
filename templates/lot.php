
<?php if (isset($_GET['success'])): ?>
  <div class="alert alert-success">
    <p>Спасибо за ваше сообщение!</p>
  </div>
<?php endif; ?>

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
          </div>
        </div>
      </div>
    </section>

