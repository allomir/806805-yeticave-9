
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
    <div class="container">
      <section class="lots">
        <h2>Все лоты в категории <span>«<?= $page_name ?>»</span></h2>
        <ul class="lots__list">

          <?php 
            /* Показ лотов по категории */
            if ($items):
            foreach ($items as $item):
            // функция таймер 
            $Timer = makeTimer(deffXSS($item['ts_end'])); 
          ?>
            <li class="lots__item lot">
                <div class="lot__image">
                    <img src="<?= deffXSS($item['img_url']); ?>" width="350" height="260" alt="<?= deffXSS($item['name']); ?>">
                </div>
                <div class="lot__info">
                    <span class="lot__category"><?= deffXSS($item['category']); ?></span>
                    <h3 class="lot__title"><a class="text-link" href="lot.php?itemID=<?= $item['id']; ?>"><?= deffXSS($item['name']); ?></a></h3>
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
          <?php endforeach; 
          else : print('В данный момент лоты отсутствуют'); endif; 
          ?>

        </ul>
      </section>

      <?php if ($items): ?>
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
