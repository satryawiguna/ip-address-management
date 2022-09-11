<?php

namespace App\Presentation\Http\Controllers;

use App\Core\Application\Request\AuditableRequest;
use Illuminate\Support\Facades\Auth;

trait RequestAuthor
{
    protected function setRequestAuthor(AuditableRequest $request)
    {
        if (Auth::user()) {
            $request->request_by = Auth::user()->name;
        } else {
            $request->request_by = 'system';
        }
    }
}
