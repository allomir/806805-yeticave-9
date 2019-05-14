    <nav class="nav">
      <ul class="nav__list container">

        <?php /* Главное меню - все страницы кроме главной */
        foreach ($categories as $category) : ?>
        <li class="nav__item">
          <a href="/all-lots.php?categoryID=<?= $category['id'] ?>"><?= htmlspecialchars($category['name']); ?></a>
        </li>
        <?php endforeach; ?>

      </ul>
    </nav>
    <div class="container">
      <section class="lots">
        <?php $whatIsThis = empty($items) ? 'Ничего не найдено по вашему запросу' : 'Результаты поиска по запросу: '; ?>
        <h2><?= $whatIsThis ?> <?= !empty($search) ? '«<span>' . $search . '</span>»' : ''; ?></h2>
        <ul class="lots__list">
        <?php 
        /* Блоки с лотами */
        foreach ($items as $item):
        // функция таймер
        $Timer = makeTimer(htmlspecialchars($item['ts_end'])); 
        ?>
            <li class="lots__item lot">
                <div class="lot__image">
                    <img src="<?= htmlspecialchars($item['img_url']); ?>" width="350" height="260" alt="<?= htmlspecialchars($item['name']); ?>">
                </div>
                <div class="lot__info">
                    <span class="lot__category"><?= htmlspecialchars($item['category']); ?></span>
                    <h3 class="lot__title"><a class="text-link" href="/lot.php?itemID=<?= $item['id']; ?>"><?= htmlspecialchars($item['name']); ?></a></h3>
                    <div class="lot__state">
                        <div class="lot__rate">
                            <span class="lot__amount"><?= $item['number_bets'] ?></span>
                            <span class="lot__cost"><?= makePriceFormat( htmlspecialchars($item['l_price']) ); ?><b class="rub">р</b></span>
                        </div>
                        <div class="lot__timer timer <?= $Timer['style']; ?>">
                            <?= $Timer['DDHHMM']; ?>
                        </div>
                    </div>
                </div>
            </li>
        <?php endforeach; ?>
        </ul>
      </section>
      <ul class="pagination-list">

    <?php if ($num_pages) : ?>
        <?php /* Пагинация */
        $params = $_GET ?? []; 
        if ($num_pages >= 2) : 
            $params = $_GET ?? [];
            $params['page'] = !empty($params['page']) ? $params['page']-1 : 1;
            if ($params['page'] - 1 < 1) {
                $params['page'] = 1;
            } 
            $row_get = http_build_query($params); ?>
        <li class="pagination-item pagination-item-prev"><a href="/search.php?<?= $row_get; ?>">Назад</a></li>
        <?php endif;
        $i=1; 
        $params = $_GET ?? [];
        $current_page = $params['page'] ?? '';

        while ($i <= $num_pages) : 
            $class_active = '';
            if ($current_page == $i OR empty($current_page)) {
                $class_active = 'pagination-item-active';
            }
            $params['page'] = $i; $row_get = http_build_query($params); 
        ?>
        <li class="pagination-item <?= $class_active; ?>"><a href="/search.php?<?= $row_get; ?>"><?= $i; ?></a></li>
        <?php 
        $i++;  endwhile; 

        if ($num_pages >= 2) : 
            $params = $_GET ?? [];
            $params['page'] = !empty($params['page']) ? $params['page']+1 : 2;
            if ($params['page'] + 1 > $num_pages) {
                $params['page'] = $num_pages;
            } 
            $row_get = http_build_query($params); ?>
        <li class="pagination-item pagination-item-next"><a href="/search.php?<?= $row_get; ?>">Вперед</a></li>
        <?php endif; ?>
    <?php endif; ?>

      </ul>
    </div>