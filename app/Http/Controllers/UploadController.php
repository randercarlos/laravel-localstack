<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeleteUploadFileRequest;
use App\Jobs\DeleteUploadFileFromS3;
use App\Jobs\UploadFileToS3;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UploadController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        try {
            $uploadedFilePath = $this->uploadFile($request->file('file'));
            UploadFileToS3::dispatch($uploadedFilePath);

            return response()->json(['message' => 'Image upload job dispatched.'], Response::HTTP_ACCEPTED);
        } catch(\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Fail on dispatch image upload job.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function uploadFile(UploadedFile $uploadedFile): string {
        return $uploadedFile->store('uploads', 'local');
    }

    public function form() {
        $savedUploads = Storage::disk('s3')->files('uploads');
        $savedUploadsUrls = collect($savedUploads)
            ->map(function ($savedUploadsUrl) {
                return (object) [
                    'name' => Str::of($savedUploadsUrl)->after('/'),
                    'url' => Str::of(Storage::disk('s3')->url($savedUploadsUrl))->replaceFirst('localstack', 'localhost'),
                    'hash' => Crypt::encryptString($savedUploadsUrl)
                ];
            })
            ->toArray();

        return view('upload', compact('savedUploadsUrls'));
    }

    public function upload(Request $request)
    {
        session()->start();
        try {
            $uploadedFilePath = $this->uploadFile($request->file('file'));
            UploadFileToS3::dispatch($uploadedFilePath);

            Log::info("Upload job dispatched: $uploadedFilePath");
            session()->flash('info', 'Upload job was dispatched.');
        } catch(\Exception $e) {
            Log::error($e->getMessage());
            session()->flash('error', 'Fail on dispatch upload job.');
        } finally {
            return redirect()->back();
        }
    }

    public function deleteUpload(string $encryptedFilename )
    {
        try {
            $uploadedFilePath = Crypt::decryptString($encryptedFilename);
            DeleteUploadFileFromS3::dispatch($uploadedFilePath);

            Log::info("Delete upload job dispatched: $uploadedFilePath");
            session()->flash('success', 'Delete upload job dispatched.');
        } catch(\Exception $e) {
            Log::error($e->getMessage());
            session()->flash('error', 'Fail on dispatch delete upload job.');
        } finally {
            return redirect()->back();
        }
    }
}
