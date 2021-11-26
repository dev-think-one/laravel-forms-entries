<?php

namespace FormEntries\Tests;

use FormEntries\Forms\PendingForm;
use FormEntries\Forms\UniversalForm;
use FormEntries\Forms\UniversalFormContent;
use FormEntries\Models\FormEntry;
use FormEntries\Notifications\FormEntryReceived;
use FormEntries\Tests\Fixtures\Models\User;
use Illuminate\Http\Request;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Mockery\MockInterface;

class FormTest extends TestCase
{

    /** @test */
    public function notifications_toggle()
    {
        $form = new UniversalForm();

        $pendingForm = $form->enableNotifications();
        $this->assertInstanceOf(PendingForm::class, $pendingForm);
        $this->assertTrue($pendingForm->isShouldNotify());

        $pendingForm = $form->disableNotifications();
        $this->assertInstanceOf(PendingForm::class, $pendingForm);
        $this->assertFalse($pendingForm->isShouldNotify());
    }

    /** @test */
    public function store_toggle()
    {
        $form = new UniversalForm();

        $pendingForm = $form->enableStoringData();
        $this->assertInstanceOf(PendingForm::class, $pendingForm);
        $this->assertTrue($pendingForm->isShouldStore());

        $pendingForm = $form->disableStoringData();
        $this->assertInstanceOf(PendingForm::class, $pendingForm);
        $this->assertFalse($pendingForm->isShouldStore());
    }

    /** @test */
    public function form_out_of_the_box()
    {
        /** @var Request $request */
        $request = $this->mock(Request::class, function (MockInterface $mock) {
            $mock->shouldReceive('all')->andReturn([]);
            $mock->shouldReceive('user')->andReturn(null)->twice();
            $mock->shouldReceive('ip')->andReturn('127.3.0.1');
            $mock->shouldReceive('ips')->andReturn(['127.3.0.1']);
            $mock->shouldReceive('userAgent')->andReturn('form_out_of_the_box');
        });

        $formEntry = UniversalForm::make()
                                  ->enableStoringData()
                                  ->enableNotifications()
                                  ->process($request);

        $this->assertInstanceOf(FormEntry::class, $formEntry);

        $this->assertNull($formEntry->notified_at);
        $this->assertEquals('127.3.0.1', $formEntry->meta->getAttribute('request_data.ip'));
        $this->assertEquals(__FUNCTION__, $formEntry->meta->getAttribute('request_data.userAgent'));
        $this->assertEquals(UniversalFormContent::class, $formEntry->content_type);
        $this->assertInstanceOf(UniversalFormContent::class, $formEntry->content);
        $this->assertTrue($formEntry->content->isEmpty());

        Notification::assertNothingSent();
    }

    /** @test */
    public function form_out_of_the_box_with_notification_to_raw_emails()
    {
        config()->set('forms-entries.defaults.notification_receivers.email', [
            'email1@test.home' => 'Email1 User',
            'email2@test.home' => 'Email2 User',
        ]);

        /** @var Request $request */
        $request = $this->mock(Request::class, function (MockInterface $mock) {
            $mock->shouldReceive('all')->andReturn([]);
            $mock->shouldReceive('user')->andReturn(null)->twice();
            $mock->shouldReceive('ip')->andReturn('127.3.0.1');
            $mock->shouldReceive('ips')->andReturn(['127.3.0.1']);
            $mock->shouldReceive('userAgent')->andReturn('form_out_of_the_box_with_notification_to_raw_emails');
        });

        $formEntry = UniversalForm::make()
                                  ->enableStoringData()
                                  ->enableNotifications()
                                  ->process($request);

        $this->assertInstanceOf(FormEntry::class, $formEntry);

        $this->assertNotNull($formEntry->notified_at);
        $this->assertEquals('127.3.0.1', $formEntry->meta->getAttribute('request_data.ip'));
        $this->assertEquals(__FUNCTION__, $formEntry->meta->getAttribute('request_data.userAgent'));
        $this->assertEquals(UniversalFormContent::class, $formEntry->content_type);
        $this->assertInstanceOf(UniversalFormContent::class, $formEntry->content);
        $this->assertTrue($formEntry->content->isEmpty());

        Notification::assertSentTo(
            new AnonymousNotifiable,
            FormEntryReceived::class,
            function ($notification, $channels, $notifiable) {
                return array_key_exists('email1@test.home', $notifiable->routes['mail'])
                       && array_key_exists('email2@test.home', $notifiable->routes['mail']);
            }
        );
    }

