<?php
/** @var callable $e */
/** @var array $inspections */
/** @var bool $canManage */
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Inspections</h1>
    <?php if ($canManage): ?>
        <a class="btn btn-primary" href="/inspections/create">Add inspection</a>
    <?php endif; ?>
</div>

<?php if (!$inspections): ?>
    <div class="alert alert-light border">No inspections yet.</div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
            <tr>
                <th>Date</th>
                <th>Vehicle</th>
                <th>Mileage</th>
                <th>Damage</th>
                <th>Status</th>
                <th>Notes</th>
                <?php if ($canManage): ?><th></th><?php endif; ?>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($inspections as $i): ?>
                <tr>
                    <td><?= $e($i['inspection_date']) ?></td>
                    <td><a href="/vehicles/<?= $e($i['vehicle_id']) ?>"><?= $e($i['registration']) ?></a></td>
                    <td><?= $e(number_format((int) $i['mileage'])) ?></td>
                    <td><?= !empty($i['damage_reported']) ? 'Yes' : 'No' ?></td>
                    <td class="text-capitalize"><?= $e($i['status']) ?></td>
                    <td><?= $e($i['notes'] ?: '—') ?></td>
                    <?php if ($canManage): ?>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-primary" href="/inspections/<?= $e($i['id']) ?>/edit">Edit</a>
                            <form class="d-inline" method="post" action="/inspections/<?= $e($i['id']) ?>/delete" data-confirm="Delete this inspection?">
                                <input type="hidden" name="_csrf" value="<?= $e($csrf) ?>">
                                <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                            </form>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
