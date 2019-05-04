<!-- Горизонтальное простое меню - для всех страниц кроме главной -->

            <?php foreach ($categories as $category): ?>
            <li class="nav__item">
                <a href="all-lots.php?categoryID=<?= $category['id']; ?>"><?= htmlspecialchars($category['name']); ?></a>
            </li>
            <?php endforeach; ?>
