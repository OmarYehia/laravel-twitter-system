<?php

namespace App\Contracts;

interface UserRepositoryInterface
{
    public function saveUser(Illuminate\Http\Request $request);
}
