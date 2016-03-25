<?php

namespace Aliznet\WCSBundle\Writer\File;

use Akeneo\Bundle\BatchBundle\Job\RuntimeErrorException;
use Akeneo\Component\Batch\Item\AbstractConfigurableStepElement;
use Akeneo\Component\Batch\Item\ItemWriterInterface;
use Akeneo\Component\Batch\Model\StepExecution;
use Akeneo\Component\Batch\Step\StepExecutionAwareInterface;
use Pim\Bundle\ImportExportBundle\Validator\Constraints\WritableDirectory;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * File Writer.
 *
 * @author    aliznet
 * @copyright 2016 ALIZNET (www.aliznet.fr)
 */
class FileWriter extends AbstractConfigurableStepElement implements
ItemWriterInterface, StepExecutionAwareInterface
{
    /**
     * @Assert\NotBlank(groups={"Execution"})
     * @WritableDirectory(groups={"Execution"})
     */
    protected $filePath = '/tmp/export_%datetime%.csv';

    /**
     * @var StepExecution
     */
    protected $stepExecution;

    /**
     * @var handler
     */
    private $handler;

    /**
     * @var string
     */
    private $resolvedFilePath;

    /**
     * Set the file path.
     *
     * @param string $filePath
     *
     * @return FileWriter
     */
    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;
        $this->resolvedFilePath = null;

        return $this;
    }

    /**
     * Get the file path.
     *
     * @return string
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * Get the file path in which to write the data.
     *
     * @return string
     */
    public function getPath()
    {
        if (!isset($this->resolvedFilePath)) {
            $this->resolvedFilePath = strtr($this->filePath, array(
                '%datetime%' => date('Y-m-d_H:i:s'),
                    ));
        }

        return $this->resolvedFilePath;
    }

    /**
     * @param array $data
     *
     * @throws RuntimeErrorException
     */
    public function write(array $data)
    {
        if (!$this->handler) {
            $path = $this->getPath();
            if (!is_dir(dirname($path))) {
                mkdir(dirname($path), 0777, true);
            }
            $this->handler = fopen($path, 'w');
        }
        foreach ($data as $entry) {
            if (false === fwrite($this->handler, $entry)) {
                throw new RuntimeErrorException('Failed to write to file %path%', ['%path%' => $this->getPath()]);
            } else {
                $this->stepExecution->incrementSummaryInfo('write');
            }
        }
    }

    /**
     * 
     */
    public function flush()
    {
    }

    /**
     * Close handler when desctructing the current instance.
     */
    public function __destruct()
    {
        if ($this->handler) {
            fclose($this->handler);
        }
    }

    /**
     * @return array
     */
    public function getConfigurationFields()
    {
        return array(
            'filePath' => array(
                'options' => array(
                    'label' => 'pim_base_connector.export.filePath.label',
                    'help'  => 'pim_base_connector.export.filePath.help',
                ),
            ), );
    }

    /**
     * @param StepExecution $stepExecution
     */
    public function setStepExecution(StepExecution $stepExecution)
    {
        $this->stepExecution = $stepExecution;
    }
}
