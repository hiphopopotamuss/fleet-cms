<?php
/** @var string $title */
/** @var string $content */
/** @var ?array $user */
/** @var string $csrf */
/** @var ?array $flash */
/** @var callable $e */
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?= $e($csrf) ?>">
    <title><?= $e($title) ?> · Fleet CMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/app.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-md navbar-dark app-nav mb-4">
    <div class="container">
        <a class="navbar-brand fw-semibold" href="/">Fleet CMS</a>
        <?php if ($user): ?>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav"><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="nav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="/vehicles">Vehicles</a></li>
                    <li class="nav-item"><a class="nav-link" href="/inspections">Inspections</a></li>
                </ul>
                <span class="navbar-text me-3 small">
                    <?= $e($user['name']) ?> · <?= $e($user['role']) ?> · <?= $e($user['level']) ?> #<?= $e($user['level_id']) ?>
                </span>
                <form method="post" action="/logout" class="d-inline">
                    <input type="hidden" name="_csrf" value="<?= $e($csrf) ?>">
                    <button class="btn btn-sm btn-outline-light" type="submit">Sign out</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</nav>

<main class="container pb-5">
    <?php if ($flash): ?>
        <div class="alert alert-<?= $e($flash['type']) ?> alert-dismissible fade show" role="alert">
            <?= $e($flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?= $content ?>
</main>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="/assets/js/app.js"></script>
</body>
</html>
