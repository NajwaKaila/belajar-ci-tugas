<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<h4>Bukti Pembayaran</h4>
<hr>

<form action="<?= base_url('upload-bukti/' . $transaksi['id']) ?>" method="post" enctype="multipart/form-data">
    <div class="mb-3">
        <label for="bukti" class="form-label">Pilih File Bukti Transfer</label>
        <input type="file" class="form-control" name="bukti" required>
    </div>
    <button type="submit" class="btn btn-primary">Upload</button>
</form>

<?= $this->endSection() ?>
