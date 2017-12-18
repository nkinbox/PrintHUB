<?php
class dbconnect {
private $user = "robinhood";
private $pass = "o3ndAhT3L2m3";
private $conn;
public $err = false;
public $errmsg = "";
public $rows;
public function __construct($db) {
try {
$this->conn = new PDO('mysql:host=localhost;dbname='.$db, $this->user, $this->pass);
$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {$this->err=true;}
}
public function q_run($sql) {
$this->err=false;
try {$this->conn->query($sql);} catch(PDOException $e) {
$this->err=true;
$this->errmsg = $e->getMessage();}
}
public function q_exe($sql,$arr) {
$this->err=false;
try {
$this->rows = $this->conn->prepare($sql);
$this->rows->execute($arr);
$this->rows = $this->rows->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
$this->err=true;
$this->errmsg = $e->getMessage();
}
}
public function __destruct() {$this->conn = null;}
}
?>