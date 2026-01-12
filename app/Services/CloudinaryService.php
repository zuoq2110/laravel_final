<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Cloudinary\Api\Upload\UploadApi;
use Illuminate\Http\UploadedFile;
use Exception;

class CloudinaryService
{
    private $cloudinary;

    public function __construct()
    {
        $this->cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => config('services.cloudinary.cloud_name'),
                'api_key' => config('services.cloudinary.api_key'),
                'api_secret' => config('services.cloudinary.api_secret'),
            ],
            'url' => [
                'secure' => true
            ]
        ]);
    }

    public function uploadFile(UploadedFile $file, string $folder = 'tickets'): array
    {
        try {
            $result = $this->cloudinary->uploadApi()->upload(
                $file->getRealPath(),
                [
                    'folder' => $folder,
                    'resource_type' => 'auto',
                    'public_id' => uniqid() . '_' . pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
                    'use_filename' => true,
                    'unique_filename' => true,
                ]
            );

            return [
                'public_id' => $result['public_id'],
                'secure_url' => $result['secure_url'],
                'url' => $result['url'],
                'bytes' => $result['bytes'],
                'format' => $result['format'],
            ];
        } catch (Exception $e) {
            throw new Exception('Failed to upload file to Cloudinary: ' . $e->getMessage());
        }
    }

    /**
     * Delete file from Cloudinary
     *
     * @param string $publicId
     * @return bool
     */
    public function deleteFile(string $publicId): bool
    {
        try {
            $this->cloudinary->uploadApi()->destroy($publicId);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}