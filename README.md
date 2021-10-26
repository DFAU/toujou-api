# Toujou API

Useful german youtube video https://www.youtube.com/watch?v=WoOuNe_rzpM

To make the API work you have to do the following:
- check Sites Management for api entrypoint
- Create BE user along with credentials. (new tab in the BE record)

Get auth token via request:
```
POST https://www.example.de/_api/token
Content-Type: application/json

{
  "grant_type": "client_credentials",
  "client_id": "XXXBEUSERIDXXX",
  "client_secret": "XXXTESTXXX"
}

> {%
    client.global.set("auth_token", response.body.access_token);
%}
```


Example requests:

```
GET https://www.example.de/_api/pages/1?include=content-elements
Authorization: Bearer {{auth_token}}
Content-Type: application/vnd.api+json
Accept: application/vnd.api+json
```

`/_api/pages/1?fields%5Bpages%5D=title,subtitle`

`/_api/group-trips/ABC?fields%5Bgroup-trips%5D=pax_min,pax_max,duration,trip_country,destination_isocodes,title,subtitle,tripdescription`


@todo:
- Fix runTests.sh as no typo3conf is set created in .Build/

## Installation

Require and install the plugin

    $ composer require toujou/{{lowercase_projectname}}
    $ vendor/bin/typo3cms extension:install toujou_api

## Development

Install php dependencies using composer:

    $ composer install

#### [PHPUnit](https://phpunit.de) Unit tests

    $ etc/scripts/runTests.sh

#### [PHPUnit](https://phpunit.de) Functional tests

    $ etc/scripts/runTests.sh -s functional


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