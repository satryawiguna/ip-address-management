<?php

namespace App\Service;

use App\Application\Exceptions\ResponseBadRequestException;
use App\Application\Exceptions\ResponseNotFoundException;
use App\Application\Log\LogActivityBuilder;
use App\Application\Request\CreateIpAddressDataRequest;
use App\Application\Request\CreateLabelDataRequest;
use App\Application\Request\UpdateIpAddressDataRequest;
use App\Application\Request\UpdateLabelDataRequest;
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
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

class ManageService extends BaseService implements IManageService
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

            $response = $this->setGenericListResponse($response,
                $ipAddresses,
                'SUCCESS',
                HttpResponseType::SUCCESS->value);

        } catch (Exception $ex) {
            $response = $this->setMessageResponse($response,
                'ERROR',
                HttpResponseType::INTERNAL_SERVER_ERROR->value,
                $ex->getMessage());

            Log::error($ex->getMessage());
        }

        return $response;
    }

    public function getIpAddressSearch(SearchRequest $searchRequest): GenericListSearchResponse
    {
        $response = new GenericListSearchResponse();

        try {
            $ipAddresses = $this->ipAddressRepository->allSearch($searchRequest->getSearch());

            $response = $this->setGenericListSearchResponse($response,
                $ipAddresses,
                $ipAddresses->count(),
                'SUCCESS',
                HttpResponseType::SUCCESS->value);

        } catch (Exception $ex) {
            $response = $this->setMessageResponse($response,
                'ERROR',
                HttpResponseType::INTERNAL_SERVER_ERROR->value,
                $ex->getMessage());

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

            $response = $this->setGenericListSearchPageResponse($response,
                $ipAddresses->getCollection(),
                $ipAddresses->count(),
                ["perPage" => $ipAddresses->perPage(), "currentPage" => $ipAddresses->currentPage()],
                'SUCCESS',
                HttpResponseType::SUCCESS->value);

        } catch (Exception $ex) {
            $response = $this->setMessageResponse($response,
                'ERROR',
                HttpResponseType::INTERNAL_SERVER_ERROR->value,
                $ex->getMessage());

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
                throw new ResponseNotFoundException("Ip Address not found");
            }

            $response = $this->setGenericObjectResponse($response,
                $ipAddress,
                'SUCCESS',
                HttpResponseType::SUCCESS->value);

        } catch (ResponseNotFoundException $ex) {
            $response = $this->setMessageResponse($response,
                'ERROR',
                HttpResponseType::NOT_FOUND->value,
                $ex->getMessage());

            Log::info($ex->getMessage(), $response->getMessageResponseError());

        } catch (Exception $ex) {
            $response = $this->setMessageResponse($response,
                'ERROR',
                HttpResponseType::INTERNAL_SERVER_ERROR->value,
                $ex->getMessage());

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
                throw new ResponseBadRequestException($brokenRules->errors()->getMessages());
            }

            foreach ($request->getLabel()->toArray() as $label) {
                $rule = ($label->id > 0) ? ['title' => 'required|string'] : ['title' => 'required|string|unique:labels'];
                $brokenRules = $label->getBrokenRules($rule);

                if ($brokenRules->fails()) {
                    throw new ResponseBadRequestException($brokenRules->errors()->getMessages());
                }
            }

            $user = Auth::user();

            $ipAddress = $this->ipAddressRepository->save($request);

            $response = $this->setGenericObjectResponse($response,
                $ipAddress,
                'SUCCESS',
                HttpResponseType::SUCCESS->value);

            Log::info("Ip Address store succeed");

            $logActivity = (new LogActivityBuilder())
                ->setAuditLogableId($ipAddress->id)
                ->setAuditLogableType("App\Domain\IpAddress")
                ->setLevel(LogLevel::INFO->value)
                ->setLoggedAt(Carbon::now()->toDateTimeString())
                ->setMessage("User $user->id: Store $ipAddress->ipv4 | $request->label succeed")
                ->setContext(["ipv4" => $ipAddress->ipv4, "label" => $request->label]);

            $this->auditLogRepository->writeLogActivity($logActivity->build());
        } catch (ResponseBadRequestException $ex) {
            $response = $this->setMessageResponse($response,
                'ERROR',
                HttpResponseType::BAD_REQUEST->value,
                $ex->getMessages());

            Log::info("Invalid field validation", $response->getMessageResponseError());

        } catch (Exception $ex) {
            DB::rollBack();

            $response = $this->setMessageResponse($response,
                'ERROR',
                HttpResponseType::INTERNAL_SERVER_ERROR->value,
                $ex->getMessage());

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
                throw new ResponseNotFoundException('Ip Address not found');
            }

            $response = $this->setGenericObjectResponse($response,
                $ipAddress,
                'SUCCESS',
                HttpResponseType::SUCCESS->value);

            Log::info("Ip Address update succeed");

            $logActivity = (new LogActivityBuilder())
                ->setAuditLogableId($ipAddress->id)
                ->setAuditLogableType("App\Domain\IpAddress")
                ->setLevel(LogLevel::INFO->value)
                ->setLoggedAt(Carbon::now()->toDateTimeString())
                ->setMessage("User $user->id: Update $ipAddress->ipv4 | $request->label succeed")
                ->setContext(["ipv4" => $ipAddress->ipv4, "label" => $request->label]);

            $this->auditLogRepository->writeLogActivity($logActivity->build());

        } catch(QueryException) {
            $response = $this->setMessageResponse($response,
                'ERROR',
                HttpResponseType::INTERNAL_SERVER_ERROR->value,
                'Duplicate entry detected');

        } catch (ResponseNotFoundException $ex) {
            $response = $this->setMessageResponse($response,
                'ERROR',
                HttpResponseType::NOT_FOUND->value,
                $ex->getMessage());

            Log::info($ex->getMessage(), $response->getMessageResponseError());

        } catch (Exception $ex) {
            DB::rollBack();

            $response = $this->setMessageResponse($response,
                'ERROR',
                HttpResponseType::INTERNAL_SERVER_ERROR->value,
                $ex->getMessage());

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

            if (!$ipAddress) {
                throw new ResponseNotFoundException('Ip Address not found');
            }

            $this->ipAddressRepository->delete($id);

            $response = $this->setMessageResponse($response,
                "SUCCESS",
                HttpResponseType::SUCCESS->value,
                'Destroy ip address ' . $ipAddress->id . ' succeed');

            Log::info("User $user->id: destroy ip address succeed", ["id" => $id, "ipv4" => $ipAddress->ipv4]);

            DB::beginTransaction();

            $logActivity = (new LogActivityBuilder())
                ->setAuditLogableId($ipAddress->id)
                ->setAuditLogableType("App\Domain\IpAddress")
                ->setLevel(LogLevel::INFO->value)
                ->setLoggedAt(Carbon::now()->toDateTimeString())
                ->setMessage("User $user->id: destroy ip address ' . $ipAddress->id . ' succeed")
                ->setContext(["id" => $ipAddress->id, "ipv4" => $ipAddress->ipv4]);

            $this->auditLogRepository->writeLogActivity($logActivity->build());

        } catch (ResponseNotFoundException $ex) {
            $response = $this->setMessageResponse($response,
                'ERROR',
                HttpResponseType::NOT_FOUND->value,
                $ex->getMessage());

            Log::info($ex->getMessage(), $response->getMessageResponseError());

        } catch (\Exception $ex) {
            DB::rollBack();

            $response = $this->setMessageResponse($response,
                'ERROR',
                HttpResponseType::INTERNAL_SERVER_ERROR->value,
                $ex->getMessage());

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

            $response = $this->setGenericListResponse($response,
                $labels,
                'SUCCESS',
                HttpResponseType::SUCCESS->value);

        } catch (Exception $ex) {
            $response = $this->setMessageResponse($response,
                'ERROR',
                HttpResponseType::INTERNAL_SERVER_ERROR->value,
                $ex->getMessage());

            Log::error($ex->getMessage());
        }

        return $response;
    }

    public function getLabelSearch(SearchRequest $searchRequest): GenericListSearchResponse
    {
        $response = new GenericListSearchResponse();

        try {
            $labels = $this->labelRepository->allSearch($searchRequest->getSearch());

            $response = $this->setGenericListSearchResponse($response,
                $labels->getCollection(),
                $labels->count(),
                'SUCCESS',
                HttpResponseType::SUCCESS->value);

        } catch (Exception $ex) {
            $response = $this->setMessageResponse($response,
                'ERROR',
                HttpResponseType::INTERNAL_SERVER_ERROR->value,
                $ex->getMessage());

            Log::error($ex->getMessage());
        }

        return $response;
    }

    public function getLabelSearchPage(SearchPageRequest $searchPageRequest): GenericListSearchPageResponse
    {
        $response = new GenericListSearchPageResponse();

        try {
            $labels = $this->labelRepository->allSearchPage($searchPageRequest->getSearch(),
                $searchPageRequest->getPerPage(),
                $searchPageRequest->getPage());

            $response = $this->setGenericListSearchPageResponse($response,
                $labels->getCollection(),
                $labels->count(),
                ["perPage" => $labels->perPage(), "currentPage" => $labels->currentPage()],
                'SUCCESS',
                HttpResponseType::SUCCESS->value);

        } catch (Exception $ex) {
            $response = $this->setMessageResponse($response,
                'ERROR',
                HttpResponseType::INTERNAL_SERVER_ERROR->value,
                $ex->getMessage());

            Log::error($ex->getMessage());
        }

        return $response;
    }

    public function getLabelById(int $id): GenericObjectResponse
    {
        $response = new GenericObjectResponse();

        try {
            $label = $this->labelRepository->findById($id);

            if (!$label) {
                throw new ResponseNotFoundException("Label not found");
            }

            $response = $this->setGenericObjectResponse($response,
                $label,
                'SUCCESS',
                HttpResponseType::SUCCESS->value);

        } catch (ResponseNotFoundException $ex) {
            $response = $this->setMessageResponse($response,
                'ERROR',
                HttpResponseType::NOT_FOUND->value,
                $ex->getMessage());

            Log::info($ex->getMessage(), $response->getMessageResponseError());
        } catch (Exception $ex) {
            $response = $this->setMessageResponse($response,
                'ERROR',
                HttpResponseType::INTERNAL_SERVER_ERROR->value,
                $ex->getMessage());

            Log::error($ex->getMessage());
        }

        return $response;
    }

    public function storeLabel(CreateLabelDataRequest $request): GenericObjectResponse
    {
        $response = new GenericObjectResponse();

        DB::beginTransaction();

        try {
            $brokenRules = $request->getBrokenRules([
                'title' => 'required|string|unique:labels'
            ]);

            if ($brokenRules->fails()) {
                throw new ResponseBadRequestException($brokenRules->errors()->getMessages());
            }

            $user = Auth::user();

            $label = $this->labelRepository->save($request);

            $response = $this->setGenericObjectResponse($response,
                $label,
                'SUCCESS',
                HttpResponseType::SUCCESS->value);

            Log::info("Label store succeed");

            $logActivity = (new LogActivityBuilder())
                ->setAuditLogableId($label->id)
                ->setAuditLogableType("App\Domain\Label")
                ->setLevel(LogLevel::INFO->value)
                ->setLoggedAt(Carbon::now()->toDateTimeString())
                ->setMessage("User $user->id: Store $label->title succeed")
                ->setContext(["title" => $label->title]);

            $this->auditLogRepository->writeLogActivity($logActivity->build());
        } catch (ResponseBadRequestException $ex) {
            $response = $this->setMessageResponse($response,
                'ERROR',
                HttpResponseType::BAD_REQUEST->value,
                $ex->getMessages());

            Log::info("Invalid field validation", $response->getMessageResponseError());

        } catch (Exception $ex) {
            DB::rollBack();

            $response = $this->setMessageResponse($response,
                'ERROR',
                HttpResponseType::INTERNAL_SERVER_ERROR->value,
                $ex->getMessage());

            Log::error($ex->getMessage());
        }

        DB::commit();

        return $response;
    }

    public function updateLabel(UpdateLabelDataRequest $request): GenericObjectResponse
    {
        $response = new GenericObjectResponse();

        DB::beginTransaction();

        try {
            $user = Auth::user();

            $label = $this->labelRepository->update($request);

            if (!$label) {
                throw new ResponseNotFoundException('Label not found');
            }

            $response = $this->setGenericObjectResponse($response,
                $label,
                'SUCCESS',
                HttpResponseType::SUCCESS->value);

            Log::info("Label update succeed");

            $logActivity = (new LogActivityBuilder())
                ->setAuditLogableId($label->id)
                ->setAuditLogableType("App\Domain\Label")
                ->setLevel(LogLevel::INFO->value)
                ->setLoggedAt(Carbon::now()->toDateTimeString())
                ->setMessage("User $user->id: Update $label->title succeed")
                ->setContext(["title" => $label->title]);

            $this->auditLogRepository->writeLogActivity($logActivity->build());

        } catch(QueryException) {
            $response = $this->setMessageResponse($response,
                'ERROR',
                HttpResponseType::INTERNAL_SERVER_ERROR->value,
                'Duplicate entry detected');

        } catch (ResponseNotFoundException $ex) {
            $response = $this->setMessageResponse($response,
                'ERROR',
                HttpResponseType::NOT_FOUND->value,
                $ex->getMessage());

            Log::info($ex->getMessage(), $response->getMessageResponseError());

        } catch (Exception $ex) {
            DB::rollBack();

            $response = $this->setMessageResponse($response,
                'ERROR',
                HttpResponseType::INTERNAL_SERVER_ERROR->value,
                $ex->getMessage());

            Log::error($ex->getMessage());
        }

        DB::commit();

        return $response;
    }

    public function destroyLabel(int $id): BasicResponse
    {
        $response = new BasicResponse();

        try {
            $user = Auth::user();

            $label = $this->labelRepository->findById($id);

            if (!$label) {
                throw new ResponseNotFoundException('Label not found');
            }

            $this->labelRepository->delete($id);

            $response = $this->setMessageResponse($response,
                "SUCCESS",
                HttpResponseType::SUCCESS->value,
                'Destroy label ' . $label->id . ' succeed');

            Log::info("User $user->id: destroy label succeed", ["id" => $id, "title" => $label->title]);

            DB::beginTransaction();

            $logActivity = (new LogActivityBuilder())
                ->setAuditLogableId($label->id)
                ->setAuditLogableType("App\Domain\Label")
                ->setLevel(LogLevel::INFO->value)
                ->setLoggedAt(Carbon::now()->toDateTimeString())
                ->setMessage("User $user->id: destroy label ' . $label->id . ' succeed")
                ->setContext(["id" => $label->id, "title" => $label->title]);

            $this->auditLogRepository->writeLogActivity($logActivity->build());

        } catch (ResponseNotFoundException $ex) {
            $response = $this->setMessageResponse($response,
                'ERROR',
                HttpResponseType::NOT_FOUND->value,
                $ex->getMessage());

            Log::info($ex->getMessage(), $response->getMessageResponseError());

        } catch (\Exception $ex) {
            DB::rollBack();

            $response = $this->setMessageResponse($response,
                'ERROR',
                HttpResponseType::INTERNAL_SERVER_ERROR->value,
                $ex->getMessage());

            Log::error($ex->getMessage());
        }

        DB::commit();

        return $response;
    }


}
