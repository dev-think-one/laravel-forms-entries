<?php

namespace FormEntries\Forms;

use FormEntries\Models\FormEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use JsonFieldCast\Json\AbstractMeta;

abstract class FormContent extends AbstractMeta
{
    use HasTypesMap;

    protected array $requestKeysToSave = ['*'];

    public static function getCastableClassByModel(FormEntry $model, ?array $data = null): string
    {
        $class = null;
        if ($model->content_type) {
            $class = static::getClassByType($model->content_type);
        }

        return $class ?: UniversalFormContent::class;
    }

    public function fillFromRequest(Request $request): static
    {
        $requestKeysToSave = $this->requestKeysToSave();
        if (!empty($requestKeysToSave) && $requestKeysToSave[0] == '*') {
            $this->data = $request->all();
        } else {
            $this->data = $request->only($requestKeysToSave);
        }

        return $this;
    }

    public function requestKeysToSave(): array
    {
        return $this->requestKeysToSave;
    }

    public function formName(): string
    {
        return Str::title(
            Str::snake(
                Str::beforeLast(
                    class_basename(static::class),
                    'FormContent'
                ),
                ' '
            )
        );
    }

    public function stringify(): string
    {
        $string = '';
        foreach ($this->data as $key => $value) {
            $string .= sprintf(
                $this->stringifyLineFormat(),
                $this->humanReadableKey((string) $key),
                $value ?: trans('forms-entries::messages.empty_value')
            );
        }

        return $string;
    }

    public function stringifyLineFormat(): string
    {
        return "%s: \n%s \n\n";
    }

    public function validateRequest(Request $request): static
    {
        // Example:
        // $request->validate([]);

        return $this;
    }

    protected function humanReadableKey(string $key): string
    {
        $formID = static::class;
        foreach (
            [
                "forms-entry-keys.{$formID}.{$key}",
                "forms-entry-keys.{$key}",
                "forms-entries::keys.{$key}",
            ] as $path
        ) {
            if (Lang::has($path)) {
                return (string) Lang::get($path);
            }
        }

        return Str::ucfirst(Str::snake(Str::camel($key), ' '));
    }
}
