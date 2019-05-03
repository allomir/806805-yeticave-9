    
    <section class="promo">
        <h2 class="promo__title">Нужен стафф для катки?</h2>
        <p class="promo__text">На нашем интернет-аукционе ты найдёшь самое эксклюзивное сноубордическое и горнолыжное снаряжение.</p>
        <ul class="promo__list">

            <?php /* Главное меню - главная страница */
            foreach ($categories as $category): ?>
            <li class="promo__item promo__item--<?= htmlspecialchars($category['symbol']); ?>">
                <!-- Защита от XSS -->
                <a class="promo__link" href="all-lots.php"><?= htmlspecialchars($category['name']); ?></a>
            </li>
            <?php endforeach; ?>

        </ul>
    </section>
    <section class="lots">
        <div class="lots__header">
            <h2>Открытые лоты</h2>
        </div>
        <ul class="lots__list">

            <?php 
            // Показ элементов (лотов) страницы
            foreach ($items as $item):
            
            // функция последняя цена и колво ставок, кол-во ставок - number_bets, стартовая цена или послед ставка - last_price
            $betsPrices = getBetsPrices($item['id'], $item['price']); 
            ?>

            <li class="lots__item lot">
                <div class="lot__image">
                    <img src="<?= htmlspecialchars($item['img_url']); ?>" width="350" height="260" alt="<?= htmlspecialchars($item['name']); ?>">
                </div>
                <div class="lot__info">
                    <span class="lot__category"><?= htmlspecialchars($item['category']); ?></span>
                    <h3 class="lot__title"><a class="text-link" href="lot.php?itemID=<?= $item['id']; ?>"><?= htmlspecialchars($item['name']); ?></a></h3>
                    <div class="lot__state">
                        <div class="lot__rate">
                            <span class="lot__amount"><?= $betsPrices['number_bets'] ?></span>
                            <span class="lot__cost"><?= makePriceFormat( htmlspecialchars($betsPrices['l_price']) ); ?><b class="rub">р</b></span>
                        </div>

                        <?php $Timer = makeTimer(htmlspecialchars($item['ts_end'])); /* функция таймер */ ?>

                        <div class="lot__timer timer <?= $Timer['style']; ?>">
                            <?= $Timer['DDHHMM']; ?>
                        </div>
                    </div>
                </div>
            </li>

            <?php endforeach; ?>

        </ul>
    </section>