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

final class TermsController extends ApiController
{
    
    private $terms;

    
    private $users;

    
    public function __construct(TermsRepositoryInterface $terms, UserRepository $users)
    {
        $this->terms = $terms;
        $this->users = $users;
    }

    
    public function latest()
    {
        return TermsResource::make($this->terms->getLatest())->response();
    }

    
    public function list()
    {
        $terms = $this->terms->getOrderedByLatest();

        return TermsResource::collection($terms->paginate())->response();
    }

    
    public function listAll()
    {
        return TermsResource::collection($this->terms->getOrderedByLatest()->get());
    }

    
    public function create(Request $request): JsonResponse
    {
        $this->authorize('update', Terms::class);

        $this->validate($request, [
            'version' => 'required|numeric|unique:terms,version',
            'content' => 'required|max:65535',
        ]);

        $terms = $this->terms->create((string) $request->get('version'), $request->get('content'), auth()->user()->id);

        $users = $this->users->allNotifiablePublicUsers();
        Notification::send($users, new TermsUpdatedNotification());

        return TermsResource::make($terms)->response()->setStatusCode(JsonResponse::HTTP_CREATED);
    }
}
