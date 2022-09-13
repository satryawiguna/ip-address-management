<?php

namespace App\Service;

use App\Application\Log\LogActivity;
use App\Application\Log\LogActivityBuilder;
use App\Application\Request\Auth\LoginDataRequest;
use App\Application\Request\Auth\LogoutDataRequest;
use App\Core\Application\LogLevel;
use App\Core\Application\Response\BasicResponse;
use App\Core\Application\Response\GenericObjectResponse;
use App\Core\Application\Response\HttpResponseType;
use App\Repository\Contract\IAuditLogRepository;
use App\Repository\Contract\IUserRepository;
use App\Service\Contract\IAuthService;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laravel\Passport\Client as OClient;

class AuthService implements IAuthService
{
    public IUserRepository $userRepository;
    public IAuditLogRepository $auditLogRepository;

    public function __construct(IUserRepository $userRepository,
        IAuditLogRepository $auditLogRepository)
    {
        $this->userRepository = $userRepository;
        $this->auditLogRepository = $auditLogRepository;
    }

    public function login(LoginDataRequest $request): GenericObjectResponse
    {
        $response = new GenericObjectResponse();

        try {
            $brokenRules = $request->getBrokenRules([
                'email' => 'required|email',
                'password' => 'required'
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

            if (!Auth::attempt(["email" => $request->getEmail(), "password" => $request->getPassword()])) {
                $response->addErrorMessageResponse('Login invalid');
                $response->setType("ERROR");
                $response->setCodeStatus(HttpResponseType::UNAUTHORIZED->value);

                Log::info("Invalid auth attempt", [$response->getMessageResponseErrorLatest()]);

                return $response;
            }

            $oClient = OClient::where('password_client', 1)->first();

            $http = new Client(['verify' => false]);
            $oauthResponse = $http->post('iam_server/oauth/token', [
                'form_params' => [
                    'grant_type' => 'password',
                    'client_id' => $oClient->id,
                    'client_secret' => $oClient->secret,
                    'username' => $request->getEmail(),
                    'password' => $request->getPassword(),
                    'scope' => '*',
                ],
            ]);

            $token = json_decode((string) $oauthResponse->getBody(),true);

            $user = Auth::user();

            $login = [
                'full_name' => $user->full_name,
                'nick_name' => $user->nick_name,
                'email' => $user->email,
                'token' => $token
            ];

            $response->dto = (object) $login;
            $response->addSuccessMessageResponse('Login succeed');
            $response->setType("SUCCESS");
            $response->setCodeStatus(HttpResponseType::SUCCESS->value);

            Log::info("User $user->id: Login succeed", ["email" => $login["email"]]);

            DB::beginTransaction();

            $logActivity = (new LogActivityBuilder())
                ->setAuditLogableId($user->id)
                ->setAuditLogableType("App\Domain\User")
                ->setLevel(LogLevel::INFO->value)
                ->setLoggedAt(Carbon::now()->toDateTimeString())
                ->setMessage("User $user->id: Login succeed")
                ->setContext(["email" => $user->email]);

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

    public function logout(LogoutDataRequest $request): BasicResponse
    {
        $response = new BasicResponse();

        try {
            $brokenRules = $request->getBrokenRules([
                'email' => 'required|email'
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

            $user = $this->userRepository->revokeToken($request->getEmail());

            if (!$user) {
                $response->addErrorMessageResponse("User not found");
                $response->setType("ERROR");
                $response->setCodeStatus(HttpResponseType::NOT_FOUND->value);

                Log::info("User not found", [$response->getMessageResponseErrorLatest()]);

                return $response;
            }

            $response->addSuccessMessageResponse('Logout succeed');
            $response->setType("SUCCESS");
            $response->setCodeStatus(HttpResponseType::SUCCESS->value);

            Log::info("User $user->id: Logout succeed", ["email" => $user->email]);

            DB::beginTransaction();

            $logActivity = (new LogActivityBuilder())
                ->setAuditLogableId($user->id)
                ->setAuditLogableType("App\Domain\User")
                ->setLevel(LogLevel::INFO->value)
                ->setLoggedAt(Carbon::now()->toDateTimeString())
                ->setMessage("User $user->id: Logout succeed")
                ->setContext(["email" => $user->email]);

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

}
