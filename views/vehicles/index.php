<?php
/** @var callable $e */
/** @var array $vehicles */
/** @var bool $canManage */
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Vehicles</h1>
    <?php if ($canManage): ?>
        <a class="btn btn-primary" href="/vehicles/create">Add vehicle</a>
    <?php endif; ?>
</div>

<?php if (!$vehicles): ?>
    <div class="alert alert-light border">No vehicles on this fleet yet.</div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
            <tr>
                <th>Registration</th>
                <th>Make / model</th>
                <th>Year</th>
                <th>Mileage</th>
                <th>MOT</th>
                <th>Tax</th>
                <th>Status</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($vehicles as $v): ?>
                <tr>
                    <td><a href="/vehicles/<?= $e($v['id']) ?>"><?= $e($v['registration']) ?></a></td>
                    <td><?= $e($v['make']) ?> <?= $e($v['model']) ?></td>
                    <td><?= $e($v['year']) ?></td>
                    <td><?= $e(number_format((int) $v['mileage'])) ?></td>
                    <td><?= $e($v['mot_expiry'] ?: '—') ?></td>
                    <td><?= $e($v['tax_expiry'] ?: '—') ?></td>
                    <td><span class="badge text-bg-secondary text-capitalize"><?= $e($v['status']) ?></span></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
