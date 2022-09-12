<?php

namespace App\Service;

use App\Application\Request\Auth\LogoutDataRequest;
use App\Application\Request\Auth\RegisterDataRequest;
use App\Core\Application\Request\SearchPageRequest;
use App\Core\Application\Request\SearchRequest;
use App\Core\Application\Response\GenericListResponse;
use App\Core\Application\Response\GenericListSearchPageResponse;
use App\Core\Application\Response\GenericListSearchResponse;
use App\Core\Application\Response\GenericObjectResponse;
use App\Core\Application\Response\HttpResponseType;
use App\Domain\User;
use App\Repository\Contract\IUserRepository;
use App\Service\Contract\IUserService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserService implements IUserService
{
    public IUserRepository $userRepository;

    /**
     * @param IUserRepository $userRepository
     */
    public function __construct(IUserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(RegisterDataRequest $request): GenericObjectResponse
    {
        $response = new GenericObjectResponse();

        DB::beginTransaction();

        try {
            $brokenRules = $request->getBrokenRules([
                'email' => 'required|string|unique:users|email',
                'password' => 'required|string|min:8',
                'confirm_password' => 'required|same:password'
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

            $register = $this->userRepository->register($request);

            $response->dto = $register;
            $response->addSuccessMessageResponse('User register succeed');
            $response->setType("SUCCESS");
            $response->setCodeStatus(HttpResponseType::SUCCESS->value);

            Log::info("User register succeed");

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
}
