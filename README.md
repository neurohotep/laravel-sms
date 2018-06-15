# Laravel SMS
Simple Laravel 5 package for sending SMS.

## Requirements

- Laravel >= 5.5
- PHP >= 7.0

## Installation

The best way to install this package is quickly and easily with <a href="https://getcomposer.org">Composer</a>.

<ol>
  <li>To install the most recent version, run the following command
      <pre><code> composer require neurohotep/laraver-sms </code></pre>
  </li>
  
  <li>Publish the config <br>
      <pre><code> php artisan vendor:publish --tag="sms"</code></pre>
  </li>

  <li>
  	Edit the <strong>config/sms.php</strong>. Set the appropriate driver and its parameters.
  </li>
</ol>

## Code Examples

```php
// send a single message
Sms::send('79123456789', 'Hello);  
 ```
 
 ## License
 [MIT License](https://github.com/neurohotep/laravel-sms/blob/master/LICENSE "MIT License")
