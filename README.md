# Laravel Forms Entries

[![Packagist License](https://img.shields.io/packagist/l/yaroslawww/laravel-forms-entries?color=%234dc71f)](https://github.com/yaroslawww/laravel-forms-entries/blob/main/LICENSE.md)
[![Packagist Version](https://img.shields.io/packagist/v/yaroslawww/laravel-forms-entries)](https://packagist.org/packages/yaroslawww/laravel-forms-entries)
[![Total Downloads](https://img.shields.io/packagist/dt/yaroslawww/laravel-forms-entries)](https://packagist.org/packages/yaroslawww/laravel-forms-entries)
[![Build Status](https://scrutinizer-ci.com/g/yaroslawww/laravel-forms-entries/badges/build.png?b=main)](https://scrutinizer-ci.com/g/yaroslawww/laravel-forms-entries/build-status/main)
[![Code Coverage](https://scrutinizer-ci.com/g/yaroslawww/laravel-forms-entries/badges/coverage.png?b=main)](https://scrutinizer-ci.com/g/yaroslawww/laravel-forms-entries/?branch=main)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/yaroslawww/laravel-forms-entries/badges/quality-score.png?b=main)](https://scrutinizer-ci.com/g/yaroslawww/laravel-forms-entries/?branch=main)

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
use FormEntries\Forms\Form;
use FormEntries\Forms\FormContent;

\FormEntries\FormEntryManager::ignoreMigrations()

Form::typesMap([
    'form-contact' => ContactUsForm::class,
]);
FormContent::typesMap([
    'contact-us' => ContactUsFormContent::class,
]);
```

You can add default routes to your `web.php`

```injectablephp
FormEntryManager::routes();
```

## Usage

### Use predefined classes

In case you do not need custom classes with validation.

```injectablephp
$formEntry = UniversalForm::make()
                ->enableStoringData()
                ->enableNotifications()
                ->process($request);
```

### Use custom form and content

```injectablephp
// /app/Http/FormEntries/FormContent/ContactUsFormContent.php

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
// /app/Http/FormEntries/Forms/ContactUsForm.php

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

```html
<form action="{{route('forms-entries.submit')}}"
      method="post"
>
    @csrf
    <input type="hidden"
           name="{{config('forms-entries.routing.form_name_parameter')}}"
           value="{{\App\Http\FormEntries\Forms\FolioMetricsForm::getType()}}">
    Other fields
    <button type="submit">Submit</button>
</form>
```

## Credits

- [![Think Studio](https://yaroslawww.github.io/images/sponsors/packages/logo-think-studio.png)](https://think.studio/)
