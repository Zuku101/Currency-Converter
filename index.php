<?php
require_once 'config.php';
require_once 'CurrencyRateFetcher.php';
require_once 'CurrencyConverter.php';

$fetcher = new CurrencyRateFetcher();
$fetcher->fetchRates();

$converter = new CurrencyConverter();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sourceCurrency = $_POST['source_currency'];
    $targetCurrency = $_POST['target_currency'];
    $amount = $_POST['amount'];

    $convertedAmount = $converter->convert($sourceCurrency, $targetCurrency, $amount);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Currency Converter</title>
</head>

<body>
    <form action="" method="post">
        <input type="number" name="amount" placeholder="Amount" required>
        <select name="source_currency" required>
            <?php foreach ($fetcher->getAllRates() as $rate) : ?>
                <option value="<?= $rate['currency_code'] ?>"><?= $rate['currency_code'] ?></option>

            <?php endforeach; ?>
        </select>
        <select name="target_currency" required>
            <?php foreach ($fetcher->getAllRates() as $rate) : ?>
                <option value="<?= $rate['currency_code'] ?>"><?= $rate['currency_code'] ?></option>

            <?php endforeach; ?>
        </select>
        <button type="submit">Convert</button>
    </form>

    <?php if (isset($convertedAmount)) : ?>
        <p>
            <?= number_format($amount, 2) ?> <?= $sourceCurrency ?> is equal to
            <?= number_format($convertedAmount, 2) ?> <?= $targetCurrency ?>.
        </p>
    <?php endif; ?>

    <h2>Conversion History</h2>
    <table>
        <thead>
            <tr>
                <th>Source Currency</th>
                <th>Target Currency</th>
                <th>Amount</th>
                <th>Converted Amount</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($converter->getConversionHistory() as $conversion) : ?>
                <tr>
                    <td><?= $conversion['source_currency'] ?></td>
                    <td><?= $conversion['target_currency'] ?></td>
                    <td><?= number_format($conversion['amount'], 2) ?></td>
                    <td><?= number_format($conversion['converted_amount'], 2) ?></td>
                    <td><?= $conversion['conversion_date'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>

</html>