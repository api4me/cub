<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
* @filename regist.php
* @touch date Thu 23 Apr 2015 06:00:31 AM CST
* @author: Fred<fred.zhou@foxmail.com>
* @license: http://www.zend.com/license/3_0.txt PHP License 3.0"
* @version 1.0.0
*/
$out = array();

if (!$post = @$_POST['data']) {
    $out['status'] = 500;
    $out['msg'] = '系统忙，服务接入中...';
    die(json_encode($out));
}
if (!isset($post['mobile']) && !isset($post['gmobile'])) {
    $out['status'] = 500;
    $out['msg'] = '请输入手机号';
    die(json_encode($out));
}

$config = array(
    'dsn' => 'mysql:host=localhost;dbname=dota2;port=3306;charset=utf8',
    'username' => 'dota2',
    'password' => 'W4zABheF95cr7ADc',
);

try {
    $pdo = new PDO($config['dsn'], $config['username'], $config['password']); 
} catch (PDOException $e) {
    $out['status'] = 400;
    $out['msg'] = '系统忙，请稍后';
    die(json_encode($out));
}

// Insert to DB
$q = "INSERT INTO(`mobile`, `qq`, `name`, `score`, `qmobile`, `gqq`, `gname`, `gteam`, `created`, `updated`) 
           VALUES(:mobile, :qq, :name, :score, :qmobile, :gqq, :gname, :gteam, 'now()', 'now()');";
$query = $pdo->prepare($q);
$query->bindParam(':mobile', @$post['mobile']);
$query->bindParam(':qq', @$post['qq']);
$query->bindParam(':name', @$post['name']);
$query->bindParam(':score', @$post['score']);
$query->bindParam(':qmobile', @$post['qmobile']);
$query->bindParam(':gqq', @$post['gqq']);
$query->bindParam(':gname', @$post['gname']);
$query->bindParam(':gteam', @$post['gteam']);

if ($query->execute()) {
    $out['status'] = 200;
    $out['msg'] = '报名成功，请保持关注，谢谢!';
    die(json_encode($out));
}

$out['status'] = 400;
$out['msg'] = '系统忙，请稍后再试';
die(json_encode($out));

?>
