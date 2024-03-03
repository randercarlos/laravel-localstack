<?php

namespace App\Providers;

use App\Jobs\DeleteUploadFileFromS3;
use App\Jobs\UploadFileToS3;
use App\Models\User;
use App\Notifications\FileDeletedInS3;
use App\Notifications\FileSavedInS3;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Queue::after(function (JobProcessed $event) {

            $job = $event->job;
            Log::info('Job processed successfully', [
                'job_name' => $job->getName(),
                'job_id' => $job->getJobId(),
                'queue' => $job->getQueue(),
                'payload' => $job->payload(),
                'rawBody' => $job->getRawBody(),
            ]);

            if ($job->resolveName() === UploadFileToS3::class) {
                $obj = unserialize($job->payload()['data']['command']);
                $uploadedFilePath = $obj?->uploadedFilePath ?? 'uploads/fake_file.pdf';

                $user = User::first();
                $user->notify(new FileSavedInS3($uploadedFilePath));
            }

            if ($job->resolveName() === DeleteUploadFileFromS3::class) {
                $obj = unserialize($job->payload()['data']['command']);
                $uploadedFilePath = $obj?->uploadedFilePath ?? 'uploads/fake_file.pdf';

                $user = User::first();
                $user->notify(new FileDeletedInS3($uploadedFilePath));
            }
        });


        Queue::failing(function (JobFailed $event) {
            Log::info('Fail on process Job', [
                'job_name' => $event->job->getName(),
                'job_id' => $event->job->getJobId(),
                'queue' => $event->job->getQueue(),
                'payload' => $event->job->payload(),
            ]);
        });
    }
}
