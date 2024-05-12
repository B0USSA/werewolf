<?php

namespace App\Controller;

use App\Entity\Comptes;
use App\Entity\Rooms;
use App\Repository\ComptesRepository;
use App\Repository\RoomsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

class RoomController extends AbstractController
{
    #region CREATE A NEW ROOM
    #[OA\Post(
        tags: ["Rooms"],
        summary: "Create a new room",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: "application/json",
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(
                            property: "nbJoueursMax",
                            type: "integer",
                            example: "8"
                        ),
                        new OA\Property(
                            property: "publicRoom",
                            type: "bool",
                            example: "true"
                        ),
                        new OA\Property(
                            property: "roomPassword",
                            type: "string",
                            example: "password"
                        ),
                        new OA\Property(
                            property: "hostId",
                            type: "string",
                            example: "username"
                        ),
                    ]
                )
            )
        ),
    )]
    #[Route('/api/rooms/{roomId}/create', name: 'room.create', methods: ['POST'])]
    public function Create(Request $request, EntityManagerInterface $em, Rooms $room = null, $roomId, ComptesRepository $comptesRepository): JsonResponse
    {
        if ($room) {
            return $this->json([
                'success' => false,
                'message' => 'RoomId already used'
            ]);
        }

        $requestData = json_decode($request->getContent(), true);

        try {
            $maxPlayers = $requestData['nbJoueursMax'];
            $publicRoom = $requestData['publicRoom'];
            $host = $comptesRepository->findOneBy(['username' => $requestData['hostId']]);
            $plainPassword = !$publicRoom ? $requestData['roomPassword'] : null;

            if (!$host) {
                return $this->json([
                    'success' => false,
                    'message' => 'Account not found'
                ]);
            }

            $room = new Rooms();
            $room->setRoomPassword(isset($requestData['roomPassword']) ? password_hash($plainPassword, PASSWORD_DEFAULT) : null)
                ->setRoomId($roomId)
                ->setPublicRoom($publicRoom)
                ->setHostId($host)
                ->setPlayerNumber(0)
                ->setNbJoueursMax($maxPlayers)
                ->setStarted(false);

            $em->persist($room);
            $em->flush();

            return new JsonResponse([
                "success" => true,
                "message" => "Room created successfully",
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                "success" => false,
                "message" => $e->getMessage()
            ]);
        }
    }
    #endregion


    #region DELETE A ROOM
    #[OA\Delete(
        tags: ["Rooms"],
        summary: "Disconnect a player",
    )]
    #[Route("/api/rooms/{roomId}/delete", name: "room.delete", methods: ["DELETE"])]
    public function Delete(EntityManagerInterface $em, Rooms $room = null): JsonResponse
    {
        if (!$room) {
            return $this->json([
                "success" => false,
                "message" => "Room not found"
            ]);
        }

        $collection = $room->getJoueurs();

        foreach ($collection as $key => $joueur) {
            $em->remove($joueur);
        }

        $em->remove($room);
        $em->flush();

        return $this->json([
            "success" => true,
            "message" => "Room deleted successfully"
        ]);
    }
    #endregion


    #region START A ROOM GAME
    #[OA\Get(
        tags: ["Rooms"],
        summary: "Start a room game",
    )]
    #[Route("/api/rooms/{roomId}/start", name: "room.startGame", methods: ["GET"])]
    public function StartGame(EntityManagerInterface $em, Rooms $room = null): JsonResponse
    {
        if (!$room) {
            return $this->json([
                "success" => false,
                "message" => "Room not found"
            ]);
        }

        $room->setStarted(true);
        $em->flush();

        return $this->json([
            "success" => true,
            "message" => "Room started successfully"
        ]);
    }
    #endregion


    #region LIST OF PLAYERS IN A ROOM
    #[OA\Get(
        tags: ["Rooms", "Joueurs"],
        summary: "List of players in a room",
    )]
    #[Route("/api/rooms/{roomId}/players", name: "room.players", methods: ["GET"])]
    public function Players(Rooms $room = null, ComptesRepository $comptesRepository): JsonResponse
    {
        if (!$room) {
            return $this->json([
                "success" => false,
                "message" => "Room not found"
            ]);
        }

        $collection = $room->getJoueurs();
        $response = [];

        foreach ($collection as $key => $player) {
            // $compte = $comptesRepository->findOneBy(["id" => $player->getCompteId()->toArray()]);

            $al = $player->getCompteId()->toArray();
            foreach ($al as $key => $kk) {
                $_player = [
                    "nom" => $kk->getUsername(),
                    "role" => $player->getRole(),
                ];
            }

            $response[] = $_player;
        }

        return $this->json($response);
    }
    #endregion


    #region GET ALL ROOMS
    #[OA\Get(
        tags: ["Rooms"],
        summary: "Get all rooms",
    )]
    #[Route("/api/rooms", name: "compte.getAll", methods: ["GET"])]
    public function GetAll(RoomsRepository $roomsRepository): JsonResponse
    {
        $rooms = $roomsRepository->findAll();

        $roomsTable = [];

        foreach ($rooms as $key => $room) {
            $_room = [
                "roomId" => $room->getRoomId(),
                "host" => $room->getHostId()->getUsername(),
                "playerNumber" => $room->getPlayerNumber(),
                "maxPlayers" => $room->getNbJoueursMax(),
                "isPublicRoom" => $room->isPublicRoom(),
                "isStarted" => $room->isStarted()
            ];

            $roomsTable[] = $_room;
        }

        return $this->json($roomsTable);
    }
    #endregion
}
