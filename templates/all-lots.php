
    <nav class="nav">
      <ul class="nav__list container">

        <?php foreach ($categories as $category): ?>
          <li class="nav__item">
            <a href="/all-lots.php?categoryID=<?= $category['id']; ?>"><?= deffXSS($category['name']); ?></a>
          </li>
        <?php endforeach; ?>

      </ul>
    </nav>
    <div class="container">
      <section class="lots">
      <?php $whatIsThis = empty($items) ? 'Лоты отсутствуют в категории' : 'Все лоты в категории '; 
      $page_category = !empty($_GET['categoryID']) ? $categories[$_GET['categoryID'] - 1]['name'] : ''; ?>
        <h2><?= $whatIsThis; ?><?= ' <span>«' . $page_category . '»</span>'; ?></h2>

        <?php if (!empty($items)) : ?>
        <ul class="lots__list">

          <?php foreach ($items as $item):
            $Timer = makeTimer(deffXSS($item['ts_end'])); ?>
            <li class="lots__item lot">
                <div class="lot__image">
                    <img src="<?= deffXSS($item['img_url']); ?>" width="350" height="260" alt="<?= deffXSS($item['name']); ?>">
                </div>
                <div class="lot__info">
                    <span class="lot__category"><?= deffXSS($item['category']); ?></span>
                    <h3 class="lot__title"><a class="text-link" href="/lot.php?itemID=<?= $item['id']; ?>"><?= deffXSS($item['name']); ?></a></h3>
                    <div class="lot__state">
                        <div class="lot__rate">
                            <span class="lot__amount"><?= $item['number_bets'] ?></span>
                            <span class="lot__cost"><?= makePriceFormat( deffXSS($item['l_price']) ); ?><b class="rub">р</b></span>
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

      <?php if (!empty($items)): ?>
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
