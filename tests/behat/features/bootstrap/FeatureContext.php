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

    /**
     * @When /^I attach the file to path  "([^"]*)" to "([^"]*)"$/
     */
    public function iAttachTheFileToBanneroidTo($file_upload, $file_id)
    {
         $sDirRoot = dirname(realpath((dirname(__FILE__)) . "/../../../../../"));
         $file = $this->getMinkContext()->getSession()->getPage()->findById($file_id);
         $file_upload = $sDirRoot . $file_upload;
         $file->attachFile($file_upload);
    }
}