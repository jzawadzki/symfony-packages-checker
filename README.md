# symfony-packages-checker

With brand-new Symfony Flex, instead of `symfony/symfony` package you will need to install every component directly.
This is small Proof of Concept for checking (based on your codebase), which symfony packages you would need to add to remove `symfony/symfony` dependency!

## Installation

You can just download the phar from here: https://github.com/jzawadzki/symfony-packages-checker/blob/master/build/checker.phar
```bash
$ wget https://github.com/jzawadzki/symfony-packages-checker/blob/master/build/checker.phar
```
## Usage

```bash
$ php checker.phar check path/to/your/src
```

## Example of output
```bash
$ php build/checker.phar check src                                      
Looks like you are using following Symfony packages:
symfony/templates
symfony/framework-bundle
symfony/form
symfony/options-resolver
```

## Thanks to 
* [@Halleck45](https://github.com/Halleck45) and other contributors for creating [PhpMetrics](https://github.com/phpmetrics/PhpMetrics) which code is used here
