<?php
require_once 'config.php';

class CurrencyRateFetcher
{
    private $conn;

    public function __construct()
    {
        $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    public function fetchRates()
    {
        $url = 'http://api.nbp.pl/api/exchangerates/tables/A?format=json';
        $response = file_get_contents($url);
        $data = json_decode($response);

        if (is_array($data)) {
            $rates = $data[0]->rates;
            $date = $data[0]->effectiveDate;

            foreach ($rates as $rate) {
                $this->saveRate($rate->code, $rate->mid, $date);
            }
        }
    }

    private function saveRate($currency_code, $rate, $date)
    {
        $stmt = $this->conn->prepare("SELECT * FROM currency_rates WHERE currency_code = ?");
        $stmt->bind_param("s", $currency_code);
        
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $stmt = $this->conn->prepare(
                "UPDATE currency_rates SET rate = ? WHERE currency_code = ? AND fetch_date = ?"
            );
            $stmt->bind_param("dss", $rate, $currency_code, $date);
            $stmt->execute();
            
        } else {
            $stmt = $this->conn->prepare(
                "INSERT INTO currency_rates (currency_code, rate, fetch_date) VALUES (?, ?, ?)"
            );
            $stmt->bind_param("sds", $currency_code, $rate, $date);
            $stmt->execute();    
        }
        
    }

    public function getAllRates()
    {
        $stmt = $this->conn->prepare("SELECT * FROM currency_rates");
        
        $stmt->execute();
        
        $result = $stmt->get_result();
        return $result;        
    }

}
