CREATE DATABASE IF NOT EXISTS currency_converter;


USE currency_converter;


CREATE TABLE IF NOT EXISTS exchange_rates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    currency_code CHAR(3) NOT NULL,
    rate DECIMAL(10, 4) NOT NULL,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE (currency_code)
);


CREATE TABLE IF NOT EXISTS conversion_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    source_currency CHAR(3) NOT NULL,
    target_currency CHAR(3) NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    converted_amount DECIMAL(10, 2) NOT NULL,
    conversion_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
