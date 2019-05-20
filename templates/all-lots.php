
    <nav class="nav">
      <ul class="nav__list container">

        <?php foreach ($categories as $category) : ?>
          <li class="nav__item">
            <a href="/all-lots.php?category_id=<?= $category['id']; ?>"><?= htmlspecialchars($category['name']); ?></a>
          </li>
        <?php endforeach; ?>

      </ul>
    </nav>
    <div class="container">
      <section class="lots">
        <?php $category_name = $category_name ?? ''; ?>
        <h2>Все лоты в категории: <span>«<?= $items[0]['category'] ?? $category_name ?>»</span></h2>
        <?= empty($items) ? '<p>Активные лоты отсутствуют, проверьте позже</p>' : '' ?>

        <?php if (!empty($items)) : ?>
        <ul class="lots__list">

            <?php foreach ($items as $item) :
                $Timer = makeTimer(htmlspecialchars($item['ts_end'])); ?>
            <li class="lots__item lot">
                <div class="lot__image">
                    <img src="<?= htmlspecialchars($item['img_url']); ?>" width="350" height="260" alt="<?= htmlspecialchars($item['name']); ?>">
                </div>
                <div class="lot__info">
                    <span class="lot__category"><?= htmlspecialchars($item['category']); ?></span>
                    <h3 class="lot__title"><a class="text-link" href="/lot.php?item_id=<?= $item['id']; ?>"><?= htmlspecialchars($item['name']); ?></a></h3>
                    <div class="lot__state">
                        <div class="lot__rate">
                            <span class="lot__amount"><?= $item['number_bets'] ?></span>
                            <span class="lot__cost"><?= makePriceFormat(htmlspecialchars($item['last_price'])); ?><b class="rub">р</b></span>
                        </div>
                        <div class="lot__timer timer <?= $Timer['style']; ?>">
                            <?= $Timer['DDHHMM']; ?>
                        </div>
                    </div>
                </div>
            </li>
            <?php endforeach; ?>

        </ul>
        <?php endif; ?>
      </section>

        <?php if (!empty($items)) : ?>
      <ul class="pagination-list">
        <li class="pagination-item pagination-item-prev"><a>Назад</a></li>
        <li class="pagination-item pagination-item-active"><a>1</a></li>
        <li class="pagination-item"><a href="#">2</a></li>
        <li class="pagination-item"><a href="#">3</a></li>
        <li class="pagination-item"><a href="#">4</a></li>
        <li class="pagination-item pagination-item-next"><a href="#">Вперед</a></li>
      </ul>
        <?php endif; ?>

    </div>
