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
