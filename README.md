# Test task "Commission Calculator"
### API
Initial API Response when accessing the https://api.exchangeratesapi.io/latest API, the response received was as follows:

```json
{
  "success": false,
  "error": {
    "code": 101,
    "type": "missing_access_key",
    "info": "You have not supplied an API Access Key. [Required format: access_key=YOUR_ACCESS_KEY]"
  }
}
```
The process of obtaining an Access Key for the API was not clear. As an alternative, the https://api.apilayer.com service was used instead.

The https://api.exchangeratesapi.io endpoint was found to redirect to https://api.apilayer.com. To access the exchange rates data, the https://api.apilayer.com/exchangerates_data/latest endpoint was used.

#### API Key Requirement
To use the https://api.apilayer.com/exchangerates_data/latest API, an API key is required. You can either sign up on the website to obtain your own API key or use the provided key: `WVEM0xJzcFbbXv7qwrmv6O6AbTK1MYRL`. 

It's important to note that the free tier of this API allows only 250 requests per month, which may have already been exceeded.

Please ensure that you have the necessary access key to use the API effectively or consider exploring alternative options if the API limits are insufficient for your needs.
#### Config Path:
* file: `./config/config.php `
* config -> comfig.php -> apilayer -> apikey

### Installation
1. Run `composer install`
2. To run the application follow the command `php app.php ./var/transactions/input.txt `

## Run app locally
* start: `docker-compose up`
* stop: `docker-compose down`

## Tests
1. To run the tests: `./vendor/bin/phpunit tests`
2. To run the phpstan analyse: `./vendor/bin/phpstan analyse`