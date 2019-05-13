
    <nav class="nav">
      <ul class="nav__list container">
      <?php /* Главное меню - все страницы кроме главной */
        foreach ($categories as $category): ?>
          <li class="nav__item">
            <a href="/all-lots.php?categoryID=<?= $category['id']; ?>"><?= htmlspecialchars($category['name']); ?></a>
          </li>
        <?php endforeach; ?>
      </ul>
    </nav>
    <section class="rates container">
      <h2>Мои ставки</h2>
      <table class="rates__list">

        <?php foreach ($bets as $bet) :
        $timer = makeTimer(htmlspecialchars($bet['ts_end'])); 
        $timer_style = $timer['style'];
        $timer = $timer['DDHHMM'];
        $class_bet = '';

        if (strtotime($bet['ts_end']) < time()) {
            if(empty($bet['winner_id'])) {
                $class_bet = 'rates__item--win';
                $timer_style = 'timer--win';
                $timer = 'Ставка выиграла';
            }
            else {
                $class_tr = 'rates__item--end';
                $timer_style = 'timer--end';
                $timer = 'Торги окончены';
            }
        }
        ?>

        <tr class="rates__item $class_bet">
          <td class="rates__info">
            <div class="rates__img">
              <img src="<?= $bet['img_url']; ?>" width="54" height="40" alt="<?= $bet['item_name']; ?>">
            </div>
            <h3 class="rates__title"><a href="/lot.php?itemID=<?= $bet['item_id']; ?>"><?= $bet['item_name']; ?></a></h3>
          </td>
          <td class="rates__category">
          <?= $bet['category']; ?>
          </td>
          <td class="rates__timer <?= $timer_style; ?>">
            <div class="timer"><?= $timer; ?></div>
          </td>

          <td class="rates__price">
            <?= makePriceFormat(htmlspecialchars($bet['bet_price'])); ?> р
          </td>
          <td class="rates__time">
            <?= makeBacktime(htmlspecialchars($bet['ts_betted'])); ?>
          </td>
        </tr>
        <?php endforeach; ?>
<!--
        <tr class="rates__item rates__item--win">
          <td class="rates__info">
            <div class="rates__img">
              <img src="../img/rate3.jpg" width="54" height="40" alt="Крепления">
            </div>
            <div>
              <h3 class="rates__title"><a href="lot.html">Крепления Union Contact Pro 2015 года размер L/XL</a></h3>
              <p>Телефон +7 900 667-84-48, Скайп: Vlas92. Звонить с 14 до 20</p>
            </div>
          </td>
          <td class="rates__category">
            Крепления
          </td>
          <td class="rates__timer">
            <div class="timer timer--win">Ставка выиграла</div>
          </td>
          <td class="rates__price">
            10 999 р
          </td>
          <td class="rates__time">
            Час назад
          </td>
        </tr>

        <tr class="rates__item rates__item--end">
          <td class="rates__info">
            <div class="rates__img">
              <img src="../img/rate5.jpg" width="54" height="40" alt="Куртка">
            </div>
            <h3 class="rates__title"><a href="lot.html">Куртка для сноуборда DC Mutiny Charocal</a></h3>
          </td>
          <td class="rates__category">
            Одежда
          </td>
          <td class="rates__timer">
            <div class="timer timer--end">Торги окончены</div>
          </td>
          <td class="rates__price">
            10 999 р
          </td>
          <td class="rates__time">
            Вчера, в 21:30
          </td>
        </tr>
    -->
      </table>
    </section>