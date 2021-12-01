# Laravel Forms Entries

[![Packagist License](https://img.shields.io/packagist/l/yaroslawww/laravel-forms-entries?color=%234dc71f)](https://github.com/yaroslawww/laravel-forms-entries/blob/master/LICENSE.md)
[![Packagist Version](https://img.shields.io/packagist/v/yaroslawww/laravel-forms-entries)](https://packagist.org/packages/yaroslawww/laravel-forms-entries)
[![Build Status](https://scrutinizer-ci.com/g/yaroslawww/laravel-forms-entries/badges/build.png?b=master)](https://scrutinizer-ci.com/g/yaroslawww/laravel-forms-entries/build-status/master)
[![Code Coverage](https://scrutinizer-ci.com/g/yaroslawww/laravel-forms-entries/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/yaroslawww/laravel-forms-entries/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/yaroslawww/laravel-forms-entries/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/yaroslawww/laravel-forms-entries/?branch=master)

Package to save forms entries (like contact us forms ...) and send notifications

## Installation

Install the package via composer:

```bash
composer require yaroslawww/laravel-forms-entries
```

You can publish the assets file with:

```bash
php artisan vendor:publish --provider="FormEntries\ServiceProvider" --tag="config"
php artisan vendor:publish --provider="FormEntries\ServiceProvider" --tag="lang"
```

To disable default migrations add this code to app service provider:

```injectablephp
\FormEntries\FormEntryManager::ignoreMigrations()
```

You can add default routes to your `web.php`

```injectablephp
FormEntryManager::routes();
```

## Usage

### Use predefined classes

```injectablephp
$formEntry = UniversalForm::make()
                ->enableStoringData()
                ->enableNotifications()
                ->process($request);
```

### Use custom form and content

```injectablephp
class ContactUsFormContent extends FormContent
{
    protected array $requestKeysToSave = ['email', 'message'];

    public function validateRequest(Request $request): static
    {
        $request->validate([
            'email'   => ['required', 'email'],
            'message' => ['required', 'min:10', 'max:500'],
        ]);

        return $this;
    }
}
```

```injectablephp
class ContactUsForm extends Form
{
    protected string $formContentClass = ContactUsFormContent::class;

    public function notify(FormEntry $model): bool
    {
        Notification::route('mail', 'tester@test.admin')
                    ->notify(new ($this->getFormNotificationClass())($model->content));

        return true;
    }
}
```

## Credits

- [![Think Studio](https://yaroslawww.github.io/images/sponsors/packages/logo-think-studio.png)](https://think.studio/)
