
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
        <section class="lot-item container">
            <h2>404 Страница не найдена</h2>
            <p>Данной страницы не существует на сайте.</p>
        </section>
