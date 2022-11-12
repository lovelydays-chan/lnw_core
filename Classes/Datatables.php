<?php

namespace Lnw\Core;

class Datatables
{
    protected $column_search = [];
    protected $column_order = [];
    protected $column_list = [];
    protected $request;
    protected $start = 0;
    protected $length = 10;
    protected $draw = 1;
    protected $total_rows_all = 0;
    protected $total_rows_filter = 0;
    protected $data = [];
    protected $query;
    // กำหนดฟิลด์ข้อมูลที่่ต้องการเรียงข้อมูลเริ่มต้น และรูปแบบการเรียงข้อมูล
    protected $order = ["id" => "asc"];
    public function __construct($request, $query)
    {
        $this->query = $query;
        $this->request = $request;
        $this->init();
    }

    public function init()
    {
        $_draw = isset($this->request['draw']) ? $this->request['draw'] : $this->draw; // ครั้งที่การดึงข้อมูล ค่าของ dataTable ส่งมาอัตโนมัติ
        $_p =  $this->request['search']; // ตัวแปรคำค้นหาถ้ามี
        $_earchValue = $_p['value']; // ค่าคำค้นหา
        $_order = $this->request['order']; // ตัวแปรคอลัมน์ที่ต้องการเรียงข้อมูล
        $_length = isset($this->request['length']) ? $this->request['length'] : $this->length; // ตัวแปรจำนวนรายการที่จะแสดงแต่ละหน้า
        $_start = isset($this->request['start']) ? $this->request['start'] : $this->start; // เริ่มต้นที่รายการ
        $total_rows_all = $this->query->count();
        $this->buildParamSearch($_earchValue);
        $this->buildOrderBy($_order);
        $total_rows_filter = !$_earchValue ? $total_rows_all :  $this->query->count();
        $this->data = $this->query->offset($_start)->limit($_length)->get();
        $this->draw = $_draw;
        $this->total_rows_all = $total_rows_all;
        $this->total_rows_filter = $total_rows_filter;
    }
    public function render()
    {
        // กำหนดรูปแบบ array ของข้อมูลที่ต้องการสร้าง JSON data ตามรูปแบบที่ DataTable กำหนด
        foreach ($this->data as $row) {
            if (count($this->column_list) > 0) {
                if (in_array(array_keys($row), $this->column_list)) {
                    $data[] = $row;
                }
            } else {
                $data[] = $row;
            }
        }
        $output = array(
            "draw" => $this->draw, // ครั้งที่เข้ามาดึงข้อมูล
            "recordsTotal" => $this->total_rows_all, // ข้อมูลทั้งหมดที่มี
            "recordsFiltered" => $this->total_rows_filter, // ข้อมูลเฉพาะที่เข้าเงื่อนไข เช่น ค้นหา แล้ว
            "data" => $this->data
        );
        return  json_encode($output);
    }
    protected function buildParamSearch($text)
    {
        $this->query->where(function ($q) use ($text) { // เปิดวงเล็บ
            $i = 0;
            // วนลูปฟิลด์ที่ต้องการค้นหา กรณีมีการส่งคำค้น เข้ามา
            foreach ($this->column_search as $item) {
                if ($text) {
                    if ($i === 0) {
                        $q->where($item, 'like', "%" . $text . "%");
                    } else {
                        $q->orWhere($item, 'like', "%" . $text . "%");
                    }
                }
                $i++;
            }
        });
    }
    protected function buildOrderBy($_order)
    {
        if (isset($_order) && $_order != NULL) {
            $_orderColumn = $_order['0']['column'];
            $_orderSort = $_order['0']['dir'];
            $this->query->orderBy($this->column_order[$_orderColumn], $_orderSort);
        } else {
            $order = $this->order;
            $this->query->orderBy(key($order), $order[key($order)]);
        }
    }
}
