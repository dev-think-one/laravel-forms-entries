<?php

namespace FormEntries\Forms;

use FormEntries\Contracts\FormManipulationContract;
use FormEntries\Models\FormEntry;
use Illuminate\Http\Request;

class PendingForm implements FormManipulationContract
{
    protected AbstractForm $form;

    protected bool $isNeedNotification;

    protected bool $isNeedSaving;

    /**
     * PendingForm constructor.
     *
     * @param $form
     */
    public function __construct(AbstractForm $form)
    {
        $this->form = $form;

        $this->isNeedNotification = config('forms-entries.defaults.is_need_notification', true);
        $this->isNeedSaving       = config('forms-entries.defaults.is_need_saving', true);
    }


    /**
     * @inheritDoc
     */
    public function withSaving($isNeedSaving = true): self
    {
        $this->isNeedSaving = $isNeedSaving;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function withOutSaving(): self
    {
        $this->isNeedSaving = false;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function withNotification($isNeedNotification = true): self
    {
        $this->isNeedNotification = $isNeedNotification;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function withoutNotification(): self
    {
        $this->isNeedNotification = false;

        return $this;
    }

    /**
     * @param Request $request
     *
     * @return FormEntry
     */
    public function process(Request $request): FormEntry
    {
        return $this->form->process($request, $this);
    }

    /**
     * Check is current form data must be stored in the storage.
     *
     * @return bool
     */
    public function isNeedSaving(): bool
    {
        return $this->isNeedSaving;
    }

    /**
     * Check is current form data must be sent.
     *
     * @return bool
     */
    public function isNeedNotification(): bool
    {
        return $this->isNeedNotification;
    }
}
