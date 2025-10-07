<?php

namespace FormsHandler\elements\types;

abstract class SimpleFormElement extends FormElement {
    abstract public function getLabel(): ?string;

    public function isButton(): bool {
        return true;
    }
}