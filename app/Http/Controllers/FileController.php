<?php

namespace App\Http\Controllers;

use App\Classes\RcnApi\RcnApiClient;
use Illuminate\Http\Request;

class FileController extends Controller
{
    protected $rcnApiClient;

    public function __construct(RcnApiClient $rcnApiClient)
    {
        $this->rcnApiClient = $rcnApiClient;
    }

    public function uploadFile(Request $request)
    {
        $request->validate([
            'file' => 'required|file'
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
