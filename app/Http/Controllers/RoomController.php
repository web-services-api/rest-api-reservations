<?php

namespace App\Http\Controllers;

use App\Models\Room;

use Illuminate\Http\Request;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\RoomResource;
use App\Interfaces\RoomRepositoryInterface;
use App\Interfaces\CinemaRepositoryInterface;
use App\Http\Requests\StoreOrUpdateRoomRequest;
use App\Classes\ApiResponseClass as ResponseClass;

/**
 * @OA\Info(title="Cinemas - API", version="1.0")
 */ 
class RoomController extends Controller
{
    
    private RoomRepositoryInterface $roomRepositoryInterface;
    
    public function __construct(
        RoomRepositoryInterface $roomRepositoryInterface,
        CinemaRepositoryInterface $cinemaRepositoryInterface
    )
    {
        $this->roomRepositoryInterface = $roomRepositoryInterface;
        $this->cinemaRepositoryInterface = $cinemaRepositoryInterface;
    }

    /**
     * Displays a list of resources.
    **/
    /**
     * @OA\Get(
     *     path="/api/cinemas/{cinemaId}/rooms",
     *     operationId="getRoomsListByCinema",
     *     tags={"Rooms"},
     *     summary="Gets the list of rooms by cinema",
     *     description="Returns the list of rooms by cinema with optional pagination",
     *     @OA\Parameter(
     *         name="perPage",
     *         in="query",
     *         description="Number of cinemas per page",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             default=10
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="cinemaId",
     *         in="path",
     *         description="ID of cinema to return rooms",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="uuid"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(type="object")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No room found"
     *     ),
     *      @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function index($cinemaId, $perPage = 10)
    {
        $perPage = request()->query('perPage', $perPage);

        $cinema = $this->cinemaRepositoryInterface->getById($cinemaId);
        if (!$cinema) {
            return ResponseClass::sendResponse(null, "Cinema not found for cinema : ".$cinemaId, 404);
        }

        $data = $this->roomRepositoryInterface->index($cinemaId, $perPage);

        if ($data->isEmpty()) {
            return ResponseClass::sendResponse([], 'No rooms found for this cinema : '.$cinemaId, 204);
        }
        
        return ResponseClass::sendResponse(RoomResource::collection($data),'',200, true);
    }


    /**
     * Store a newly created resource in storage.
     */
    /**
     * @OA\Post(
     *     path="/api/cinemas/{cinemaId}/rooms",
     *     operationId="storeRoomByCinema",
     *     tags={"Rooms"},
     *     summary="Store a new room by cinema",
     *     description="Stores a new room by cinema and returns the room data",
     *     @OA\Parameter(
     *         name="cinemaId",
     *         in="path",
     *         description="ID of cinema to store room",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="uuid"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Room data",
     *         @OA\JsonContent(
     *             required={"name", "seats"},
     *             @OA\Property(property="name", type="string", example="A New Beginning"),
     *             @OA\Property(property="seats", type="integer", example="10")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="string", format="uuid", example="3434-4343-4343-4343"),
     *             @OA\Property(property="name", type="string", example="A New Beginning"),
     *             @OA\Property(property="seats", type="integer", example="10"),
     *             @OA\Property(property="cinema_id", type="string", format="uuid", example="3434-4343-4343-4343"),
    *              @OA\Property(property="created_at", type="string", format="date", example="2021-09-15"),
    *              @OA\Property(property="updated_at", type="string", format="date", example="2021-09-15")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid input data"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function store(StoreOrUpdateRoomRequest $request, $cinemaId)
    {
        DB::beginTransaction();
        try{

            $cinema = $this->cinemaRepositoryInterface->getById($cinemaId);
            if (!$cinema) {
                return ResponseClass::sendResponse(null, "Cinema not found for cinema : ".$cinemaId, 404);
            }

            $data = [
                'name' => $request->input('name'),
                'seats' => $request->input('seats'),
                'cinema_id' => $cinemaId
            ];

            $room = $this->roomRepositoryInterface->store($data, $cinemaId);

            DB::commit();
            return ResponseClass::sendResponse(new RoomResource($room),'Room created successfully for cinema : '.$cinemaId,201);

        }catch(\Exception $ex){
            return ResponseClass::rollback($ex);
        }
    }

    /**
     * Display the specified resource.
     */
    /**
     * @OA\Get(
     *     path="/api/cinemas/{cinemaId}/rooms/{roomId}",
     *     operationId="getRoomByIdByCinema",
     *     tags={"Rooms"},
     *     summary="Get room by ID by cinema",
     *     description="Returns a single room by cinema",
     *     @OA\Parameter(
     *         name="cinemaId",
     *         in="path",
     *         description="ID of cinema to return",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="uuid"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="roomId",
     *         in="path",
     *         description="ID of room to return",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="uuid"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="string", format="uuid", example="3434-4343-4343-4343"),
     *             @OA\Property(property="name", type="string", example="A New Beginning"),
     *             @OA\Property(property="seats", type="integer", example="10"),
     *             @OA\Property(property="cinema_id", type="string", format="uuid", example="3434-4343-4343-4343"),
     *             @OA\Property(property="created_at", type="string", format="date", example="2021-09-15"),
     *             @OA\Property(property="updated_at", type="string", format="date", example="2021-09-15")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Room not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function show($cinemaId, $id)
    {
        $cinema = $this->cinemaRepositoryInterface->getById($cinemaId);
        if (!$cinema) {
            return ResponseClass::sendResponse(null, "Cinema not found for cinema : ".$cinemaId, 404);
        }
        $room = $this->roomRepositoryInterface->getById($cinemaId, $id);
        if (!$room) {
            return ResponseClass::sendResponse(null, "Room not found for cinema : ".$cinemaId, 404);
        }

        return ResponseClass::sendResponse(new RoomResource($room),'',200);
    }


    /**
     * Update the specified resource in storage.
     */
    /**
     * @OA\Put(
     *     path="/api/cinemas/{cinemaId}/rooms/{roomId}",
     *     operationId="updateRoomByCinema",
     *     tags={"Rooms"},
     *     summary="Update an existing room by cinema",
     *     description="Updates and returns a room by cinema data",
     *     @OA\Parameter(
     *         name="cinemaId",
     *         in="path",
     *         description="ID of cinema room that needs to be updated",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="uuid"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="roomId",
     *         in="path",
     *         description="ID of room that needs to be updated",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="uuid"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Room data",
     *         @OA\JsonContent(
     *             required={"name", "seats"},
     *             @OA\Property(property="name", type="string", example="A New Beginning"),
     *             @OA\Property(property="seats", type="integer", example="10"),
     *             @OA\Property(property="created_at", type="string", format="date", example="2021-09-15"),
     *             @OA\Property(property="updated_at", type="string", format="date", example="2021-09-15")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Room updated",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="string", format="uuid", example="3434-4343-4343-4343"),
     *             @OA\Property(property="name", type="string", example="A New Beginning"),
     *             @OA\Property(property="seats", type="integer", example="10"),
     *             @OA\Property(property="cinema_id", type="string", format="uuid", example="3434-4343-4343-4343"),
     *             @OA\Property(property="created_at", type="string", format="date", example="2021-09-15"),
     *             @OA\Property(property="updated_at", type="string", format="date", example="2021-09-15")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid ID supplied"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Room not found"
     *     ),
     *     @OA\Response(
     *         response=405,
     *         description="Validation exception"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function update(StoreOrUpdateRoomRequest $request, $cinemaId, $id)
    {
        DB::beginTransaction();
        try{
            $cinema = $this->cinemaRepositoryInterface->getById($cinemaId);
            if (!$cinema) {
                return ResponseClass::sendResponse(null, "Cinema not found for cinema : ".$cinemaId, 404);
            }
            $room = $this->roomRepositoryInterface->getById($cinemaId, $id);
            if (!$room) {
                return ResponseClass::sendResponse(null, "Room not found for cinema : ".$cinemaId, 404);
            }

            $data = [
                'name' => $request->input('name'),
                'seats' => $request->input('seats'),
                'cinema_id' => $cinemaId
            ];

            $updated = $this->roomRepositoryInterface->update($data, $cinemaId, $id);

            if ($updated) {
                $responseCode = 200;
                $responseMessage = 'Room updated successfully';
            }  else {
                $responseCode = 422;
                $responseMessage = 'Unable to process the request';
            }
            DB::commit();
            return ResponseClass::sendResponse($updated ?? null,$responseMessage,$responseCode);

        }catch(\Exception $ex){
            return ResponseClass::rollback($ex);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    /**
     * @OA\Delete(
     *     path="/api/cinemas/{cinemaId}/rooms/{roomId}",
     *     operationId="deleteRoomByCinema",
     *     tags={"Rooms"},
     *     summary="Delete a room by cinema",
     *     description="Deletes a room by cinema and returns a success message",
     *     @OA\Parameter(
     *         name="cinemaId",
     *         in="path",
     *         description="ID of cinema to delete room",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="uuid"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="roomId",
     *         in="path",
     *         description="ID of room to delete",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="uuid"
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Room deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Room not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error deleting room"
     *     )
     * )
     */
    public function destroy($cinemaId, $id)
    {
        $cinema = $this->cinemaRepositoryInterface->getById($cinemaId);
        if (!$cinema) {
            return ResponseClass::sendResponse(null, "Cinema not found for cinema : ".$cinemaId, 404);
        }
        $room = $this->roomRepositoryInterface->getById($cinemaId, $id);
        if (!$room) {
            return ResponseClass::sendResponse(null, "Room not found for cinema : ".$cinemaId, 404);
        }
        
        $deleted = $this->roomRepositoryInterface->delete($cinemaId, $id);
        if(!$deleted) {
            return ResponseClass::sendResponse(null, 'Error deleting room', 500);
        }

        return ResponseClass::sendResponse(null,'Room deleted successfully',204);
    }
}
