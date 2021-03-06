<?php 
    require_once("db.php");
    class Backend{

        public $url;
        private $key;
        private $password;
    
    // init backend    
    public function __construct($url,$key,$password){
            $this->url = $url;    
            $this->password = $password;
            $this->key = $key;   
     } 
     

     private function httpApiRequest(){

        try{
         
            $curl = curl_init(); 
            curl_setopt_array($curl, [
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => $this->url .'?key='.$this->key .'&password='.$this->password ,
            ]);
            
            $resp = curl_exec($curl);
            curl_close($curl);
        }
        catch (\Exception $e){
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
      
        return $resp;
     }


    public function processData(){
        $response_from_api = $this->httpApiRequest();
        $db_instanse = new db();

        $filtered = $this->filterbyAgeUsers($response_from_api);         
        $dupl_result = $this->filterDuplacate($filtered[0]);
     
        $for_save =  $dupl_result[0];
        $duplacated =  $dupl_result[1];
        $rejected = $filtered[1];
        $report =  $db_instanse->insertOrUpdate($for_save);
        
        return [ "<h2>End process log ..)))</h2><br>" , 
                 "<h2>Rejected users =>".count($rejected)."</h2><br>",
                 "<h2>Duplicated users =>".count($duplacated)."</h2><br>",
                 "<h2>". $report ."</h2><br>" ] ;
   }
      
    public function AjaxResponse($param){

        header('Content-Type: application/json');

        $db_instanse = new db();
        $response = [];

        if($param =='graphs'){
            $response = $db_instanse->selectStat();
        }
        else if($param == 'table'){
            $response = $db_instanse->select();
        }
        echo json_encode($response);
    }

     private function filterbyAgeUsers($response){
        
        $users =  $this->getUser($response);
        $after18 = [] ; $after21fromJerusalem = [] ; $rejected=[];
        array_walk($users, function($user) use (&$after18 , &$after21fromJerusalem, &$rejected){
                    
                    $user->age = $this->convertDateToAge($user->birth_date);

                    if($this->convertDateToAge($user->birth_date) >18){
                        array_push($after18,$user);
                    }
                    else if($this->convertDateToAge($user->birth_date) >21 && $user->city_id == 5){
                        array_push($after21fromJerusalem,$user);
                    }
                    else{
                        array_push($rejected,$user);
                    }
        });
          
        $filter_pass  = array_unique(array_merge($after18 ,$after21fromJerusalem),SORT_REGULAR) ;                 
        return   [ $filter_pass , $rejected ] ;                       
     }
     
     private function filterDuplacate($filtered){

        $email_dupl = [];
        $users_dup =[];
               
        foreach ($filtered as $index=>$f) {
           if (isset($email_dupl[$f->email])) {
                array_push($users_dup, $filtered[$index]);
                unset($filtered[$index]);
              continue;
            }
            $email_dupl[$f->email] = true;
        }        
       return  [ $filtered , $users_dup ];
     }

     private function getUser($response){
        $obj_users = json_decode($response);
        return $obj_users->users; 
     }


     private function convertDateToAge($birth_date){
        $d = new DateTime($birth_date);
        $now = new DateTime();
        $interval = $now->diff($d); 
        return  $interval->y ;
     }

     
}
?>    
