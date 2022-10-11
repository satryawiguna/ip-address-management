<?php

namespace App\Presentation\Http\Controllers\Api;

use App\Core\Application\Response\BasicResponse;
use App\Core\Application\Response\GenericListResponse;
use App\Core\Application\Response\GenericListSearchPageResponse;
use App\Core\Application\Response\GenericListSearchResponse;
use App\Core\Application\Response\GenericObjectResponse;
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

    protected function getObjectJsonResponse(GenericObjectResponse $response): JsonResponse {
        return response()->json([
            "type" => $response->getType(),
            "code_status" => $response->getCodeStatus(),
            "data" => $response->getDto()
        ], $response->getCodeStatus());
    }

    protected function getListJsonResponse(GenericListResponse $response): JsonResponse {
        return response()->json([
            "type" => $response->getType(),
            "code_status" => $response->getCodeStatus(),
            "datas" => $response->getDtoList()
        ], $response->getCodeStatus());
    }

    protected function getListSearchJsonResponse(GenericListSearchResponse $response): JsonResponse {
        return response()->json([
            "type" => $response->getType(),
            "code_status" => $response->getCodeStatus(),
            "total_count" => $response->totalCount,
            "datas" => $response->getDtoListSearch()
        ], $response->getCodeStatus());
    }

    protected function getListSearchPageJsonResponse(GenericListSearchPageResponse $response): JsonResponse {
        return response()->json([
            "type" => $response->getType(),
            "code_status" => $response->getCodeStatus(),
            "total_count" => $response->totalCount,
            "meta" => $response->meta,
            "datas" => $response->getDtoListSearchPage()
        ], $response->getCodeStatus());
    }
}
