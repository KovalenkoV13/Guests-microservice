<?php

namespace App\Infrastructure\Http\Controllers;

use App\Domain\Guest\Application\GuestService;
use App\Infrastructure\Http\Requests\StoreGuestRequest;
use App\Infrastructure\Http\Requests\UpdateGuestRequest;
use App\Infrastructure\Http\Resources\GuestResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Http;

class GuestController
{
    private $guestService;

    public function __construct(GuestService $guestService)
    {
        $this->guestService = $guestService;
    }

    /**
     * @OA\Get(
     *     path="/api/v1/guests",
     *     tags={"Guests"},
     *     summary="Получение всех гостей",
     *     @OA\Response(
     *          response=200,
     *          description="Успешно. Вернет список всех гостей с информацией о них",
     *          @OA\JsonContent(
     *              @OA\Property(property="data", type="array",
     *                  @OA\Items(
     *                    @OA\Property(property="id", type="number", example=1),
     *                    @OA\Property(property="first_name", type="string", example="vlad"),
     *                    @OA\Property(property="last_name", type="string", example="kovalenko"),
     *                    @OA\Property(property="phone", type="string", example="+7965749854"),
     *                    @OA\Property(property="email", type="string", example="kovalenko@kovalenko.ru"),
     *                    @OA\Property(property="country", type="string", example="Russia"),
     *                    @OA\Property(property="created_at", type="string", example="2024-08-10T17:55:30.000000Z"),
     *                    @OA\Property(property="updated_at", type="string", example="2024-08-10T17:55:30.000000Z"),
     *                  ),
     *              ),
     *          ),
     *      )
     * )
     */
    public function index(): ResourceCollection
    {
        $guests = $this->guestService->getAllGuests();

        return GuestResource::collection($guests);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/guests/create",
     *     tags={"Guests"},
     *     summary="Добавление нового гостя",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                      type="object",
     *                      @OA\Property(
     *                          property="first_name",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="last_name",
     *                          type="string"
     *                      ),
     *                       @OA\Property(
     *                          property="phone",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="email",
     *                          type="string"
     *                      ),
     *                       @OA\Property(
     *                          property="country",
     *                          type="string"
     *                      )
     *                 ),
     *                 example={
     *                      "first_name": "vlad",
     *                      "last_name": "vlad",
     *                       "phone": "+79657498546",
     *                       "email": "kovalenko@kovalenkoVlad.ru",
     *                       "country": "Russia"
     *                }
     *             )
     *         )
     *      ),
     *     @OA\Response(
     *          response=201,
     *          description="Успешно создано.",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Guest created successfully!"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Ошибка при валидации данных.",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Validation errors"),
     *               @OA\Property(property="errors", type="object",
     *                       @OA\Property(property="first_name", type="array", 
     *                           @OA\Items(
     *                                  @OA\Property(property="required", type="string", example="First name is require"), 
     *                            )
     *                       ),
     *                       @OA\Property(property="email", type="array", 
     *                           @OA\Items(
     *                                  @OA\Property(property="unique", type="string", example="Email is already exist"), 
     *                            )
     *                       ),
     *               )
     *          ),
     *      )
     * )
     */
    public function store(StoreGuestRequest $request)
    {
        $this->guestService->createGuest($request->toArray());

        return response()->json(['message' => 'Guest created successfully!'], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/guests/{id}",
     *     tags={"Guests"},
     *     summary="Получение гостя по идентификатору",
     *      @OA\Parameter(
     *          description="Идентификатор гостя",
     *          in="path",
     *          name="id",
     *          required=true,
     *          example="1",
     *          @OA\Schema(
     *              type="integer",
     *              format="int64"
     *          )
     *      ),
     *     @OA\Response(
     *          response=200,
     *          description="Успешно. Вернет нужного гостя.",
     *          @OA\JsonContent(
     *                    @OA\Property(property="id", type="number", example=1),
     *                    @OA\Property(property="first_name", type="string", example="vlad"),
     *                    @OA\Property(property="last_name", type="string", example="kovalenko"),
     *                    @OA\Property(property="phone", type="string", example="+12495854349"),
     *                    @OA\Property(property="email", type="string", example="kovalenko@kovalenko.ru"),
     *                    @OA\Property(property="country", type="string", example="Canada"),
     *                    @OA\Property(property="created_at", type="string", example="2024-08-10T17:55:30.000000Z"),
     *                    @OA\Property(property="updated_at", type="string", example="2024-08-10T17:55:30.000000Z"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Гость не найден.",
     *          @OA\JsonContent(
     *                    @OA\Property(property="error", type="string", example="Guest does not found!"),
     *          ),
     *      ),
     * )
     */
    public function show(int $id)
    {
        try {
            return $this->guestService->getGuestById($id);
        } catch (ModelNotFoundException $modelNotFound) {
            return response()->json(['error' => 'Guest does not found!'], 404);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/v1/guests/update/{id}",
     *     tags={"Guests"},
     *     summary="Обновление информации о госте",
     *     @OA\Parameter(
     *          description="Идентификатор гостя",
     *          in="path",
     *          name="id",
     *          required=true,
     *          example="1",
     *          @OA\Schema(
     *              type="integer",
     *              format="int64"
     *          )
     *      ),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                      type="object",
     *                      @OA\Property(
     *                          property="first_name",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="last_name",
     *                          type="string"
     *                      ),
     *                       @OA\Property(
     *                          property="phone",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="email",
     *                          type="string"
     *                      ),
     *                       @OA\Property(
     *                          property="country",
     *                          type="string"
     *                      )
     *                 ),
     *                 example={
     *                      "first_name": "vlad",
     *                      "last_name": "kovalenko",
     *                       "phone": "+7965749854",
     *                       "email": "kovalenko@kovalenkoVlad.ru",
     *                       "country": "Russia"
     *                }
     *             )
     *         )
     *      ),
     *     @OA\Response(
     *          response=200,
     *          description="Успешно обновлено.",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Guest 1 updated successfully!"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Ошибка при валидации данных.",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Validation errors"),
     *               @OA\Property(property="errors", type="object",
     *                       @OA\Property(property="phone", type="array", 
     *                           @OA\Items(
     *                                  @OA\Property(property="phone", type="string", example="Phone is invalid format"), 
     *                            )
     *                       ),
     *                       @OA\Property(property="email", type="array", 
     *                           @OA\Items(
     *                                  @OA\Property(property="unique", type="string", example="Email is already exist"), 
     *                            )
     *                       ),
     *               )
     *          ),
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Гость не найден.",
     *          @OA\JsonContent(
     *                    @OA\Property(property="error", type="string", example="Guest does not found!"),
     *          ),
     *      ),
     * )
     */
    public function update(UpdateGuestRequest $request, int $id)
    {
        try {
            $this->guestService->updateGuestInfo($id, $request->toArray());

            return response()->json(['message' => "Guest {$id} updated successfully!"], 200);
        } catch (ModelNotFoundException $modelNotFound) {
            return response()->json(['error' => 'Guest does not found!'], 404);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/guests/delete/{id}",
     *     tags={"Guests"},
     *     summary="Удаление гостя по идентификатору",
     *      @OA\Parameter(
     *          description="Идентификатор гостя",
     *          in="path",
     *          name="id",
     *          required=true,
     *          example="1",
     *          @OA\Schema(
     *              type="integer",
     *              format="int64"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Успешно удалено.",
     *          @OA\JsonContent(
     *                    @OA\Property(property="message", type="string", example="Guest 1 deleted successfully!"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Гость не найден.",
     *          @OA\JsonContent(
     *                    @OA\Property(property="error", type="string", example="Guest does not found!"),
     *          ),
     *      ),
     * )
     */
    public function destroy(int $id)
    {
        try {
            $this->guestService->removeGuest($id);

            return response()->json(['message' => "Guest {$id} deleted successfully!"], 200);
        } catch (ModelNotFoundException $modelNotFound) {
            return response()->json(['error' => 'Guest does not found!'], 404);
        }
    }
}
