<?php

declare(strict_types=1);

namespace Rucaro\Support\Container;

use PDO;
use Rucaro\Application\AccountTitle\CreateAccountTitleUseCase;
use Rucaro\Application\AccountTitle\DeleteAccountTitleUseCase;
use Rucaro\Application\AccountTitle\ListAccountTitlesUseCase;
use Rucaro\Application\AccountTitle\UpdateAccountTitleUseCase;
use Rucaro\Application\Approval\ExpirePastDueApprovalsUseCase;
use Rucaro\Application\Approval\FindApprovalByTokenUseCase;
use Rucaro\Application\Approval\IssueApprovalTokenUseCase;
use Rucaro\Application\Approval\Port\ApprovalNotifierInterface;
use Rucaro\Application\Approval\Port\ApprovalTargetResolverInterface;
use Rucaro\Application\Approval\Port\MailSenderInterface;
use Rucaro\Application\Approval\Port\MessagingChannelInterface;
use Rucaro\Application\Approval\ResendApprovalUseCase;
use Rucaro\Application\Approval\RespondToApprovalUseCase;
use Rucaro\Application\Auth\GetMyProfileUseCase;
use Rucaro\Application\Auth\LoginUseCase;
use Rucaro\Application\BreakEvenPoint\AnalyzeBreakEvenPointUseCase;
use Rucaro\Application\BreakEvenPoint\ListCvpClassificationsUseCase;
use Rucaro\Application\BreakEvenPoint\UpsertCvpClassificationsUseCase;
use Rucaro\Application\BlueReturn\CreateBlueReturnUseCase;
use Rucaro\Application\BlueReturn\DeleteBlueReturnUseCase;
use Rucaro\Application\BlueReturn\FinalizeBlueReturnUseCase;
use Rucaro\Application\BlueReturn\GenerateBlueReturnSnapshotUseCase;
use Rucaro\Application\BlueReturn\GetBlueReturnUseCase;
use Rucaro\Application\BlueReturn\ListBlueReturnsUseCase;
use Rucaro\Application\BlueReturn\UpdateBlueReturnUseCase;
use Rucaro\Application\Budget\AnalyzeBudgetVarianceUseCase;
use Rucaro\Application\Budget\ApproveBudgetUseCase;
use Rucaro\Application\Budget\CreateBudgetUseCase;
use Rucaro\Application\Budget\DeleteBudgetUseCase;
use Rucaro\Application\Budget\GetBudgetUseCase;
use Rucaro\Application\Budget\ListBudgetsUseCase;
use Rucaro\Application\Budget\LockBudgetUseCase;
use Rucaro\Application\Budget\UpdateBudgetUseCase;
use Rucaro\Application\CashPlan\CreateCashPlanUseCase;
use Rucaro\Application\CashPlan\DeleteCashPlanUseCase;
use Rucaro\Application\CashPlan\GetCashPlanUseCase;
use Rucaro\Application\CashPlan\ListCashPlansUseCase;
use Rucaro\Application\CashPlan\UpdateCashPlanUseCase;
use Rucaro\Application\StatementOfChangesInEquity\CreateSsAdjustmentUseCase;
use Rucaro\Application\StatementOfChangesInEquity\DeleteSsAdjustmentUseCase;
use Rucaro\Application\StatementOfChangesInEquity\GenerateStatementOfChangesInEquityUseCase;
use Rucaro\Application\StatementOfChangesInEquity\ListSsAdjustmentsUseCase;
use Rucaro\Application\StatementOfChangesInEquity\UpdateSsAdjustmentUseCase;
use Rucaro\Application\ConsumptionTax\CalculateConsumptionTaxUseCase;
use Rucaro\Application\ConsumptionTax\CreateConsumptionTaxPeriodUseCase;
use Rucaro\Application\ConsumptionTax\GenerateConsumptionTaxReportUseCase;
use Rucaro\Application\ConsumptionTax\ListAccountTitleTaxDefaultsUseCase;
use Rucaro\Application\ConsumptionTax\ListConsumptionTaxCategoriesUseCase;
use Rucaro\Application\ConsumptionTax\ListConsumptionTaxPeriodsUseCase;
use Rucaro\Application\ConsumptionTax\ListConsumptionTaxRatesUseCase;
use Rucaro\Application\ConsumptionTax\ListInvoiceRegistrationsUseCase;
use Rucaro\Application\ConsumptionTax\UpsertAccountTitleTaxDefaultsUseCase;
use Rucaro\Application\ConsumptionTax\UpsertInvoiceRegistrationUseCase;
use Rucaro\Application\Entity\CreateEntityUseCase;
use Rucaro\Application\Entity\DeleteEntityUseCase;
use Rucaro\Application\Entity\ListEntitiesUseCase;
use Rucaro\Application\Entity\UpdateEntityUseCase;
use Rucaro\Application\FiscalTerm\CreateFiscalTermUseCase;
use Rucaro\Application\FiscalTerm\DeleteFiscalTermUseCase;
use Rucaro\Application\FiscalTerm\UpdateFiscalTermUseCase;
use Rucaro\Application\SubAccountTitle\CreateSubAccountTitleUseCase;
use Rucaro\Application\SubAccountTitle\DeleteSubAccountTitleUseCase;
use Rucaro\Application\SubAccountTitle\UpdateSubAccountTitleUseCase;
use Rucaro\Application\FinancialStatement\GenerateFinancialStatementUseCase;
use Rucaro\Application\FinancialStatement\Multi\FinancialStatementProviderInterface;
use Rucaro\Application\FinancialStatement\Multi\FiscalTermMetadataRepositoryInterface;
use Rucaro\Application\FinancialStatement\Multi\GenerateMultiPeriodFinancialStatementUseCase;
use Rucaro\Application\FinancialStatementNotes\BulkImportFsNotesFromTemplatesUseCase;
use Rucaro\Application\FinancialStatementNotes\CreateFsNoteUseCase;
use Rucaro\Application\FinancialStatementNotes\DeleteFsNoteUseCase;
use Rucaro\Application\FinancialStatementNotes\GetFsNoteUseCase;
use Rucaro\Application\FinancialStatementNotes\ListFsNoteTemplatesUseCase;
use Rucaro\Application\FinancialStatementNotes\ListFsNotesUseCase;
use Rucaro\Application\FinancialStatementNotes\ReorderFsNotesUseCase;
use Rucaro\Application\FinancialStatementNotes\UpdateFsNoteUseCase;
use Rucaro\Application\FixedAsset\CreateFixedAssetUseCase;
use Rucaro\Application\FixedAsset\DisposeFixedAssetUseCase;
use Rucaro\Application\FixedAsset\GenerateDepreciationScheduleUseCase;
use Rucaro\Application\FixedAsset\GetFixedAssetLedgerUseCase;
use Rucaro\Application\FixedAsset\GetFixedAssetUseCase;
use Rucaro\Application\FixedAsset\ListFixedAssetsUseCase;
use Rucaro\Application\FixedAsset\PostDepreciationJournalUseCase;
use Rucaro\Application\FixedAsset\UpdateFixedAssetUseCase;
use Rucaro\Application\Journal\ApproveJournalUseCase;
use Rucaro\Application\Journal\CreateJournalUseCase;
use Rucaro\Application\Journal\DeleteJournalUseCase;
use Rucaro\Application\Journal\ListJournalsUseCase;
use Rucaro\Application\Journal\PostJournalUseCase;
use Rucaro\Application\Journal\ReverseJournalUseCase;
use Rucaro\Application\Journal\SearchJournalUseCase;
use Rucaro\Application\Journal\UpdateJournalUseCase;
use Rucaro\Application\Ledger\QueryLedgerUseCase;
use Rucaro\Application\TrialBalance\QueryTrialBalanceUseCase;
use Rucaro\Application\TrialBalance\RefreshTrialBalanceSnapshotUseCase;
use Rucaro\Domain\Journal\Service\JournalReverser;
use Rucaro\Domain\AccountTitle\AccountTitleRepositoryInterface;
use Rucaro\Domain\Approval\ApprovalTokenRepositoryInterface;
use Rucaro\Domain\Auth\ApiTokenRepositoryInterface;
use Rucaro\Domain\BreakEvenPoint\AccountTitleCvpClassificationRepositoryInterface;
use Rucaro\Domain\BreakEvenPoint\BreakEvenPointPdfGeneratorInterface;
use Rucaro\Domain\BreakEvenPoint\Service\BreakEvenPointCalculator;
use Rucaro\Domain\BlueReturn\BlueReturnPdfGeneratorInterface;
use Rucaro\Domain\BlueReturn\BlueReturnRepositoryInterface;
use Rucaro\Domain\BlueReturn\Service\BlueReturnBuilder;
use Rucaro\Domain\Budget\BudgetPdfGeneratorInterface;
use Rucaro\Domain\Budget\BudgetRepositoryInterface;
use Rucaro\Domain\Budget\BudgetVariancePdfGeneratorInterface;
use Rucaro\Domain\CashPlan\CashPlanPdfGeneratorInterface;
use Rucaro\Domain\CashPlan\CashPlanRepositoryInterface;
use Rucaro\Domain\ConsumptionTax\AccountTitleConsumptionTaxDefaultRepositoryInterface;
use Rucaro\Domain\ConsumptionTax\ConsumptionTaxCategoryRepositoryInterface;
use Rucaro\Domain\ConsumptionTax\ConsumptionTaxPeriodRepositoryInterface;
use Rucaro\Domain\ConsumptionTax\ConsumptionTaxRateRepositoryInterface;
use Rucaro\Domain\ConsumptionTax\ConsumptionTaxReportGeneratorInterface;
use Rucaro\Domain\ConsumptionTax\InvoiceRegistrationRepositoryInterface;
use Rucaro\Domain\ConsumptionTax\Service\ConsumptionTaxCalculatorFactory;
use Rucaro\Domain\ConsumptionTax\Service\InvoiceDeductionCalculator;
use Rucaro\Domain\ConsumptionTax\TaxableTransactionQueryInterface;
use Rucaro\Domain\Entity\EntityRepositoryInterface;
use Rucaro\Domain\FinancialStatement\FinancialStatementGeneratorInterface;
use Rucaro\Domain\FinancialStatementNotes\FsNoteRepositoryInterface;
use Rucaro\Domain\FinancialStatementNotes\FsNoteTemplateRepositoryInterface;
use Rucaro\Domain\FinancialStatementNotes\FsNotesPdfGeneratorInterface;
use Rucaro\Domain\FixedAsset\DepreciationScheduleRepositoryInterface;
use Rucaro\Domain\FixedAsset\FixedAssetCategoryRepositoryInterface;
use Rucaro\Domain\FixedAsset\FixedAssetLedgerGeneratorInterface;
use Rucaro\Domain\FixedAsset\FixedAssetRepositoryInterface;
use Rucaro\Domain\FixedAsset\Service\DepreciationCalculatorFactory;
use Rucaro\Domain\FinancialStatement\Port\AccountTitleFsMappingRepositoryInterface;
use Rucaro\Domain\FinancialStatement\Port\Cs\AccountTitleCsMappingRepositoryInterface;
use Rucaro\Domain\FinancialStatement\Port\Cs\CsSectionDefinitionRepositoryInterface;
use Rucaro\Domain\FinancialStatement\Port\Cs\Service\CashFlowStatementBuilder;
use Rucaro\Domain\FinancialStatement\Port\FsSectionDefinitionRepositoryInterface;
use Rucaro\Domain\FinancialStatement\Port\Service\FinancialStatementBuilder;
use Rucaro\Domain\StatementOfChangesInEquity\Service\StatementOfChangesInEquityBuilder;
use Rucaro\Domain\StatementOfChangesInEquity\SsManualAdjustmentRepositoryInterface;
use Rucaro\Domain\StatementOfChangesInEquity\StatementOfChangesInEquityPdfGeneratorInterface;
use Rucaro\Domain\Journal\JournalRepositoryInterface;
use Rucaro\Domain\Ledger\LedgerGeneratorInterface;
use Rucaro\Domain\Ledger\LedgerQueryInterface;
use Rucaro\Domain\Ledger\OpeningBalanceRepositoryInterface;
use Rucaro\Domain\TrialBalance\TrialBalanceQueryInterface;
use Rucaro\Domain\TrialBalance\TrialBalanceSnapshotRepositoryInterface;
use Rucaro\Domain\User\UserRepositoryInterface;
use Rucaro\Http\Controller\AccountTitle\ListAccountTitleController;
use Rucaro\Http\Controller\Approval\GetApprovalController;
use Rucaro\Http\Controller\BreakEvenPoint\GetBreakEvenPointController;
use Rucaro\Http\Controller\BreakEvenPoint\ListCvpClassificationController;
use Rucaro\Http\Controller\BreakEvenPoint\PutCvpClassificationController;
use Rucaro\Http\Controller\BlueReturn\CreateBlueReturnController;
use Rucaro\Http\Controller\BlueReturn\DeleteBlueReturnController;
use Rucaro\Http\Controller\BlueReturn\FinalizeBlueReturnController;
use Rucaro\Http\Controller\BlueReturn\GetBlueReturnController;
use Rucaro\Http\Controller\BlueReturn\ListBlueReturnController;
use Rucaro\Http\Controller\BlueReturn\UpdateBlueReturnController;
use Rucaro\Http\Controller\Budget\ApproveBudgetController;
use Rucaro\Http\Controller\Budget\CreateBudgetController;
use Rucaro\Http\Controller\Budget\DeleteBudgetController;
use Rucaro\Http\Controller\Budget\GetBudgetController;
use Rucaro\Http\Controller\Budget\GetBudgetVarianceController;
use Rucaro\Http\Controller\Budget\ListBudgetController;
use Rucaro\Http\Controller\Budget\LockBudgetController;
use Rucaro\Http\Controller\Budget\UpdateBudgetController;
use Rucaro\Http\Controller\CashPlan\CreateCashPlanController;
use Rucaro\Http\Controller\CashPlan\DeleteCashPlanController;
use Rucaro\Http\Controller\CashPlan\GetCashPlanController;
use Rucaro\Http\Controller\CashPlan\ListCashPlanController;
use Rucaro\Http\Controller\CashPlan\UpdateCashPlanController;
use Rucaro\Http\Controller\ConsumptionTax\CalculateConsumptionTaxController;
use Rucaro\Http\Controller\ConsumptionTax\CreateConsumptionTaxPeriodController;
use Rucaro\Http\Controller\ConsumptionTax\GetConsumptionTaxReportController;
use Rucaro\Http\Controller\ConsumptionTax\ListAccountTitleTaxDefaultsController;
use Rucaro\Http\Controller\ConsumptionTax\ListConsumptionTaxCategoriesController;
use Rucaro\Http\Controller\ConsumptionTax\ListConsumptionTaxPeriodsController;
use Rucaro\Http\Controller\ConsumptionTax\ListConsumptionTaxRatesController;
use Rucaro\Http\Controller\ConsumptionTax\ListInvoiceRegistrationsController;
use Rucaro\Http\Controller\ConsumptionTax\PutAccountTitleTaxDefaultsController;
use Rucaro\Http\Controller\ConsumptionTax\UpsertInvoiceRegistrationController;
use Rucaro\Http\Controller\Approval\PostApprovalController;
use Rucaro\Http\Controller\Approval\RequestApprovalController;
use Rucaro\Http\Controller\Approval\ResendApprovalController;
use Rucaro\Http\Controller\Auth\LoginController;
use Rucaro\Http\Controller\Auth\MeController;
use Rucaro\Http\Controller\Entity\ListEntityController;
use Rucaro\Http\Controller\FinancialStatement\GetFinancialStatementController;
use Rucaro\Http\Controller\FinancialStatement\Multi\GetMultiPeriodFinancialStatementController;
use Rucaro\Http\Controller\FinancialStatementNotes\BulkImportFsNotesController;
use Rucaro\Http\Controller\FinancialStatementNotes\CreateFsNoteController;
use Rucaro\Http\Controller\FinancialStatementNotes\DeleteFsNoteController;
use Rucaro\Http\Controller\FinancialStatementNotes\ExportFsNotesController;
use Rucaro\Http\Controller\FinancialStatementNotes\GetFsNoteController;
use Rucaro\Http\Controller\FinancialStatementNotes\ListFsNoteTemplatesController;
use Rucaro\Http\Controller\FinancialStatementNotes\ListFsNotesController;
use Rucaro\Http\Controller\FinancialStatementNotes\ReorderFsNotesController;
use Rucaro\Http\Controller\FinancialStatementNotes\UpdateFsNoteController;
use Rucaro\Http\Controller\StatementOfChangesInEquity\CreateSsAdjustmentController;
use Rucaro\Http\Controller\StatementOfChangesInEquity\DeleteSsAdjustmentController;
use Rucaro\Http\Controller\StatementOfChangesInEquity\GetStatementOfChangesInEquityController;
use Rucaro\Http\Controller\StatementOfChangesInEquity\ListSsAdjustmentsController;
use Rucaro\Http\Controller\StatementOfChangesInEquity\UpdateSsAdjustmentController;
use Rucaro\Http\Controller\FixedAsset\CreateFixedAssetController;
use Rucaro\Http\Controller\FixedAsset\DisposeFixedAssetController;
use Rucaro\Http\Controller\FixedAsset\GenerateDepreciationController;
use Rucaro\Http\Controller\FixedAsset\GetFixedAssetController;
use Rucaro\Http\Controller\FixedAsset\GetFixedAssetLedgerController;
use Rucaro\Http\Controller\FixedAsset\ListFixedAssetController;
use Rucaro\Http\Controller\FixedAsset\PostDepreciationJournalController;
use Rucaro\Http\Controller\FixedAsset\UpdateFixedAssetController;
use Rucaro\Http\Controller\Journal\ApproveJournalController;
use Rucaro\Http\Controller\Journal\CreateJournalController;
use Rucaro\Http\Controller\Journal\DeleteJournalController;
use Rucaro\Http\Controller\Journal\GetJournalController;
use Rucaro\Http\Controller\Journal\ListJournalController;
use Rucaro\Http\Controller\Journal\UpdateJournalController;
use Rucaro\Http\Controller\Ledger\GetLedgerController;
use Rucaro\Http\Controller\TrialBalance\GetTrialBalanceController;
use Rucaro\Http\Controller\Ui\DashboardController as UiDashboardController;
use Rucaro\Http\Controller\Ui\EntitySwitchController as UiEntitySwitchController;
use Rucaro\Http\Controller\Ui\Journal\JournalDeleteController as UiJournalDeleteController;
use Rucaro\Http\Controller\Ui\Journal\JournalEditController as UiJournalEditController;
use Rucaro\Http\Controller\Ui\Journal\JournalListController as UiJournalListController;
use Rucaro\Http\Controller\Ui\Journal\JournalNewController as UiJournalNewController;
use Rucaro\Http\Controller\Ui\Journal\JournalShowController as UiJournalShowController;
use Rucaro\Http\Controller\Ui\Journal\JournalUiContext as UiJournalUiContext;
use Rucaro\Http\Controller\Ui\Budget\BudgetLifecycleController as UiBudgetLifecycleController;
use Rucaro\Http\Controller\Ui\Budget\BudgetListController as UiBudgetListController;
use Rucaro\Http\Controller\Ui\Budget\BudgetNewController as UiBudgetNewController;
use Rucaro\Http\Controller\Ui\Budget\BudgetShowController as UiBudgetShowController;
use Rucaro\Http\Controller\Ui\Budget\BudgetVarianceController as UiBudgetVarianceController;
use Rucaro\Http\Controller\Ui\CashPlan\CashPlanDeleteController as UiCashPlanDeleteController;
use Rucaro\Http\Controller\Ui\CashPlan\CashPlanListController as UiCashPlanListController;
use Rucaro\Http\Controller\Ui\CashPlan\CashPlanNewController as UiCashPlanNewController;
use Rucaro\Http\Controller\Ui\CashPlan\CashPlanShowController as UiCashPlanShowController;
use Rucaro\Http\Controller\Ui\ConsumptionTax\AccountDefaultsController as UiCtAccountDefaultsController;
use Rucaro\Http\Controller\Ui\ConsumptionTax\ConsumptionTaxCalculateController as UiCtCalculateController;
use Rucaro\Http\Controller\Ui\ConsumptionTax\ConsumptionTaxPeriodListController as UiCtPeriodListController;
use Rucaro\Http\Controller\Ui\ConsumptionTax\ConsumptionTaxPeriodNewController as UiCtPeriodNewController;
use Rucaro\Http\Controller\Ui\ConsumptionTax\ConsumptionTaxPeriodShowController as UiCtPeriodShowController;
use Rucaro\Http\Controller\Ui\ConsumptionTax\InvoiceRegistrationController as UiCtInvoiceRegistrationController;
use Rucaro\Http\Controller\Ui\FixedAsset\FixedAssetDepreciateController as UiFixedAssetDepreciateController;
use Rucaro\Http\Controller\Ui\FixedAsset\FixedAssetDisposeController as UiFixedAssetDisposeController;
use Rucaro\Http\Controller\Ui\FixedAsset\FixedAssetEditController as UiFixedAssetEditController;
use Rucaro\Http\Controller\Ui\FixedAsset\FixedAssetListController as UiFixedAssetListController;
use Rucaro\Http\Controller\Ui\FixedAsset\FixedAssetNewController as UiFixedAssetNewController;
use Rucaro\Http\Controller\Ui\FixedAsset\FixedAssetShowController as UiFixedAssetShowController;
use Rucaro\Http\Controller\Ui\Planning\PlanningUiContext as UiPlanningContext;
use Rucaro\Http\Controller\Ui\StatementOfChangesInEquity\SsAdjustmentDeleteController as UiSsAdjustmentDeleteController;
use Rucaro\Http\Controller\Ui\StatementOfChangesInEquity\SsAdjustmentEditController as UiSsAdjustmentEditController;
use Rucaro\Http\Controller\Ui\StatementOfChangesInEquity\SsAdjustmentListController as UiSsAdjustmentListController;
use Rucaro\Http\Controller\Ui\StatementOfChangesInEquity\SsAdjustmentNewController as UiSsAdjustmentNewController;
use Rucaro\Http\Controller\Ui\LoginController as UiLoginController;
use Rucaro\Http\Controller\Ui\LogoutController as UiLogoutController;
use Rucaro\Http\Controller\Ui\Report\BepViewController as UiBepViewController;
use Rucaro\Http\Controller\Ui\Report\BlueReturnViewController as UiBlueReturnViewController;
use Rucaro\Http\Controller\Ui\Report\BsViewController as UiBsViewController;
use Rucaro\Http\Controller\Ui\Report\CsViewController as UiCsViewController;
use Rucaro\Http\Controller\Ui\Report\LedgerViewController as UiLedgerViewController;
use Rucaro\Http\Controller\Ui\Report\MultiPeriodFsViewController as UiMultiPeriodFsViewController;
use Rucaro\Http\Controller\Ui\Report\NotesListViewController as UiNotesListViewController;
use Rucaro\Http\Controller\Ui\Report\PlViewController as UiPlViewController;
use Rucaro\Http\Controller\Ui\Master\AccountTitleController as UiMasterAccountTitleController;
use Rucaro\Http\Controller\Ui\Master\EntityController as UiMasterEntityController;
use Rucaro\Http\Controller\Ui\Master\FiscalTermController as UiMasterFiscalTermController;
use Rucaro\Http\Controller\Ui\Master\SubAccountTitleController as UiMasterSubAccountTitleController;
use Rucaro\Http\Controller\Ui\Report\SsViewController as UiSsViewController;
use Rucaro\Http\Middleware\AuthenticateBearer;
use Rucaro\Http\Middleware\AuthenticateSession;
use Rucaro\Support\Web\CsrfTokenManager;
use Rucaro\Support\Web\FlashMessageBag;
use Rucaro\Support\Web\PeriodQueryHelper;
use Rucaro\Support\Web\SessionStore;
use Rucaro\Support\Web\SmartyViewRenderer;
use Rucaro\Domain\FiscalTerm\FiscalTermRepositoryInterface;
use Rucaro\Domain\SubAccountTitle\SubAccountTitleRepositoryInterface;
use Rucaro\Infrastructure\AccountTitle\PdoAccountTitleRepository;
use Rucaro\Infrastructure\FiscalTerm\PdoFiscalTermRepository;
use Rucaro\Infrastructure\SubAccountTitle\PdoSubAccountTitleRepository;
use Rucaro\Infrastructure\Approval\DefaultApprovalNotifier;
use Rucaro\Infrastructure\BreakEvenPoint\DompdfBreakEvenPointGenerator;
use Rucaro\Infrastructure\BreakEvenPoint\PdoAccountTitleCvpClassificationRepository;
use Rucaro\Infrastructure\BlueReturn\DompdfBlueReturnGenerator;
use Rucaro\Infrastructure\BlueReturn\PdoBlueReturnRepository;
use Rucaro\Infrastructure\Budget\DompdfBudgetGenerator;
use Rucaro\Infrastructure\Budget\DompdfBudgetVarianceGenerator;
use Rucaro\Infrastructure\Budget\PdoBudgetRepository;
use Rucaro\Infrastructure\CashPlan\DompdfCashPlanGenerator;
use Rucaro\Infrastructure\CashPlan\PdoCashPlanRepository;
use Rucaro\Infrastructure\ConsumptionTax\DompdfConsumptionTaxReportGenerator;
use Rucaro\Infrastructure\ConsumptionTax\PdoAccountTitleConsumptionTaxDefaultRepository;
use Rucaro\Infrastructure\ConsumptionTax\PdoConsumptionTaxCategoryRepository;
use Rucaro\Infrastructure\ConsumptionTax\PdoConsumptionTaxPeriodRepository;
use Rucaro\Infrastructure\ConsumptionTax\PdoConsumptionTaxRateRepository;
use Rucaro\Infrastructure\ConsumptionTax\PdoInvoiceRegistrationRepository;
use Rucaro\Infrastructure\ConsumptionTax\PdoTaxableTransactionQueryService;
use Rucaro\Infrastructure\Approval\JournalApprovalTargetResolver;
use Rucaro\Infrastructure\Approval\PdoApprovalTokenRepository;
use Rucaro\Infrastructure\Auth\BearerTokenGenerator;
use Rucaro\Infrastructure\Auth\PasswordHasher;
use Rucaro\Infrastructure\Auth\PdoApiTokenRepository;
use Rucaro\Infrastructure\Entity\PdoEntityRepository;
use Rucaro\Infrastructure\FinancialStatement\DompdfFinancialStatementGenerator;
use Rucaro\Infrastructure\FinancialStatement\Multi\DompdfMultiPeriodFinancialStatementGenerator;
use Rucaro\Infrastructure\FinancialStatement\Multi\MultiPeriodFinancialStatementGeneratorInterface;
use Rucaro\Infrastructure\FinancialStatement\Multi\PdoFiscalTermMetadataRepository;
use Rucaro\Infrastructure\FinancialStatementNotes\DompdfFsNotesGenerator;
use Rucaro\Infrastructure\FinancialStatementNotes\PdoFsNoteRepository;
use Rucaro\Infrastructure\FinancialStatementNotes\PdoFsNoteTemplateRepository;
use Rucaro\Infrastructure\StatementOfChangesInEquity\DompdfStatementOfChangesInEquityGenerator;
use Rucaro\Infrastructure\StatementOfChangesInEquity\PdoSsManualAdjustmentRepository;
use Rucaro\Infrastructure\FinancialStatement\Port\Cs\PdoAccountTitleCsMappingRepository;
use Rucaro\Infrastructure\FinancialStatement\Port\Cs\PdoCsSectionDefinitionRepository;
use Rucaro\Infrastructure\FinancialStatement\Port\PdoAccountTitleFsMappingRepository;
use Rucaro\Infrastructure\FinancialStatement\Port\PdoFsSectionDefinitionRepository;
use Rucaro\Infrastructure\FixedAsset\DompdfFixedAssetLedgerGenerator;
use Rucaro\Infrastructure\FixedAsset\PdoDepreciationScheduleRepository;
use Rucaro\Infrastructure\FixedAsset\PdoFixedAssetCategoryRepository;
use Rucaro\Infrastructure\FixedAsset\PdoFixedAssetRepository;
use Rucaro\Infrastructure\Journal\PdoJournalRepository;
use Rucaro\Infrastructure\Ledger\DompdfLedgerGenerator;
use Rucaro\Infrastructure\Ledger\PdoLedgerQueryService;
use Rucaro\Infrastructure\Ledger\ZeroOpeningBalanceRepository;
use Rucaro\Infrastructure\Mail\InMemoryMailSender;
use Rucaro\Infrastructure\Mail\NullMailSender;
use Rucaro\Infrastructure\Mail\SymfonyMailSender;
use Rucaro\Infrastructure\Messaging\NullMessagingChannel;
use Rucaro\Infrastructure\TrialBalance\PdoTrialBalanceQueryService;
use Rucaro\Infrastructure\TrialBalance\PdoTrialBalanceSnapshotRepository;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Infrastructure\User\PdoUserRepository;
use Rucaro\Support\Clock\ClockInterface;
use Rucaro\Support\Clock\SystemClock;

