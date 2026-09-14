<?php

namespace App\Http\Responses;

use App\Enums\UserRole;
use App\Http\Responses\Concerns\RedirectsToCurrentTeam;
use Illuminate\Http\JsonResponse;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;
use Laravel\Fortify\Fortify;
use Symfony\Component\HttpFoundation\Response;

class RegisterResponse implements RegisterResponseContract
{
    use RedirectsToCurrentTeam;

    public function toResponse($request): Response
    {
        if ($request->user()?->role === UserRole::Administrateur) {
            return redirect()->route('admin.dashboard');
        }

        return $request->wantsJson()
            ? new JsonResponse(['two_factor' => false], 201)
            : redirect($this->redirectPathForCurrentTeam($request, Fortify::redirects('register')));
    }
}
