<?php

namespace App\Service;

use App\Application\Exceptions\ResponseBadRequestException;
use App\Application\Request\Auth\RegisterDataRequest;
use App\Core\Application\Response\GenericObjectResponse;
use App\Core\Application\Response\HttpResponseType;
use App\Repository\Contract\IUserRepository;
use App\Service\Contract\IUserService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserService extends BaseService implements IUserService
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
                throw new ResponseBadRequestException($brokenRules->errors()->getMessages());
            }

            $register = $this->userRepository->register($request);

            $response = $this->setGenericObjectResponse($response,
                $register,
                'SUCCESS',
                HttpResponseType::SUCCESS->value);

            Log::info("User register succeed");

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

            Log::info($ex->getMessage());
        }

        DB::commit();

        return $response;
    }
}
