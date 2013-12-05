<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
* @filename common_helper.php
* @touch date Thursday, May 16, 2013 PM12:35:58 CST
* @author: Fred<fred.zhou@foxmail.com>
* @license: http://www.zend.com/license/3_0.txt PHP License 3.0"
* @version 1.0.0
*/
if (!defined('BASEPATH')) exit('No direct script access allowed');
/*{{{ option_value */
if (!function_exists("option_value")) {
    function option_value($type, $code) {
        $ci =& get_instance();
        $ci->load->library("lcommon");
        if ($val = $ci->lcommon->option($type, $code)) {
            return $val;
        }

        return $code;
    }
}
/*}}}*/
/*{{{ model_value */
if (!function_exists("model_value")) {
    function model_value($code) {
        $ci =& get_instance();
        $ci->load->library("lcommon");
        if ($val = $ci->lcommon->model($code)) {
            return $val;
        }

        return $code;
    }
}
/*}}}*/
/*{{{ area_value */
if (!function_exists("area_value")) {
    function area_value($code) {
        $ci =& get_instance();
        $ci->load->library("lcommon");
        if ($val = $ci->lcommon->area($code)) {
            return $val;
        }

        return $code;
    }
}
/*}}}*/
/*{{{ area_dropdown */
if (!function_exists("area_dropdown")) {
    function area_dropdown($name, $code, $attr) {
        list($province, $city, $district) = str_split($code, 2);
        $out[] = '<div id="area">';
        $out[] = '<select name="province" class="input-medium"></select>';
        $out[] = '<select name="city" class="input-medium"></select>';
        $out[] = '<select name="district" class="input-medium"></select>';
        $out[] = "\n";
        $out[] = '<script>';
        $site_url = site_url();
        $out[] = <<<EOF
            jQuery(document).ready(function($){
                $.get("{$site_url}/api/area/", function(data){
                    $.each(data.data, function(k, v){
                        var selected = (k == "{$province}")? " selected" : "";
                        $("#area select[name='province']").append($('<option value="'+k+'"'+selected+'>' + v["00"]["00"]  + '</option>'));
                    });
                    $("#area select[name='province']").change(function(){
                        $("#area select[name='city']").empty();
                        $.each(data.data[$(this).val()], function(k, v){
                            if (k != "00") {
                                var selected = (k == "{$city}")? " selected" : "";
                                $("#area select[name='city']").append($('<option value="'+k+'"'+selected+'>' + v["00"]  + '</option>'));
                            }
                        });
                        $("#area select[name='city']").trigger("change");
                    }).trigger("change");
                    $("#area select[name='city']").change(function(){
                        var province = $("#area select[name='province']").val();
                        $("#area select[name='district']").empty();
                        $.each(data.data[province][$(this).val()], function(k, v){
                            if (k != "00") {
                                var selected = (k == "{$district}")? " selected" : "";
                                $("#area select[name='district']").append($('<option value="'+k+'"'+selected+'>' + v  + '</option>'));
                            }
                        });
                    }).trigger("change");
                }, "json");
            });
EOF;
        $out[] = '</script></div>';
        
        return implode($out);
    }
}
/*}}}*/
/*{{{ model_dropdown */
if (!function_exists("model_dropdown")) {
    function model_dropdown($name, $code, $attr, $blank = false) {
        if (!isset($code)) {
            $code = '000000';
        }
        list($brand, $model) = str_split($code, 3);
        $out[] = '<div id="model">';
        $out[] = '<select name="brand" class="input-medium"></select>';
        $out[] = '<select name="model" class="input-medium"></select>';
        $out[] = "\n";
        $out[] = '<script>';
        $site_url = site_url();
        $b = '';
        if ($blank) {
            $b = '$("#model select[name=\'brand\']").append($(\'<option value="">--</option>\'));';
        }
        $out[] = <<<EOF
            jQuery(document).ready(function($){
                $.get("{$site_url}/api/model/", function(data){
                    {$b}
                    $.each(data.data, function(k, v){
                        var selected = (k == "{$brand}")? " selected" : "";
                        $("#model select[name='brand']").append($('<option value="'+k+'"'+selected+'>' + v["000"]  + '</option>'));
                    });
                    $("#model select[name='brand']").change(function(){
                        $("#model select[name='model']").empty();
                        data.data[$(this).val()] && $.each(data.data[$(this).val()], function(k, v){
                            if (k != "000") {
                                var selected = (k == "{$model}")? " selected" : "";
                                $("#model select[name='model']").append($('<option value="'+k+'"'+selected+'>' + v  + '</option>'));
                            }
                        });
                    }).trigger("change");
                }, "json");
            });
EOF;
        $out[] = '</script></div>';
        
        return implode($out);
    }
}
/*}}}*/

