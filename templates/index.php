    
    <section class="promo">
        <h2 class="promo__title">Нужен стафф для катки?</h2>
        <p class="promo__text">На нашем интернет-аукционе ты найдёшь самое эксклюзивное сноубордическое и горнолыжное снаряжение.</p>
        <ul class="promo__list">

            <?php foreach ($categories as $category): ?>
            <li class="promo__item promo__item--boards">
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

            <?php foreach ($items as $item): ?>
            <li class="lots__item lot">
                <div class="lot__image">
                    <!-- Защита от XSS -->
                    <img src="<?= htmlspecialchars($item['imgURL']); ?>" width="350" height="260" alt="<?= htmlspecialchars($item['name']); ?>">
                </div>
                <div class="lot__info">
                    <!-- Защита от XSS -->
                    <span class="lot__category"><?= htmlspecialchars($item['category']); ?></span>
                    <!-- Защита от XSS -->
                    <h3 class="lot__title"><a class="text-link" href="pages/lot.html"><?= htmlspecialchars($item['name']); ?></a></h3>
                    <div class="lot__state">
                        <div class="lot__rate">
                            <span class="lot__amount">Стартовая цена</span>
                            <span class="lot__cost"><?= makePriceFormat(htmlspecialchars($item['price'])); ?></span>
                        </div>
                        <div class="lot__timer timer">
                            12:23
                        </div>
                    </div>
                </div>
            </li>
            <?php endforeach; ?>

        </ul>
    </section>