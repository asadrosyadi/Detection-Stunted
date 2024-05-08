<div class="page-breadcrumb">
    <div class="row">
        <div class="col-7 align-self-center">
            <h1 class="page-title text-truncate text-dark font-weight-medium mb-1">&nbsp; &nbsp; &nbsp; <?= $title; ?></h1>
            <div class="d-flex align-items-center">
                <nav aria-label="breadcrumb">
                </nav>
            </div>
        </div>
        <div class="col-5 align-self-center text-right">
            <?php foreach ($data2 as $u) : ?>
                <h7 class="ml-auto">Last Update : <?= $u->time ?> &nbsp; &nbsp; &nbsp;</h7>
            <?php endforeach; ?>
            <a href="<?= base_url('data/reset_data'); ?>" class="btn btn-danger">Reset Data</a> &nbsp; &nbsp; &nbsp;<!-- Tombol Reset Data -->
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="table table-hover">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="datatable" class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Time</th>
                                    <th>Mass (kg)</th>
                                    <th>Height (cm)</th>
                                    <th>Head Circumference (cm)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                foreach ($data as $u) { ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= $u->time ?></td>
                                        <td><?= $u->mass ?></td>
                                        <td><?= $u->height ?></td>
                                        <td><?= $u->head ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('#datatable').DataTable({
            dom: 'Bfrtip',
            buttons: [
                'copy', 'excel', 'pdf', 'csv'
            ]
        });
    });
</script>