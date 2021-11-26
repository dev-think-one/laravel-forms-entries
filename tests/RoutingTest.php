<?php

namespace FormEntries\Tests;

use FormEntries\Forms\UniversalForm;
use FormEntries\Models\FormEntry;
use FormEntries\Notifications\FormEntryReceived;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;

class RoutingTest extends TestCase
{
    /** @test */
    public function send_form_json()
    {
        config()->set('forms-entries.defaults.notification_receivers.email', [
            'email@test.home' => 'Email User',
        ]);
        $this->assertEquals(0, FormEntry::count());
        $response = $this->postJson(config('forms-entries.routing.path'), [
            config('forms-entries.routing.form_name_parameter') => UniversalForm::class,
            'foo'                                               => 'bar',
        ]);
        $response->assertJsonStructure([
            'message',
            'data' => [
                'type',
                'saved',
                'notified',
            ],
        ]);
        $this->assertTrue($response->json('data.saved'));
        $this->assertTrue($response->json('data.notified'));

        Notification::assertSentTo(
            new AnonymousNotifiable,
            FormEntryReceived::class,
            function ($notification, $channels, $notifiable) {
                return array_key_exists('email@test.home', $notifiable->routes['mail']);
            }
        );
        $this->assertEquals('bar', FormEntry::first()->content->getAttribute('foo'));
    }

    /** @test */
    public function send_form()
    {
        config()->set('forms-entries.defaults.notification_receivers.email', [
            'email@test.home' => 'Email User',
        ]);
        $this->assertEquals(0, FormEntry::count());
        $response = $this->post(config('forms-entries.routing.path'), [
            config('forms-entries.routing.form_name_parameter') => UniversalForm::class,
            'foo'                                               => 'bar',
        ]);
        $response->assertRedirect();
        $response->assertSessionHas('success');

        Notification::assertSentTo(
            new AnonymousNotifiable,
            FormEntryReceived::class,
            function ($notification, $channels, $notifiable) {
                return array_key_exists('email@test.home', $notifiable->routes['mail']);
            }
        );
        $this->assertEquals('bar', FormEntry::first()->content->getAttribute('foo'));
    }

    /** @test */
    public function send_form_404()
    {
        config()->set('forms-entries.defaults.notification_receivers.email', [
            'email@test.home' => 'Email User',
        ]);
        $this->assertEquals(0, FormEntry::count());
        $response = $this->post(config('forms-entries.routing.path'), [
            'foo'                                               => 'bar',
        ]);

        $response->assertNotFound();
        $this->assertEquals(0, FormEntry::count());
        Notification::assertNothingSent();
    }
}
