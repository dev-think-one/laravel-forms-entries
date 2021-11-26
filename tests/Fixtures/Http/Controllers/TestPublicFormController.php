<?php

namespace FormEntries\Tests\Fixtures\Http\Controllers;

use Exception;
use FormEntries\Forms\Form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class TestPublicFormController
{
    public function __invoke(Request $request)
    {
        $formClass = Form::getClassByType($request->form_type_field);
        if (is_a($formClass, Form::class, true)) {
            return Response::json($formClass::make()->process($request));
        }

        throw new Exception('Test Error');
    }
}
