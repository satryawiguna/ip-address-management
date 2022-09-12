<?php

namespace App\Service;

use App\Application\Request\Auth\LoginDataRequest;
use App\Application\Request\Auth\LogoutDataRequest;
use App\Core\Application\Response\BasicResponse;
use App\Core\Application\Response\GenericObjectResponse;
use App\Core\Application\Response\HttpResponseType;
use App\Repository\Contract\IUserRepository;
use App\Service\Contract\IAuthService;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Passport\Client as OClient;

class AuthService implements IAuthService
{
    public IUserRepository $userRepository;

    public function __construct(IUserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
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

                Log::info(implode(", ", $response->getMessageResponseError()));

                return $response;
            }

            if (!Auth::attempt(["email" => $request->getEmail(), "password" => $request->getPassword()])) {
                $response->addErrorMessageResponse('Login invalid');
                $response->setType("ERROR");
                $response->setCodeStatus(HttpResponseType::UNAUTHORIZED->value);

                return $response;
            }

            $user = Auth::user();

            $oClient = OClient::where('password_client', 1)->first();

            $http = new Client(['verify' => false]);
            $oauthResponse = $http->request('POST', 'iam_server/oauth/token', [
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

            Log::info("Login succeed");

        } catch (\Exception $ex) {
            Log::info($ex->getMessage());

            $response->addErrorMessageResponse($ex->getMessage());
            $response->setType("ERROR");
            $response->setCodeStatus(HttpResponseType::UNAUTHORIZED->value);
        }

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

                Log::info(implode(", ", $response->getMessageResponseError()));

                return $response;
            }

            $user = $this->userRepository->revokeToken($request->getEmail());

            if (!$user) {
                $response->addErrorMessageResponse("User not found");
                $response->setType("ERROR");
                $response->setCodeStatus(HttpResponseType::NOT_FOUND->value);

                Log::info(implode(", ", $response->getMessageResponseError()));

                return $response;
            }

            $response->addSuccessMessageResponse('Logout succeed');
            $response->setType("SUCCESS");
            $response->setCodeStatus(HttpResponseType::SUCCESS->value);

            Log::info("Logout succeed");

        } catch (\Exception $ex) {
            Log::info($ex->getMessage());

            $response->addErrorMessageResponse($ex->getMessage());
            $response->setType("ERROR");
            $response->setCodeStatus(HttpResponseType::UNAUTHORIZED->value);
        }

        return $response;
    }

}
