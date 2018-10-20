<?php
$total = 0;
// echo '<pre>';
// print_r($this->results);
// echo '</pre>';
?>
<table class="table-bordered" width="100%">
    <thead>
        <tr>
            <th class="pas">Salon Name</th>
            <th width="15%">Sale Name</th>
            <th width="15%">Project Target</th>
            <th width="15%">Order</th>
            <th width="15%">Paid</th>
            <th width="10%">Detail</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $totalAmount = 0;
        $totalPay = 0;
        $$totalTarget = 0;

        if (!empty($this->results)) {
            foreach ($this->results as $key => $value) {
                $orderTarget = $value['project_target'] - $value['orders_amount'];
                $totalAmount = $totalAmount + $value['orders_amount'];
                $totalTarget = $totalTarget + $orderTarget;
                $totalPay = $totalPay + $value['orders_pay']; ?>
        <tr>
            <td class=" pas"><?=$value['customer']; ?></td>
            <td class="tac pas">(<?=$value['sale_code']; ?>) <?=$value['sale_fullname']; ?></td>
            <?php if ($value['ord_project_id'] != '') {
                    ?>
                <td class="tar pas"><?=number_format($value['project_target']); ?></td>
            <?php
                } else {
                    ?>
                <td class="tar pas">-</td>
            <?php
                } ?>

            <td class="tar pas"><?=number_format($value['orders_amount']); ?></td>
            <!-- <td class="tar pas"><?=number_format($value['project_target'] - $value['orders_amount']); ?></td> -->
            <td class="tar pas"><?=number_format($value['orders_pay']); ?></td>
            <td class="tac pas"><a class="btn btn-success" href="/customers/<?=$value['customer_id']; ?>">Detail</a></td>
        </tr>
        <?php //$total += $value['balance'];
            }
        } else {
            echo '<tr><td colspan="6" class="pam tac fcr fwb">Not found !</td></tr>';
        } ?>
    </tbody>
    <tfoot>
        <tr>
            <th class="pas tar" colspan="3">TOTAL</th>
            <th class="pas tar"><?php echo number_format($totalAmount); ?></th>
            <th class="pas tar"><?php echo number_format($totalPay); ?></th>
            <th></th>
        </tr>
    </tfoot>
</table>