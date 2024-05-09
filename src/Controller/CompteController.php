<?php

namespace App\Controller;

use App\Entity\Comptes;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;
use App\Controller\SecurityController;

class CompteController extends AbstractController
{
    #region GENERATE A TOKEN FOR A PLAYER
    #[OA\Post(
        tags: ["Comptes"],
        summary: "Generate a token for a new player",
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
    #[Route('/api/comptes/{username}/signup', name: 'compte.signup', methods: ['POST'])]
    public function Signup(Request $request, EntityManagerInterface $em, SecurityController $securityController, Comptes $compte = null, $username): JsonResponse
    {
        if ($compte) {
            return $this->json([
                'success' => false,
                'message' => 'Username already used'
            ]);
        }
        $requestData = json_decode($request->getContent(), true);

        try {
            $plainPassword = $requestData['password'];

            $compte = new Comptes();
            $compte->setPassword(password_hash($plainPassword, PASSWORD_DEFAULT))
                ->setConnected(true)
                ->setUsername($username);

            $em->persist($compte);
            $em->flush();

            return new JsonResponse([
                "success" => true,
                "message" => "Player registered successfully",
                "token" => $securityController->Token($username),
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                "success" => false,
                "message" => $e->getMessage()
            ]);
        }
    }
    #endregion


    #region DISCONNECT A PLAYER
    #[OA\Get(
        tags: ["Comptes"],
        summary: "Disconnect a player",
    )]
    #[Route("/api/comptes/{username}/disconnect", name: "compte.deisconnect", methods: ["GET"])]
    public function Disconnect(EntityManagerInterface $em, Comptes $compte = null): JsonResponse
    {
        if (!$compte) {
            return $this->json([
                "success" => false,
                "message" => "Account not found"
            ]);
        }

        $compte->setConnected(false);
        $em->flush();

        return $this->json([
            "success" => true,
            "message" => "Account disconnected successfully"
        ]);
    }
    #endregion
}
