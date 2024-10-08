<?php
namespace Aws\LakeFormation;

use Aws\AwsClient;

/**
 * This client is used to interact with the **AWS Lake Formation** service.
 * @method \Aws\Result addLFTagsToResource(array $args = [])
 * @method \GuzzleHttp\Promise\Promise addLFTagsToResourceAsync(array $args = [])
 * @method \Aws\Result assumeDecoratedRoleWithSAML(array $args = [])
 * @method \GuzzleHttp\Promise\Promise assumeDecoratedRoleWithSAMLAsync(array $args = [])
 * @method \Aws\Result batchGrantPermissions(array $args = [])
 * @method \GuzzleHttp\Promise\Promise batchGrantPermissionsAsync(array $args = [])
 * @method \Aws\Result batchRevokePermissions(array $args = [])
 * @method \GuzzleHttp\Promise\Promise batchRevokePermissionsAsync(array $args = [])
 * @method \Aws\Result cancelTransaction(array $args = [])
 * @method \GuzzleHttp\Promise\Promise cancelTransactionAsync(array $args = [])
 * @method \Aws\Result commitTransaction(array $args = [])
 * @method \GuzzleHttp\Promise\Promise commitTransactionAsync(array $args = [])
 * @method \Aws\Result createDataCellsFilter(array $args = [])
 * @method \GuzzleHttp\Promise\Promise createDataCellsFilterAsync(array $args = [])
 * @method \Aws\Result createLFTag(array $args = [])
 * @method \GuzzleHttp\Promise\Promise createLFTagAsync(array $args = [])
 * @method \Aws\Result createLakeFormationIdentityCenterConfiguration(array $args = [])
 * @method \GuzzleHttp\Promise\Promise createLakeFormationIdentityCenterConfigurationAsync(array $args = [])
 * @method \Aws\Result createLakeFormationOptIn(array $args = [])
 * @method \GuzzleHttp\Promise\Promise createLakeFormationOptInAsync(array $args = [])
 * @method \Aws\Result deleteDataCellsFilter(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteDataCellsFilterAsync(array $args = [])
 * @method \Aws\Result deleteLFTag(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteLFTagAsync(array $args = [])
 * @method \Aws\Result deleteLakeFormationIdentityCenterConfiguration(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteLakeFormationIdentityCenterConfigurationAsync(array $args = [])
 * @method \Aws\Result deleteLakeFormationOptIn(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteLakeFormationOptInAsync(array $args = [])
 * @method \Aws\Result deleteObjectsOnCancel(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteObjectsOnCancelAsync(array $args = [])
 * @method \Aws\Result deregisterResource(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deregisterResourceAsync(array $args = [])
 * @method \Aws\Result describeLakeFormationIdentityCenterConfiguration(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describeLakeFormationIdentityCenterConfigurationAsync(array $args = [])
 * @method \Aws\Result describeResource(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describeResourceAsync(array $args = [])
 * @method \Aws\Result describeTransaction(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describeTransactionAsync(array $args = [])
 * @method \Aws\Result extendTransaction(array $args = [])
 * @method \GuzzleHttp\Promise\Promise extendTransactionAsync(array $args = [])
 * @method \Aws\Result getDataCellsFilter(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getDataCellsFilterAsync(array $args = [])
 * @method \Aws\Result getDataLakeSettings(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getDataLakeSettingsAsync(array $args = [])
 * @method \Aws\Result getEffectivePermissionsForPath(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getEffectivePermissionsForPathAsync(array $args = [])
 * @method \Aws\Result getLFTag(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getLFTagAsync(array $args = [])
 * @method \Aws\Result getQueryState(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getQueryStateAsync(array $args = [])
 * @method \Aws\Result getQueryStatistics(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getQueryStatisticsAsync(array $args = [])
 * @method \Aws\Result getResourceLFTags(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getResourceLFTagsAsync(array $args = [])
 * @method \Aws\Result getTableObjects(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getTableObjectsAsync(array $args = [])
 * @method \Aws\Result getTemporaryGluePartitionCredentials(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getTemporaryGluePartitionCredentialsAsync(array $args = [])
 * @method \Aws\Result getTemporaryGlueTableCredentials(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getTemporaryGlueTableCredentialsAsync(array $args = [])
 * @method \Aws\Result getWorkUnitResults(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getWorkUnitResultsAsync(array $args = [])
 * @method \Aws\Result getWorkUnits(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getWorkUnitsAsync(array $args = [])
 * @method \Aws\Result grantPermissions(array $args = [])
 * @method \GuzzleHttp\Promise\Promise grantPermissionsAsync(array $args = [])
 * @method \Aws\Result listDataCellsFilter(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listDataCellsFilterAsync(array $args = [])
 * @method \Aws\Result listLFTags(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listLFTagsAsync(array $args = [])
 * @method \Aws\Result listLakeFormationOptIns(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listLakeFormationOptInsAsync(array $args = [])
 * @method \Aws\Result listPermissions(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listPermissionsAsync(array $args = [])
 * @method \Aws\Result listResources(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listResourcesAsync(array $args = [])
 * @method \Aws\Result listTableStorageOptimizers(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listTableStorageOptimizersAsync(array $args = [])
 * @method \Aws\Result listTransactions(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listTransactionsAsync(array $args = [])
 * @method \Aws\Result putDataLakeSettings(array $args = [])
 * @method \GuzzleHttp\Promise\Promise putDataLakeSettingsAsync(array $args = [])
 * @method \Aws\Result registerResource(array $args = [])
 * @method \GuzzleHttp\Promise\Promise registerResourceAsync(array $args = [])
 * @method \Aws\Result removeLFTagsFromResource(array $args = [])
 * @method \GuzzleHttp\Promise\Promise removeLFTagsFromResourceAsync(array $args = [])
 * @method \Aws\Result revokePermissions(array $args = [])
 * @method \GuzzleHttp\Promise\Promise revokePermissionsAsync(array $args = [])
 * @method \Aws\Result searchDatabasesByLFTags(array $args = [])
 * @method \GuzzleHttp\Promise\Promise searchDatabasesByLFTagsAsync(array $args = [])
 * @method \Aws\Result searchTablesByLFTags(array $args = [])
 * @method \GuzzleHttp\Promise\Promise searchTablesByLFTagsAsync(array $args = [])
 * @method \Aws\Result startQueryPlanning(array $args = [])
 * @method \GuzzleHttp\Promise\Promise startQueryPlanningAsync(array $args = [])
 * @method \Aws\Result startTransaction(array $args = [])
 * @method \GuzzleHttp\Promise\Promise startTransactionAsync(array $args = [])
 * @method \Aws\Result updateDataCellsFilter(array $args = [])
 * @method \GuzzleHttp\Promise\Promise updateDataCellsFilterAsync(array $args = [])
 * @method \Aws\Result updateLFTag(array $args = [])
 * @method \GuzzleHttp\Promise\Promise updateLFTagAsync(array $args = [])
 * @method \Aws\Result updateLakeFormationIdentityCenterConfiguration(array $args = [])
 * @method \GuzzleHttp\Promise\Promise updateLakeFormationIdentityCenterConfigurationAsync(array $args = [])
 * @method \Aws\Result updateResource(array $args = [])
 * @method \GuzzleHttp\Promise\Promise updateResourceAsync(array $args = [])
 * @method \Aws\Result updateTableObjects(array $args = [])
 * @method \GuzzleHttp\Promise\Promise updateTableObjectsAsync(array $args = [])
 * @method \Aws\Result updateTableStorageOptimizer(array $args = [])
 * @method \GuzzleHttp\Promise\Promise updateTableStorageOptimizerAsync(array $args = [])
 */
class LakeFormationClient extends AwsClient {}
