<?php


namespace App\Concerns;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Session;
use Spatie\Backup\BackupDestination\Backup;
use Spatie\Backup\BackupDestination\BackupDestination;
use Spatie\Backup\Config\MonitoredBackupsConfig;
use Spatie\Backup\Helpers\Format;
use Spatie\Backup\Tasks\Monitor\BackupDestinationStatus;
use Spatie\Backup\Tasks\Monitor\BackupDestinationStatusFactory;
use Symfony\Component\HttpFoundation\StreamedResponse;


trait HasBackups
{
    public function getFiles(string $disk = 'local')
    {
        $backupDestination = BackupDestination::create($disk, config('backup.backup.name'));

        return $backupDestination
            ->backups()
            ->map(function (Backup $backup) {
                return [
                    'path' => $backup->path(),
                    'file_name' => explode('/', $backup->path())[1],
                    'date' => $backup->date()->format('Y-m-d H:i:s'),
                    'size' => Format::humanReadableSize($backup->sizeInBytes()),
                ];
            })
            ->toArray();
    }

    public function backupList(): array
    {
        $monitorConfig = MonitoredBackupsConfig::fromArray(config('backup.monitor_backups'));

        return BackupDestinationStatusFactory::createForMonitorConfig($monitorConfig)
            ->map(function (BackupDestinationStatus $backupDestinationStatus) {
                return [
                    'name' => $backupDestinationStatus->backupDestination()->backupName(),
                    'disk' => $backupDestinationStatus->backupDestination()->diskName(),
                    'reachable' => $backupDestinationStatus->backupDestination()->isReachable(),
                    'healthy' => $backupDestinationStatus->isHealthy(),
                    'amount' => $backupDestinationStatus->backupDestination()->backups()->count(),
                    'newest' => $backupDestinationStatus->backupDestination()->newestBackup()
                        ? $backupDestinationStatus->backupDestination()->newestBackup()->date()->diffForHumans()
                        : 'No backups present',
                    'usedStorage' => Format::humanReadableSize($backupDestinationStatus->backupDestination()->usedStorage()),
                ];
            })
            ->values()
            ->toArray();
    }

    public function make(): void
    {

        $result = Artisan::call('backup:run --only-db');

        if ($result) {
            Session::flash('status', [
                'message' => 'Backup created successfully!',
                'type' => 'success'
            ]);
        } else {
            Session::flash('status', [
                'message' => 'There was a problem creating the backup!',
                'type' => 'danger'
            ]);
        }
    }

    public function destroy(int $id)
    {
        $files = $this->getFiles('local');

        $deletingFile = $files[$id];

        try {
            $backupDestination = BackupDestination::create('local', config('backup.backup.name'));

            $backupDestination
                ->backups()
                ->first(function (Backup $backup) use ($deletingFile) {
                    return $backup->path() === $deletingFile['path'];
                })
                ->delete();

            Session::flash('status', [
                'message' => 'File Deleted Successfully!',
                'type' => 'success'
            ]);
        } catch (\Throwable $th) {
            report($th);

            Session::flash('status', [
                'message' => 'There was a problem deleting the file!',
                'type' => 'success'
            ]);
        }

    }

    public function databaseBackupDownload(string $fileName)
    {

        $backupDestination = BackupDestination::create('local', config('backup.backup.name'));

        $backup = $backupDestination->backups()->first(function (Backup $backup) use ($fileName) {
            return $backup->path() === config('backup.backup.name').'/'.$fileName;
        });

        if (!$backup) {
            Session::flash('status', [
                'message' => 'Backup file not found.',
                'type' => 'danger'
            ]);
        }

        return $this->respondWithBackupStream($backup);
    }

    public function respondWithBackupStream(Backup $backup): StreamedResponse
    {
        $fileName = pathinfo($backup->path(), PATHINFO_BASENAME);

        $downloadHeaders = [
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Content-Type' => 'application/zip',
            'Content-Length' => $backup->sizeInBytes(),
            'Content-Disposition' => 'attachment; filename="'. $fileName . '"',
            'Pragma' => 'public',
        ];

        return response()->stream(function () use ($backup) {
            $stream = $backup->stream();

            fpassthru($stream);

            if (is_resource($stream)) {
                fclose($stream);
            }
        }, 200, $downloadHeaders);
    }
}
