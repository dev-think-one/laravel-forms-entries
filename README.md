# Laravel Forms Entries

Package to save forms entries and send notifications

## Installation

Install the package via composer:

```bash
composer require yaroslawww/laravel-forms-entries
```

You can publish the config file with:

```bash
php artisan vendor:publish --provider="FormEntries\ServiceProvider" --tag="config"
```

## Usage

```injectablephp

class ContactUsForm extends \FormEntries\Forms\AbstractForm
{

    public function notify(FormEntry $model): bool
    {
       
        $users = User::whereIn('email', [
            'my.user@email.uk',
            'other.user@email.uk',
        ])->get();
        if ($users->count()) {
            /** @var User $user */
            foreach ($users as $user) {
                $user->notify(new ContactUsRequestReceived($model->content));
            }
        }
        
        Notification::route('mail', [
            'direct@example.uk' => 'Support',
        ])->notify(new ContactUsRequestReceived($model->content));

        return true;
    }

    public function validate(Request $request): self
    {
        $request->validate([
            'subject' => [ 'required', 'string', 'max:200' ],
            'message' => [ 'required', 'string', 'max:3000' ],
        ]);

        return $this;
    }
}

$entry = ContactUsForm::make()->process($request);
```

## Credits

- [![Think Studio](https://yaroslawww.github.io/images/sponsors/packages/logo-think-studio.png)](https://think.studio/)
