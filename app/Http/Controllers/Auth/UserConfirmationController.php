<?php

namespace App\Http\Controllers\Auth;

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

    /**
     * @OA\Get(
     *     path="/users/{id}/resend",
     *     tags={"Users"},
     *     summary="Resend confirmation email",
     *     description="Sends a confirmation email to the user",
     *     operationId="resendConfirmationEmail",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="User ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function sendConfirmationEmail(UserRepository $userRepository, $userId)
    {
        $user = $userRepository->findOrFail($userId);
        $user->notify(new UserNeedsConfirmation(new UserConfirmationToken($user->confirmation_code)));
    }

    
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
