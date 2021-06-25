<?php

namespace FormEntries\Forms;

use Carbon\Carbon;
use FormEntries\Contracts\FormManipulationContract;
use FormEntries\Helpers\RequestTracer;
use FormEntries\Models\FormEntry;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

abstract class AbstractForm implements FormManipulationContract
{
    public static function make(...$arguments): PendingForm
    {
        return new PendingForm(new static(...$arguments));
    }

    /**
     * @return string
     */
    public function getContentClass(): string
    {
        return config('forms-entries.defaults.content_class');
    }

    /**
     * @return string
     */
    public function getFormEntryClass(): string
    {
        return config('forms-entries.defaults.storage_model_class');
    }

    protected function newFormEntry(): FormEntry
    {
        $class = $this->getFormEntryClass();

        return new $class();
    }

    /**
     * Send notifications
     *
     * @param FormEntry $model
     *
     * @return bool
     */
    public function notify(FormEntry $model): bool
    {
        return false;
    }

    /**
     * Validate request if need
     *
     * @param Request $request
     *
     * @return $this
     */
    public function validate(Request $request): self
    {

        // $request->validate([]);

        return $this;
    }

    public function process(Request $request, PendingForm $pendingForm): FormEntry
    {
        $this->validate($request);

        $model = $this->newFormEntry();

        $model->class_name = $this->getContentClass();

        if (Auth::check()) {
            /** @var Model $user */
            $user               = Auth::user();
            $model->sender_type = $user->getMorphClass();
            $model->sender_id   = $user->getKey();
        }


        $model->content = (new $model->class_name($model))->fromRequest($request);

        $model->meta->setData([ 'request_data' => RequestTracer::getRequestMetaInfo(), ]);

        if ($pendingForm->isNeedSaving()) {
            $model->save();
        }

        if ($pendingForm->isNeedNotification() && $this->notify($model)) {
            if ($model->exists) {
                $model->is_notified = Carbon::now();
                $model->save();
            }
        }



        return $model;
    }


    public function withSaving($isNeedSaving = true): FormManipulationContract
    {
        return ( new PendingForm($this) )->withSaving($isNeedSaving);
    }

    public function withOutSaving(): FormManipulationContract
    {
        return ( new PendingForm($this) )->withOutSaving();
    }

    public function withNotification($isNeedNotification = true): FormManipulationContract
    {
        return ( new PendingForm($this) )->withNotification($isNeedNotification);
    }

    public function withoutNotification(): FormManipulationContract
    {
        return ( new PendingForm($this) )->withoutNotification();
    }
}
