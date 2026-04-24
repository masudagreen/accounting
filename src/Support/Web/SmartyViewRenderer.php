<?php

declare(strict_types=1);

namespace Rucaro\Support\Web;

use Smarty\Smarty;

/**
 * Light Smarty adapter for UI pages. Keeps Smarty configuration in exactly
 * one place so every controller renders with consistent compile dirs, security
 * options, and shared helpers (CSRF field, flash bag, selected entity).
 *
 * Intentionally does not depend on the HTTP layer — takes and returns plain
 * strings so it can be unit-tested without standing up a full request.
 */
final class SmartyViewRenderer
{
    private ?Smarty $smarty = null;

    public function __construct(
        private readonly string $templateDir,
        private readonly string $compileDir,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public function render(string $template, array $data = []): string
    {
        $smarty = $this->smarty();
        $smarty->assign($data);
        return (string) $smarty->fetch($template);
    }

    private function smarty(): Smarty
    {
        if ($this->smarty !== null) {
            return $this->smarty;
        }
        if (!is_dir($this->compileDir)) {
            @mkdir($this->compileDir, 0775, true);
        }
        $smarty = new Smarty();
        $smarty->setTemplateDir($this->templateDir);
        $smarty->setCompileDir($this->compileDir);
        $smarty->setEscapeHtml(true);
        $this->smarty = $smarty;
        return $smarty;
    }
}
