<?php
include_once "../untils/db.php";
include_once "../untils/functions.php";
include_once "../untils/origin.php";
// session_start();
//使⽤action参数,区别不同的⽅法
//action=save 保存
//action=delete 删除
//action=update修改
//action=login 登录
//action=query 查询
//判断action是否存在
if (isset($_POST['action'])) {
    //保存
    if ($_POST['action'] == 'save') {
    //获取⽤户名与密码
    $username = $_POST["username"];
    $password = $_POST["password"];
    echo saveUser($conn, $username, $password);
     }
    //登录
    else if ($_POST['action'] == 'login') {
    //获取⽤户名与密码
    $username = $_POST["username"];
    $password = $_POST["password"];
    echo login($conn, $username, $password);
     }
    }
    //使⽤get⽅式获取⽤户信息
    if (isset($_GET['action'])) {
    //1. 获取⽤户名
    if ($_GET["action"] == "query") {
    echo getAllUsers($conn);
     }
    //2. 检查⽤户是否登录
    else if ($_GET['action'] == 'checkLogin') {
    echo checkLogin();
     }
    }
    //1. 创建⽤户
    function saveUser($con, $username, $password)
    {
    $password = sha2($password);
    $sql = "insert into tbl_user(username,password) values('" . 
    $username . "','" . $password . "')";
    $result = mysqli_query($con, $sql);
    $ajaxReturn = [];
    if ($result) {
    $ajaxReturn = [
    "code" => "200",
    "msg" => "数据保存成功"
     ];
     } else {
    $ajaxReturn = [
    "code" => "0",
    "msg" => "数据保存失败"
     ];
     }
    return json_encode($ajaxReturn, JSON_UNESCAPED_UNICODE);
    }
   //2. ⽤户登录
function login($con, $username, $password)
{
$ajaxReturn = [];
//1. 编写sql语句,?表示站位符
$sql = "select * from tbl_user where username=? and password=?";
//2. 创建预查询
$stmt = mysqli_prepare($con, $sql);
//3. 对密码进⾏加密sha256⽅式进⾏加密
$password = sha2($password);
//4. 对占位符进⾏赋值
mysqli_stmt_bind_param($stmt, "ss", $username, $password);
//5. 执⾏查询
mysqli_stmt_execute($stmt);
//6. 从$stmt中获取结果集
$result = mysqli_stmt_get_result($stmt);
//7.获取结果集中的记录数
$rows = mysqli_num_rows($result);
if ($rows > 0) {
$ajaxReturn = ['code' => 200, 'msg' => '⽤户名密码正确'];
 } else {
$ajaxReturn = ['code' => 0, 'msg' => '⽤户名或者密码错误'];
 }
return json_encode($ajaxReturn, JSON_UNESCAPED_UNICODE);
}
//3. 查询⽤户
function getAllUsers($conn)
{
$sql = "select * from tbl_user";
$result = mysqli_query($conn, $sql);
$data = [];
$nums = mysqli_num_rows($result);
if ($nums > 0) {
while ($row = mysqli_fetch_assoc($result)) {
$data[] = ["id" => $row["id"], "username" => $row["username"]];
 }
$ajaxReturn = ["code" => 200, "msg" => "数据访问成功", "data" =>
$data];
return json_encode($ajaxReturn, JSON_UNESCAPED_UNICODE);
 } else {
return json_encode(["code" => 0, "msg" => "数据访问失败", "data" =>
null]);
 }
}
//4. 判断⽤户是否登录
function checkLogin()
{
session_start();
if (isset($_SESSION["user"])) {
return json_encode(['code' => 200, 'msg' => '⽤户已登录', 'status'
=> true], JSON_UNESCAPED_UNICODE);
 } else {
return json_encode(['code' => 0, 'msg' => '⽤户未登录', 'status' =>
false], JSON_UNESCAPED_UNICODE);
 }
}
    