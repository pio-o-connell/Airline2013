<?php 
include("library.php");

session_start();

   
  // start point for script 
  main();
   
   

   function main(){
   
        $cmd ="";
        if (!isset($_SESSION['cart'])){
                // only create the cart when the session starts
                 $_SESSION['cart'] = new Cart();
        }
 

         if (isset($_POST['cmd'])){
                 $cmd = $_POST['cmd'];
         }
         else if (isset($_GET['cmd'])){

                 $cmd = $_GET['cmd'];
         } 
   

          switch($cmd){
                case "":   
                    new SearchView(); 
                    break;
                case "search":
                    handleSearch();
                    break;
                case "addToCart":
                   
                   
                    handleAddToCart();
                    
                    break;
                case "checkoutView":
                    new CheckoutView();
                    break;
                
                case "delCheckoutItem":
                     $_SESSION['cart']->deleteFromCart($_GET['cartID']);
                     new CheckoutView();
                    break;
                    
                
            
            }
  
        
   }
   
   function handleSearch(){
       
        $_SESSION['route']=$_POST['route'];
        $_SESSION['seatCount']=$_POST['seatCount'];
        
        
        
        new ResultsView($_POST['route'], $_POST['seatCount'],$_POST['date']);
       
   }
   
   function handleAddToCart(){
       $flightInfo = array();
       $flightInfo['route']= $_SESSION['route'];
       $flightInfo['seatCount']= $_SESSION['seatCount'];
       

       $flightInfo['date']= $_POST['flightDate'];

       $_SESSION['cart']->addToCart($flightInfo);
       new SearchView(); 
       
       
       
       
       
       
   }
   
   
   
?>