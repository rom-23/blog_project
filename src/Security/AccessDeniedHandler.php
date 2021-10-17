<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;

class AccessDeniedHandler implements AccessDeniedHandlerInterface
{

    public function handle(Request $request, AccessDeniedException $accessDeniedException): RedirectResponse|JsonResponse|Response|null
    {
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(['message' => $accessDeniedException->getMessage(), 'trace' => $accessDeniedException->getTraceAsString(), 'exception' => get_class($accessDeniedException)], Response::HTTP_SERVICE_UNAVAILABLE);
        } else {
            $url = $request->getBasePath() !== "" ? $request->getBasePath() : "/";
            $response = new RedirectResponse($url);
            $response->setStatusCode(Response::HTTP_FORBIDDEN);
            $response->prepare($request);
            return $response->send();
        }
    }
}
