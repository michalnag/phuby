## Getting Started ##
### Create Docs ###
`vendor/bin/phpdoc.php -t docs -d lib --force --template="responsive-twig"`

### Configuration ###
* Setup Configuration by providing an absolute path to the configuration directory, for example
`PHuby\Config::set_config_root(__DIR__."/../config.d");`

* Configuration for logger example
```
"default" : {
   "name" : "default",
   "output" : "php://stderr",
   "level" : "info"
}
```