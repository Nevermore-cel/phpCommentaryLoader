<?php
// Подключаем файл с логикой поиска.
require_once 'search_logic.php';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Поиск записей по комментариям</title>
    <link rel="stylesheet" href="css/style.css">

</head>
<body>
    <div class="container">
        <h1>Поиск записей по комментариям</h1>
        
        <!-- Форма поиска -->
        <form method="GET" action="" class="search-form">
            <input type="text" 
                   name="search" 
                   value="<?= htmlspecialchars(string: $searchTerm) ?>" 
                   placeholder="Введите текст для поиска (минимум 3 символа)"
                   minlength="3"
                   required>
            <button type="submit">Найти</button>
        </form>
        
        <?php if (strlen(string: $searchTerm) >= 3): ?>
            <div class="results">
                <?php if (empty($results)): ?>
                    <p class="no-results">По вашему запросу "<?= htmlspecialchars(string: $searchTerm) ?>" ничего не найдено</p>
                <?php else: ?>
                    <h2>Результаты поиска для "<?= htmlspecialchars(string: $searchTerm) ?>"</h2>
                    <?php 
                    $currentPostId = null; // Переменная для отслеживания текущего поста
                    foreach ($results as $result): 
                        // Если ID поста изменился, закрываем предыдущий div.post и открываем новый
                        if ($currentPostId !== $result['id']):
                            if ($currentPostId !== null) echo '</div>'; // Закрываем предыдущий пост, если он был
                            $currentPostId = $result['id'];
                    ?>
                        <div class="post">
                            <!-- Отображаем заголовк поста -->
                            <h3>Пост: <?= htmlspecialchars(string: $result['title']) ?></h3>
                    <?php endif; ?>
                        <!-- Отображаем комментарий -->
                        <div class="comment">
                            <div class="comment-author">
                                <strong><?= htmlspecialchars(string: $result['comment_author']) ?>:</strong>
                                <?php if (!empty($result['comment_email'])): ?>
                                    <!-- Ссылка на email -->
                                    <a href="mailto:<?= htmlspecialchars(string: $result['comment_email']) ?>" class="comment-email-link"><?= htmlspecialchars(string: $result['comment_email']) ?></a>
                                <?php endif; ?>
                            </div>
                            <div class="comment-body">
                                <?php
                                // Подсветка найденного текста в теле комментария
                                // preg_quote экранирует специальные символы в searchTerm, чтобы они не сломали регулярное выражение.
                                // preg_replace заменяет найденное вхождение на <mark>найденное_вхождение</mark>.
                                // '/i' делает поиск регистронезависимым.
                                // htmlspecialchars используется для безопасности, чтобы предотвратить XSS.
                                $highlightedBody = preg_replace(
                                    pattern: '/' . preg_quote(str: $searchTerm, delimiter: '/') . '/i', // Шаблон поиска
                                    replacement: '<mark>$0</mark>', // Замена ( $0 - это вся найденная подстрока )
                                    subject: htmlspecialchars(string: $result['comment_body']) // Исходный текст, экранированный
                                );
                                echo $highlightedBody;
                                ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php elseif (!empty($searchTerm)): ?>
            <!-- Сообщение, если поисковый запрос был, но короче 3 символов -->
            <p class="warning">Введите минимум 3 символа для поиска</p>
        <?php endif; ?>
    </div>
</body>
</html>