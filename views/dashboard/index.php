<?php
/** @var callable $e */
/** @var int $vehicleCount */
/** @var int $inspectionCount */
/** @var array $upcomingMot */
/** @var array $user */
?>
<h1 class="h3 mb-3">Dashboard</h1>
<p class="text-muted">You are signed in to <strong><?= $e($user['level']) ?></strong> tenant <strong><?= $e($user['level_id']) ?></strong>. Every query is scoped to that pair.</p>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="text-muted small">Vehicles</div>
                <div class="display-6"><?= $e($vehicleCount) ?></div>
                <a href="/vehicles">View fleet</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="text-muted small">Inspections</div>
                <div class="display-6"><?= $e($inspectionCount) ?></div>
                <a href="/inspections">View inspections</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="text-muted small">Your role</div>
                <div class="h4 text-capitalize mb-0"><?= $e($user['role']) ?></div>
                <p class="small text-muted mb-0 mt-2">
                    <?php if ($user['role'] === 'admin'): ?>Manage vehicles and inspections.<?php endif; ?>
                    <?php if ($user['role'] === 'manager'): ?>Manage inspections. Vehicles are read-only.<?php endif; ?>
                    <?php if ($user['role'] === 'driver'): ?>View-only access to your company’s fleet.<?php endif; ?>
                </p>
            </div>
        </div>
    </div>
</div>

<h2 class="h5">MOT due within 30 days</h2>
<?php if (!$upcomingMot): ?>
    <p class="text-muted">Nothing due soon.</p>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-sm align-middle">
            <thead><tr><th>Registration</th><th>MOT expiry</th></tr></thead>
            <tbody>
            <?php foreach ($upcomingMot as $v): ?>
                <tr>
                    <td><a href="/vehicles/<?= $e($v['id']) ?>"><?= $e($v['registration']) ?></a></td>
                    <td><?= $e($v['mot_expiry']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
