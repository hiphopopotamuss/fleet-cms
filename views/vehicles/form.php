<?php
/** @var callable $e */
/** @var array $vehicle */
/** @var array $errors */
/** @var string $mode */
?>
<a class="small" href="/vehicles">← Vehicles</a>
<h1 class="h3 mb-3"><?= $mode === 'create' ? 'Add vehicle' : 'Edit vehicle' ?></h1>

<form method="post" action="<?= $mode === 'create' ? '/vehicles' : '/vehicles/' . $e($vehicle['id'] ?? '') ?>" class="card card-body shadow-sm" novalidate>
    <input type="hidden" name="_csrf" value="<?= $e($csrf) ?>">
    <div class="row g-3">
        <div class="col-md-4">
            <label class="form-label" for="registration">Registration</label>
            <input class="form-control <?= isset($errors['registration']) ? 'is-invalid' : '' ?>" name="registration" id="registration" value="<?= $e($vehicle['registration'] ?? '') ?>" required>
            <?php if (isset($errors['registration'])): ?><div class="invalid-feedback"><?= $e($errors['registration']) ?></div><?php endif; ?>
        </div>
        <div class="col-md-4">
            <label class="form-label" for="make">Make</label>
            <input class="form-control <?= isset($errors['make']) ? 'is-invalid' : '' ?>" name="make" id="make" value="<?= $e($vehicle['make'] ?? '') ?>" required>
            <?php if (isset($errors['make'])): ?><div class="invalid-feedback"><?= $e($errors['make']) ?></div><?php endif; ?>
        </div>
        <div class="col-md-4">
            <label class="form-label" for="model">Model</label>
            <input class="form-control <?= isset($errors['model']) ? 'is-invalid' : '' ?>" name="model" id="model" value="<?= $e($vehicle['model'] ?? '') ?>" required>
            <?php if (isset($errors['model'])): ?><div class="invalid-feedback"><?= $e($errors['model']) ?></div><?php endif; ?>
        </div>
        <div class="col-md-3">
            <label class="form-label" for="year">Year</label>
            <input class="form-control <?= isset($errors['year']) ? 'is-invalid' : '' ?>" type="number" name="year" id="year" value="<?= $e($vehicle['year'] ?? '') ?>" required>
            <?php if (isset($errors['year'])): ?><div class="invalid-feedback"><?= $e($errors['year']) ?></div><?php endif; ?>
        </div>
        <div class="col-md-3">
            <label class="form-label" for="mileage">Mileage</label>
            <input class="form-control <?= isset($errors['mileage']) ? 'is-invalid' : '' ?>" type="number" name="mileage" id="mileage" value="<?= $e($vehicle['mileage'] ?? '') ?>" required>
            <?php if (isset($errors['mileage'])): ?><div class="invalid-feedback"><?= $e($errors['mileage']) ?></div><?php endif; ?>
        </div>
        <div class="col-md-3">
            <label class="form-label" for="mot_expiry">MOT expiry</label>
            <input class="form-control <?= isset($errors['mot_expiry']) ? 'is-invalid' : '' ?>" type="date" name="mot_expiry" id="mot_expiry" value="<?= $e($vehicle['mot_expiry'] ?? '') ?>">
            <?php if (isset($errors['mot_expiry'])): ?><div class="invalid-feedback"><?= $e($errors['mot_expiry']) ?></div><?php endif; ?>
        </div>
        <div class="col-md-3">
            <label class="form-label" for="tax_expiry">Tax expiry</label>
            <input class="form-control <?= isset($errors['tax_expiry']) ? 'is-invalid' : '' ?>" type="date" name="tax_expiry" id="tax_expiry" value="<?= $e($vehicle['tax_expiry'] ?? '') ?>">
            <?php if (isset($errors['tax_expiry'])): ?><div class="invalid-feedback"><?= $e($errors['tax_expiry']) ?></div><?php endif; ?>
        </div>
        <div class="col-md-4">
            <label class="form-label" for="status">Status</label>
            <select class="form-select <?= isset($errors['status']) ? 'is-invalid' : '' ?>" name="status" id="status">
                <?php foreach (['active' => 'Active', 'inactive' => 'Inactive', 'maintenance' => 'Maintenance'] as $value => $label): ?>
                    <option value="<?= $e($value) ?>" <?= (($vehicle['status'] ?? '') === $value) ? 'selected' : '' ?>><?= $e($label) ?></option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($errors['status'])): ?><div class="invalid-feedback"><?= $e($errors['status']) ?></div><?php endif; ?>
        </div>
    </div>
    <div class="mt-4">
        <button class="btn btn-primary" type="submit">Save</button>
        <a class="btn btn-link" href="/vehicles">Cancel</a>
    </div>
</form>
