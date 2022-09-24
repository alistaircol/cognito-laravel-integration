Simple Laravel app using Cognito API calls.

Other than `terraform` files, relevant files:

* [`src/config/auth.php`](https://github.com/alistaircol/cognito-laravel-integration/blob/main/src/config/auth.php#L111)
* [`src/routes/web.php`](https://github.com/alistaircol/cognito-laravel-integration/blob/main/src/routes/web.php)
* [`src/app/Http/Controllers/IndexController.php`](https://github.com/alistaircol/cognito-laravel-integration/blob/main/src/app/Http/Controllers/IndexController.php)
* [`src/app/Http/Controllers/LoginController.php`](https://github.com/alistaircol/cognito-laravel-integration/blob/main/src/app/Http/Controllers/LoginController.php)
* [`src/app/Http/Controllers/LoginSuccessController.php`](https://github.com/alistaircol/cognito-laravel-integration/blob/main/src/app/Http/Controllers/LoginSuccessController.php)
* [`src/app/Http/Controllers/LogoutController.php`](https://github.com/alistaircol/cognito-laravel-integration/blob/main/src/app/Http/Controllers/LogoutController.php)
* [`src/app/Http/Controllers/LogoutSuccessController.php`](https://github.com/alistaircol/cognito-laravel-integration/blob/main/src/app/Http/Controllers/LogoutSuccessController.php)
* [`src/app/Console/Commands/CreateAdminToken.php`](https://github.com/alistaircol/cognito-laravel-integration/blob/main/src/app/Console/Commands/CreateAdminToken.php)
* [`src/app/Console/Commands/DecodeToken.php`](https://github.com/alistaircol/cognito-laravel-integration/blob/main/src/app/Console/Commands/DecodeToken.php)

More context [here](https://ac93.uk/articles/laravel-integration-with-amazon-cognito/)

```bash
git clone git@github.com:alistaircol/cognito-laravel-integration.git
cd cognito-laravel-integration

# create cognito pool, clients, users
terraform init
terraform fmt
terraform plan
terraform apply

# set up the integration
cd src
touch database/database.sqlite
composer install
php artisan migrate
php artisan db:seed

# figure out how to run web server :)
# I used valet
composer global require laravel/valet
export PATH="$PATH:$(realpath ~/.composer/vendor/bin)"
valet install
valet link --secure t3.dafedteam
```
