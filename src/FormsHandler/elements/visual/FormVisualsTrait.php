<?php

namespace FormsHandler\elements\visual;

use FormsHandler\elements\visual\types\Divider;
use FormsHandler\elements\visual\types\Header;
use FormsHandler\elements\visual\types\Label;
use FormsHandler\types\CustomForm;
use FormsHandler\types\SimpleForm;

trait FormVisualsTrait {
    /**
     * @param string $text
     * @return CustomForm|SimpleForm|FormVisualsTrait
     */
    public function addLabel(string $text): self {
        return $this->addElement(new Label($text));
    }

    /**
     * @param string $text
     * @return CustomForm|SimpleForm|FormVisualsTrait
     */
    public function addHeader(string $text): self {
        return $this->addElement(new Header($text));
    }

    /**
     * @return CustomForm|SimpleForm|FormVisualsTrait
     */
    public function addDivider(): self {
        return $this->addElement(new Divider());
    }
}