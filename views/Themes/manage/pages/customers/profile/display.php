<div id="mainContainer" class="profile clearfix" data-plugins="main">
    <div class="setting-content" role="content">
        <div class="setting-main" role="main">

            <div class="clearfix mal">

                <div class="setting-title">
                    <i class="icon-users mrm"></i><?=$this->item['name_store']; ?>
                </div>
                <!-- <div class="rfloat mrm">
                    <a class="btn btn-no-padding btn-red" data-plugins="dialog" href="<?=URL; ?>customers/del/<?=$this->item['id']; ?>?next=<?=URL; ?>customers"><i class="icon-trash"></i></a>
                </div> -->

            </div>

            <div class="clearfix mal">
                <div class="uiBoxOverlay pam pas">
                    <h3 class="mbm fwb"><i class="icon-user"></i> Customer data</h3>
                    <ul>
                        <li>
                            <label><span class="fwb">Store name : </span><?=$this->item['name_store']; ?></label>
                        </li>

                        <li>
                            <label><span class="fwb">Code : </span><?=$this->item['sub_code']; ?></label>
                        </li>
                        <?php
                        $num = 1;
                        foreach ($this->item['address'] as $key => $value) {
                            $address = '';
                            if (!empty($value['address'])) {
                                $address .= $value['address'];
                            }
                            if (!empty($value['road'])) {
                                $address .= ' <span class="fwb">Road</span> '.$value['road'];
                            }
                            if (!empty($value['district'])) {
                                $address .= ' <span class="fwb">District</span> '.$value['district'];
                            }
                            if (!empty($value['area'])) {
                                $address .= ' <span class="fwb">Area</span> '.$value['area'];
                            }
                            if (!empty($value['province'])) {
                                $address .= ' <span class="fwb">Province</span> '.$value['province'];
                            }
                            if (!empty($value['post_code'])) {
                                $address .= ' '.$value['post_code'];
                            }
                            if (!empty($value['country_name'])) {
                                $address .= ' <span class="fwb">'.$value['country_name'].'</span>';
                            }

                            echo '<li>
                                    <label>
                                        <span class="fwb">(Address '.$num.')</span> '.$address.'
                                    </label>
                                 </li>';
                            ++$num;
                        }
                        ?>
                        <li>
                            <label style="display: inline-block;"><span class="fwb">Project: </span></label>

                            <select style="display: inline-block;" id="project" name="project" class="inputtext" data-name="project_id" data-id="<?=$this->item['id']; ?>">
                                <option value="">- All -</option>
                                <?php
                                $projectName = '-';
                                foreach ($this->projectList as $key => $value) {
                                    $active = '';
                                    if ($_GET['project'] == $value['project_id']) {
                                        $active = ' selected';
                                    }

                                    echo '<option'.$active.' value="'.$value['project_id'].'">'.$value['project_name'].'</option>';
                                }
                                ?>
                            </select>

                            <label style="display: inline-block;"><span class="fwb">Status: </span></label>
                            <select style="display: inline-block;" id="status" name="status" class="inputtext" data-name="status" data-id="<?=$this->item['id']; ?>">
                                <option value="A" <?php if ($_REQUEST['status'] == 'A') {
                                    echo ' selected';
                                }?>>Active</option>
                                <option value="I"<?php if ($_REQUEST['status'] == 'I') {
                                    echo ' selected';
                                }?>>Cancel</option>
                            </select>

                            <label style="display: inline-block;"><span class="fwb">Payment: </span></label>
                            <select style="display: inline-block;" id="due" name="due" class="inputtext" data-name="due" data-id="<?=$this->item['id']; ?>">
                                <option value="" >All</option>
                                <option value="1"<?php if ($_REQUEST['due'] == '1') {
                                    echo ' selected';
                                }?>>ยอดค้างจ่าย</option>
                            </select>
                        </li>
                    </ul>
                </div>

            </div>

            <div class="clearfix mal">
                <div class="uiBoxOverlay mtm pam pas">
                    <div ref="table" class="listpage2-table">
                        <table class="table-bordered">
                            <thead>
                                <tr>
                                    <th class="ID">Order</th>
                                    <th class="date">Date</th>
                                    <th class="name">Order Code</th>
                                    <th class="status">Status</th>
                                    <th class="status">Project</th>
                                    <th class="status">Sale</th>
                                    <th class="price">Amount</th>

                                    <th class="date">Receive-Date</th>
                                    <th class="price">Receive-Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $num = 0;
                                $total_price = 0;
                                $total_payment = 0;

                                if (!empty($this->item['orders'])) {
                                    foreach ($this->item['orders'] as $key => $value) {
                                        ++$num;
                                        $total_price += $value['price'];

                                        $time = strtotime($value['ord_dateCreate']);
                                        $date = date('d/m/Y', $time);

                                        $status = $this->fn->q('order')->process($value['ord_process']);

                                        // $status = 'Cancle';
                                        // if ($value['ord_status'] == 'A') {
                                        //     $status = 'Active';
                                        // }

                                        // $project = '-';
                                        // if ($value['ord_project_id']) {
                                        //     $project = 'ok';
                                        // }

                                        $total_payment += !empty($value['payment']['pay_amount']) ? $value['payment']['pay_amount'] : 0; ?>
                                        <tr>
                                            <td class="ID"><?=$num; ?></td>
                                            <td class="date"><?= $date; ?></td>
                                            <td class="name">
                                                <span class="fwb">
                                                    <a href="<?=URL; ?>payments/<?=$value['id']; ?>" target="_blank"><?=$value['code']; ?></a>
                                                </span>
                                            </td>

                                            <td class="" style="width: 150px;"><?=$status; ?></td>

                                            <td class="" style="width: 150px;"><?=$value['project']; ?></td>

                                            <td class="" style="width: 50px;"><?=$value['sale_name']; ?></td>

                                            <td class="price"><?=number_format($value['price']); ?></td>

                                            <td class="date"><?= !empty($value['payment']['pay_date']) ? '<span style="color:#017506">'.$value['payment']['pay_date'].'</span>' : '-'; ?></td>
                                            <td class="price"><?php if (!empty($value['payment']['pay_amount']) && $value['payment']['pay_amount'] != 0) {
                                            echo '<span style="color:#017506">'.(number_format($value['payment']['pay_amount'])).'</span>';
                                        } else {
                                            echo  '0';
                                        } ?></td>
                                        </tr>
                                        <?php
                                    }
                                } else {
                                    echo '<td colspan="9" style="text-align:center;color:red;padding: 50px;background: #eee;" class="fwb">No purchase information found.</td>';
                                }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="6" style="text-align: right;" class="fwb">Sum</th>
                                    <th class="fwb" style="text-align: right;"><?=number_format($total_price); ?></th>
                                    <th colspan="2" class="fwb" style="text-align: right;"><?=number_format($total_payment); ?></th>
                                </tr>
                                <tr>
                                    <th colspan="8" style="text-align: right;" class="fwb">Total Due</th>
                                    <th class="fwb" style="text-align: right;"><span style="color:#f44336"><?=number_format($total_price - $total_payment); ?></span></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<script>
    $(function(){
        // $(document).on('change', '#project', function(){
        //     console.log($(this).val());
        //     window.location = '?project='+ $('#project').val()+'&status='+$('#status').val();
        // });
        // $(document).on('change', '#status', function(){
        //     window.location = '?project='+ $('#project').val()+'&status='+$(this).val();
        // });
        $(document).on('change', 'select', function(){
            window.location = '?project='+ $('#project').val()+'&status='+$('#status').val()+'&due='+$('#due').val();
        });
    });

</script>