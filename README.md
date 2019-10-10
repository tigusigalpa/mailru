# Mail.Ru for business API

PHP wrapper of [Mail.Ru for business](https://biz.mail.ru/) to work with the API. For the moment its only [Sendbox](https://biz.mail.ru/sendbox/) integration. Official documentation https://help.mail.ru/biz/sendbox/api

![mailru-for-business-logo](https://user-images.githubusercontent.com/2721390/66565183-89a71500-eb6a-11e9-863b-09da5feca3f7.png)

## Install

### Client side

1. **[Add your domain to Mail.Ru](https://help.mail.ru/biz/domain/verification_settings/other/confirm)**. Approve domain via any available method: DNS TXT record or HTML file or HTML meta-tag or change domain NS records to mail.ru
2. **[Configure domain MX-records](https://help.mail.ru/biz/domain/verification_settings/other/mx)**. Keep only Mail.Ru backed MX-records:
   - Host/subdomain: `@`
   - Value: `emx.mail.ru`
   - Priority: `10`
3. **[Configure SPF-record](https://help.mail.ru/biz/domain/verification_settings/other/spf)** (TXT domain record):
   - Host/subdomain: `@`
   - Value: `v=spf1 redirect=_spf.mail.ru`
   - TTL: `21600`
   
   _If you are using any paid services like SMTP, your SPF-record may be another but only single one. For example, if you are using an SMTP service, SPF (TXT) record will be `v=spf1 include:send-box.ru include:smtp.send-box.ru redirect=_spf.mail.ru`_
4. **[Configure DKIM signature](https://help.mail.ru/biz/domain/records/dkim-all)**. _There may be a up to 3 DKIM records, depending on number of connected services._
5. **Get API credentials**. Go to [https://mailer.i.bizml.ru/settings/](https://mailer.i.bizml.ru/settings/), select an **API** tab, activate REST API, copy **ID** (CLIENT ID) and **Secret** (CLIENT SECRET).

### Developer side

``` bash
$ composer require tigusigalpa/mailru
```

``` php
require_once 'vendor/autoload.php';
```

## Sendbox

### Email

``` php
$email = new \Tigusigalpa\MailRu\Sendbox\Email($client_id, $client_secret);
```

### SMS

``` php
$sms = new \Tigusigalpa\MailRu\Sendbox\SMS($client_id, $client_secret);
```

### SMTP

``` php
$smtp = new \Tigusigalpa\MailRu\Sendbox\SMTP($client_id, $client_secret);
```

#### Available methods

Name | Arguments
-----|-------------
`sendEmail(array $email)` | `$email = ['html' => (string), 'text' => (string), 'template' => ['id' => (int), 'variables' => (string)], 'subject' => (string)]`

Coming soon...

## PHPUnit tests
Use environment variables to test API:
- **CLIENT_ID** _(required)_
- **CLIENT_SECRET** _(required)_
- **TIMEOUT** _(optional)_

All coverage tests are coming soon...

## License

MIT License

## Author

[Igor Sazonov](https://twitter.com/tigusigalpa) ([sovletig@gmail.com](mailto:sovletig@gmail.com))