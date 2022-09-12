<?php

namespace App\Service;

use App\Application\Log\LogActivityBuilder;
use App\Application\Request\CreateIpAddressDataRequest;
use App\Application\Request\UpdateIpAddressDataRequest;
use App\Core\Application\LogLevel;
use App\Core\Application\Request\SearchPageRequest;
use App\Core\Application\Request\SearchRequest;
use App\Core\Application\Response\GenericListResponse;
use App\Core\Application\Response\GenericListSearchPageResponse;
use App\Core\Application\Response\GenericListSearchResponse;
use App\Core\Application\Response\GenericObjectResponse;
use App\Core\Application\Response\HttpResponseType;
use App\Repository\Contract\IAuditLogRepository;
use App\Repository\Contract\IIpAddressRepository;
use App\Service\Contract\IManageService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ManageService implements IManageService
{
    public IIpAddressRepository $ipAddressRepository;
    public IAuditLogRepository $auditLogRepository;


    public function __construct(IIpAddressRepository $ipAddressRepository,
                                IAuditLogRepository $auditLogRepository)
    {
        $this->ipAddressRepository = $ipAddressRepository;
        $this->auditLogRepository = $auditLogRepository;
    }


    public function getIpAddressAll(): GenericListResponse
    {
        $response = new GenericListResponse();

        try {
            $ipAddresses = $this->ipAddressRepository->all();

            $response->dtoList = $ipAddresses;
            $response->setType("SUCCESS");
            $response->setCodeStatus(HttpResponseType::SUCCESS->value);

        } catch (Exception $ex) {
            $response->addErrorMessageResponse($ex->getMessage());
            $response->setType("ERROR");
            $response->setCodeStatus(HttpResponseType::INTERNAL_SERVER_ERROR->value);

            Log::info($ex->getMessage());
        }

        return $response;
    }

    public function getIpAddressById(int $id): GenericObjectResponse
    {
        $response = new GenericObjectResponse();

        try {
            $ipAddress = $this->ipAddressRepository->findById($id);

            $response->dto = $ipAddress;
            $response->setType("SUCCESS");
            $response->setCodeStatus(HttpResponseType::SUCCESS->value);

        } catch (Exception $ex) {
            $response->addErrorMessageResponse($ex->getMessage());
            $response->setType("ERROR");
            $response->setCodeStatus(HttpResponseType::INTERNAL_SERVER_ERROR->value);

            Log::info($ex->getMessage());
        }

        return $response;
    }

    public function getIpAddressSearch(SearchRequest $searchRequest): GenericListSearchResponse
    {
        $response = new GenericListSearchResponse();

        try {
            $ipAddresses = $this->ipAddressRepository->allSearch($searchRequest->getSearch());

            $response->dtoListSearch = $ipAddresses;
            $response->totalCount = $ipAddresses->count();
            $response->setType("SUCCESS");
            $response->setCodeStatus(HttpResponseType::SUCCESS->value);

        } catch (Exception $ex) {
            $response->addErrorMessageResponse($ex->getMessage());
            $response->setType("ERROR");
            $response->setCodeStatus(HttpResponseType::INTERNAL_SERVER_ERROR->value);

            Log::info($ex->getMessage());
        }

        return $response;
    }

    public function getIpAddressSearchPage(SearchPageRequest $searchPageRequest): GenericListSearchPageResponse
    {
        $response = new GenericListSearchPageResponse();

        try {
            $ipAddresses = $this->ipAddressRepository->allSearchPage($searchPageRequest->getSearch(),
                $searchPageRequest->getPerPage(),
                $searchPageRequest->getPage());

            $response->dtoListSearchPage = $ipAddresses->getCollection();
            $response->totalCount = $ipAddresses->count();
            $response->meta = [
                "perPage" => $ipAddresses->perPage(),
                "currentPage" => $ipAddresses->currentPage()
            ];
            $response->setType("SUCCESS");
            $response->setCodeStatus(HttpResponseType::SUCCESS->value);

        } catch (Exception $ex) {
            $response->addErrorMessageResponse($ex->getMessage());
            $response->setType("ERROR");
            $response->setCodeStatus(HttpResponseType::INTERNAL_SERVER_ERROR->value);

            Log::info($ex->getMessage());
        }

        return $response;
    }

    public function storeIpAddress(CreateIpAddressDataRequest $request): GenericObjectResponse
    {
        $response = new GenericObjectResponse();

        DB::beginTransaction();

        try {
            $brokenRules = $request->getBrokenRules([
                'ipv4' => 'required|string|unique:ip_addresses|ip',
                'label' => 'required|string'
            ]);

            if ($brokenRules->fails()) {
                foreach ($brokenRules->errors()->getMessages() as $key => $value) {
                    foreach ($value as $message) {
                        $response->addErrorMessageResponse($message);
                    }
                }

                $response->setType("ERROR");
                $response->setCodeStatus(HttpResponseType::BAD_REQUEST->value);

                Log::info("Invalid field validation", $response->getMessageResponseError());

                return $response;
            }

            $user = Auth::user();

            $ipAddress = $this->ipAddressRepository->save($request);

            $response->dto = $ipAddress;
            $response->addSuccessMessageResponse('Ip Address store succeed');
            $response->setType("SUCCESS");
            $response->setCodeStatus(HttpResponseType::SUCCESS->value);

            Log::info("Ip Address store succeed");

            $logActivity = (new LogActivityBuilder())
                ->setAuditLogableId($ipAddress->id)
                ->setAuditLogableType("Domain/IpAddress")
                ->setLevel(LogLevel::INFO->value)
                ->setLoggedAt(Carbon::now()->toDateTimeString())
                ->setMessage("User $user->id: Store $ipAddress->ipv4 | $request->label succeed")
                ->setContext(["ipv4" => $ipAddress->ipv4]);

            $this->auditLogRepository->writeLogActivity($logActivity->build());
        } catch (Exception $ex) {
            DB::rollBack();

            $response->addErrorMessageResponse($ex->getMessage());
            $response->setType("ERROR");
            $response->setCodeStatus(HttpResponseType::INTERNAL_SERVER_ERROR->value);

            Log::info($ex->getMessage());
        }

        DB::commit();

        return $response;
    }

    public function updateIpAddress(UpdateIpAddressDataRequest $request): GenericObjectResponse
    {
        $response = new GenericObjectResponse();

        DB::beginTransaction();

        try {
            $ipAddress = $this->ipAddressRepository->update($request);

            if (!$ipAddress) {
                $response->addErrorMessageResponse('Ip Address not found');
                $response->setType("ERROR");
                $response->setCodeStatus(HttpResponseType::NOT_FOUND->value);
            }



        } catch (Exception $ex) {
            DB::rollBack();

            $response->addErrorMessageResponse($ex->getMessage());
            $response->setType("ERROR");
            $response->setCodeStatus(HttpResponseType::INTERNAL_SERVER_ERROR->value);

            Log::info($ex->getMessage());
        }

        DB::commit();

        return $response;
    }

    public function destroyIpAddress(int $id): int
    {
        // TODO: Implement destroyIpAddress() method.
    }


}
