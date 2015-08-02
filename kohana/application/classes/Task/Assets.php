<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Task to minify and compiled asset collections
 *
 * Asset collections are defined in:
 *
 *     APPPATH.'config/assets.php'
 *
 * @package     Application
 * @category    Tasks
 * @author      Todd Wirth
 * @copyright   (c) 2013
 */
class Task_Assets extends Minion_Task {

    /**
     * @var     array       Command line options for this task
     */
    protected $_options = array(
        );

    /**
     * Adds any validation rules/labels for validating _options
     *
     * @param   Validation  The validation object to add rules to
     * @return  Validation
     */
    public function build_validation(Validation $validation)
    {
        return parent::build_validation($validation);
    }

    /**
     * Execute the task
     *
     * @param   array       Options passed from command line
     * @return  void
     */
    protected function _execute(array $params)
    {
        $config = Kohana::$config->load('assets')->as_array();

        $types = array_keys($config);

        foreach ($types as $type)
        {
            $names = array_keys($config[$type]);

            foreach ($names as $name)
            {
                $asset = new Asset_Compiled($type, $name);
                $asset->update();

                Minion_CLI::write('[' . Minion_CLI::color(sprintf('%3s', $type), 'blue') . '] Compiled and updated asset collection named: ' . $name);
            }
        }

        // All done !
    }
}

