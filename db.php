<?php
class db{

public $conn;   

public function getDbCon() {
        try {
            $con = new PDO("mysql:host=127.0.0.1;port=3306;dbname=become;charset=utf8", "root", "");
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

public function selectStat(){
    $this->conn = $this->getDbCon(); 
    $query = "SELECT  COUNT(CASE WHEN age  <= 20 THEN 1 END) ,
                      COUNT(CASE WHEN (age >=20 AND age <=40) THEN 1 END),
                      COUNT(CASE WHEN (age >=40 AND age <=70) THEN 1 END),
                      COUNT(CASE WHEN (age >=70 AND age <=90) THEN 1 END)
                      FROM `users` ";
    $city = $this->conn->query("SELECT  count(city_id) FROM `users` GROUP BY city_id")->fetchAll();
    $age =  $this->conn->query($query)->fetch();
    $this->conn = null;
    return  [$city , $age];
}
private function rowsCount(){
    
    $this->conn = $this->getDbCon();
    $rows = $this->conn->query("SELECT count(*) FROM users")->fetchColumn();
    $this->conn = null;
    return $rows;
}
private function selectUniqe($email){
    $this->conn = $this->getDbCon();
    $sth = $this->conn->prepare("SELECT * FROM users WHERE email =:email");
    $sth->execute(['email'=>trim($email)]);              
    $this->conn = null;
    $row = $sth->fetch();
    return $row['id'] ?? 0 ;
}
public function insert($data){
    $this->conn = $this->getDbCon();    
    $sql = "INSERT INTO users (first_name, last_name, email, birth_date, phone, city_id ,age) VALUES (?,?,?,?,?,?,?)";
    $result=  $this->conn->prepare($sql)->execute($data);
    $this->conn = null;
    return $result;

}    

public function update($data){
   
    $this->conn = $this->getDbCon();
    $sql = "UPDATE users SET first_name=:first_name, last_name=:last_name, email=:email, birth_date=:birth_date, phone=:phone, city_id=:city_id ,age=:age WHERE id=:id";
    $result = $this->conn->prepare($sql)->execute($data);
    $this->conn = null;
    return $result;
 
}
public function insertOrUpdate($alls){
       $rows = count($alls);
       $db_rows = $this->rowsCount();
       if($db_rows == 0){     
        array_walk($alls,function($item){
                 $data = array_values((array)$item);   
                 $this->insert($data);
        });
       }
       else{   
        array_walk($alls,function($item){
                $id = $this->selectUniqe($item->email);
                $item->id = $id;
                $data = array_values((array)$item);           
                $this->update($data);
        });
    }

    return  $db_rows == 0 ? "insert new users=>" .$rows  : "updated new users=>" .$rows;  
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
