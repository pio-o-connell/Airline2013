<?php

class Cart{

    private $cartElements=array();
    private $cartID=0;

    
    
    function addToCart($ticketInfo){
      
        // adding a numeric key to the cardElements array
        $this->cartElements[$this->cartID] = $ticketInfo;
        $this->cartID++;
    
        
    }
    
    
    function deleteFromCart($cartID){
        unset($this->cartElements[$cartID]);
      
    }
    
    function displayCart(){

        $cartIds = array_keys($this->cartElements);
      
        
        print_r($this->cartElements);
        
        
        for($i=0;$i<sizeof($cartIds);$i++){
                 
                $cartID = $cartIds[$i];
                
             
                echo "<p style=\"border:red solid 1px\">";
                 
           
 
                echo "Route: ".$this->cartElements[$cartID]['route']."<br>";
                 
                echo "date: ".$this->cartElements[$cartID]['date']."<br>";
                echo "Number of seats: ".$this->cartElements[$cartID]['seatCount']."<br>";
                
                echo '<a href="controller.php?cmd=delCheckoutItem&cartID='.$cartID.'">delete</a>';
                
                
                 echo "</p>";   
            }
            echo "<br><a href=\"controller.php\">Back to search</a>";   
    }
    
}


class SearchView{
    
   
    public function __construct(){
        
        $this->top();
        $this->searchDiv();
        $this->bottom();
        
    }
    
    function searchDiv(){
        
       

            echo "
            <a style=\"float:right;\" href=\"controller.php?cmd=checkoutView\">Go to checkout</a> 

            <div id=\"search\">
                <form action=\"controller.php\" method=\"post\">
                <input type=\"hidden\" value=\"search\" name=\"cmd\">
                <table>
                <tr>
                    <td>
                    From:
                    </td>
                    <td>
                    <select name=\"route\">
               ";
                    // need to get the route decsriptions and ids out of the database
                        $DBM = new DBManager();
                        $data =  $DBM->getAllRoutes();
    
                   for ($i=0;$i<sizeof($data);$i++){
                       
                            echo "<option value=\"{$data[$i]['id']}\"> {$data[$i]['source']} - {$data[$i]['dest']}
                                      </option> ";                       
                       
                       
                       
                   }     
            
                    echo "
                        </select>
                        </td>
                        </tr>
                        <tr>
                            <td>
                                Number of tickets:   
                             </td>
                        <td>
                        <input type=\"text\" name=\"seatCount\"  size=\"3\">
                        </td
                    </tr>
                <tr>
                    <td> 
                        Select a date:
                    </td>
                        <td>
                        
                        <input 
                            id=\"startdate\" 
                            name=\"date\"  
                            type=\"date\">
                        </td>
                </tr>    
                
                <tr><td>
                        <input type=\"submit\" value=\"search\">
                </tr>    
                
                
                
            </table>
           
            
        </form>
        </div>
        ";
    }
    

