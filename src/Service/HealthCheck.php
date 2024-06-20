<?php

declare(strict_types=1);

namespace Dbp\Relay\CabinetConnectorCampusonlineBundle\Service;

use Dbp\Relay\CabinetConnectorCampusonlineBundle\CoApi\CoApi;
use Dbp\Relay\CoreBundle\HealthCheck\CheckInterface;
use Dbp\Relay\CoreBundle\HealthCheck\CheckOptions;
use Dbp\Relay\CoreBundle\HealthCheck\CheckResult;

class HealthCheck implements CheckInterface
{
    private ConfigurationService $config;

    public function __construct(ConfigurationService $config)
    {
        $this->config = $config;
    }

    public function getName(): string
    {
        return 'cabinet-connector-campusonline';
    }

    private function checkMethod(string $description, callable $func): CheckResult
    {
        $result = new CheckResult($description);
        try {
            $func();
        } catch (\Throwable $e) {
            $result->set(CheckResult::STATUS_FAILURE, $e->getMessage(), ['exception' => $e]);

            return $result;
        }
        $result->set(CheckResult::STATUS_SUCCESS);

        return $result;
    }

    public function check(CheckOptions $options): array
    {
        $api = new CoApi($this->config);

        return [
            $this->checkMethod('Check if the students API works', [$api->getStudentsApi(), 'checkConnection']),
            $this->checkMethod('Check if the studies API works', [$api->getStudiesApi(), 'checkConnection']),
            $this->checkMethod('Check if the applications API works', [$api->getApplicationsApi(), 'checkConnection']),
        ];
    }
}
