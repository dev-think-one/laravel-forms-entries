<?php

namespace FormEntries\Http\Controllers;

use FormEntries\Forms\Form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class SendFormEntryController
{
    public function __invoke(Request $request): mixed
    {
        $formClass = Form::getClassByType($request->input(config('forms-entries.routing.form_name_parameter')));
        if (is_a($formClass, Form::class, true)) {
            $formEntry = $formClass::make()->process($request);
            if ($request->expectsJson()) {
                return Response::json([
                    'message' => trans('forms-entries::messages.form_sent'),
                    'data'    => [
                        'type'     => (string) (class_exists($formEntry->type)) ? class_basename($formEntry->type) : $formEntry->type,
                        'saved'    => (bool) $formEntry->exists,
                        'notified' => (bool) $formEntry->notified_at,
                    ],
                ]);
            }

            return redirect()->back()->with('success', trans('forms-entries::messages.form_sent'));
        }

        return abort(404);
    }
}
