# Table of Contents

- [Quick installation](#quick-installation)
- [Testing](#testing)
    - [Expected result](#expected-result)
    - [After testing](#after-testing-(important))
- [API Documentation](#API-Documentation)
    - [Register](#register)
    - [Login](#login)
    - [Friendships](#Friendships)
    - [Tweets](#tweets)
    - [Report](#report)

---
# Quick installation
1- start by installing required dependencies with composer

```
composer install
```

2- Create a new database and update you `.env` file with the required information

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE={{DatabaseName}}
DB_USERNAME=root
DB_PASSWORD=
```
3- Make the required migrations
```
php artisan migrate:fresh
```
4- Use passport for authentication
```
php artisan passport:install
```
[Back to Top](#Table-of-Contents)
<hr>

# Testing
```
composer test
```
### Expected Result
```
> vendor/bin/phpunit --testdox
PHPUnit 9.5.4 by Sebastian Bergmann and contributors.

Authentication (Tests\Feature\Authentication)
 ✔ Required fields for registration
 ✔ Too short password length constraint
 ✔ Invalid date of birth date constraint
 ✔ Successful registeration
 ✔ Login must enter email and password
 ✔ Account is locked after too many login attempts
 ✔ Successful login

Followers (Tests\Feature\Followers)
 ✔ Deny unauthenticated user follow request
 ✔ User can not follow himself
 ✔ User can not follow another same user again
 ✔ Successful user follow

Tweeting (Tests\Feature\Tweeting)
 ✔ Required fields while adding tweet
 ✔ Deny tweets bigger than 140 characters
 ✔ Succesfully posting tweet

 Time: XX:XX.XX, Memory: XX.XX MB

 OK (14 tests, 33 assertions)
 ```
 ## After testing (important)
 > Please make sure to run 
 ```php artisan migrate:fresh``` and ```php artisan passport:install``` again to test the api via external resource as Postman<br>
 > It's advisable to use a testing database because the tables gets dropped after the tests

[Back to Top](#Table-of-Contents)
<hr>
 
# API Documentation

## Register
> Register a new user

`POST {{url}}/api/v1/register/` 

> Request example:

`{{url}}/api/v1/register/`

* Request body
```
{
    "image": {{uploaded_image}}
    "name": "Omar Yehia"
    "password": "1234598798"
    "email": "admin@omar.com"
    "date_of_birth": "1994-09-04"
}
```

> Response example: `(Status: 201)`
```
{
    "success": true,
    "data": {
        "user_id": 1,
        "access_token": "{{token}}"
    }
}
```


> Errors:
* invalid_arguments `(Status: 400)`
```
{
    "success": false,
    "errors": {
        "name": [
            "The name field is required."
        ],
        "password": [
            "The password field is required."
        ],
        "email": [
            "The email field is required."
        ],
        "date_of_birth": [
            "The date of birth field is required."
        ],
        "image": [
            "The image field is required."
        ]
    }
}
```
* data_base_error `(Status: 500)`
```
{
    "success": false,
    "errors": "Database error"
}
```
[Back to Top](#Table-of-Contents)
<hr>

## Login
> Login a user

`POST {{url}}/api/v1/login/` 

> Request example:

`{{url}}/api/v1/login/`

* Request body
```
{
    "email": "admin@omar.com"
    "password": "1234598798"
}
```

> Response example: `(Status: 200)`
```
{
    "success": true,
    "data": {
        "access_token": "{{token}}"
    }
}
```

> Errors:
* invalid_credentials  `(Status: 400)`
    * Wrong username or password
```
{
    "success": false,
    "errors": "Invalid credentials"
}
```
* too_many_unsuccessful_attempts  `(Status: 400)`
    * Wrong username or password for 5 times
    * This locks the account for 30 minutes
```
{
    "success": false,
    "errors": "Too many login attemps. You're account is locked for 30 minutes."
}
```
* data_base_error `(Status: 500)`
```
{
    "success": false,
    "errors": "Database error"
}
```
[Back to Top](#Table-of-Contents)
<hr>

## Friendships
> Following a user

`POST {{url}}/api/v1/friendships/{{person_to_be_followed_id}}` 

>### Important: This is a protected route. You need to have authorization token.
> Requires Header:<br>
Authorization: Bearer {{authentication_token}}

Request example:
> User with id = 1 trying to follow user with id = 2

`{{url}}/api/v1/friendships/2`

> Response example: `(Status: 200)`
```
{
    "success": true,
    "data": {
        "message": "User followed successfuly."
    }
}
```

> Errors:
* unauthenticated_user `(Status: 401)`
```
{
    "success": false,
    "errors": "Unauthorized for this action."
}
```
* follow_self `(Status: 400)`
```
{
    "success": false,
    "errors": "Can't follow self."
}
```
* requested_user_not_found `(Status: 404)`
```
{
    "success": false,
    "errors": "Followee not found."
}
```
* data_base_error `(Status: 500)`
```
{
    "success": false,
    "errors": "Database error"
}
```
[Back to Top](#Table-of-Contents)
<hr>

## Tweets
> Post a tweet

`POST {{url}}/api/v1/tweets` 

### Important: This is a protected route. You need to have authorization token.
Requires Header:<br>
Authorization: Bearer {{authentication_token}}

> Request example:

`{{url}}/api/v1/tweets`

* Request body
```
{
    "text": "Some tweet text less than 140 characters"
}
```

> Response example: `(Status: 201)`
```
{
    "success": true,
    "data": {
        "message": "Tweet posted successfuly."
    }
}
```

> Errors:
* unauthenticated_user `(Status: 401)`
```
{
    "success": false,
    "errors": "Unauthorized for this action."
}
```
* invalid_request `(Status: 400)`
```
{
    "success": false,
    "errors": {
        "text": [
            "The text must not be greater than 140 characters."
        ]
    }
}
```

* data_base_error `(Status: 500)`
```
{
    "success": false,
    "errors": "Database error"
}
```
[Back to Top](#Table-of-Contents)
<hr>

## Report
> Download a user report with users and their number of tweets and tweets/user average

`GET {{url}}/api/v1/report/download` 

> Open this url from your browser to get a download subwindow

> Errors:

* data_base_error `(Status: 500)`
```
{
    "success": false,
    "errors": "Database error"
}
```
[Back to Top](#Table-of-Contents)