<?php

namespace FormsHandler\elements\simpleform;

use FormsHandler\elements\types\SimpleFormElement;

class Button extends SimpleFormElement {
    public function __construct(
        protected string $text,
        protected ?ImageType $imageType = null,
        protected string $imageData = "",
        protected ?string $label = null
    ) {}

    /**
     * @return string
     */
    public function getText(): string {
        return $this->text;
    }

    /**
     * @return ImageType|null
     */
    public function getImageType(): ?ImageType {
        return $this->imageType;
    }

    /**
     * @return string
     */
    public function getImageData(): string {
        return $this->imageData;
    }

    /**
     * @return string|null
     */
    public function getLabel(): ?string {
        return $this->label;
    }


    /**
     * @return array|string[]
     */
    public function jsonSerialize(): array {
        $content = ["text" => $this->getText()];

        $imageType = $this->getImageType();
        if ($imageType !== null) {
            $content["image"]["type"] = $imageType->value;
            $content["image"]["data"] = $this->getImageData();
        }

        return $content;
    }
}