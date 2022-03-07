<?php

namespace Simtabi\Lacommerce\Generators\Exceptions;

use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Request;

class InvalidOptionException extends Exception
{

    /**
     * Invalid Argument.
     *
     * @param string $message
     * @return self [type]
     */
    public static function invalidArgument(string $message): self
    {
        return new static($message);
    }

    /**
     * Make the Exception renderable.
     *
     * @param Request $request
     * @return Application|ResponseFactory|Response
     */
    public function render(Request $request): Response|Application|ResponseFactory
    {
        return response(['error' => $this->getMessage()], $this->getCode());
    }

}
