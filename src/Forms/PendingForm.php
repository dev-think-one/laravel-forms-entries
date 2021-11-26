<?php

namespace FormEntries\Forms;

use FormEntries\Contracts\FormManipulationContract;
use FormEntries\Models\FormEntry;
use Illuminate\Http\Request;

class PendingForm implements FormManipulationContract
{
    protected Form $form;

    protected bool $shouldNotify;

    protected bool $shouldStore;

    /**
     * PendingForm constructor.
     *
     * @param $form
     */
    public function __construct(Form $form)
    {
        $this->form = $form;

        $this->shouldStore  = (bool) config('forms-entries.defaults.should_store');
        $this->shouldNotify = (bool) config('forms-entries.defaults.should_notify');
    }

    /**
     * @inheritDoc
     */
    public function enableStoringData(bool $shouldStore = true): static
    {
        $this->shouldStore = $shouldStore;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function disableStoringData(): static
    {
        $this->shouldStore = false;

        return $this;
    }

    public function isShouldStore(): bool
    {
        return $this->shouldStore;
    }

    /**
     * @inheritDoc
     */
    public function enableNotifications(bool $shouldNotify = true): static
    {
        $this->shouldNotify = $shouldNotify;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function disableNotifications(): static
    {
        $this->shouldNotify = false;

        return $this;
    }

    public function isShouldNotify(): bool
    {
        return $this->shouldNotify;
    }

    public function setNotifiableUsers(array|\Closure $notifiableUsers): static
    {
        $this->form->setNotifiableUsers($notifiableUsers);

        return $this;
    }

    public function process(Request $request): FormEntry
    {
        return $this->form->process($request, $this);
    }
}
