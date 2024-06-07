<?php

namespace App\Http\Controllers;

use Carbon\Carbon;

use App\Models\Seance;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use App\Http\Services\MovieService;
use App\Http\Resources\ReservationResource;
use App\Interfaces\SeanceRepositoryInterface;
use App\Classes\ApiResponseClass as ResponseClass;
use App\Interfaces\ReservationRepositoryInterface;
use App\Http\Requests\StoreOrUpdateReservationRequest;

/**
 * @OA\Info(title="Reservations - API", version="1.0")
 */ 
class ReservationController extends Controller
{

    private ReservationRepositoryInterface $reservationRepositoryInterface;


    private SeanceRepositoryInterface $seanceRepositoryInterface;

    private MovieService $movieService;

    public function __construct(
        ReservationRepositoryInterface $reservationRepositoryInterface,
        SeanceRepositoryInterface $seanceRepositoryInterface,
        MovieService $movieService
    )
    {
        $this->reservationRepositoryInterface = $reservationRepositoryInterface;
        $this->seanceRepositoryInterface = $seanceRepositoryInterface;
        $this->movieService = $movieService;
    }

    /**
     * Displays a list of resources.
    **/
    /**
     * @OA\Get(
     *     path="/api/movie/{movieId}/reservations",
     *     operationId="getReservationsListByMovie",
     *     tags={"Reservations"},
     *     summary="Gets the list of reservations by movie",
     *     description="Returns the list of reservations by movie with optional pagination",
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
     *         name="movieId",
     *         in="path",
     *         description="ID of movie to return reservations",
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
     *         description="No reservations found"
     *     ),
     *      @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function index($movieId, $perPage = 10)
    {
        $perPage = request()->query('perPage', $perPage);

        $movie = $this->movieService->getById($movieId);
        if (!$movie) {
            return ResponseClass::sendResponse(null, "Movie not found for movie : ".$movieId, 404);
        }

        $reservations = $this->reservationRepositoryInterface->index($movieId, $perPage);
        if ($reservations->isEmpty()) {
            return ResponseClass::sendResponse([], "No reservations found for this movie : ".$movieId, 204);
        }
        
        return ResponseClass::sendResponse(ReservationResource::collection($reservations),'',200, true);
    }


    /**
     * Store a newly created resource in storage.
     */
    /**
     * @OA\Post(
     *     path="/api/movie/{movieId}/reservations",
     *     operationId="storeReservationByMovie",
     *     tags={"Reservations"},
     *     summary="Store a new reservation by movie",
     *     description="Stores a new reservation by movie and returns the reservation data",
     *     @OA\Parameter(
     *         name="movieId",
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
     *         description="Reservation data",
     *         @OA\JsonContent(
     *             required={"seance_id", "seat"},
     *             @OA\Property(property="seance_id", type="string", format="uuid", example="3434-4343-4343-4343"),
     *             @OA\Property(property="seat", type="string", format="date", example="2021-09-15")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="string", format="uuid", example="3434-4343-4343-4343"),
     *             @OA\Property(property="seance_id", type="string", format="uuid", example="3434-4343-4343-4343"),
     *             @OA\Property(property="seat", type="int", format="int", example="34"),
     *             @OA\Property(property="rank", type="int", format="int", example="3"),
     *             @OA\Property(property="status", type="string", format="string", example="open"),
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
    public function store(StoreOrUpdateReservationRequest $request, $movieId)
    {
        DB::beginTransaction();
        try{

            $movie = $this->movieService->getById($movieId);
            if (!$movie) {
                return ResponseClass::sendResponse(null, "Movie not found for movie : ".$movieId, 404);
            }

            $data = [
                'seance_id' => $request->input('seance_id'),
                'seat' => $request->input('seat')
            ];

            $reservation = $this->reservationRepositoryInterface->store($data);

            DB::commit();
            return ResponseClass::sendResponse(new ReservationResource($reservation),'Reservation created successfully for movie : '.$movieId,201);

        }catch(\Exception $ex){
            return ResponseClass::rollback($ex);
        }
    }

    /**
     * Display the specified resource.
     */
    /**
     * @OA\Get(
     *     path="/api/reservations/{reservationId}",
     *     operationId="getReservationById",
     *     tags={"Reservations"},
     *     summary="Get reservation by ID",
     *     description="Returns a single reservation",
     *     @OA\Parameter(
     *         name="reservationId",
     *         in="path",
     *         description="ID of reservation to return",
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
     *             @OA\Property(property="seance_id", type="string", format="uuid", example="3434-4343-4343-4343"),
     *             @OA\Property(property="seat", type="int", format="int", example="34"),
     *             @OA\Property(property="rank", type="int", format="int", example="3"),
     *             @OA\Property(property="status", type="string", format="string", example="open"),
     *             @OA\Property(property="created_at", type="string", format="date", example="2021-09-15"),
     *             @OA\Property(property="updated_at", type="string", format="date", example="2021-09-15")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Reservation not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function show($id)
    {
        $reservation = $this->reservationRepositoryInterface->getById($id);
        if (!$reservation) {
            return ResponseClass::sendResponse(null, "Reservation not found for reservation : ".$id, 404);
        }

        return ResponseClass::sendResponse(new ReservationResource($reservation),'',200);
    }


    /**
     * Update the specified resource in storage.
     */
    /**
     * @OA\Put(
     *     path="/api/reservations/{reservationId}",
     *     operationId="updateReservation",
     *     tags={"Reservations"},
     *     summary="Update an existing reservation",
     *     description="Updates and returns a reservation data",
     *     @OA\Parameter(
     *         name="reservationId",
     *         in="path",
     *         description="ID of reservation that needs to be updated",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="uuid"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Reservation data",
     *         @OA\JsonContent(
     *             required={"seance_id", "seat", "rank", "status"},
     *             @OA\Property(property="seance_id", type="string", format="uuid", example="3434-4343-4343-4343"),
     *             @OA\Property(property="seat", type="int", format="int", example="34"),
     *             @OA\Property(property="rank", type="int", format="int", example="3"),
     *             @OA\Property(property="status", type="string", format="string", example="open")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Reservation updated",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="string", format="uuid", example="3434-4343-4343-4343"),
     *             @OA\Property(property="seance_id", type="string", format="uuid", example="3434-4343-4343-4343"),
     *             @OA\Property(property="seat", type="int", format="int", example="34"),
     *             @OA\Property(property="rank", type="int", format="int", example="3"),
     *             @OA\Property(property="status", type="string", format="string", example="open"),
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
     *         description="Reservation not found"
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
    public function update(StoreOrUpdateReservationRequest $request, $id)
    {
        DB::beginTransaction();
        try{
            $reservation = $this->reservationRepositoryInterface->getById($id);
            if (!$reservation) {
                return ResponseClass::sendResponse(null, "Reservation not found for reservation : ".$id, 404);
            }

            $seanceId = $request->input('seance_id');

            $seance = $this->seanceRepositoryInterface->getById(null, $seanceId);
            if(!$seance) {
                return ResponseClass::sendResponse(null, "Seance not found with ID : ".$seanceId, 404);
            }

            $data = [
                'seance_id' => $seanceId,
                'seat' => $request->input('seat')
            ];


            $updated = $this->reservationRepositoryInterface->update($data, $id);

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
     * Update the specified resource in storage.
     */
    /**
     * @OA\Put(
     *     path="/api/reservations/{reservationId}/confirm",
     *     operationId="confirmReservation",
     *     tags={"Reservations"},
     *     summary="Confirm an existing reservation",
     *     description="Confirms and returns a reservation data",
     *     @OA\Parameter(
     *         name="reservationId",
     *         in="path",
     *         description="ID of reservation that needs to be confirmed",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="uuid"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Reservation confirmed",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="string", format="uuid", example="3434-4343-4343-4343"),
     *             @OA\Property(property="seance_id", type="string", format="uuid", example="3434-4343-4343-4343"),
     *             @OA\Property(property="seat", type="int", format="int", example="34"),
     *             @OA\Property(property="rank", type="int", format="int", example="3"),
     *             @OA\Property(property="status", type="string", format="string", example="open"),
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
     *         description="Reservation not found"
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
    public function confirm($id)
    {
        DB::beginTransaction();
        try{
            $reservation = $this->reservationRepositoryInterface->getById($id);
            if (!$reservation) {
                return ResponseClass::sendResponse(null, "Reservation not found for reservation : ".$id, 404);
            }

            if($reservation->status == 'confirmed') {
                return ResponseClass::sendResponse(null, 'Reservation has been confirmed', 400);
            }
            if($reservation->status == 'expired') {
                return ResponseClass::sendResponse(null, 'Reservation has been expired', 400);
            }

            $currentTime = Carbon::now();
            $createdAt = Carbon::parse($reservation->created_at);
            $minuteDiff = $currentTime->diffInMinutes($createdAt);

            if(intval($minuteDiff) < -intval(env('RESERVATION_EXPIRATION_MINUTES') ?? 20)){
                $reservation->status = 'expired';
                $expired = $reservation->save();
            } else {
                $updated = $this->reservationRepositoryInterface->confirm($id);
            }

            if(isset($expired) && $expired) {
                $responseCode = 400;
                $responseMessage = 'Reservation has been expired';
            } else if(isset($updated) && $updated) {
                $responseCode = 200;
                $responseMessage = 'Reservation confirmed successfully';
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
     *     path="/api/reservations/{reservationId}",
     *     operationId="deleteReservationById",
     *     tags={"Reservations"},
     *     summary="Delete a reservation",
     *     description="Deletes a reservation and returns a success message",
     *     @OA\Parameter(
     *         name="reservationId",
     *         in="path",
     *         description="ID of reservation to delete",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="uuid"
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Reservation deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Reservation not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error deleting room"
     *     )
     * )
     */
    public function destroy($id)
    {
        $reservation = $this->reservationRepositoryInterface->getById($id);
        if (!$reservation) {
            return ResponseClass::sendResponse(null, "Reservation not found for reservation : ".$id, 404);
        }

        $deleted = $this->reservationRepositoryInterface->delete($id);
        if(!$deleted) {
            return ResponseClass::sendResponse(null, 'Error deleting reservation', 500);
        }

        return ResponseClass::sendResponse(null,'Reservation deleted successfully',204);
    }
}
