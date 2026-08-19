<?php
/** @var callable $e */
/** @var array $inspection */
/** @var array $vehicles */
/** @var array $errors */
/** @var string $mode */
?>
<a class="small" href="/inspections">← Inspections</a>
<h1 class="h3 mb-3"><?= $mode === 'create' ? 'Add inspection' : 'Edit inspection' ?></h1>

<?php if (!$vehicles): ?>
    <div class="alert alert-warning">Add a vehicle before recording an inspection.</div>
<?php else: ?>
<form method="post" action="<?= $mode === 'create' ? '/inspections' : '/inspections/' . $e($inspection['id'] ?? '') ?>" class="card card-body shadow-sm" novalidate>
    <input type="hidden" name="_csrf" value="<?= $e($csrf) ?>">
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label" for="vehicle_id">Vehicle</label>
            <select class="form-select <?= isset($errors['vehicle_id']) ? 'is-invalid' : '' ?>" name="vehicle_id" id="vehicle_id" required>
                <option value="">Choose…</option>
                <?php foreach ($vehicles as $v): ?>
                    <option value="<?= $e($v['id']) ?>" <?= ((string) ($inspection['vehicle_id'] ?? '') === (string) $v['id']) ? 'selected' : '' ?>>
                        <?= $e($v['registration']) ?> — <?= $e($v['make']) ?> <?= $e($v['model']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($errors['vehicle_id'])): ?><div class="invalid-feedback"><?= $e($errors['vehicle_id']) ?></div><?php endif; ?>
        </div>
        <div class="col-md-3">
            <label class="form-label" for="inspection_date">Inspection date</label>
            <input class="form-control <?= isset($errors['inspection_date']) ? 'is-invalid' : '' ?>" type="date" name="inspection_date" id="inspection_date" value="<?= $e($inspection['inspection_date'] ?? '') ?>" required>
            <?php if (isset($errors['inspection_date'])): ?><div class="invalid-feedback"><?= $e($errors['inspection_date']) ?></div><?php endif; ?>
        </div>
        <div class="col-md-3">
            <label class="form-label" for="mileage">Mileage</label>
            <input class="form-control <?= isset($errors['mileage']) ? 'is-invalid' : '' ?>" type="number" name="mileage" id="mileage" value="<?= $e($inspection['mileage'] ?? '') ?>" required>
            <?php if (isset($errors['mileage'])): ?><div class="invalid-feedback"><?= $e($errors['mileage']) ?></div><?php endif; ?>
        </div>
        <div class="col-md-4">
            <label class="form-label" for="status">Status</label>
            <select class="form-select <?= isset($errors['status']) ? 'is-invalid' : '' ?>" name="status" id="status">
                <?php foreach (['pending' => 'Pending', 'pass' => 'Pass', 'fail' => 'Fail'] as $value => $label): ?>
                    <option value="<?= $e($value) ?>" <?= (($inspection['status'] ?? '') === $value) ? 'selected' : '' ?>><?= $e($label) ?></option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($errors['status'])): ?><div class="invalid-feedback"><?= $e($errors['status']) ?></div><?php endif; ?>
        </div>
        <div class="col-md-8 d-flex align-items-end">
            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" name="damage_reported" value="1" id="damage_reported" <?= !empty($inspection['damage_reported']) ? 'checked' : '' ?>>
                <label class="form-check-label" for="damage_reported">Damage reported</label>
            </div>
        </div>
        <div class="col-12">
            <label class="form-label" for="notes">Notes</label>
            <textarea class="form-control <?= isset($errors['notes']) ? 'is-invalid' : '' ?>" name="notes" id="notes" rows="4"><?= $e($inspection['notes'] ?? '') ?></textarea>
            <?php if (isset($errors['notes'])): ?><div class="invalid-feedback"><?= $e($errors['notes']) ?></div><?php endif; ?>
        </div>
    </div>
    <div class="mt-4">
        <button class="btn btn-primary" type="submit">Save</button>
        <a class="btn btn-link" href="/inspections">Cancel</a>
    </div>
</form>
<?php endif; ?>
