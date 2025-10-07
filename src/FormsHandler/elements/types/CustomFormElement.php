<?php

namespace FormsHandler\elements\types;

abstract class CustomFormElement extends FormElement {
    abstract public function getLabel(): ?string;
}