<?php

class order_Fn extends _function
{
    public function process($processId)
    {
        $process = [
            0 => 'รอการตรวจสอบ',
            1 => 'สินค้ามีบางส่วน',
            2 => 'สินค้ามีทั้งหมด',
            3 => 'อนุมัติจัดส่ง',
            4 => 'ส่งสินค้าแล้ว',
            5 => 'เก็บเงินมาบางส่วน',
            6 => 'เก็บเงินทั้งหมดแล้ว',
            7 => 'ยกเลิก',
        ];

        if (isset($process[$processId])) {
            return $process[$processId];
        }

        return '-';
    }
}
