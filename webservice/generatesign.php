<?php
$time = time();

// $str1 = $str1 = "app_id=KV1LI73MXVBAQ&content=newproductmask&environment=test&product_id=2314&random=289898&shop_id=1&amp;timestamp=".$time."&user_id=1";
// $str1 = $str1 = "app_id=KV1LI73MXVBAQ&content=newproductmask&environment=test&product_id=2314&random=289898&timestamp=".$time;
// $str1 = "app_id=KV1LI73MXVBAQ&company_id=1&company_name=Sabsons&contact_person=HSBBarry&phone=923232985464&random=289898B&shop_id=1&shop_name=SabifyTest&sunmi_shop_key=ZC7XS250CFAM52MYNZ1E&sunmi_shop_no=348958051948&amp;timestamp=$time&user_id=1";
// $str1 = "app_id=KV1LI73MXVBAQ&amp;company_id=1&amp;company_name=Sabsons&amp;contact_person=HSBBarry&amp;phone=923232985464&amp;random=ABC1234&amp;shop_id=1&amp;shop_name=SabifyTest&amp;sunmi_shop_key=ZC7XS250CFAM52MYNZ1E&amp;sunmi_shop_no=348958051948&amp;timestamp=$time&amp;user_id=1";
$str1 = 'app_id=KV1LI73MXVBAQ&amp;product_list=[{"name":"Redmi 9C Mobile","id":"Xiomi","bar_code":"58976457","unit":"unit","price":"26500","member_price":"3","spec":"250ml","level":"","brand":"Xiomi"}]&amp;random=XIOMI89902&amp;shop_id=1&amp;timestamp='.$time;
// echo $str1;
// exit();
$str2 = $str1 . "&key=0XsVp45yO0vJlEbWsPPQ";
$sign = strtoupper(MD5($str2));
echo $sign."</br>".$time;