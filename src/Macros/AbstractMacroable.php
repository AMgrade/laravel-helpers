<?php

declare(strict_types = 1);

namespace McMatters\Helpers\Macros;

use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;
use ReflectionClass;
use ReflectionException;
use function array_unshift;

/**
 * Class AbstractMacroable
 *
 * @package McMatters\Helpers\Helpers
 */
abstract class AbstractMacroable
{
    /**
     * Auto registration of macros.
     *
     * @throws ReflectionException
     */
    public function register()
    {
        $reflection = new ReflectionClass(static::class);

        /** @var Macroable $class */
        $class = static::getClass();
        $static = $this;

        foreach ($reflection->getMethods() as $method) {
            $method = $method->getName();

            if ($method !== 'register' && Str::startsWith($method, 'register')) {
                $macro = Str::camel(Str::substr($method, 8));
                if (!$class::hasMacro($macro)) {
                    $class::macro($macro, function (...$args) use ($static, $method) {
                        if (isset($this)) {
                            array_unshift($args, $this);
                        }

                        return $static->{$method}(...$args);
                    });
                }
            }
        }
    }

    /**
     * @return string
     */
    abstract public static function getClass(): string;
}
