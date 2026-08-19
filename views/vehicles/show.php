<?php
/** @var callable $e */
/** @var array $vehicle */
/** @var array $inspections */
/** @var bool $canManage */
/** @var bool $canInspect */
?>
<div class="d-flex justify-content-between align-items-start mb-3">
    <div>
        <a class="small" href="/vehicles">← Vehicles</a>
        <h1 class="h3 mb-0"><?= $e($vehicle['registration']) ?></h1>
        <p class="text-muted mb-0"><?= $e($vehicle['make']) ?> <?= $e($vehicle['model']) ?> · <?= $e($vehicle['year']) ?></p>
    </div>
    <?php if ($canManage): ?>
        <div class="d-flex gap-2">
            <a class="btn btn-outline-primary" href="/vehicles/<?= $e($vehicle['id']) ?>/edit">Edit</a>
            <form method="post" action="/vehicles/<?= $e($vehicle['id']) ?>/delete" data-confirm="Delete this vehicle?">
                <input type="hidden" name="_csrf" value="<?= $e($csrf) ?>">
                <button class="btn btn-outline-danger" type="submit">Delete</button>
            </form>
        </div>
    <?php endif; ?>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="card"><div class="card-body"><div class="small text-muted">Mileage</div><strong><?= $e(number_format((int) $vehicle['mileage'])) ?></strong></div></div></div>
    <div class="col-md-3"><div class="card"><div class="card-body"><div class="small text-muted">MOT expiry</div><strong><?= $e($vehicle['mot_expiry'] ?: '—') ?></strong></div></div></div>
    <div class="col-md-3"><div class="card"><div class="card-body"><div class="small text-muted">Tax expiry</div><strong><?= $e($vehicle['tax_expiry'] ?: '—') ?></strong></div></div></div>
    <div class="col-md-3"><div class="card"><div class="card-body"><div class="small text-muted">Status</div><strong class="text-capitalize"><?= $e($vehicle['status']) ?></strong></div></div></div>
</div>

<div class="d-flex justify-content-between align-items-center mb-2">
    <h2 class="h5 mb-0">Inspections</h2>
    <?php if ($canInspect): ?>
        <a class="btn btn-sm btn-primary" href="/inspections/create?vehicle_id=<?= $e($vehicle['id']) ?>">Add inspection</a>
    <?php endif; ?>
</div>

<?php if (!$inspections): ?>
    <p class="text-muted">No inspections recorded.</p>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-sm">
            <thead><tr><th>Date</th><th>Mileage</th><th>Damage</th><th>Status</th><th>Notes</th></tr></thead>
            <tbody>
            <?php foreach ($inspections as $i): ?>
                <tr>
                    <td><?= $e($i['inspection_date']) ?></td>
                    <td><?= $e(number_format((int) $i['mileage'])) ?></td>
                    <td><?= !empty($i['damage_reported']) ? 'Yes' : 'No' ?></td>
                    <td class="text-capitalize"><?= $e($i['status']) ?></td>
                    <td><?= $e($i['notes'] ?: '—') ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
