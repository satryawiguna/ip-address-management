<?php

namespace App\Service;

use App\Application\Log\LogActivityBuilder;
use App\Application\Request\CreateIpAddressDataRequest;
use App\Application\Request\UpdateIpAddressDataRequest;
use App\Core\Application\LogLevel;
use App\Core\Application\Request\SearchPageRequest;
use App\Core\Application\Request\SearchRequest;
use App\Core\Application\Response\BasicResponse;
use App\Core\Application\Response\GenericListResponse;
use App\Core\Application\Response\GenericListSearchPageResponse;
use App\Core\Application\Response\GenericListSearchResponse;
use App\Core\Application\Response\GenericObjectResponse;
use App\Core\Application\Response\HttpResponseType;
use App\Repository\Contract\IAuditLogRepository;
use App\Repository\Contract\IIpAddressRepository;
use App\Repository\Contract\ILabelRepository;
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

    public ILabelRepository $labelRepository;


    public function __construct(IIpAddressRepository $ipAddressRepository,
                                IAuditLogRepository $auditLogRepository,
                                ILabelRepository $labelRepository)
    {
        $this->ipAddressRepository = $ipAddressRepository;
        $this->auditLogRepository = $auditLogRepository;
        $this->labelRepository = $labelRepository;
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

            Log::error($ex->getMessage());
        }

        return $response;
    }

    public function getIpAddressById(int $id): GenericObjectResponse
    {
        $response = new GenericObjectResponse();

        try {
            $ipAddress = $this->ipAddressRepository->findById($id);

            if (!$ipAddress) {
                $response->addErrorMessageResponse("Ip Address not found");
                $response->setType("ERROR");
                $response->setCodeStatus(HttpResponseType::BAD_REQUEST->value);

                Log::info("Ip Address not found", $response->getMessageResponseError());

                return $response;
            }

            $response->dto = $ipAddress;
            $response->setType("SUCCESS");
            $response->setCodeStatus(HttpResponseType::SUCCESS->value);

        } catch (Exception $ex) {
            $response->addErrorMessageResponse($ex->getMessage());
            $response->setType("ERROR");
            $response->setCodeStatus(HttpResponseType::INTERNAL_SERVER_ERROR->value);

            Log::error($ex->getMessage());
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

            Log::error($ex->getMessage());
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

            Log::error($ex->getMessage());
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
                'label' => 'required'
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

            foreach ($request->getLabel()->toArray() as $label) {
                $rule = ($label->id > 0) ? ['title' => 'required|string'] : ['title' => 'required|string|unique:labels'];
                $brokenRules = $label->getBrokenRules($rule);

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
                ->setAuditLogableType("App\Domain\IpAddress")
                ->setLevel(LogLevel::INFO->value)
                ->setLoggedAt(Carbon::now()->toDateTimeString())
                ->setMessage("User $user->id: Store $ipAddress->ipv4 | $request->label succeed")
                ->setContext(["ipv4" => $ipAddress->ipv4, "label" => $request->label]);

            $this->auditLogRepository->writeLogActivity($logActivity->build());
        } catch (Exception $ex) {
            DB::rollBack();

            $response->addErrorMessageResponse($ex->getMessage());
            $response->setType("ERROR");
            $response->setCodeStatus(HttpResponseType::INTERNAL_SERVER_ERROR->value);

            Log::error($ex->getMessage());
        }

        DB::commit();

        return $response;
    }

    public function updateIpAddress(UpdateIpAddressDataRequest $request): GenericObjectResponse
    {
        $response = new GenericObjectResponse();

        DB::beginTransaction();

        try {
            $user = Auth::user();

            $ipAddress = $this->ipAddressRepository->update($request);

            if (!$ipAddress) {
                $response->addErrorMessageResponse('Ip Address not found');
                $response->setType("ERROR");
                $response->setCodeStatus(HttpResponseType::NOT_FOUND->value);
            }

            $response->dto = $ipAddress;
            $response->addSuccessMessageResponse('Ip Address update succeed');
            $response->setType("SUCCESS");
            $response->setCodeStatus(HttpResponseType::SUCCESS->value);

            Log::info("Ip Address update succeed");

            $logActivity = (new LogActivityBuilder())
                ->setAuditLogableId($ipAddress->id)
                ->setAuditLogableType("App\Domain\IpAddress")
                ->setLevel(LogLevel::INFO->value)
                ->setLoggedAt(Carbon::now()->toDateTimeString())
                ->setMessage("User $user->id: Update $ipAddress->ipv4 | $request->label succeed")
                ->setContext(["ipv4" => $ipAddress->ipv4, "label" => $request->label]);

            $this->auditLogRepository->writeLogActivity($logActivity->build());

        } catch (Exception $ex) {
            DB::rollBack();

            $response->addErrorMessageResponse($ex->getMessage());
            $response->setType("ERROR");
            $response->setCodeStatus(HttpResponseType::INTERNAL_SERVER_ERROR->value);

            Log::error($ex->getMessage());
        }

        DB::commit();

        return $response;
    }

    public function destroyIpAddress(int $id): BasicResponse
    {
        $response = new BasicResponse();

        try {
            $user = Auth::user();

            $ipAddress = $this->ipAddressRepository->findById($id);

            $this->ipAddressRepository->delete($id);

            $response->addSuccessMessageResponse('Destroy ip address succeed');
            $response->setType("SUCCESS");
            $response->setCodeStatus(HttpResponseType::SUCCESS->value);

            Log::info("User $user->id: destroy ip address succeed", ["id" => $id, "ipv4" => $ipAddress->ipv4]);

            DB::beginTransaction();

            $logActivity = (new LogActivityBuilder())
                ->setAuditLogableId($user->id)
                ->setAuditLogableType("App\Domain\IpAddress")
                ->setLevel(LogLevel::INFO->value)
                ->setLoggedAt(Carbon::now()->toDateTimeString())
                ->setMessage("User $user->id: destroy ip address succeed")
                ->setContext(["id" => $id, "ipv4" => $ipAddress->ipv4]);

            $this->auditLogRepository->writeLogActivity($logActivity->build());

        } catch (\Exception $ex) {
            DB::rollBack();

            $response->addErrorMessageResponse($ex->getMessage());
            $response->setType("ERROR");
            $response->setCodeStatus(HttpResponseType::UNAUTHORIZED->value);

            Log::error($ex->getMessage());
        }

        DB::commit();

        return $response;
    }


    public function getLabelAll(): GenericListResponse
    {
        $response = new GenericListResponse();

        try {
            $labels = $this->labelRepository->all();

            $response->dtoList = $labels;
            $response->setType("SUCCESS");
            $response->setCodeStatus(HttpResponseType::SUCCESS->value);

        } catch (Exception $ex) {
            $response->addErrorMessageResponse($ex->getMessage());
            $response->setType("ERROR");
            $response->setCodeStatus(HttpResponseType::INTERNAL_SERVER_ERROR->value);

            Log::error($ex->getMessage());
        }

        return $response;
    }
}
