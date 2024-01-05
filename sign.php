<?php
include_once "../untils/db.php";
include_once "../untils/functions.php";
include_once "../untils/origin.php";
session_start();
//判断action是否存在
if (isset($_POST['action'])) {
//保存
if ($_POST['action'] == 'save') {
//获取⽤户id，path
$userid = $_POST["userid"];
// $path = $_FILES["sign"];
$signData = $_POST['signData'];
//$path = $_POST["path"]
//echo save($conn, $userid, $signData);
echo saveSign($conn, $userid, $signData);
 }
//解析图⽚
if ($_POST['action'] == 'upload') {
//获取⽤户id，path
$signData = $_POST["signData"];
echo saveSignImage($signData);
 }
}
//判断action是否存在
if (isset($_GET['action'])) {
//保存
if ($_GET['action'] == 'query') {
echo getAllSigns($conn);
 }
//按⽤户查询
if ($_GET['action'] == 'queryuser') {
    $userid = $_GET['userid'];
echo getSignsByUserid($conn, $userid);
 }
}
//1. 保存签名
function save($con, $userid, $path)
{
//1. 编写sql语句
$sql = "insert into tbl_sign(userid,path) values(?,?)";
//2. 创建预处理语句
$stmt = mysqli_prepare($con, $sql);
//3. 对站位符进⾏赋值
mysqli_stmt_bind_param($stmt, "is", $userid, $path);
//4. 执⾏查询
$result = $stmt->execute();
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
function saveSignImage($imgData)
{
//1. 对$imgData删除头部类型数据，data:image/jpeg;base64,
$image = preg_replace('/^(data:\s*image\/\w+;base64)/', '', 
$imgData);
//2. 解码base64为⼆进制
$image = base64_decode($image);
//3. 获取图⽚类型
preg_match('/^(data:\s*image\/(\w+);base64,)/', $imgData, $result);
$type = $result[2];
//4. 获取⽹站根⽬录
$root = $_SERVER['DOCUMENT_ROOT'];
//5. 创建随机⽂件名
$relativePath = "/uploads/sign/" . "sign_" . date('Y_m_d_His') . 
"_" . rand(100000, 999999) . "." . $type;
$absolutePath = $root . $relativePath;
//6.保存⽂件
file_put_contents($absolutePath, $image);
//7. 返回相对⽂件名
return $relativePath;
}
function saveSign($con, $userid, $signData)
{
// $path = uploadSign($sign, "sign");
$path = saveSignImage($signData);
return save($con, $userid, $path);
}
function getAllSigns($conn)
{
$sql = "select s.id,u.username,u.id as userid,s.created,s.path from 
tbl_user u,tbl_sign s where u.id=s.userid";
$result = mysqli_query($conn, $sql);
$data = [];
while ($row = mysqli_fetch_assoc($result)) {
$data[] = ["id" => $row["id"], "userid" => $row["userid"], 
"username" => $row["username"], "path" => $row["path"], "created" =>
$row["created"]];
 }
return json_encode(["code" => 200, "msg" => "查询成功", "records" =>
count($data), "data" => $data]);
}
//获取某个⽤户的签名
function getSignsByUserid($conn, $userid)
{
$sql = "select s.id,u.username,u.id as userid,s.created,s.path from 
tbl_user u,tbl_sign s where u.id=s.userid and u.id=" . $userid;
$result = mysqli_query($conn, $sql);
$data = [];
while ($row = mysqli_fetch_assoc($result)) {
$data[] = ["id" => $row["id"], "userid" => $row["userid"], 
"username" => $row["username"], "path" => $row["path"], "created" =>
$row["created"]];
 }
return json_encode(["code" => 200, "msg" => "查询成功", "records" =>
count($data), "data" => $data]);
}