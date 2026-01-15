<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

require __DIR__ . '/view/metadata/head.index.php'; ?>

<body>

    <?php
    require __DIR__ . '/view/components/header.php';

    require __DIR__ . '/view/components/content.main.php';

    require __DIR__ . '/view/components/footer.php';
    ?>