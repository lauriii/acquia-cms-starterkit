<?php

namespace AcquiaCMS\Cli\Helpers\Process;

use AcquiaCMS\Cli\Enum\StatusCodes;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

/**
 * ProcessManager class to add & execute different commands.
 */
class ProcessManager {

  /**
   * An array of process object.
   *
   * @var array
   */
  protected $process;

  /**
   * A Symfony console output interface object.
   *
   * @var \Symfony\Component\Console\Output\OutputInterface
   */
  protected $output;

  /**
   * Constructs an object.
   *
   * @param \Symfony\Component\Console\Output\OutputInterface $output
   *   Hold the symfony console output object.
   */
  public function __construct(OutputInterface $output) {
    $this->output = $output;
    $this->process = [];
  }

  /**
   * Adds the process command to an array.
   *
   * @param array $command
   *   An array of commands to execute.
   */
  public function add(array $command) :void {
    $process = new Process($command);
    $process->setTimeout(NULL)
      ->setIdleTimeout(NULL)
      ->setTty(Process::isTtySupported());
    $this->process[] = $process;
  }

  /**
   * Returns the last inserted process to provide ability to alter.
   *
   * @return \Symfony\Component\Process\Process
   *   Returns a process object or null (when empty)
   */
  public function getLastProcess() :?Process {
    return $this->process ? reset($this->process) : NULL;
  }

  /**
   * Returns an array of process commands array.
   *
   * @return array
   *   Returns an array of Process object.
   */
  public function getAllProcess() :array {
    return $this->process;
  }

  /**
   * Executes all process commands from the array.
   */
  public function runAll() :int {
    $status = StatusCodes::OK;
    foreach ($this->getAllProcess() as $process) {
      $status = $this->run($process);
      array_shift($this->process);
    }
    return $status;
  }

  /**
   * Executes the command from the process array.
   *
   * @param \Symfony\Component\Process\Process $process
   *   A Process object to execute.
   *
   * @throws \Symfony\Component\Process\Exception\ProcessFailedException
   *   A Process object.
   */
  public function run(Process $process = NULL) :int {
    $this->output->writeln(sprintf('> %s', $process->getCommandLine()));
    $status = $process->run(function ($type, $buffer) {
      if (Process::ERR != $type) {
        $this->output->writeln($buffer);
      }
    });
    if (!$process->isSuccessful()) {
      $process->disableOutput();
      throw new ProcessFailedException($process);
    }
    return $status;
  }

}