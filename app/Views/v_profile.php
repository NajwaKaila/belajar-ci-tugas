<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
History Transaksi Pembelian <strong><?= $username ?></strong>
<hr>
<div class="table-responsive">
    <!-- Table with stripped rows -->
    <table class="table datatable">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">ID Pembelian</th>
                <th scope="col">Waktu Pembelian</th>
                <th scope="col">Total Bayar</th>
                <th scope="col">Alamat</th>
                <th scope="col">Status</th>
                <th scope="col"></th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (!empty($buy)) :
                foreach ($buy as $index => $item) :
            ?>
                    <tr>
                        <th scope="row"><?php echo $index + 1 ?></th>
                        <td><?php echo $item['id'] ?></td>
                        <td><?php echo $item['created_at'] ?></td>
                        <td><?php echo number_to_currency($item['total_harga'], 'IDR') ?></td>
                        <td><?php echo $item['alamat'] ?></td>
                        <td><?php echo ($item['status'] == "1") ? "Sudah Selesai" : "Belum Selesai" ?></td>
                        <td>
                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#detailModal-<?= $item['id'] ?>">
                                Detail
                            </button>
                        </td>
                    </tr>
                    <!-- Detail Modal Begin -->
                    <div class="modal fade" id="detailModal-<?= $item['id'] ?>" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Detail Data</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="modal-body">
  <?php if (!empty($product[$item['id']])) : ?>
      <?php foreach ($product[$item['id']] as $idx => $detail) : ?>
          <div class="border rounded p-2 mb-2">
              <div class="fw-bold mb-2">
  <?= ($idx + 1) ?> <?= $detail['nama'] ?>
  <span class="text-primary">IDR <?= number_format($detail['harga'], 0, ',', '.') ?></span>
</div>

<?php if (!empty($detail['foto'])) : ?>
  <div class="mb-2">
    <img src="<?= base_url('uploads/' . $detail['foto']) ?>" alt="<?= $detail['nama'] ?>" class="img-thumbnail" style="max-height: 100px;">
  </div>
<?php endif; ?>

              <div class="text-muted">(<?= $detail['jumlah'] ?> pcs)</div>
              <?php 
                $diskon = $detail['diskon'] ?? 0;
                $harga_asli = $detail['harga'];
                $harga_setelah_diskon = $harga_asli - $diskon;
              ?>
              <div><strong>Harga Setelah Diskon:</strong> 
                IDR <?= number_format($harga_setelah_diskon, 0, ',', '.') ?>
              </div>
          </div>
      <?php endforeach; ?>
  <?php endif; ?>
</div>

                                  <?php foreach ($product[$item['id']] as $p): ?>
<tr>
    <td><?= $p['nama'] ?></td>
    <td><?= $p['jumlah'] ?> pcs</td>
    <td>
        <?php if ($p['diskon'] > 0): ?>
            <del>Rp <?= number_format($p['harga']) ?></del><br>
            <small class="text-success">Diskon: Rp <?= number_format($p['diskon']) ?></small><br>
            <strong>Rp <?= number_format($p['harga'] - $p['diskon']) ?></strong>
        <?php else: ?>
            Rp <?= number_format($p['harga']) ?>
        <?php endif; ?>
    </td>
    <td>Rp <?= number_format($p['subtotal_harga']) ?></td>
</tr>
<?php endforeach; ?>

                                    Ongkir <?= number_to_currency($item['ongkir'], 'IDR') ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Detail Modal End -->
            <?php
                endforeach;
            endif;
            ?>
        </tbody>
    </table>
    <!-- End Table with stripped rows -->
</div>
<?= $this->endSection() ?>