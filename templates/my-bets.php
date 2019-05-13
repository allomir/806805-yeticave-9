
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
        $user_contacts = '';

        if (strtotime($bet['ts_end']) < time()) {
            if(!empty($bet['winner_id'])) {
                $class_bet = 'rates__item--win';
                $timer_style = 'timer--win';
                $timer = 'Ставка выиграла';
                $user_contacts = '<p>' . $bet['contacts'] . '</p>';
            }
            else {
                $class_bet = 'rates__item--end';
                $timer_style = 'timer--end';
                $timer = 'Торги окончены';
            }
        }
        ?>

        <tr class="rates__item <?= $class_bet; ?>">
          <td class="rates__info">
            <div class="rates__img">
              <img src="<?= $bet['img_url']; ?>" width="54" height="40" alt="<?= $bet['item_name']; ?>">
            </div>
            <div>
            <h3 class="rates__title"><a href="/lot.php?itemID=<?= $bet['item_id']; ?>"><?= $bet['item_name']; ?></a></h3>
            <p><?= $user_contacts; ?></p>
            </div>
          </td>
          <td class="rates__category">
          <?= $bet['category']; ?>
          </td>
          <td class="rates__timer">
            <div class="timer <?= $timer_style; ?>"><?= $timer; ?></div>
          </td>
          <td class="rates__price">
            <?= makePriceFormat(htmlspecialchars($bet['bet_price'])); ?> р
          </td>
          <td class="rates__time">
            <?= makeBacktime(htmlspecialchars($bet['ts_betted'])); ?>
          </td>
        </tr>
        <?php endforeach; ?>

      </table>
    </section>