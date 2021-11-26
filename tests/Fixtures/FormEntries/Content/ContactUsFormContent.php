<?php

namespace FormEntries\Tests\Fixtures\FormEntries\Content;

use FormEntries\Forms\FormContent;
use Illuminate\Http\Request;

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
