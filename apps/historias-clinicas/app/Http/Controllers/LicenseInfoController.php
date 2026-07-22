<?php

namespace App\Http\Controllers;

use App\Services\License\LicenseClientInterface;

class LicenseInfoController extends Controller
{
    public function __construct(private LicenseClientInterface $license) {}

    public function index()
    {
        try {
            $info = $this->license->getInfo();
        } catch (\Throwable) {
            $info = null;
        }

        return view('license.index', compact('info'));
    }
}
