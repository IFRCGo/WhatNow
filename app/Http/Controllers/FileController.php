<?php

namespace App\Http\Controllers;

use App\Classes\RcnApi\RcnApiClient;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Files",
 *     description="Operations about Files"
 * )
 */
class FileController extends Controller
{
    protected $rcnApiClient;

    public function __construct(RcnApiClient $rcnApiClient)
    {
        $this->rcnApiClient = $rcnApiClient;
    }

    /**
     * @OA\Post(
     *     path="/upload-file",
     *     tags={"Files"},
     *     summary="Upload a file",
     *     description="Uploads a file to the server",
     *     operationId="uploadFile",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"file"},
     *                 @OA\Property(
     *                     property="file",
     *                     type="string",
     *                     format="binary",
     *                     description="File to be uploaded"
     *                 )
     *             )
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
    public function uploadFile(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:jpg,png|max:10240',
        ]);
        $file = $request->file('file');
        $filePath = $file->getPathname();
        $fileName = $file->getClientOriginalName();

        try {
            $response = $this->rcnApiClient->whatnow()->uploadFile($filePath, $fileName);
            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['error' => 'File upload failed'], 500);
        }
    }
}
