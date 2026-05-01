<?php

namespace FormsHandler\elements\simpleform;

use FormsHandler\elements\FormElement;
use FormsHandler\elements\types\SimpleElement;
use FormsHandler\elements\types\SimpleElementTrait;

class Button extends FormElement implements SimpleElement {
    use SimpleElementTrait;

    /** @var ImageType|null $imageType */
    protected ?ImageType $imageType;

    /** @var string $imageData */
    protected string $imageData;

    /**
     * @param string $text
     * @param ImageType|null $imageType
     * @param string $imageData
     * @param string|null $label
     */
    public function __construct(
        string $text,
        ?ImageType $imageType = null,
        string $imageData = "",
        ?string $label = null
    ) {
        parent::__construct($text);

        $this->label = $label;

        $this->imageType = $imageType;
        $this->imageData = $imageData;
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
     * @return array{text: string, image: array{type: string, data: string}}
     */
    public function jsonSerialize(): array {
        $content = [
            "type" => "button",
            "text" => $this->getText()
        ];

        $imageType = $this->getImageType();
        if ($imageType !== null) {
            $content["image"]["type"] = $imageType->value;
            $content["image"]["data"] = $this->getImageData();
        }

        return $content;
    }
}