<?php

namespace FormEntries\Contracts;

use FormEntries\Forms\PendingForm;

interface FormManipulationContract
{
    /**
     * Form data must be stored in the storage.
     *
     * @param bool $shouldStore
     *
     * @return PendingForm
     */
    public function enableStoringData(bool $shouldStore = true): PendingForm;

    /**
     * Form data does not need to be stored in storage.
     *
     * @return PendingForm
     */
    public function disableStoringData(): PendingForm;

    /**
     * Form data must be sent via notifications.
     *
     * @param bool $shouldNotify
     *
     * @return PendingForm
     */
    public function enableNotifications(bool $shouldNotify = true): PendingForm;

    /**
     * Form data does not need to be sent via notifications.
     *
     * @return PendingForm
     */
    public function disableNotifications(): PendingForm;
}
