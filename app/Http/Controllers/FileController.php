<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

use App\Classes\RcnApi\RcnApiClient;
use Illuminate\Http\Request;

#[OA\Tag(
    name: 'Files',
    description: 'Operations about Files',
)]
class FileController extends Controller
{
    protected $rcnApiClient;

    public function __construct(RcnApiClient $rcnApiClient)
    {
        $this->rcnApiClient = $rcnApiClient;
    }

    #[OA\Post(
        path: '/upload-file',
        tags: ['Files'],
        summary: 'Upload a file',
        description: 'Uploads a file to the server',
        operationId: 'uploadFile',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: [new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    required: ['file'],
                    properties: [new OA\Property(
                        property: 'file',
                        type: 'string',
                        format: 'binary',
                        description: 'File to be uploaded',
                    )],
                ),
            )],
        ),
        responses: [new OA\Response(
            response: 200,
            description: 'Successful response',
            content: new OA\JsonContent(
                type: 'object',
                properties: [new OA\Property(
                    property: 'data',
                    type: 'array',
                    items: new OA\Items(
                        type: 'object',
                    ),
                )],
            ),
        )],
    )]
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
