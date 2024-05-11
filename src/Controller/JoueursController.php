<?php

namespace App\Controller;

use App\Entity\Comptes;
use App\Entity\Joueurs;
use App\Repository\RoomsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

class JoueursController extends AbstractController
{

    #region JOIN A ROOM
    #[OA\Post(
        tags: ["Joueurs"],
        summary: "Join a room",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: "application/json",
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(
                            property: "roomId",
                            type: "string",
                            example: "roomId"
                        ),
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
    #[Route("/api/players/{username}/join", name: "joueur.join", methods: ["POST"])]
    public function Join(EntityManagerInterface $em, Comptes $compte = null, RoomsRepository $roomsRepository, Request $request): JsonResponse
    {
        if (!$compte) {
            return $this->json([
                "success" => false,
                "message" => "Account not found"
            ]);
        }

        $requestData = json_decode($request->getContent(), true);

        $roomId = $requestData["roomId"];
        $plainPwd = $requestData["password"];

        $room = $roomsRepository->findOneBy(['roomId' => $roomId]);
        if (!$room) {
            return $this->json([
                'success' => false,
                'message' => 'Room not found'
            ]);
        }
        if ($room->getPlayerNumber() == $room->getNbJoueursMax()) {
            return $this->json([
                'success' => false,
                'message' => 'Room already full'
            ]);
        }
        if ($room->isStarted()) {
            return $this->json([
                'success' => false,
                'message' => 'Room already started'
            ]);
        }
        if (!$room->isPublicRoom()) {
            if (!password_verify($plainPwd, $room->getRoomPassword())) {
                return $this->json([
                    'success' => false,
                    'message' => 'Wrong password'
                ]);
            }
        }

        $collection = $room->getJoueurs();

        foreach ($collection as $key => $joueur) {
            $_collection = $joueur->getCompteId();
            foreach ($_collection as $key => $_compte) {
                if ($compte == $_compte) {
                    return $this->json([
                        'success' => true,
                        'message' => 'Already in this room'
                    ]);
                }
            }
        }

        $room->setPlayerNumber($room->getPlayerNumber() + 1);

        $joueur = new Joueurs();
        $joueur->addCompteId($compte)
            ->addRoomId($room)
            ->setDead(false)
            ->setInRoom(true);

        $em->persist($joueur);

        $em->flush();

        return $this->json([
            "success" => true,
            "message" => "Room joined successfully"
        ]);
    }
    #endregion


    #region QUIT A ROOM
    #[OA\Post(
        tags: ["Joueurs"],
        summary: "Quit a room",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: "application/json",
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(
                            property: "roomId",
                            type: "string",
                            example: "roomId"
                        ),
                    ]
                )
            )
        ),
    )]
    #[Route("/api/players/{username}/quit", name: "joueur.quit", methods: ["POST"])]
    public function Quit(EntityManagerInterface $em, Comptes $compte = null, RoomsRepository $roomsRepository, Request $request): JsonResponse
    {
        if (!$compte) {
            return $this->json([
                "success" => false,
                "message" => "Account not found"
            ]);
        }

        $requestData = json_decode($request->getContent(), true);

        $roomId = $requestData["roomId"];

        $room = $roomsRepository->findOneBy(['roomId' => $roomId]);
        if (!$room) {
            return $this->json([
                'success' => false,
                'message' => 'Room not found'
            ]);
        }

        if ($room->isStarted()) {
            return $this->json([
                'success' => false,
                'message' => 'Room already started'
            ]);
        }

        $collection = $room->getJoueurs();
        $found = false;

        foreach ($collection as $key => $joueur) {
            $_collection = $joueur->getCompteId();
            foreach ($_collection as $key => $_compte) {
                if ($compte == $_compte) {
                    $em->remove($joueur);
                    $found = true;
                }
            }
        }

        if (!$found) {
            return $this->json([
                'success' => false,
                'message' => 'No such a player in this room'
            ]);
        }

        $room->setPlayerNumber($room->getPlayerNumber() - 1);

        $em->flush();

        return $this->json([
            "success" => true,
            "message" => "Room quited successfully"
        ]);
    }
    #endregion
}
