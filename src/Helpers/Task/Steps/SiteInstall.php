<?php

namespace AcquiaCMS\Cli\Helpers\Task\Steps;

use AcquiaCMS\Cli\Helpers\Process\ProcessManager;

/**
 * Run the drush command to install Drupal site.
 */
class SiteInstall {

  /**
   * A process manager object.
   *
   * @var \AcquiaCMS\Cli\Helpers\Process\ProcessManager
   */
  protected $processManager;

  /**
   * Constructs an object.
   *
   * @param \AcquiaCMS\Cli\Helpers\Process\ProcessManager $processManager
   *   Hold the process manager class object.
   */
  public function __construct(ProcessManager $processManager) {
    $this->processManager = $processManager;
  }

  /**
   * Run the drush commands to install Drupal.
   *
   * @param array $args
   *   An array of params argument to pass.
   */
  public function execute(array $args = []) :int {
    $siteInstallCommand = ["./vendor/bin/drush", "site:install", "minimal"];
    if ($args['no-interaction']) {
      $siteInstallCommand = array_merge($siteInstallCommand, ["--yes"]);
    }
    $this->processManager->add($siteInstallCommand);
    return $this->processManager->runAll();
  }

}