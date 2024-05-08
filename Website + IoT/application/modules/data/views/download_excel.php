<?php
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Content-type: application/x-msexcel");
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=Excel_File.xls");
?>
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
                                    <th>Name</th>
                                    <th>Gender</th>
                                    <th>Birth</th>
                                    <th>Parent's Name</th>
                                    <th>Address</th>
                                    <th>Date Measure</th>
                                    <th>Age</th>
                                    <th>Measure</th>
                                    <th>Z-Score</th>
                                    <th>Result</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                foreach ($data as $u) {
                                    $birth_date = new DateTime($u->date_brith);
                                    $measurement_date = new DateTime($u->date_create);
                                    $age_interval = $measurement_date->diff($birth_date);
                                    $age_years = $age_interval->y;
                                    $age_months = $age_interval->m;
                                    $result_age_monts = ($age_years * 12) + $age_months; // hitugan bulan
                                    $age_display = $age_years . ' years, ' . $age_months . ' months'; // hitungan tahun
                                ?>
                                    <tr>
                                        <td><?php echo $no++ ?></td>
                                        <td><?php echo $u->name ?></td>
                                        <td><?php echo $u->gender ?></td>
                                        <td><?php echo $u->date_brith ?></td>
                                        <td><?php echo $u->parents ?></td>
                                        <td><?php echo $u->address ?></td>
                                        <td><?php echo $u->date_create ?></td>
                                        <td><?php echo $age_display ?></td>
                                        <td>Mass: <?php echo $u->mass ?> Kg <br /> <br /> Height: <?php echo $u->height ?> cm <br /><br /> Head: <?php echo $u->head ?> cm</td>
                                        <td>
                                            <?php
                                            $bbu = $this->db->select('*')->from('bbu')->where('umur', $result_age_monts)->where('nb', $u->gender)->get()->result();

                                            if (!empty($bbu)) {
                                                $bbu_med = $bbu[0]->Med;
                                                $bbu_min = $bbu[0]->min;
                                                $bbu_max = $bbu[0]->max;
                                                $zbbu = 0;
                                                $kesimpulan_zbbu = '';
                                                if ($u->mass == $bbu_med) {
                                                    $zbbu = 0;
                                                } else if ($u->mass <= $bbu_med) {
                                                    $zbbu = number_format((float)((($u->mass - $bbu_med) / ($bbu_med - $bbu_min))), 2, '.', '');
                                                } else if ($u->mass >= $bbu_med) {
                                                    $zbbu = number_format((float)((($u->mass - $bbu_med) / ($bbu_med - $bbu_max))), 2, '.', '');
                                                }
                                                if ($u->mass > $bbu_max) {
                                                    $kesimpulan_zbbu = 'Weight Risk';
                                                    $zbbu = $zbbu * -1;
                                                } else if ($zbbu >= 1) {
                                                    $kesimpulan_zbbu = 'Weight Risk';
                                                } else if ($zbbu <= -3) {
                                                    $kesimpulan_zbbu = 'Severely Underweight';
                                                } else if ($zbbu >= -3 && $zbbu <= -2) {
                                                    $kesimpulan_zbbu = 'Underweight';
                                                } else if ($zbbu >= -2 && $zbbu <= 1) {
                                                    $kesimpulan_zbbu = 'Normal';
                                                }
                                                echo "1. BB/U = $zbbu <b> ($kesimpulan_zbbu) </b> <br/> <br/>";

                                                $pbu_tbu = $this->db->select('*')->from('pbu_tbu')->where('umur', $result_age_monts)->where('nb', $u->gender)->get()->result();
                                                $pbu_tbu_med = $pbu_tbu[0]->Med;
                                                $pbu_tbu_min = $pbu_tbu[0]->min;
                                                $pbu_tbu_max = $pbu_tbu[0]->max;
                                                $zpbu_tbu = 0;
                                                $kesimpulan_pbu_tbu = '';
                                                if ($u->height == $pbu_tbu_med) {
                                                    $zpbu_tbu = 0;
                                                } else if ($u->height <= $pbu_tbu_med) {
                                                    $zpbu_tbu = number_format((float)((($u->height - $pbu_tbu_med) / ($pbu_tbu_med - $pbu_tbu_min))), 2, '.', '');
                                                } else if ($u->height >= $pbu_tbu_med) {
                                                    $zpbu_tbu = number_format((float)((($u->height - $pbu_tbu_med) / ($pbu_tbu_med - $pbu_tbu_max))), 2, '.', '');
                                                }
                                                if ($u->height > $pbu_tbu_max) {
                                                    $kesimpulan_pbu_tbu = 'Too High';
                                                    $zpbu_tbu = $zpbu_tbu * -1;
                                                } else if ($zpbu_tbu >= 3) {
                                                    $kesimpulan_pbu_tbu = 'Too High';
                                                } else if ($zpbu_tbu <= -3) {
                                                    $kesimpulan_pbu_tbu = 'Severely Stunted';
                                                } else if ($zpbu_tbu >= -3 && $zpbu_tbu <= -2) {
                                                    $kesimpulan_pbu_tbu = 'Stunted';
                                                } else if ($zpbu_tbu >= -2 && $zpbu_tbu <= 3) {
                                                    $kesimpulan_pbu_tbu = 'Normal';
                                                }
                                                echo "2. PB/U and TB/U = $zpbu_tbu <b> ($kesimpulan_pbu_tbu) </b> <br/> <br/>";

                                                $bulan = 0;
                                                if ($result_age_monts >= 24) {
                                                    $bulan = 2;
                                                } else {
                                                    $bulan = 0;
                                                }
                                                $bbpb_bbtb = $this->db->select('*')->from('bbpb_bbtb')->where('umur', $bulan)->where('tinggi', $u->height)->where('nb', $u->gender)->get()->result();
                                                $bbpb_bbtb_med = $bbpb_bbtb[0]->Med;
                                                $bbpb_bbtb_min = $bbpb_bbtb[0]->min;
                                                $bbpb_bbtb_max = $bbpb_bbtb[0]->max;
                                                $zbbpb_bbtb = 0;
                                                $kesimpulan_bbpb_bbtbu = '';
                                                if ($u->mass == $bbpb_bbtb_med) {
                                                    $zbbpb_bbtb = 0;
                                                } else if ($u->mass <= $bbpb_bbtb_med) {
                                                    $zbbpb_bbtb = number_format((float)((($u->mass - $bbpb_bbtb_med) / ($bbpb_bbtb_med - $bbpb_bbtb_min))), 2, '.', '');
                                                } else if ($u->mass >= $bbpb_bbtb_med) {
                                                    $zbbpb_bbtb = number_format((float)((($u->mass - $bbpb_bbtb_med) / ($bbpb_bbtb_med - $bbpb_bbtb_max))), 2, '.', '');
                                                }
                                                if ($u->mass > $bbpb_bbtb_max) {
                                                    $kesimpulan_bbpb_bbtbu = 'Overweight';
                                                    $zbbpb_bbtb = $zbbpb_bbtb * -1;
                                                }
                                                if ($zbbpb_bbtb >= 1 && $zbbpb_bbtb <= 2) {
                                                    $kesimpulan_bbpb_bbtbu = 'Possible Risk of Overweight';
                                                } else if ($zbbpb_bbtb >= 3) {
                                                    $kesimpulan_bbpb_bbtbu = 'Obese';
                                                } else if ($zbbpb_bbtb <= -3) {
                                                    $kesimpulan_bbpb_bbtbu = 'Severely Wasted';
                                                } else if ($zbbpb_bbtb >= -3 && $zbbpb_bbtb <= -2) {
                                                    $kesimpulan_bbpb_bbtbu = 'Wasted';
                                                } else if ($zbbpb_bbtb >= -2 && $zbbpb_bbtb <= 1) {
                                                    $kesimpulan_bbpb_bbtbu = 'Normal';
                                                }
                                                echo "3. BB/PB and BB/TB = $zbbpb_bbtb <b> ($kesimpulan_bbpb_bbtbu) </b><br/><br/>";

                                                $tinggi = $u->height / 100;
                                                $imtu = $this->db->select('*')->from('imtu')->where('umur', $result_age_monts)->where('nb', $u->gender)->get()->result();
                                                $imt = $u->mass /  pow($tinggi, 2);
                                                $imtu_med = $imtu[0]->Med;
                                                $imtu_min = $imtu[0]->min;
                                                $imtu_max = $imtu[0]->max;
                                                $zimtu = 0;
                                                $kesimpulan_imtu = '';
                                                if ($imt == $imtu_med) {
                                                    $zimtu = 0;
                                                } else if ($imt <= $imtu_med) {
                                                    $zimtu = number_format((float)((($imt - $imtu_med) / ($imtu_med - $imtu_min))), 2, '.', '');
                                                } else if ($imt >= $imtu_med) {
                                                    $zimtu = number_format((float)((($imt - $imtu_med) / ($imtu_med - $imtu_max))), 2, '.', '');
                                                }
                                                if ($imt > $imtu_max) {
                                                    $kesimpulan_imtu = 'Overweight';
                                                    $zimtu = $zimtu * -1;
                                                }
                                                if ($zimtu >= 1 && $zimtu <= 2) {
                                                    $kesimpulan_imtu = 'Possible Risk of Overweight';
                                                }
                                                if ($zimtu >= 3) {
                                                    $kesimpulan_imtu = 'Obese';
                                                } else if ($zimtu <= -3) {
                                                    $kesimpulan_imtu = 'Severely Wasted';
                                                } else if ($zimtu >= -3 && $zimtu <= -2) {
                                                    $kesimpulan_imtu = 'Wasted';
                                                } else if ($zimtu >= -2 && $zimtu <= 1) {
                                                    $kesimpulan_imtu = 'Normal';
                                                }
                                                echo "4. IMT/U = $zimtu <b> ($kesimpulan_imtu) </b> <br/><br/>";

                                                $headu = $this->db->select('*')->from('headu')->where('umur', $result_age_monts)->where('nb', $u->gender)->get()->result();
                                                $headu_med = $headu[0]->Med;
                                                $headu_min = $headu[0]->min;
                                                $headu_max = $headu[0]->max;
                                                $zheadu = 0;
                                                $kesimpulan_zheadu = '';
                                                if ($u->head == $headu_med) {
                                                    $zheadu = 0;
                                                } else if ($u->head <= $headu_med) {
                                                    $zheadu = number_format((float)((($u->head - $headu_med) / ($headu_med - $headu_min))), 2, '.', '');
                                                } else if ($u->head >= $headu_med) {
                                                    $zheadu = number_format((float)((($u->head - $headu_med) / ($headu_med - $headu_max))), 2, '.', '');
                                                }
                                                if ($u->head > $headu_max) {
                                                    $kesimpulan_zheadu = 'Weight Risk';
                                                    $zheadu = $zheadu * -1;
                                                } else if ($zheadu >= 1) {
                                                    $kesimpulan_zheadu = 'Weight Risk';
                                                } else if ($zheadu <= -3) {
                                                    $kesimpulan_zheadu = 'Severely Underweight';
                                                } else if ($zheadu >= -3 && $zheadu <= -2) {
                                                    $kesimpulan_zheadu = 'Underweight';
                                                } else if ($zheadu >= -2 && $zheadu <= 1) {
                                                    $kesimpulan_zheadu = 'Normal';
                                                }
                                                echo "5. Head/U =  $zheadu <b> ($kesimpulan_zheadu) </b><br/><br/>";
                                            } else {
                                                echo "<b> Error: Max 5 th </b>";
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            echo "1. Weight = <b> $kesimpulan_zbbu </b> or <b> $kesimpulan_imtu </b><br/><br/>";
                                            echo "2. Height = <b> $kesimpulan_pbu_tbu </b><br/><br/>";
                                            echo "3. Nutrition = <b> $kesimpulan_bbpb_bbtbu </b> or <b> $kesimpulan_imtu </b>";
                                            ?>
                                        </td>

                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        Note: <br />
                        1. BB/U = Berat Badan / umur <br />
                        2. PB/U and TB/U = Panjang Badan / Umur and Tinggi Badan / umur <br />
                        3. BB/PB and BB/TB = Berat Badan / Panjang Badan and Berat Badan / Tinggi Badan <br />
                        4. IMT/U = Index Masa Tubuh / Uur <br />
                        5. Head/U = Head Circumference / Umur <br />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<!-- Include Bootstrap Datepicker library -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

<script>
    $(document).ready(function() {
        $('#changeStatusBtn').click(function() {
            $.ajax({
                url: '<?php echo base_url("data/changeStatus"); ?>',
                type: 'GET',
                success: function(response) {
                    // Berhasil mengubah status
                    console.log(response);
                },
                error: function(xhr, status, error) {
                    // Terjadi kesalahan saat mengubah status
                    console.error(error);
                }
            });
        });

        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true
        });

        $('#datatable').DataTable({
            dom: 'Bfrtip',
            buttons: [
                'copy', 'excel', 'pdf', 'csv'
            ]
        });
    });
</script>