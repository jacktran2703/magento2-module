<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Config;

use Omikron\Factfinder\Api\Config\ParametersSourceInterface;

class CommunicationParametersProvider implements ParametersSourceInterface
{
    /** @var ParametersSourceInterface[] */
    private $sources;

    public function __construct(array $parametersSource = [])
    {
        $this->sources = $parametersSource;
    }

    public function getParameters(): array
    {
        return array_reduce($this->sources, function (array $params, ParametersSourceInterface $source): array {
            return array_merge($params, $source->getParameters());
        }, []);
    }
}
