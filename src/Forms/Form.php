<?php

namespace FormEntries\Forms;

use Carbon\Carbon;
use FormEntries\Contracts\FormManipulationContract;
use FormEntries\Helpers\RequestTracer;
use FormEntries\Models\FormEntry;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notification;

abstract class Form implements FormManipulationContract
{
    use HasTypesMap;

    public array|\Closure $notifiableUsers  = [];
    public array|\Closure $notifiableEmails = [];

    public static function make(...$arguments): PendingForm
    {
        return new PendingForm(new static(...$arguments));
    }

    public function getFormNotificationClass(): string
    {
        return $this->formNotificationClass ?? config('forms-entries.defaults.notification_class');
    }

    public function getFormContentClass(): string
    {
        return $this->formContentClass ?? config('forms-entries.defaults.content_class');
    }

    protected function newFormContent(Model $model, Request $request): FormContent
    {
        return (new ($this->getFormContentClass())($model))->fillFromRequest($request);
    }

    public function getFormEntryClass(): string
    {
        return $this->formEntryClass ?? config('forms-entries.defaults.storage_model_class');
    }

    protected function newFormEntry(): FormEntry
    {
        return new ($this->getFormEntryClass())();
    }

    public function setNotifiableUsers(array|\Closure $notifiableUsers): static
    {
        $this->notifiableUsers = $notifiableUsers;

        return $this;
    }

    protected function notifiableUsers(FormEntry $model): array
    {
        if (is_callable($this->notifiableUsers)) {
            return call_user_func($this->notifiableUsers, $model);
        }

        return $this->notifiableUsers;
    }

    public function setNotifiableEmails(array|\Closure $notifiableEmails): static
    {
        $this->notifiableEmails = $notifiableEmails;

        return $this;
    }

    protected function notifiableEmails(FormEntry $model): array
    {
        if (is_callable($this->notifiableEmails)) {
            return call_user_func($this->notifiableEmails, $model);
        }

        return $this->notifiableEmails;
    }

    public function notify(FormEntry $model): bool
    {
        $notificationSent = false;
        /** @var Notification $notification */
        $notification = new ($this->getFormNotificationClass())($model->content);

        foreach ($this->notifiableUsers($model) as $user) {
            $user->notify($notification);
            $notificationSent = true;
        }

        $emailReceivers = array_merge(
            config('forms-entries.defaults.notification_receivers.email', []),
            $this->notifiableEmails($model)
        );
        if (is_array($emailReceivers) && !empty($emailReceivers)) {
            \Illuminate\Support\Facades\Notification::route('mail', $emailReceivers)
                                                    ->notify($notification);
            $notificationSent = true;
        }

        return $notificationSent;
    }

    public function process(Request $request, PendingForm $pendingForm): FormEntry
    {
        $model       = $this->newFormEntry();
        $formContent = $this->newFormContent($model, $request)
                            ->validateRequest($request);

        /** @var Model $user */
        if ($user = $request->user(config('forms-entries.defaults.auth_guard', config('auth.defaults.guard')))) {
            $model->fill([
                'sender_id'   => $user->getKey(),
                'sender_type' => $user->getMorphClass(),
            ]);
        }

        $model->fill([
            'type'         => $this::getType(),
            'content_type' => $formContent::getType(),
            'content'      => $formContent->toArray(),
        ]);
        $model->meta->setData(['request_data' => RequestTracer::getRequestMetaInfo($request),]);

        if ($pendingForm->isShouldStore()) {
            $model->save();
        }

        if ($pendingForm->isShouldNotify()
            && $this->notify($model)
            && $model->exists
        ) {
            $model->fill([
                'notified_at' => Carbon::now(),
            ])->save();
        }

        return $model;
    }

    /**
     * @inheritDoc
     */
    public function enableStoringData(bool $shouldStore = true): PendingForm
    {
        return (new PendingForm($this))->enableStoringData($shouldStore);
    }

    /**
     * @inheritDoc
     */
    public function disableStoringData(): PendingForm
    {
        return (new PendingForm($this))->disableStoringData();
    }

    /**
     * @inheritDoc
     */
    public function enableNotifications(bool $shouldNotify = true): PendingForm
    {
        return (new PendingForm($this))->enableNotifications($shouldNotify);
    }

    /**
     * @inheritDoc
     */
    public function disableNotifications(): PendingForm
    {
        return (new PendingForm($this))->disableNotifications();
    }
}
