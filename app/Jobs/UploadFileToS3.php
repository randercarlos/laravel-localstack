<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\File;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use romanzipp\QueueMonitor\Traits\IsMonitored;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

class UploadFileToS3 implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    public function __construct(public string $uploadedFilePath) {}


    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $this->existsUploadedFileInLocal();

            $s3UploadedPath = $this->saveLocalUploadedFileInS3();

            $this->deleteUploadedFileInLocal();

            Log::info("Arquivo local salvo no s3 com sucesso: $s3UploadedPath");
        } catch(\Exception $e) {
            Log::error($e->getMessage());
        }
    }

    private function existsUploadedFileInLocal(): void {
        if (Storage::disk('local')->missing($this->uploadedFilePath)) {
            throw new FileNotFoundException("Arquivo $this->uploadedFilePath não existe localmente.");
        }
    }

    private function saveLocalUploadedFileInS3(): string {
        $uploadedFileFolder = Str::before($this->uploadedFilePath, '/');
        $uploadedFileName = Str::after($this->uploadedFilePath, '/');

        $s3UploadedPath = Storage::disk('s3')->putFileAs(
            $uploadedFileFolder,
            new File(storage_path("app/$this->uploadedFilePath")),
            $uploadedFileName
        );

        if (! $s3UploadedPath) {
            throw new \Exception("Falha ao salvar arquivo local no s3: $s3UploadedPath");
        }

        return $s3UploadedPath;
    }

    private function deleteUploadedFileInLocal(): void {
        if (! Storage::disk('local')->delete($this->uploadedFilePath)) {
            throw new \Exception("Falha ao deletar arquivo de upload local: $this->uploadedFilePath");
        }
    }


}
