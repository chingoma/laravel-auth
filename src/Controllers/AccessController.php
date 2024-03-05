<?php

namespace Lockminds\LaravelAuth\Controllers;

use Exception;
use Faker\Factory;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\Exceptions\OAuthServerException;
use Laravel\Passport\Http\Controllers\AccessTokenController as BaseAccessController;
use Lockminds\LaravelAuth\Helpers\Responses;
use Lockminds\LaravelAuth\Helpers\Validations;
use Lockminds\LaravelAuth\Models\User;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

class AccessController extends BaseAccessController
{
    public function issueToken(ServerRequestInterface $request): JsonResponse
    {
        try {

            //get username (default is :email)
            $username = $request->getParsedBody()['username'];

            //get user
            $user = DB::table('users')
                ->where('email', '=', $username)
                ->first();

            //issue token
            $tokenResponse = parent::issueToken($request);

            //convert response to json string
            $content = $tokenResponse->getContent();

            //convert json to array
            $data = json_decode($content, true);

            //add access token to user
            $user = collect($user);
            $user->put('access_token', $data['access_token']);
            $user->put('refresh_token', $data['refresh_token']);
            $user->put('expires_at', $data['expires_in']);
            $user->put('status', 'success');
            \Lockminds\LaravelAuth\Jobs\StoreAndSendOTP::dispatchAfterResponse($user->id);

            return response()->json($user);
        } catch (ModelNotFoundException $e) { // email notfound
            return Responses::badCredentials(code: 400);
        } catch (OAuthServerException $e) { //password not correct..token not granted
            return Responses::badCredentials(code: 400);
        } catch (Exception $e) {
            return Responses::unhandledException(exception: $e, code: 400);
        }
    }

    public function createClient(): JsonResponse
    {
        try {
            $factory = Factory::create();
            $clients = App::make(ClientRepository::class);
            $client = $clients->create(userId: null, name: $factory->name, redirect: '', password: true);

            return Responses::success(code: 200, data: [
                'client_id' => $client->id,
                'client_secret' => $client->secret,
            ]);
        } catch (Throwable $throwable) {
            return Responses::unhandledThrowable(throwable: $throwable, code: 'unhandledException');
        }
    }

    public function sendResetPasswordLink(Request $request): JsonResponse
    {
        try {

            $user = DB::table('uses')
                ->where('email', $request->email)
                ->first();

            if (empty($user->email)) {
                return Responses::badCredentials(code: 400);
            }

            $status = Password::sendResetLink(
                $request->only('email')
            );

            return $status === Password::RESET_LINK_SENT
                ? Responses::success(code: 200, message: 'Password reset link has been sent to your email')
                : Responses::error(code: 400, message: __($status));

        } catch (Throwable $throwable) {
            return Responses::unhandledThrowable($throwable, code: 'unhandledException');
        }
    }

    public function changePassword(Request $request): JsonResponse
    {

        try {

            $validation = Validator::make($request->all(), Validations::changePassword());

            if ($validation->fails()) {
                return Responses::validationError(code: 400, message: $validation->messages()->first(), data: $validation->errors());
            }

            $status = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function (User $user, string $password) {
                    $user->forceFill([
                        'password' => Hash::make($password),
                    ])->setRememberToken(Str::random(60));

                    $user->save();

                    event(new PasswordReset($user));
                }
            );

            return $status === Password::PASSWORD_RESET
                ? Responses::success(code: 200, message: 'Password reset successfully')
                : Responses::error(code: 400, message: __($status));

        } catch (Throwable $throwable) {
            return Responses::unhandledThrowable(throwable: $throwable, code: 'unhandledException');
        }
    }

    public function resetPassword(Request $request): JsonResponse
    {

        try {

            $validator = Validator::make($request->all(), Validations::resetPassword());

            if ($validator->fails()) {
                return Responses::validationError(code: 400, message: $validator->messages()->first(), data: $validator->errors());
            }

            $user = User::find($request->id);

            if (empty($user->email)) {
                return Responses::badCredentials(code: 400);
            }

            if (! Hash::check($request->current_password, $user->password)) {
                return Responses::badCredentials(code: 400);
            }

            $status = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function (User $user, string $password) {
                    $user->forceFill([
                        'password' => Hash::make($password),
                    ])->setRememberToken(Str::random(60));

                    $user->save();

                    event(new PasswordReset($user));
                }
            );

            return $status === Password::PASSWORD_RESET
                ? Responses::success(code: 200, message: 'Password reset successfully')
                : Responses::error(code: 400, message: __($status));

        } catch (Throwable $throwable) {
            return Responses::unhandledThrowable(throwable: $throwable, code: 'unhandledException');
        }
    }

    public function logout(): JsonResponse
    {

        try {

            Auth::logout();

            return Responses::success(code: 200, message: 'You have successfully logged out');

        } catch (Throwable $throwable) {
            return Responses::unhandledThrowable(throwable: $throwable, code: 'unhandledException');
        }
    }

    public function resendOTP(): JsonResponse
    {

        try {

            \Lockminds\LaravelAuth\Jobs\StoreAndSendOTP::dispatchAfterResponse(\auth()->id());

            return Responses::success(code: 200, message: 'OTP resent');

        } catch (Throwable $throwable) {
            return Responses::unhandledThrowable(throwable: $throwable, code: 'unhandledException');
        }
    }

    public function verifyOTP(Request $request): JsonResponse
    {

        try {

            $key = \auth()->id();

            $oldOtp = \Cache::get($key);

            if (empty($oldOtp)) {
                \Cache::put('otp-verified-'.$key, false);

                return Responses::error(code: 400, message: 'OTP Expired');
            }

            if ($request->otp !== $oldOtp) {
                \Cache::put('otp-verified-'.$key, false);

                return Responses::error(code: 400, message: 'OTP Expired');
            }

            \Cache::put('otp-verified-'.$key, true);

            return Responses::success(code: 200, message: 'OTP Verified');

        } catch (Throwable $throwable) {
            return Responses::unhandledThrowable(throwable: $throwable, code: 'unhandledException');
        }
    }
}
