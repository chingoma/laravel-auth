<?php

namespace Lockminds\LaravelAuth\Controllers;

use Exception;
use Faker\Factory;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\Http\Controllers\AccessTokenController as BaseAccessController;
use Lockminds\LaravelAuth\Helpers\Auths;
use Lockminds\LaravelAuth\Helpers\Responses;
use Lockminds\LaravelAuth\Helpers\Validations;
use Lockminds\LaravelAuth\Jobs\StoreAndSendOTP;
use Lockminds\LaravelAuth\Models\Otp;
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
                ->select(['id', 'email', 'firstname'])
                ->where('email', '=', $username)
                ->first();

            if ($request->getParsedBody()['grant_type'] == 'refresh_token') {
                Log::info('Creating refresh token');
                $otp = rand(100000, 999999);
                $store = new Otp();
                $store->status = 'valid';
                $store->user_id = $user->id;
                $store->otp = $otp;
                $store->expires_at = now()->addMinutes(config('lockminds-auth.otp.ttl'));
                $store->save();
            }

            //issue token
            $tokenResponse = parent::issueToken($request);

            //convert response to json string
            $content = $tokenResponse->getContent();

            //convert json to array
            $data = json_decode($content, true);
            if ($request->getParsedBody()['grant_type'] == 'password') {
                Log::info('Creating new token');
                Auths::storeAndSendOTP($user->id);
                StoreAndSendOTP::dispatch($user->id);
            }

            return response()->json([
                'token' => $data['access_token'],
                'name' => $user->firstname,
                'id' => $user->id,
                'email' => $user->email,
                'role' => '',
                'permissions' =>[
                         "accounting_categories_create",
                    "accounting_categories_delete",
                    "accounting_categories_read",
                    "accounting_categories_update",
                    "accounting_settings_create",
                    "accounting_settings_delete",
                    "accounting_settings_read",
                    "accounting_settings_update",
                    "accounting_years_create",
                    "accounting_years_delete",
                    "accounting_years_read",
                    "accounting_years_update",
                    "accounts_create",
                    "accounts_delete",
                    "accounts_read",
                    "accounts_update",
                    "audits_read",
                    "bonds_approve",
                    "bonds_create",
                    "bonds_delete",
                    "bonds_read",
                    "bonds_review",
                    "bonds_update",
                    "business_settings_read",
                    "business_settings_update",
                    "client_categories_create",
                    "client_categories_delete",
                    "client_categories_read",
                    "client_categories_update",
                    "client_statement_read",
                    "clients_approve",
                    "clients_create",
                    "clients_delete",
                    "clients_read",
                    "clients_review",
                    "clients_update",
                    "companies_create",
                    "companies_delete",
                    "companies_read",
                    "companies_update",
                    "custom_rates_approve",
                    "custom_rates_create",
                    "custom_rates_delete",
                    "custom_rates_read",
                    "custom_rates_update",
                    "dealing_sheets_approve",
                    "dealing_sheets_create",
                    "dealing_sheets_delete",
                    "dealing_sheets_read",
                    "dealing_sheets_review",
                    "dealing_sheets_update",
                    "departments_create",
                    "departments_delete",
                    "departments_read",
                    "departments_update",
                    "end_of_day_report_create",
                    "end_of_day_report_read",
                    "end_of_day_report_update",
                    "journals_approve",
                    "journals_create",
                    "journals_delete",
                    "journals_read",
                    "journals_update",
                    "market_data_create",
                    "market_data_delete",
                    "market_data_read",
                    "market_data_update",
                    "news_letter_create",
                    "news_letter_delete",
                    "news_letter_read",
                    "news_letter_update",
                    "overdraft_approve",
                    "overdraft_read",
                    "payment_methods_create",
                    "payment_methods_delete",
                    "payment_methods_read",
                    "payment_methods_update",
                    "payments_approve",
                    "payments_create",
                    "payments_delete",
                    "payments_read",
                    "payments_update",
                    "receipts_approve",
                    "receipts_create",
                    "receipts_delete",
                    "receipts_read",
                    "receipts_update",
                    "reconciliation_create",
                    "reconciliation_delete",
                    "reconciliation_read",
                    "reconciliation_update",
                    "reports_bonds_read",
                    "reports_equities_read",
                    "reports_financials_read",
                    "reports_trading_read",
                    "roles_read",
                    "roles_update",
                    "schemes_create",
                    "schemes_delete",
                    "schemes_read",
                    "schemes_update",
                    "shares_approve",
                    "shares_create",
                    "shares_delete",
                    "shares_read",
                    "shares_review",
                    "shares_update",
                    "transactions_approve",
                    "transactions_read",
                    "user_categories_create",
                    "user_categories_delete",
                    "user_categories_read",
                    "user_categories_update",
                    "users_approve",
                    "users_create",
                    "users_delete",
                    "users_read",
                    "users_review",
                    "users_update"
                ],
                'notifications' => [],
                'authToken' => $data['access_token'],
                'access_token' => $data['access_token'],
                'refresh_token' => $data['refresh_token'],
            ]);
        } catch (ModelNotFoundException $e) { // email notfound
            return Responses::badCredentials(code: 400);
        } catch (Exception $e) {
            return Responses::unhandledException(exception: $e, code: 400);
        }
    }

    public function verifyOTP(ServerRequestInterface $request): JsonResponse
    {
        try {

            $otp = Otp::where('user_id', \auth()->id())
                ->where('status', 'invalid')
                ->where('otp', $request->getParsedBody()['otp'])
                ->where('expires_at', '>', now())
                ->first();

            if (empty($otp->otp)) {
                return Responses::error('Invalid OTP');
            }

            $otp->status = 'valid';
            $otp->save();

            //get username (default is :email)
            $username = $request->getParsedBody()['username'];

            //get user
            $user = DB::table('users')
                ->select(['id', 'email'])
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

            return response()->json($user);
        } catch (ModelNotFoundException $e) { // email notfound
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

            return Responses::success(data: [
                'client_id' => $client->id,
                'client_secret' => $client->secret,
            ]);
        } catch (Throwable $throwable) {
            return Responses::unhandledThrowable(throwable: $throwable, code: 'unhandledException');
        }
    }

    public function generateUser(): JsonResponse
    {
        try {
            $user = new User();
            $user->name = Factory::create()->name;
            $user->email = Factory::create()->email;
            $user->password = Hash::make('Nyambura1!');
            $user->save();

            return Responses::success(data: $user);
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
                ? Responses::success(message: 'Password reset link has been sent to your email')
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
                ? Responses::success(message: 'Password reset successfully')
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
                ? Responses::success(message: 'Password reset successfully')
                : Responses::error(code: 400, message: __($status));

        } catch (Throwable $throwable) {
            return Responses::unhandledThrowable(throwable: $throwable, code: 'unhandledException');
        }
    }

    public function resendOTP(): JsonResponse
    {

        try {

            StoreAndSendOTP::dispatch(\auth()->id());

            return Responses::success(message: 'OTP resent');

        } catch (Throwable $throwable) {
            return Responses::unhandledThrowable(throwable: $throwable, code: 'unhandledException');
        }
    }
}
