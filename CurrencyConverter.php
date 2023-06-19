<?php
require_once 'config.php';

class CurrencyConverter
{
    private $conn;

    public function __construct()
    {
        $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    public function convert($sourceCurrency, $targetCurrency, $amount)
    {
        $sourceRate = $this->getRate($sourceCurrency);
        $targetRate = $this->getRate($targetCurrency);

        if ($sourceRate && $targetRate) {
            $convertedAmount = ($amount * $targetRate) / $sourceRate;
            $this->saveConversion($sourceCurrency, $targetCurrency, $amount, $convertedAmount);
            return $convertedAmount;
        }

        return null;
    }

    private function getRate($currencyCode)
    {
        if ($currencyCode == 'PLN') {
            return 1;
        }
        
        $stmt = $this->conn->prepare(
            "SELECT rate FROM currency_rates WHERE currency_code = ? ORDER BY fetch_date DESC LIMIT 1"
        );
        $stmt->bind_param("s", $currencyCode);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['rate'];
        }
    
        return null;
    }
    
    private function saveConversion($sourceCurrency, $targetCurrency, $amount, $convertedAmount)
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO conversion_history (source_currency, target_currency, amount, converted_amount, conversion_date) VALUES (?, ?, ?, ?, CURDATE())"
        );
        $stmt->bind_param("ssdd", $sourceCurrency, $targetCurrency, $amount, $convertedAmount);
        $stmt->execute();
    }
    
    public function getConversionHistory()
    {
        $result = $this->conn->query("SELECT * FROM conversion_history ORDER BY conversion_date DESC");
    
        $conversions = [];
        while ($row = $result->fetch_assoc()) {
            $conversions[] = $row;
        }
    
        return $conversions;
    }
}    
