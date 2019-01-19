<?php 

require_once __DIR__ . "/../../vendor/autoload.php";

class TestCase extends PHPUnit\Framework\TestCase {

    protected $requiresDB = false;
    
    public function setUp() {
        \PHuby\Config::set_config_root(__DIR__."/../_support/config.d.testing");
    }

    public static function getJsonData($filename) {
        return json_decode(@file_get_contents(__DIR__ . '/../_data/' . $filename . '.json'), true);
    }

    public function tearDown() {
        if ($this->requiresDB) {
            $this->unsetDB();
        }
    }

    protected function setDB() {
        // see also https://github.com/robmorgan/phinx/issues/364
        $phinxApp = new \Phinx\Console\PhinxApplication();
        $phinxTextWrapper = new \Phinx\Wrapper\TextWrapper($phinxApp);

        $phinxTextWrapper->setOption('configuration', __DIR__ . '/../../phinx.yml');
        $phinxTextWrapper->setOption('parser', 'YAML');

        $logMigrate = $phinxTextWrapper->getMigrate('testing');
        $logSeed = $phinxTextWrapper->getSeed();

        $this->requiresDB = true;
    }

    protected function unsetDB() {
        $phinxApp = new \Phinx\Console\PhinxApplication();
        $phinxTextWrapper = new \Phinx\Wrapper\TextWrapper($phinxApp);

        $phinxTextWrapper->setOption('configuration', __DIR__ . '/../../phinx.yml');
        $phinxTextWrapper->setOption('parser', 'YAML');
        $phinxTextWrapper->getRollback('testing', '0');
    }
}