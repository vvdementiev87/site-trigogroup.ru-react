<?php

namespace devavi\leveltwo\Http\Actions;

use devavi\leveltwo\http\Request;
use devavi\leveltwo\http\Response;

interface ActionInterface
{
    public function handle(Request $request): Response;
}