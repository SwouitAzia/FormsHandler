<?php

namespace FormsHandler\elements\visual;

use FormsHandler\elements\FormElement;
use FormsHandler\elements\types\CustomElement;
use FormsHandler\elements\types\SimpleElement;

abstract class VisualElement extends FormElement implements CustomElement, SimpleElement {
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
    public  function getType(): VisualElementType {
        return $this->type;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array {
        return [
            "type" => $this->getType()->getName(),
            "text" => $this->getText()
        ];
    }

    /**
     * @return string|null
     */
    public function getLabel(): ?string {
        return null;
    }
}