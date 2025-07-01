<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<div class="container mt-4">
    <h2>Edit Diskon</h2>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger">
            <?php foreach(session()->getFlashdata('errors') as $e): ?>
                <p><?= esc($e) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form action="/diskon/update/<?= $diskon['id'] ?>" method="post">
        <div class="mb-3">
            <label for="tanggal" class="form-label">Tanggal</label>
            <input type="date" class="form-control" id="tanggal" name="tanggal" value="<?= $diskon['tanggal'] ?>" readonly>
        </div>

        <div class="mb-3">
            <label for="nominal" class="form-label">Nominal</label>
            <input type="number" class="form-control" id="nominal" name="nominal" value="<?= $diskon['nominal'] ?>">
        </div>

        <button type="submit" class="btn btn-success">Update</button>
        <a href="/diskon" class="btn btn-secondary">Kembali</a>
    </form>
</div>

<?= $this->endSection() ?>
