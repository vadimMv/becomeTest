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
        $filtered = $this->filterUsers($response_from_api);
       
       
        $emails = array_count_values(array_map(function($item){
                                    return $item->email;
                            },$filtered));
        
        
        $duplacted=[];                    

        foreach($filtered as $key=> $item){

               if($emails[$item->email] == 1 ){
                    $db_instanse->insert(array_values((array)$item));
               } 
               else{
                   array_push($duplacted ,$item);
               }
        }
          
       
     }



     private function filterUsers($response){
        
        $users =  $this->getUser($response);
     
        $after18 = array_filter($users, function($user){
                        return $this->convertDateToAge($user->birth_date) >18;
                }); 
       
        $after21fromJerusalem =  array_filter($users, function($user){
                                        return $this->convertDateToAge($user->birth_date) >21 && $user->city_id == 5;
                                 }); 
                              
        return   array_unique(array_merge($after18 ,$after21fromJerusalem),SORT_REGULAR)  ;                       
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
