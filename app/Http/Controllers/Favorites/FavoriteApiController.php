<?php

namespace App\Http\Controllers\Favorites;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PropertiesFavorites;
use App\Models\DevelopmentsFavorites;
use App\Models\DevelopmentsHorizontalFavorites;
use App\Models\LotsFavorites;
use App\Models\ApartmentsFavorites;
use App\Models\TerrainsFavorites;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Models\ListsUser;
use App\Models\Properties;
use App\Models\Developments;
use App\Models\Lots;
use App\Models\Apartments;
use App\Models\Terrains;
use App\Models\DevelopmentsHorizontals;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class FavoriteApiController extends Controller
{
    public function listsUser($id): Collection
    {
        return User::find($id)?->listUser ?? collect();
    }

    public function createList(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'id_user' => 'required|integer|exists:app_users,id',
                'title' => 'required|string|max:255'
            ]);

            $list = ListsUser::create($validated);

            return response()->json([
                'status' => 'success',
                'data' => $list
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'errors' => $e->validator->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al crear la lista'
            ], 500);
        }
    }

    public function deleteList($id_user, $id_list)
    {
        try {
            DB::transaction(function () use ($id_user, $id_list) {
                $list = User::findOrFail($id_user)
                    ->listUser()
                    ->findOrFail($id_list);

                $list->delete();

                collect([
                    PropertiesFavorites::class,
                    DevelopmentsFavorites::class,
                    DevelopmentsHorizontalFavorites::class,
                    LotsFavorites::class,
                    ApartmentsFavorites::class,
                    TerrainsFavorites::class,
                ])->each(fn($model) => $model::where([
                    'id_user' => $id_user,
                    'id_list' => $id_list
                ])->delete());
            });

            return response()->json(['status' => 'success']);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Recurso no encontrado'
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * DataFromList
     * */

    public function dataFromList($id): JsonResponse
    {
        try {
            $list = ListsUser::with('user:id,name')
                ->findOrFail($id);

            $entities = $this->getEntityConfig();

            $result = [
                'listdata' => $this->formatListData($list),
                'entities' => $this->fetchEntitiesData($id, $entities)
            ];

            return response()->json($result);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Recurso no encontrado'], 404);
        }
    }

    protected function getEntityConfig(): array
    {
        return [
            'properties' => [
                'model' => Properties::class,
                'relation' => PropertiesFavorites::class,
                'foreign_key' => 'id_property',
                'columns' => ['id', 'title', 'price', 'location', 'rooms', 'parkings', 'bathrooms', 'area', 'description', 'views', 'images']
            ],
            'developments' => [
                'model' => Developments::class,
                'relation' => DevelopmentsFavorites::class,
                'foreign_key' => 'id_development',
                'columns' => ['id', 'status', 'title', 'price_min', 'price_max', 'location', 'description', 'commission_percentage', 'mode', 'views', 'images']
            ],
            'developmentsHorizontal' => [
                'model' => DevelopmentsHorizontals::class,
                'relation' => DevelopmentsHorizontalFavorites::class,
                'foreign_key' => 'id_development',
                'columns' => ['id', 'status', 'title', 'price_min', 'price_max', 'location', 'description', 'commission_percentage', 'mode', 'views', 'images']
            ],
            'lots' => [
                'model' => Lots::class,
                'relation' => LotsFavorites::class,
                'foreign_key' => 'id_lot',
                'columns' => ['id', 'title', 'status', 'type_lots', 'price_min', 'price_max', 'location', 'slope', 'description', 'lots_min', 'lots_max', 'type_terrain', 'initial_fee', 'price_mt2', 'commission_percentage', 'images']
            ],
            'apartments' => [
                'model' => Apartments::class,
                'relation' => ApartmentsFavorites::class,
                'foreign_key' => 'id_property',
                'columns' => ['id', 'title', 'price', 'location', 'rooms', 'dev_type', 'parkings', 'bathrooms', 'area', 'description', 'views', 'images']
            ],
            'terrains' => [
                'model' => Terrains::class,
                'relation' => TerrainsFavorites::class,
                'foreign_key' => 'id_property',
                'columns' => ['id', 'title', 'price', 'location', 'parkings', 'area_terrain', 'description', 'services', 'images']
            ]
        ];
    }

    protected function formatListData(ListsUser $list): array
    {
        return $list->only('id', 'id_user', 'title', 'timestamp') + [
            'name_user' => $list->user->name
        ];
    }

    protected function fetchEntitiesData(int $listId, array $entitiesConfig): array
    {
        return collect($entitiesConfig)->mapWithKeys(function ($config, $key) use ($listId) {
            $ids = $config['relation']::where('id_list', $listId)
                ->pluck($config['foreign_key']);

            $data = $config['model']::select($config['columns'])
                ->whereIntegerInRaw('id', $ids)
                ->get();

            return [$key => $data];
        })->all();
    }

    /**
     * SaveFavoriteItem
     * */

    public function saveFavoriteItem(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'type' => 'required|string|in:property,development,developmentHorizontal,lot,apartment,terrain',
                'id_user' => 'required|integer|exists:app_users,id',
                'id_property' => 'required|integer',
                'id_list' => 'required|integer|exists:list_favorites_list,id'
            ]);

            $modelConfig = $this->getFavoriteModelConfig($validated['type']);
            $foreignKey = $modelConfig['foreign_key'];

            $this->validateRelatedResourceExists(
                $modelConfig['model']::getRelatedModelClass(),
                $validated['id_property']
            );

            $favorite = $modelConfig['model']::create([
                'id_user' => $validated['id_user'],
                $foreignKey => $validated['id_property'],
                'id_list' => $validated['id_list']
            ]);

            return response()->json([
                'message' => 'success',
                'data' => $favorite
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Parámetros inválidos',
                'details' => $e->errors()
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error del servidor',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    protected function validateRelatedResourceExists(string $modelClass, int $id): void
    {
        throw_unless(
            $modelClass::where('id', $id)->exists(),
            ModelNotFoundException::class,
            'El recurso especificado no existe'
        );
    }

    protected function getFavoriteModelConfig(string $type): array
    {
        return match ($type) {
            'property' => [
                'model' => PropertiesFavorites::class,
                'foreign_key' => 'id_property'
            ],
            'development' => [
                'model' => DevelopmentsFavorites::class,
                'foreign_key' => 'id_development'
            ],
            'developmentHorizontal' => [
                'model' => DevelopmentsHorizontalFavorites::class,
                'foreign_key' => 'id_development'
            ],
            'lot' => [
                'model' => LotsFavorites::class,
                'foreign_key' => 'id_lot'
            ],
            'apartment' => [
                'model' => ApartmentsFavorites::class,
                'foreign_key' => 'id_property'
            ],
            'terrain' => [
                'model' => TerrainsFavorites::class,
                'foreign_key' => 'id_property'
            ],
            default => throw new \InvalidArgumentException('Invalid property type')
        };
    }

    /**
     * DeleteFavoriteItem
     * */

    public function deleteFavoriteItem(Request $request, string $type_property, int $id_list, int $id_property): JsonResponse
    {
        try {
            $request->validate([
                'type_property' => 'required|in:property,development,developmentHorizontal,lot,apartment,terrain'
            ]);

            $modelConfig = $this->getFavoriteModelConfig($type_property);

            $deleted = $modelConfig['model']::where([
                'id_list' => $id_list,
                $modelConfig['foreign_key'] => $id_property
            ])->delete();

            return $deleted
                ? response()->json(['message' => 'success'])
                : response()->json(['error' => 'Favorite not found'], 404);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Invalid parameters',
                'details' => $e->errors()
            ], 400);
        }
    }
}
