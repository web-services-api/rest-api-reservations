<?php

namespace App\Http\Controllers;

use App\Models\Cinema;

use Illuminate\Http\Request;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\CinemaResource;
use App\Http\Requests\StoreOrUpdateCinemaRequest;
use App\Interfaces\CinemaRepositoryInterface;
use App\Classes\ApiResponseClass as ResponseClass;

class CinemaController extends Controller
{
    
    private CinemaRepositoryInterface $cinemaRepositoryInterface;
    
    public function __construct(
        CinemaRepositoryInterface $cinemaRepositoryInterface
    )
    {
        $this->cinemaRepositoryInterface = $cinemaRepositoryInterface;
    }

    /**
     * Displays a list of resources.
    **/
    /**
     * @OA\Get(
     *     path="/api/cinemas",
     *     operationId="getCinemasList",
     *     tags={"Cinemas"},
     *     summary="Gets the list of cinemas",
     *     description="Returns the list of cinemas with optional pagination",
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
     *         description="No cinema(s) found"
     *     ),
     *      @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function index($perPage = 10)
    {
        $perPage = request()->query('perPage', $perPage);

        $data = $this->cinemaRepositoryInterface->index($perPage);

        if ($data->isEmpty()) {
            return ResponseClass::sendResponse([], 'No cinemas found', 204);
        }
        
        return ResponseClass::sendResponse(CinemaResource::collection($data),'',200, true);
    }


    /**
     * Store a newly created resource in storage.
     */
    /**
     * @OA\Post(
     *     path="/api/cinemas",
     *     operationId="storeCinema",
     *     tags={"Cinemas"},
     *     summary="Store a new cinema",
     *     description="Stores a new cinema and returns the cinema data",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Cinema data",
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="A New Beginning")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="string", format="uuid", example="3434-4343-4343-4343"),
     *             @OA\Property(property="name", type="string", example="A New Beginning"),
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
    public function store(StoreOrUpdateCinemaRequest $request)
    {
        DB::beginTransaction();
        try{
            $data = [
                'name' => $request->input('name')
            ];

            $cinema = $this->cinemaRepositoryInterface->store($data);

            DB::commit();
            return ResponseClass::sendResponse(new CinemaResource($cinema),'Cinema created successfully',201);

        }catch(\Exception $ex){
            return ResponseClass::rollback($ex);
        }
    }

    /**
     * Display the specified resource.
     */
    /**
     * @OA\Get(
     *     path="/api/cinemas/{id}",
     *     operationId="getCinemaById",
     *     tags={"Cinemas"},
     *     summary="Get cinema by ID",
     *     description="Returns a single cinema",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of cinema to return",
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
    *              @OA\Property(property="created_at", type="string", format="date", example="2021-09-15"),
    *              @OA\Property(property="updated_at", type="string", format="date", example="2021-09-15")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Cinema not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function show($id)
    {
        $cinema = $this->cinemaRepositoryInterface->getById($id);
        if (!$cinema) {
            return ResponseClass::sendResponse(null, "Cinema not found", 404);
        }

        return ResponseClass::sendResponse(new CinemaResource($cinema),'',200);
    }


    /**
     * Update the specified resource in storage.
     */
    /**
     * @OA\Put(
     *     path="/api/cinemas/{id}",
     *     operationId="updateCinema",
     *     tags={"Cinemas"},
     *     summary="Update an existing cinema",
     *     description="Updates and returns a cinema data",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of cinema that needs to be updated",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="uuid"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Cinema data",
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="A New Beginning"),
     *              @OA\Property(property="created_at", type="string", format="date", example="2021-09-15"),
     *              @OA\Property(property="updated_at", type="string", format="date", example="2021-09-15")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cinema updated",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="string", format="uuid", example="3434-4343-4343-4343"),
     *             @OA\Property(property="name", type="string", example="A New Beginning"),
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
     *         description="Cinema not found"
     *     ),
     *     @OA\Response(
     *         response=405,
     *         description="Validation exception"
     *     )
     * )
     */
    public function update(StoreOrUpdateCinemaRequest $request, $id)
    {
        DB::beginTransaction();
        try{
            $cinema = $this->cinemaRepositoryInterface->getById($id);
            if (!$cinema) {
                return ResponseClass::sendResponse(null, "Cinema not found", 404);
            }

            $data = [
                'name' => $request->input('name')
            ];

            $updated = $this->cinemaRepositoryInterface->update($data,$id);

            if ($updated) {
                $responseCode = 200;
                $responseMessage = 'Cinema updated successfully';
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
     *     path="/api/cinemas/{id}",
     *     operationId="deleteCinema",
     *     tags={"Cinemas"},
     *     summary="Delete a cinema",
     *     description="Deletes a cinema and returns a success message",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of cinema to delete",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="uuid"
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Cinema deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Cinema not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error deleting movie"
     *     )
     * )
     */
    public function destroy($id)
    {
        $cinema = $this->cinemaRepositoryInterface->getById($id);
        if (!$cinema) {
            return ResponseClass::sendResponse(null, "Cinema not found", 404);
        }

        $deleted = $this->cinemaRepositoryInterface->delete($id);
        if(!$deleted) {
            return ResponseClass::sendResponse(null, 'Error deleting cinema', 500);
        }

        return ResponseClass::sendResponse(null,'Cinema deleted successfully',204);
    }
}
