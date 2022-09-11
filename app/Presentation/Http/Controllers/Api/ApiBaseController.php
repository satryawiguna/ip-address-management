<?php

namespace App\Presentation\Http\Controllers\Api;

use App\Core\Application\Response\BasicResponse;
use App\Presentation\Http\Controllers\Controller;
use App\Presentation\Http\Controllers\RequestAuthor;
use Illuminate\Http\JsonResponse;

class ApiBaseController extends Controller
{
    use RequestAuthor;

    protected function getAllJsonResponse(BasicResponse $response): JsonResponse {
        return response()->json([
            "type" => $response->getType(),
            "code_status" => $response->getCodeStatus(),
            "messages" => $response->getMessageResponseAll()
        ], $response->getCodeStatus());
    }

    protected function getAllLatestJsonResponse(BasicResponse $response): JsonResponse {
        return response()->json([
            "type" => $response->getType(),
            "code_status" => $response->getCodeStatus(),
            "message" => $response->getMessageResponseAllLatest()
        ], $response->getCodeStatus());
    }

    protected function getSuccessJsonResponse(BasicResponse $response): JsonResponse {
        return response()->json([
            "type" => $response->getType(),
            "code_status" => $response->getCodeStatus(),
            "messages" => $response->getMessageResponseSuccess()
        ], $response->getCodeStatus());
    }

    protected function getSuccessLatestJsonResponse(BasicResponse $response): JsonResponse {
        return response()->json([
            "type" => $response->getType(),
            "code_status" => $response->getCodeStatus(),
            "message" => $response->getMessageResponseSuccessLatest()
        ], $response->getCodeStatus());
    }

    protected function getErrorJsonResponse(BasicResponse $response): JsonResponse {
        return response()->json([
            "type" => $response->getType(),
            "code_status" => $response->getCodeStatus(),
            "messages" => $response->getMessageResponseError()
        ], $response->getCodeStatus());
    }

    protected function getErrorLatestJsonResponse(BasicResponse $response): JsonResponse {
        return response()->json([
            "type" => $response->getType(),
            "code_status" => $response->getCodeStatus(),
            "message" => $response->getMessageResponseErrorLatest()
        ], $response->getCodeStatus());
    }

    protected function getInfoJsonResponse(BasicResponse $response): JsonResponse {
        return response()->json([
            "type" => $response->getType(),
            "code_status" => $response->getCodeStatus(),
            "messages" => $response->getMessageResponseInfo()
        ], $response->getCodeStatus());
    }

    protected function getInfoLatestJsonResponse(BasicResponse $response): JsonResponse {
        return response()->json([
            "type" => $response->getType(),
            "code_status" => $response->getCodeStatus(),
            "message" => $response->getMessageResponseInfoLatest()
        ], $response->getCodeStatus());
    }

    protected function getWarningJsonResponse(BasicResponse $response): JsonResponse {
        return response()->json([
            "type" => $response->getType(),
            "code_status" => $response->getCodeStatus(),
            "messages" => $response->getMessageResponseWarning()
        ], $response->getCodeStatus());
    }

    protected function getWarningLatestJsonResponse(BasicResponse $response): JsonResponse {
        return response()->json([
            "type" => $response->getType(),
            "code_status" => $response->getCodeStatus(),
            "message" => $response->getMessageResponseWarningLatest()
        ], $response->getCodeStatus());
    }

    protected function getObjectJsonResponse(BasicResponse $response): JsonResponse {
        return response()->json([
            "type" => $response->getType(),
            "code_status" => $response->getCodeStatus(),
            "data" => $response->dto
        ], $response->getCodeStatus());
    }

    protected function getListJsonResponse(BasicResponse $response): JsonResponse {
        return response()->json([
            "type" => $response->getType(),
            "code_status" => $response->getCodeStatus(),
            "datas" => $response->dtoList
        ], $response->getCodeStatus());
    }

    protected function getListSearchJsonResponse(BasicResponse $response): JsonResponse {
        return response()->json([
            "type" => $response->getType(),
            "code_status" => $response->getCodeStatus(),
            "total_count" => $response->totalCount,
            "datas" => $response->dtoListSearch
        ], $response->getCodeStatus());
    }

    protected function getListSearchPageJsonResponse(BasicResponse $response): JsonResponse {
        return response()->json([
            "type" => $response->getType(),
            "code_status" => $response->getCodeStatus(),
            "total_count" => $response->totalCount,
            "datas" => $response->dtoListSearchPage,
            "meta" => $response->meta
        ], $response->getCodeStatus());
    }
}
