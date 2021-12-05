<?php

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context
{
    protected  $response = null;
    protected $username = null;
    protected $password = null;
    protected $client   = null;

    /**
     * @return null
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param null $response
     */
    public function setResponse($response): void
    {
        $this->response = $response;
    }

    /**
     * @return null
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param null $username
     */
    public function setUsername($username): void
    {
        $this->username = $username;
    }

    /**
     * @return null
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param null $password
     */
    public function setPassword($password): void
    {
        $this->password = $password;
    }

    /**
     * @return null
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param null $client
     */
    public function setClient($client): void
    {
        $this->client = $client;
    }

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct($github_username, $github_password)
    {
        $this->username = $github_username;
        $this->password = $github_password;
    }

    /**
     * @Given I am anonymous user
     */
    public function iAmAnAnonymousUser(): bool
    {
        return true;
    }


    /**
     * @When I search for :arg1
     */
    public function iSearchFor($arg1)
    {
        $client = new GuzzleHttp\Client(['base_uri' => 'https://api.github.com']);
        $this->response = $client->get( '/search/repositories?q=' . $arg1);
    }


    /**
     * @Then I expect a :arg1 response code
     * @throws Exception
     */
    public function iExpectAResponseCode($arg1)
    {
        $response_code = $this->response->getStatusCode();
        if($response_code <> $arg1) {
            throw new Exception("It didn't work. We expected a 200 response but got a $arg1 response" . $response_code);
        }
    }

    /**
     * @Then I expect at least :arg1 result
     * @throws Exception
     */
    public function iExpectAtLeastResult($arg1)
    {
        $data= $this->getBodyAsJson();
        if($data['total_count'] < $arg1){
            throw new Exception("We expected at least $arg1 results but found: " . $data['total_count']);
        }
    }

    /**
     * @Given I am an authenticated user
     * @throws Exception
     */
    public function iAmAnAuthenticatedUser()
    {
        $this->client = new GuzzleHttp\Client([
            'base_uri' => 'https://api.github.com',
            'auth' => [$this->username, $this->password]
        ]);
        $response = $this->client ->get('/'); //Get to the root of the API
        $this->iExpectAResponseCode(200);
    }

    /**
     * @When I request a list of repositories
     * @throws Exception
     */
    public function iRequestAListOfRepositories()
    {
        $this->response = $this->client->get('/user/repos');
        $this->iExpectAResponseCode(200);
    }

    /**
     * @Then The results should include repository name :arg1
     */
    public function theResultsShouldIncludeRepositoryName($arg1) : bool
    {

        $repositories = $this->getBodyAsJson();

        foreach ($repositories as $repository){
            if($repository['name'] == $arg1){
                return true;
            }
        }

        throw new Exception("Expected to find a repository named '$arg1' but did not.");
    }

        protected function getBodyAsJson(){

            return json_decode($this->response->getBody(), true);
        }
}