<?php

namespace FormEntries\Helpers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class RequestTracer
{
    public static function getRequestMetaInfo(?Request $request = null): array
    {
        if (!$request) {
            $request = request();
        }

        $data = static::getRequestSimpleMetaInfo($request);


        if (
            ($user = $request->user(
                config('forms-entries.defaults.auth_guard', config('auth.defaults.guard'))
            ))
            && $user instanceof Model) {
            $data['creator'] = [
                'id'   => $user->getKey(),
                'type' => $user->getMorphClass(),
            ];
        }

        return $data;
    }

    public static function getRequestSimpleMetaInfo(?Request $request = null): array
    {
        if (!$request) {
            $request = request();
        }

        return [
            'ip'        => $request->ip(),
            'ips'       => $request->ips(),
            'userAgent' => $request->userAgent(),
        ];
    }
}
