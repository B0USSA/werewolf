<?php

namespace App\Controller;

use App\Entity\Comptes;
use App\Repository\ComptesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Firebase\JWT\JWT;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

class SecurityController extends AbstractController
{
    #region GENERATE A TOKEN FOR A PLAYER
    #[OA\Post(
        tags: ["Security"],
        summary: "Generate a token for a player",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: "application/json",
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(
                            property: "password",
                            type: "string",
                            example: "password"
                        ),
                    ]
                )
            )
        ),
    )]
    #[Route('/api/comptes/{username}/login', name: 'security.generateToken', methods: ['POST'])]
    public function GenerateToken(Request $request, ComptesRepository $ComptesRepository, EntityManagerInterface $em, Comptes $compte = null): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        $plainPassword = $requestData['password'];

        if ($compte && password_verify($plainPassword, $compte->getPassword())) {
            $compte->setConnected(true);
            $em->flush();

            return new JsonResponse([
                'token' => $this->Token($compte->getUsername()),
            ]);
        }

        return new JsonResponse([
            "success" => false,
            "message" => "Identifiants incorrects."
        ]);
    }

    public function Token($username)
    {
        $secretKey = "LUM€N$";
        $token = [
            "iat" => time(),
            "exp" => time() + (3600 * 3),
            "sub" => $username
        ];

        $jwt = JWT::encode($token, $secretKey, 'HS256');

        return $jwt;
    }
    #endregion
}
