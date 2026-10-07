<?php

namespace Simtabi\Lacommerce\Generators\Exceptions;

use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

/**
 * Thrown for a configuration value the package cannot use.
 *
 * Not final, so a subclass keeps working, and invalidArgument() returns an instance of the class it is called
 * on. A subclass must keep a constructor callable as `new static($message, $code)`.
 *
 * @phpstan-consistent-constructor
 */
class InvalidOptionException extends Exception
{

    /**
     * Invalid Argument.
     *
     * @param string $message
     * @param int    $code    HTTP status render() responds with; 500 when not given
     * @return self
     */
    public static function invalidArgument(string $message, int $code = 500): self
    {
        return new static($message, $code);
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
