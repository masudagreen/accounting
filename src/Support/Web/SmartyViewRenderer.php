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
        private readonly ?NavbarLookup $navbar = null,
        private readonly ?SessionStore $session = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public function render(string $template, array $data = []): string
    {
        $smarty = $this->smarty();
        $smarty->assign($this->withNavbarDefaults($data));

        return (string) $smarty->fetch($template);
    }

    /**
     * Auto-fill the few keys the shared navbar template needs whenever a
     * controller didn't bother. Without this, every non-Dashboard page
     * would have to know to load the entity list and fiscal terms just to
     * render the chrome — so the entity selector silently disappeared on
     * Reports / Master / FixedAsset etc.
     *
     * Controller-supplied values always win; we only fill missing or empty
     * keys.
     *
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    private function withNavbarDefaults(array $data): array
    {
        if ($this->navbar === null || $this->session === null) {
            return $data;
        }
        $userId = $this->session->getUserId();
        if ($userId === null || $userId === '') {
            return $data;
        }

        $entities = $data['entities'] ?? null;
        if (!is_array($entities) || $entities === []) {
            $data['entities'] = $this->navbar->entitiesForUser($userId);
        }

        $entityId = isset($data['selected_entity_id']) && is_string($data['selected_entity_id'])
            ? $data['selected_entity_id']
            : ($this->session->getSelectedEntity() ?? '');
        if ($entityId !== '') {
            // Only auto-fill the term list when nothing usable is in scope —
            // controllers that already query terms (e.g. Journal pages
            // passing `nav_fiscal_terms`) win.
            $hasNavTerms = isset($data['nav_fiscal_terms']) && is_array($data['nav_fiscal_terms']) && $data['nav_fiscal_terms'] !== [];
            $hasFiscalTerms = isset($data['fiscal_terms']) && is_array($data['fiscal_terms']) && $data['fiscal_terms'] !== [];
            if (!$hasNavTerms && !$hasFiscalTerms) {
                $data['nav_fiscal_terms'] = $this->navbar->fiscalTermsForEntity($entityId);
            }
            if (!isset($data['selected_entity_id']) || $data['selected_entity_id'] === '') {
                $data['selected_entity_id'] = $entityId;
            }
        }

        return $data;
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