/**
 * Wire the Rucaro service graph. Called from the public entry point with a
 * pre-built PDO connection so HTTP requests don't each pay the connection
 * handshake cost.
 */
final class ContainerBootstrap
{
    public static function build(PDO $pdo): Container
    {
        $c = new Container();

        $c->setInstance(PDO::class, $pdo);
        $c->set(ClockInterface::class, static fn (): ClockInterface => new SystemClock());
        $c->set(UlidGenerator::class, static fn (Container $c): UlidGenerator
            => new UlidGenerator($c->getTyped(ClockInterface::class)));
        $c->set(PasswordHasher::class, static fn (): PasswordHasher => new PasswordHasher());
        $c->set(BearerTokenGenerator::class, static fn (): BearerTokenGenerator
            => new BearerTokenGenerator());

        // Repositories
        $c->set(
            UserRepositoryInterface::class,
            static fn (Container $c): UserRepositoryInterface
                => new PdoUserRepository($c->getTyped(PDO::class)),
        );
        $c->set(
            EntityRepositoryInterface::class,
            static fn (Container $c): EntityRepositoryInterface
                => new PdoEntityRepository($c->getTyped(PDO::class)),
        );
        $c->set(
            AccountTitleRepositoryInterface::class,
            static fn (Container $c): AccountTitleRepositoryInterface
                => new PdoAccountTitleRepository($c->getTyped(PDO::class)),
        );
        $c->set(
            SubAccountTitleRepositoryInterface::class,
            static fn (Container $c): SubAccountTitleRepositoryInterface
                => new PdoSubAccountTitleRepository($c->getTyped(PDO::class)),
        );
        $c->set(
            FiscalTermRepositoryInterface::class,
            static fn (Container $c): FiscalTermRepositoryInterface
                => new PdoFiscalTermRepository($c->getTyped(PDO::class)),
        );
        $c->set(
            JournalRepositoryInterface::class,
            static fn (Container $c): JournalRepositoryInterface
                => new PdoJournalRepository(
                    $c->getTyped(PDO::class),
                    $c->getTyped(UlidGenerator::class),
                ),
        );
        $c->set(
            ApiTokenRepositoryInterface::class,
            static fn (Container $c): ApiTokenRepositoryInterface
                => new PdoApiTokenRepository($c->getTyped(PDO::class)),
        );
        $c->set(
            TrialBalanceQueryInterface::class,
            static fn (Container $c): TrialBalanceQueryInterface
                => new PdoTrialBalanceQueryService($c->getTyped(PDO::class)),
        );
        $c->set(
            TrialBalanceSnapshotRepositoryInterface::class,
            static fn (Container $c): TrialBalanceSnapshotRepositoryInterface
                => new PdoTrialBalanceSnapshotRepository($c->getTyped(PDO::class)),
        );
        $c->set(
            LedgerQueryInterface::class,
            static fn (Container $c): LedgerQueryInterface
                => new PdoLedgerQueryService($c->getTyped(PDO::class)),
        );
        $c->set(
            OpeningBalanceRepositoryInterface::class,
            static fn (Container $c): OpeningBalanceRepositoryInterface => new ZeroOpeningBalanceRepository(),
        );

        // Middleware
        $c->set(
            AuthenticateBearer::class,
            static fn (Container $c): AuthenticateBearer
                => new AuthenticateBearer(
                    $c->getTyped(ApiTokenRepositoryInterface::class),
                    $c->getTyped(ClockInterface::class),
                ),
        );

        // Use cases
        $c->set(LoginUseCase::class, static fn (Container $c): LoginUseCase
            => new LoginUseCase(
                users: $c->getTyped(UserRepositoryInterface::class),
                tokens: $c->getTyped(ApiTokenRepositoryInterface::class),
                passwords: $c->getTyped(PasswordHasher::class),
                tokenGenerator: $c->getTyped(BearerTokenGenerator::class),
                ulids: $c->getTyped(UlidGenerator::class),
                clock: $c->getTyped(ClockInterface::class),
            ));
        $c->set(GetMyProfileUseCase::class, static fn (Container $c): GetMyProfileUseCase
            => new GetMyProfileUseCase($c->getTyped(UserRepositoryInterface::class)));
        $c->set(ListEntitiesUseCase::class, static fn (Container $c): ListEntitiesUseCase
            => new ListEntitiesUseCase($c->getTyped(EntityRepositoryInterface::class)));
        $c->set(ListAccountTitlesUseCase::class, static fn (Container $c): ListAccountTitlesUseCase
            => new ListAccountTitlesUseCase($c->getTyped(AccountTitleRepositoryInterface::class)));
        $c->set(ListJournalsUseCase::class, static fn (Container $c): ListJournalsUseCase
            => new ListJournalsUseCase($c->getTyped(JournalRepositoryInterface::class)));
        $c->set(CreateJournalUseCase::class, static fn (Container $c): CreateJournalUseCase
            => new CreateJournalUseCase(
                journals: $c->getTyped(JournalRepositoryInterface::class),
                ulids: $c->getTyped(UlidGenerator::class),
                clock: $c->getTyped(ClockInterface::class),
            ));
        $c->set(UpdateJournalUseCase::class, static fn (Container $c): UpdateJournalUseCase
            => new UpdateJournalUseCase(
                journals: $c->getTyped(JournalRepositoryInterface::class),
                ulids: $c->getTyped(UlidGenerator::class),
                clock: $c->getTyped(ClockInterface::class),
            ));
        $c->set(DeleteJournalUseCase::class, static fn (Container $c): DeleteJournalUseCase
            => new DeleteJournalUseCase(
                journals: $c->getTyped(JournalRepositoryInterface::class),
                clock: $c->getTyped(ClockInterface::class),
            ));
        $c->set(ApproveJournalUseCase::class, static fn (Container $c): ApproveJournalUseCase
            => new ApproveJournalUseCase(
                journals: $c->getTyped(JournalRepositoryInterface::class),
                clock: $c->getTyped(ClockInterface::class),
            ));
        $c->set(PostJournalUseCase::class, static fn (Container $c): PostJournalUseCase
            => new PostJournalUseCase(
                journals: $c->getTyped(JournalRepositoryInterface::class),
                clock: $c->getTyped(ClockInterface::class),
            ));
        $c->set(JournalReverser::class, static fn (Container $c): JournalReverser
            => new JournalReverser($c->getTyped(UlidGenerator::class)));
        $c->set(ReverseJournalUseCase::class, static fn (Container $c): ReverseJournalUseCase
            => new ReverseJournalUseCase(
                journals: $c->getTyped(JournalRepositoryInterface::class),
                reverser: $c->getTyped(JournalReverser::class),
                clock: $c->getTyped(ClockInterface::class),
            ));
        $c->set(SearchJournalUseCase::class, static fn (Container $c): SearchJournalUseCase
            => new SearchJournalUseCase($c->getTyped(JournalRepositoryInterface::class)));
        $c->set(QueryTrialBalanceUseCase::class, static fn (Container $c): QueryTrialBalanceUseCase
            => new QueryTrialBalanceUseCase(
                query: $c->getTyped(TrialBalanceQueryInterface::class),
                snapshots: $c->getTyped(TrialBalanceSnapshotRepositoryInterface::class),
                clock: $c->getTyped(ClockInterface::class),
            ));
        $c->set(RefreshTrialBalanceSnapshotUseCase::class, static fn (Container $c): RefreshTrialBalanceSnapshotUseCase
            => new RefreshTrialBalanceSnapshotUseCase(
                query: $c->getTyped(TrialBalanceQueryInterface::class),
                snapshots: $c->getTyped(TrialBalanceSnapshotRepositoryInterface::class),
                ulids: $c->getTyped(UlidGenerator::class),
                clock: $c->getTyped(ClockInterface::class),
            ));
        $c->set(QueryLedgerUseCase::class, static fn (Container $c): QueryLedgerUseCase
            => new QueryLedgerUseCase(
                query: $c->getTyped(LedgerQueryInterface::class),
                openingBalances: $c->getTyped(OpeningBalanceRepositoryInterface::class),
                clock: $c->getTyped(ClockInterface::class),
            ));

        // Controllers
        $c->set(LoginController::class, static fn (Container $c): LoginController
            => new LoginController($c->getTyped(LoginUseCase::class)));
        $c->set(MeController::class, static fn (Container $c): MeController
            => new MeController(
                $c->getTyped(GetMyProfileUseCase::class),
                $c->getTyped(AuthenticateBearer::class),
            ));
        $c->set(ListEntityController::class, static fn (Container $c): ListEntityController
            => new ListEntityController(
                $c->getTyped(ListEntitiesUseCase::class),
                $c->getTyped(AuthenticateBearer::class),
            ));
        $c->set(ListAccountTitleController::class, static fn (Container $c): ListAccountTitleController
            => new ListAccountTitleController(
                $c->getTyped(ListAccountTitlesUseCase::class),
                $c->getTyped(AuthenticateBearer::class),
            ));
        $c->set(ListJournalController::class, static fn (Container $c): ListJournalController
            => new ListJournalController(
                $c->getTyped(ListJournalsUseCase::class),
                $c->getTyped(AuthenticateBearer::class),
            ));
        $c->set(CreateJournalController::class, static fn (Container $c): CreateJournalController
            => new CreateJournalController(
                $c->getTyped(CreateJournalUseCase::class),
                $c->getTyped(AuthenticateBearer::class),
            ));
        $c->set(GetJournalController::class, static fn (Container $c): GetJournalController
            => new GetJournalController(
                $c->getTyped(JournalRepositoryInterface::class),
                $c->getTyped(AuthenticateBearer::class),
            ));
        $c->set(UpdateJournalController::class, static fn (Container $c): UpdateJournalController
            => new UpdateJournalController(
                $c->getTyped(UpdateJournalUseCase::class),
                $c->getTyped(AuthenticateBearer::class),
            ));
        $c->set(DeleteJournalController::class, static fn (Container $c): DeleteJournalController
            => new DeleteJournalController(
                $c->getTyped(DeleteJournalUseCase::class),
                $c->getTyped(AuthenticateBearer::class),
            ));
        $c->set(ApproveJournalController::class, static fn (Container $c): ApproveJournalController
            => new ApproveJournalController(
                $c->getTyped(ApproveJournalUseCase::class),
                $c->getTyped(AuthenticateBearer::class),
            ));
        $c->set(GetTrialBalanceController::class, static fn (Container $c): GetTrialBalanceController
            => new GetTrialBalanceController(
                $c->getTyped(QueryTrialBalanceUseCase::class),
                $c->getTyped(AuthenticateBearer::class),
                $c->getTyped(PDO::class),
            ));

        // --- Ledger (Phase 6 Wave 6-C) ---
        $c->set(
            LedgerGeneratorInterface::class,
            static function (Container $c): LedgerGeneratorInterface {
                unset($c);
                $repoRoot = dirname(__DIR__, 3);
                $templateDir = $repoRoot . DIRECTORY_SEPARATOR . 'storage'
                    . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'ledger';
                $compileDir = $repoRoot . DIRECTORY_SEPARATOR . 'storage'
                    . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'smarty_ledger';
                $fontDir = $repoRoot . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'fonts';
                if (!is_dir($compileDir)) {
                    @mkdir($compileDir, 0775, true);
                }
                if (!is_dir($fontDir)) {
                    @mkdir($fontDir, 0775, true);
                }
                return new DompdfLedgerGenerator(
                    templateDir: $templateDir,
                    compileDir: $compileDir,
                    fontDir: $fontDir,
                );
            },
        );
        $c->set(
            GetLedgerController::class,
            static fn (Container $c): GetLedgerController
                => new GetLedgerController(
                    useCase: $c->getTyped(QueryLedgerUseCase::class),
                    generator: $c->getTyped(LedgerGeneratorInterface::class),
                    auth: $c->getTyped(AuthenticateBearer::class),
                    pdo: $c->getTyped(PDO::class),
                ),
        );

        // --- Financial Statements (Phase 6.6 + Phase 6-A port) ---
        $c->set(
            AccountTitleFsMappingRepositoryInterface::class,
            static fn (Container $c): AccountTitleFsMappingRepositoryInterface
                => new PdoAccountTitleFsMappingRepository($c->getTyped(PDO::class)),
        );
        $c->set(
            FsSectionDefinitionRepositoryInterface::class,
            static fn (Container $c): FsSectionDefinitionRepositoryInterface
                => new PdoFsSectionDefinitionRepository($c->getTyped(PDO::class)),
        );
        $c->set(
            FinancialStatementBuilder::class,
            static fn (Container $c): FinancialStatementBuilder => new FinancialStatementBuilder(),
        );
        // --- Wave 6-B: CS (Cash Flow Statement, indirect method) ---
        $c->set(
            AccountTitleCsMappingRepositoryInterface::class,
            static fn (Container $c): AccountTitleCsMappingRepositoryInterface
                => new PdoAccountTitleCsMappingRepository($c->getTyped(PDO::class)),
        );
        $c->set(
            CsSectionDefinitionRepositoryInterface::class,
            static fn (Container $c): CsSectionDefinitionRepositoryInterface
                => new PdoCsSectionDefinitionRepository($c->getTyped(PDO::class)),
        );
        $c->set(
            CashFlowStatementBuilder::class,
            static fn (Container $c): CashFlowStatementBuilder => new CashFlowStatementBuilder(),
        );
        $c->set(
            GenerateFinancialStatementUseCase::class,
            static fn (Container $c): GenerateFinancialStatementUseCase
                => new GenerateFinancialStatementUseCase(
                    trialBalance: $c->getTyped(QueryTrialBalanceUseCase::class),
                    accounts: $c->getTyped(AccountTitleRepositoryInterface::class),
                    clock: $c->getTyped(ClockInterface::class),
                    mappings: $c->getTyped(AccountTitleFsMappingRepositoryInterface::class),
                    definitions: $c->getTyped(FsSectionDefinitionRepositoryInterface::class),
                    builder: $c->getTyped(FinancialStatementBuilder::class),
                    csMappings: $c->getTyped(AccountTitleCsMappingRepositoryInterface::class),
                    csDefinitions: $c->getTyped(CsSectionDefinitionRepositoryInterface::class),
                    csBuilder: $c->getTyped(CashFlowStatementBuilder::class),
                ),
        );
        $c->set(
            FinancialStatementGeneratorInterface::class,
            static function (Container $c): FinancialStatementGeneratorInterface {
                unset($c);
                $repoRoot = dirname(__DIR__, 3);
                $templateDir = $repoRoot . DIRECTORY_SEPARATOR . 'storage'
                    . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'fs';
                $compileDir = $repoRoot . DIRECTORY_SEPARATOR . 'storage'
                    . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'smarty_fs';
                $fontDir = $repoRoot . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'fonts';
                if (!is_dir($compileDir)) {
                    @mkdir($compileDir, 0775, true);
                }
                if (!is_dir($fontDir)) {
                    @mkdir($fontDir, 0775, true);
                }
                return new DompdfFinancialStatementGenerator(
                    templateDir: $templateDir,
                    compileDir: $compileDir,
                    fontDir: $fontDir,
                );
            },
        );
        $c->set(
            GetFinancialStatementController::class,
            static fn (Container $c): GetFinancialStatementController
                => new GetFinancialStatementController(
                    useCase: $c->getTyped(GenerateFinancialStatementUseCase::class),
                    generator: $c->getTyped(FinancialStatementGeneratorInterface::class),
                    auth: $c->getTyped(AuthenticateBearer::class),
                    pdo: $c->getTyped(PDO::class),
                ),
        );

        // --- Wave 6-I: Multi-period comparison FS ---
        $c->set(
            FiscalTermMetadataRepositoryInterface::class,
            static fn (Container $c): FiscalTermMetadataRepositoryInterface
                => new PdoFiscalTermMetadataRepository($c->getTyped(PDO::class)),
        );
        $c->set(
            FinancialStatementProviderInterface::class,
            static fn (Container $c): FinancialStatementProviderInterface
                => new class ($c->getTyped(GenerateFinancialStatementUseCase::class))
                    implements FinancialStatementProviderInterface {
                    public function __construct(
                        private readonly GenerateFinancialStatementUseCase $inner,
                    ) {
                    }
                    public function provide(
                        \Rucaro\Application\FinancialStatement\GenerateFinancialStatementUseCaseInput $input,
                    ): \Rucaro\Domain\FinancialStatement\FinancialStatement {
                        return $this->inner->execute($input);
                    }
                },
        );
        $c->set(
            GenerateMultiPeriodFinancialStatementUseCase::class,
            static fn (Container $c): GenerateMultiPeriodFinancialStatementUseCase
                => new GenerateMultiPeriodFinancialStatementUseCase(
                    provider: $c->getTyped(FinancialStatementProviderInterface::class),
                    fiscalTerms: $c->getTyped(FiscalTermMetadataRepositoryInterface::class),
                    clock: $c->getTyped(ClockInterface::class),
                ),
        );
        $c->set(
            MultiPeriodFinancialStatementGeneratorInterface::class,
            static function (Container $c): MultiPeriodFinancialStatementGeneratorInterface {
                unset($c);
                $repoRoot = dirname(__DIR__, 3);
                $templateDir = $repoRoot . DIRECTORY_SEPARATOR . 'storage'
                    . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'fs_multi';
                $compileDir = $repoRoot . DIRECTORY_SEPARATOR . 'storage'
                    . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'smarty_fs_multi';
                $fontDir = $repoRoot . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'fonts';
                if (!is_dir($compileDir)) {
                    @mkdir($compileDir, 0775, true);
                }
                if (!is_dir($fontDir)) {
                    @mkdir($fontDir, 0775, true);
                }
                return new DompdfMultiPeriodFinancialStatementGenerator(
                    templateDir: $templateDir,
                    compileDir: $compileDir,
                    fontDir: $fontDir,
                );
            },
        );
        $c->set(
            GetMultiPeriodFinancialStatementController::class,
            static fn (Container $c): GetMultiPeriodFinancialStatementController
                => new GetMultiPeriodFinancialStatementController(
                    useCase: $c->getTyped(GenerateMultiPeriodFinancialStatementUseCase::class),
                    generator: $c->getTyped(MultiPeriodFinancialStatementGeneratorInterface::class),
                    auth: $c->getTyped(AuthenticateBearer::class),
                ),
        );

        // --- Approval pipeline (Phase 5.2) ---
        $c->set(
            ApprovalTokenRepositoryInterface::class,
            static fn (Container $c): ApprovalTokenRepositoryInterface
                => new PdoApprovalTokenRepository($c->getTyped(PDO::class)),
        );
        $c->set(
            ApprovalTargetResolverInterface::class,
            static fn (Container $c): ApprovalTargetResolverInterface
                => new JournalApprovalTargetResolver($c->getTyped(JournalRepositoryInterface::class)),
        );
        $c->set(
            MailSenderInterface::class,
            static function (Container $c): MailSenderInterface {
                $mailer = (string) (getenv('MAIL_MAILER') ?: 'null');
                switch (strtolower($mailer)) {
                    case 'smtp':
                    case 'sendmail':
                    case 'mailer':
                        $dsn = (string) (getenv('MAIL_DSN') ?: '');
                        $from = (string) (getenv('MAIL_FROM') ?: 'no-reply@example.com');
                        $fromName = (string) (getenv('MAIL_FROM_NAME') ?: '');
                        return new SymfonyMailSender($dsn, $from, $fromName);
                    case 'memory':
                    case 'array':
                        return new InMemoryMailSender();
                    case 'null':
                    case '':
                    default:
                        unset($c);
                        return new NullMailSender();
                }
            },
        );
        $c->set(
            MessagingChannelInterface::class,
            static fn (Container $c): MessagingChannelInterface => new NullMessagingChannel(),
        );
        $c->set(
            ApprovalNotifierInterface::class,
            static function (Container $c): ApprovalNotifierInterface {
                $appUrl = (string) (getenv('APP_URL') ?: 'http://localhost:8080');
                $approveTpl = (string) (getenv('APPROVAL_APPROVE_URL_TEMPLATE') ?: ($appUrl . '/api/v1/approvals/?token={token}&decision=approved'));
                $rejectTpl = (string) (getenv('APPROVAL_REJECT_URL_TEMPLATE') ?: ($appUrl . '/api/v1/approvals/?token={token}&decision=rejected'));
                $repoRoot = dirname(__DIR__, 3);
                $templateDir = $repoRoot . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'mail' . DIRECTORY_SEPARATOR . 'approval';
                $compileDir = $repoRoot . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'smarty_compile';
                if (!is_dir($compileDir)) {
                    @mkdir($compileDir, 0775, true);
                }
                return new DefaultApprovalNotifier(
                    mail: $c->getTyped(MailSenderInterface::class),
                    messaging: $c->getTyped(MessagingChannelInterface::class),
                    appUrl: $appUrl,
                    approveUrlTemplate: $approveTpl,
                    rejectUrlTemplate: $rejectTpl,
                    templateDir: $templateDir,
                    compileDir: $compileDir,
                );
            },
        );
        $c->set(
            IssueApprovalTokenUseCase::class,
            static function (Container $c): IssueApprovalTokenUseCase {
                $ttlEnv = getenv('APPROVAL_TTL_HOURS');
                $ttl = IssueApprovalTokenUseCase::DEFAULT_TTL_HOURS;
                if ($ttlEnv !== false && $ttlEnv !== '' && ctype_digit($ttlEnv)) {
                    $parsed = (int) $ttlEnv;
                    if ($parsed >= 1) {
                        $ttl = $parsed;
                    }
                }
                return new IssueApprovalTokenUseCase(
                    tokens: $c->getTyped(ApprovalTokenRepositoryInterface::class),
                    targets: $c->getTyped(ApprovalTargetResolverInterface::class),
                    notifier: $c->getTyped(ApprovalNotifierInterface::class),
                    tokenGenerator: $c->getTyped(BearerTokenGenerator::class),
                    ulids: $c->getTyped(UlidGenerator::class),
                    clock: $c->getTyped(ClockInterface::class),
                    defaultTtlHours: $ttl,
                );
            },
        );
        $c->set(
            FindApprovalByTokenUseCase::class,
            static fn (Container $c): FindApprovalByTokenUseCase
                => new FindApprovalByTokenUseCase(
                    tokens: $c->getTyped(ApprovalTokenRepositoryInterface::class),
                    targets: $c->getTyped(ApprovalTargetResolverInterface::class),
                    clock: $c->getTyped(ClockInterface::class),
                ),
        );
        $c->set(
            RespondToApprovalUseCase::class,
            static fn (Container $c): RespondToApprovalUseCase
                => new RespondToApprovalUseCase(
                    tokens: $c->getTyped(ApprovalTokenRepositoryInterface::class),
                    targets: $c->getTyped(ApprovalTargetResolverInterface::class),
                    clock: $c->getTyped(ClockInterface::class),
                ),
        );
        $c->set(
            ResendApprovalUseCase::class,
            static fn (Container $c): ResendApprovalUseCase
                => new ResendApprovalUseCase(
                    tokens: $c->getTyped(ApprovalTokenRepositoryInterface::class),
                    issue: $c->getTyped(IssueApprovalTokenUseCase::class),
                    targets: $c->getTyped(ApprovalTargetResolverInterface::class),
                    clock: $c->getTyped(ClockInterface::class),
                ),
        );
        $c->set(
            ExpirePastDueApprovalsUseCase::class,
            static fn (Container $c): ExpirePastDueApprovalsUseCase
                => new ExpirePastDueApprovalsUseCase(
                    tokens: $c->getTyped(ApprovalTokenRepositoryInterface::class),
                    clock: $c->getTyped(ClockInterface::class),
                ),
        );
        $c->set(
            RequestApprovalController::class,
            static fn (Container $c): RequestApprovalController
                => new RequestApprovalController(
                    $c->getTyped(IssueApprovalTokenUseCase::class),
                    $c->getTyped(AuthenticateBearer::class),
                ),
        );
        $c->set(
            GetApprovalController::class,
            static fn (Container $c): GetApprovalController
                => new GetApprovalController($c->getTyped(FindApprovalByTokenUseCase::class)),
        );
        $c->set(
            PostApprovalController::class,
            static fn (Container $c): PostApprovalController
                => new PostApprovalController($c->getTyped(RespondToApprovalUseCase::class)),
        );
        $c->set(
            ResendApprovalController::class,
            static fn (Container $c): ResendApprovalController
                => new ResendApprovalController(
                    $c->getTyped(ResendApprovalUseCase::class),
                    $c->getTyped(AuthenticateBearer::class),
                ),
        );

        // --- Fixed assets (Phase 6 Wave 6-D, ADR-012) ---
        $c->set(
            FixedAssetRepositoryInterface::class,
            static fn (Container $c): FixedAssetRepositoryInterface
                => new PdoFixedAssetRepository($c->getTyped(PDO::class)),
        );
        $c->set(
            DepreciationScheduleRepositoryInterface::class,
            static fn (Container $c): DepreciationScheduleRepositoryInterface
                => new PdoDepreciationScheduleRepository($c->getTyped(PDO::class)),
        );
        $c->set(
            FixedAssetCategoryRepositoryInterface::class,
            static fn (Container $c): FixedAssetCategoryRepositoryInterface
                => new PdoFixedAssetCategoryRepository($c->getTyped(PDO::class)),
        );
        $c->set(
            DepreciationCalculatorFactory::class,
            static function (Container $c): DepreciationCalculatorFactory {
                unset($c);
                return new DepreciationCalculatorFactory();
            },
        );
        $c->set(
            FixedAssetLedgerGeneratorInterface::class,
            static function (Container $c): FixedAssetLedgerGeneratorInterface {
                unset($c);
                $repoRoot = dirname(__DIR__, 3);
                $templateDir = $repoRoot . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'fixed_asset';
                $compileDir = $repoRoot . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'smarty_fixed_asset';
                $fontDir = $repoRoot . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'fonts';
                if (!is_dir($compileDir)) {
                    @mkdir($compileDir, 0775, true);
                }
                if (!is_dir($fontDir)) {
                    @mkdir($fontDir, 0775, true);
                }
                return new DompdfFixedAssetLedgerGenerator(
                    templateDir: $templateDir,
                    compileDir: $compileDir,
                    fontDir: $fontDir,
                );
            },
        );
        $c->set(CreateFixedAssetUseCase::class, static fn (Container $c): CreateFixedAssetUseCase
            => new CreateFixedAssetUseCase(
                assets: $c->getTyped(FixedAssetRepositoryInterface::class),
                ulids: $c->getTyped(UlidGenerator::class),
                clock: $c->getTyped(ClockInterface::class),
            ));
        $c->set(UpdateFixedAssetUseCase::class, static fn (Container $c): UpdateFixedAssetUseCase
            => new UpdateFixedAssetUseCase(
                assets: $c->getTyped(FixedAssetRepositoryInterface::class),
                clock: $c->getTyped(ClockInterface::class),
            ));
        $c->set(DisposeFixedAssetUseCase::class, static fn (Container $c): DisposeFixedAssetUseCase
            => new DisposeFixedAssetUseCase(
                assets: $c->getTyped(FixedAssetRepositoryInterface::class),
            ));
        $c->set(ListFixedAssetsUseCase::class, static fn (Container $c): ListFixedAssetsUseCase
            => new ListFixedAssetsUseCase(
                assets: $c->getTyped(FixedAssetRepositoryInterface::class),
            ));
        $c->set(GetFixedAssetUseCase::class, static fn (Container $c): GetFixedAssetUseCase
            => new GetFixedAssetUseCase(
                assets: $c->getTyped(FixedAssetRepositoryInterface::class),
            ));
        $c->set(GenerateDepreciationScheduleUseCase::class, static fn (Container $c): GenerateDepreciationScheduleUseCase
            => new GenerateDepreciationScheduleUseCase(
                assets: $c->getTyped(FixedAssetRepositoryInterface::class),
                schedules: $c->getTyped(DepreciationScheduleRepositoryInterface::class),
                calculatorFactory: $c->getTyped(DepreciationCalculatorFactory::class),
                ulids: $c->getTyped(UlidGenerator::class),
                clock: $c->getTyped(ClockInterface::class),
            ));
        $c->set(PostDepreciationJournalUseCase::class, static fn (Container $c): PostDepreciationJournalUseCase
            => new PostDepreciationJournalUseCase(
                assets: $c->getTyped(FixedAssetRepositoryInterface::class),
                schedules: $c->getTyped(DepreciationScheduleRepositoryInterface::class),
                journals: $c->getTyped(JournalRepositoryInterface::class),
                ulids: $c->getTyped(UlidGenerator::class),
                clock: $c->getTyped(ClockInterface::class),
            ));
        $c->set(GetFixedAssetLedgerUseCase::class, static fn (Container $c): GetFixedAssetLedgerUseCase
            => new GetFixedAssetLedgerUseCase(
                assets: $c->getTyped(FixedAssetRepositoryInterface::class),
                schedules: $c->getTyped(DepreciationScheduleRepositoryInterface::class),
                clock: $c->getTyped(ClockInterface::class),
            ));
        $c->set(ListFixedAssetController::class, static fn (Container $c): ListFixedAssetController
            => new ListFixedAssetController(
                $c->getTyped(ListFixedAssetsUseCase::class),
                $c->getTyped(AuthenticateBearer::class),
            ));
        $c->set(GetFixedAssetController::class, static fn (Container $c): GetFixedAssetController
            => new GetFixedAssetController(
                $c->getTyped(GetFixedAssetUseCase::class),
                $c->getTyped(AuthenticateBearer::class),
            ));
        $c->set(CreateFixedAssetController::class, static fn (Container $c): CreateFixedAssetController
            => new CreateFixedAssetController(
                $c->getTyped(CreateFixedAssetUseCase::class),
                $c->getTyped(AuthenticateBearer::class),
            ));
        $c->set(UpdateFixedAssetController::class, static fn (Container $c): UpdateFixedAssetController
            => new UpdateFixedAssetController(
                $c->getTyped(UpdateFixedAssetUseCase::class),
                $c->getTyped(AuthenticateBearer::class),
            ));
        $c->set(DisposeFixedAssetController::class, static fn (Container $c): DisposeFixedAssetController
            => new DisposeFixedAssetController(
                $c->getTyped(DisposeFixedAssetUseCase::class),
                $c->getTyped(AuthenticateBearer::class),
            ));
        $c->set(GenerateDepreciationController::class, static fn (Container $c): GenerateDepreciationController
            => new GenerateDepreciationController(
                $c->getTyped(GenerateDepreciationScheduleUseCase::class),
                $c->getTyped(AuthenticateBearer::class),
                $c->getTyped(PDO::class),
            ));
        $c->set(PostDepreciationJournalController::class, static fn (Container $c): PostDepreciationJournalController
            => new PostDepreciationJournalController(
                $c->getTyped(PostDepreciationJournalUseCase::class),
                $c->getTyped(AuthenticateBearer::class),
            ));
        $c->set(GetFixedAssetLedgerController::class, static fn (Container $c): GetFixedAssetLedgerController
            => new GetFixedAssetLedgerController(
                $c->getTyped(GetFixedAssetLedgerUseCase::class),
                $c->getTyped(FixedAssetLedgerGeneratorInterface::class),
                $c->getTyped(AuthenticateBearer::class),
            ));

        // --- Cash Plan (Phase 6 Wave 6-E, ADR-013) ---
        $c->set(
            CashPlanRepositoryInterface::class,
            static fn (Container $c): CashPlanRepositoryInterface
                => new PdoCashPlanRepository($c->getTyped(PDO::class)),
        );
        $c->set(
            CashPlanPdfGeneratorInterface::class,
            static function (Container $c): CashPlanPdfGeneratorInterface {
                unset($c);
                $repoRoot = dirname(__DIR__, 3);
                $templateDir = $repoRoot . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'cash_plan';
                $compileDir = $repoRoot . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'smarty_cash_plan';
                $fontDir = $repoRoot . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'fonts';
                if (!is_dir($compileDir)) {
                    @mkdir($compileDir, 0775, true);
                }
                if (!is_dir($fontDir)) {
                    @mkdir($fontDir, 0775, true);
                }
                return new DompdfCashPlanGenerator(
                    templateDir: $templateDir,
                    compileDir: $compileDir,
                    fontDir: $fontDir,
                );
            },
        );
        $c->set(CreateCashPlanUseCase::class, static fn (Container $c): CreateCashPlanUseCase
            => new CreateCashPlanUseCase(
                plans: $c->getTyped(CashPlanRepositoryInterface::class),
                ulids: $c->getTyped(UlidGenerator::class),
                clock: $c->getTyped(ClockInterface::class),
            ));
        $c->set(UpdateCashPlanUseCase::class, static fn (Container $c): UpdateCashPlanUseCase
            => new UpdateCashPlanUseCase(
                plans: $c->getTyped(CashPlanRepositoryInterface::class),
                ulids: $c->getTyped(UlidGenerator::class),
                clock: $c->getTyped(ClockInterface::class),
            ));
        $c->set(ListCashPlansUseCase::class, static fn (Container $c): ListCashPlansUseCase
            => new ListCashPlansUseCase($c->getTyped(CashPlanRepositoryInterface::class)));
        $c->set(GetCashPlanUseCase::class, static fn (Container $c): GetCashPlanUseCase
            => new GetCashPlanUseCase($c->getTyped(CashPlanRepositoryInterface::class)));
        $c->set(DeleteCashPlanUseCase::class, static fn (Container $c): DeleteCashPlanUseCase
            => new DeleteCashPlanUseCase($c->getTyped(CashPlanRepositoryInterface::class)));
        $c->set(ListCashPlanController::class, static fn (Container $c): ListCashPlanController
            => new ListCashPlanController(
                $c->getTyped(ListCashPlansUseCase::class),
                $c->getTyped(AuthenticateBearer::class),
            ));
        $c->set(GetCashPlanController::class, static fn (Container $c): GetCashPlanController
            => new GetCashPlanController(
                $c->getTyped(GetCashPlanUseCase::class),
                $c->getTyped(CashPlanPdfGeneratorInterface::class),
                $c->getTyped(AuthenticateBearer::class),
            ));
        $c->set(CreateCashPlanController::class, static fn (Container $c): CreateCashPlanController
            => new CreateCashPlanController(
                $c->getTyped(CreateCashPlanUseCase::class),
                $c->getTyped(AuthenticateBearer::class),
            ));
        $c->set(UpdateCashPlanController::class, static fn (Container $c): UpdateCashPlanController
            => new UpdateCashPlanController(
                $c->getTyped(UpdateCashPlanUseCase::class),
                $c->getTyped(AuthenticateBearer::class),
            ));
        $c->set(DeleteCashPlanController::class, static fn (Container $c): DeleteCashPlanController
            => new DeleteCashPlanController(
                $c->getTyped(DeleteCashPlanUseCase::class),
                $c->getTyped(AuthenticateBearer::class),
            ));

        // --- Break-Even Point (Phase 6 Wave 6-E, ADR-013) ---
        $c->set(
            AccountTitleCvpClassificationRepositoryInterface::class,
            static fn (Container $c): AccountTitleCvpClassificationRepositoryInterface
                => new PdoAccountTitleCvpClassificationRepository(
                    $c->getTyped(PDO::class),
                    $c->getTyped(UlidGenerator::class),
                ),
        );
        $c->set(
            BreakEvenPointCalculator::class,
            static function (Container $c): BreakEvenPointCalculator {
                unset($c);
                return new BreakEvenPointCalculator();
            },
        );
        $c->set(
            BreakEvenPointPdfGeneratorInterface::class,
            static function (Container $c): BreakEvenPointPdfGeneratorInterface {
                unset($c);
                $repoRoot = dirname(__DIR__, 3);
                $templateDir = $repoRoot . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'break_even_point';
                $compileDir = $repoRoot . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'smarty_bep';
                $fontDir = $repoRoot . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'fonts';
                if (!is_dir($compileDir)) {
                    @mkdir($compileDir, 0775, true);
                }
                if (!is_dir($fontDir)) {
                    @mkdir($fontDir, 0775, true);
                }
                return new DompdfBreakEvenPointGenerator(
                    templateDir: $templateDir,
                    compileDir: $compileDir,
                    fontDir: $fontDir,
                );
            },
        );
        $c->set(AnalyzeBreakEvenPointUseCase::class, static fn (Container $c): AnalyzeBreakEvenPointUseCase
            => new AnalyzeBreakEvenPointUseCase(
                trialBalance: $c->getTyped(QueryTrialBalanceUseCase::class),
                classifications: $c->getTyped(AccountTitleCvpClassificationRepositoryInterface::class),
                calculator: $c->getTyped(BreakEvenPointCalculator::class),
                clock: $c->getTyped(ClockInterface::class),
            ));
        $c->set(ListCvpClassificationsUseCase::class, static fn (Container $c): ListCvpClassificationsUseCase
            => new ListCvpClassificationsUseCase($c->getTyped(AccountTitleCvpClassificationRepositoryInterface::class)));
        $c->set(UpsertCvpClassificationsUseCase::class, static fn (Container $c): UpsertCvpClassificationsUseCase
            => new UpsertCvpClassificationsUseCase($c->getTyped(AccountTitleCvpClassificationRepositoryInterface::class)));
        $c->set(GetBreakEvenPointController::class, static fn (Container $c): GetBreakEvenPointController
            => new GetBreakEvenPointController(
                $c->getTyped(AnalyzeBreakEvenPointUseCase::class),
                $c->getTyped(BreakEvenPointPdfGeneratorInterface::class),
                $c->getTyped(AuthenticateBearer::class),
            ));
        $c->set(ListCvpClassificationController::class, static fn (Container $c): ListCvpClassificationController
            => new ListCvpClassificationController(
                $c->getTyped(ListCvpClassificationsUseCase::class),
                $c->getTyped(AuthenticateBearer::class),
            ));
        $c->set(PutCvpClassificationController::class, static fn (Container $c): PutCvpClassificationController
            => new PutCvpClassificationController(
                $c->getTyped(UpsertCvpClassificationsUseCase::class),
                $c->getTyped(AuthenticateBearer::class),
            ));

        // --- Consumption Tax (Phase 6 Wave 6-F, ADR-014) ---
        $c->set(
            ConsumptionTaxRateRepositoryInterface::class,
            static fn (Container $c): ConsumptionTaxRateRepositoryInterface
                => new PdoConsumptionTaxRateRepository($c->getTyped(PDO::class)),
        );
        $c->set(
            ConsumptionTaxCategoryRepositoryInterface::class,
            static fn (Container $c): ConsumptionTaxCategoryRepositoryInterface
                => new PdoConsumptionTaxCategoryRepository($c->getTyped(PDO::class)),
        );
        $c->set(
            AccountTitleConsumptionTaxDefaultRepositoryInterface::class,
            static fn (Container $c): AccountTitleConsumptionTaxDefaultRepositoryInterface
                => new PdoAccountTitleConsumptionTaxDefaultRepository($c->getTyped(PDO::class)),
        );
        $c->set(
            InvoiceRegistrationRepositoryInterface::class,
            static fn (Container $c): InvoiceRegistrationRepositoryInterface
                => new PdoInvoiceRegistrationRepository($c->getTyped(PDO::class)),
        );
        $c->set(
            ConsumptionTaxPeriodRepositoryInterface::class,
            static fn (Container $c): ConsumptionTaxPeriodRepositoryInterface
                => new PdoConsumptionTaxPeriodRepository($c->getTyped(PDO::class)),
        );
        $c->set(
            TaxableTransactionQueryInterface::class,
            static fn (Container $c): TaxableTransactionQueryInterface
                => new PdoTaxableTransactionQueryService($c->getTyped(PDO::class)),
        );
        $c->set(InvoiceDeductionCalculator::class, static fn (Container $c): InvoiceDeductionCalculator => new InvoiceDeductionCalculator());
        $c->set(ConsumptionTaxCalculatorFactory::class, static fn (Container $c): ConsumptionTaxCalculatorFactory
            => new ConsumptionTaxCalculatorFactory($c->getTyped(InvoiceDeductionCalculator::class)));
        $c->set(
            ConsumptionTaxReportGeneratorInterface::class,
            static function (Container $c): ConsumptionTaxReportGeneratorInterface {
                unset($c);
                $repoRoot = dirname(__DIR__, 3);
                $templateDir = $repoRoot . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'consumption_tax';
                $compileDir = $repoRoot . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'smarty_consumption_tax';
                $fontDir = $repoRoot . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'fonts';
                if (!is_dir($compileDir)) {
                    @mkdir($compileDir, 0775, true);
                }
                if (!is_dir($fontDir)) {
                    @mkdir($fontDir, 0775, true);
                }
                return new DompdfConsumptionTaxReportGenerator(
                    templateDir: $templateDir,
                    compileDir: $compileDir,
                    fontDir: $fontDir,
                );
            },
        );

        // Use cases
        $c->set(ListConsumptionTaxRatesUseCase::class, static fn (Container $c): ListConsumptionTaxRatesUseCase
            => new ListConsumptionTaxRatesUseCase($c->getTyped(ConsumptionTaxRateRepositoryInterface::class)));
        $c->set(ListConsumptionTaxCategoriesUseCase::class, static fn (Container $c): ListConsumptionTaxCategoriesUseCase
            => new ListConsumptionTaxCategoriesUseCase($c->getTyped(ConsumptionTaxCategoryRepositoryInterface::class)));
        $c->set(ListAccountTitleTaxDefaultsUseCase::class, static fn (Container $c): ListAccountTitleTaxDefaultsUseCase
            => new ListAccountTitleTaxDefaultsUseCase($c->getTyped(AccountTitleConsumptionTaxDefaultRepositoryInterface::class)));
        $c->set(UpsertAccountTitleTaxDefaultsUseCase::class, static fn (Container $c): UpsertAccountTitleTaxDefaultsUseCase
            => new UpsertAccountTitleTaxDefaultsUseCase(
                defaults: $c->getTyped(AccountTitleConsumptionTaxDefaultRepositoryInterface::class),
                ulids: $c->getTyped(UlidGenerator::class),
                clock: $c->getTyped(ClockInterface::class),
            ));
        $c->set(ListInvoiceRegistrationsUseCase::class, static fn (Container $c): ListInvoiceRegistrationsUseCase
            => new ListInvoiceRegistrationsUseCase($c->getTyped(InvoiceRegistrationRepositoryInterface::class)));
        $c->set(UpsertInvoiceRegistrationUseCase::class, static fn (Container $c): UpsertInvoiceRegistrationUseCase
            => new UpsertInvoiceRegistrationUseCase(
                registrations: $c->getTyped(InvoiceRegistrationRepositoryInterface::class),
                ulids: $c->getTyped(UlidGenerator::class),
                clock: $c->getTyped(ClockInterface::class),
            ));
        $c->set(ListConsumptionTaxPeriodsUseCase::class, static fn (Container $c): ListConsumptionTaxPeriodsUseCase
            => new ListConsumptionTaxPeriodsUseCase($c->getTyped(ConsumptionTaxPeriodRepositoryInterface::class)));
        $c->set(CreateConsumptionTaxPeriodUseCase::class, static fn (Container $c): CreateConsumptionTaxPeriodUseCase
            => new CreateConsumptionTaxPeriodUseCase(
                periods: $c->getTyped(ConsumptionTaxPeriodRepositoryInterface::class),
                ulids: $c->getTyped(UlidGenerator::class),
                clock: $c->getTyped(ClockInterface::class),
            ));
        $c->set(CalculateConsumptionTaxUseCase::class, static fn (Container $c): CalculateConsumptionTaxUseCase
            => new CalculateConsumptionTaxUseCase(
                periods: $c->getTyped(ConsumptionTaxPeriodRepositoryInterface::class),
                transactions: $c->getTyped(TaxableTransactionQueryInterface::class),
                factory: $c->getTyped(ConsumptionTaxCalculatorFactory::class),
            ));
        $c->set(GenerateConsumptionTaxReportUseCase::class, static fn (Container $c): GenerateConsumptionTaxReportUseCase
            => new GenerateConsumptionTaxReportUseCase(
                calculate: $c->getTyped(CalculateConsumptionTaxUseCase::class),
            ));

        // Controllers
        $c->set(ListConsumptionTaxRatesController::class, static fn (Container $c): ListConsumptionTaxRatesController
            => new ListConsumptionTaxRatesController(
                $c->getTyped(ListConsumptionTaxRatesUseCase::class),
                $c->getTyped(AuthenticateBearer::class),
            ));
        $c->set(ListConsumptionTaxCategoriesController::class, static fn (Container $c): ListConsumptionTaxCategoriesController
            => new ListConsumptionTaxCategoriesController(
                $c->getTyped(ListConsumptionTaxCategoriesUseCase::class),
                $c->getTyped(AuthenticateBearer::class),
            ));
        $c->set(ListAccountTitleTaxDefaultsController::class, static fn (Container $c): ListAccountTitleTaxDefaultsController
            => new ListAccountTitleTaxDefaultsController(
                $c->getTyped(ListAccountTitleTaxDefaultsUseCase::class),
                $c->getTyped(AuthenticateBearer::class),
            ));
        $c->set(PutAccountTitleTaxDefaultsController::class, static fn (Container $c): PutAccountTitleTaxDefaultsController
            => new PutAccountTitleTaxDefaultsController(
                $c->getTyped(UpsertAccountTitleTaxDefaultsUseCase::class),
                $c->getTyped(AuthenticateBearer::class),
            ));
        $c->set(ListInvoiceRegistrationsController::class, static fn (Container $c): ListInvoiceRegistrationsController
            => new ListInvoiceRegistrationsController(
                $c->getTyped(ListInvoiceRegistrationsUseCase::class),
                $c->getTyped(AuthenticateBearer::class),
            ));
        $c->set(UpsertInvoiceRegistrationController::class, static fn (Container $c): UpsertInvoiceRegistrationController
            => new UpsertInvoiceRegistrationController(
                $c->getTyped(UpsertInvoiceRegistrationUseCase::class),
                $c->getTyped(AuthenticateBearer::class),
            ));
        $c->set(ListConsumptionTaxPeriodsController::class, static fn (Container $c): ListConsumptionTaxPeriodsController
            => new ListConsumptionTaxPeriodsController(
                $c->getTyped(ListConsumptionTaxPeriodsUseCase::class),
                $c->getTyped(AuthenticateBearer::class),
            ));
        $c->set(CreateConsumptionTaxPeriodController::class, static fn (Container $c): CreateConsumptionTaxPeriodController
            => new CreateConsumptionTaxPeriodController(
                $c->getTyped(CreateConsumptionTaxPeriodUseCase::class),
                $c->getTyped(AuthenticateBearer::class),
            ));
        $c->set(CalculateConsumptionTaxController::class, static fn (Container $c): CalculateConsumptionTaxController
            => new CalculateConsumptionTaxController(
                $c->getTyped(CalculateConsumptionTaxUseCase::class),
                $c->getTyped(AuthenticateBearer::class),
            ));
        $c->set(GetConsumptionTaxReportController::class, static fn (Container $c): GetConsumptionTaxReportController
            => new GetConsumptionTaxReportController(
                $c->getTyped(GenerateConsumptionTaxReportUseCase::class),
                $c->getTyped(ConsumptionTaxReportGeneratorInterface::class),
                $c->getTyped(AuthenticateBearer::class),
            ));

        // --- Budget (Phase 6 Wave 6-G, ADR-015) ---
        $c->set(
            BudgetRepositoryInterface::class,
            static fn (Container $c): BudgetRepositoryInterface
                => new PdoBudgetRepository($c->getTyped(PDO::class)),
        );
        $c->set(
            BudgetPdfGeneratorInterface::class,
            static function (Container $c): BudgetPdfGeneratorInterface {
                unset($c);
                $repoRoot = dirname(__DIR__, 3);
                $templateDir = $repoRoot . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'budget';
                $compileDir = $repoRoot . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'smarty_budget';
                $fontDir = $repoRoot . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'fonts';
                if (!is_dir($compileDir)) {
                    @mkdir($compileDir, 0775, true);
                }
                if (!is_dir($fontDir)) {
                    @mkdir($fontDir, 0775, true);
                }
                return new DompdfBudgetGenerator(
                    templateDir: $templateDir,
                    compileDir: $compileDir,
                    fontDir: $fontDir,
                );
            },
        );
        $c->set(
            BudgetVariancePdfGeneratorInterface::class,
            static function (Container $c): BudgetVariancePdfGeneratorInterface {
                unset($c);
                $repoRoot = dirname(__DIR__, 3);
                $templateDir = $repoRoot . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'budget';
                $compileDir = $repoRoot . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'smarty_budget';
                $fontDir = $repoRoot . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'fonts';
                if (!is_dir($compileDir)) {
                    @mkdir($compileDir, 0775, true);
                }
                if (!is_dir($fontDir)) {
                    @mkdir($fontDir, 0775, true);
                }
                return new DompdfBudgetVarianceGenerator(
                    templateDir: $templateDir,
                    compileDir: $compileDir,
                    fontDir: $fontDir,
                );
            },
        );
        $c->set(CreateBudgetUseCase::class, static fn (Container $c): CreateBudgetUseCase
            => new CreateBudgetUseCase(
                budgets: $c->getTyped(BudgetRepositoryInterface::class),
                ulids: $c->getTyped(UlidGenerator::class),
                clock: $c->getTyped(ClockInterface::class),
            ));
        $c->set(UpdateBudgetUseCase::class, static fn (Container $c): UpdateBudgetUseCase
            => new UpdateBudgetUseCase(
                budgets: $c->getTyped(BudgetRepositoryInterface::class),
                ulids: $c->getTyped(UlidGenerator::class),
                clock: $c->getTyped(ClockInterface::class),
            ));
        $c->set(ApproveBudgetUseCase::class, static fn (Container $c): ApproveBudgetUseCase
            => new ApproveBudgetUseCase(
                budgets: $c->getTyped(BudgetRepositoryInterface::class),
                clock: $c->getTyped(ClockInterface::class),
            ));
        $c->set(LockBudgetUseCase::class, static fn (Container $c): LockBudgetUseCase
            => new LockBudgetUseCase(
                budgets: $c->getTyped(BudgetRepositoryInterface::class),
                clock: $c->getTyped(ClockInterface::class),
            ));
        $c->set(DeleteBudgetUseCase::class, static fn (Container $c): DeleteBudgetUseCase
            => new DeleteBudgetUseCase($c->getTyped(BudgetRepositoryInterface::class)));
        $c->set(ListBudgetsUseCase::class, static fn (Container $c): ListBudgetsUseCase
            => new ListBudgetsUseCase($c->getTyped(BudgetRepositoryInterface::class)));
        $c->set(GetBudgetUseCase::class, static fn (Container $c): GetBudgetUseCase
            => new GetBudgetUseCase($c->getTyped(BudgetRepositoryInterface::class)));
        $c->set(AnalyzeBudgetVarianceUseCase::class, static fn (Container $c): AnalyzeBudgetVarianceUseCase
            => new AnalyzeBudgetVarianceUseCase(
                budgets: $c->getTyped(BudgetRepositoryInterface::class),
                trialBalance: $c->getTyped(QueryTrialBalanceUseCase::class),
                clock: $c->getTyped(ClockInterface::class),
            ));
        $c->set(ListBudgetController::class, static fn (Container $c): ListBudgetController
            => new ListBudgetController(
                $c->getTyped(ListBudgetsUseCase::class),
                $c->getTyped(AuthenticateBearer::class),
            ));
        $c->set(GetBudgetController::class, static fn (Container $c): GetBudgetController
            => new GetBudgetController(
                $c->getTyped(GetBudgetUseCase::class),
                $c->getTyped(BudgetPdfGeneratorInterface::class),
                $c->getTyped(AuthenticateBearer::class),
            ));
        $c->set(CreateBudgetController::class, static fn (Container $c): CreateBudgetController
            => new CreateBudgetController(
                $c->getTyped(CreateBudgetUseCase::class),
                $c->getTyped(AuthenticateBearer::class),
            ));
        $c->set(UpdateBudgetController::class, static fn (Container $c): UpdateBudgetController
            => new UpdateBudgetController(
                $c->getTyped(UpdateBudgetUseCase::class),
                $c->getTyped(AuthenticateBearer::class),
            ));
        $c->set(DeleteBudgetController::class, static fn (Container $c): DeleteBudgetController
            => new DeleteBudgetController(
                $c->getTyped(DeleteBudgetUseCase::class),
                $c->getTyped(AuthenticateBearer::class),
            ));
        $c->set(ApproveBudgetController::class, static fn (Container $c): ApproveBudgetController
            => new ApproveBudgetController(
                $c->getTyped(ApproveBudgetUseCase::class),
                $c->getTyped(AuthenticateBearer::class),
            ));
        $c->set(LockBudgetController::class, static fn (Container $c): LockBudgetController
            => new LockBudgetController(
                $c->getTyped(LockBudgetUseCase::class),
                $c->getTyped(AuthenticateBearer::class),
            ));
        $c->set(GetBudgetVarianceController::class, static fn (Container $c): GetBudgetVarianceController
            => new GetBudgetVarianceController(
                $c->getTyped(GetBudgetUseCase::class),
                $c->getTyped(AnalyzeBudgetVarianceUseCase::class),
                $c->getTyped(BudgetVariancePdfGeneratorInterface::class),
                $c->getTyped(AuthenticateBearer::class),
                $c->getTyped(PDO::class),
            ));

        // --- Statement of Changes in Equity (Phase 6 Wave 6-H-2, ADR-017) ---
        $c->set(
            SsManualAdjustmentRepositoryInterface::class,
            static fn (Container $c): SsManualAdjustmentRepositoryInterface
                => new PdoSsManualAdjustmentRepository($c->getTyped(PDO::class)),
        );
        $c->set(
            StatementOfChangesInEquityBuilder::class,
            static fn (): StatementOfChangesInEquityBuilder => new StatementOfChangesInEquityBuilder(),
        );
        $c->set(
            StatementOfChangesInEquityPdfGeneratorInterface::class,
            static function (Container $c): StatementOfChangesInEquityPdfGeneratorInterface {
                unset($c);
                $repoRoot = dirname(__DIR__, 3);
                $templateDir = $repoRoot . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'ss';
                $compileDir = $repoRoot . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'smarty_ss';
                $fontDir = $repoRoot . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'fonts';
                if (!is_dir($compileDir)) {
                    @mkdir($compileDir, 0775, true);
                }
                if (!is_dir($fontDir)) {
                    @mkdir($fontDir, 0775, true);
                }
                return new DompdfStatementOfChangesInEquityGenerator(
                    templateDir: $templateDir,
                    compileDir: $compileDir,
                    fontDir: $fontDir,
                );
            },
        );
        $c->set(GenerateStatementOfChangesInEquityUseCase::class, static fn (Container $c): GenerateStatementOfChangesInEquityUseCase
            => new GenerateStatementOfChangesInEquityUseCase(
                repo: $c->getTyped(SsManualAdjustmentRepositoryInterface::class),
                builder: $c->getTyped(StatementOfChangesInEquityBuilder::class),
                clock: $c->getTyped(ClockInterface::class),
            ));
        $c->set(ListSsAdjustmentsUseCase::class, static fn (Container $c): ListSsAdjustmentsUseCase
            => new ListSsAdjustmentsUseCase($c->getTyped(SsManualAdjustmentRepositoryInterface::class)));
        $c->set(CreateSsAdjustmentUseCase::class, static fn (Container $c): CreateSsAdjustmentUseCase
            => new CreateSsAdjustmentUseCase(
                repo: $c->getTyped(SsManualAdjustmentRepositoryInterface::class),
                ulids: $c->getTyped(UlidGenerator::class),
            ));
        $c->set(UpdateSsAdjustmentUseCase::class, static fn (Container $c): UpdateSsAdjustmentUseCase
            => new UpdateSsAdjustmentUseCase($c->getTyped(SsManualAdjustmentRepositoryInterface::class)));
        $c->set(DeleteSsAdjustmentUseCase::class, static fn (Container $c): DeleteSsAdjustmentUseCase
            => new DeleteSsAdjustmentUseCase($c->getTyped(SsManualAdjustmentRepositoryInterface::class)));
        $c->set(GetStatementOfChangesInEquityController::class, static fn (Container $c): GetStatementOfChangesInEquityController
            => new GetStatementOfChangesInEquityController(
                $c->getTyped(GenerateStatementOfChangesInEquityUseCase::class),
                $c->getTyped(StatementOfChangesInEquityPdfGeneratorInterface::class),
                $c->getTyped(AuthenticateBearer::class),
            ));
        $c->set(ListSsAdjustmentsController::class, static fn (Container $c): ListSsAdjustmentsController
            => new ListSsAdjustmentsController(
                $c->getTyped(ListSsAdjustmentsUseCase::class),
                $c->getTyped(AuthenticateBearer::class),
            ));
        $c->set(CreateSsAdjustmentController::class, static fn (Container $c): CreateSsAdjustmentController
            => new CreateSsAdjustmentController(
                $c->getTyped(CreateSsAdjustmentUseCase::class),
                $c->getTyped(AuthenticateBearer::class),
            ));
        $c->set(UpdateSsAdjustmentController::class, static fn (Container $c): UpdateSsAdjustmentController
            => new UpdateSsAdjustmentController(
                $c->getTyped(UpdateSsAdjustmentUseCase::class),
                $c->getTyped(AuthenticateBearer::class),
            ));
        $c->set(DeleteSsAdjustmentController::class, static fn (Container $c): DeleteSsAdjustmentController
            => new DeleteSsAdjustmentController(
                $c->getTyped(DeleteSsAdjustmentUseCase::class),
                $c->getTyped(AuthenticateBearer::class),
            ));

        // --- Blue Return (Phase 6 Wave 6-H-1, ADR-016) ---
        $c->set(
            BlueReturnRepositoryInterface::class,
            static fn (Container $c): BlueReturnRepositoryInterface
                => new PdoBlueReturnRepository($c->getTyped(PDO::class)),
        );
        $c->set(
            BlueReturnPdfGeneratorInterface::class,
            static function (Container $c): BlueReturnPdfGeneratorInterface {
                unset($c);
                $repoRoot = dirname(__DIR__, 3);
                $templateDir = $repoRoot . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'blue_return';
                $compileDir = $repoRoot . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'smarty_blue_return';
                $fontDir = $repoRoot . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'fonts';
                if (!is_dir($compileDir)) {
                    @mkdir($compileDir, 0775, true);
                }
                if (!is_dir($fontDir)) {
                    @mkdir($fontDir, 0775, true);
                }
                return new DompdfBlueReturnGenerator(
                    templateDir: $templateDir,
                    compileDir: $compileDir,
                    fontDir: $fontDir,
                );
            },
        );
        $c->set(
            BlueReturnBuilder::class,
            static fn (): BlueReturnBuilder => new BlueReturnBuilder(),
        );
        $c->set(CreateBlueReturnUseCase::class, static fn (Container $c): CreateBlueReturnUseCase
            => new CreateBlueReturnUseCase(
                forms: $c->getTyped(BlueReturnRepositoryInterface::class),
                entities: $c->getTyped(EntityRepositoryInterface::class),
                ulids: $c->getTyped(UlidGenerator::class),
                clock: $c->getTyped(ClockInterface::class),
            ));
        $c->set(UpdateBlueReturnUseCase::class, static fn (Container $c): UpdateBlueReturnUseCase
            => new UpdateBlueReturnUseCase(
                forms: $c->getTyped(BlueReturnRepositoryInterface::class),
                clock: $c->getTyped(ClockInterface::class),
            ));
        $c->set(FinalizeBlueReturnUseCase::class, static fn (Container $c): FinalizeBlueReturnUseCase
            => new FinalizeBlueReturnUseCase(
                forms: $c->getTyped(BlueReturnRepositoryInterface::class),
                clock: $c->getTyped(ClockInterface::class),
            ));
        $c->set(DeleteBlueReturnUseCase::class, static fn (Container $c): DeleteBlueReturnUseCase
            => new DeleteBlueReturnUseCase($c->getTyped(BlueReturnRepositoryInterface::class)));
        $c->set(GetBlueReturnUseCase::class, static fn (Container $c): GetBlueReturnUseCase
            => new GetBlueReturnUseCase($c->getTyped(BlueReturnRepositoryInterface::class)));
        $c->set(ListBlueReturnsUseCase::class, static fn (Container $c): ListBlueReturnsUseCase
            => new ListBlueReturnsUseCase($c->getTyped(BlueReturnRepositoryInterface::class)));
        $c->set(GenerateBlueReturnSnapshotUseCase::class, static fn (Container $c): GenerateBlueReturnSnapshotUseCase
            => new GenerateBlueReturnSnapshotUseCase($c->getTyped(BlueReturnBuilder::class)));
        $c->set(CreateBlueReturnController::class, static fn (Container $c): CreateBlueReturnController
            => new CreateBlueReturnController(
                $c->getTyped(CreateBlueReturnUseCase::class),
                $c->getTyped(AuthenticateBearer::class),
            ));
        $c->set(UpdateBlueReturnController::class, static fn (Container $c): UpdateBlueReturnController
            => new UpdateBlueReturnController(
                $c->getTyped(UpdateBlueReturnUseCase::class),
                $c->getTyped(AuthenticateBearer::class),
            ));
        $c->set(GetBlueReturnController::class, static fn (Container $c): GetBlueReturnController
            => new GetBlueReturnController(
                $c->getTyped(GetBlueReturnUseCase::class),
                $c->getTyped(BlueReturnPdfGeneratorInterface::class),
                $c->getTyped(AuthenticateBearer::class),
            ));
        $c->set(ListBlueReturnController::class, static fn (Container $c): ListBlueReturnController
            => new ListBlueReturnController(
                $c->getTyped(ListBlueReturnsUseCase::class),
                $c->getTyped(AuthenticateBearer::class),
            ));
        $c->set(DeleteBlueReturnController::class, static fn (Container $c): DeleteBlueReturnController
            => new DeleteBlueReturnController(
                $c->getTyped(DeleteBlueReturnUseCase::class),
                $c->getTyped(AuthenticateBearer::class),
            ));
        $c->set(FinalizeBlueReturnController::class, static fn (Container $c): FinalizeBlueReturnController
            => new FinalizeBlueReturnController(
                $c->getTyped(FinalizeBlueReturnUseCase::class),
                $c->getTyped(AuthenticateBearer::class),
            ));

        // --- Financial Statement Notes (Phase 6 Wave 6-H-3, ADR-018) ---
        $c->set(
            FsNoteRepositoryInterface::class,
            static fn (Container $c): FsNoteRepositoryInterface
                => new PdoFsNoteRepository($c->getTyped(PDO::class)),
        );
        $c->set(
            FsNoteTemplateRepositoryInterface::class,
            static fn (Container $c): FsNoteTemplateRepositoryInterface
                => new PdoFsNoteTemplateRepository($c->getTyped(PDO::class)),
        );
        $c->set(
            FsNotesPdfGeneratorInterface::class,
            static function (Container $c): FsNotesPdfGeneratorInterface {
                unset($c);
                $repoRoot = dirname(__DIR__, 3);
                $templateDir = $repoRoot . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'fs_notes';
                $compileDir = $repoRoot . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'smarty_fs_notes';
                $fontDir = $repoRoot . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'fonts';
                if (!is_dir($compileDir)) {
                    @mkdir($compileDir, 0775, true);
                }
                if (!is_dir($fontDir)) {
                    @mkdir($fontDir, 0775, true);
                }
                return new DompdfFsNotesGenerator(
                    templateDir: $templateDir,
                    compileDir: $compileDir,
                    fontDir: $fontDir,
                );
            },
        );
        $c->set(ListFsNoteTemplatesUseCase::class, static fn (Container $c): ListFsNoteTemplatesUseCase
            => new ListFsNoteTemplatesUseCase($c->getTyped(FsNoteTemplateRepositoryInterface::class)));
        $c->set(ListFsNotesUseCase::class, static fn (Container $c): ListFsNotesUseCase
            => new ListFsNotesUseCase($c->getTyped(FsNoteRepositoryInterface::class)));
        $c->set(GetFsNoteUseCase::class, static fn (Container $c): GetFsNoteUseCase
            => new GetFsNoteUseCase($c->getTyped(FsNoteRepositoryInterface::class)));
        $c->set(CreateFsNoteUseCase::class, static fn (Container $c): CreateFsNoteUseCase
            => new CreateFsNoteUseCase(
                notes: $c->getTyped(FsNoteRepositoryInterface::class),
                ulids: $c->getTyped(UlidGenerator::class),
                clock: $c->getTyped(ClockInterface::class),
            ));
        $c->set(UpdateFsNoteUseCase::class, static fn (Container $c): UpdateFsNoteUseCase
            => new UpdateFsNoteUseCase(
                notes: $c->getTyped(FsNoteRepositoryInterface::class),
                clock: $c->getTyped(ClockInterface::class),
            ));
        $c->set(DeleteFsNoteUseCase::class, static fn (Container $c): DeleteFsNoteUseCase
            => new DeleteFsNoteUseCase($c->getTyped(FsNoteRepositoryInterface::class)));
        $c->set(ReorderFsNotesUseCase::class, static fn (Container $c): ReorderFsNotesUseCase
            => new ReorderFsNotesUseCase(
                notes: $c->getTyped(FsNoteRepositoryInterface::class),
                clock: $c->getTyped(ClockInterface::class),
            ));
        $c->set(BulkImportFsNotesFromTemplatesUseCase::class, static fn (Container $c): BulkImportFsNotesFromTemplatesUseCase
            => new BulkImportFsNotesFromTemplatesUseCase(
                notes: $c->getTyped(FsNoteRepositoryInterface::class),
                templates: $c->getTyped(FsNoteTemplateRepositoryInterface::class),
                ulids: $c->getTyped(UlidGenerator::class),
                clock: $c->getTyped(ClockInterface::class),
            ));
        $c->set(ListFsNoteTemplatesController::class, static fn (Container $c): ListFsNoteTemplatesController
            => new ListFsNoteTemplatesController(
                $c->getTyped(ListFsNoteTemplatesUseCase::class),
                $c->getTyped(AuthenticateBearer::class),
            ));
        $c->set(ListFsNotesController::class, static fn (Container $c): ListFsNotesController
            => new ListFsNotesController(
                $c->getTyped(ListFsNotesUseCase::class),
                $c->getTyped(AuthenticateBearer::class),
            ));
        $c->set(GetFsNoteController::class, static fn (Container $c): GetFsNoteController
            => new GetFsNoteController(
                $c->getTyped(GetFsNoteUseCase::class),
                $c->getTyped(AuthenticateBearer::class),
            ));
        $c->set(CreateFsNoteController::class, static fn (Container $c): CreateFsNoteController
            => new CreateFsNoteController(
                $c->getTyped(CreateFsNoteUseCase::class),
                $c->getTyped(AuthenticateBearer::class),
            ));
        $c->set(UpdateFsNoteController::class, static fn (Container $c): UpdateFsNoteController
            => new UpdateFsNoteController(
                $c->getTyped(UpdateFsNoteUseCase::class),
                $c->getTyped(AuthenticateBearer::class),
            ));
        $c->set(DeleteFsNoteController::class, static fn (Container $c): DeleteFsNoteController
            => new DeleteFsNoteController(
                $c->getTyped(DeleteFsNoteUseCase::class),
                $c->getTyped(AuthenticateBearer::class),
            ));
        $c->set(BulkImportFsNotesController::class, static fn (Container $c): BulkImportFsNotesController
            => new BulkImportFsNotesController(
                $c->getTyped(BulkImportFsNotesFromTemplatesUseCase::class),
                $c->getTyped(AuthenticateBearer::class),
            ));
        $c->set(ReorderFsNotesController::class, static fn (Container $c): ReorderFsNotesController
            => new ReorderFsNotesController(
                $c->getTyped(ReorderFsNotesUseCase::class),
                $c->getTyped(AuthenticateBearer::class),
            ));
        $c->set(ExportFsNotesController::class, static fn (Container $c): ExportFsNotesController
            => new ExportFsNotesController(
                $c->getTyped(ListFsNotesUseCase::class),
                $c->getTyped(FsNotesPdfGeneratorInterface::class),
                $c->getTyped(AuthenticateBearer::class),
            ));

        // --- Phase 7-1: Web UI (/ui/*) ---
        $c->set(SessionStore::class, static function (Container $c): SessionStore {
            unset($c);
            return new SessionStore();
        });
        $c->set(CsrfTokenManager::class, static fn (Container $c): CsrfTokenManager
            => new CsrfTokenManager($c->getTyped(ClockInterface::class)));
        $c->set(FlashMessageBag::class, static function (Container $c): FlashMessageBag {
            unset($c);
            return new FlashMessageBag();
        });
        $c->set(SmartyViewRenderer::class, static function (Container $c): SmartyViewRenderer {
            unset($c);
            $repoRoot = dirname(__DIR__, 3);
            $templateDir = $repoRoot . DIRECTORY_SEPARATOR . 'storage'
                . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'ui';
            $compileDir = $repoRoot . DIRECTORY_SEPARATOR . 'storage'
                . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'smarty_ui';
            return new SmartyViewRenderer($templateDir, $compileDir);
        });
        $c->set(AuthenticateSession::class, static fn (Container $c): AuthenticateSession
            => new AuthenticateSession(
                tokens: $c->getTyped(ApiTokenRepositoryInterface::class),
                clock: $c->getTyped(ClockInterface::class),
                session: $c->getTyped(SessionStore::class),
            ));
        $c->set(UiLoginController::class, static fn (Container $c): UiLoginController
            => new UiLoginController(
                loginUseCase: $c->getTyped(LoginUseCase::class),
                listEntities: $c->getTyped(ListEntitiesUseCase::class),
                session: $c->getTyped(SessionStore::class),
                csrf: $c->getTyped(CsrfTokenManager::class),
                flash: $c->getTyped(FlashMessageBag::class),
                view: $c->getTyped(SmartyViewRenderer::class),
            ));
        $c->set(UiLogoutController::class, static fn (Container $c): UiLogoutController
            => new UiLogoutController(
                tokens: $c->getTyped(ApiTokenRepositoryInterface::class),
                clock: $c->getTyped(ClockInterface::class),
                session: $c->getTyped(SessionStore::class),
                csrf: $c->getTyped(CsrfTokenManager::class),
                flash: $c->getTyped(FlashMessageBag::class),
            ));
        $c->set(UiDashboardController::class, static fn (Container $c): UiDashboardController
            => new UiDashboardController(
                listEntities: $c->getTyped(ListEntitiesUseCase::class),
                listJournals: $c->getTyped(ListJournalsUseCase::class),
                session: $c->getTyped(SessionStore::class),
                csrf: $c->getTyped(CsrfTokenManager::class),
                flash: $c->getTyped(FlashMessageBag::class),
                view: $c->getTyped(SmartyViewRenderer::class),
            ));
        $c->set(UiEntitySwitchController::class, static fn (Container $c): UiEntitySwitchController
            => new UiEntitySwitchController(
                session: $c->getTyped(SessionStore::class),
                csrf: $c->getTyped(CsrfTokenManager::class),
                flash: $c->getTyped(FlashMessageBag::class),
            ));

        // --- Phase 7-2: Journal CRUD UI (/ui/journals) ---
        $c->set(UiJournalUiContext::class, static fn (Container $c): UiJournalUiContext
            => new UiJournalUiContext(
                $c->getTyped(ListAccountTitlesUseCase::class),
                $c->getTyped(PDO::class),
            ));
        $c->set(UiJournalListController::class, static fn (Container $c): UiJournalListController
            => new UiJournalListController(
                search: $c->getTyped(SearchJournalUseCase::class),
                uiContext: $c->getTyped(UiJournalUiContext::class),
                session: $c->getTyped(SessionStore::class),
                csrf: $c->getTyped(CsrfTokenManager::class),
                flash: $c->getTyped(FlashMessageBag::class),
                view: $c->getTyped(SmartyViewRenderer::class),
            ));
        $c->set(UiJournalNewController::class, static fn (Container $c): UiJournalNewController
            => new UiJournalNewController(
                createJournal: $c->getTyped(CreateJournalUseCase::class),
                uiContext: $c->getTyped(UiJournalUiContext::class),
                clock: $c->getTyped(ClockInterface::class),
                session: $c->getTyped(SessionStore::class),
                csrf: $c->getTyped(CsrfTokenManager::class),
                flash: $c->getTyped(FlashMessageBag::class),
                view: $c->getTyped(SmartyViewRenderer::class),
            ));
        $c->set(UiJournalShowController::class, static fn (Container $c): UiJournalShowController
            => new UiJournalShowController(
                journals: $c->getTyped(JournalRepositoryInterface::class),
                uiContext: $c->getTyped(UiJournalUiContext::class),
                session: $c->getTyped(SessionStore::class),
                csrf: $c->getTyped(CsrfTokenManager::class),
                flash: $c->getTyped(FlashMessageBag::class),
                view: $c->getTyped(SmartyViewRenderer::class),
            ));
        $c->set(UiJournalEditController::class, static fn (Container $c): UiJournalEditController
            => new UiJournalEditController(
                updateJournal: $c->getTyped(UpdateJournalUseCase::class),
                journals: $c->getTyped(JournalRepositoryInterface::class),
                uiContext: $c->getTyped(UiJournalUiContext::class),
                session: $c->getTyped(SessionStore::class),
                csrf: $c->getTyped(CsrfTokenManager::class),
                flash: $c->getTyped(FlashMessageBag::class),
                view: $c->getTyped(SmartyViewRenderer::class),
            ));
        $c->set(UiJournalDeleteController::class, static fn (Container $c): UiJournalDeleteController
            => new UiJournalDeleteController(
                deleteJournal: $c->getTyped(DeleteJournalUseCase::class),
                journals: $c->getTyped(JournalRepositoryInterface::class),
                session: $c->getTyped(SessionStore::class),
                csrf: $c->getTyped(CsrfTokenManager::class),
                flash: $c->getTyped(FlashMessageBag::class),
                view: $c->getTyped(SmartyViewRenderer::class),
            ));

        // --- Phase 7-3: Web UI reports (Ledger / PL / BS) ---
        $c->set(PeriodQueryHelper::class, static fn (Container $c): PeriodQueryHelper
            => new PeriodQueryHelper($c->getTyped(PDO::class)));
        $c->set(UiLedgerViewController::class, static fn (Container $c): UiLedgerViewController
            => new UiLedgerViewController(
                queryLedger: $c->getTyped(QueryLedgerUseCase::class),
                listAccountTitles: $c->getTyped(ListAccountTitlesUseCase::class),
                pdfGenerator: $c->getTyped(LedgerGeneratorInterface::class),
                period: $c->getTyped(PeriodQueryHelper::class),
                session: $c->getTyped(SessionStore::class),
                csrf: $c->getTyped(CsrfTokenManager::class),
                flash: $c->getTyped(FlashMessageBag::class),
                view: $c->getTyped(SmartyViewRenderer::class),
            ));
        $c->set(UiPlViewController::class, static fn (Container $c): UiPlViewController
            => new UiPlViewController(
                useCase: $c->getTyped(GenerateFinancialStatementUseCase::class),
                pdfGenerator: $c->getTyped(FinancialStatementGeneratorInterface::class),
                period: $c->getTyped(PeriodQueryHelper::class),
                session: $c->getTyped(SessionStore::class),
                csrf: $c->getTyped(CsrfTokenManager::class),
                flash: $c->getTyped(FlashMessageBag::class),
                view: $c->getTyped(SmartyViewRenderer::class),
            ));
        $c->set(UiBsViewController::class, static fn (Container $c): UiBsViewController
            => new UiBsViewController(
                useCase: $c->getTyped(GenerateFinancialStatementUseCase::class),
                pdfGenerator: $c->getTyped(FinancialStatementGeneratorInterface::class),
                period: $c->getTyped(PeriodQueryHelper::class),
                session: $c->getTyped(SessionStore::class),
                csrf: $c->getTyped(CsrfTokenManager::class),
                flash: $c->getTyped(FlashMessageBag::class),
                view: $c->getTyped(SmartyViewRenderer::class),
            ));

        // --- Phase 7-4-B: CS / Multi FS / BEP / BlueReturn / SS / Notes UI ---
        $c->set(UiCsViewController::class, static fn (Container $c): UiCsViewController
            => new UiCsViewController(
                useCase: $c->getTyped(GenerateFinancialStatementUseCase::class),
                pdfGenerator: $c->getTyped(FinancialStatementGeneratorInterface::class),
                period: $c->getTyped(PeriodQueryHelper::class),
                session: $c->getTyped(SessionStore::class),
                csrf: $c->getTyped(CsrfTokenManager::class),
                flash: $c->getTyped(FlashMessageBag::class),
                view: $c->getTyped(SmartyViewRenderer::class),
            ));
        $c->set(UiMultiPeriodFsViewController::class, static fn (Container $c): UiMultiPeriodFsViewController
            => new UiMultiPeriodFsViewController(
                useCase: $c->getTyped(GenerateMultiPeriodFinancialStatementUseCase::class),
                pdfGenerator: $c->getTyped(MultiPeriodFinancialStatementGeneratorInterface::class),
                period: $c->getTyped(PeriodQueryHelper::class),
                session: $c->getTyped(SessionStore::class),
                csrf: $c->getTyped(CsrfTokenManager::class),
                flash: $c->getTyped(FlashMessageBag::class),
                view: $c->getTyped(SmartyViewRenderer::class),
            ));
        $c->set(UiBepViewController::class, static fn (Container $c): UiBepViewController
            => new UiBepViewController(
                useCase: $c->getTyped(AnalyzeBreakEvenPointUseCase::class),
                pdfGenerator: $c->getTyped(BreakEvenPointPdfGeneratorInterface::class),
                period: $c->getTyped(PeriodQueryHelper::class),
                session: $c->getTyped(SessionStore::class),
                csrf: $c->getTyped(CsrfTokenManager::class),
                flash: $c->getTyped(FlashMessageBag::class),
                view: $c->getTyped(SmartyViewRenderer::class),
            ));
        $c->set(UiBlueReturnViewController::class, static fn (Container $c): UiBlueReturnViewController
            => new UiBlueReturnViewController(
                getBlueReturn: $c->getTyped(GetBlueReturnUseCase::class),
                listBlueReturns: $c->getTyped(ListBlueReturnsUseCase::class),
                pdfGenerator: $c->getTyped(BlueReturnPdfGeneratorInterface::class),
                period: $c->getTyped(PeriodQueryHelper::class),
                session: $c->getTyped(SessionStore::class),
                csrf: $c->getTyped(CsrfTokenManager::class),
                flash: $c->getTyped(FlashMessageBag::class),
                view: $c->getTyped(SmartyViewRenderer::class),
            ));
        $c->set(UiSsViewController::class, static fn (Container $c): UiSsViewController
            => new UiSsViewController(
                useCase: $c->getTyped(GenerateStatementOfChangesInEquityUseCase::class),
                pdfGenerator: $c->getTyped(StatementOfChangesInEquityPdfGeneratorInterface::class),
                period: $c->getTyped(PeriodQueryHelper::class),
                session: $c->getTyped(SessionStore::class),
                csrf: $c->getTyped(CsrfTokenManager::class),
                flash: $c->getTyped(FlashMessageBag::class),
                view: $c->getTyped(SmartyViewRenderer::class),
            ));
        $c->set(UiNotesListViewController::class, static fn (Container $c): UiNotesListViewController
            => new UiNotesListViewController(
                listNotes: $c->getTyped(ListFsNotesUseCase::class),
                pdfGenerator: $c->getTyped(FsNotesPdfGeneratorInterface::class),
                period: $c->getTyped(PeriodQueryHelper::class),
                session: $c->getTyped(SessionStore::class),
                csrf: $c->getTyped(CsrfTokenManager::class),
                flash: $c->getTyped(FlashMessageBag::class),
                view: $c->getTyped(SmartyViewRenderer::class),
            ));

        // --- Phase 7-4-A: Master CRUD UI (/ui/masters/*) ---
        $c->set(CreateAccountTitleUseCase::class, static fn (Container $c): CreateAccountTitleUseCase
            => new CreateAccountTitleUseCase(
                repo:   $c->getTyped(AccountTitleRepositoryInterface::class),
                ulids:  $c->getTyped(UlidGenerator::class),
                clock:  $c->getTyped(ClockInterface::class),
            ));
        $c->set(UpdateAccountTitleUseCase::class, static fn (Container $c): UpdateAccountTitleUseCase
            => new UpdateAccountTitleUseCase(
                repo:  $c->getTyped(AccountTitleRepositoryInterface::class),
                clock: $c->getTyped(ClockInterface::class),
            ));
        $c->set(DeleteAccountTitleUseCase::class, static fn (Container $c): DeleteAccountTitleUseCase
            => new DeleteAccountTitleUseCase(
                repo:  $c->getTyped(AccountTitleRepositoryInterface::class),
                clock: $c->getTyped(ClockInterface::class),
            ));
        $c->set(CreateSubAccountTitleUseCase::class, static fn (Container $c): CreateSubAccountTitleUseCase
            => new CreateSubAccountTitleUseCase(
                repo:  $c->getTyped(SubAccountTitleRepositoryInterface::class),
                ulids: $c->getTyped(UlidGenerator::class),
                clock: $c->getTyped(ClockInterface::class),
            ));
        $c->set(UpdateSubAccountTitleUseCase::class, static fn (Container $c): UpdateSubAccountTitleUseCase
            => new UpdateSubAccountTitleUseCase(
                repo:  $c->getTyped(SubAccountTitleRepositoryInterface::class),
                clock: $c->getTyped(ClockInterface::class),
            ));
        $c->set(DeleteSubAccountTitleUseCase::class, static fn (Container $c): DeleteSubAccountTitleUseCase
            => new DeleteSubAccountTitleUseCase(
                repo:  $c->getTyped(SubAccountTitleRepositoryInterface::class),
                clock: $c->getTyped(ClockInterface::class),
            ));
        $c->set(CreateEntityUseCase::class, static fn (Container $c): CreateEntityUseCase
            => new CreateEntityUseCase(
                repo:  $c->getTyped(EntityRepositoryInterface::class),
                ulids: $c->getTyped(UlidGenerator::class),
                clock: $c->getTyped(ClockInterface::class),
            ));
        $c->set(UpdateEntityUseCase::class, static fn (Container $c): UpdateEntityUseCase
            => new UpdateEntityUseCase(
                repo:  $c->getTyped(EntityRepositoryInterface::class),
                clock: $c->getTyped(ClockInterface::class),
            ));
        $c->set(DeleteEntityUseCase::class, static fn (Container $c): DeleteEntityUseCase
            => new DeleteEntityUseCase(
                repo:  $c->getTyped(EntityRepositoryInterface::class),
                clock: $c->getTyped(ClockInterface::class),
            ));
        $c->set(CreateFiscalTermUseCase::class, static fn (Container $c): CreateFiscalTermUseCase
            => new CreateFiscalTermUseCase(
                repo:  $c->getTyped(FiscalTermRepositoryInterface::class),
                ulids: $c->getTyped(UlidGenerator::class),
                clock: $c->getTyped(ClockInterface::class),
            ));
        $c->set(UpdateFiscalTermUseCase::class, static fn (Container $c): UpdateFiscalTermUseCase
            => new UpdateFiscalTermUseCase(
                repo:  $c->getTyped(FiscalTermRepositoryInterface::class),
                clock: $c->getTyped(ClockInterface::class),
            ));
        $c->set(DeleteFiscalTermUseCase::class, static fn (Container $c): DeleteFiscalTermUseCase
            => new DeleteFiscalTermUseCase(
                repo: $c->getTyped(FiscalTermRepositoryInterface::class),
            ));

        $c->set(UiMasterAccountTitleController::class, static fn (Container $c): UiMasterAccountTitleController
            => new UiMasterAccountTitleController(
                listUseCase:   $c->getTyped(ListAccountTitlesUseCase::class),
                createUseCase: $c->getTyped(CreateAccountTitleUseCase::class),
                updateUseCase: $c->getTyped(UpdateAccountTitleUseCase::class),
                deleteUseCase: $c->getTyped(DeleteAccountTitleUseCase::class),
                repo:          $c->getTyped(AccountTitleRepositoryInterface::class),
                session:       $c->getTyped(SessionStore::class),
                csrf:          $c->getTyped(CsrfTokenManager::class),
                flash:         $c->getTyped(FlashMessageBag::class),
                view:          $c->getTyped(SmartyViewRenderer::class),
            ));
        $c->set(UiMasterSubAccountTitleController::class, static fn (Container $c): UiMasterSubAccountTitleController
            => new UiMasterSubAccountTitleController(
                repo:             $c->getTyped(SubAccountTitleRepositoryInterface::class),
                accountTitleRepo: $c->getTyped(AccountTitleRepositoryInterface::class),
                createUseCase:    $c->getTyped(CreateSubAccountTitleUseCase::class),
                updateUseCase:    $c->getTyped(UpdateSubAccountTitleUseCase::class),
                deleteUseCase:    $c->getTyped(DeleteSubAccountTitleUseCase::class),
                session:          $c->getTyped(SessionStore::class),
                csrf:             $c->getTyped(CsrfTokenManager::class),
                flash:            $c->getTyped(FlashMessageBag::class),
                view:             $c->getTyped(SmartyViewRenderer::class),
            ));
        $c->set(UiMasterEntityController::class, static fn (Container $c): UiMasterEntityController
            => new UiMasterEntityController(
                listUseCase:   $c->getTyped(ListEntitiesUseCase::class),
                createUseCase: $c->getTyped(CreateEntityUseCase::class),
                updateUseCase: $c->getTyped(UpdateEntityUseCase::class),
                deleteUseCase: $c->getTyped(DeleteEntityUseCase::class),
                repo:          $c->getTyped(EntityRepositoryInterface::class),
                session:       $c->getTyped(SessionStore::class),
                csrf:          $c->getTyped(CsrfTokenManager::class),
                flash:         $c->getTyped(FlashMessageBag::class),
                view:          $c->getTyped(SmartyViewRenderer::class),
            ));
        $c->set(UiMasterFiscalTermController::class, static fn (Container $c): UiMasterFiscalTermController
            => new UiMasterFiscalTermController(
                repo:          $c->getTyped(FiscalTermRepositoryInterface::class),
                createUseCase: $c->getTyped(CreateFiscalTermUseCase::class),
                updateUseCase: $c->getTyped(UpdateFiscalTermUseCase::class),
                deleteUseCase: $c->getTyped(DeleteFiscalTermUseCase::class),
                session:       $c->getTyped(SessionStore::class),
                csrf:          $c->getTyped(CsrfTokenManager::class),
                flash:         $c->getTyped(FlashMessageBag::class),
                view:          $c->getTyped(SmartyViewRenderer::class),
            ));

        // --- Phase 7-4-C: Planning CRUD UI (Fixed Assets / Budgets / CashPlan / Consumption Tax / SS) ---
        $c->set(UiPlanningContext::class, static fn (Container $c): UiPlanningContext
            => new UiPlanningContext(
                $c->getTyped(ListAccountTitlesUseCase::class),
                $c->getTyped(PDO::class),
            ));

        $c->set(UiFixedAssetListController::class, static fn (Container $c): UiFixedAssetListController
            => new UiFixedAssetListController(
                listAssets: $c->getTyped(ListFixedAssetsUseCase::class),
                session:    $c->getTyped(SessionStore::class),
                csrf:       $c->getTyped(CsrfTokenManager::class),
                flash:      $c->getTyped(FlashMessageBag::class),
                view:       $c->getTyped(SmartyViewRenderer::class),
            ));
        $c->set(UiFixedAssetNewController::class, static fn (Container $c): UiFixedAssetNewController
            => new UiFixedAssetNewController(
                createAsset: $c->getTyped(CreateFixedAssetUseCase::class),
                session:     $c->getTyped(SessionStore::class),
                csrf:        $c->getTyped(CsrfTokenManager::class),
                flash:       $c->getTyped(FlashMessageBag::class),
                view:        $c->getTyped(SmartyViewRenderer::class),
            ));
        $c->set(UiFixedAssetShowController::class, static fn (Container $c): UiFixedAssetShowController
            => new UiFixedAssetShowController(
                getAsset:  $c->getTyped(GetFixedAssetUseCase::class),
                schedules: $c->getTyped(DepreciationScheduleRepositoryInterface::class),
                session:   $c->getTyped(SessionStore::class),
                csrf:      $c->getTyped(CsrfTokenManager::class),
                flash:     $c->getTyped(FlashMessageBag::class),
                view:      $c->getTyped(SmartyViewRenderer::class),
            ));
        $c->set(UiFixedAssetEditController::class, static fn (Container $c): UiFixedAssetEditController
            => new UiFixedAssetEditController(
                updateAsset: $c->getTyped(UpdateFixedAssetUseCase::class),
                session:     $c->getTyped(SessionStore::class),
                csrf:        $c->getTyped(CsrfTokenManager::class),
                flash:       $c->getTyped(FlashMessageBag::class),
            ));
        $c->set(UiFixedAssetDisposeController::class, static fn (Container $c): UiFixedAssetDisposeController
            => new UiFixedAssetDisposeController(
                disposeAsset: $c->getTyped(DisposeFixedAssetUseCase::class),
                clock:        $c->getTyped(ClockInterface::class),
                session:      $c->getTyped(SessionStore::class),
                csrf:         $c->getTyped(CsrfTokenManager::class),
                flash:        $c->getTyped(FlashMessageBag::class),
            ));
        $c->set(UiFixedAssetDepreciateController::class, static fn (Container $c): UiFixedAssetDepreciateController
            => new UiFixedAssetDepreciateController(
                generate: $c->getTyped(GenerateDepreciationScheduleUseCase::class),
                ctx:      $c->getTyped(UiPlanningContext::class),
                clock:    $c->getTyped(ClockInterface::class),
                session:  $c->getTyped(SessionStore::class),
                csrf:     $c->getTyped(CsrfTokenManager::class),
                flash:    $c->getTyped(FlashMessageBag::class),
            ));

        $c->set(UiBudgetListController::class, static fn (Container $c): UiBudgetListController
            => new UiBudgetListController(
                listBudgets: $c->getTyped(ListBudgetsUseCase::class),
                ctx:         $c->getTyped(UiPlanningContext::class),
                session:     $c->getTyped(SessionStore::class),
                csrf:        $c->getTyped(CsrfTokenManager::class),
                flash:       $c->getTyped(FlashMessageBag::class),
                view:        $c->getTyped(SmartyViewRenderer::class),
            ));
        $c->set(UiBudgetNewController::class, static fn (Container $c): UiBudgetNewController
            => new UiBudgetNewController(
                createBudget: $c->getTyped(CreateBudgetUseCase::class),
                ctx:          $c->getTyped(UiPlanningContext::class),
                clock:        $c->getTyped(ClockInterface::class),
                session:      $c->getTyped(SessionStore::class),
                csrf:         $c->getTyped(CsrfTokenManager::class),
                flash:        $c->getTyped(FlashMessageBag::class),
                view:         $c->getTyped(SmartyViewRenderer::class),
            ));
        $c->set(UiBudgetShowController::class, static fn (Container $c): UiBudgetShowController
            => new UiBudgetShowController(
                getBudget: $c->getTyped(GetBudgetUseCase::class),
                session:   $c->getTyped(SessionStore::class),
                csrf:      $c->getTyped(CsrfTokenManager::class),
                flash:     $c->getTyped(FlashMessageBag::class),
                view:      $c->getTyped(SmartyViewRenderer::class),
            ));
        $c->set(UiBudgetLifecycleController::class, static fn (Container $c): UiBudgetLifecycleController
            => new UiBudgetLifecycleController(
                approve: $c->getTyped(ApproveBudgetUseCase::class),
                lock:    $c->getTyped(LockBudgetUseCase::class),
                delete:  $c->getTyped(DeleteBudgetUseCase::class),
                session: $c->getTyped(SessionStore::class),
                csrf:    $c->getTyped(CsrfTokenManager::class),
                flash:   $c->getTyped(FlashMessageBag::class),
            ));
        $c->set(UiBudgetVarianceController::class, static fn (Container $c): UiBudgetVarianceController
            => new UiBudgetVarianceController(
                getBudget: $c->getTyped(GetBudgetUseCase::class),
                analyze:   $c->getTyped(AnalyzeBudgetVarianceUseCase::class),
                ctx:       $c->getTyped(UiPlanningContext::class),
                clock:     $c->getTyped(ClockInterface::class),
                session:   $c->getTyped(SessionStore::class),
                csrf:      $c->getTyped(CsrfTokenManager::class),
                flash:     $c->getTyped(FlashMessageBag::class),
                view:      $c->getTyped(SmartyViewRenderer::class),
            ));

        $c->set(UiCashPlanListController::class, static fn (Container $c): UiCashPlanListController
            => new UiCashPlanListController(
                listPlans: $c->getTyped(ListCashPlansUseCase::class),
                ctx:       $c->getTyped(UiPlanningContext::class),
                session:   $c->getTyped(SessionStore::class),
                csrf:      $c->getTyped(CsrfTokenManager::class),
                flash:     $c->getTyped(FlashMessageBag::class),
                view:      $c->getTyped(SmartyViewRenderer::class),
            ));
        $c->set(UiCashPlanNewController::class, static fn (Container $c): UiCashPlanNewController
            => new UiCashPlanNewController(
                createPlan: $c->getTyped(CreateCashPlanUseCase::class),
                ctx:        $c->getTyped(UiPlanningContext::class),
                clock:      $c->getTyped(ClockInterface::class),
                session:    $c->getTyped(SessionStore::class),
                csrf:       $c->getTyped(CsrfTokenManager::class),
                flash:      $c->getTyped(FlashMessageBag::class),
                view:       $c->getTyped(SmartyViewRenderer::class),
            ));
        $c->set(UiCashPlanShowController::class, static fn (Container $c): UiCashPlanShowController
            => new UiCashPlanShowController(
                getPlan: $c->getTyped(GetCashPlanUseCase::class),
                session: $c->getTyped(SessionStore::class),
                csrf:    $c->getTyped(CsrfTokenManager::class),
                flash:   $c->getTyped(FlashMessageBag::class),
                view:    $c->getTyped(SmartyViewRenderer::class),
            ));
        $c->set(UiCashPlanDeleteController::class, static fn (Container $c): UiCashPlanDeleteController
            => new UiCashPlanDeleteController(
                delete:  $c->getTyped(DeleteCashPlanUseCase::class),
                session: $c->getTyped(SessionStore::class),
                csrf:    $c->getTyped(CsrfTokenManager::class),
                flash:   $c->getTyped(FlashMessageBag::class),
            ));

        $c->set(UiCtPeriodListController::class, static fn (Container $c): UiCtPeriodListController
            => new UiCtPeriodListController(
                listPeriods: $c->getTyped(ListConsumptionTaxPeriodsUseCase::class),
                session:     $c->getTyped(SessionStore::class),
                csrf:        $c->getTyped(CsrfTokenManager::class),
                flash:       $c->getTyped(FlashMessageBag::class),
                view:        $c->getTyped(SmartyViewRenderer::class),
            ));
        $c->set(UiCtPeriodNewController::class, static fn (Container $c): UiCtPeriodNewController
            => new UiCtPeriodNewController(
                createPeriod: $c->getTyped(CreateConsumptionTaxPeriodUseCase::class),
                ctx:          $c->getTyped(UiPlanningContext::class),
                clock:        $c->getTyped(ClockInterface::class),
                session:      $c->getTyped(SessionStore::class),
                csrf:         $c->getTyped(CsrfTokenManager::class),
                flash:        $c->getTyped(FlashMessageBag::class),
                view:         $c->getTyped(SmartyViewRenderer::class),
            ));
        $c->set(UiCtPeriodShowController::class, static fn (Container $c): UiCtPeriodShowController
            => new UiCtPeriodShowController(
                periods: $c->getTyped(ConsumptionTaxPeriodRepositoryInterface::class),
                session: $c->getTyped(SessionStore::class),
                csrf:    $c->getTyped(CsrfTokenManager::class),
                flash:   $c->getTyped(FlashMessageBag::class),
                view:    $c->getTyped(SmartyViewRenderer::class),
            ));
        $c->set(UiCtCalculateController::class, static fn (Container $c): UiCtCalculateController
            => new UiCtCalculateController(
                calculate: $c->getTyped(CalculateConsumptionTaxUseCase::class),
                report:    $c->getTyped(GenerateConsumptionTaxReportUseCase::class),
                session:   $c->getTyped(SessionStore::class),
                csrf:      $c->getTyped(CsrfTokenManager::class),
                flash:     $c->getTyped(FlashMessageBag::class),
                view:      $c->getTyped(SmartyViewRenderer::class),
            ));
        $c->set(UiCtAccountDefaultsController::class, static fn (Container $c): UiCtAccountDefaultsController
            => new UiCtAccountDefaultsController(
                listDefaults: $c->getTyped(ListAccountTitleTaxDefaultsUseCase::class),
                upsert:       $c->getTyped(UpsertAccountTitleTaxDefaultsUseCase::class),
                ctx:          $c->getTyped(UiPlanningContext::class),
                session:      $c->getTyped(SessionStore::class),
                csrf:         $c->getTyped(CsrfTokenManager::class),
                flash:        $c->getTyped(FlashMessageBag::class),
                view:         $c->getTyped(SmartyViewRenderer::class),
            ));
        $c->set(UiCtInvoiceRegistrationController::class, static fn (Container $c): UiCtInvoiceRegistrationController
            => new UiCtInvoiceRegistrationController(
                listRegs: $c->getTyped(ListInvoiceRegistrationsUseCase::class),
                upsert:   $c->getTyped(UpsertInvoiceRegistrationUseCase::class),
                session:  $c->getTyped(SessionStore::class),
                csrf:     $c->getTyped(CsrfTokenManager::class),
                flash:    $c->getTyped(FlashMessageBag::class),
                view:     $c->getTyped(SmartyViewRenderer::class),
            ));

        $c->set(UiSsAdjustmentListController::class, static fn (Container $c): UiSsAdjustmentListController
            => new UiSsAdjustmentListController(
                listAdjustments: $c->getTyped(ListSsAdjustmentsUseCase::class),
                ctx:             $c->getTyped(UiPlanningContext::class),
                clock:           $c->getTyped(ClockInterface::class),
                session:         $c->getTyped(SessionStore::class),
                csrf:            $c->getTyped(CsrfTokenManager::class),
                flash:           $c->getTyped(FlashMessageBag::class),
                view:            $c->getTyped(SmartyViewRenderer::class),
            ));
        $c->set(UiSsAdjustmentNewController::class, static fn (Container $c): UiSsAdjustmentNewController
            => new UiSsAdjustmentNewController(
                createAdjustment: $c->getTyped(CreateSsAdjustmentUseCase::class),
                ctx:              $c->getTyped(UiPlanningContext::class),
                clock:            $c->getTyped(ClockInterface::class),
                session:          $c->getTyped(SessionStore::class),
                csrf:             $c->getTyped(CsrfTokenManager::class),
                flash:            $c->getTyped(FlashMessageBag::class),
                view:             $c->getTyped(SmartyViewRenderer::class),
            ));
        $c->set(UiSsAdjustmentEditController::class, static fn (Container $c): UiSsAdjustmentEditController
            => new UiSsAdjustmentEditController(
                update:  $c->getTyped(UpdateSsAdjustmentUseCase::class),
                repo:    $c->getTyped(SsManualAdjustmentRepositoryInterface::class),
                session: $c->getTyped(SessionStore::class),
                csrf:    $c->getTyped(CsrfTokenManager::class),
                flash:   $c->getTyped(FlashMessageBag::class),
                view:    $c->getTyped(SmartyViewRenderer::class),
            ));
        $c->set(UiSsAdjustmentDeleteController::class, static fn (Container $c): UiSsAdjustmentDeleteController
            => new UiSsAdjustmentDeleteController(
                delete:  $c->getTyped(DeleteSsAdjustmentUseCase::class),
                session: $c->getTyped(SessionStore::class),
                csrf:    $c->getTyped(CsrfTokenManager::class),
                flash:   $c->getTyped(FlashMessageBag::class),
            ));

        return $c;
    }
}
