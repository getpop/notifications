<?php
namespace PoP\Notifications\TypeDataLoaders;

use PoP\Hooks\Facades\HooksAPIFacade;
use PoP\ComponentModel\TypeDataLoaders\AbstractTypeQueryableDataLoader;
use PoP\ComponentModel\State\ApplicationState;
use PoP_Notifications_API;

class NotificationTypeDataLoader extends AbstractTypeQueryableDataLoader
{
    protected function getOrderbyDefault()
    {
        return 'histid';
    }

    protected function getOrderDefault()
    {
        return 'DESC';
    }

    public function getQuery($query_args): array
    {
        $query = parent::getQuery($query_args);

        // Pagination
        $vars = ApplicationState::getVars();
        if ($vars['loading-latest']) {
            $query['pagenumber'] = 1;
            $query['limit'] = -1; // Limit=-1 => Bring all results
        }
        else {
            if ($pagenumber = $query_args[GD_URLPARAM_PAGENUMBER]) {
                $query['pagenumber'] = $pagenumber;
            }
            if ($limit = $query_args[GD_URLPARAM_LIMIT]) {
                $query['limit'] = $limit;
            }
        }

        return HooksAPIFacade::getInstance()->applyFilters(
            $this->getQueryHookName(),
            $query,
            $query_args
        );
    }

    protected function getQueryHookName()
    {
        return 'Dataloader_NotificationList:query';
    }

    public function getDataFromIdsQuery(array $ids): array
    {
        return array(
            'include' => $ids,
        );
    }

    public function executeQueryIds($query): array
    {
        $query['array'] = true;
        $query['fields'] = array('histid');

        // By default, we use joinstatus => false to make the initial query run faster
        // however, this param can be provided in the dataload_query_args,
        // eg: to bring in only notifications which have not been read, for the automated emails daily notification digest
        // then keep it as it is
        if (!isset($query['joinstatus'])) {
            $query['joinstatus'] = false;
        }

        $results = (array)$this->executeQuery($query);

        $ret = array();
        if ($results) {
            foreach ($results as $value) {
                $ret[] = $value['histid'];
            }
        }

        return $ret;
    }

    public function executeQuery($query, array $options = [])
    {
        return PoP_Notifications_API::getNotifications($query);
    }
}
