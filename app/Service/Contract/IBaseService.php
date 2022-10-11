<?php

namespace App\Service\Contract;

use App\Core\Application\Response\BasicResponse;
use App\Core\Application\Response\GenericListResponse;
use App\Core\Application\Response\GenericListSearchPageResponse;
use App\Core\Application\Response\GenericListSearchResponse;
use App\Core\Application\Response\GenericObjectResponse;
use App\Core\Domain\BaseEntity;
use Illuminate\Support\Collection;

interface IBaseService
{
    public function setGenericListResponse(GenericListResponse $response,
                                           Collection $dtoList,
                                           string $type,
                                           int $codeStatus): GenericListResponse;

    public function setGenericListSearchResponse(GenericListSearchResponse $response,
                                                 Collection $dtoListSearch,
                                                 int $totalCount,
                                                 string $type,
                                                 int $codeStatus): GenericListSearchResponse;

    public function setGenericListSearchPageResponse(GenericListSearchPageResponse $response,
                                                 Collection $dtoListSearchPage,
                                                 int $totalCount,
                                                 array $meta,
                                                 string $type,
                                                 int $codeStatus): GenericListSearchPageResponse;

    public function setGenericObjectResponse(GenericObjectResponse $response,
                                             BaseEntity|array|null $dto,
                                             string $type,
                                             int $codeStatus): GenericObjectResponse;

    public function setMessageResponse(BasicResponse $response,
                                     string $type,
                                     int $codeStatus,
                                     string|array $message = null);
}