/*{{{ model_dropdown */
if (!function_exists("model_dropdown2")) {
    function model_dropdown2($name, $code, $attr, $blank = false) {
        if (!isset($code) || $code == null) {
            $code = '000001';
        }
        list($brand, $model) = str_split($code, 3);
        $out[] = '<div id="model">';
        $out[] = '<select name="brand" class="input-medium"></select>';
        $out[] = '<select name="model" class="input-medium"></select>';
        $out[] = "\n";
        $out[] = '<script>';
        $site_url = site_url();
        $b = '';
        if ($blank) {
            $b = '$("#model select[name=\'brand\']").append($(\'<option value="">--</option>\'));';
        }
        $out[] = <<<EOF
            jQuery(document).ready(function($){
                $.get("{$site_url}/api/model/", function(data){
                    {$b}
                    data.data["000"] = {"000": "不限", "001": "不限"};
                    $.each(data.data, function(k, v){
                        var selected = (k == "{$brand}")? " selected" : "";
                        $("#model select[name='brand']").append($('<option value="'+k+'"'+selected+'>' + v["000"]  + '</option>'));
                    });
                    $("#model select[name='brand']").change(function(){
                        $("#model select[name='model']").empty();
                        data.data[$(this).val()] && $.each(data.data[$(this).val()], function(k, v){
                            if (k != "000") {
                                var selected = (k == "{$model}")? " selected" : "";
                                $("#model select[name='model']").append($('<option value="'+k+'"'+selected+'>' + v  + '</option>'));
                            }
                        });
                    }).trigger("change");
                }, "json");
            });
EOF;
        $out[] = '</script></div>';

        return implode($out);
    }
}
/*}}}*/


/*{{{ buydate_dropdown */
if (!function_exists("buydate_dropdown")) {
    function buydate_dropdown() {
        $out[] = '<div id="buydate">';

        $now = date('Y');
        $option = array();
        for($i = 12; $i > -1; $i--) {
            $k = $now - $i;
            $option[] = sprintf('<option value="%s">%s</option>' ,$k, $k);
        }
        $out[] = '<select name="buydate-year" class="input-small">' . implode("", $option) . '</select>';
        $option = array();
        for($i = 1; $i < 13; $i++) {
            $option[] = sprintf('<option value="%s">%s</option>' ,$i, $i);
        }
        $out[] = '<select name="buydate-month" class="input-small">' . implode("", $option) . '</select>';
        $out[] = '</div>';
        
        return implode($out);
    }
}
/*}}}*/
/*{{{ session_value */
if (!function_exists("session_value")) {
    function session_value($code) {
        $ci =& get_instance();
        $ci->load->library("lsession");
        if ($val = $ci->lsession->get($code)) {
            return $val;
        }

        return false;
    }
}
/*}}}*/
/*{{{ sale_status */
if (!function_exists("sale_status")) {
    /**
     * For car status is auction
     *
     */
    function sale_status($start, $end) {
        $ci =& get_instance();
        // presale 售前
        // selling 售中
        // sold 售后
        $now = time();
        $start = strtotime($start);
        $end = strtotime($end);
        if ($start > $now) {
            return "presale";
        } else if ($end < $now) {
            return "sold";
        }

        return "selling";
    }
}
/*}}}*/
/*{{{ get_route */
if (!function_exists("get_route")) {
    function get_route() {
        $ci =& get_instance();
        return $ci->router;
    }
}
/*}}}*/
/*{{{ get_appraisal */
if (!function_exists("get_appraisal")) {
    function get_appraisal($issue, $score) {
        // 一级 鉴定总分≥90
        // 二级 60≤鉴定总分＜90
        // 三级 20≤鉴定总分＜60
        // 四级 鉴定总分＜20
        // 五级 事故车
        if (!$issue) {
            return '';
        }
        if ($issue == 'yes') {
            return '五级';
        }

        $score = intval($score);
        if($score >= 90) {
            return '一级';
        }
        if($score >= 60 && $score < 90) {
            return '二级';
        }
        if($score >= 20 && $score < 60) {
            return '三级';
        }
        if($score <= 20) {
            return '四级';
        }
    }
}
/*}}}*/
/*{{{ show_final_price */
if (!function_exists("show_final_price")) {
    function show_final_price($price) {
        $len = strlen($price);
        return substr($price, 0, 1) . str_pad('', $len - 1, '*');
    }
}
/*}}}*/
/*{{{ is_pay */
if (!function_exists("is_pay")) {
    function is_pay($uid, $cid) {
        static $data;
        if (!isset($data)) {
            $url = sprintf('http://localhost:6060/getpay?uid=%s', $uid);
            $data = explode(',', file_get_contents($url));
        }
        if (in_array($cid, $data)) {
            return true;
        }

        return false;
    }
}
/*}}}*/
/*{{{ pay_limit */
if (!function_exists("pay_limit")) {
    function pay_limit($uid, $cid) {
        $url = sprintf('http://localhost:6060/paylimit?uid=%s&cid=%s&max=%s', $uid, $cid, 3);
        $limit = file_get_contents($url);

        if (!$limit) {
            $out = '0$竞拍数已完';
        } else {
            $out = sprintf('%s$剩余竞拍%s次', $limit, $limit);
        }

        return $out;
    }
}
/*}}}*/

