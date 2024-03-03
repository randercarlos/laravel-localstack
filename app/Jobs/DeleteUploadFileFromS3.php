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

class DeleteUploadFileFromS3 implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    public function __construct(public string $uploadedFilePath) {}


    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $s3Disk = Storage::disk('s3');
            if (! $s3Disk->exists($this->uploadedFilePath)) {
                throw new FileNotFoundException("Arquivo $this->uploadedFilePath não existe no bucket do S3.");
            }

            if (! $s3Disk->delete($this->uploadedFilePath)) {
                throw new \Exception("Falha ao deletar arquivo de upload do S3: $this->uploadedFilePath");
            }

            Log::info("Arquivo deletado da s3 com sucesso: $this->uploadedFilePath");
        } catch(\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
