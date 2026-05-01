<?php

namespace FormsHandler\elements\customform\visual;

use FormsHandler\elements\types\CustomFormElement;
use FormsHandler\utils\VisualElementType;

abstract class VisualElement extends CustomFormElement {
    /** @var VisualElementType $type */
    protected VisualElementType $type;

    /**
     * @param VisualElementType $type
     * @param string $text
     */
    public function __construct(
        VisualElementType $type,
        string $text
    ) {
        parent::__construct($text);

        $this->type = $type;
    }

    /**
     * @return VisualElementType
     */
    public function getType(): VisualElementType {
        return $this->type;
    }

    /**
     * @return array{type: string, text: string}
     */
    public function jsonSerialize(): array {
        return [
            "type" => $this->getType()->getName(),
            "text" => $this->getText()
        ];
    }
}