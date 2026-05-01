<?php

namespace FormsHandler\utils\traits;

trait DefaultValueTrait {
    /** @var mixed $default */
    protected mixed $default;

    /**
     * @return mixed
     */
    public function getDefaultValue(): mixed {
        return $this->default;
    }
}