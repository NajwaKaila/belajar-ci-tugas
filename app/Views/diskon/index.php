<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<div class="container mt-4">
    <a href="/diskon/create" class="btn btn-primary mb-2">+ Tambah Diskon</a>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Nominal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($diskon as $key => $d): ?>
                <tr>
                    <td><?= $key + 1 ?></td>
                    <td><?= $d['tanggal'] ?></td>
                    <td>Rp <?= number_format($d['nominal']) ?></td>
                    <td>
                        <a href="/diskon/edit/<?= $d['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                        <a href="/diskon/delete/<?= $d['id'] ?>" onclick="return confirm('Hapus data ini?')" class="btn btn-sm btn-danger">Hapus</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?= $this->endSection() ?>
