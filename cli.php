<?php

class VendingMachine
{
    private $available_coins = [
        "0.01", 
        "0.05", 
        "0.10", 
        "0.25", 
        "0.50", 
        "1.00"
    ];

    private $count_products = 0;
    private $total_money_input = 0;
    private $currency = '$';
    private $selected_product = "";

    private $available_products = [
        "0" => [
            "title" => "Coca-cola",
            "price" => 1.5,
            "count" => 2,
        ],
        "1" => [
            "title" => "Snickers",
            "price" => 1.2,
            "count" => 2,
        ],
        "2" => [
            "title" => "Lay's",
            "price" => 2,
            "count" => 1,
        ],
    ];

    function __construct()
    {
        self::showHeader();
    }

    public function __destruct()
    {
        // self::showHeader();
        print "We look forward to seeing you again with your money. Thank you for choosing us!\n";
    }

    public function createOrder($first_order = true)
    {
        if (self::isProductsInMachine() != 0) {
            if ($first_order) {
                do {
                    print "Now u can select to buy next products:\n";

                    self::displayProductList();

                    print "\nAre you ready to make an order? (Press 'Y' or 'N'): \n";

                    flush();
                    $input = fread(STDIN, 1);

                    if ($input == "Y" || $input == "y") {
                        break;
                    }

                    if ($input == "N" || $input == "n") {
                        self::showHeader();
                        return;
                    }

                    self::showHeader();
                    print "Sorry, but your entry is incorrect. ";
                } while (true);
            }

            self::selectProduct();

            self::inputMoney();

            print "Please get your " .
                self::getProductTitle($this->selected_product) .
                ".\n";
            do {
                print "Your change: " .
                    $this->currency .
                    number_format($this->total_money_input, 2) .
                    ". Would you like to choose something else? (Press 'Y' or 'N'):\n";
                flush();
                $input = fread(STDIN, 1);

                if ($input == "Y" || $input == "y") {
                    self::createOrder(false);
                    return;
                }

                self::showHeader();

                if ($input == "N" || $input == "n") {
                    self::returnUserChange();
                    return;
                }
            } while (true);
        } else {
            self::showHeader();
            print "Sorry, but all of our products have now been sold out.\n";
            self::returnUserChange();
        }
    }

    private function returnUserChange()
    {
        if ($this->total_money_input > 0) {
            print "Please take your change: " .
                $this->currency .
                number_format($this->total_money_input, 2) .
                "\n";

            $available_coins = $this->available_coins;

            $remaining_amount = $this->total_money_input;

            rsort($available_coins);

            foreach ($available_coins as $coin) {
                $coin_value = floatval($coin);
                
                if ($remaining_amount >= $coin_value) {
                    $coin_count = floor($remaining_amount / $coin_value);
                    $remaining_amount -= number_format(
                        $coin_count * $coin_value,
                        2
                    );
                    $remaining_amount = round($remaining_amount, 2);
                    echo $coin_count . " x " . $this->currency . $coin . "\n";
                }
            }
        }
    }

    private function inputMoney()
    {
        $total_money_input = $this->total_money_input;
        $total_money_need = self::getProductPrice($this->selected_product);

        self::showHeader();

        do {
            print "You chose " . self::getProductTitle($this->selected_product);
            print "\nProduct cost:       " .
                $this->currency .
                number_format($total_money_need, 2);
            print "\nMoney deposited:    " .
                $this->currency .
                number_format($total_money_input, 2);
            print "\nRemains to deposit: " .
                $this->currency .
                number_format($total_money_need - $total_money_input, 2);

            print "\n\nDeposit money: ";

            flush();
            $input = fread(STDIN, 10);

            self::showHeader();

            if (
                !is_numeric($input) ||
                !in_array(
                    number_format($input, 2),
                    $this->available_coins,
                    true
                )
            ) {
                self::showHeader();
                print "The device could not recognize your input. Please use only the following coins/bills:\n";

                self::getCorrectCoins();
                continue;
            }

            $total_money_input += (float) $input;
        } while ($total_money_need > $total_money_input);

        $this->total_money_input = $total_money_input - $total_money_need;

        self::reduceProductCount($this->selected_product);
        self::reduceTotalCount();
    }

    private function getCorrectCoins()
    {
        $currency = $this->currency;
        $coins_with_currency = array_map(function ($coin) use ($currency) {
            return $currency . number_format($coin, 2);
        }, $this->available_coins);

        print implode(", ", $coins_with_currency) . "\n\n";
    }

    private function showHeader()
    {
        self::cls();
        print "\n------------------------------------\nWelcome to VendingMachine simulator!\n------------------------------------\n\n";
    }

    private function selectProduct()
    {
        self::showHeader();
        print "Lets do it! ";

        do {
            print "Please choose one of the products (Input his code): \n";

            self::displayProductList(true);

            flush();
            $input = fread(STDIN, 10);
            $input = trim($input);

            if (!self::getProductById($input)) {
                self::showHeader();
                print "This option is not in the list. ";
            } else {
                $this->selected_product = $input;
                break;
            }
        } while (true);
    }

    private function displayProductList($show_id = false)
    {
        if (self::isProductsInMachine() == 0) {
            return 0;
        }

        foreach ($this->available_products as $key => $product) {
            if ($product["count"] == 0) {
                continue;
            }

            print ($show_id ? $key . ": " : "") .
                $this->currency .
                number_format($product["price"], 2) .
                " " .
                $product["title"] .
                "\n";
        }

        return $this->available_products;
    }

    public function isProductsInMachine()
    {
        if ($this->count_products == 0) {
            $count_products = 0;

            foreach ($this->available_products as $product) {
                $count_products += $product["count"];
            }
            $this->count_products = $count_products;
        }

        return $this->count_products;
    }

    private function cls()
    {
        print "\033[2J\033[;H";
    }

    private function getProductById($id)
    {
        // var_dump($id);
        return $this->available_products[$id] ?? "";
    }

    private function reduceTotalCount()
    {
        $this->count_products--;
    }

    public function getProductTitle($id)
    {
        $data = self::getProductById($id);
        return $data ? $data["title"] : "";
    }

    public function getProductPrice($id)
    {
        $data = self::getProductById($id);
        return $data ? $data["price"] : "";
    }

    public function reduceProductCount($id)
    {
        $this->available_products[$id]["count"]--;
    }
}

$VM = new VendingMachine();

$VM->createOrder();

?>
