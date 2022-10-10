<?php

namespace devavi\leveltwo\Http\Actions;

use devavi\leveltwo\Http\Request;
use devavi\leveltwo\Http\Response;

interface ActionInterface
{
    public function handle(Request $request): Response;
}
