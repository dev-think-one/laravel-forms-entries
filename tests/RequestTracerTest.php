<?php

namespace FormEntries\Tests;

use FormEntries\Helpers\RequestTracer;
use FormEntries\Tests\Fixtures\Models\User;

class RequestTracerTest extends TestCase
{

    /** @test */
    public function get_request_information_without_user()
    {
        $response = $this->get('testing/trace-request');

        $response->assertExactJson([
            'ip'        => '127.0.0.1',
            'ips'       => ['127.0.0.1'],
            'userAgent' => 'Symfony',
        ]);
    }

    /** @test */
    public function get_request_information_with_user()
    {
        User::factory()->count(20)->create();
        /** @var User $user */
        $user = User::factory()->create();
        User::factory()->count(20)->create();

        $response = $this->actingAs($user)
                         ->get('testing/trace-request');

        $response->assertExactJson([
            'ip'        => '127.0.0.1',
            'ips'       => ['127.0.0.1'],
            'userAgent' => 'Symfony',
            'creator'   => [
                'id'   => $user->getKey(),
                'type' => get_class($user),
            ],
        ]);
    }

    /** @test */
    public function get_default_request_for_simple_info()
    {
        $this->assertEquals('127.0.0.1', RequestTracer::getRequestSimpleMetaInfo()['ip']);
    }
}
