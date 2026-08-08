<?php

namespace App\Http\Controllers;

use Livewire\Features\SupportFileUploads\FileUploadConfiguration;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class LivewireCustomFileUploadController implements HasMiddleware
{
    public static array $defaultMiddleware = ['web'];

    public static function middleware()
    {
        $middleware = (array) FileUploadConfiguration::middleware();

        foreach (array_reverse(static::$defaultMiddleware) as $defaultMiddleware) {
            if (! in_array($defaultMiddleware, $middleware)) {
                array_unshift($middleware, $defaultMiddleware);
            }
        }

        return array_map(fn ($middleware) => new Middleware($middleware), $middleware);
    }

    public function handle(Request $request)
    {
        $disk = FileUploadConfiguration::disk();

        $files = $request->file('files') ?? $request->input('files');
        if (!$files) {
            return response()->json(['error' => 'Tidak ada file yang diunggah'], 400);
        }

        $filePaths = $this->validateAndStore($files, $disk);

        return ['paths' => $filePaths];
    }

    public function validateAndStore($files, $disk)
    {
        Validator::make(['files' => $files], [
            'files.*' => FileUploadConfiguration::rules()
        ])->validate();

        $fileHashPaths = collect($files)->map(function ($file) use ($disk) {
            return FileUploadConfiguration::storeTemporaryFile($file, $disk);
        });

        return $fileHashPaths->map(function ($path) {
            $stripped = str_replace(FileUploadConfiguration::path('/'), '', $path);

            return TemporaryUploadedFile::signPath($stripped);
        });
    }
}
