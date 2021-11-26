<?php

namespace FormEntries\Tests\Fixtures\Http\Controllers;

use FormEntries\Helpers\RequestTracer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class TraceRequestController
{
    public function __invoke(Request $request)
    {
        return Response::json(RequestTracer::getRequestMetaInfo());
    }
}
