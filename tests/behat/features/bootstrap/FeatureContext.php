<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\MinkExtension\Context\MinkContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

$sDirRoot = dirname(realpath((dirname(__FILE__)) . "/../../../../../"));
set_include_path(get_include_path().PATH_SEPARATOR.$sDirRoot);

require_once("tests/behat/features/bootstrap/BaseFeatureContext.php");

/**
 * LiveStreet custom feature context
 */
class FeatureContext extends MinkContext
{
    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
        $this->useContext('base', new BaseFeatureContext($parameters));
    }

    public function getMinkContext()
    {
        return $this->getMainContext();
    }

    /**
     * @Given /^the following banners exist:$/
     */
    public function theFollowingBannersExist(TableNode $table)
    {
        foreach ($table->getHash() as $genreHash) {

            if ($genreHash['image'] != '' && $genreHash['text'] != '')
            {
                $pattern = '".*' . $genreHash['image'] . '|' . $genreHash['text'] . '.*"';
            } else if ($genreHash['text'] != '') {
                $pattern = '".*' . $genreHash['text'] . '.*"';
            } else if ($genreHash['image'] != '') {
                $pattern = '".*<img src=\"' . $genreHash['value'] . '\">.*"';
            }
            $this->assertSession()->responseMatches($pattern);
        }
    }
}