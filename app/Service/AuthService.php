<?php

namespace App\Service;

use App\Application\Exceptions\ResponseBadRequestException;
use App\Application\Exceptions\ResponseInvalidClientException;
use App\Application\Exceptions\ResponseInvalidLoginAttemptException;
use App\Application\Exceptions\ResponseNotFoundException;
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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Laravel\Passport\Client as OClient;

class AuthService extends BaseService implements IAuthService
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
                throw new ResponseBadRequestException($brokenRules->errors()->getMessages());
            }

            if (!Auth::attempt(["email" => $request->getEmail(), "password" => $request->getPassword()])) {
                throw new ResponseInvalidLoginAttemptException('Login invalid');
            }

            $oClient = new OClient();
            $oClient->setKeyType("string");
            $oClient = $oClient->where('password_client', 1)->first();

            $oauthResponse = Http::asForm()->post(env('APP_URL') . '/oauth/token', [
                'grant_type' => 'password',
                'client_id' => $oClient->id,
                'client_secret' => $oClient->secret,
                'username' => $request->getEmail(),
                'password' => $request->getPassword(),
                'scope' => '*',
            ]);

            if (array_key_exists("error", $oauthResponse->json())) {
                throw new ResponseInvalidClientException($oauthResponse->json()["message"]);
            }

            $token = $oauthResponse->json();

            $user = Auth::user();

            $login = [
                'full_name' => $user->full_name,
                'nick_name' => $user->nick_name,
                'email' => $user->email,
                'token' => $token
            ];

            $response = $this->setGenericObjectResponse($response,
                $login,
                'SUCCESS',
                HttpResponseType::SUCCESS->value);

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

        } catch (ResponseBadRequestException $ex) {
            $response = $this->setMessageResponse($response,
                'ERROR',
                HttpResponseType::BAD_REQUEST->value,
                $ex->getMessages());

            Log::info("Invalid field validation", $response->getMessageResponseError());

        } catch (ResponseInvalidLoginAttemptException $ex) {
            $response = $this->setMessageResponse($response,
                "ERROR",
                HttpResponseType::UNAUTHORIZED->value,
                $ex->getMessage());

            Log::info("Invalid auth attempt", [$ex->getMessage()]);
        } catch (ResponseInvalidClientException $ex) {
            $response = $this->setMessageResponse($response,
                "ERROR",
                HttpResponseType::UNAUTHORIZED->value,
                $ex->getMessage());

            Log::info("Invalid client", [$ex->getMessage()]);
        } catch (\Exception $ex) {
            DB::rollBack();

            $response = $this->setMessageResponse($response,
                'ERROR',
                HttpResponseType::UNAUTHORIZED->value,
                $ex->getMessage());

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
                throw new ResponseBadRequestException($brokenRules->errors()->getMessages());
            }

            $user = $this->userRepository->revokeToken($request->getEmail());

            if (!$user) {
                throw new ResponseNotFoundException('User not found');
            }

            $response = $this->setMessageResponse($response,
                'SUCCESS',
                HttpResponseType::SUCCESS->value,
                'Logout succeed');

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

        } catch (ResponseBadRequestException $ex) {
            $response = $this->setMessageResponse($response,
                'ERROR',
                HttpResponseType::BAD_REQUEST->value,
                $ex->getMessages());

            Log::info("Invalid field validation", $response->getMessageResponseError());

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
                HttpResponseType::UNAUTHORIZED->value,
                $ex->getMessage());

            Log::error($ex->getMessage());
        }

        DB::commit();

        return $response;
    }

    public function refreshToken(string $token): GenericObjectResponse
    {
        $response = new GenericObjectResponse();

        try {
            $oClient = new OClient();
            $oClient->setKeyType("string");
            $oClient = $oClient->where('password_client', 1)->first();

            $oauthResponse = Http::asForm()->post('iam_server/oauth/token', [
                'grant_type' => 'refresh_token',
                'refresh_token' => $token,
                'client_id' => $oClient->id,
                'client_secret' => $oClient->secret,
                'scope' => '*',
            ]);

            if (array_key_exists("error", $oauthResponse->json())) {
                throw new ResponseInvalidClientException($oauthResponse->json()["message"]);
            }

            $token = $oauthResponse->json();

            $refresh = [
                'token' => $token
            ];

            $response = $this->setGenericObjectResponse($response,
                $refresh,
                'SUCCESS',
                HttpResponseType::SUCCESS->value);

        } catch (ResponseInvalidClientException $ex) {
            $response = $this->setMessageResponse($response,
                "ERROR",
                HttpResponseType::UNAUTHORIZED->value,
                $ex->getMessage());

            Log::info("Invalid client", [$ex->getMessage()]);
        } catch (\Exception $ex) {
            $response->addErrorMessageResponse($ex->getMessage());
            $response->setType("ERROR");
            $response->setCodeStatus(HttpResponseType::UNAUTHORIZED->value);

            Log::error($ex->getMessage());
        }

        return $response;
    }
}