    /** @test */
    public function form_out_of_the_box_with_notification_to_users()
    {
        User::factory()->count(10)->create();
        /** @var User $user */
        $user  = User::factory()->create();
        $user  = User::find($user->getKey());
        $user2 = User::factory()->create();
        $user2 = User::find($user2->getKey());
        User::factory()->count(10)->create();

        /** @var Request $request */
        $request = $this->mock(Request::class, function (MockInterface $mock) {
            $mock->shouldReceive('all')->andReturn([]);
            $mock->shouldReceive('user')->andReturn(null)->twice();
            $mock->shouldReceive('ip')->andReturn('127.3.0.1');
            $mock->shouldReceive('ips')->andReturn(['127.3.0.1']);
            $mock->shouldReceive('userAgent')->andReturn('form_out_of_the_box_with_notification_to_users');
        });

        $pendingForm = UniversalForm::make()
                                    ->enableStoringData()
                                    ->enableNotifications();
        $pendingForm->setNotifiableUsers([$user2, $user]);
        $formEntry = $pendingForm->process($request);

        $this->assertInstanceOf(FormEntry::class, $formEntry);

        $this->assertNotNull($formEntry->notified_at);
        $this->assertEquals('127.3.0.1', $formEntry->meta->getAttribute('request_data.ip'));
        $this->assertEquals(__FUNCTION__, $formEntry->meta->getAttribute('request_data.userAgent'));
        $this->assertEquals(UniversalFormContent::class, $formEntry->content_type);
        $this->assertInstanceOf(UniversalFormContent::class, $formEntry->content);
        $this->assertTrue($formEntry->content->isEmpty());

        Notification::assertSentTo(
            [$user, $user2],
            FormEntryReceived::class
        );
    }

    /** @test */
    public function form_out_of_the_box_with_notification_to_users_as_callback()
    {
        User::factory()->count(10)->create();
        /** @var User $user */
        $user  = User::factory()->create();
        $user  = User::find($user->getKey());
        $user2 = User::factory()->create();
        $user2 = User::find($user2->getKey());
        User::factory()->count(10)->create();

        /** @var Request $request */
        $request = $this->mock(Request::class, function (MockInterface $mock) {
            $mock->shouldReceive('all')->andReturn([]);
            $mock->shouldReceive('user')->andReturn(null)->twice();
            $mock->shouldReceive('ip')->andReturn('127.3.0.1');
            $mock->shouldReceive('ips')->andReturn(['127.3.0.1']);
            $mock->shouldReceive('userAgent')->andReturn('form_out_of_the_box_with_notification_to_users');
        });

        $formEntry = UniversalForm::make()
                                  ->disableStoringData()
                                  ->enableNotifications()
                                  ->setNotifiableUsers(fn () => [$user2, $user])
                                  ->process($request);

        $this->assertNull($formEntry->notified_at);
        $this->assertFalse($formEntry->exists);
        Notification::assertSentTo(
            [$user],
            function (FormEntryReceived $notification, $channels) use ($formEntry, $user) {
                /** @var MailMessage $mailMessage */
                $mailMessage = $notification->toMail($user);
                $this->assertTrue(Str::startsWith($mailMessage->subject, 'Universal '));

                $notification->subject = 'My foo bar subject';
                $this->assertEquals('My foo bar subject', $notification->toMail($user)->subject);

                return $notification->content->isEmpty();
            }
        );
    }

    /** @test */
    public function form_out_of_the_box_with_auth_user()
    {
        User::factory()->count(10)->create();
        /** @var User $user */
        $user = User::factory()->create();
        User::factory()->count(10)->create();

        /** @var Request $request */
        $request = $this->mock(Request::class, function (MockInterface $mock) use ($user) {
            $mock->shouldReceive('all')->andReturn([]);
            $mock->shouldReceive('user')->andReturn($user)->twice();
            $mock->shouldReceive('ip')->andReturn('127.3.0.1');
            $mock->shouldReceive('ips')->andReturn(['127.3.0.1']);
            $mock->shouldReceive('userAgent')->andReturn('form_out_of_the_box_with_auth_user');
        });

        $formEntry = UniversalForm::make()
                                  ->enableStoringData()
                                  ->disableNotifications()
                                  ->process($request);

        $this->assertInstanceOf(FormEntry::class, $formEntry);

        $this->assertNull($formEntry->notified_at);
        $this->assertEquals($user->getKey(), $formEntry->sender_id);
        $this->assertEquals($user->getMorphClass(), $formEntry->sender_type);
        $this->assertEquals('127.3.0.1', $formEntry->meta->getAttribute('request_data.ip'));
        $this->assertEquals(__FUNCTION__, $formEntry->meta->getAttribute('request_data.userAgent'));
        $this->assertEquals($user->getKey(), $formEntry->meta->getAttribute('request_data.creator.id'));
        $this->assertEquals($user->getMorphClass(), $formEntry->meta->getAttribute('request_data.creator.type'));
        $this->assertEquals(UniversalFormContent::class, $formEntry->content_type);
        $this->assertInstanceOf(UniversalFormContent::class, $formEntry->content);
        $this->assertTrue($formEntry->content->isEmpty());
    }
}
