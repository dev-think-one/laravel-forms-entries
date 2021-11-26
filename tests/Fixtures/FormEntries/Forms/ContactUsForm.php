<?php

namespace FormEntries\Tests\Fixtures\FormEntries\Forms;

use FormEntries\Forms\Form;
use FormEntries\Models\FormEntry;
use FormEntries\Tests\Fixtures\FormEntries\Content\ContactUsFormContent;
use Illuminate\Support\Facades\Notification;

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
