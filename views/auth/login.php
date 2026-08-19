<?php
/** @var callable $e */
/** @var array $errors */
?>
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h1 class="h4 mb-1">Sign in</h1>
                <p class="text-muted small mb-4">Use your company account. Access is limited to your business <code>levelId</code>.</p>
                <form method="post" action="/login" novalidate>
                    <input type="hidden" name="_csrf" value="<?= $e($csrf) ?>">
                    <div class="mb-3">
                        <label class="form-label" for="email">Email</label>
                        <input class="form-control" type="email" name="email" id="email" required autocomplete="username">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="password">Password</label>
                        <input class="form-control" type="password" name="password" id="password" required autocomplete="current-password">
                    </div>
                    <button class="btn btn-primary w-100" type="submit">Sign in</button>
                </form>
            </div>
        </div>
    </div>
</div>
