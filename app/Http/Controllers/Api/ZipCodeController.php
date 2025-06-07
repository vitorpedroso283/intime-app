<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ZipCode\ZipCodeLookupRequest;
use App\Http\Resources\ZipCodeResource;
use App\Services\ZipCodeService;
use App\Traits\HandlesApiExceptions;
use Illuminate\Http\JsonResponse;

class ZipCodeController extends Controller
{
    use HandlesApiExceptions;

    protected ZipCodeService $zipCodeService;

    public function __construct(ZipCodeService $zipCodeService)
    {
        $this->zipCodeService = $zipCodeService;
    }

    public function lookup(ZipCodeLookupRequest $request): JsonResponse
    {
        return $this->handleApi(function () use ($request) {
            $cep = $request->validated('cep');

            $data = $this->zipCodeService->lookup($cep);

            if (!$data) {
                return response()->json(['message' => 'ZIP code not found.'], 404);
            }

            return new ZipCodeResource($data)->response();
        });
    }
}
