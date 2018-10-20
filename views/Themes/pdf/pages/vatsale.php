<?php

$bkk = array('กรุงเทพมหานคร', 'กรุงเทพฯ', 'กทม.', 'กทม', 'กรุงเทพ ฯ');

$address = '';
$address .= $this->item['address'];
if (!empty($this->item['road'])) {
    $address .= !empty($address) ? ' ' : '';
    $address .= 'ถ.'.$this->item['road'];
}

if (!empty($this->item['district']) || !empty($this->item['area']) || !empty($this->item['province']) || !empty($this->item['post_code'])) {
    $address .= '<br>';
}

if (!empty($this->item['district'])) {
    $address .= !empty($address) ? ' ' : '';
    if (!empty($this->item['province'])) {
        if (in_array($this->item['province'], $bkk)) {
            $address .= 'แขวง'.$this->item['district'];
        } else {
            $address .= 'ต.'.$this->item['district'];
        }
    } else {
        $address .= 'ต.'.$this->item['district'];
    }
}

if (!empty($this->item['area'])) {
    $address .= !empty($address) ? ' ' : '';
    if (!empty($this->item['province'])) {
        if (in_array($this->item['province'], $bkk)) {
            $address .= 'เขต'.$this->item['area'];
        } else {
            $address .= 'อ.'.$this->item['area'];
        }
    } else {
        $address .= 'อ.'.$this->item['area'];
    }
}

if (!empty($this->item['province'])) {
    $address .= !empty($address) ? ' ' : '';
    if (!in_array($this->item['province'], $bkk)) {
        $address .= 'จ.'.$this->item['province'];
    } else {
        $address .= ' '.$this->item['province'];
    }
}
if (!empty($this->item['post_code'])) {
    $address .= !empty($address) ? ' ' : '';
    $address .= ' '.$this->item['post_code'];
}

// if( !empty($this->item['country']) ){
//   $address .= !empty($address) ? ' ' : '';
//   $address .= 'ประเทศ'.$this->item['country'];
// }

if (!empty($this->item['customer_phone'])) {
    $address .= !empty($address) ? '<br>' : '';
    $address .= ' '.$this->item['customer_phone'];
}

//$submit_date = $this->item['submit_date'];
//if ($_GET['type'] == 'slip') {
//    $submit_date = date('Y-m-d', strtotime('+30 days', strtotime($this->item['submit_date'])));
//}

$submit_date = date('Y-m-d', strtotime('+30 days', strtotime($this->item['send_date'])));

