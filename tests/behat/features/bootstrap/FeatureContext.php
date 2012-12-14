<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\MinkExtension\Context\MinkContext,
    Behat\Mink\Exception\ExpectationException,
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

    public function getEngine() {
        return $this->getSubcontext('base')->getEngine();
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
         $file = $this->getSession()->getPage()->findById($file_id);
         $file_upload = $sDirRoot . $file_upload;
         $file->attachFile($file_upload);
    }

    /**
     * @Then /^I fill the element "([^"]*)" value "([^"]*)"$/
     */
    public function IFillElement($path, $value) {
        $element = $this->getSession()->getPage()->find('css', $path);
        if ($element) {
            $element->SetValue($value);
        }
        else {
            throw new ExpectationException('Element not found', $this->getSession());
        }
    }

    /**
     * @Then /^I should see in element by css "([^"]*)" any of values:$/
     */
    public function iShouldSeeInContainerAnyOfValues($objectId, TableNode $table)
    {
        $element = $this->getSession()->getPage()->find('css', $objectId);

        if ($element) {
            $content = $element->getHtml();

            foreach ($table->getHash() as $genreHash) {
                $regex  = '/'.preg_quote($genreHash['value'], '/').'/ui';
                if (preg_match($regex, $content)) {
                    return true;
                }
            }
        }
        else {
            throw new ExpectationException('Container not found', $this->getSession());
        }
    }

}