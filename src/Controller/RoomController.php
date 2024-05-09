<?php

namespace App\Controller;

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
                            property: "playerNumber",
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
            $playerNumber = $requestData['playerNumber'];
            $publicRoom = $requestData['publicRoom'];
            $host = $comptesRepository->findOneBy(['username' => $requestData['hostId']]);
            $plainPassword = $requestData['roomPassword'];

            $room = new Rooms();
            $room->setRoomPassword(isset($requestData['roomPassword']) ? password_hash($plainPassword, PASSWORD_DEFAULT) : null)
                ->setRoomId($roomId)
                ->setPublicRoom($publicRoom)
                ->setHostId($host)
                ->setPlayerNumber($playerNumber)
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
        tags: ["Comptes"],
        summary: "Disconnect a player",
    )]
    #[Route("/api/rooms/{roomId}/delete", name: "compte.delete", methods: ["DELETE"])]
    public function Delete(EntityManagerInterface $em, Rooms $room = null): JsonResponse
    {
        if (!$room) {
            return $this->json([
                "success" => false,
                "message" => "Room not found"
            ]);
        }

        $em->remove($room);
        $em->flush();

        return $this->json([
            "success" => true,
            "message" => "Room deleted successfully"
        ]);
    }
    #endregion


    #region GET ALL ROOMS
    #[OA\Get(
        tags: ["Rooms"],
        summary: "Get all rooms",
    )]
    #[Route("/api/rooms", name: "compte.getAll", methods: ["GET"])]
    public function GetAll(RoomsRepository $roomsRepository ): JsonResponse
    {
        $rooms = $roomsRepository->findAll();

        $roomsTable = [];

        foreach ($rooms as $key => $room) {
            $_room = [
                "roomId" => $room->getRoomId(),
                "host" => $room->getHostId()->getUsername(),
                "playerNumber" => $room->getPlayerNumber(),
                "isPublicRoom" => $room->isPublicRoom(),
                "isStarted" => $room->isStarted()
            ];

            $roomsTable[] = $_room;
        }
        
        return $this->json($roomsTable);
    }
    #endregion
}
