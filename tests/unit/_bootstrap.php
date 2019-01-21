<?php 

require_once __DIR__ . "/../../vendor/autoload.php";

use PHubyTest\DBI;
use PHubyTest\Model\User;
use PHuby\Attribute\PasswordAttr;

class TestCase extends PHPUnit\Framework\TestCase {

    protected 
        $requiresDB = false,
        $phinxApp,
        $phinxTextWrapper;

    
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
        $this->phinxApp = new \Phinx\Console\PhinxApplication();
        $this->phinxTextWrapper = new \Phinx\Wrapper\TextWrapper($this->phinxApp);

        $this->phinxTextWrapper->setOption('configuration', __DIR__ . '/../../phinx.yml');
        $this->phinxTextWrapper->setOption('parser', 'YAML');
        
        $logMigrate = $this->phinxTextWrapper->getMigrate('testing');
        $logSeed = $this->phinxTextWrapper->getSeed();

        $this->requiresDB = true;
    }

    protected function unsetDB() {
        $this->phinxTextWrapper->getRollback('testing', '0');
    }

    protected function createUser() {
        $user = new User([
            'email' => 'adams@gmail.com',
            'password' => PasswordAttr::hash_password('password'),
            'first_name' => 'Tester'
        ]);
        $user->save();
        return $user;
    }
}