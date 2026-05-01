<?php

namespace FormsHandler\utils;

enum VisualElementType: string {
    case DIVIDER = "divider";
    case HEADER = "header";
    case LABEL = "label";

    /**
     * @return string
     */
    public function getName(): string {
        return $this->value;
    }
}