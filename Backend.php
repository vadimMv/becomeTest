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


     public function processData()
     {
        $response_from_api = $this->httpApiRequest();
        $db_instanse = new db();

        $filtered = $this->filterbyAgeUsers($response_from_api);         
        $dupl_result = filterDuplacate($filtered);

        $for_save =  $dupl_result[0];
        $duplacated =  $dupl_result[1];

        
     }



     private function filterbyAgeUsers($response){
        
        $users =  $this->getUser($response);
     
        $after18 = array_filter($users, function($user){
                        return $this->convertDateToAge($user->birth_date) >18;
                }); 
       
        $after21fromJerusalem =  array_filter($users, function($user){
                                        return $this->convertDateToAge($user->birth_date) >21 && $user->city_id == 5;
                                 }); 
        $filter_pass  = array_unique(array_merge($after18 ,$after21fromJerusalem),SORT_REGULAR) ;
     

     
        return     ;                       
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
