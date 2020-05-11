# Development

# Code Style

Code should use [Yii 2 Core Framework Code Style](https://github.com/yiisoft/yii2/blob/master/docs/internals/core-code-style.md).

# Specification

RestAPI will follow [OpenAPI Specification, version 3](http://spec.openapis.org/oas/v3.0.3).

# [API Host and Base URL](https://swagger.io/docs/specification/api-host-and-base-path)

- https://devdata.frontline.ro/v1/

# Methods

### Exchange rates

#### 1. All rates

- https://devdata.frontline.ro/v1/filter/2012-01-5
- Example response:

```
{
    "date": "2012-01-05",
    "rate": {
        "EUR":"4.3398",
        "CHF":"3.5629",
        "AUD":"3.4648",
        "USD":"3.3796",
        "UAH":"0.4194",
        "CAD":"3.3165",
        "BRL":"1.8444",
        "PLN":"0.9596",
        "RUB":"0.1057",
        "CZK":"0.1672",
        "BGN":"2.2189",
        "NOK":"0.5646",
        "MDL":"0.2867",
        "GBP":"5.2549"
}
```

#### 2. Single rate

- https://devdata.frontline.ro/v1/filter/2012-01-5/EUR
- Example response:

```
{
    "date": "2012-01-05",
    "rate": "4.3398"
}
```
