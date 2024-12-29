# Herepay SDK

A PHP SDK for integrating with the Herepay payment gateway. This package provides a simple and efficient way to interact with the Herepay API for managing transactions, retrieving payment channels, and more.

## Installation

Install the package via Composer:

```bash
composer require Herepay/herepay-php-sdk
```

If using Laravel, publish the configuration file:

```bash
php artisan vendor:publish --provider="HerepaySDK\HerepayServiceProvider" --tag=config
```

## Configuration

Ensure you add the following environment variables to your `.env` file:

```env
HEREPAY_SANDBOX=true
HEREPAY_SECRET_KEY=your_secret_key
HEREPAY_API_KEY=your_api_key
HEREPAY_PRIVATE_KEY=your_private_key
```

Or you can manually configure the SDK by passing the configuration array when initializing the `HerepayService`.

## Usage

### Initialize the SDK

```php
use HerepaySDK\HerepayService;

$config = [
    'sandbox' => true, // Use false for production
    'secret_key' => 'your_secret_key',
    'api_key' => 'your_api_key',
    'private_key' => 'your_private_key',
];

$herepay = new HerepayService($config);
```

### Get Payment Channels

Retrieve available payment channels:

```php
$paymentChannels = $herepay->getPaymentChannels();
echo $paymentChannels;
```

### Initiate transaction

Initiate transaction, response will redirect to Acquiring Bank

```php
$transactionData = [
    'payment_code' => 'REF-123',
    'created_at' => date('Y-m-d H:i:s'),
    'amount' => 100,
    'name' => 'John Doe',
    'email' => 'john.doe@example.com',
    'phone' => '0123456789',
    'description' => 'Test Transaction',
    'bank_prefix' => 'TEST0021',
    'payment_method' => 'Online Banking',
];

$transactionData['checksum'] = $herepay->generateChecksum($transactionData);
$response = $herepay->initiate($transactionData);
header($response);
```

### Get Transaction Details

Retrieve the details of a transaction:

```php
$referenceCode = 'HP-INVAPI-XXXXXXXXXX';
$transactionDetails = $herepay->getTransactionDetails($referenceCode);
echo $transactionDetails;
```

### Get Latest Transaction

Retrieve the latest transaction:

```php
$transactions = $herepay->getTransactions();
echo $transactions;
```

### Generate Checksum

Generate a checksum for data validation:

```php
$transactionData = [
    'payment_code' => 'REF-123',
    'created_at' => date('Y-m-d H:i:s'),
    'amount' => 100,
    'name' => 'John Doe',
    'email' => 'john.doe@example.com',
    'phone' => '0123456789',
    'description' => 'Test Transaction',
    'bank_prefix' => 'TEST0021',
    'payment_method' => 'Online Banking',
];

$checksum = $herepay->generateChecksum($transactionData);
echo $checksum;
```

## Testing

Run the tests using PHPUnit:

```bash
vendor/bin/phpunit --testdox tests
```

Ensure that the `.env.testing` file is properly set up with sandbox credentials.

## Contributing

Contributions are welcome! Please fork this repository, make your changes, and submit a pull request.

## License

This package is open-sourced software licensed under the [MIT license](LICENSE).