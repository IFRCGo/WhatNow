<?php

namespace App\Http\Controllers\Auth;

use OpenApi\Attributes as OA;

use App\Classes\MailApi\MailApiService;
use App\Exceptions\Auth\UserAlreadyConfirmedException;
use App\Exceptions\Auth\UserConfirmationException;
use App\Exceptions\Auth\UserConfirmationMismatchException;
use App\Http\Controllers\Controller;
use App\Models\Access\User\UserConfirmationToken;
use App\Notifications\Auth\UserNeedsConfirmation;
use App\Repositories\Access\UserRepository;
use Illuminate\Auth\Passwords\PasswordBroker;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;


class UserConfirmationController extends Controller
{
    use SendsPasswordResetEmails;
    private $mailApiService;

    public function __construct( MailApiService $mailApiService)
    {
        $this->mailApiService = $mailApiService;
    }

    #[OA\Get(
        path: '/users/{id}/resend',
        tags: ['Users'],
        summary: 'Resend confirmation email',
        description: 'Sends a confirmation email to the user',
        operationId: 'resendConfirmationEmail',
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(
            name: 'id',
            in: 'path',
            required: true,
            description: 'User ID',
            schema: new OA\Schema(
                type: 'integer',
            ),
        )],
        responses: [new OA\Response(
            response: 200,
            description: 'Successful response',
            content: new OA\JsonContent(
                type: 'object',
                properties: [new OA\Property(
                    property: 'data',
                    type: 'array',
                    items: new OA\Items(
                        type: 'object',
                    ),
                )],
            ),
        )],
    )]
    public function sendConfirmationEmail(UserRepository $userRepository, $userId)
    {
        $user = $userRepository->findOrFail($userId);
        $notification = new UserNeedsConfirmation(new UserConfirmationToken($user->confirmation_code), $this->mailApiService);
        $notification->toMail($user);
    }

    #[OA\Get(
        path: '/confirm/{token}',
        tags: ['Auth'],
        summary: 'Confirm user account',
        description: 'Confirms the user\'s account using the provided confirmation token',
        operationId: 'confirm',
        parameters: [new OA\Parameter(
            name: 'token',
            in: 'path',
            required: true,
            description: 'The user confirmation token',
            schema: new OA\Schema(
                type: 'string',
            ),
        )],
        responses: [new OA\Response(
            response: 200,
            description: 'Successful response',
            content: new OA\JsonContent(
                type: 'object',
                properties: [new OA\Property(
                    property: 'data',
                    type: 'array',
                    items: new OA\Items(
                        type: 'object',
                    ),
                )],
            ),
        )],
    )]
    public function confirm(UserRepository $userRepository, $token, Request $request, PasswordBroker $passwordBroker)
    {
        $user = null;

        try {
            $token = new UserConfirmationToken($token);

            $user = $userRepository->confirmAccount($token);

            if ($user->hasSetOwnPassword()) {
                return redirect()->route('login.account_confirmed');
            }

            $token = $passwordBroker->createToken($user);

            return redirect()->route('password.set', ['token' => $token])->with(['token' => $token]);
        } catch (UserConfirmationMismatchException $e) {
            return redirect()->route('login.confirmation_failed');
        } catch (UserAlreadyConfirmedException $e) {
            return redirect()->route('login.account_confirmed');
        } catch (UserConfirmationException $e) {
            if ($user) {
                $this->sendConfirmationEmail($userRepository, $user->id);
            }

            return redirect()->route('login.confirmation_failed');
        }
    }
}
