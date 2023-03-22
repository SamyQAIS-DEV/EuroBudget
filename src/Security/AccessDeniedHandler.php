<?php

namespace App\Security;

use App\Security\Voter\CategoryVoter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;
use Twig\Environment;

class AccessDeniedHandler implements AccessDeniedHandlerInterface
{
    public function __construct(private readonly UrlGeneratorInterface $urlGenerator, private readonly Environment $twig)
    {
    }

    public function handle(Request $request, AccessDeniedException $accessDeniedException): Response
    {
        $attributes = $accessDeniedException->getAttributes();
        $session = $request->getSession();
        if (count($attributes) > 0) {
            $attribute = $attributes[0];
            if (in_array($attribute, [
                CategoryVoter::ACCESS
            ])) {
                $session->getFlashBag()->add('error', 'Vous devez être premium pour pouvoir accéder aux catégories');

                return new RedirectResponse($this->urlGenerator->generate('premium'));
            }

        }

        if (in_array('application/json', $request->getAcceptableContentTypes())) {
            return new JsonResponse(null, Response::HTTP_FORBIDDEN);
        }

        return new Response($this->twig->render('bundles/TwigBundle/Exception/error403.html.twig'), Response::HTTP_FORBIDDEN);
    }
}
