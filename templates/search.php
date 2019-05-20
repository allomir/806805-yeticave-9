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
        <?php $whatIsThis = empty($items) ? 'Ничего не найдено по вашему запросу' : 'Результаты поиска по запросу: '; ?>
        <h2><?= $whatIsThis ?> <?= !empty($search) ? '<span>«' . $search . '»</span>' : ''; ?></h2>

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

        <?php if ($num_pages >= 2) : ?>
        <ul class="pagination-list">
            <?php 
            $params = $_GET ?? [];
            $link_back = !empty($params['page']) ? $params['page'] - 1 : 0;
            
            if ($link_back < 1) {
                $row_get = '';
            } else {
                $params['page'] = $link_back;
                $row_get = http_build_query($params); 
            }           
            ?>
            <li class="pagination-item pagination-item-prev"><a <?= !empty($row_get) ? ' href="/search.php?' . $row_get : ' style="color: #789"'; ?>">Назад</a></li>
            
            <?php
            $i=1;
            $params = $_GET ?? [];
            $current_link = $params['page'] ?? 1;
            while ($i <= $num_pages) :
                $class_active = '';

                if ($current_link == $i) {
                    $class_active = 'pagination-item-active';
                }

                $params['page'] = $i;
                $row_get = http_build_query($params);
            ?>

            <li class="pagination-item <?= $class_active; ?>"><a href="/search.php?<?= $row_get; ?>"><?= $i; ?></a></li>
            <?php
                $i++;
            endwhile;

            $params = $_GET ?? [];
            $link_next = !empty($params['page']) ? $params['page'] + 1 : 2;

            if ($link_next > $num_pages) {
                $row_get = '';
            } else {
                $params['page'] = $link_next;
                $row_get = http_build_query($params); 
            }   
            ?>
            <li class="pagination-item pagination-item-next"><a <?= !empty($row_get) ? ' href="/search.php?' . $row_get : ' style="color: #899"'; ?>">Вперед</a></li>
        </ul>
        <?php endif; ?>

    </div>