$style = "
<style>
    @page { margin: 10px 10px 0px 10px;  }
    .page-break {page-break-after: always;}
    div.breakNow { page-break-inside:avoid; page-break-after:always; }
    body { margin: 0px; font-size: 10pt; }
    body {font-family: 'THSarabunNew';}
    .font-kanit {font-family:'Kanit-Light';}
    .box_customers {padding:5px 10px;}
    .box_customers_ii {border:1px solid #000000;margin-top:15px;display:block;padding:5px 4px 5px 8px;text-align:center;}
    .box_customers_iii {border:1px solid #000000;margin-top:15px;display:block;padding:10px 4px 10px 8px;text-align:center;}
    table.table_main_user {}
    .font_b {font-weight: bold;display: block;}
    .font_company {margin-bottom: -5px;margin-top: 5px;}
    table.items {border: 0.1mm solid #000;}
    td { vertical-align: top; }
    .items td {border-left: 0.1mm solid #000000;border-right: 0.1mm solid #000000;}
    table thead td { background-color: #FFF;text-align: center;border: 0.1mm solid #000000;font-weight:600;padding-bottom: 3px;padding-top: 2px;}
    table tbody td {padding-bottom: 2px;padding-top: 2px;}
    /*table tbody td {}*/
    .items td.blanktotal {background-color: #FFFFFF;border: 0mm none #000000;border-top: 0.1mm solid #000000;border-right: 0.1mm solid #000000;}
    .items td.totals {text-align: right;border-top: 0.1mm solid #000000;}
    .items td.cost {text-align: right;padding-right:5px;}
    .items td.txt_left {text-align: left;padding-left:8px;}
    .box_sig2 {text-align:center;width:300px;display:block;margin:1em;}
    .box_sig3 {text-align:center;width:200px;display:block;margin:1em;}
    .txt_hidden {content: ;display: none;}
    div.footer
    {
        right           : 0;
        bottom          : 0;
        margin-bottom   : 0mm;
        height          : 50mm;
        text-align      : right;
    }
  </style>
";

$discount = array();
$net_price = array();
$rowData = array();
$html = '';

foreach ($this->item['items'] as $key => $value) {
    $record = $key + 1;
    $rowData[$record]['pro_name'] = $value['pro_name'];
    $rowData[$record]['qty'] = $value['qty'];
    $rowData[$record]['sales'] = $value['sales'];
    $rowData[$record]['unit'] = !empty($value['unit']) ? $value['unit'] : $value['pds_unit'];
    $rowData[$record]['amount'] = $value['amount'];
    $rowData[$record]['remark'] = $value['remark'];
    $discount[] = 0;
    $net_price[] = $value['amount'];
}

$vat = 0;
$total = array_sum($net_price);
$discount = array_sum($discount);
$total_net_price = $total - $vat;

$total_paper = ceil(count($rowData) / 25);
if ($total_paper == 0) {
    $total_paper = 1;
}
$page = 1;

for ($page_count = 1; $page_count <= $total_paper; ++$page_count) { // Loop
    $html .= '
<table width="100%">
  <tr>
    <td align="center" valign="top">
    <h3>
      <span class="font_b" style="">บริษัท โมเดิร์น แฟนตาซี จำกัด ( สำนักงานใหญ่ )</span><br>
      <span class="font_b">Modern Fantasy Co., Ltd.</span>
    </h3>
    </td>
  </tr>
</table>
<table width="100%" style="margin-top: 0px;" cellpadding="0" cellspacing="0">
  <tr>
    <td width="33.33%" style="border:0.5px solid #000;padding: 5px 0px 10px 10px;">
      <span class="f-18">
        <span style="white-spance:nowrap;">66 ซอยราษฎร์พัฒนา 1 แขวงสะพานสูง</span><br/>
        เขตสะพานสูง กรุงเทพมหานคร 10240 <br>เบอร์โทรศัพท์ 02-9171941-2<br/>
        เลขประจำตัวผู้เสียภาษี 0-1055-53049-89-9
      </span>
    </td>
    <td width="33.33%" valign="top" style="border:0.5px solid #000;padding: 15px 10px 10px 10px;" align="center">
      <h3 style="font-weight:bold;">'.$this->title.'</h3>
      <span style="font-size: 14px;">('.$page_count.'/'.$total_paper.')</span>
    </td>
    <td width="33.33%" style="border:0.5px solid #000;padding: 5px 10px 0px 10px;">
      Inv. No. : IN'.sprintf('%05d', $this->item['id']).'<br>
      Date : '.date('d/m/Y', strtotime($this->item['send_date'])).'<br>
      Time : '.date('H:i:s', strtotime($this->item['created'])).'
    </td>
  </tr>
</table>
<table width="100%" cellspacing="0" cellpadding="0" style="padding:0px 0px;">
    <tr>
      <td valign="top" style="padding: 0px 5px 7px 7px;border:0.5px solid #000;border-top: none;border-right: 0.5px solid #000;">
        <table width="100%">
          <tr>
            <td style="" ><b>Customer No. : </b></td>
            <td style="">'.$this->item['sub_code'].'</td>
          </tr>
          <tr>
            <td style="" ><b>Customer Name : </b></td>
            <td style="">'.$this->item['name_store'].'</td>
          </tr>
          <tr>
            <td style="" ></td>
            <td style="padding-top: -5px;">'.$address.'</td>
          </tr>
        </table>
      </td>
      <td width="40%" style="border:0.5px solid #000;border-top: none;padding: 1px 5px 7px 7px;">
        <table width="100%">
          <tr>
            <td style="">เงื่อนไขการชำระเงิน : </td>
            <td style="">'.$this->item['term_of_payment_arr']['name'].'</td>
          </tr>
          <tr>
            <td style="">เลขที่อ้างอิง : </td>
            <td style="">IN'.sprintf('%05d', $this->item['id']).'</td>
          </tr>
          <tr>
            <td style="">วันครบกำหนดชำระเงิน : </td>
            <td style="">'.date('d/m/Y', strtotime($submit_date)).'</td>
          </tr>

        </table>
      </td>
  </tr>
</table>

<div style="height:5px;"></div>
<table class="items" width="100%" style="border-collapse: collapse;" cellpadding="0" cellspacing="0">
  <thead>
    <tr>
      <td width="3%" style="">ลำดับ<br>Item.</td>
      <td width="30%" style="">สินค้า<br>Description</td>
      <td width="6%" style="">จำนวน<br>Quantity</td>
      <td width="5%" style="">หน่วย<br>Unit</td>
      <td width="7%" style="">ราคา/หน่วย<br>Unit/Price</td>
      <td width="7%" style="">รวม<br>Total</td>
      <td width="10%" style="">หมายเหตุ<br>Remark</td>
    </tr>
  </thead>
  <tbody>';

    $itemPage = 25;
    if (count($rowData) >= $itemPage * $page_count) {
        $count_RowPage = $itemPage;
    } else {
        $total_row = count($rowData);
        $count_RowPage = $itemPage;
    }
    $h = 1;
    for ($i = 0; $i < $count_RowPage; ++$i) {
        $rowCount = ($itemPage * $page_count) - ($itemPage - $h);
        ++$h;
        if (isset($rowData[$rowCount]['pro_name'])) {
            if ($rowCount == count($rowData)) {
                $style_border_bottom = 'border-bottom:1px solid #000;';
            } else {
                $style_border_bottom = 'border-bottom:none;';
            }

            $html .= '<tr>
                <td align="center" valign="middle" style="padding:5px; ">'.$rowCount.'</td>
                <td align="left" valign="middle" style="padding:5px; padding-left: 5px;">'.$rowData[$rowCount]['pro_name'].'</td>
                <td align="center" valign="middle" style="padding:5px; ">'.number_format($rowData[$rowCount]['qty']).'</td>
                <td align="center" valign="middle" style="padding:5px; padding-right: 5px;">'.$rowData[$rowCount]['unit'].'</td>
                <td align="right" valign="middle" style="padding:5px; padding-right: 5px;">'.number_format($rowData[$rowCount]['sales']).'</td>
                <td align="right" valign="middle" style="padding:5px; padding-right: 5px;">'.number_format($rowData[$rowCount]['amount']).'</td>
                <td align="right" valign="middle" style="padding:5px; padding-right: 5px;">'.$rowData[$rowCount]['remark'].'</td>
              </tr>';
        } else {
            $html .= '<tr>
          <td align="center" valign="middle" style="padding:5px;">&nbsp;</td>
          <td align="center" valign="middle" style="padding:5px;">&nbsp;</td>
          <td align="center" valign="middle" style="padding:5px;">&nbsp;</td>
          <td align="center" valign="middle" style="padding:5px;">&nbsp;</td>
          <td align="center" valign="middle" style="padding:5px;">&nbsp;</td>
          <td align="center" valign="middle" style="padding:5px;">&nbsp;</td>
          <td align="center" valign="middle" style="padding:5px;">&nbsp;</td>
        </tr>';
        }
    }

    $html .= '</tbody>
</table>';

    if ($page_count == $total_paper) {
        $table_total = $this->item['total'];
        $table_discount_extra = 0;
        $total_vat = $this->item['vat'];
        $table_net_price = $this->item['total'];
        $table_total_net_price = $this->item['amount'];
        $price_str = $this->fn->q('number')->numberTH($this->item['amount']);
    } else {
        $table_total = 0;
        $table_discount_extra = 0;
        $total_vat = 0;
        $table_net_price = 0;
        $table_total_net_price = 0;
        $price_str = '-';
    }

    $html .= '<div class="footer" style="padding-top: 5px;">
  <table class="items" width="100%" style="border-collapse: collapse;border-top: none;" cellpadding="0" cellspacing="0">
    <tr>
      <td class="blanktotal" rowspan="4" align="left" valign="top" width="60%" style="padding-left: 5px;padding-top: 5px;">
        <font style="display: block; font-size:14.5px;">ได้รับสินค้าดังรายการข้างบนนี้เรียบร้อยแล้ว</font><br/>
        <font style="display: block; font-size:14.5px;">Received the above mentioned goods in good order and condition.</font><br/>
        <font style="display: block; font-size:14.5px;">การชำระเงินด้วยเช็คจะสมบูรณ์ต่อเมื่อบริษัทฯ ได้รับเงินตามเช็คเรียบร้อยแล้ว</font><br/>
        <font style="display: block; font-size:14.5px;">Payment by cheque not valid till the cheque has been honoured</font><br/>
      </td>
      <td class="totals" width="20%" style="padding: 5px;">รวมเงิน</td>
      <td class="totals cost" width="20%" style="padding: 5px;">'.number_format($table_total, 2).'</td>
    </tr>
    <tr>
      <td class="totals" style="padding: 5px;">หักส่วนลดพิเศษ</td>
      <td class="totals cost" style="padding: 5px;">'.$table_discount_extra.'</td>
    </tr>
    <tr>
      <td class="totals" style="padding: 5px;">ยอดสุทธิ</td>
      <td class="totals cost" style="padding: 5px;">'.number_format($table_net_price, 2).'</td>
    </tr>
    <tr>
      <td class="totals" style="padding: 5px;">ภาษีมูลค่าเพิ่ม&nbsp;7%</b></td>
      <td class="totals cost" style="padding: 5px;">'.number_format($total_vat, 2).'</td>
    </tr>
    <tr>
      <td class="blanktotal" style="padding: 5px;" align="center" width="48.53%">
        ( '.$price_str.' )
      </td>
      <td class="totals" style="padding: 5px;">จำนวนเงินรวมทั้งสิ้น</td>
      <td class="totals cost" style="padding: 5px;"><b>'.number_format($table_total_net_price, 2).'</b></td>
    </tr>
  </table>
  <table width="100%" style="border-collapse: collapse;border-top: none;border:0.5px solid #000;border-top: none;" cellpadding="0" cellspacing="0">
    <tr>
      <td style="height: 80px;" align="center">
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>...............................</p>
        <div style="margin-top: 3px;">ผู้รับสินค้า</div>
        <div style="">Receiver</div>
      </td>
      <td style="height: 80px;border-right: none;border-right:0.5px solid #000;" align="center">
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>...............................</p>
        <div style="margin-top: 3px;">วันที่รับ</div>
        <div style="">Received Date</div>
      </td>
      <td style="height: 80px;border-right:0.5px solid #000;" align="center">
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>...............................</p>
        <div style="margin-top: 3px;">ผู้ส่งสินค้า</div>
        <div style="">Deliverer</div>
      </td>
      <td style="height: 80px;" align="center">
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>...............................</p>
        <div style="margin-top: 3px;">ผู้รับเงิน</div>
        <div style="">Collector</div>
      </td>
      <td style="height: 80px;" align="center">
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>...............................</p>
        <div style="margin-top: 3px;">ผู้อนุมัติ</div>
        <div style="">Authorized</div>
      </td>
    </tr>
  </table>
</div>
';
    if ($page_count != $total_paper) {
        $html .= '<div class="breakNow"></div>';
    }
}

$content = '<!doctype html><html lang="th"><head><title id="pageTitle">plate</title><meta charset="utf-8" />'.$style.'</head><body>'.$html.'</body></html>';

// echo $content;

$mpdf = new mPDF('th', 'A4', '0');

$mpdf->debug = true;
$mpdf->allow_output_buffering = true;

$mpdf->charset_in = 'UTF-8';
$mpdf->allow_charset_conversion = true;

$mpdf->list_indent_first_level = 0;

// $stylesheet = file_get_contents(CSS . 'bootstrap.css');
// $mpdf->WriteHTML($stylesheet,1);

// $stylesheet2 = file_get_contents(VIEW.'Themes/plate/assess/css/main.css');
// $mpdf->WriteHTML($stylesheet2,1);

// $content = iconv('UTF-8', 'windows-1252', $content);
// $content = mb_convert_encoding($content, 'UTF-8', 'UTF-8');

ob_clean();
$mpdf->SetTitle('VAT-SALE-IN'.sprintf('%05d', $this->item['id']));
$mpdf->WriteHTML($content);
$mpdf->Output('IN'.sprintf('%05d', $this->item['id']).'.pdf', 'I');
