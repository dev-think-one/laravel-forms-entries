<?php

namespace FormEntries\Notifications;

use FormEntries\Forms\FormContent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class FormEntryReceived extends Notification implements ShouldQueue
{
    use Queueable;

    public FormContent $content;

    public ?string $subject = null;

    public function __construct(FormContent $content)
    {
        $this->content = $content;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function via($notifiable = null)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        /** @var MailMessage $message */
        $message = (new MailMessage)
            ->subject($this->getSubject())
            ->greeting(trans('forms-entries::notification.form_content_greeting'))
            ->line(trans('forms-entries::notification.form_content_header'));
        foreach (explode("\n", $this->content->stringify()) as $line) {
            $message->line($line);
        }

        return $message;
    }

    protected function getSubject(): string
    {
        if ($this->subject) {
            return $this->subject;
        }
        $formName = Str::title(Str::snake(Str::beforeLast(class_basename(get_class($this->content)), 'FormContent'), ' '));

        return trans('forms-entries::notification.form_subject', ['name' => $formName]);
    }
}
