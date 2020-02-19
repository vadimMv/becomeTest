<?php
class db{

public $conn;    
public function getDbCon() {
        try {
            $con = new PDO("mysql:host=127.0.0.1;port=3306;dbname=become;charset=utf8", "root", "root");
            $con->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $con;
        }
        catch(PDOException $e) {
            echo "db connection error." , $e->getMessage(), "\n";
            die();
        }
    }


public function select(){
  
    $this->conn = $this->getDbCon();
    $data = $this->conn->query("SELECT * FROM users")->fetchAll();
    $this->conn = null;
    return $data;
}

public function insert($data){
     print_r($data) ; echo '<br>' ;
    $this->conn = $this->getDbCon();    
    $sql = "INSERT INTO users (first_name, last_name, email, birth_date, phone, city_id) VALUES (?,?,?,?,?,?)";
    $result=  $this->conn->prepare($sql)->execute($data);
    $this->conn = null;
    return $result;

}    

public function update($data){

    $this->conn = $this->getDbCon();
    $sql = "UPDATE users SET first_name=:first_name, last_name=:last_name, email=:email, birth_date=:birth_date, phone=:phone, city_id=:city_id WHERE id=:id";
    $result = $this->conn->prepare($sql)->execute($data);
    $this->conn = null;
    return $result;
 
}

public function delete($id){

    $this->conn = $this->getDbCon();
    
    $sql =  "DELETE FROM users WHERE id =:id";
    $stm= $this->conn->prepare($sql);
    $stm->bindParam(':id', $id);
    $result =  $stm->execute();

    $this->conn = null;
    return $result;
}

}
?>
