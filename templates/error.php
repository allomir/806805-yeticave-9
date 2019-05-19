
        <nav class="nav">
            <ul class="nav__list container">

            <?php foreach ($categories as $category) : ?>
            <li class="nav__item">
                <a href="/all-lots.php?categoryID=<?= $category['id']; ?>"><?= deffXSS($category['name']); ?></a>
            </li>
            <?php endforeach; ?>

            </ul>
        </nav>

        <?php if (isset($page_error)) : ?>
            <?php if ($page_error == '404') : ?>
            <section class="lot-item container">
                <h2>404 Страница не найдена</h2>
                <p>Данной страницы не существует на сайте.</p>
            </section>
            <?php endif; ?>

            <?php if ($page_error == '403') : ?>
            <section class="lot-item container">
                <h2>403 Ошибка доступа</h2>
                <p>Для просмотра страницы авторизуйтесь</p>
            </section>
            <?php endif; ?>

            <?php if ($page_error == 'login') : ?>
            <section class="lot-item container">
                <h2>Добро пожаловать,  <?= $_SESSION["user"]["name"]; ?></h2>
                <p>Успешных ставок!</p>
            </section>
            <?php endif; ?>

        <?php endif; ?>