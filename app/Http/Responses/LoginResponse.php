<?php

namespace App\Http\Responses;

use App\Enums\UserRole;
use App\Http\Responses\Concerns\RedirectsToCurrentTeam;
use Illuminate\Http\JsonResponse;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Laravel\Fortify\Fortify;
use Symfony\Component\HttpFoundation\Response;

class LoginResponse implements LoginResponseContract
{
    use RedirectsToCurrentTeam;

    public function toResponse($request): Response
    {
        if ($request->user()?->role === UserRole::Administrateur) {
            return redirect()->route('admin.dashboard');
        }

        return $request->wantsJson()
            ? new JsonResponse(['two_factor' => false], 200)
            : redirect($this->redirectPathForCurrentTeam($request, Fortify::redirects('login')));
    }
}
