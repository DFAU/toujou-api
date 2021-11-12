# Toujou API

REST API for TYPO3 CMS based on [{json:api}](https://jsonapi.org/)

Useful german youtube video https://www.youtube.com/watch?v=WoOuNe_rzpM

## Installation

Require and install the plugin

    $ composer require dfau/toujou-api
    $ vendor/bin/typo3cms extension:install toujou_api 

## Configuration

To make the API work you have to do the following:

- Check *Sites Management* for api entrypoint
- Create a BE user with OAuth2 client_id and client_secrect (OAuth2 tab)
- Add and configure API resources (see [Resources.php](Configuration/ToujouApi/Resources.php))
- Add and configure API routes (see [Resources.php](Configuration/ToujouApi/Routes.php))

## Security

The API will be secured to prevent unwarranted requests.

You can obtain an access token by sending a POST request to `/_api/token` with following parameters:

| key           | value               |
|---------------|---------------------|
| grant_type    | client_credentials  |
| client_id     | <CLIENT_ID>         |
| client_secret | <CLIENT_SECRET>     |


Get auth token via request (Example):
```bash
curl --location --request POST 'https://rms.dfau.dev/_api/token' \
    --form 'grant_type="client_credentials"' \
    --form 'client_id="12fc7d65-0177-48be-9c3c-e7d8b2a1"' \
    --form 'client_secret="131"'
```

On valid credentials the json response will contain an access token:

```json
{
  "token_type": "Bearer",
  "expires_in": 86400,
  "access_token": "eyJ0eXAiOiJ..."
}
```

For all following requests you need to use this access token by adding following line to the request header

    Authorization : Bearer <ACCESS_TOKEN>

Example requests:

```bash
curl --location --request GET 'https://rms.dfau.dev/_api/pages/' \
    --header 'Authorization: Bearer eyJ0eXAi....' \
```

## Development

Install php dependencies using composer:

    $ composer install

#### [PHPUnit](https://phpunit.de) Unit tests

    $ etc/scripts/runTests.sh

#### [PHPUnit](https://phpunit.de) Functional tests

    $ etc/scripts/runTests.sh -s functional

#### [Codeception](https://codeception.com/) Acceptance tests

    $ etc/scripts/runTests.sh -s acceptance


#### [Easy-Coding-Standard](https://github.com/Symplify/EasyCodingStandard)

Check coding standard violations

    $ etc/scripts/checkCodingStandards.sh

Fix coding standard violations automatically

    $ etc/scripts/checkCodingStandards.sh --fix


## Documentation

Make `dockrun_t3rd available in current terminal

    source <(docker run --rm t3docs/render-documentation show-shell-commands)

Run `dockrun_t3rd`

    dockrun_t3rd makehtml

Open documentation

    open "Documentation-GENERATED-temp/Result/project/0.0.0/Index.html"
