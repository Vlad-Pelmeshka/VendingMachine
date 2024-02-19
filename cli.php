<?php

class VendingMachine
{
    private $available_coins = array(
        '0.01',
        '0.05',
        '0.10',
        '0.25',
        '0.50',
        '1.00',
    );
    private $count_roducts = 0;
    private $currency = '$';
    
    private $available_products = array(
        '0' => array(
            'title' => "Coca-cola",
            'price' => 1.5,
            'count' => 3,
        ),
        '1' => array(
            'title' => "Snickers",
            'price' => 1.2,
            'count' => 6,
        ),
        '2' => array(
            'title' => "Lay's",
            'price' => 2,
            'count' => 1,
        ),
    );

    function __construct() {
        self::showHeader();
        
    }

    public function createOrder() {

        if(self::isProductsInMachine() != 0){
            print "Now u can select to buy next products:\n";
            
            self::displayProductList();
            
            do { 
                print "\nAre you ready to make an order? (Press 'Y' or 'N'): \n";
                $b = fgetc(STDIN);
                
            
                if ($b == 'Y' || $b == 'y') {
                    break;
                }
            
                if ($b == 'N' || $b == 'n') {
                    
                    self::showHeader();
                    echo "Looking forward to seeing you next time\n";
                    return;
                }
                
                echo "\nSorry. But your entry is incorrect. ";
            } while (true);


            self::showHeader();
            print "\nLets do it! ";
            
            do {
                print "Please choose one of the products (Input his number): \n";

                self::displayProductList(true);
            } while (false);

        }else{
            print "Sorry, but all of our products have now been sold out.\n";
        }
        
        self::showHeader();
        print "We look forward to seeing you again with your money. Thank you for choosing us!\n";
        
    }

    private function showHeader(){
        self::cls();
        print "\n------------------------------------\nWelcome to VendingMachine simulator!\n------------------------------------\n\n";
    }

    private function displayProductList($show_id = false){

        if(self::isProductsInMachine() == 0){
            return 0;
        }
        
        foreach($this->available_products as $key => $product){

            if($product['count'] == 0){
                continue;
            }

            print ($show_id ? $key . ') ' : '') . $this-> currency . number_format($product['price'],2) . " " . $product['title'] . "\n";
        }
        
        return $this->available_products;
    }

    public function isProductsInMachine(){

        if($this->count_roducts == 0){
            $count_roducts = 0;
            
            foreach($this->available_products as $product){
                $count_roducts += $product['count'];
            }
            $this->count_roducts = $count_roducts;
        }

        return $this->count_roducts;
    }

    private function cls(){
        print("\033[2J\033[;H");
    }

    /*private function getProductById($id){
        return $this->available_products[$id] ?? '';
    }

    public function getProductTitle($id){
        $data = self::getProductById($id);
        return $data ? $data['title'] : '';
    }

    public function getProductPrice($id){
        $data = self::getProductById($id);
        return $data ? $data['price'] : '';
    }

    public function getProductCount($id){
        $data = self::getProductById($id);
        return $data ? $data['count'] : '';
    }*/
}

$VM = new VendingMachine();

$VM->createOrder();

// echo $VM->getProductTitle(1);

    //$b = fread(STDIN, 1); 
    // $b = rtrim($b, "\n");
?>
