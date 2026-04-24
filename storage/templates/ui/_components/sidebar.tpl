<aside class="rucaro-sidebar">
  <div class="text-uppercase small text-muted mb-2">メニュー</div>
  <ul class="nav nav-pills flex-column gap-1">
    <li class="nav-item">
      <a class="nav-link{if isset($active_nav) && $active_nav == 'dashboard'} active{/if}" href="/ui/dashboard">
        <i class="bi bi-speedometer2"></i> ダッシュボード
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link{if isset($active_nav) && $active_nav == 'journals'} active{/if}" href="/ui/journals">
        <i class="bi bi-journal-text"></i> 仕訳一覧
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link{if isset($active_nav) && $active_nav == 'ledger'} active{/if}" href="/ui/ledger">
        <i class="bi bi-list-columns-reverse"></i> 総勘定元帳
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link{if isset($active_nav) && $active_nav == 'pl'} active{/if}" href="/ui/pl">
        <i class="bi bi-graph-up-arrow"></i> 損益計算書
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link{if isset($active_nav) && $active_nav == 'bs'} active{/if}" href="/ui/bs">
        <i class="bi bi-columns-gap"></i> 貸借対照表
      </a>
    </li>
  </ul>

  <div class="text-uppercase small text-muted mt-4 mb-2">帳票</div>
  <ul class="nav nav-pills flex-column gap-1">
    <li class="nav-item">
      <a class="nav-link{if isset($active_nav) && $active_nav == 'cs'} active{/if}" href="/ui/cs">
        <i class="bi bi-cash-stack"></i> キャッシュフロー計算書
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link{if isset($active_nav) && $active_nav == 'fs_multi'} active{/if}" href="/ui/fs/multi">
        <i class="bi bi-layout-three-columns"></i> 複数期比較決算書
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link{if isset($active_nav) && $active_nav == 'bep'} active{/if}" href="/ui/bep">
        <i class="bi bi-bullseye"></i> 損益分岐点分析
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link{if isset($active_nav) && $active_nav == 'blue_return'} active{/if}" href="/ui/blue-return">
        <i class="bi bi-file-earmark-text"></i> 青色申告決算書
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link{if isset($active_nav) && $active_nav == 'ss'} active{/if}" href="/ui/ss">
        <i class="bi bi-diagram-3"></i> 株主資本等変動計算書
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link{if isset($active_nav) && $active_nav == 'notes'} active{/if}" href="/ui/notes">
        <i class="bi bi-sticky"></i> 注記表
      </a>
    </li>
  </ul>

  <div class="text-uppercase small text-muted mt-4 mb-2">マスタ</div>
  <ul class="nav nav-pills flex-column gap-1">
    <li class="nav-item">
      <a class="nav-link{if isset($active_master) && $active_master == 'account_titles'} active{/if}" href="/ui/masters/account-titles">
        <i class="bi bi-bookmark"></i> 勘定科目
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link{if isset($active_master) && $active_master == 'sub_account_titles'} active{/if}" href="/ui/masters/sub-account-titles">
        <i class="bi bi-bookmark-dash"></i> 補助科目
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link{if isset($active_master) && $active_master == 'entities'} active{/if}" href="/ui/masters/entities">
        <i class="bi bi-building"></i> 事業主
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link{if isset($active_master) && $active_master == 'fiscal_terms'} active{/if}" href="/ui/masters/fiscal-terms">
        <i class="bi bi-calendar3"></i> 会計期
      </a>
    </li>
  </ul>

  <div class="text-uppercase small text-muted mt-4 mb-2">計画・管理</div>
  <ul class="nav nav-pills flex-column gap-1">
    <li class="nav-item">
      <a class="nav-link{if isset($active_nav) && $active_nav == 'fixed_assets'} active{/if}" href="/ui/fixed-assets">
        <i class="bi bi-box-seam"></i> 固定資産
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link{if isset($active_nav) && $active_nav == 'budgets'} active{/if}" href="/ui/budgets">
        <i class="bi bi-cash-stack"></i> 予算
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link{if isset($active_nav) && $active_nav == 'cash_plans'} active{/if}" href="/ui/cash-plans">
        <i class="bi bi-piggy-bank"></i> 資金繰り
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link{if isset($active_nav) && $active_nav == 'consumption_tax'} active{/if}" href="/ui/consumption-tax/periods">
        <i class="bi bi-receipt"></i> 消費税申告期間
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link{if isset($active_nav) && $active_nav == 'ss_adjustments'} active{/if}" href="/ui/ss-adjustments">
        <i class="bi bi-diagram-3"></i> 純資産変動調整
      </a>
    </li>
  </ul>
</aside>
