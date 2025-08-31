<?php

/**
 * @file FunctionDescriptor.php
 * A basic class for describing a function
 *
 * Lang en
 * Reviewstatus: 2025-07-15
 * Create date: 2025-02-28
 * Localization: complete
 * Documentation: complete
 * Tests: /tests/Unit/Parser/LanguageDescriptor/FunctionDescriptorTest.php
 * Coverage Unit:
 */

namespace Sunhill\Parser\LanguageDescriptor;

use Sunhill\Basic\Base;

class FunctionDescriptor extends Base
{
    protected string $context;

    /**
     * The name of the function
     */
    protected string $name;

    /**
     * The return type of the function
     */
    protected string $return_type;

    /**
     * An array of parameter descriptors (or empty if no parameter)
     */
    protected array $parameters = [];

    public function __construct(string $descriptor)
    {
        preg_match('/(.*)\:([_a-zA-Z0-9]*)\((.*)\)\:(.*)/', $descriptor, $matches);
        $this->context = $matches[1];
        $this->name = $matches[2];
        $this->parameters = empty($matches[3]) ? [] : explode(',', $matches[3]);
        $this->return_type = $matches[4];
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getReturnType(): string
    {
        return $this->return_type;
    }

    public function getParameterCount(): int
    {
        return count($this->parameters);
    }

    public function getContext(): string
    {
        return $this->context;
    }

    private int $stack_pointer = 0;

    public function reset()
    {
        $this->stack_pointer = 0;
    }

    public function pop(): ?string
    {
        if ($this->stack_pointer >= count($this->parameters)) {
            return null;
        }
        $result = $this->parameters[$this->stack_pointer++];
        if (substr($result, 0, 3) == '...') {
            $this->stack_pointer--;

            return '?'.substr($result, 3);
        }
        if ($result[0] !== '?') {
            return '!'.$result;
        }

        return $result;
    }
}