/*{{{ price_dropdown */
if (!function_exists("price_dropdown")) {
    function price_dropdown($price_type) {
        $out[] = '<div id="price">';

        $option = array();
        $text = array('不限', '3万以下', '3万-5万', '5万-8万', '8万-12万', '12万-18万',
            '18万-24万', '24万-35万', '35万-60万','60万-100万', '100万以上');

        $i = 0;
        while ($i < count($text)) {
            if ($i == intval($price_type)) {
                $option[] = sprintf('<option value="%s" selected="selected">%s</option>' , $i . '', $text[$i]);
            } else {
                $option[] = sprintf('<option value="%s">%s</option>' , $i . '', $text[$i]);
            }
            $i++;
        }

        $out[] = '<select name="consign_price" class="input-medium">' . implode("", $option) . '</select>';
        $out[] = '</div>';

        return implode($out);
    }
}
/*}}}*/

/*{{{ age_dropdown */
if (!function_exists("age_dropdown")) {
    function age_dropdown($age_type) {
        $out[] = '<div id="age">';

        $option = array();
        $text = array('不限', '1年内', '1年-3年', '3年-5年', '5年-8年', '8年以上');

        $i = 0;
        while ($i < count($text)) {
            if ($i == intval($age_type)) {
                $option[] = sprintf('<option value="%s" selected="selected">%s</option>' , $i . '', $text[$i]);
            } else {
                $option[] = sprintf('<option value="%s">%s</option>' , $i . '', $text[$i]);
            }
            $i++;
        }

        $out[] = '<select name="consign_age" class="input-medium">' . implode("", $option) . '</select>';
        $out[] = '</div>';

        return implode($out);
    }
}
/*}}}*/

/*{{{ mileage_dropdown */
if (!function_exists("mileage_dropdown")) {
    function mileage_dropdown($mileage_type) {
        $out[] = '<div id="mileage">';

        $option = array();
        $text = array('不限', '1万公里以下', '3万公里以下', '5万公里以下', '8万公里以下', '8万公里以上');

        $i = 0;
        while ($i < count($text)) {
            if ($i == intval($mileage_type)) {
                $option[] = sprintf('<option value="%s" selected="selected">%s</option>' , $i . '', $text[$i]);
            } else {
                $option[] = sprintf('<option value="%s">%s</option>' , $i . '', $text[$i]);
            }
            $i++;
        }

        $out[] = '<select name="consign_mileage" class="input-medium">' . implode("", $option) . '</select>';
        $out[] = '</div>';

        return implode($out);
    }
}
/*}}}*/

/*{{{ gearbox_dropdown */
if (!function_exists("gearbox_dropdown")) {
    function gearbox_dropdown($gearbox_type) {
        $out[] = '<div id="gearbox">';

        $option = array();
        $text = array('不限', '手动挡', '自动档', '手自一体');

        $i = 0;
        while ($i < count($text)) {
            if ($i == intval($gearbox_type)) {
                $option[] = sprintf('<option value="%s" selected="selected">%s</option>' , $i . '', $text[$i]);
            } else {
                $option[] = sprintf('<option value="%s">%s</option>' , $i . '', $text[$i]);
            }
            $i++;
        }

        $out[] = '<select name="consign_gearbox" class="input-medium">' . implode("", $option) . '</select>';
        $out[] = '</div>';

        return implode($out);
    }
}
/*}}}*/
