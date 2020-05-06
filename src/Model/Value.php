<?php

namespace Plexikon\Kernel\Model;

interface Value
{
    /**
     * @param Value $value
     * @return bool
     */
    public function sameValueAs(Value $value): bool;

    /**
     * @return mixed
     */
    public function getValue();
}
