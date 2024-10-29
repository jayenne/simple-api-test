# Laravel: Payment Application

## Project Specifications

**Read-Only Files**

-   tests/\*

**Environment**

-   PHP version: 8.1
-   Laravel version: 10.10
-   Default Port: 8000

**Commands**

-   run:

```bash
php artisan migrate && php artisan serve --host=0.0.0.0 --port=8000
```

-   install:

```bash
composer install
```

-   test:

```bash
php artisan migrate:refresh && ./vendor/bin/phpunit tests --log-junit junit.xml
```

## Question description

In this challenge, your task is to implement a simple REST API to accept simplified online payments. There are two kinds of entities your API must handle: Accounts and Payments.

The definitions and detailed requirements list follow. You will be graded on whether your application performs data retrieval and manipulation based on given use cases exactly as described in the requirements.

Each account has the following structure:

-   `id`: a unique integer ID of the payment
-   `account`: an integer denoting the account ID to which the payment was made
-   `amount`: an integer denoting the amount related to the payment in cents

### Example of an account JSON object:

```
{
   "id": 1,
   "account": 1,
   "amount": "1200"
}
```

## Requirements:

The REST service must expose the `/accounts/` and `/payments/` endpoints, which allow for managing the collection of Accounts and Payments in the following way:

`POST /accounts`:

-   creates a new account
-   expects an empty body payload
-   adds the given account to the collection of accounts and assigns a unique integer id to it. The first created account must have id 1, the second one 2, and so on.
-   the response code is 201, and the response body is the created account object

`GET /accounts/:id`:

-   returns an account with the given id
-   if the matching account exists, the response code is 200 and the response body is the matching account object
-   if there is no account with the given id in the collection, the response code is 404

`GET /articles/<id>`:

-   returns an article with the given id
-   if the matching article exists, the response code is 200 and the response body is the matching article object
-   if there is no article with the given id in the collection, the response code is 404

`POST /payments`:

-   creates a new payment
-   expects a body payload containing account and amount
-   if the given account doesn't exist, the response code is 404
-   if the given account exists, it creates a new payment with the given amount, relating it to the given account, and assigns a unique integer id to it. The first created payment must have id 1, the second one 2, and so on.
-   if the payment was successfully created, then the response code is 201 and the response body is the created payment object

Your task is to complete the given project so that it passes all the test cases when running the provided unit tests. The implementation of the model is given and read-only so you are not allowed to modify it. The project by default supports the use of the SQLite3 database. Implement the POST request to /accounts/ first because testing the other methods requires it to work correctly.

## Example requests and responses

`POST` request to `/accounts/`

The request body is empty.

The response code is 201, and the response body (when converted to JSON) is:

```json
{
    "id": 1,
    "balance": 0
}
```

This adds a new account with id 1 to the collection of accounts.

`POST` request to `/payments/`

Request body:

```json
{
    "account": 1,
    "amount": 1000
}
```

The response code is 201, and the response body (when converted to JSON) is:

```json
{
    "id": 1,
    "account": 1,
    "amount": 1000
}
```

This adds a new payment with id 1 to the collection of payments and relates it to the account with id 1.

`GET` request to `/accounts/1/`

Assuming that the account with id 1 exists, and has two payments related to it with amounts 1000 and 1500 respectively, then the response code is 200 and the response body (when converted to JSON) is:

```json
{
    "id": 1,
    "balance": 2500
}
```

If an account with id 1 doesn't exist, then the response code is 404 and there are no particular requirements for the response body.
