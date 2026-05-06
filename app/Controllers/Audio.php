<?php
namespace App\Controllers;
use CodeIgniter\Controller;

class Audio extends Controller
{
    public function serve($file)
    {
        $file = basename($file);
        if (!preg_match('/^[\w\-]+\.mp3$/', $file)) {
            return $this->response->setStatusCode(404);
        }
        $path = FCPATH . 'mp3/' . $file;
        if (!file_exists($path)) {
            return $this->response->setStatusCode(404);
        }
        return $this->response
            ->setHeader('Content-Type', 'audio/mpeg')
            ->setHeader('Cache-Control', 'public, max-age=86400')
            ->setHeader('Accept-Ranges', 'bytes')
            ->setBody(file_get_contents($path));
    }
}
