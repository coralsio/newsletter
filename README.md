# Corals Newsletter

- If you prefer to manage your email subscribers under Laraship platform, then the Newsletter module is your ready to go solution, create an unlimited mailing list and assign subscribers into by adding them manually or using smart upload wizard to attach them to one or multiple lists.

<p><img src="https://www.laraship.com/wp-content/uploads/2018/10/laraship_laravel_newsletter_1.png" alt="" width="598" height="268"></p>  

- Schedule your campaigns, create an email template, assign it to one or multiple mailing listing, you can even assign it to individual visitors.

- then you can check an online statistic about the mailed listing, not only on how many got sent/opened but also about visitor statistics like Browser, device, country.

<p><img src="https://www.laraship.com/wp-content/uploads/2018/10/laraship_laravel_newsletter_2.png" alt="" width="603" height="280"></p>
<p>&nbsp;</p>

- an Unsubscribe URL will be attached automatically to the email body through newsletter subscribers and click and unsubscribe.

- Emails are sent using Queue to avoid any performance issues and ensure best deliverable method.

- Make sure your email / SMTP settings are correct under. env file

```php
MAIL_DRIVER=log
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=laraship@corals.io

MAIL_FROM_NAME="Laraship"
```
<p>&nbsp;</p>

- Emails are running synchronously if you’re running large bulk emails its recommend to setup laravel queue
Adding Table for Queues
Let’s now add the table for queued jobs and failed the job. For that, run the following Artisan commands:

```ph
php artisan queue:table
php artisan queue:failed-table
```

- Migrating the Tables
Now that I have all the required tables, let’s now migrate it using the following Artisan command:

``` php
php artisan migrate
```
Once the command is completed, all the tables will be generated.

- Updating the .env File
update the .env file with the mail and the queue driver information.

```php
QUEUE_DRIVER=database
```

<p><img src="https://www.laraship.com/wp-content/uploads/2018/10/laraship_laravel_newsletter_3.png" alt="" width="689" height="1338"></p>
<p>&nbsp;</p>

## Installation

You can install the package via composer:

```bash
composer require corals/newsletter
```

## Testing

```bash
vendor/bin/phpunit vendor/corals/newsletter/tests 
```
