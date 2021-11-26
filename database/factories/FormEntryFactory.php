<?php

namespace FormEntries\Database\Factories;

use Carbon\Carbon;
use FormEntries\Forms\FormContent;
use FormEntries\Models\FormEntry;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

class FormEntryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = FormEntry::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'type'         => 'default',
            'content_type' => null,
            'content'      => null,
            'notified_at'  => null,
            'sender_id'    => null,
            'sender_type'  => null,
            'meta'         => null,
        ];
    }

    public function type(?string $type): static
    {
        return $this->state([
            'type' => $type,
        ]);
    }

    public function notified(?\DateTimeInterface $dateTime): static
    {
        return $this->state([
            'notified_at' => $dateTime ?? Carbon::now(),
        ]);
    }

    public function notNotified(): static
    {
        return $this->state([
            'notified_at' => null,
        ]);
    }

    public function usingContent(FormContent $formContent): static
    {
        return $this->state([
            'content_type' => $formContent->getType(),
            'content'      => $formContent->toArray(),
        ]);
    }
}