    function top(){

        echo "<html> 
                    <head>
                   </head>
                    <body>
                    <H1>Welcome to PHP Airlines.com</H1>
                ";
    }

    
    function bottom(){
        echo "
            </body>
            </html>
         ";  
    }
    
    
}

 class ResultsView extends SearchView{
        
    private $route;
    private $seatCount;
    private $date;
     
     
     
     public function __construct($route,$seatCount,$date){
        
        $this->route = $route;
        $this->seatCount = $seatCount;
        $this->date= $date;
        
        
        
        $this->top();
        $this->searchDiv();
        $this->results();
        $this->bottom();
        
    }
    
    public function results(){
        
        //echo "<br><br><br>";
        //echo  $this->route." ".$this->ticketCount." ".$this->date."<br>";
        
        // set-up list of dates
        
         $DBM = new DBManager();
       $routeData =  $DBM->getRoute($this->route);
       
       $startDate = new DateTime($routeData[0]['start']);
       $endDate = new DateTime($routeData[0]['end']);
       
       
       
       echo "start: ".$startDate->format('d-M-Y')." end: ".$endDate->format('d-M-Y')."<br><br>"; 
   
        $dates = array();
    
        $searchDate= new DateTime($this->date);
        // go back to days
        
        //$searchDate->sub(new DateInterval('P2D'));
        //$dates[-2]= clone $searchDate;
        //echo $dates[-2]->format('d-M-Y')."<br>";

            echo "<div>";
            echo "<form action=\"controller.php\" method=\"post\">";
            echo "<input type=\"hidden\" name=\"cmd\" value=\"addToCart\">";
            
        
        for($i=-2;$i<3;$i++){
                    
            
            if ($i==-2){
                $searchDate->sub(new DateInterval('P2D'));
            }
            else{
                $searchDate->add(new DateInterval('P1D'));
            }
            
            $dates[$i]= clone $searchDate;
            

            
            // check against the operation date
                if ( ($dates[$i]->getTimestamp() < $startDate->getTimestamp()) || 
                        ($dates[$i]->getTimestamp() > $endDate->getTimestamp()) ){
            
                    echo $dates[$i]->format('d-M-Y')." not operating <br>";
            
                
                }
                else{
                    // the route is operational on this date
                    
                    // must go to database to see if there are enough seats for $seatCount
                    // checkSeat returns a boolean 
                    
                    
                    if ($DBM->checkSeats($dates[$i]->format('Y-m-d'),$this->route,$this->seatCount)){
                        
                        $totalPrice = $this->seatCount * $routeData[0]['price'];
                        
                        
                        echo $dates[$i]->format('d-M-Y')."  $totalPrice";
                        echo "<input type=\"radio\" name=\"flightDate\" value=\"{$dates[$i]->format('Y-m-d')}\"> <br>";
                       
                    
                   }
                   else{
                        echo $dates[$i]->format('d-M-Y')." Not enough seats <br>";
                        
                    }
                
                }
                
                }
                
                
            echo "<input type=\"submit\" value=\"Add to Cart\">";
            echo "</form></div>";
            
        
        
        
      
        
        
        
        
    }
        
        
        
        
  }
  
  
  class CheckoutView{
      
      
      function __construct(){
          $this->top();
          $_SESSION['cart']->displayCart();
          $this->bottom();
          
          
          
      }
      
      
        function top(){

        echo "<html> 
                    <head>
                   </head>
                    <body>
                    <H1>Welcome to PHP Airlines.com</H1>
                ";
    }

    
    function bottom(){
        echo "
            </body>
            </html>
         ";  
    }
    

      
      
      
      
      
  }
    


class DBManager{
        
        private $db;
        
        
        public function __construct(){
            
           $address = 'mysql:host=localhost:3361;dbname=airline2013';
           $username = 'root';
           $password = '';
            
           try {
            $this->db = new PDO($address, $username, $password);
    
           }catch (PDOException $e) {
                $error_message = $e->getMessage();
                echo $error_message;
                exit();
           } 
        }
        
        
        public function checkSeats(){
            return true;
            
            
        }
        
        public function getAllRoutes(){
             $query = "select * from routes";

            //db is a PDO object
            $resultSet = $this->db->query($query);
            
            return $resultSet->fetchAll();
        }
        
        
        function getRoute($id){
             $query = "select * from routes where id=$id";

            //db is a PDO object
            $resultSet = $this->db->query($query);
            
            return $resultSet->fetchAll();
            
            
            
            
            
            
        }
        
        public function loadRoutes(){
            
                $query = "INSERT INTO routes 
                                (source, dest, start, end, price) 
                                VALUE ('Cork','London', '2014-01-01','2014-01-31',100)";
          
                $count = $this->db->exec($query);
                
                
                
                
                
                 $query = "INSERT INTO routes 
                                (source, dest, start, end, price) 
                                VALUE ('Cork','Berlin', '2014-02-01','2014-02-28',100)";
          
                $count = $this->db->exec($query);    

                  
                
        }
        
        /*
        public function addPost($username,$title,$body){
            
            
           $query = "INSERT INTO post (username, title, body) VALUE ('$username','$title', '$body')";
          echo $query;
           
          $count = $this->db->exec($query);
          
          echo "rows inserted: ".$count;
             
        }
         
         */
       
        /*
       public function getPost($username){
            
            
            $query = "select * from post where username='$username'";

            //db is a PDO object
            $resultSet = $this->db->query($query);
            
            return $resultSet->fetchAll();
            
            
            
            
        }
        */
        
        /*
        public function processLogin($username, $password){
            
            // get the login details and query database
            
            
            $query = "select * from user";
            
            $resultSet = $this->db->query($query);
    
            while($row = $resultSet->fetch()){
        
                if ( ($row['username']==$username)&& ($row['password']==$password)){
                    if ($username == "admin"){
                        return "admin";
                    }
                    else{
                        return "blogger";      
                    }
                }
            }
 
            return "invalid";
            
        }
        */
        
        
        
        
}
    
    
    
    
    
?>
