<?php

namespace App\Http\Controllers;

use App\Models\Seance;

use Illuminate\Http\Request;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use App\Http\Services\MovieService;
use App\Http\Resources\SeanceResource;
use App\Interfaces\CinemaRepositoryInterface;
use App\Interfaces\SeanceRepositoryInterface;
use App\Http\Requests\StoreOrUpdateSeanceRequest;
use App\Classes\ApiResponseClass as ResponseClass;

/**
 * @OA\Info(title="Seances - API", version="1.0")
 */ 
class SeanceController extends Controller
{

    private CinemaRepositoryInterface $cinemaRepositoryInterface;

    private SeanceRepositoryInterface $seanceRepositoryInterface;

    protected $movieService;
    
    public function __construct(
        CinemaRepositoryInterface $cinemaRepositoryInterface,
        SeanceRepositoryInterface $seanceRepositoryInterface,
        MovieService $movieService
    )
    {
        $this->cinemaRepositoryInterface = $cinemaRepositoryInterface;
        $this->seanceRepositoryInterface = $seanceRepositoryInterface;
        $this->movieService = $movieService;
    }

    /**
     * Displays a list of resources.
    **/
    /**
     * @OA\Get(
     *     path="/api/cinemas/{cinemaId}/rooms/{roomId}/seances",
     *     operationId="getSeancesListByRoom",
     *     tags={"Seances"},
     *     summary="Gets the list of seances by room",
     *     description="Returns the list of seances by room with optional pagination",
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
     *      @OA\Parameter(
     *         name="roomId",
     *         in="path",
     *         description="ID of room to return seances",
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
     *         description="No seance(s) found"
     *     ),
     *      @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function index($cinemaId, $roomId, $perPage = 10)
    {
        $perPage = request()->query('perPage', $perPage);

        $cinema = $this->cinemaRepositoryInterface->getById($cinemaId);
        if (!$cinema) {
            return ResponseClass::sendResponse(null, "Cinema not found for cinema : ".$cinemaId, 404);
        }
        $room = $cinema->rooms->where('id', $roomId)->first();
        if (!$room) {
            return ResponseClass::sendResponse(null, "Room not found for cinema : ".$cinemaId, 404);
        }

        $data = $this->seanceRepositoryInterface->index($roomId, $perPage);

        if ($data->isEmpty()) {
            return ResponseClass::sendResponse([], "No seance(s) found for this room : ".$roomId, 204);
        }
        
        return ResponseClass::sendResponse(SeanceResource::collection($data),'',200, true);
    }


    /**
     * Store a newly created resource in storage.
     */
    /**
     * @OA\Post(
     *     path="/api/cinemas/{cinemaId}/rooms/{roomId}/seances",
     *     operationId="storeSeanceByRoom",
     *     tags={"Seances"},
     *     summary="Store a new seance by room",
     *     description="Stores a new seance by room and returns the seance data",
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
     *     @OA\Parameter(
     *         name="roomId",
     *         in="path",
     *         description="ID of room to store seance",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="uuid"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Seance data",
     *         @OA\JsonContent(
     *             required={"movie_id", "start_date"},
     *             @OA\Property(property="movie_id", type="string", format="uuid", example="3434-4343-4343-4343"),
     *             @OA\Property(property="start_date", type="string", format="date", example="2021-09-15")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="string", format="uuid", example="3434-4343-4343-4343"),
     *             @OA\Property(property="movie_id", type="string", format="uuid", example="3434-4343-4343-4343"),
     *             @OA\Property(property="start_date", type="string", format="date", example="2021-09-15"),
     *             @OA\Property(property="room_id", type="string", format="uuid", example="3434-4343-4343-4343"),
     *             @OA\Property(property="created_at", type="string", format="date", example="2021-09-15"),
     *             @OA\Property(property="updated_at", type="string", format="date", example="2021-09-15")
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
    public function store(StoreOrUpdateSeanceRequest $request, $cinemaId, $roomId)
    {
        DB::beginTransaction();
        try{

            $cinema = $this->cinemaRepositoryInterface->getById($cinemaId);
            if (!$cinema) {
                return ResponseClass::sendResponse(null, "Cinema not found for cinema : ".$cinemaId, 404);
            }
            $room = $cinema->rooms->where('id', $roomId)->first();
            if (!$room) {
                return ResponseClass::sendResponse(null, "Room not found for cinema : ".$cinemaId, 404);
            }

            $movieId = $request->input('movie_id');

            $movie = $this->movieService->getById($movieId);
            if(!$movie) {
                return ResponseClass::sendResponse(null, "Movie not found with ID : ".$movieId, 404);
            }

            $data = [
                'movie_id' => $request->input('movie_id'),
                'start_date' => $request->input('start_date'),
                'room_id' => $roomId
            ];

            $seance = $this->seanceRepositoryInterface->store($data, $roomId);

            DB::commit();
            return ResponseClass::sendResponse(new SeanceResource($seance),'Seance created successfully for room : '.$roomId,201);

        }catch(\Exception $ex){
            return ResponseClass::rollback($ex);
        }
    }

    /**
     * Display the specified resource.
     */
    /**
     * @OA\Get(
     *     path="/api/cinemas/{cinemaId}/rooms/{roomId}/seances/{seanceId}",
     *     operationId="getSeanceByIdByRoom",
     *     tags={"Seances"},
     *     summary="Get seance by ID by room",
     *     description="Returns a single seance by room",
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
     *     @OA\Parameter(
     *         name="seanceId",
     *         in="path",
     *         description="ID of seance to return",
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
     *             @OA\Property(property="movie_id", type="string", format="uuid", example="3434-4343-4343-4343"),
     *             @OA\Property(property="start_date", type="string", format="date", example="2021-09-15"),
     *             @OA\Property(property="room_id", type="string", format="uuid", example="3434-4343-4343-4343"),
     *             @OA\Property(property="created_at", type="string", format="date", example="2021-09-15"),
     *             @OA\Property(property="updated_at", type="string", format="date", example="2021-09-15")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Seance not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function show($cinemaId, $roomId, $id)
    {
        $cinema = $this->cinemaRepositoryInterface->getById($cinemaId);
        if (!$cinema) {
            return ResponseClass::sendResponse(null, "Cinema not found for cinema : ".$cinemaId, 404);
        }
        $room = $cinema->rooms->where('id', $roomId)->first();
        if (!$room) {
            return ResponseClass::sendResponse(null, "Room not found for cinema : ".$cinemaId, 404);
        }
        $seance = $room->seances->where('id', $id)->first();
        if (!$seance) {
            return ResponseClass::sendResponse(null, "Seance not found for room : ".$roomId, 404);
        }

        return ResponseClass::sendResponse(new SeanceResource($seance),'',200);
    }


    /**
     * Update the specified resource in storage.
     */
    /**
     * @OA\Put(
     *     path="/api/cinemas/{cinemaId}/rooms/{roomId}/seances/{seanceId}",
     *     operationId="updateSeanceByRoom",
     *     tags={"Seances"},
     *     summary="Update an existing seance by room",
     *     description="Updates and returns a seance by room data",
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
     *     @OA\Parameter(
     *         name="seanceId",
     *         in="path",
     *         description="ID of seance that needs to be updated",
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
     *             required={"movie_id", "start_date"},
     *             @OA\Property(property="movie_id", type="string", format="uuid", example="3434-4343-4343-4343"),
     *             @OA\Property(property="start_date", type="string", format="date", example="2021-09-15")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Seance updated",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="string", format="uuid", example="3434-4343-4343-4343"),
     *             @OA\Property(property="movie_id", type="string", format="uuid", example="3434-4343-4343-4343"),
     *             @OA\Property(property="start_date", type="string", format="date", example="2021-09-15"),
     *             @OA\Property(property="room_id", type="string", format="uuid", example="3434-4343-4343-4343"),
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
     *         description="Seance not found"
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
    public function update(StoreOrUpdateSeanceRequest $request, $cinemaId, $roomId, $id)
    {
        DB::beginTransaction();
        try{
            $cinema = $this->cinemaRepositoryInterface->getById($cinemaId);
            if (!$cinema) {
                return ResponseClass::sendResponse(null, "Cinema not found for cinema : ".$cinemaId, 404);
            }
            $room = $cinema->rooms->where('id', $roomId)->first();
            if (!$room) {
                return ResponseClass::sendResponse(null, "Room not found for cinema : ".$cinemaId, 404);
            }
            $seance = $room->seances->where('id', $id)->first();
            if (!$seance) {
                return ResponseClass::sendResponse(null, "Seance not found for room : ".$roomId, 404);
            }

            $data = [
                'movie_id' => $request->input('movie_id'),
                'start_date' => $request->input('start_date'),
                'room_id' => $roomId
            ];

            $updated = $this->seanceRepositoryInterface->update($data, $roomId, $id);

            if ($updated) {
                $responseCode = 200;
                $responseMessage = 'Seance updated successfully';
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
     *     path="/api/cinemas/{cinemaId}/rooms/{roomId}/seances/{seanceId}",
     *     operationId="deleteSeanceByRoom",
     *     tags={"Seances"},
     *     summary="Delete a seance by room",
     *     description="Deletes a seance by room and returns a success message",
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
     *     @OA\Parameter(
     *         name="seanceId",
     *         in="path",
     *         description="ID of seance to delete",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="uuid"
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Seance deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Seance not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error deleting room"
     *     )
     * )
     */
    public function destroy($cinemaId, $roomId, $id)
    {
        $cinema = $this->cinemaRepositoryInterface->getById($cinemaId);
        if (!$cinema) {
            return ResponseClass::sendResponse(null, "Cinema not found for cinema : ".$cinemaId, 404);
        }
        $room = $cinema->rooms->where('id', $roomId)->first();
        if (!$room) {
            return ResponseClass::sendResponse(null, "Room not found for cinema : ".$cinemaId, 404);
        }
        $seance = $room->seances->where('id', $id)->first();
        if (!$seance) {
            return ResponseClass::sendResponse(null, "Seance not found for room : ".$roomId, 404);
        }
        
        $deleted = $this->seanceRepositoryInterface->delete($roomId, $id);
        if(!$deleted) {
            return ResponseClass::sendResponse(null, 'Error deleting seance', 500);
        }

        return ResponseClass::sendResponse(null,'Seance deleted successfully',204);
    }
}
