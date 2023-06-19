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
        $stmt = $this->conn->prepare(
            "INSERT INTO currency_rates (currency_code, rate, fetch_date) VALUES (?, ?, ?)"
        );
        $stmt->bind_param("sds", $currency_code, $rate, $date);
        $stmt->execute();
    }
}
