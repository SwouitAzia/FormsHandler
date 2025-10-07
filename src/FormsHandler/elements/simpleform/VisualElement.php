<?php

namespace FormsHandler\elements\simpleform;

/**
 * Technically, visual elements are implemented as buttons because
 * Simple Forms only supports button-based components.
 *
 * The visual appearance is handled client-side through the
 * optional UI texture pack, which defines how this pseudo-button is rendered.
 */
abstract class VisualElement extends Button {
    public function isButton(): bool {
        return false;
    }
}