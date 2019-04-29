    
    <section class="promo">
        <h2 class="promo__title">Нужен стафф для катки?</h2>
        <p class="promo__text">На нашем интернет-аукционе ты найдёшь самое эксклюзивное сноубордическое и горнолыжное снаряжение.</p>
        <ul class="promo__list">

            <?php foreach ($categories as $symbol => $category): ?>
            <li class="promo__item promo__item--<?= $symbol?>">
                <!-- Защита от XSS -->
                <a class="promo__link" href="pages/all-lots.html"><?= htmlspecialchars($category); ?></a>
            </li>
            <?php endforeach; ?>

        </ul>
    </section>
    <section class="lots">
        <div class="lots__header">
            <h2>Открытые лоты</h2>
        </div>
        <ul class="lots__list">

            <?php foreach ($items as $item): 
                $bet = getLastPrice($item[id], $item[price]); // функция возвращает массив $bet - id лота, кол-во ставок number_bets, last_price
            ?>
            <li class="lots__item lot">
                <div class="lot__image">
                    <!-- Защита от XSS -->
                    <img src="<?= htmlspecialchars($item['img_url']); ?>" width="350" height="260" alt="<?= htmlspecialchars($item['name']); ?>">
                </div>
                <div class="lot__info">
                    <!-- Защита от XSS -->
                    <span class="lot__category"><?= htmlspecialchars($item['category']); ?></span>
                    <!-- Защита от XSS -->
                    <h3 class="lot__title"><a class="text-link" href="pages/lot.html"><?= htmlspecialchars($item['name']); ?></a></h3>
                    <div class="lot__state">
                        <div class="lot__rate">
                            <span class="lot__amount"><?= $bet[number_bets] ?></span>
                            <span class="lot__cost"><?= makePriceFormat( htmlspecialchars($bet[last_price]) ); ?></span>
                        </div>
                        <div class="lot__timer timer <?= makeTimer( htmlspecialchars($item['ts_end']) )[1]; ?>">
                            <?= makeTimer( htmlspecialchars($item['ts_end']) )[0]; ?>
                        </div>
                    </div>
                </div>
            </li>
            <?php endforeach; ?>

        </ul>
    </section>