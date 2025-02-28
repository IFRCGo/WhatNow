<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\RoleResource;
use App\Models\Access\Role\Role;
use App\Repositories\Access\Role\RoleRepository;
/**
 * @OA\Tag(
 *     name="Roles",
 *     description="Operations about roles"
 * )
 */
class RoleController extends Controller
{
    
    private $roles;

    
    public function __construct(RoleRepository $roles)
    {
        $this->roles = $roles;
    }

    /**
     * @OA\Get(
     *     path="/roles",
     *     tags={"Roles"},
     *     summary="List all roles",
     *     description="Retrieves a list of all roles",
     *     operationId="listRoles",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", description="Role ID"),
     *                     @OA\Property(property="name", type="string", description="Role name")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function listRoles()
    {
        $this->authorize('list', Role::class);
        return RoleResource::collection($this->roles->getAll());
    }
}
