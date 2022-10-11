<?php

namespace App\Service;

use App\Core\Application\Response\BasicResponse;
use App\Core\Application\Response\GenericListResponse;
use App\Core\Application\Response\GenericListSearchPageResponse;
use App\Core\Application\Response\GenericListSearchResponse;
use App\Core\Application\Response\GenericObjectResponse;
use App\Core\Domain\BaseEntity;
use App\Service\Contract\IBaseService;
use Illuminate\Support\Collection;


abstract class BaseService implements IBaseService
{
    public function setGenericListResponse(GenericListResponse $response,
                                           Collection $dtoList,
                                           string $type,
                                           int $codeStatus): GenericListResponse
    {
        $response->dtoList = $dtoList;
        $response->type = $type;
        $response->codeStatus = $codeStatus;

        return $response;
    }

    public function setGenericListSearchResponse(GenericListSearchResponse $response,
                                                 Collection $dtoListSearch,
                                                 int $totalCount,
                                                 string $type,
                                                 int $codeStatus): GenericListSearchResponse
    {
        $response->dtoListSearch = $dtoListSearch;
        $response->totalCount = $totalCount;
        $response->type = $type;
        $response->codeStatus = $codeStatus;

        return $response;
    }

    public function setGenericListSearchPageResponse(GenericListSearchPageResponse $response,
                                                     Collection $dtoListSearchPage,
                                                     int $totalCount,
                                                     array $meta,
                                                     string $type,
                                                     int $codeStatus): GenericListSearchPageResponse
    {
        $response->dtoListSearchPage = $dtoListSearchPage;
        $response->totalCount = $totalCount;
        $response->meta = $meta;
        $response->type = $type;
        $response->codeStatus = $codeStatus;

        return $response;
    }

    public function setGenericObjectResponse(GenericObjectResponse $response,
                                             BaseEntity|array|null $dto,
                                             string $type,
                                             int $codeStatus): GenericObjectResponse
    {
        $response->dto = $dto;
        $response->type = $type;
        $response->codeStatus = $codeStatus;

        return $response;
    }

    public function setMessageResponse(BasicResponse $response,
                                       string $type,
                                       int $codeStatus,
                                       string|array $message = null)
    {
        $response->type = $type;
        $response->codeStatus = $codeStatus;

        if (is_array($message)) {
            foreach ($message as $key => $value) {
                foreach ($value as $item) {
                    $method = "add" . ucfirst($type) . "MessageResponse";
                    $response->$method($item);
                }
            }
        } else {
            $method = "add" . ucfirst($type) . "MessageResponse";

            $response->$method($message);
        }

        return $response;
    }


}
