<?php

declare(strict_types=1);

namespace App\Domain\Report;

/**
 * 帳票の抽象基底インターフェース.
 *
 * 各 Renderer は render() で HTML 文字列を返し、
 * format() で対応するフォーマットを返す.
 *
 * @template T
 */
interface Report
{
    /**
     * @param T $data
     */
    public function render(mixed $data): string;

    public function format(): ReportFormat;
}
