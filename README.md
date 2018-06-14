# Laravel SMS
Simple Laravel 5 package for sending SMS.

## Installation
<ol>
  <li>Edit the composer.json add to the require array & run composer update<br>
      <pre><code> "neurohotep/laravel-sms": "dev-master" </code></pre>
      <pre><code> composer update </code></pre>
      or just run
      <pre><code> composer require neurohotep/laraver-sms </code></pre>
  </li>
  
  <li>Publish the config <br>
      <pre><code> php artisan vendor:publish --tag="sms"</code></pre>
  </li>

  <li>
  	Edit the <strong>config/sms.php</strong>. Set the appropriate driver and its parameters.
  </li>
</ol>

## Usage

For sending single message:

```php
Sms::send('79123456789', 'Hello);  
 ```