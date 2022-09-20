<?php

namespace devavi\leveltwo\Http\Auth;

use devavi\leveltwo\Blog\User;
use devavi\leveltwo\Http\Request;

interface AuthenticationInterface
{
    public function user(Request $request): User;
}
