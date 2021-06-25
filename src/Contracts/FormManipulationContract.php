<?php

namespace FormEntries\Contracts;

interface FormManipulationContract
{

    /**
     * Form data must be stored in the storage.
     *
     * @param bool $isNeedSaving
     *
     * @return self
     */
    public function withSaving($isNeedSaving = true): self;

    /**
     * Form data does not need to be stored in storage.
     *
     * @return self
     */
    public function withOutSaving(): self;


    /**
     * Form data must be sent via notifications.
     *
     * @param bool $isNeedNotification
     *
     * @return self
     */
    public function withNotification($isNeedNotification = true): self;

    /**
     * Form data does not need to be sent via notifications.
     *
     * @return self
     */
    public function withoutNotification(): self;
}
