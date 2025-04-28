<?php

namespace App\Http\Controllers\Terms;

use App\Http\Controllers\ApiController;
use App\Http\Resources\Terms\TermsResource;
use App\Models\Terms;
use App\Notifications\Terms\TermsUpdatedNotification;
use App\Repositories\Access\UserRepository;
use App\Repositories\TermsRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use App\Classes\MailApi\MailApiService;
/**
 * @OA\Tag(
 *     name="Terms",
 *     description="Operations about Terms"
 * )
 */
final class TermsController extends ApiController
{
    
    private $terms;

    
    private $users;

    private $mailApiService;

    
    public function __construct(TermsRepositoryInterface $terms, UserRepository $users, MailApiService $mailApiService)
    {
        $this->terms = $terms;
        $this->users = $users;
        $this->mailApiService = $mailApiService;
    }

    /**
     * @OA\Get(
     *     path="/terms/latest",
     *     tags={"Terms"},
     *     summary="Get the latest terms",
     *     description="Returns the latest terms and conditions",
     *     operationId="getLatestTerms",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(type="object")
     *             )
     *         )
     *     )
     * )
     */
    public function latest()
    {
        return TermsResource::make($this->terms->getLatest())->response();
    }

    
    /**
     * @OA\Get(
     *     path="/terms",
     *     tags={"Terms"},
     *     summary="List all terms",
     *     description="Retrieves a paginated list of all terms ordered by the latest version.",
     *     operationId="listTerms",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function list()
    {
        $terms = $this->terms->getOrderedByLatest();

        return TermsResource::collection($terms->paginate())->response();
    }

    
    /**
     * @OA\Get(
     *     path="/terms/all",
     *     tags={"Terms"},
     *     summary="List all terms",
     *     description="Retrieves a list of all terms",
     *     operationId="listAllTerms",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function listAll()
    {
        return TermsResource::collection($this->terms->getOrderedByLatest()->get());
    }

    
    /**
     * @OA\Post(
     *     path="/terms",
     *     tags={"Terms"},
     *     summary="Create new terms",
     *     description="Creates a new version of terms and conditions",
     *     operationId="createTerms",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"version", "content"},
     *             @OA\Property(property="version", type="number", description="Version number of the terms", example=1.0),
     *             @OA\Property(property="content", type="string", maxLength=65535, description="Content of the terms")
     *         )
     *     ),
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function create(Request $request): JsonResponse
    {
        $this->authorize('update', Terms::class);

        $this->validate($request, [
            'version' => 'required|numeric|unique:terms,version',
            'content' => 'required|max:65535',
        ]);

        $terms = $this->terms->create((string) $request->get('version'), $request->get('content'), auth()->user()->id);

        $users = $this->users->allNotifiablePublicUsers();
        $emails = $users->pluck('email')->implode(',');
        $route = route('login');
        $subject = trans('terms.email.subject', ['app_name' => config('app.name')]);
        $html = $this->mailApiService->buildTermsAndConditionsTemplate($route);

        $this->mailApiService->sendMail(
            $emails,
            $subject,
            $html,
        );


        return TermsResource::make($terms)->response()->setStatusCode(JsonResponse::HTTP_CREATED);
    }
}
