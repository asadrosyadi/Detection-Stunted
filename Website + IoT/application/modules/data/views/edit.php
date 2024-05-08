<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <?php foreach ($data2 as $d) { ?>
                        <h2 class="card-title text-center mb-4">Change Measurement</h2>
                        <?php echo form_open_multipart('data/edit', 'role="form" class="form-horizontal"'); ?>
                        <?php echo form_hidden('id', $d->id); ?>
                        <div class="mb-3 input-rounded">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" value="<?php echo $d->name ?>" class="form-control" id="name" name="name">
                        </div>
                        <div class="mb-3 input-rounded">
                            <label for="gender" class="form-label">Gender</label>
                            <div>
                                <label class="radio-inline">
                                    <input type="radio" name="gender" value="Male" <?php echo ($d->gender == "Male") ? "checked" : ""; ?>> Male
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="gender" value="Female" <?php echo ($d->gender == "Female") ? "checked" : ""; ?>> Female
                                </label>
                            </div>
                        </div>
                        <div class="mb-3 input-rounded">
                            <label for="date_brith" class="form-label">Date Birth</label>
                            <input type="date" value="<?php echo $d->date_brith ?>" class="form-control" id="date_brith" name="date_brith">
                        </div>
                        <div class="mb-3 input-rounded">
                            <label for="parents" class="form-label">Parents</label>
                            <input type="text" value="<?php echo $d->parents ?>" class="form-control" id="parents" name="parents">
                        </div>
                        <div class="mb-3 input-rounded">
                            <label for="address" class="form-label">Address</label>
                            <input type="text" value="<?php echo $d->address ?>" class="form-control" id="address" name="address">
                        </div>
                        <div class="mb-3 input-rounded">
                            <label for="date_create" class="form-label">Measurement Date</label>
                            <input type="text" value="<?php echo $d->date_create ?>" class="form-control" id="date_create" name="date_create" readonly>
                        </div>
                        <div class="mb-3 input-rounded">
                            <label for="mass" class="form-label">Mass</label>
                            <input type="text" value="<?php echo $d->mass ?>" class="form-control" id="mass" name="mass">
                        </div>
                        <div class="mb-3 input-rounded">
                            <label for="height" class="form-label">Height</label>
                            <input type="text" value="<?php echo $d->height ?>" class="form-control" id="height" name="height">
                        </div>
                        <div class="mb-3 input-rounded">
                            <label for="head" class="form-label">Head</label>
                            <input type="text" value="<?php echo $d->head ?>" class="form-control" id="head" name="head">
                        </div>
                        <div class="d-flex justify-content-center">
                            <?php echo anchor('data/', '<button type="button" class="btn btn-secondary me-3">Back</button>'); ?> <h3> </h3>
                            &nbsp; &nbsp; &nbsp; <button type="submit" name="submit" value="submit" class="btn btn-success">Change</button>
                        </div>
                    <?php } ?>
                    <?php echo form_close(); ?>
                </div>
            </div>
        </div>
    </div>
</div>