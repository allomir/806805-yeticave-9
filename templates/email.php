
<h1>Поздравляем с победой</h1>
<p>Здравствуйте, <?= $winBet['user_name']; ?></p>
<p>Ваша ставка для лота <a href="<?= 'http://alomir.ru/lot.php?itemID' . $winBet[item_id]; ?>"><?= $winBet['item_name']; ?></a> победила.</p>
<p>Перейдите по ссылке <a href="<?= '/my-bets.php'; ?>">мои ставки</a>,
чтобы связаться с автором объявления</p>
<small>Интернет Аукцион "YetiCave"</small>